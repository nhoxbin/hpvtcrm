<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('app:sync-products')->hourly();
        $schedule->command('app:check-digishop-session')->everySixHours();
        $schedule->command('app:onebss-check-customers')->cron('* 21-23,0-8 * * *')->shouldSkipDueToOverlapping();
        $schedule->command('queue:retry all')->everyMinute()->shouldSkipDueToOverlapping();
        $schedule->command('app:digishop-check-customers')->everyMinute()->shouldSkipDueToOverlapping();
        $schedule->command('queue:work --stop-when-empty')->everyMinute()->shouldSkipDueToOverlapping();
        $schedule->command('queue:restart')->hourly();
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
