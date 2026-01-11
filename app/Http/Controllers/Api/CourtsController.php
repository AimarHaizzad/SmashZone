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
     * Returns real-time availability for viewing (not booking)
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
        
        // Get date from query parameter, default to today
        $date = $request->query('date', date('Y-m-d'));
        
        try {
            // Validate date format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Use YYYY-MM-DD'
                ], 400);
            }
            
            // Define time slots (8 AM to 10 PM, hourly)
            $timeSlots = [];
            for ($hour = 8; $hour <= 22; $hour++) {
                $timeSlots[] = date('g:i A', mktime($hour, 0, 0));
            }
            
            // Check if courts table exists
            if (!DB::getSchemaBuilder()->hasTable('courts')) {
                return response()->json([
                    'success' => true,
                    'date' => $date,
                    'courts' => []
                ]);
            }
            
            // Get all courts
            $courts = DB::table('courts')
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();
            
            // Determine price column
            $priceColumn = null;
            if (DB::getSchemaBuilder()->hasColumn('courts', 'price_per_hour')) {
                $priceColumn = 'price_per_hour';
            } elseif (DB::getSchemaBuilder()->hasColumn('courts', 'price')) {
                $priceColumn = 'price';
            }
            
            $availabilityData = [];
            
            foreach ($courts as $court) {
                // Get court price
                $courtPrice = 20.00; // Default price
                if ($priceColumn) {
                    $courtData = DB::table('courts')
                        ->where('id', $court->id)
                        ->select($priceColumn)
                        ->first();
                    if ($courtData && isset($courtData->$priceColumn)) {
                        $courtPrice = floatval($courtData->$priceColumn);
                    }
                }
                
                // Try to use Court model with pricing rules for dynamic pricing
                try {
                    $courtModel = \App\Models\Court::with('pricingRules')->find($court->id);
                    if ($courtModel) {
                        // Use pricing rules if available
                        $courtPrice = $courtModel->hourlyRateFor(Carbon::parse($date . ' 12:00:00'));
                    }
                } catch (\Exception $e) {
                    // Fallback to static price if model fails
                    \Log::warning('Could not load court model for pricing', ['court_id' => $court->id]);
                }
                
                $courtTimeSlots = [];
                
                foreach ($timeSlots as $timeSlot) {
                    // Check if this time slot is booked
                    $booking = null;
                    
                    if (DB::getSchemaBuilder()->hasTable('bookings')) {
                        // Check if bookings table has time_slot column (for exact match)
                        $hasTimeSlot = DB::getSchemaBuilder()->hasColumn('bookings', 'time_slot');
                        $hasStartEndTime = DB::getSchemaBuilder()->hasColumn('bookings', 'start_time') && 
                                          DB::getSchemaBuilder()->hasColumn('bookings', 'end_time');
                        
                        if ($hasTimeSlot) {
                            // Use time_slot column for exact match
                            $booking = DB::table('bookings')
                                ->where('court_id', $court->id)
                                ->where('date', $date)
                                ->where('time_slot', $timeSlot)
                                ->whereIn('status', ['confirmed', 'pending'])
                                ->first();
                        } elseif ($hasStartEndTime) {
                            // Use start_time/end_time for overlap detection
                            // Convert time slot to 24-hour format for comparison
                            $slotTime24 = date('H:i:s', strtotime($timeSlot));
                            $slotEndTime24 = date('H:i:s', strtotime($slotTime24 . ' +1 hour'));
                            
                            $booking = DB::table('bookings')
                                ->where('court_id', $court->id)
                                ->where('date', $date)
                                ->whereIn('status', ['confirmed', 'pending'])
                                ->where(function($query) use ($slotTime24, $slotEndTime24) {
                                    // Check if slot overlaps with booking
                                    // Slot overlaps if: slot_start < booking_end AND slot_end > booking_start
                                    $query->where('start_time', '<', $slotEndTime24)
                                          ->where('end_time', '>', $slotTime24);
                                })
                                ->first();
                        }
                    }
                    
                    $status = 'available';
                    if ($booking) {
                        // Check if it's the current user's booking
                        $status = ($booking->user_id == $user->id) ? 'my_booking' : 'booked';
                    }
                    
                    // Get dynamic price for this specific time slot if court model is available
                    $slotPrice = $courtPrice;
                    try {
                        $courtModel = \App\Models\Court::with('pricingRules')->find($court->id);
                        if ($courtModel) {
                            $slotTime24 = date('H:i:s', strtotime($timeSlot));
                            $slotDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $slotTime24);
                            $slotPrice = $courtModel->hourlyRateFor($slotDateTime);
                        }
                    } catch (\Exception $e) {
                        // Use default price if dynamic pricing fails
                    }
                    
                    $priceFormatted = 'RM ' . number_format($slotPrice, 2) . '/hr';
                    
                    $courtTimeSlots[] = [
                        'time_slot' => $timeSlot,
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
            \Log::error('Court Availability Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
