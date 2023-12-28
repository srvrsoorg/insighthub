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
use App\Models\Summary\Status;

class ProcessStatusData implements ShouldQueue
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

            // Retrieve the latest status summary creation timestamp for the application
            $latestStatusCreated = Status::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Process status data in smaller batches to reduce memory usage
            $chunkSize = config("insighthub.process_chunk_read_size"); // Set your desired chunk size
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, status, is_bot_request, COUNT(id) as count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'status', 'is_bot_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestStatusCreated, function ($query) use ($latestStatusCreated) {
                    $query->where('created_at', '>', $latestStatusCreated);
                });

            $query->chunkById($chunkSize, function ($statusSummaries) use ($application) {
                $statusesData = [];

                foreach ($statusSummaries as $statusSummary) {
                    $statusesData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'status' => $statusSummary->status,
                        'is_bot_request' => $statusSummary->is_bot_request,
                        'count' => $statusSummary->count,
                        'created_at' => $statusSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert status summaries into status_summaries table
                if (!empty($statusesData)) {
                    Status::insert($statusesData);
                }

                $statusSummaries = [];
                $statusesData = [];
            });

        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }
}
