<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Notifications\BookingReminder;
use Carbon\Carbon;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send booking reminders to users 24 hours before their scheduled booking';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to send booking reminders...');

        // Get bookings that are scheduled for tomorrow
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        
        $bookings = Booking::with(['user', 'court'])
            ->where('date', $tomorrow)
            ->where('status', 'confirmed')
            ->get();

        $this->info("Found {$bookings->count()} bookings for tomorrow.");

        $sentCount = 0;
        $errorCount = 0;

        foreach ($bookings as $booking) {
            try {
                // Send reminder notification
                $booking->user->notify(new BookingReminder($booking));
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
    }
}
