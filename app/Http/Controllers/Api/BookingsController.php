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
            // Check if bookings table exists
            if (!DB::getSchemaBuilder()->hasTable('bookings')) {
                return response()->json([
                    'success' => true,
                    'bookings' => [],
                    'total_bookings' => 0
                ]);
            }
            
            // Try with join first
            try {
                // Check if time_slot column exists, otherwise use start_time/end_time
                $hasTimeSlot = DB::getSchemaBuilder()->hasColumn('bookings', 'time_slot');
                $hasStartEndTime = DB::getSchemaBuilder()->hasColumn('bookings', 'start_time') && 
                                   DB::getSchemaBuilder()->hasColumn('bookings', 'end_time');
                
                if ($hasTimeSlot) {
                    // Use time_slot column directly
                    $timeSlotSelect = DB::raw('COALESCE(bookings.time_slot, "") as time_slot');
                    $orderByTime = 'bookings.time_slot';
                } elseif ($hasStartEndTime) {
                    // Create time_slot from start_time and end_time
                    $timeSlotSelect = DB::raw('CONCAT(TIME_FORMAT(bookings.start_time, "%h:%i %p"), " - ", TIME_FORMAT(bookings.end_time, "%h:%i %p")) as time_slot');
                    $orderByTime = 'bookings.start_time';
                } else {
                    // No time information available
                    $timeSlotSelect = DB::raw('"" as time_slot');
                    $orderByTime = 'bookings.created_at';
                }
                
                // Determine price column
                $hasTotalPrice = DB::getSchemaBuilder()->hasColumn('bookings', 'total_price');
                $hasPrice = DB::getSchemaBuilder()->hasColumn('bookings', 'price');
                
                if ($hasTotalPrice) {
                    $priceSelect = DB::raw('COALESCE(CONCAT("RM ", FORMAT(bookings.total_price, 2)), "RM 0.00") as price');
                } elseif ($hasPrice) {
                    $priceSelect = DB::raw('COALESCE(CONCAT("RM ", FORMAT(bookings.price, 2)), "RM 0.00") as price');
                } else {
                    $priceSelect = DB::raw('"RM 0.00" as price');
                }
                
                $bookings = DB::table('bookings')
                    ->leftJoin('courts', 'bookings.court_id', '=', 'courts.id')
                    ->select(
                        'bookings.id',
                        DB::raw('COALESCE(courts.name, "Court") as court_name'),
                        DB::raw('COALESCE(bookings.date, DATE(bookings.created_at)) as date'),
                        $timeSlotSelect,
                        DB::raw('COALESCE(bookings.status, "pending") as status'),
                        $priceSelect
                    )
                    ->where('bookings.user_id', $user->id)
                    ->orderBy('bookings.date', 'desc')
                    ->orderBy($orderByTime, 'desc')
                    ->get();
            } catch (\Exception $e) {
                // If join fails, try without join
                $hasTimeSlot = DB::getSchemaBuilder()->hasColumn('bookings', 'time_slot');
                $hasStartEndTime = DB::getSchemaBuilder()->hasColumn('bookings', 'start_time') && 
                                   DB::getSchemaBuilder()->hasColumn('bookings', 'end_time');
                
                if ($hasTimeSlot) {
                    $timeSlotSelect = DB::raw('COALESCE(time_slot, "") as time_slot');
                    $orderByTime = 'time_slot';
                } elseif ($hasStartEndTime) {
                    $timeSlotSelect = DB::raw('CONCAT(TIME_FORMAT(start_time, "%h:%i %p"), " - ", TIME_FORMAT(end_time, "%h:%i %p")) as time_slot');
                    $orderByTime = 'start_time';
                } else {
                    $timeSlotSelect = DB::raw('"" as time_slot');
                    $orderByTime = 'created_at';
                }
                
                $hasTotalPrice = DB::getSchemaBuilder()->hasColumn('bookings', 'total_price');
                $hasPrice = DB::getSchemaBuilder()->hasColumn('bookings', 'price');
                
                if ($hasTotalPrice) {
                    $priceSelect = DB::raw('COALESCE(CONCAT("RM ", FORMAT(total_price, 2)), "RM 0.00") as price');
                } elseif ($hasPrice) {
                    $priceSelect = DB::raw('COALESCE(CONCAT("RM ", FORMAT(price, 2)), "RM 0.00") as price');
                } else {
                    $priceSelect = DB::raw('"RM 0.00" as price');
                }
                
                $bookings = DB::table('bookings')
                    ->select(
                        'id',
                        DB::raw('"Court" as court_name'),
                        DB::raw('COALESCE(date, DATE(created_at)) as date'),
                        $timeSlotSelect,
                        DB::raw('COALESCE(status, "pending") as status'),
                        $priceSelect
                    )
                    ->where('user_id', $user->id)
                    ->orderBy('date', 'desc')
                    ->orderBy($orderByTime, 'desc')
                    ->get();
            }
            
            $totalBookings = count($bookings);
            
            return response()->json([
                'success' => true,
                'bookings' => $bookings,
                'total_bookings' => $totalBookings
            ]);
            
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('BookingsController Error: ' . $e->getMessage());
            
            // If table doesn't exist or there's an error, return empty data
            return response()->json([
                'success' => true,
                'bookings' => [],
                'total_bookings' => 0
            ]);
        }
    }
}
