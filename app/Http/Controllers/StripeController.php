<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipping;
use App\Services\WebNotificationService;
use App\Services\FCMService;
use App\Notifications\OrderConfirmation;

class StripeController extends Controller
{
    protected $webNotificationService;

    public function __construct(WebNotificationService $webNotificationService)
    {
        $this->webNotificationService = $webNotificationService;
    }

    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate delivery information
        $request->validate([
            'delivery_method' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_method,delivery',
            'delivery_city' => 'required_if:delivery_method,delivery',
            'delivery_postcode' => 'required_if:delivery_method,delivery',
            'delivery_state' => 'required_if:delivery_method,delivery',
            'delivery_phone' => 'required_if:delivery_method,delivery',
        ]);

        // Store delivery information in session
        session([
            'delivery_info' => [
                'method' => $request->delivery_method,
                'address' => $request->delivery_address,
                'city' => $request->delivery_city,
                'postcode' => $request->delivery_postcode,
                'state' => $request->delivery_state,
                'phone' => $request->delivery_phone,
                'notes' => $request->notes,
            ]
        ]);
        
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

                        // Get delivery information from session
                        $deliveryInfo = session('delivery_info', [
                            'method' => 'pickup',
                            'address' => null,
                            'city' => null,
                            'postcode' => null,
                            'state' => null,
                            'phone' => null,
                            'notes' => null,
                        ]);

                        // Create order
                        $order = Order::create([
                            'order_number' => Order::generateOrderNumber(),
                            'user_id' => auth()->id(),
                            'payment_id' => $payment->id,
                            'total_amount' => $payment->amount,
                            'status' => 'confirmed',
                            'delivery_method' => $deliveryInfo['method'],
                            'delivery_address' => $deliveryInfo['address'],
                            'delivery_city' => $deliveryInfo['city'],
                            'delivery_postcode' => $deliveryInfo['postcode'],
                            'delivery_state' => $deliveryInfo['state'],
                            'delivery_phone' => $deliveryInfo['phone'],
                            'notes' => $deliveryInfo['notes'],
                        ]);

                        // Create order items and reduce product quantities
                        foreach ($cart as $productId => $quantityPurchased) {
                            $product = Product::find($productId);
                            if (!$product) {
                                continue;
                            }

                            // Create order item
                            OrderItem::create([
                                'order_id' => $order->id,
                                'product_id' => $product->id,
                                'product_name' => $product->name,
                                'product_price' => $product->price,
                                'quantity' => $quantityPurchased,
                                'subtotal' => $product->price * $quantityPurchased,
                            ]);

                            // Reduce product quantity
                            $newQuantity = max(0, $product->quantity - $quantityPurchased);
                            $product->update(['quantity' => $newQuantity]);
                        }

                        // Create shipping record
                        $carrier = $deliveryInfo['method'] === 'pickup' ? 'Self Pickup' : null;
                        Shipping::create([
                            'order_id' => $order->id,
                            'status' => 'preparing',
                            'carrier' => $carrier,
                            'estimated_delivery_date' => $deliveryInfo['method'] === 'delivery' 
                                ? now()->addDays(3) 
                                : null,
                        ]);

                        // Clear delivery info and cart from session
                        session()->forget('delivery_info');
                        session()->forget('cart');

                        // Load order relationships
                        $order->load(['items.product', 'shipping', 'user']);

                        // Send web notifications to owners and staff
                        try {
                            $this->webNotificationService->notifyNewOrder($order);
                        } catch (\Exception $e) {
                            // Log the error but don't fail the order creation
                            \Log::error('Failed to send web notification for new order: ' . $e->getMessage());
                        }

                        // Send order confirmation email with invoice
                        try {
                            $order->user->notify(new OrderConfirmation($order));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send order confirmation email', [
                                'order_id' => $order->id,
                                'error' => $e->getMessage()
                            ]);
                        }

                        // Send FCM notification to user for product purchase
                        try {
                            $fcmService = new FCMService();
                            $fcmService->sendOrderConfirmation($order->user_id, [
                                'order_id' => $order->id,
                                'order_number' => $order->order_number,
                                'total_amount' => $order->total_amount,
                                'item_count' => $order->items()->count()
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to send FCM notification for order', [
                                'order_id' => $order->id,
                                'error' => $e->getMessage()
                            ]);
                        }

                        // Pass order to success view
                        return view('cart.success', compact('order'));
                    }
                }
            } catch (\Exception $e) {
                // Log error but still clear cart and show success
                \Log::error('Stripe session verification failed: ' . $e->getMessage());
            }
        }
        
        // Clear cart after successful payment (fallback)
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