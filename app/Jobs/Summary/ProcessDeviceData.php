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
use App\Models\Summary\Device;

class ProcessDeviceData implements ShouldQueue
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

            // Retrieve the latest device creation timestamp for the application
            $latestDeviceCreated = Device::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Process device data in smaller batches to reduce memory usage
            $chunkSize = config("insighthub.process_chunk_read_size");
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, device, is_bot_request, COUNT(id) as device_count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'device', 'is_bot_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestDeviceCreated, function ($query) use ($latestDeviceCreated) {
                    $query->where('created_at', '>', $latestDeviceCreated);
                });

            $query->chunkById($chunkSize, function ($deviceSummaries) use ($application) {
                $devicesData = [];

                foreach ($deviceSummaries as $deviceSummary) {
                    $devicesData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'device' => $deviceSummary->device,
                        'is_bot_request' => $deviceSummary->is_bot_request,
                        'count' => $deviceSummary->device_count,
                        'created_at' => $deviceSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert device summaries into devices table
                if (!empty($devicesData)) {
                    Device::insert($devicesData);
                }

                $deviceSummaries = [];
                $devicesData = [];
            });

        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }
}