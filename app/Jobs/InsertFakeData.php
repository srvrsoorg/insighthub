<?php

namespace App\Jobs;

use App\Models\ApacheAccessLog;
use App\Models\Application;
use App\Models\NginxAccessLog;
use App\Models\OlsAccessLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InsertFakeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{

            if(Application::whereHas('server', function ($query) { $query->where('web_server', 'apache2'); })->exists()){
                ApacheAccessLog::factory(50)->create();
            }
    
            if(Application::whereHas('server', function ($query) { $query->where('web_server', 'nginx'); })->exists()){
                NginxAccessLog::factory(50)->create();
            }
    
            if(Application::whereHas('server', function ($query) { $query->where('web_server', 'ols'); })->exists()){
                OlsAccessLog::factory(50)->create();
            }

        }catch(\Exception $e){
            $this->fail($e->getMessage());
        }
    }
}
