<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingsController extends Controller
{
    /**
     * Get bookings data for mobile app
     * Returns list of all user bookings
     */
    public function getBookings(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            // Get all bookings for the user, ordered by date (newest first)
            $bookings = $user->bookings()
                ->with('court') // Eager load court relationship
                ->orderBy('date', 'desc')
                ->orderBy('start_time', 'desc')
                ->get();
            
            // Format bookings for response
            $bookingsFormatted = $bookings->map(function ($booking) {
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
                'bookings' => $bookingsFormatted,
                'total_bookings' => $bookings->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('BookingsController Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return empty data on error
            return response()->json([
                'success' => true,
                'bookings' => [],
                'total_bookings' => 0
            ]);
        }
    }
    
    /**
     * Alias method for /api/bookings endpoint
     * This matches the specification exactly
     */
    public function index(Request $request)
    {
        return $this->getBookings($request);
    }
}
