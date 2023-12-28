<?php

namespace App\Jobs\Summary;

use App\Traits\PreventOverlapping;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\Summary\Url;
use App\Http\Helper;

class ProcessUrlData implements ShouldQueue
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

            // Retrieve the latest URL creation timestamp for the application
            $latestUrlCreated = Url::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Process URL data in smaller batches to reduce memory usage
            //$chunkSize = config("insighthub.process_chunk_read_size"); // Set your desired chunk size
            $chunkSize = 50; // Set your desired chunk size
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, url, browser, method, status, bot_name, is_bot_request, is_sitemap_url, is_xmlrpc_request, COUNT(id) as count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'url', 'browser', 'method', 'status', 'bot_name', 'is_bot_request', 'is_sitemap_url', 'is_xmlrpc_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestUrlCreated, function ($query) use ($latestUrlCreated) {
                    $query->where('created_at', '>', $latestUrlCreated);
                });

            $query->chunkById($chunkSize, function ($urlSummaries) use ($application) {
                $urlsData = [];

                foreach ($urlSummaries as $urlSummary) {
                    $urlsData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'url' => $urlSummary->url,
                        'browser' => $urlSummary->browser,
                        'method' => $urlSummary->method,
                        'status' => $urlSummary->status,
                        'bot_name' => $urlSummary->bot_name,
                        'is_bot_request' => $urlSummary->is_bot_request,
                        'is_sitemap_url' => $urlSummary->is_sitemap_url,
                        'is_xmlrpc_request' => $urlSummary->is_xmlrpc_request,
                        'count' => $urlSummary->count,
                        'created_at' => $urlSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert URL summaries into url_summaries table
                if (!empty($urlsData)) {
                    Url::insert($urlsData);
                }

                $urlSummaries = [];
                $urlsData = [];
            });

        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }
}
