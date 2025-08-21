<?php

/**
 * Comprehensive script to complete past bookings
 * Run this script manually: php complete_past_bookings.php
 * Or set up a cron job to run it every hour
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;
use Carbon\Carbon;

echo "=== Past Bookings Completion Script ===\n";
echo "Current time: " . now()->format('Y-m-d H:i:s') . "\n\n";

// Option 1: Complete only confirmed past bookings (default behavior)
echo "=== Option 1: Complete Confirmed Past Bookings ===\n";
$confirmedPastBookings = Booking::with(['user', 'court'])
    ->where('status', 'confirmed')
    ->where(function ($query) {
        $now = now();
        $query->where('date', '<', $now->format('Y-m-d'))
            ->orWhere(function ($subQuery) use ($now) {
                $subQuery->where('date', '=', $now->format('Y-m-d'))
                        ->where('end_time', '<', $now->format('H:i:s'));
            });
    })
    ->get();

echo "Found {$confirmedPastBookings->count()} confirmed past bookings to complete.\n";

if ($confirmedPastBookings->count() > 0) {
    $completedCount = 0;
    foreach ($confirmedPastBookings as $booking) {
        try {
            $booking->update(['status' => 'completed']);
            $completedCount++;
            echo "✓ Completed confirmed booking #{$booking->id} - {$booking->court->name} on {$booking->date} at {$booking->start_time}\n";
        } catch (Exception $e) {
            echo "✗ Failed to complete booking #{$booking->id}: " . $e->getMessage() . "\n";
        }
    }
    echo "Successfully completed: {$completedCount} confirmed bookings\n\n";
} else {
    echo "No confirmed past bookings found.\n\n";
}

// Option 2: Complete pending past bookings (cleanup old pending bookings)
echo "=== Option 2: Complete Pending Past Bookings ===\n";
$pendingPastBookings = Booking::with(['user', 'court'])
    ->where('status', 'pending')
    ->where(function ($query) {
        $now = now();
        $query->where('date', '<', $now->format('Y-m-d'))
            ->orWhere(function ($subQuery) use ($now) {
                $subQuery->where('date', '=', $now->format('Y-m-d'))
                        ->where('end_time', '<', $now->format('H:i:s'));
            });
    })
    ->get();

echo "Found {$pendingPastBookings->count()} pending past bookings to complete.\n";

if ($pendingPastBookings->count() > 0) {
    $completedCount = 0;
    foreach ($pendingPastBookings as $booking) {
        try {
            $booking->update(['status' => 'completed']);
            $completedCount++;
            echo "✓ Completed pending booking #{$booking->id} - {$booking->court->name} on {$booking->date} at {$booking->start_time}\n";
        } catch (Exception $e) {
            echo "✗ Failed to complete booking #{$booking->id}: " . $e->getMessage() . "\n";
        }
    }
    echo "Successfully completed: {$completedCount} pending bookings\n\n";
} else {
    echo "No pending past bookings found.\n\n";
}

// Show all bookings for debugging
echo "=== All Bookings Status ===\n";
$allBookings = Booking::with(['user', 'court'])->orderBy('date', 'desc')->orderBy('start_time', 'desc')->get();

foreach ($allBookings as $booking) {
    $bookingDateTime = Carbon::parse($booking->date . ' ' . $booking->end_time);
    $isPast = $bookingDateTime->isPast();
    $statusColor = match($booking->status) {
        'confirmed' => 'green',
        'completed' => 'gray',
        'pending' => 'yellow',
        'cancelled' => 'red',
        default => 'white'
    };
    
    echo "Booking #{$booking->id}: {$booking->court->name} on {$booking->date} at {$booking->start_time}-{$booking->end_time} - Status: {$booking->status} - Past: " . ($isPast ? 'Yes' : 'No') . "\n";
}

echo "\n=== Summary ===\n";
echo "Total bookings: {$allBookings->count()}\n";
echo "Confirmed past bookings completed: {$confirmedPastBookings->count()}\n";
echo "Pending past bookings completed: {$pendingPastBookings->count()}\n";

echo "\n=== Recommendations ===\n";
if ($allBookings->where('status', 'pending')->count() > 0) {
    echo "- You have pending bookings that may need attention\n";
    echo "- Consider setting up automatic confirmation when payment is received\n";
}

echo "\n=== Script completed ===\n";
