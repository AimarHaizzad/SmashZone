<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Product;

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
        foreach ($products as $product) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'myr',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => (int)($product->price * 100),
                ],
                'quantity' => $cart[$product->id],
            ];
        }
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = StripeSession::create([
            'payment_method_types' => ['card', 'fpx'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url' => route('stripe.cancel'),
        ]);
        return redirect($session->url);
    }

    public function success()
    {
        session()->forget('cart');
        return view('cart.success');
    }

    public function cancel()
    {
        return view('cart.cancel');
    }
} 