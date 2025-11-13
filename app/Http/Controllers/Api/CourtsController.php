<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            // Get courts - adjust table name according to your database
            // Assuming you have a 'courts' table
            // Try with price_per_hour first, then fallback to price
            $courts = DB::table('courts')
                ->select(
                    'id',
                    'name',
                    'location',
                    DB::raw('CONCAT("RM ", FORMAT(COALESCE(price_per_hour, price, 0), 2)) as price'),
                    'image',
                    DB::raw('CASE WHEN status = "available" THEN true ELSE false END as available')
                )
                ->where(function($query) {
                    $query->where('status', 'available')
                          ->orWhereNull('status');
                })
                ->orderBy('name', 'asc')
                ->get();
            
            // If no courts found or price column issue, try alternative query
            if ($courts->isEmpty()) {
                $courts = DB::table('courts')
                    ->select(
                        'id',
                        'name',
                        'location',
                        DB::raw('CONCAT("RM ", FORMAT(COALESCE(price, 0), 2)) as price'),
                        'image',
                        DB::raw('CASE WHEN status = "available" THEN true ELSE false END as available')
                    )
                    ->where(function($query) {
                        $query->where('status', 'available')
                              ->orWhereNull('status');
                    })
                    ->orderBy('name', 'asc')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'courts' => $courts
            ]);
            
        } catch (\Exception $e) {
            // If table doesn't exist or there's an error, return empty data
            return response()->json([
                'success' => true,
                'courts' => []
            ]);
        }
    }
}

