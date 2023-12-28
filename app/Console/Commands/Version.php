<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Version extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command return version.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Attempt to retrieve the version from the project
            $this->info(config('app.version'));
        } catch(\Exception $e) {
            // ❌ Error response: Handle and report any exceptions that occur
            report($e);
            $this->info("");
        }
    }
}
