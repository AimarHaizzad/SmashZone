<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Get profile data for mobile app
     * Returns user profile information with statistics
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            // Get user statistics
            $totalBookings = DB::table('bookings')
                ->where('user_id', $user->id)
                ->count();
            
            $totalSpent = DB::table('bookings')
                ->where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->sum(DB::raw('COALESCE(total_price, price, 0)'));
            
            $profile = [
                'id' => (string)$user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'role' => $user->role ?? null,
                'position' => $user->position ?? null,
                'total_bookings' => $totalBookings,
                'total_spent' => 'RM ' . number_format($totalSpent ?? 0, 2)
            ];
            
            return response()->json([
                'success' => true,
                'profile' => $profile
            ]);
            
        } catch (\Exception $e) {
            // Return basic profile data if there's an error
            return response()->json([
                'success' => true,
                'profile' => [
                    'id' => (string)$user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                    'role' => $user->role ?? null,
                    'position' => $user->position ?? null,
                    'total_bookings' => 0,
                    'total_spent' => 'RM 0.00'
                ]
            ]);
        }
    }
}

