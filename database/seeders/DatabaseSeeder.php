<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Court;
use App\Models\Product;
use App\Models\Booking;
use App\Models\Payment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Create 1 Owner ---
        $owner = User::factory()->create([
            'name' => 'Owner',
            'email' => 'AimarHaizzad@gmail.com',
            'password' => bcrypt('Aimar123'),
            'role' => 'owner',
        ]);

        // --- Create 2 Staff ---
        $staff = User::factory(2)->create([
            'role' => 'staff',
        ]);

        // --- Create 5 Customers ---
        $customers = User::factory(5)->create([
            'role' => 'customer',
        ]);

        // --- Create 3 Courts owned by the Owner ---
        $courts = Court::factory(3)->create([
            'owner_id' => $owner->id,
        ]);

        // --- Create 10 Products ---
        Product::factory(10)->create();

        // --- Create 10 Bookings for random customers and courts ---
        $bookings = collect();

        foreach (range(1, 10) as $i) {
            $bookings->push(
                Booking::factory()->create([
                    'user_id' => $customers->random()->id,
                    'court_id' => $courts->random()->id,
                ])
            );
        }

        // --- Create Payments for each Booking ---
        foreach ($bookings as $booking) {
            Payment::factory()->create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'amount' => $booking->total_price ?? 50.00, // fallback value
                'status' => 'paid',
                'payment_date' => now(),
            ]);
        }

        $this->command->info('✅ Database seeding completed successfully!');
    }
}