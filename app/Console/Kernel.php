<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Application;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $app_random_ids_cache_key = "appRandomIds";
        $applications = Application::whereHas('server', function ($query) {
            $query->where('agent_status', 1);
        })
        ->when(\Cache::has($app_random_ids_cache_key), function ($query) use ($app_random_ids_cache_key) {
            $query->whereNotIn("id", \Cache::get($app_random_ids_cache_key));
        })
        ->where('enable', true)
        ->select('id', 'server_id', 'priority')
        ->inRandomOrder()
        ->take((int)config("insighthub.max_cronjob_application"))
        ->get();

        $applicationIds = $applications->pluck('id')->toArray();

        if (\Cache::has($app_random_ids_cache_key)) {
            \Cache::forget($app_random_ids_cache_key);
        }
        \Cache::put($app_random_ids_cache_key, $applicationIds, 43200);


        foreach ($applications as $application) {
            $cronFrequency = $this->getCronFrequency($application->priority);

            $schedule->command("execute:access-logs {$application->id}")
                ->cron($cronFrequency)
                ->withoutOverlapping();
        }

        $schedule->command('execute:server-status')->cron('*/7 * * * *');
        $schedule->command('execute:server-services')->cron('*/13 * * * *');
        $schedule->command('execute:server-usage')->cron('*/7 * * * *');

        $schedule->command('model:prune')->daily();

        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        $schedule->command('telescope:prune --hours=24')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Get cron frequency based on application priority.
     *
     * @param string $priority
     * @return string
     */
    protected function getCronFrequency($priority): string
    {
        switch ($priority) {
            case 'high':
                return '*/10 * * * *'; // Every 10 minutes
            case 'medium':
                return '*/20 * * * *'; // Every 20 minutes
            case 'low':
                return '*/30 * * * *'; // Every 30 minutes
            default:
                return '*/10 * * * *'; // Default to every 10 minutes if priority is not recognized
        }
    }
}
