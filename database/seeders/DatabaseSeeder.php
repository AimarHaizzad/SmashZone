<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 owner, 2 staff, 5 customers
        $owner = \App\Models\User::factory()->create([
            'name' => 'Owner',
            'email' => 'AimarHaizzad@gmail.com',
            'password' => bcrypt('Aimar123'),
            'role' => 'owner',
        ]);
        $staff = \App\Models\User::factory(2)->create(['role' => 'staff']);
        $customers = \App\Models\User::factory(5)->create(['role' => 'customer']);

        // Create 3 courts for the owner
        $courts = \App\Models\Court::factory(3)->create(['owner_id' => $owner->id]);

        // Create 10 products
        \App\Models\Product::factory(10)->create();

        // Create 10 bookings for random customers and courts
        $bookings = collect();
        foreach (range(1, 10) as $i) {
            $bookings->push(\App\Models\Booking::factory()->create([
                'user_id' => $customers->random()->id,
                'court_id' => $courts->random()->id,
            ]));
        }

        // Create a payment for each booking
        foreach ($bookings as $booking) {
            \App\Models\Payment::factory()->create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'status' => 'completed',
                'payment_date' => now(),
            ]);
        }
    }
}
