<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
