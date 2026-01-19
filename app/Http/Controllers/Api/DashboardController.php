<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard data for mobile app
     * Returns user stats and upcoming bookings
     */
    public function getDashboardData(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            // Get all bookings for the user
            $allBookings = $user->bookings()->get();
            
            // Get upcoming bookings (date >= today, not cancelled)
            $upcomingBookings = $user->bookings()
                ->with('court')
                ->where('date', '>=', Carbon::today()->toDateString())
                ->where('status', '!=', 'cancelled')
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();
            
            // Calculate stats
            $totalBookings = $allBookings->count();
            $upcomingCount = $upcomingBookings->count();
            $totalSpent = $allBookings->where('status', 'confirmed')
                ->sum('total_price');
            
            // Format upcoming bookings for response
            $upcomingBookingsFormatted = $upcomingBookings->map(function ($booking) {
                // Format time slot from start_time and end_time
                $timeSlot = 'N/A';
                if ($booking->start_time && $booking->end_time) {
                    $startTime = Carbon::parse($booking->start_time)->format('g:i A');
                    $endTime = Carbon::parse($booking->end_time)->format('g:i A');
                    $timeSlot = $startTime . ' - ' . $endTime;
                }
                
                return [
                    'id' => (string) $booking->id,
                    'court_name' => $booking->court->name ?? 'Court',
                    'date' => $booking->date,
                    'time_slot' => $timeSlot,
                    'status' => $booking->status,
                    'price' => 'RM ' . number_format($booking->total_price ?? 0, 2)
                ];
            });
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'upcoming_bookings' => $upcomingCount,
                    'total_bookings' => $totalBookings,
                    'total_spent' => 'RM ' . number_format($totalSpent, 2)
                ],
                'upcoming_bookings' => $upcomingBookingsFormatted
            ]);
            
        } catch (\Exception $e) {
            \Log::error('DashboardController Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return empty data on error
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
    
    /**
     * Alias method for /api/dashboard endpoint
     * This matches the specification exactly
     */
    public function index(Request $request)
    {
        return $this->getDashboardData($request);
    }
}
