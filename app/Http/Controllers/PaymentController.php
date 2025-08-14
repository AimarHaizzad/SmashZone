<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Notifications\PaymentConfirmation;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->isOwner()) {
            $courtIds = $user->courts->pluck('id');
            $payments = \App\Models\Payment::whereHas('booking.court', function($q) use ($courtIds) {
                $q->whereIn('id', $courtIds);
            })->with(['user', 'booking.court'])->orderBy('payment_date', 'desc')->get();
        } elseif ($user->isStaff()) {
            // Staff can view all payments
            $payments = \App\Models\Payment::with(['user', 'booking.court'])->orderBy('payment_date', 'desc')->get();
        } else {
            $payments = \App\Models\Payment::where('user_id', $user->id)->with(['user', 'booking.court'])->orderBy('payment_date', 'desc')->get();
        }
        return view('payments.index', compact('payments'));
    }

    public function showPaymentForm(Payment $payment)
    {
        // Check if user owns this payment
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment.');
        }

        // Check if payment is already completed
        if ($payment->status === 'paid') {
            return redirect()->route('payments.success', $payment)->with('info', 'Payment already completed.');
        }

        return view('payments.pay', compact('payment'));
    }

    public function processPayment(Request $request, Payment $payment)
    {
        // Debug logging
        \Log::info('Payment processing started', [
            'payment_id' => $payment->id,
            'user_id' => auth()->id(),
            'payment_user_id' => $payment->user_id
        ]);

        // Check if user owns this payment
        if ($payment->user_id !== auth()->id()) {
            \Log::error('Unauthorized payment access', [
                'payment_id' => $payment->id,
                'user_id' => auth()->id(),
                'payment_user_id' => $payment->user_id
            ]);
            abort(403, 'Unauthorized access to payment.');
        }

        // Check if payment is already completed
        if ($payment->status === 'paid') {
            return redirect()->route('payments.success', $payment);
        }

        try {
            // Check if Stripe is configured
            $stripeSecret = config('services.stripe.secret');
            if (!$stripeSecret) {
                \Log::error('Stripe secret not configured');
                return response()->json(['error' => 'Payment system not configured. Please contact support.'], 500);
            }

            \Log::info('Setting Stripe API key');
            
            // Set Stripe API key
            Stripe::setApiKey($stripeSecret);

            \Log::info('Creating Stripe session', [
                'payment_amount' => $payment->amount,
                'court_name' => $payment->booking->court->name ?? 'Unknown'
            ]);

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
                'success_url' => route('payments.success', $payment) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payments.cancel', $payment),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                    'user_id' => $payment->user_id,
                ],
            ]);

            \Log::info('Stripe session created', ['session_id' => $session->id]);

            // Update payment with Stripe session ID
            $payment->update([
                'stripe_session_id' => $session->id,
                'status' => 'pending'
            ]);

            return response()->json(['session_id' => $session->id]);

        } catch (\Exception $e) {
            \Log::error('Payment processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paymentSuccess(Request $request, Payment $payment)
    {
        // Check if user owns this payment
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment.');
        }

        $sessionId = $request->get('session_id');

        if ($sessionId) {
            try {
                // Set Stripe API key
                Stripe::setApiKey(config('services.stripe.secret'));

                // Retrieve the session
                $session = Session::retrieve($sessionId);

                if ($session->payment_status === 'paid') {
                    // Update payment status
                    $payment->update([
                        'status' => 'paid',
                        'payment_date' => now(),
                        'stripe_payment_intent_id' => $session->payment_intent,
                    ]);

                    // Send payment confirmation email
                    try {
                        $payment->user->notify(new PaymentConfirmation($payment));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
                    }

                    return view('payments.success', compact('payment', 'session'));
                }
            } catch (\Exception $e) {
                // Log error but still show success page
                \Log::error('Stripe session verification failed: ' . $e->getMessage());
            }
        }

        return view('payments.success', compact('payment'));
    }

    public function paymentCancel(Payment $payment)
    {
        // Check if user owns this payment
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment.');
        }

        return view('payments.cancel', compact('payment'));
    }

    public function markAsPaid(Payment $payment)
    {
        // Only owners and staff can mark payments as paid
        $user = auth()->user();
        if (!$user->isOwner() && !$user->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        $payment->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        // Send payment confirmation email
        try {
            $payment->user->notify(new PaymentConfirmation($payment));
        } catch (\Exception $e) {
            \Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Payment marked as paid successfully.');
    }

    /**
     * Handle Stripe webhook events
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        if (!$endpoint_secret) {
            \Log::error('Stripe webhook secret not configured');
            return response('Webhook secret not configured', 400);
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            \Log::error('Stripe webhook invalid payload: ' . $e->getMessage());
            return response('Invalid payload', 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            \Log::error('Stripe webhook invalid signature: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        // Handle the event
        if ($event['type'] === 'checkout.session.completed') {
            $session = $event['data']['object'];
            
            // Find the payment by Stripe session ID
            $payment = Payment::where('stripe_session_id', $session['id'])->first();
            
            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'paid',
                    'payment_date' => now(),
                    'stripe_payment_intent_id' => $session['payment_intent'],
                ]);
                
                \Log::info('Payment updated via webhook', ['payment_id' => $payment->id]);
            }
        }

        return response('Webhook handled', 200);
    }
}
