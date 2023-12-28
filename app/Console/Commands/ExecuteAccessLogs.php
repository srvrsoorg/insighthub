<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use App\Jobs\ProcessAccessLogs;

class ExecuteAccessLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:access-logs {application}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute access log and send them to a queue.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {   
        try{
            $application = Application::where('id',$this->argument('application'))->first();
            if($application){
                ProcessAccessLogs::dispatch($application)->onQueue("fetch-access-logs")->delay(now()->addMinutes(2));
                $this->info('Send Application to Fetch and Insert Access Log.');
            }else{
                $this->error("Application not found!");
            }
        }catch(\Exception $e){
            $this->error($e->getMessage());
            report($e);
        }
    }
}
