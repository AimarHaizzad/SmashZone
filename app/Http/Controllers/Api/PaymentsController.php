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
            // Check if payments table exists
            if (!DB::getSchemaBuilder()->hasTable('payments')) {
                return response()->json([
                    'success' => true,
                    'payments' => [],
                    'total_paid' => 'RM 0.00',
                    'pending_amount' => 'RM 0.00'
                ]);
            }
            
            // Get payments with flexible column handling
            try {
                $payments = DB::table('payments')
                    ->select(
                        'id',
                        DB::raw('COALESCE(CONCAT("RM ", FORMAT(amount, 2)), "RM 0.00") as amount'),
                        DB::raw('COALESCE(DATE_FORMAT(created_at, "%Y-%m-%d"), DATE_FORMAT(updated_at, "%Y-%m-%d"), "") as date'),
                        DB::raw('COALESCE(status, "pending") as status'),
                        DB::raw('COALESCE(method, "") as method'),
                        DB::raw('COALESCE(booking_id, "") as booking_id')
                    )
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                // If query fails, try simpler approach
                $payments = DB::table('payments')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($payment) {
                        return (object)[
                            'id' => (string)($payment->id ?? ''),
                            'amount' => isset($payment->amount) ? 'RM ' . number_format($payment->amount, 2) : 'RM 0.00',
                            'date' => isset($payment->created_at) ? date('Y-m-d', strtotime($payment->created_at)) : '',
                            'status' => $payment->status ?? 'pending',
                            'method' => $payment->method ?? '',
                            'booking_id' => isset($payment->booking_id) ? (string)$payment->booking_id : ''
                        ];
                    });
            }
            
            // Calculate totals with flexible status values
            $totalPaid = 0;
            $pendingAmount = 0;
            
            try {
                // Try different status values for completed
                $completedStatuses = ['completed', 'paid', 'success', 'successful', 'confirmed'];
                foreach ($completedStatuses as $status) {
                    $sum = DB::table('payments')
                        ->where('user_id', $user->id)
                        ->where('status', $status)
                        ->sum('amount');
                    if ($sum) {
                        $totalPaid += $sum;
                        break;
                    }
                }
                
                // Try different status values for pending
                $pendingStatuses = ['pending', 'unpaid', 'waiting'];
                foreach ($pendingStatuses as $status) {
                    $sum = DB::table('payments')
                        ->where('user_id', $user->id)
                        ->where('status', $status)
                        ->sum('amount');
                    if ($sum) {
                        $pendingAmount += $sum;
                        break;
                    }
                }
            } catch (\Exception $e) {
                // Totals calculation failed, use defaults
            }
            
            return response()->json([
                'success' => true,
                'payments' => $payments,
                'total_paid' => 'RM ' . number_format($totalPaid, 2),
                'pending_amount' => 'RM ' . number_format($pendingAmount, 2)
            ]);
            
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('PaymentsController Error: ' . $e->getMessage());
            
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
