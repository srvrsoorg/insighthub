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
use App\Models\Summary\Referrer;

class ProcessReferrerData implements ShouldQueue
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

            // Retrieve the latest referrer summary creation timestamp for the application
            $latestReferrerCreated = Referrer::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Process referrer data in smaller batches to reduce memory usage
            //$chunkSize = config("insighthub.process_chunk_read_size"); // Set your desired chunk size
            $chunkSize = 50; // Set your desired chunk size
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, referrer_url, referrer_domain, is_bot_request, SUM(bytes) as bytes, COUNT(id) as count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'referrer_url', 'referrer_domain', 'is_bot_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestReferrerCreated, function ($query) use ($latestReferrerCreated) {
                    $query->where('created_at', '>', $latestReferrerCreated);
                });

            $query->chunkById($chunkSize, function ($referrerSummaries) use ($application) {
                $referrersData = [];

                foreach ($referrerSummaries as $referrerSummary) {
                    $referrersData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'referrer_url' => $referrerSummary->referrer_url,
                        'referrer_domain' => $referrerSummary->referrer_domain,
                        'bandwidth' => $referrerSummary->bytes,
                        'is_bot_request' => $referrerSummary->is_bot_request,
                        'count' => $referrerSummary->count,
                        'created_at' => $referrerSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert referrer summaries into referrer_summaries table
                if (!empty($referrersData)) {
                    Referrer::insert($referrersData);
                }

                $referrerSummaries = [];
                $referrersData = [];
            });

        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }
}