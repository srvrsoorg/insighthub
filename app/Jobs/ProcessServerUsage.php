<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Helper;
use App\Models\Server;

class ProcessServerUsage implements ShouldQueue
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
        try{
            $server = Server::find($this->server->id);

            if(!$server){
                $this->fail("Server not found!");
            }

            $response = Helper::serveravatarClient("organizations/$server->sa_organization_id/servers/$server->sa_server_id/usage", 'GET');

            if (isset($response['error'])) {
                $this->fail("Process Server Usage: ".$response['message']);
            }

            $server->usages()->create([
                'five_min_load' => $response['fiveMinServerLoad'],
                'fifteen_min_load' => $response['serverLoad'],
                'memory_in_pr' => $response['memory']['usage_in_percentage'],
                'disk_in_pr' => $response['disk']['usage_in_percentage'],
                'swap_in_pr' => $response['swapMemory']['usage_in_percentage'],
            ]);

        }catch(\Exception $e){
            $this->fail($e->getMessage());
        }
    }
}
