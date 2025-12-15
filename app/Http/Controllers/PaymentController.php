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
        
        // Get payments based on user role
        $paymentsQuery = Payment::with(['user', 'bookings.court', 'booking.court']);
        
        if ($user->isCustomer()) {
            // Customers only see their own payments
            $paymentsQuery->where('user_id', $user->id);
        } elseif ($user->isOwner()) {
            // Owners see payments for their courts
            $paymentsQuery->where(function($query) use ($user) {
                $query->whereHas('bookings.court', function($q) use ($user) {
                    $q->where('owner_id', $user->id);
                })->orWhereHas('booking.court', function($q) use ($user) {
                    $q->where('owner_id', $user->id);
                });
            });
        }
        // Staff can see all payments (no filter needed)
        
        $payments = $paymentsQuery->orderBy('payment_date', 'desc')->orderBy('created_at', 'desc')->get();
        
        // Get refunds based on user role
        $refundsQuery = \App\Models\Refund::with(['user', 'booking.court', 'payment.bookings.court']);
        
        if ($user->isCustomer()) {
            // Customers only see their own refunds
            $refundsQuery->where('user_id', $user->id);
        } elseif ($user->isOwner()) {
            // Owners see refunds for their courts
            $refundsQuery->whereHas('booking.court', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->orWhereHas('payment.bookings.court', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            });
        }
        // Staff can see all refunds (no filter needed)
        
        $refunds = $refundsQuery->orderBy('created_at', 'desc')->get();
        
        return view('payments.index', compact('payments', 'refunds'));
    }

    public function showPaymentForm(Payment $payment)
    {
        // Check if user owns this payment
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment.');
        }

        // Check if payment is already completed
        if ($payment->status === 'paid') {
            return redirect()->route('payments.success', $payment, absolute: false)->with('info', 'Payment already completed.');
        }

        $payment->loadMissing('bookings.court', 'booking.court');

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
            return redirect()->route('payments.success', $payment, absolute: false);
        }

        $payment->loadMissing('bookings.court', 'booking.court');

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

            $bookingCollection = $payment->bookings;
            if ($bookingCollection->isEmpty() && $payment->booking) {
                $bookingCollection = collect([$payment->booking]);
            }

            $primaryBooking = $bookingCollection->sortBy([
                ['date', 'asc'],
                ['start_time', 'asc'],
            ])->first();

            $bookingDescription = $bookingCollection->map(function ($booking) {
                $start = \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('g:i A');
                $end = \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('g:i A');
                return ($booking->court->name ?? 'Court ' . $booking->court_id) . ' (' . $booking->date . ' ' . $start . ' - ' . $end . ')';
            })->implode(', ');

            $productName = $bookingCollection->count() > 1
                ? 'Court Bookings Bundle'
                : 'Court Booking - ' . ($primaryBooking->court->name ?? 'Unknown Court');

            \Log::info('Creating Stripe session', [
                'payment_amount' => $payment->amount,
                'payment_id' => $payment->id,
            ]);

            // Create Stripe checkout session
            // Stripe requires absolute URLs, so we need to use the full URL
            $appUrl = config('app.url', env('APP_URL', 'https://smashzone-ywoa.onrender.com'));
            $successUrl = $appUrl . route('payments.success', $payment, absolute: false) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = $appUrl . route('payments.cancel', $payment, absolute: false);
            
            \Log::info('Stripe URLs', [
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'app_url' => $appUrl
            ]);
            
            $session = Session::create([
                'payment_method_types' => ['card', 'fpx'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'myr',
                        'product_data' => [
                            'name' => $productName,
                            'description' => $bookingDescription,
                        ],
                        'unit_amount' => (int)($payment->amount * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'booking_id' => $primaryBooking?->id,
                    'booking_ids' => $bookingCollection->pluck('id')->implode(','),
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

        $payment->loadMissing('bookings.court', 'booking.court');

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

                    $this->markRelatedBookingsAsPaid($payment);

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

        $payment->loadMissing('bookings.court', 'booking.court');

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

        $this->markRelatedBookingsAsPaid($payment);

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

                $this->markRelatedBookingsAsPaid($payment);
                
                \Log::info('Payment updated via webhook', ['payment_id' => $payment->id]);
            }
        }

        return response('Webhook handled', 200);
    }

    protected function markRelatedBookingsAsPaid(Payment $payment): void
    {
        if ($payment->relationLoaded('bookings')) {
            $payment->bookings->each(function ($booking) {
                $booking->update(['status' => 'confirmed']);
            });
        } else {
            $payment->bookings()->update(['status' => 'confirmed']);
        }
    }
}
