<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingsController extends Controller
{
    /**
     * Get bookings data for mobile app
     * Returns list of all user bookings
     */
    public function getBookings(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            // Get all bookings with court details
            // Adapting for actual database structure: using start_time and end_time to create time_slot
            $bookings = DB::table('bookings')
                ->leftJoin('courts', 'bookings.court_id', '=', 'courts.id')
                ->select(
                    'bookings.id',
                    'courts.name as court_name',
                    'bookings.date',
                    DB::raw('CONCAT(TIME_FORMAT(bookings.start_time, "%h:%i %p"), " - ", TIME_FORMAT(bookings.end_time, "%h:%i %p")) as time_slot'),
                    'bookings.status',
                    DB::raw('CONCAT("RM ", FORMAT(COALESCE(bookings.total_price, bookings.price, 0), 2)) as price')
                )
                ->where('bookings.user_id', $user->id)
                ->orderBy('bookings.date', 'desc')
                ->orderBy('bookings.start_time', 'desc')
                ->get();
            
            $totalBookings = count($bookings);
            
            return response()->json([
                'success' => true,
                'bookings' => $bookings,
                'total_bookings' => $totalBookings
            ]);
            
        } catch (\Exception $e) {
            // If table doesn't exist or there's an error, return empty data
            return response()->json([
                'success' => true,
                'bookings' => [],
                'total_bookings' => 0
            ]);
        }
    }
}

