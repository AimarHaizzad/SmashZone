<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Get user's bookings
     */
    public function index()
    {
        $user = Auth::user();
        
        $bookings = Booking::where('user_id', $user->id)
            ->with(['court', 'payment'])
            ->orderBy('date', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    /**
     * Create new booking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'court_id' => 'required|exists:courts,id',
            'date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        // Calculate total price (you can customize this logic)
        $totalPrice = 50.00; // Default price, you can make this dynamic
        
        $booking = Booking::create([
            'user_id' => $user->id,
            'court_id' => $request->court_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'pending',
            'total_price' => $totalPrice,
        ]);

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'amount' => $totalPrice,
            'status' => 'pending',
        ]);

        $booking->load(['court', 'payment']);
        
        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    /**
     * Get specific booking
     */
    public function show(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $booking->load(['court', 'payment']);
        
        return response()->json([
            'success' => true,
            'data' => $booking
        ]);
    }

    /**
     * Update booking
     */
    public function update(Request $request, Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'sometimes|date|after:today',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking->update($request->only(['date', 'start_time', 'end_time']));
        $booking->load(['court', 'payment']);
        
        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking
        ]);
    }

    /**
     * Cancel booking
     */
    public function destroy(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $booking->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully'
        ]);
    }
}
