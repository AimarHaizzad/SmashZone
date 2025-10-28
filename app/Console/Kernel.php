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

        // Send booking reminders every day at 6 PM (24 hours before booking)
        $schedule->call(function () {
            app(\App\Http\Controllers\Api\BookingNotificationController::class)->sendPendingReminders();
        })->dailyAt('18:00')->name('booking-reminders');

        // Send "starting soon" notifications every 30 minutes
        $schedule->call(function () {
            app(\App\Http\Controllers\Api\BookingNotificationController::class)->sendStartingSoonNotifications();
        })->everyThirtyMinutes()->name('booking-starting-soon');

        // Send payment reminders every day at 9 AM
        $schedule->call(function () {
            // Get bookings that need payment reminders
            $bookings = \App\Models\Booking::where('status', 'pending_payment')
                ->where('created_at', '<=', now()->subHours(24))
                ->get();
                
            foreach ($bookings as $booking) {
                app(\App\Http\Controllers\Api\BookingNotificationController::class)->sendPaymentReminder($booking->id);
            }
        })->dailyAt('09:00')->name('payment-reminders');
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
