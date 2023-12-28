<?php

namespace App\Jobs\Summary;

use App\Traits\PreventOverlapping;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\Summary\MimeType;
use Illuminate\Bus\Batchable;

class ProcessMimeTypeData implements ShouldQueue
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

            // Retrieve the latest mime type summary creation timestamp for the application
            $latestMimeTypeCreated = MimeType::where(['application_id'=>$this->application->id, 'type' => $this->logType])->max('created_at');

            // Process mime type data in smaller batches to reduce memory usage
            $chunkSize = config("insighthub.process_chunk_read_size");
            $query = $application->accessLogs()
                ->where('type', $this->logType)
                ->selectRaw("id, application_id, mime_type, document_type, is_bot_request, COUNT(id) as count, MAX(created_at) as created_at, DATE_FORMAT(created_at, '%m/%d/%Y') as date")
                ->groupBy('application_id', 'mime_type', 'document_type', 'is_bot_request', 'date')
                ->orderBy('created_at', 'asc')
                ->when($latestMimeTypeCreated, function ($query) use ($latestMimeTypeCreated) {
                    $query->where('created_at', '>', $latestMimeTypeCreated);
                });

            $query->chunkById($chunkSize, function ($mimeTypeSummaries) use ($application) {
                $mimeTypesData = [];

                foreach ($mimeTypeSummaries as $mimeTypeSummary) {
                    $mimeTypesData[] = [
                        'type' => $this->logType,
                        'server_id' => $application->server_id,
                        'application_id' => $application->id,
                        'mime_type' => $mimeTypeSummary->mime_type,
                        'document_type' => $mimeTypeSummary->document_type,
                        'is_bot_request' => $mimeTypeSummary->is_bot_request,
                        'count' => $mimeTypeSummary->count,
                        'created_at' => $mimeTypeSummary->created_at,
                        'updated_at' => now(),
                    ];
                }

                // Bulk insert mime type summaries into mime_type_summaries table
                if (!empty($mimeTypesData)) {
                    MimeType::insert($mimeTypesData);
                }

                $mimeTypeSummaries = [];
                $mimeTypesData = [];
            });

        } catch (\Exception $e) {
            // Handle exceptions and fail the job
            $this->fail($e->getMessage());
        }
    }
}
