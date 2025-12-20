<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class OwnerDashboardController extends Controller
{
    public function bookings()
    {
        $ownerId = auth()->id();
        $bookings = \App\Models\Booking::with(['court', 'user', 'payment'])
            ->whereHas('court', function($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->orderBy('date', 'desc')
            ->get();
        return view('owner.bookings', compact('bookings'));
    }

    /**
     * Run the PastDataSeeder to generate historical booking data
     * Only accessible by owners
     */
    public function seedPastData()
    {
        // Ensure user is owner
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Unauthorized. Only owners can run this seeder.');
        }

        try {
            Artisan::call('db:seed', ['--class' => 'PastDataSeeder']);
            $output = Artisan::output();
            
            return redirect()->back()->with('success', 'Past data seeder completed successfully! ' . trim($output));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Seeder failed: ' . $e->getMessage());
        }
    }
}
