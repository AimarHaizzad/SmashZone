<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Product;
use App\Models\Payment;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        
        $products = Product::whereIn('id', array_keys($cart))->get();
        $lineItems = [];
        $totalAmount = 0;
        
        foreach ($products as $product) {
            $quantity = $cart[$product->id];
            $lineAmount = $product->price * $quantity;
            $totalAmount += $lineAmount;
            
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'myr',
                    'product_data' => [
                        'name' => $product->name,
                        'description' => $product->description,
                    ],
                    'unit_amount' => (int)($product->price * 100),
                ],
                'quantity' => $quantity,
            ];
        }
        
        // Create payment record for this transaction
        $payment = Payment::create([
            'user_id' => auth()->id(),
            'booking_id' => null, // This is for product purchase, not booking
            'amount' => $totalAmount,
            'status' => 'pending',
        ]);
        
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = StripeSession::create([
            'payment_method_types' => ['card', 'fpx'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}&payment_id=' . $payment->id,
            'cancel_url' => route('stripe.cancel') . '?payment_id=' . $payment->id,
            'metadata' => [
                'payment_id' => $payment->id,
                'user_id' => auth()->id(),
                'type' => 'product_purchase',
            ],
        ]);
        
        // Update payment with Stripe session ID
        $payment->update(['stripe_session_id' => $session->id]);
        
        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        $paymentId = $request->get('payment_id');
        
        $cart = session('cart', []);

        if ($sessionId && $paymentId) {
            try {
                // Set Stripe API key
                Stripe::setApiKey(config('services.stripe.secret'));
                
                // Retrieve the session
                $session = StripeSession::retrieve($sessionId);
                
                if ($session->payment_status === 'paid') {
                    // Update payment status
                    $payment = Payment::find($paymentId);
                    if ($payment) {
                        $payment->update([
                            'status' => 'paid',
                            'payment_date' => now(),
                            'stripe_payment_intent_id' => $session->payment_intent,
                        ]);

                        // Reduce product quantities based on cart
                        foreach ($cart as $productId => $quantityPurchased) {
                            $product = Product::find($productId);
                            if (!$product) {
                                continue;
                            }

                            $newQuantity = max(0, $product->quantity - $quantityPurchased);
                            $product->update(['quantity' => $newQuantity]);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Log error but still clear cart and show success
                \Log::error('Stripe session verification failed: ' . $e->getMessage());
            }
        }
        
        // Clear cart after successful payment
        session()->forget('cart');
        return view('cart.success');
    }

    public function cancel(Request $request)
    {
        $paymentId = $request->get('payment_id');
        
        if ($paymentId) {
            // Update payment status to failed
            $payment = Payment::find($paymentId);
            if ($payment && $payment->status === 'pending') {
                $payment->update(['status' => 'failed']);
            }
        }
        
        return view('cart.cancel');
    }
} 