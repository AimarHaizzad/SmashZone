<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== BOOKING DEBUG ===\n";

// Get all bookings for today
$bookings = App\Models\Booking::where('date', '2025-01-27')->get();
echo "Total bookings for today: " . $bookings->count() . "\n";

foreach($bookings as $booking) {
    echo "Booking ID: {$booking->id}, User: {$booking->user_id}, Court: {$booking->court_id}, Time: {$booking->start_time} - {$booking->end_time}\n";
}

// Check current user
$user = auth()->user();
if($user) {
    echo "\nCurrent user: {$user->id} ({$user->name})\n";
    $myBookings = App\Models\Booking::where('user_id', $user->id)->where('date', '2025-01-27')->get();
    echo "My bookings: " . $myBookings->count() . "\n";
    foreach($myBookings as $booking) {
        echo "My Booking: Court {$booking->court_id}, Time: {$booking->start_time} - {$booking->end_time}\n";
    }
} else {
    echo "\nNo user logged in\n";
}
