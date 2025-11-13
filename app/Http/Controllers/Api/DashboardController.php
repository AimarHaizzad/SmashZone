<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            // Get stats - adjust table names according to your database
            // Assuming you have a 'bookings' table
            
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
            
            // Calculate total spent - handle both price and total_price columns
            $hasTotalPrice = DB::getSchemaBuilder()->hasColumn('bookings', 'total_price');
            $hasPrice = DB::getSchemaBuilder()->hasColumn('bookings', 'price');
            
            if ($hasTotalPrice) {
                $totalSpent = DB::table('bookings')
                    ->where('user_id', $user->id)
                    ->where('status', 'confirmed')
                    ->sum('total_price');
            } elseif ($hasPrice) {
                $totalSpent = DB::table('bookings')
                    ->where('user_id', $user->id)
                    ->where('status', 'confirmed')
                    ->sum('price');
            } else {
                $totalSpent = 0;
            }
            
            // Format total spent
            $totalSpentFormatted = 'RM ' . number_format($totalSpent ?? 0, 2);
            
            // Get upcoming bookings with court details
            // Handle time_slot vs start_time/end_time
            $hasTimeSlot = DB::getSchemaBuilder()->hasColumn('bookings', 'time_slot');
            $hasStartEndTime = DB::getSchemaBuilder()->hasColumn('bookings', 'start_time') && 
                               DB::getSchemaBuilder()->hasColumn('bookings', 'end_time');
            
            if ($hasTimeSlot) {
                $timeSlotSelect = DB::raw('COALESCE(bookings.time_slot, "") as time_slot');
                $orderByTime = 'bookings.time_slot';
            } elseif ($hasStartEndTime) {
                $timeSlotSelect = DB::raw('CONCAT(TIME_FORMAT(bookings.start_time, "%h:%i %p"), " - ", TIME_FORMAT(bookings.end_time, "%h:%i %p")) as time_slot');
                $orderByTime = 'bookings.start_time';
            } else {
                $timeSlotSelect = DB::raw('"" as time_slot');
                $orderByTime = 'bookings.created_at';
            }
            
            // Handle price column
            if ($hasTotalPrice) {
                $priceSelect = DB::raw('COALESCE(CONCAT("RM ", FORMAT(bookings.total_price, 2)), "RM 0.00") as price');
            } elseif ($hasPrice) {
                $priceSelect = DB::raw('COALESCE(CONCAT("RM ", FORMAT(bookings.price, 2)), "RM 0.00") as price');
            } else {
                $priceSelect = DB::raw('"RM 0.00" as price');
            }
            
            $upcomingBookings = DB::table('bookings')
                ->leftJoin('courts', 'bookings.court_id', '=', 'courts.id')
                ->select(
                    'bookings.id',
                    'courts.name as court_name',
                    'bookings.date',
                    $timeSlotSelect,
                    'bookings.status',
                    $priceSelect
                )
                ->where('bookings.user_id', $user->id)
                ->where('bookings.date', '>=', now()->format('Y-m-d'))
                ->whereIn('bookings.status', ['confirmed', 'pending'])
                ->orderBy('bookings.date', 'asc')
                ->orderBy($orderByTime, 'asc')
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
            // If tables don't exist or there's an error, return empty data
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
