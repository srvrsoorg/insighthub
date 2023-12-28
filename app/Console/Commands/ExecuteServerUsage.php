<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Jobs\ProcessServerUsage;

class ExecuteServerUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:server-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command Send Fetch Server Usage Job into Queue.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Server::where('agent_status', 1)->chunk(10, function ($servers) {
            foreach ($servers as $server) {
                // Queue the processing job for each server
                ProcessServerUsage::dispatch($server)->onQueue("server-usages")->delay(now()->addSeconds(5));
            }
        });
        
        $this->info('Servers have been sent to the queue for processing.');
    }
}
