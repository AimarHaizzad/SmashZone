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
        $cart = session('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        session(['cart' => $cart]);
        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request)
    {
        $quantities = $request->input('quantities', []);
        $cart = session('cart', []);
        foreach ($quantities as $productId => $quantity) {
            $cart[$productId] = max(1, (int)$quantity);
        }
        session(['cart' => $cart]);
        return redirect()->route('cart.index');
    }

    public function remove(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);
        return redirect()->back();
    }
} 