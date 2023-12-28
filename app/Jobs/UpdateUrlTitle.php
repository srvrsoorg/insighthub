<?php

namespace App\Jobs;

use App\Traits\PreventOverlapping;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Http\Helper;

class UpdateUrlTitle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, PreventOverlapping;

    protected $application;

    public $timeout = 1200;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function handle()
    {
        // Get distinct URLs associated with the application
        $urls = $this->application->urls()
            ->whereNull('title')
            ->where(function ($query) {
                $query->whereRaw("url NOT REGEXP '^(http|https)://'")
                    ->whereRaw("url NOT REGEXP '\\.[^/]+$'");
            })
            ->groupBy('url')
            ->pluck('url');

        foreach ($urls as $url) {
            try {
                $existingUrlWithTitle = $this->application->urls()->select('title')->where('url', $url)
                    ->whereNotNull('title')
                    ->first();

                if ($existingUrlWithTitle) {
                    // If URL with title exists, update titles of URLs with null title for the same address
                    $this->application->urls()->where('url', $url)
                        ->whereNull('title')
                        ->update(['title' => $existingUrlWithTitle->title]);
                } else {
                    // If no URL with title exists for the address, fetch the title
                    $newTitle = Helper::getTitleFromUrl($this->application, $url);

                    if ($newTitle) {
                        // Update URLs with null title for the address
                        $this->application->urls()->where('url', $url)
                            ->whereNull('title')
                            ->update(['title' => $newTitle]);
                    }
                }
            } catch (\Exception $e) {
                // Handle exceptions here or log them for further investigation
                \Log::error('Error processing URL: ' . $url . '. Error message: ' . $e->getMessage());
            }
        }
    }
}
