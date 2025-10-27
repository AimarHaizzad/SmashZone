<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard data for mobile app
     * Returns user stats and upcoming bookings
     */
    public function getDashboardData(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            // Count upcoming bookings (future dates, not cancelled)
            $upcomingBookingsCount = DB::table('bookings')
                ->where('user_id', $user->id)
                ->where('date', '>=', now()->format('Y-m-d'))
                ->whereIn('status', ['confirmed', 'pending'])
                ->count();
            
            // Count total bookings
            $totalBookingsCount = DB::table('bookings')
                ->where('user_id', $user->id)
                ->count();
            
            // Calculate total spent
            $totalSpent = DB::table('bookings')
                ->where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->sum('total_price');
            
            // Format total spent
            $totalSpentFormatted = 'RM ' . number_format($totalSpent ?? 0, 2);
            
            // Get upcoming bookings with court details
            $upcomingBookings = DB::table('bookings')
                ->leftJoin('courts', 'bookings.court_id', '=', 'courts.id')
                ->select(
                    'bookings.id',
                    'courts.name as court_name',
                    'bookings.date',
                    DB::raw("CONCAT(TIME_FORMAT(bookings.start_time, '%h:%i %p'), ' - ', TIME_FORMAT(bookings.end_time, '%h:%i %p')) as time_slot"),
                    'bookings.status',
                    DB::raw('CONCAT("RM ", FORMAT(bookings.total_price, 2)) as price')
                )
                ->where('bookings.user_id', $user->id)
                ->where('bookings.date', '>=', now()->format('Y-m-d'))
                ->whereIn('bookings.status', ['confirmed', 'pending'])
                ->orderBy('bookings.date', 'asc')
                ->orderBy('bookings.start_time', 'asc')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'upcoming_bookings' => $upcomingBookingsCount,
                    'total_bookings' => $totalBookingsCount,
                    'total_spent' => $totalSpentFormatted
                ],
                'upcoming_bookings' => $upcomingBookings
            ]);
            
        } catch (\Exception $e) {
            // If there's an error, return empty data with error message
            \Log::error('Dashboard API Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'upcoming_bookings' => 0,
                    'total_bookings' => 0,
                    'total_spent' => 'RM 0.00'
                ],
                'upcoming_bookings' => []
            ]);
        }
    }
}
