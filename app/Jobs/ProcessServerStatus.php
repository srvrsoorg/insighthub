<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Server;
use App\Http\Helper;

class ProcessServerStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
            $response = Helper::serveravatarClient("organizations/{$server->sa_organization_id}/servers/{$server->sa_server_id}", 'GET');

            if (isset($response['error'])) {
                $this->fail($response['message']);
            }

            if(isset($response['server']['agent_status'])) {
                $server->agent_status = $response['server']['agent_status'];
                $server->save();
            }

        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }   
    }
}
