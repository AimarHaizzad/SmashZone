<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Court;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PastDataSeeder extends Seeder
{
    /**
     * Seed past booking data for reports and predictions.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding past booking data...');

        // Get or create owner
        $owner = User::firstOrCreate(
            ['email' => 'AimarHaizzad@gmail.com'],
            [
                'name' => 'Owner',
                'password' => bcrypt('Aimar123'),
                'role' => 'owner',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Get existing courts or create some
        $courts = Court::where('owner_id', $owner->id)->get();
        
        if ($courts->isEmpty()) {
            $this->command->info('Creating courts for past data...');
            $courts = Court::factory(3)->create([
                'owner_id' => $owner->id,
            ]);
        }

        // Get or create customers
        $customers = User::where('role', 'customer')->get();
        
        if ($customers->isEmpty()) {
            $this->command->info('Creating customers for past data...');
            $customers = User::factory(10)->create([
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }

        // Generate bookings for the past 4 months
        $startDate = Carbon::now()->subMonths(4);
        $endDate = Carbon::now()->subDay(); // Up to yesterday
        
        $this->command->info("Generating bookings from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}...");

        $bookingCount = 0;
        $paymentCount = 0;

        // Generate bookings for each day in the past 4 months
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            // Skip some days randomly (not every day has bookings)
            if (rand(1, 10) <= 7) { // 70% chance of having bookings on a day
                // Generate 1-8 bookings per day (more on weekends)
                $isWeekend = $currentDate->isWeekend();
                $bookingsPerDay = $isWeekend ? rand(4, 8) : rand(1, 5);
                
                for ($i = 0; $i < $bookingsPerDay; $i++) {
                    $court = $courts->random();
                    $customer = $customers->random();
                    
                    // Generate time slots (8 AM to 10 PM)
                    $startHour = rand(8, 21);
                    $duration = rand(1, 3); // 1-3 hours
                    $endHour = min(22, $startHour + $duration);
                    
                    // Determine status based on date
                    $status = 'confirmed';
                    if ($currentDate->lt(Carbon::now()->subWeek())) {
                        // Older bookings are mostly completed
                        $status = rand(1, 10) <= 8 ? 'completed' : (rand(1, 10) <= 2 ? 'cancelled' : 'confirmed');
                    } elseif ($currentDate->lt(Carbon::now()->subDay())) {
                        // Yesterday's bookings are completed or confirmed
                        $status = rand(1, 10) <= 7 ? 'completed' : 'confirmed';
                    } else {
                        // Today's bookings are confirmed or pending
                        $status = rand(1, 10) <= 8 ? 'confirmed' : 'pending';
                    }
                    
                    // Calculate price based on duration and court
                    $hourlyRate = $court->hourlyRateForSlot($currentDate->format('Y-m-d'), sprintf('%02d:00', $startHour));
                    $totalPrice = $hourlyRate * $duration;
                    
                    // Add some randomness to price
                    $totalPrice = round($totalPrice * (0.9 + (rand(0, 20) / 100)), 2);
                    
                    // Create booking
                    $booking = Booking::create([
                        'user_id' => $customer->id,
                        'court_id' => $court->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'start_time' => sprintf('%02d:00:00', $startHour),
                        'end_time' => sprintf('%02d:00:00', $endHour),
                        'status' => $status,
                        'total_price' => $totalPrice,
                    ]);
                    
                    $bookingCount++;
                    
                    // Create payment for confirmed/completed bookings (90% of them)
                    if (in_array($status, ['confirmed', 'completed']) && rand(1, 10) <= 9) {
                        // Payment date should be on or before booking date
                        $paymentDate = $currentDate->copy()->subDays(rand(0, 2));
                        if ($paymentDate->gt(Carbon::now())) {
                            $paymentDate = $currentDate->copy();
                        }
                        
                        $payment = Payment::create([
                            'user_id' => $customer->id,
                            'booking_id' => $booking->id,
                            'amount' => $totalPrice,
                            'status' => $status === 'completed' ? 'paid' : (rand(1, 10) <= 8 ? 'paid' : 'pending'),
                            'payment_date' => $paymentDate,
                        ]);
                        
                        // Link payment to booking
                        $booking->update(['payment_id' => $payment->id]);
                        
                        $paymentCount++;
                    }
                }
            }
            
            $currentDate->addDay();
        }

        $this->command->info("✅ Created {$bookingCount} past bookings with {$paymentCount} payments!");
        $this->command->info("📊 Data range: {$startDate->format('M d, Y')} to {$endDate->format('M d, Y')}");
    }
}

