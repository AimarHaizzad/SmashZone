<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    /**
     * Get user's payments
     */
    public function index()
    {
        $user = Auth::user();
        
        $payments = Payment::where('user_id', $user->id)
            ->with(['booking.court'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Get specific payment
     */
    public function show(Payment $payment)
    {
        // Check if user owns this payment
        if ($payment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $payment->load(['booking.court']);
        
        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }

    /**
     * Process payment with Stripe
     */
    public function processPayment(Request $request, Payment $payment)
    {
        // Check if user owns this payment
        if ($payment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Check if payment is already completed
        if ($payment->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payment already completed'
            ], 400);
        }

        try {
            // Check if Stripe is configured
            $stripeSecret = config('services.stripe.secret');
            if (!$stripeSecret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment system not configured'
                ], 500);
            }

            // Set Stripe API key
            Stripe::setApiKey($stripeSecret);

            // Create Stripe checkout session
            $session = Session::create([
                'payment_method_types' => ['card', 'fpx'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'myr',
                        'product_data' => [
                            'name' => 'Court Booking - ' . ($payment->booking->court->name ?? 'Unknown Court'),
                            'description' => 'Booking for ' . $payment->booking->date . ' from ' . $payment->booking->start_time . ' to ' . $payment->booking->end_time,
                        ],
                        'unit_amount' => (int)($payment->amount * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => config('app.url') . '/api/payments/' . $payment->id . '/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('app.url') . '/api/payments/' . $payment->id . '/cancel',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                    'user_id' => $payment->user_id,
                ],
            ]);

            // Update payment with Stripe session ID
            $payment->update([
                'stripe_session_id' => $session->id,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'checkout_url' => $session->url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
