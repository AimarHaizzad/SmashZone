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
}
