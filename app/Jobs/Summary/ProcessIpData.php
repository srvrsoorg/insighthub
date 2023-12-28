<?php

namespace App\Jobs\Summary;

use App\Traits\PreventOverlapping;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\Summary\Ip;
use Illuminate\Bus\Batchable;

class ProcessIpData implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, PreventOverlapping;

    protected $application;

    public $timeout = 1200;

    public function __construct(Application $application, protected $logType)
    {
        $this->application = $application;
    }

    public function handle(): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        try {
            $application = Application::with('accessLogs')->findOrFail($this->application->id);

            // Retrieve the latest IP summary creation timestamp for the application
            $latestIpCreated = Ip::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Process IP data in smaller batches to reduce memory usage
            //$chunkSize = config("insighthub.process_chunk_read_size");
            $chunkSize = 50;
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, ip, url, is_bot_request, SUM(bytes) as bytes, COUNT(id) as count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'ip', 'url', 'is_bot_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestIpCreated, function ($query) use ($latestIpCreated) {
                    $query->where('created_at', '>', $latestIpCreated);
                });

            $query->chunkById($chunkSize, function ($ipSummaries) use ($application) {
                $ipsData = [];

                foreach ($ipSummaries as $ipSummary) {
                    $ipsData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'ip' => $ipSummary->ip,
                        'url' => $ipSummary->url,
                        'bandwidth' => $ipSummary->bytes,
                        'is_bot_request' => $ipSummary->is_bot_request,
                        'count' => $ipSummary->count,
                        'created_at' => $ipSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert IP summaries into ip_summaries table
                if (!empty($ipsData)) {
                    Ip::insert($ipsData);
                }

                $ipSummaries = [];
                $ipsData = [];
            });

        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }
}
