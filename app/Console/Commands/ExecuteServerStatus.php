<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Jobs\ProcessServerStatus;

class ExecuteServerStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:server-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve servers with agent status equal to 1 in chunks of 10
        Server::chunk(5, function ($servers) {
            foreach ($servers as $server) {
                // Queue the processing job for each server
                ProcessServerStatus::dispatch($server)->onQueue("server-status")->delay(now()->addSeconds(5));
            }
        });
        
        $this->info('Servers have been sent to the queue for agent status update.');
    }
}
