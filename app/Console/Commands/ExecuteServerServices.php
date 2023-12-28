<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Jobs\ProcessServerServices;

class ExecuteServerServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:server-services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute for server services and send them to a queue.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Retrieve servers with agent status equal to 1 in chunks of 10
        Server::where('agent_status', 1)->chunk(10, function ($servers) {
            foreach ($servers as $server) {
                // Queue the processing job for each server
                ProcessServerServices::dispatch($server)->onQueue("server-services")->delay(now()->addSeconds(5));
            }
        });
        
        $this->info('Servers have been sent to the queue for processing.');
    }
}
