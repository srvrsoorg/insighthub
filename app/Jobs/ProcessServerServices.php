<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Helper;
use App\Models\{Server, Service};

class ProcessServerServices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Server $server)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $server = Server::find($this->server->id);

            if(!$server){
                $this->fail("Server not found!");
            }

            // Make an external request to fetch server data
            $services = Helper::serveravatarClient("organizations/{$server->sa_organization_id}/servers/{$server->sa_server_id}/services", 'GET');

            if (isset($services['error'])) {
                $this->fail($services['message']);
            }

            // Prepare an array of data to insert
            $insertData = [];
            foreach ($services['services'] as $service) {
                $insertData[] = [
                    'server_id' => $server->id,
                    'name' => $service['name'],
                    'status' => $service['status'],
                    'cpu_usage' => $service['resourceUsage']['cpu'],
                    'memory_usage' => $service['resourceUsage']['ram'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert all records at once
            Service::insert($insertData);
            $insertData = [];
            
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
