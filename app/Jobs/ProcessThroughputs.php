<?php

namespace App\Jobs;

use App\Traits\PreventOverlapping;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\{Application, Throughput};
use Illuminate\Bus\Batchable;

class ProcessThroughputs implements ShouldQueue
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
        // Check if the batch is cancelled, if so, return without processing
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        try {
            // Retrieve the application with its access logs
            $application = Application::with('accessLogs')->findOrFail($this->application->id);

            // Retrieve the latest throughput summary creation timestamp for the application
            $latestThroughputCreated = Throughput::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Get throughput summaries based on application and latest timestamp
            $throughputSummaries = $this->getThroughputSummaries($application, $latestThroughputCreated);

            // Prepare throughput data for insertion
            $throughputsData = $this->prepareThroughputData($throughputSummaries, $application);

            // Insert data into Throughput model if it's not empty
            if (!empty($throughputsData)) {
                Throughput::insert($throughputsData);
            }
        } catch (\Exception $e) {
            // Report and handle exceptions, then fail the job
            report($e);
            $this->fail($e->getMessage());
        }
    }

    // Function to get throughput summaries based on application and latest timestamp
    protected function getThroughputSummaries($application, $latestThroughputCreated)
    {
        return $application->accessLogs()
            ->where('type', $this->logType)
            ->selectRaw("is_bot_request, COUNT(id) as count, DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour_interval,  MAX(created_at) as created_at")
            ->groupBy('hour_interval', 'is_bot_request')
            ->orderBy('hour_interval', 'asc')
            ->when($latestThroughputCreated, function ($query) use ($latestThroughputCreated) {
                $query->where('created_at', '>', $latestThroughputCreated);
            })
            ->get();
    }

    // Function to prepare throughput data for insertion
    protected function prepareThroughputData($throughputSummaries, $application)
    {
        return $throughputSummaries->map(function ($throughputSummary) use ($application) {
            return [
                'type' => $this->logType,
                'server_id' => $application->server_id,
                'application_id' => $application->id,
                'total_request' => $throughputSummary->count,
                'request_per' => $throughputSummary->count,
                'calculation_per' => 'hour',
                'is_bot_request' => $throughputSummary->is_bot_request,
                'log_time' => $throughputSummary->hour_interval,
                'created_at' => $throughputSummary->created_at,
                'updated_at' => now()
            ];
        })->toArray();
    }
}