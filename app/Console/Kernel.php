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
        $schedule->command('app:sync-products')->hourly();
        $schedule->command('app:check-digishop-session')->hourly();
        $schedule->command('app:onebss-check-customers')->everyFiveMinutes()->shouldSkipDueToOverlapping();
        $schedule->command('app:digishop-check-customers')->everyFiveMinutes()->shouldSkipDueToOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
