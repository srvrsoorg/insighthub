<?php

namespace App\Jobs;

use App\Jobs\Summary\ProcessBandwidthData;
use App\Jobs\Summary\ProcessBotData;
use App\Jobs\Summary\ProcessBrowserData;
use App\Jobs\Summary\ProcessDeviceData;
use App\Jobs\Summary\ProcessIpData;
use App\Jobs\Summary\ProcessMethodData;
use App\Jobs\Summary\ProcessMimeTypeData;
use App\Jobs\Summary\ProcessPlatformData;
use App\Jobs\Summary\ProcessPlatformVersionData;
use App\Jobs\Summary\ProcessProtocolData;
use App\Jobs\Summary\ProcessReferrerData;
use App\Jobs\Summary\ProcessStatusData;
use App\Jobs\Summary\ProcessUrlData;
use App\Jobs\ProcessThroughputs;
use App\Jobs\ProcessErrorRates;
use App\Jobs\UpdateUrlTitle;
use App\Models\Application;
use App\Traits\PreventOverlapping;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Throwable;

class ProcessSummaryBatch implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, PreventOverlapping;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $application, protected $logType)
    {
        $this->application = $application;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $application = Application::find($this->application);
        Bus::batch([
            new ProcessBandwidthData($application, $this->logType),
            new ProcessBotData($application, $this->logType),
            new ProcessBrowserData($application, $this->logType),
            new ProcessDeviceData($application, $this->logType),
            new ProcessIpData($application, $this->logType),
            new ProcessMethodData($application, $this->logType),
            new ProcessMimeTypeData($application, $this->logType),
            new ProcessPlatformData($application, $this->logType),
            new ProcessPlatformVersionData($application, $this->logType),
            new ProcessProtocolData($application, $this->logType),
            new ProcessStatusData($application, $this->logType),
            new ProcessUrlData($application, $this->logType),
            new ProcessReferrerData($application, $this->logType),
            new ProcessThroughputs($application, $this->logType),
            new ProcessErrorRates($application, $this->logType),
            //new UpdateUrlTitle($application),
        ])
        ->then(function () use ($application) {
            \Log::info("Application {$application->name} Job Completed Successfully!!");
            UpdateUrlTitle::dispatch($application)->onQueue("import-summaries")->delay(now()->addMinutes(1));
        })->catch(function (Batch $batch, Throwable $exception) use($application) {
            \Log::info("Inside catch {$application->name} : " . $exception->getMessage());
        })
        ->name("{$application->name}-import-summaries")
        ->onQueue("import-summaries")->dispatch();
    }
}
