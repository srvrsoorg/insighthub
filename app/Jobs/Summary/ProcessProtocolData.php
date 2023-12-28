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
use App\Models\Summary\Protocol;

class ProcessProtocolData implements ShouldQueue
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

            // Retrieve the latest protocol creation timestamp for the application
            $latestProtocolCreated = Protocol::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Process protocol data in smaller batches to reduce memory usage
            $chunkSize = config("insighthub.process_chunk_read_size"); // Set your desired chunk size
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, protocol, is_bot_request, COUNT(id) as count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'protocol', 'is_bot_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestProtocolCreated, function ($query) use ($latestProtocolCreated) {
                    $query->where('created_at', '>', $latestProtocolCreated);
                });

            $query->chunkById($chunkSize, function ($protocolSummaries) use ($application) {
                $protocolsData = [];

                foreach ($protocolSummaries as $protocolSummary) {
                    $protocolsData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'protocol' => $protocolSummary->protocol,
                        'is_bot_request' => $protocolSummary->is_bot_request,
                        'count' => $protocolSummary->count,
                        'created_at' => $protocolSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert protocol summaries into protocol table
                if (!empty($protocolsData)) {
                    Protocol::insert($protocolsData);
                }

                $protocolSummaries = [];
                $protocolsData = [];
            });

        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e);
        }
    }
}
