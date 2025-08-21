<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register our custom commands
Artisan::command('bookings:complete-past', function () {
    $this->info('Starting to complete past bookings...');

    // Get current date and time
    $now = now();
    
    // Find bookings that have ended and are still confirmed
    $pastBookings = \App\Models\Booking::with(['user', 'court'])
        ->where('status', 'confirmed')
        ->where(function ($query) use ($now) {
            $query->where('date', '<', $now->format('Y-m-d'))
                ->orWhere(function ($subQuery) use ($now) {
                    $subQuery->where('date', '=', $now->format('Y-m-d'))
                            ->where('end_time', '<', $now->format('H:i:s'));
                });
        })
        ->get();

    $this->info("Found {$pastBookings->count()} past bookings to complete.");

    $completedCount = 0;
    $errorCount = 0;

    foreach ($pastBookings as $booking) {
        try {
            // Mark booking as completed
            $booking->update(['status' => 'completed']);
            $completedCount++;
            
            $this->line("✓ Completed booking #{$booking->id} - {$booking->court->name} on {$booking->date} at {$booking->start_time}");
        } catch (\Exception $e) {
            $errorCount++;
            $this->error("✗ Failed to complete booking #{$booking->id}: " . $e->getMessage());
        }
    }

    $this->info("Booking completion process finished!");
    $this->info("Successfully completed: {$completedCount}");
    $this->info("Errors: {$errorCount}");

    return 0;
})->purpose('Automatically mark past bookings as completed');

Artisan::command('bookings:send-reminders', function () {
    $this->info('Starting to send booking reminders...');

    // Get bookings that are scheduled for tomorrow
    $tomorrow = \Carbon\Carbon::tomorrow()->format('Y-m-d');
    
    $bookings = \App\Models\Booking::with(['user', 'court'])
        ->where('date', $tomorrow)
        ->where('status', 'confirmed')
        ->get();

    $this->info("Found {$bookings->count()} bookings for tomorrow.");

    $sentCount = 0;
    $errorCount = 0;

    foreach ($bookings as $booking) {
        try {
            // Send reminder notification
            $booking->user->notify(new \App\Notifications\BookingReminder($booking));
            $sentCount++;
            
            $this->line("✓ Reminder sent for booking #{$booking->id} - {$booking->court->name} at {$booking->start_time}");
        } catch (\Exception $e) {
            $errorCount++;
            $this->error("✗ Failed to send reminder for booking #{$booking->id}: " . $e->getMessage());
        }
    }

    $this->info("Reminder sending completed!");
    $this->info("Successfully sent: {$sentCount}");
    $this->info("Errors: {$errorCount}");

    return 0;
})->purpose('Send booking reminders to users 24 hours before their scheduled booking');
