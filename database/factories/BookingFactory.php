<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Court;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $startHour = $this->faker->numberBetween(8, 20);
        $endHour = $startHour + 1;
        return [
            'user_id' => User::factory(),
            'court_id' => Court::factory(),
            'date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'start_time' => sprintf('%02d:00', $startHour),
            'end_time' => sprintf('%02d:00', $endHour),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'total_price' => $this->faker->randomFloat(2, 20, 100),
        ];
    }
} 