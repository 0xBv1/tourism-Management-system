<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('currency:rate')->hourly();

        $schedule->command('activitylog:clean', ['--days' => 180])->daily();

        $schedule->command('queue-monitor:purge', ['--beforeDays' => 1, '--only-succeeded' => true])->daily();

        $schedule->command('queue-monitor:purge', ['--beforeDays' => 60])->daily();

        // Generate monthly settlements automatically on the 1st of each month
        $schedule->command('settlements:generate --type=monthly --month=' . now()->subMonth()->month . ' --year=' . now()->subMonth()->year)->monthlyOn(1, '00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
