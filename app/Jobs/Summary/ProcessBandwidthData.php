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
use App\Models\Summary\Bandwidth;

class ProcessBandwidthData implements ShouldQueue
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

            // Retrieve the latest bandwidth summary creation timestamp for the application
            $latestBandwidthCreated = Bandwidth::where(['application_id'=>$this->application->id, 'type'=>$this->logType])->max('created_at');

            // Process bandwidth data in smaller batches to reduce memory usage
            //$chunkSize = config("insighthub.process_chunk_read_size");
            $chunkSize = 50;
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, url, mime_type, document_type, SUM(bytes) as bytes, is_bot_request, COUNT(id) as count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'url', 'mime_type', 'document_type', 'is_bot_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestBandwidthCreated, function ($query) use ($latestBandwidthCreated) {
                    $query->where('created_at', '>', $latestBandwidthCreated);
                });

            $query->chunkById($chunkSize, function ($data) use ($application) {
                $bandwidthsData = [];

                foreach ($data as $bandwidthSummary) {
                    $bandwidthsData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'url' => $bandwidthSummary->url,
                        'mime_type' => $bandwidthSummary->mime_type,
                        'document_type' => $bandwidthSummary->document_type,
                        'bandwidth' => $bandwidthSummary->bytes,
                        'is_bot_request' => $bandwidthSummary->is_bot_request,
                        'count' => $bandwidthSummary->count,
                        'created_at' => $bandwidthSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert bandwidth summaries into bandwidth_summaries table
                if (!empty($bandwidthsData)) {
                    Bandwidth::insert($bandwidthsData);
                }

                $data = [];
                $bandwidthsData = [];
            });
            
        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }
}