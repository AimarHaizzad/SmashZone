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
        // Run every hour to complete past bookings
        $schedule->command('bookings:complete-past')
                ->hourly()
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/booking-completion.log'));

        // Send booking reminders daily at 9 AM
        $schedule->command('bookings:send-reminders')
                ->dailyAt('09:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/booking-reminders.log'));
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
