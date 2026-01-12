<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Notifications\PaymentConfirmation;
use App\Notifications\BookingConfirmation;
use App\Notifications\OrderConfirmation;
use App\Services\FCMService;

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
        // Allow access if user is authenticated and owns the payment, or if session_id is provided (Stripe redirect)
        $sessionId = $request->get('session_id');
        $isAuthenticated = auth()->check();
        
        if ($isAuthenticated && $payment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment.');
        }

        // If not authenticated and no session_id, require login
        if (!$isAuthenticated && !$sessionId) {
            return redirect()->route('login')->with('error', 'Please login to view your payment details.');
        }

        $payment->loadMissing('bookings.court', 'booking.court', 'user');

        if ($sessionId) {
            try {
                // Set Stripe API key
                Stripe::setApiKey(config('services.stripe.secret'));

                // Retrieve the session
                $session = Session::retrieve($sessionId);

                if ($session->payment_status === 'paid') {
                    // Only update if payment is still pending (avoid duplicate updates)
                    $wasPending = $payment->status === 'pending';
                    
                    if ($wasPending) {
                        $payment->update([
                            'status' => 'paid',
                            'payment_date' => now(),
                            'stripe_payment_intent_id' => $session->payment_intent,
                        ]);

                        $this->markRelatedBookingsAsPaid($payment);
                    }
                    
                    // Always send confirmation emails if payment is paid (even if already processed by webhook)
                    // This ensures emails are sent even if webhook failed or user visits before webhook
                    if ($payment->status === 'paid') {
                        $this->sendPaymentConfirmationEmails($payment);
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
    
    /**
     * Send payment and booking confirmation emails
     */
    protected function sendPaymentConfirmationEmails(Payment $payment): void
    {
        // Send payment confirmation email
        try {
            $payment->load('user');
            if ($payment->user) {
                $payment->user->notify(new PaymentConfirmation($payment));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send payment confirmation email', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }

        // Send booking confirmation email with invoice for each booking
        try {
            $bookings = $payment->bookings()->with('court')->get();
            if ($bookings->isEmpty() && $payment->booking) {
                $bookings = collect([$payment->booking]);
            }

            foreach ($bookings as $booking) {
                try {
                    // Send booking confirmation email with invoice PDF
                    $booking->load('court');
                    $payment->load('user');
                    if ($booking->user) {
                        $booking->user->notify(new BookingConfirmation($booking, $payment));
                    }
                    
                    // Send FCM notification for booking confirmation
                    try {
                        $fcmService = new FCMService();
                        $fcmService->sendBookingConfirmation($booking->user_id, [
                            'booking_id' => $booking->id,
                            'court_name' => $booking->court->name ?? 'Court',
                            'date' => \Carbon\Carbon::parse($booking->date)->format('M d, Y'),
                            'time' => \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('g:i A'),
                            'amount' => $booking->total_price
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send FCM notification for booking confirmation', [
                            'booking_id' => $booking->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send booking confirmation email', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send booking confirmation notifications', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function paymentCancel(Payment $payment)
    {
        // Allow access if user is authenticated and owns the payment, or allow guest access for Stripe redirects
        $isAuthenticated = auth()->check();
        
        if ($isAuthenticated && $payment->user_id !== auth()->id()) {
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

        // Send booking confirmation email with invoice for each booking
        try {
            $bookings = $payment->bookings()->with('court')->get();
            if ($bookings->isEmpty() && $payment->booking) {
                $bookings = collect([$payment->booking]);
            }

            foreach ($bookings as $booking) {
                try {
                    // Send booking confirmation email with invoice PDF
                    $booking->load('court');
                    $payment->load('user');
                    $booking->user->notify(new BookingConfirmation($booking, $payment));
                    
                    // Send FCM notification for booking confirmation
                    try {
                        $fcmService = new FCMService();
                        $fcmService->sendBookingConfirmation($booking->user_id, [
                            'booking_id' => $booking->id,
                            'court_name' => $booking->court->name ?? 'Court',
                            'date' => \Carbon\Carbon::parse($booking->date)->format('M d, Y'),
                            'time' => \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('g:i A'),
                            'amount' => $booking->total_price
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send FCM notification for booking confirmation', [
                            'booking_id' => $booking->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send booking confirmation email', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send booking confirmation notifications', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
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

                // Check if this is a booking payment or product purchase
                $paymentType = $session['metadata']['type'] ?? null;
                
                if ($paymentType === 'product_purchase') {
                    // Handle product purchase - create order and send email
                    $this->handleProductPurchaseWebhook($payment, $session);
                } else {
                    // Handle booking payment
                    $this->markRelatedBookingsAsPaid($payment);
                    
                    // Send confirmation emails via webhook (ensures emails are sent even if user doesn't visit success page)
                    $this->sendPaymentConfirmationEmails($payment);
                }
                
                \Log::info('Payment updated via webhook', [
                    'payment_id' => $payment->id,
                    'type' => $paymentType ?? 'booking'
                ]);
            }
        }

        return response('Webhook handled', 200);
    }

    /**
     * Handle product purchase via webhook
     */
    protected function handleProductPurchaseWebhook(Payment $payment, $session): void
    {
        try {
            // Check if order already exists (user might have visited success page)
            $order = \App\Models\Order::where('payment_id', $payment->id)->first();
            
            if (!$order) {
                // Order doesn't exist yet, create it (this shouldn't happen often as success page usually creates it first)
                \Log::warning('Order not found for product purchase webhook', [
                    'payment_id' => $payment->id
                ]);
                // Can't create order without cart data, so just send email if order exists
                return;
            }
            
            // Load order relationships
            $order->load(['items.product', 'shipping', 'user']);
            
            // Send order confirmation email with invoice
            try {
                if ($order->user) {
                    $order->user->notify(new OrderConfirmation($order));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send order confirmation email via webhook', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to handle product purchase webhook', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
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
