<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    /**
     * Get payments data for mobile app
     * Returns list of user payments
     */
    public function getPayments(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            // Get payments - adjust table name according to your database
            // Assuming you have a 'payments' table
            $payments = DB::table('payments')
                ->select(
                    'id',
                    DB::raw('CONCAT("RM ", FORMAT(amount, 2)) as amount'),
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'),
                    'status',
                    'method',
                    'booking_id'
                )
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Calculate totals
            $totalPaid = DB::table('payments')
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->orWhere('status', 'paid')
                ->sum('amount');
            
            $pendingAmount = DB::table('payments')
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->sum('amount');
            
            return response()->json([
                'success' => true,
                'payments' => $payments,
                'total_paid' => 'RM ' . number_format($totalPaid ?? 0, 2),
                'pending_amount' => 'RM ' . number_format($pendingAmount ?? 0, 2)
            ]);
            
        } catch (\Exception $e) {
            // If table doesn't exist or there's an error, return empty data
            return response()->json([
                'success' => true,
                'payments' => [],
                'total_paid' => 'RM 0.00',
                'pending_amount' => 'RM 0.00'
            ]);
        }
    }
}

