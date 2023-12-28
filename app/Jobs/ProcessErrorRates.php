<?php

namespace App\Jobs;

use App\Traits\PreventOverlapping;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\{Application, ErrorRate};
use Illuminate\Bus\Batchable;

class ProcessErrorRates implements ShouldQueue
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

            // Retrieve the latest error rate creation timestamp for the application
            $latestErrorRateLogTime = ErrorRate::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Get error rate summaries based on application and latest timestamp
            $errorRateSummaries = $this->getErrorRateSummaries($application, $latestErrorRateLogTime);

            // Prepare error rate data for insertion
            $errorRateDataForInsertion = $this->prepareErrorRateData($errorRateSummaries, $application);

            // Insert data into ErrorRate model if it's not empty
            if (!empty($errorRateDataForInsertion)) {
                ErrorRate::insert($errorRateDataForInsertion);
            }
        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }

    // Function to get error rate summaries based on application and latest timestamp
    protected function getErrorRateSummaries($application, $latestErrorRateLogTime)
    {
        return $application->accessLogs()
            ->where('type', $this->logType)
            ->selectRaw("is_bot_request, COUNT(id) as count, SUM(CASE WHEN status BETWEEN 400 AND 599 THEN 1 ELSE 0 END) as failed_count, DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour_interval, MAX(created_at) as created_at")
            ->groupBy('hour_interval', 'is_bot_request')
            ->havingRaw('failed_count > 0')
            ->orderBy('hour_interval', 'asc')
            ->when($latestErrorRateLogTime, function ($query) use ($latestErrorRateLogTime) {
                $query->where('created_at', '>', $latestErrorRateLogTime);
            })
            ->get();
    }

    // Function to prepare error rate data for insertion
    protected function prepareErrorRateData($errorRateSummaries, $application)
    {
        return $errorRateSummaries->map(function ($errorRateSummary) use ($application) {
            // Calculate error rate based on total and failed requests
            $totalRequests = $errorRateSummary->count;
            $failedRequests = $errorRateSummary->failed_count;
            $errorRate = ($totalRequests > 0) ? ($failedRequests / $totalRequests) * 100 : 0;

            return [
                'type' => $this->logType,
                'server_id' => $application->server_id,
                'application_id' => $application->id,
                'total_request' => $totalRequests,
                'failed_request' => $failedRequests,
                'request_per' => $totalRequests,
                'calculation_per' => 'hour',
                'error_rate' => $errorRate,
                'is_bot_request' => $errorRateSummary->is_bot_request,
                'log_time' => $errorRateSummary->hour_interval,
                'created_at' => $errorRateSummary->created_at,
                'updated_at' => now()
            ];
        })->toArray();
    }
}