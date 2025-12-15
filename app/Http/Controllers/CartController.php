<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get();
        return view('cart.index', compact('cart', 'products'));
    }

    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = max(1, (int)$request->input('quantity', 1));

        $product = Product::find($productId);
        if (!$product) {
            return redirect()->back()->withErrors(['product' => 'Product no longer exists.']);
        }

        if ($product->quantity <= 0) {
            return redirect()->back()->withErrors(['product' => 'This product is out of stock.']);
        }

        $cart = session('cart', []);
        $currentQuantity = $cart[$productId] ?? 0;
        $availableToAdd = max(0, $product->quantity - $currentQuantity);

        if ($availableToAdd <= 0) {
            return redirect()->back()->withErrors(['product' => 'You already have the maximum available quantity in your cart.']);
        }

        $quantityToAdd = min($quantity, $availableToAdd);
        $cart[$productId] = $currentQuantity + $quantityToAdd;
        session(['cart' => $cart]);

        $message = $quantityToAdd < $quantity
            ? "Only {$quantityToAdd} item(s) were added due to limited stock."
            : 'Product added to cart!';

        return redirect()->back()->with('success', $message);
    }

    public function update(Request $request)
    {
        $quantities = $request->input('quantities', []);
        $cart = session('cart', []);
        $messages = [];

        foreach ($quantities as $productId => $quantity) {
            $product = Product::find($productId);
            if (!$product) {
                unset($cart[$productId]);
                $messages[] = "Removed unavailable product #{$productId} from your cart.";
                continue;
            }

            $requestedQuantity = max(1, (int)$quantity);
            $allowedQuantity = min($requestedQuantity, max(0, $product->quantity));

            if ($allowedQuantity === 0) {
                unset($cart[$productId]);
                $messages[] = "{$product->name} is out of stock and was removed from your cart.";
                continue;
            }

            if ($allowedQuantity < $requestedQuantity) {
                $messages[] = "Quantity for {$product->name} was reduced to {$allowedQuantity} due to limited stock.";
            }

            $cart[$productId] = $allowedQuantity;
        }

        session(['cart' => $cart]);

        if (!empty($messages)) {
            return redirect()->route('cart.index', absolute: false)->with('info', implode(' ', $messages));
        }

        return redirect()->route('cart.index', absolute: false)->with('success', 'Cart updated successfully.');
    }

    public function remove(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);
        return redirect()->back();
    }

    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index', absolute: false)->with('error', 'Your cart is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $total = 0;
        foreach ($products as $product) {
            $total += $product->price * ($cart[$product->id] ?? 0);
        }

        return view('cart.checkout', compact('cart', 'products', 'total'));
    }
} 