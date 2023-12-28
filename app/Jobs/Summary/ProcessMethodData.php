<?php

namespace App\Jobs\Summary;

use App\Traits\PreventOverlapping;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\Summary\Method;
use Illuminate\Bus\Batchable;

class ProcessMethodData implements ShouldQueue
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

            // Retrieve the latest method summary creation timestamp for the application
            $latestMethodCreated = Method::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Process method data in smaller batches to reduce memory usage
            $chunkSize = config("insighthub.process_chunk_read_size");
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, method, is_bot_request, COUNT(id) as count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'method', 'is_bot_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestMethodCreated, function ($query) use ($latestMethodCreated) {
                    $query->where('created_at', '>', $latestMethodCreated);
                });

            $query->chunkById($chunkSize, function ($methodSummaries) use ($application) {
                $methodsData = [];

                foreach ($methodSummaries as $methodSummary) {
                    $methodsData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'method' => $methodSummary->method,
                        'is_bot_request' => $methodSummary->is_bot_request,
                        'count' => $methodSummary->count,
                        'created_at' => $methodSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert method summaries into method_summaries table
                if (!empty($methodsData)) {
                    Method::insert($methodsData);
                }

                $methodSummaries = [];
                $methodsData = [];
            });

        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }
}
