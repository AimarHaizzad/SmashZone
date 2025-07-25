<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Court;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_booking()
    {
        $user = User::factory()->create();
        $court = Court::create(['name' => 'Court 1']);

        $response = $this->actingAs($user)->post('/bookings', [
            'court_id' => $court->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $response->assertRedirect('/bookings');
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'court_id' => $court->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);
    }

    public function test_user_cannot_double_book_same_slot()
    {
        $user = User::factory()->create();
        $court = Court::create(['name' => 'Court 1']);
        Booking::create([
            'user_id' => $user->id,
            'court_id' => $court->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'pending',
            'total_price' => 20,
        ]);

        $response = $this->actingAs($user)->post('/bookings', [
            'court_id' => $court->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:30',
            'end_time' => '11:30',
        ]);

        $response->assertSessionHasErrors('overlap');
    }

    public function test_owner_can_see_all_bookings()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $user = User::factory()->create();
        $court = Court::create(['name' => 'Court 1']);
        Booking::create([
            'user_id' => $user->id,
            'court_id' => $court->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'pending',
            'total_price' => 20,
        ]);

        $response = $this->actingAs($owner)->get('/bookings');
        $response->assertSee('Court 1');
    }
} 