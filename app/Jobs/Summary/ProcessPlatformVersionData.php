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
use App\Models\Summary\PlatformVersion;

class ProcessPlatformVersionData implements ShouldQueue
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

            // Retrieve the latest platform version summary creation timestamp for the application
            $latestPlatformVersionCreated = PlatformVersion::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Process platform version data in smaller batches to reduce memory usage
            $chunkSize = config("insighthub.process_chunk_read_size");
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, platform, platform_version, is_bot_request, COUNT(id) as count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'platform', 'platform_version', 'is_bot_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestPlatformVersionCreated, function ($query) use ($latestPlatformVersionCreated) {
                    $query->where('created_at', '>', $latestPlatformVersionCreated);
                });

            $query->chunkById($chunkSize, function ($platformVersionSummaries) use ($application) {
                $platformVersionsData = [];

                foreach ($platformVersionSummaries as $platformVersionSummary) {
                    $platformVersionsData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'platform' => $platformVersionSummary->platform,
                        'platform_version' => $platformVersionSummary->platform_version,
                        'is_bot_request' => $platformVersionSummary->is_bot_request,
                        'count' => $platformVersionSummary->count,
                        'created_at' => $platformVersionSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert platform version summaries into platform_version_summaries table
                if (!empty($platformVersionsData)) {
                    PlatformVersion::insert($platformVersionsData);
                }

                $platformVersionSummaries = [];
                $platformVersionsData = [];
            });

        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }
}
