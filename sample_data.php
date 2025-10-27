<?php

/**
 * Sample Data Seeder for SmashZone Dashboard Testing
 * 
 * This script creates sample users, courts, and bookings for testing the dashboard API.
 * Run with: php artisan tinker < sample_data.php
 */

use App\Models\User;
use App\Models\Court;
use App\Models\Booking;

echo "Creating sample data for SmashZone Dashboard...\n";

// Create test users
$customer = User::firstOrCreate(
    ['email' => 'customer@test.com'],
    [
        'name' => 'Test Customer',
        'password' => bcrypt('password'),
        'role' => 'customer'
    ]
);

$owner = User::firstOrCreate(
    ['email' => 'owner@test.com'],
    [
        'name' => 'Test Owner',
        'password' => bcrypt('password'),
        'role' => 'owner'
    ]
);

echo "Users created:\n";
echo "- Customer ID: {$customer->id} ({$customer->email})\n";
echo "- Owner ID: {$owner->id} ({$owner->email})\n";

// Create test courts
$court1 = Court::firstOrCreate(
    ['name' => 'Court A'],
    [
        'owner_id' => $owner->id,
        'description' => 'Premium badminton court',
        'status' => 'active',
        'location' => 'center'
    ]
);

$court2 = Court::firstOrCreate(
    ['name' => 'Court B'],
    [
        'owner_id' => $owner->id,
        'description' => 'Standard badminton court',
        'status' => 'active',
        'location' => 'side'
    ]
);

echo "Courts created:\n";
echo "- Court A ID: {$court1->id}\n";
echo "- Court B ID: {$court2->id}\n";

// Create test bookings
$today = now()->format('Y-m-d');
$tomorrow = now()->addDay()->format('Y-m-d');
$dayAfter = now()->addDays(2)->format('Y-m-d');
$lastWeek = now()->subWeek()->format('Y-m-d');

// Future bookings (upcoming)
$booking1 = Booking::create([
    'user_id' => $customer->id,
    'court_id' => $court1->id,
    'date' => $tomorrow,
    'start_time' => '10:00:00',
    'end_time' => '11:00:00',
    'status' => 'confirmed',
    'total_price' => 30.00
]);

$booking2 = Booking::create([
    'user_id' => $customer->id,
    'court_id' => $court2->id,
    'date' => $dayAfter,
    'start_time' => '14:00:00',
    'end_time' => '15:00:00',
    'status' => 'pending',
    'total_price' => 35.00
]);

// Past booking (completed)
$booking3 = Booking::create([
    'user_id' => $customer->id,
    'court_id' => $court1->id,
    'date' => $lastWeek,
    'start_time' => '16:00:00',
    'end_time' => '17:00:00',
    'status' => 'completed',
    'total_price' => 30.00
]);

echo "Bookings created:\n";
echo "- Future booking 1: {$booking1->id} ({$tomorrow})\n";
echo "- Future booking 2: {$booking2->id} ({$dayAfter})\n";
echo "- Past booking: {$booking3->id} ({$lastWeek})\n";

// Generate API token for testing
$token = $customer->createToken('dashboard-test')->plainTextToken;

echo "\n=== API Testing Information ===\n";
echo "Customer Token: {$token}\n";
echo "API Endpoint: http://10.62.93.132:8000/api/dashboard\n";
echo "\nTest with curl:\n";
echo "curl -X GET http://10.62.93.132:8000/api/dashboard \\\n";
echo "  -H \"Authorization: Bearer {$token}\" \\\n";
echo "  -H \"Accept: application/json\"\n";

echo "\nExpected Response:\n";
echo "- Upcoming bookings: 2\n";
echo "- Total bookings: 3\n";
echo "- Total spent: RM 60.00\n";

echo "\nSample data created successfully! 🏸\n";
