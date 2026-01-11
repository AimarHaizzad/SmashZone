<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourtsController extends Controller
{
    /**
     * Get courts data for mobile app
     * Returns list of available courts
     */
    public function getCourts(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            // Check if courts table exists
            if (!DB::getSchemaBuilder()->hasTable('courts')) {
                return response()->json([
                    'success' => true,
                    'courts' => []
                ]);
            }
            
            $query = DB::table('courts');
            
            // Determine price column
            $priceColumn = 'price';
            if (DB::getSchemaBuilder()->hasColumn('courts', 'price_per_hour')) {
                $priceColumn = 'price_per_hour';
            } elseif (!DB::getSchemaBuilder()->hasColumn('courts', 'price')) {
                $priceColumn = null;
            }
            
            // Build select query
            $selectFields = [
                'id',
                'name',
                DB::raw('COALESCE(location, "") as location'),
            ];
            
            if ($priceColumn) {
                $selectFields[] = DB::raw('CONCAT("RM ", FORMAT(' . $priceColumn . ', 2)) as price');
            } else {
                $selectFields[] = DB::raw('"RM 0.00" as price');
            }
            
            $selectFields[] = DB::raw('COALESCE(image, "") as image');
            
            // Handle available status
            if (DB::getSchemaBuilder()->hasColumn('courts', 'status')) {
                $selectFields[] = DB::raw('CASE 
                    WHEN status = "available" OR status = "active" OR status IS NULL OR status = "" THEN true 
                    ELSE false 
                END as available');
            } else {
                $selectFields[] = DB::raw('true as available');
            }
            
            $courts = $query->select($selectFields);
            
            // Less restrictive status filter
            if (DB::getSchemaBuilder()->hasColumn('courts', 'status')) {
                $courts = $courts->where(function($q) {
                    $q->where('status', 'available')
                      ->orWhere('status', 'active')
                      ->orWhere('status', '=', '')
                      ->orWhereNull('status');
                });
            }
            
            $courts = $courts->orderBy('name', 'asc')->get();
            
            // If still empty, try getting all courts without status filter
            if ($courts->isEmpty() && DB::getSchemaBuilder()->hasColumn('courts', 'status')) {
                $courts = DB::table('courts')
                    ->select($selectFields)
                    ->orderBy('name', 'asc')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'courts' => $courts
            ]);
            
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('CourtsController Error: ' . $e->getMessage());
            
            // If table doesn't exist or there's an error, return empty data
            return response()->json([
                'success' => true,
                'courts' => []
            ]);
        }
    }

    /**
     * Get court availability with time slots for a specific date
     */
    public function getAvailability(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $date = $request->query('date', date('Y-m-d'));
        
        try {
            // Define time slots (8 AM to 10 PM, hourly)
            // 8 AM = 08:00, 9 AM = 09:00, ..., 10 PM = 22:00
            $timeSlots = [];
            for ($hour = 8; $hour <= 22; $hour++) {
                $timeSlots[] = [
                    'time' => sprintf('%02d:00:00', $hour), // Database format: "08:00:00"
                    'display' => date('g:i A', mktime($hour, 0, 0)) // Display format: "8:00 AM"
                ];
            }
            
            // Get all courts with pricing rules
            $courts = \App\Models\Court::with('pricingRules')
                ->orderBy('name', 'asc')
                ->get();
            
            // Get all bookings for this date (excluding cancelled)
            $bookings = \App\Models\Booking::where('date', $date)
                ->whereIn('status', ['confirmed', 'pending'])
                ->get();
            
            $availabilityData = [];
            
            foreach ($courts as $court) {
                $courtTimeSlots = [];
                
                foreach ($timeSlots as $slotData) {
                    $slotTime = $slotData['time']; // "08:00:00"
                    $slotDisplay = $slotData['display']; // "8:00 AM"
                    
                    // Calculate slot end time (1 hour later)
                    $slotEndTime = date('H:i:s', strtotime($slotTime . ' +1 hour'));
                    
                    // Check if this time slot overlaps with any booking
                    $booking = $bookings->first(function ($b) use ($court, $slotTime, $slotEndTime) {
                        // Check if slot overlaps with booking
                        // Slot overlaps if: slot_start < booking_end AND slot_end > booking_start
                        return $b->court_id == $court->id && 
                               $slotTime < $b->end_time && 
                               $slotEndTime > $b->start_time;
                    });
                    
                    $status = 'available';
                    if ($booking) {
                        $status = ($booking->user_id == $user->id) ? 'my_booking' : 'booked';
                    }
                    
                    // Get price for this time slot (use court's pricing rules)
                    $slotDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $slotTime);
                    $price = $court->hourlyRateFor($slotDateTime);
                    $priceFormatted = 'RM ' . number_format($price, 2) . '/hr';
                    
                    $courtTimeSlots[] = [
                        'time_slot' => $slotDisplay,
                        'status' => $status,
                        'price' => $priceFormatted
                    ];
                }
                
                $availabilityData[] = [
                    'court_id' => (string)$court->id,
                    'court_name' => $court->name,
                    'time_slots' => $courtTimeSlots
                ];
            }
            
            return response()->json([
                'success' => true,
                'date' => $date,
                'courts' => $availabilityData
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Court Availability Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
