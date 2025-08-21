<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class CompletePastBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:complete-past';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark past bookings as completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to complete past bookings...');

        // Get current date and time
        $now = Carbon::now();
        
        // Find bookings that have ended and are still confirmed
        $pastBookings = Booking::with(['user', 'court'])
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
    }
}
