<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // Schedule the queue worker to process emails and default queues
        $schedule->command('queue:work --queue=emails,default --sleep=3 --tries=3 --max-time=3600 --memory=512')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        // Schedule the queue restart command to restart workers periodically
        $schedule->command('queue:restart')->everyTwoHours();

        // Optional: Monitor queue size and alert if needed
        $schedule->call(function () {
            $jobCount = DB::table('jobs')->count();
            if ($jobCount > 100) {
                Log::warning('Queue backlog detected', ['job_count' => $jobCount]);
            }
        })->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}