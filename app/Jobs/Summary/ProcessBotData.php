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
use App\Models\Summary\Bot;

class ProcessBotData implements ShouldQueue
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

            // Retrieve the latest bot summary creation timestamp for the application
            $latestBotCreated = Bot::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Process bot data in smaller batches to reduce memory usage
            $chunkSize = config("insighthub.process_chunk_read_size");
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, bot_name, is_bot_request, COUNT(id) as count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'bot_name', 'is_bot_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestBotCreated, function ($query) use ($latestBotCreated) {
                    $query->where('created_at', '>', $latestBotCreated);
                });

            $query->chunkById($chunkSize, function ($botSummaries) use ($application) {
                $botsData = [];

                foreach ($botSummaries as $botSummary) {
                    $botsData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'bot_name' => $botSummary->bot_name,
                        'is_bot_request' => $botSummary->is_bot_request,
                        'count' => $botSummary->count,
                        'created_at' => $botSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert bot summaries into bot_summaries table
                if (!empty($botsData)) {
                    Bot::insert($botsData);
                }

                $botSummaries = [];
                $botsData = [];
            });

        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }
}