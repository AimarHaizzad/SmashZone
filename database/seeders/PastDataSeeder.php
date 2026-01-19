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
     * Optional owner ID to use (when called from HTTP route)
     */
    private ?int $ownerId = null;

    /**
     * Set the owner ID to use for seeding
     */
    public function setOwnerId(int $ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    /**
     * Seed past booking data for reports and predictions.
     */
    public function run(): void
    {
        $this->log('🌱 Seeding past booking data...');

        // Get owner - use provided owner ID or fallback to hardcoded email (for CLI usage)
        if ($this->ownerId) {
            $owner = User::find($this->ownerId);
            if (!$owner || !$owner->isOwner()) {
                throw new \Exception("Invalid owner ID provided: {$this->ownerId}");
            }
        } else {
            // Fallback for CLI usage - get or create owner
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
        }

        // Get existing courts or create some
        $courts = Court::where('owner_id', $owner->id)->get();
        
        if ($courts->isEmpty()) {
            $this->log('Creating courts for past data...');
            $courts = Court::factory(3)->create([
                'owner_id' => $owner->id,
            ]);
        }

        // Get or create customers
        $customers = User::where('role', 'customer')->get();
        
        if ($customers->isEmpty()) {
            $this->log('Creating customers for past data...');
            $customers = User::factory(10)->create([
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }

        // Ensure we have courts and customers before proceeding
        if ($courts->isEmpty()) {
            throw new \Exception("No courts found for owner ID {$owner->id}. Please create courts first.");
        }
        
        if ($customers->isEmpty()) {
            throw new \Exception("No customers found. Cannot create bookings without customers.");
        }

        // Generate bookings for the past 3 months for better analytics
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now()->subDay(); // Up to yesterday
        
        $this->log("Generating bookings from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}...");

        $bookingCount = 0;
        $paymentCount = 0;

        // Generate bookings for each day in the past 6 months
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            // Skip some days randomly (not every day has bookings)
            if (rand(1, 10) <= 7) { // 70% chance of having bookings on a day
                // Generate 1-8 bookings per day (more on weekends)
                $isWeekend = $currentDate->isWeekend();
                $bookingsPerDay = $isWeekend ? rand(4, 10) : rand(1, 6);
                
                for ($i = 0; $i < $bookingsPerDay; $i++) {
                    $court = $courts->random();
                    $customer = $customers->random();
                    
                    // Generate time slots (8 AM to 10 PM)
                    $startHour = rand(8, 21);
                    $duration = rand(1, 3); // 1-3 hours
                    $endHour = min(22, $startHour + $duration);
                    
                    // Determine status based on date
                    $status = 'confirmed';
                    $daysAgo = $currentDate->diffInDays(Carbon::now());
                    
                    if ($daysAgo > 7) {
                        // Older bookings are mostly completed
                        $rand = rand(1, 10);
                        $status = $rand <= 7 ? 'completed' : ($rand <= 9 ? 'confirmed' : 'cancelled');
                    } elseif ($daysAgo > 1) {
                        // Recent past bookings are completed or confirmed
                        $status = rand(1, 10) <= 8 ? 'completed' : 'confirmed';
                    } elseif ($daysAgo == 1) {
                        // Yesterday's bookings are mostly completed
                        $status = rand(1, 10) <= 7 ? 'completed' : 'confirmed';
                    } else {
                        // Today's bookings are confirmed or pending
                        $status = rand(1, 10) <= 8 ? 'confirmed' : 'pending';
                    }
                    
                    // Calculate price based on duration and court
                    try {
                        $hourlyRate = $court->hourlyRateForSlot($currentDate->format('Y-m-d'), sprintf('%02d:00', $startHour));
                    } catch (\Exception $e) {
                        // Fallback to default rate if pricing rule fails
                        $hourlyRate = Court::DEFAULT_HOURLY_RATE;
                    }
                    
                    $totalPrice = $hourlyRate * $duration;
                    
                    // Add some randomness to price (±10%)
                    $totalPrice = round($totalPrice * (0.9 + (rand(0, 20) / 100)), 2);
                    
                    // Ensure minimum price
                    if ($totalPrice < 10) {
                        $totalPrice = 10.00;
                    }
                    
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
                        $paymentDate = $currentDate->copy()->subDays(rand(0, 3));
                        if ($paymentDate->gt(Carbon::now())) {
                            $paymentDate = $currentDate->copy();
                        }
                        
                        // Determine payment status
                        $paymentStatus = 'paid';
                        if ($status === 'completed') {
                            $paymentStatus = 'paid'; // Completed bookings are always paid
                        } else {
                            // Confirmed bookings: 85% paid, 15% pending
                            $paymentStatus = rand(1, 100) <= 85 ? 'paid' : 'pending';
                        }
                        
                        $payment = Payment::create([
                            'user_id' => $customer->id,
                            'booking_id' => $booking->id,
                            'amount' => $totalPrice,
                            'status' => $paymentStatus,
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

        $this->log("✅ Created {$bookingCount} past bookings with {$paymentCount} payments!");
        $this->log("📊 Data range: {$startDate->format('M d, Y')} to {$endDate->format('M d, Y')}");
    }

    /**
     * Log message - works both in CLI and HTTP contexts
     */
    private function log(string $message): void
    {
        if (isset($this->command)) {
            $this->command->info($message);
        } else {
            \Illuminate\Support\Facades\Log::info('PastDataSeeder: ' . $message);
        }
    }
}

