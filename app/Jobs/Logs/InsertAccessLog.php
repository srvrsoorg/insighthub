<?php

namespace App\Jobs\Logs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Helper;
use App\Models\{Server, Application};
use BenMorel\ApacheLogParser\Parser;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Batchable;

class InsertAccessLog implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Application $application, protected $type, protected $logs = [])
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!is_array($this->logs)) {
            $this->fail('$this->logs is not an array');
        }

        try{
            $insertData = [];
            foreach ($this->logs as $log) {
                if ($log) {
                    $insertData[] = [
                        'server_id' => $this->application->server_id,
                        'application_id' => $this->application->id,
                        'type' => $this->type,
                        'ip' => $log['ip'],
                        'time' => $log['time'],
                        'url' => $log['url'],
                        'status' => $log['status'],
                        'bytes' => $log['bytes'],
                        'referrer_url' => $log['referrer_url'],
                        'referrer_domain' =>$log['referrer_domain'],
                        'is_bot_request' => $log['is_bot_request'],
                        'is_sitemap_url' => $log['is_sitemap_url'],
                        'is_robots_txt' => $log['is_robots_txt'],
                        'is_xmlrpc_request' => $log['is_xmlrpc_request'],
                        'platform' => $log['platform'],
                        'platform_version' => $log['platform_version'],
                        'device' => $log['device'],
                        'bot_name' => $log['bot_name'],
                        'method' => $log['method'],
                        'browser' => $log['browser'],
                        'mime_type' => $log['mime_type'],
                        'document_type' => $log['document_type'],
                        'protocol' => $log['protocol'],
                        'country' => $log['country'],
                        'state' => $log['state'],
                        'city' => $log['city'],
                        'created_at' => $log['created_at'],
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($insertData)) {
                DB::table($this->application->accessLogTable())->insert($insertData);
            }

            $insertData = [];
            $this->logs = [];

        }catch(\Exception $e){
            $insertData = [];
            $this->logs = [];
            $this->fail($e->getMessage());
        }
    }
}