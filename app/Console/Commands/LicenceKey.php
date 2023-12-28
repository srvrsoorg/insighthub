<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BasicDetail;

class LicenceKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licence:key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command return licence key.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Attempt to retrieve the license key from the database
            $licenceKey = BasicDetail::where('key', 'license_key')->value('value');
            $this->info($licenceKey);
        } catch(\Exception $e) {
            // ❌ Error response: Handle and report any exceptions that occur
            report($e);
            $this->info("");
        }
    }
}
