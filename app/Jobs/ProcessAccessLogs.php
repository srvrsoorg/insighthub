<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use App\Http\Helper;
use App\Models\{Server, Application, AccessLog};
use App\Jobs\Logs\InsertAccessLog;
use BenMorel\ApacheLogParser\Parser;
use Jenssegers\Agent\Agent;
use App\Http\Utilities\DetectMimeType;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Throwable;

class ProcessAccessLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;
    private $parser;
    private $agent;

    private $fetch_access_log_limit;
    private $insert_chunk_size;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Application $application)
    {
        // Initialize the log parser with the log format
        $this->parser = new Parser("%h %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"");
        $this->agent = new Agent();

        $this->fetch_access_log_limit = config("insighthub.fetch_access_log_limit");
        $this->insert_chunk_size = (int)config("insighthub.insert_chunk_size");
    }

    public function middleware()
    {
        return [new WithoutOverlapping($this->application->id)];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $application = Application::find($this->application->id);

            if(!$application){
                $this->fail("Application not found!");
            }

            $logTypes = [
                ['type' => 'access', 'log' => 'access.log'],
                ['type' => 'access-ssl', 'log' => 'access-ssl.log']
            ];

            foreach($logTypes as $log) {
               $response =  $this->getResponse($application, $log['log'], $log['type']);
                if (isset($response['error'])) {
                    \Log::error($response['message']);
                    $this->fail($response['message']);
                }

                // Process the Apache access log data
                $allLog = $this->accessLog($response);

                $chunkedData = array_chunk($allLog, $this->insert_chunk_size);

                $insertJobs = [];
                foreach($chunkedData as $chunk) {
                    $insertJobs[] = (new InsertAccessLog($application, $log['type'], $chunk))->delay(now()->addSeconds(15));
                    $chunk = null;
                }

                if(count($insertJobs) > 0){
                    \Bus::batch($insertJobs)->then(function() use ($application, $log) {
                        ProcessSummaryBatch::dispatch($application->id, $log['type'])->onQueue('import-summaries')->delay(now()->addMinutes(5));
                    })->catch(function (Batch $batch, Throwable $exception) use($application) {
                        \Log::info("Process Logs :: {$application->name} : " . $exception->getMessage());
                    })
                    ->name("{$application->name}-import-access-logs")
                    ->onQueue('import-access-logs')
                    ->dispatch();
                }

                $chunkedData = [];
                $allLog = null;
                $response = null;
                $insertJobs = [];
            }

            // Update application information
            $this->updateApplicationInfo($application);

        } catch (\Exception $e) {
            report($e->getMessage());
            $this->fail('Something went wrong: ' . $e->getMessage());
        }
    }

    // Get response
    private function getResponse($application, $log, $type)
    {
        // Check if there's an existing access log record for the application
        $accessLog = $application->accessLogs()->select('ip', 'url', 'status', 'referrer_url', 'method', 'bytes', 'time')->where('type', $type)->latest()->first();

        // Prepare data for finding logs
        $formData = [
            'log' => $log,
            'type' => 'fullLogs',
            'limit' => (string)$this->fetch_access_log_limit
        ];

        if ($accessLog) {

            $formData['type'] = 'findLogs';
            $formData['ip'] = $accessLog->ip;
            $formData['url'] = $accessLog->url;
            $formData['status'] = $accessLog->status;
            $formData['referrer'] = $accessLog->referrer_url;
            $formData['method'] = $accessLog->method;
            $formData['bytes'] = $accessLog->bytes;
            $formData['time'] = $accessLog->time;

            // Call the getLog method with the prepared data
            $matchResponse = $this->getLog($application->server, $application, $formData);

            if(isset($matchResponse['filterLineNumber'])) {

                $formData['type'] = "specificLines";
                $formData['startLine'] = $matchResponse['filterLineNumber']+1;
                $formData['endLine'] = $formData['startLine'] + (int)$this->fetch_access_log_limit;

                // Call the getLog method with the prepared data
                return $this->getLog($application->server, $application, $formData);

            } else {
                // If no existing access log, prepare data for fetching full logs
                // Call the getLog method with the prepared data
                return $this->getLog($application->server, $application, $formData);
            }
            
        } else {
            // If no existing access log, prepare data for fetching full logs
            // Call the getLog method with the prepared data
            return $this->getLog($application->server, $application, $formData);
        }
    }

    // Get log
    private function getLog($server, $application, $formData)
    {
        // Make an external request to fetch server data
        $response = Helper::serveravatarClient("organizations/$server->sa_organization_id/servers/$server->sa_server_id/applications/$application->sa_application_id/advance-logs", 'PATCH', $formData);

        if (isset($response['error'])) {
            $this->fail($response['message']);
        }

        return $response;
    }

    // Apache/Nginx access log
    private function accessLog($response)
    {
        try {
            
            if (isset($response['output']) && $response['output'] != "") {
                $lines = explode("\n", htmlspecialchars_decode($response['output']));

                // Use array_filter to remove empty lines and array_map to process each line
                return array_map([$this, 'parseAccessLog'], array_filter($lines));
            }

            return [];

        } catch (\InvalidArgumentException $e) {
            $this->fail($e->getMessage());
        }
    }

    private function parseAccessLog($line)
    {
        try {

            // Parse the log line using the configured parser
            $log = $this->parser->parse($line, true);

            // Extract log data into an associative array
            $accessLog = [
                'ip' => $log['remoteHostname'],
                'created_at' => Carbon::createFromTimestamp(strtotime($log['time']))->format('Y-m-d H:i:s'),
                'time' => $log['time'],
                'status' => $log['status'],
                'bytes' => $log['bytesSent'],
                'referrer_url' => $log['requestHeader:Referer'],
                'referrer_domain' => Helper::getReferrerDomain($log['requestHeader:Referer']),
                'country' => null,
                'state' => null,
                'city' => null
            ];
            
            // Get method and request
            $parts = explode(" ", $log['firstRequestLine']);
            $url = null;
            if (count($parts) === 3) {
                $accessLog['method'] = $parts[0];    // The HTTP method (e.g., GET)
                $url = $parts[1];
                $accessLog['url'] = $url;       // The request URI (e.g., /favicon.ico)
                $accessLog['protocol'] = $parts[2];     // The request Protocol (e.g., HTTP/1.0)
            }

            // Get detail from helper
            $accessLog['is_robots_txt'] = Helper::isRobotsTxt($url);
            $accessLog['is_sitemap_url'] = Helper::isSitemapUrl($url);
            $accessLog['is_xmlrpc_request'] = Helper::isXmlrpcRequest($url);

            // Get browser information using the User-Agent string
            $this->agent->setUserAgent($log['requestHeader:User-Agent']);
            $accessLog['browser'] = $this->agent->browser();
            $accessLog['is_bot_request'] = $this->agent->isRobot();
            $accessLog['bot_name'] = $this->agent->robot();

            $platform = $this->agent->platform();
            $accessLog['platform'] = $platform;
            $accessLog['platform_version'] = $this->agent->version($platform);

            // Get url type
            $extension =  DetectMimeType::getExtension($parts[1]);
            $mimeType = DetectMimeType::fromExtension($extension);
            $accessLog['document_type'] = DetectMimeType::getDocumentType($mimeType);
            $accessLog['mime_type'] = $mimeType;
            $accessLog['device'] = $this->agent->deviceType();

            return $accessLog;
        } catch (\Exception $e) {
            report($e->getMessage());
            return [];
        }
    }

    // Update application information
    private function updateApplicationInfo(Application $application)
    {
        $applicationResponse = Helper::serveravatarClient("organizations/{$application->server->sa_organization_id}/servers/{$application->server->sa_server_id}/applications/$application->sa_application_id", 'GET');

        if (isset($applicationResponse['error'])) {
            \Log::error($applicationResponse['message']);
            $this->fail($applicationResponse['message']);
        }

        if (isset($applicationResponse['application'])) {
            $application->update($applicationResponse['application']);
        }
    }
}
