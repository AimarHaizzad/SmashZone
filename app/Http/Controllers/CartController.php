<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index(Request $request)
    {
        try {
            $cart = session('cart', []);
            
            // Try to get products, but handle database errors gracefully
            try {
                $products = Product::whereIn('id', array_keys($cart))->get();
            } catch (\Exception $e) {
                \Log::error('Failed to load products for cart', [
                    'error' => $e->getMessage(),
                    'cart_keys' => array_keys($cart)
                ]);
                $products = collect([]);
            }
            
            // Check if user should see cart page tutorial (first time on this page)
            $user = auth()->user();
            $showTutorial = $user && $user->isCustomer() && !session('cart_tutorial_shown', false) && count($cart) > 0;
            if ($showTutorial) {
                session(['cart_tutorial_shown' => true]);
            }
            
            return view('cart.index', compact('cart', 'products', 'showTutorial'));
        } catch (\Exception $e) {
            \Log::error('Cart index failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return empty cart view on error
            return view('cart.index', [
                'cart' => [],
                'products' => collect([]),
                'showTutorial' => false
            ])->with('error', 'Unable to load cart. Please try again.');
        }
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
        try {
            $quantities = $request->input('quantities', []);
            $cart = session('cart', []);
            $messages = [];

            if (empty($quantities)) {
                return redirect()->route('cart.index', absolute: false)->with('error', 'No quantities provided.');
            }

            foreach ($quantities as $productId => $quantity) {
                try {
                    // Ensure productId is valid
                    $productId = (int)$productId;
                    if ($productId <= 0) {
                        continue;
                    }

                    // Try to find product, handle database errors
                    try {
                        $product = Product::find($productId);
                    } catch (\Exception $dbException) {
                        \Log::warning('Database error finding product', [
                            'product_id' => $productId,
                            'error' => $dbException->getMessage()
                        ]);
                        // If database fails, keep the quantity as requested (best effort)
                        $cart[$productId] = max(1, (int)$quantity);
                        continue;
                    }

                    if (!$product) {
                        unset($cart[$productId]);
                        $messages[] = "Removed unavailable product #{$productId} from your cart.";
                        continue;
                    }

                    $requestedQuantity = max(1, (int)$quantity);
                    if ($requestedQuantity <= 0) {
                        $requestedQuantity = 1;
                    }

                    // Safely get product quantity with fallback
                    $productQuantity = isset($product->quantity) ? (int)$product->quantity : 0;
                    $allowedQuantity = min($requestedQuantity, max(0, $productQuantity));

                    if ($allowedQuantity === 0) {
                        unset($cart[$productId]);
                        $productName = $product->name ?? "Product #{$productId}";
                        $messages[] = "{$productName} is out of stock and was removed from your cart.";
                        continue;
                    }

                    if ($allowedQuantity < $requestedQuantity) {
                        $productName = $product->name ?? "Product #{$productId}";
                        $messages[] = "Quantity for {$productName} was reduced to {$allowedQuantity} due to limited stock.";
                    }

                    $cart[$productId] = $allowedQuantity;
                } catch (\Exception $e) {
                    // Log error but continue processing other items
                    \Log::warning('Error updating cart item', [
                        'product_id' => $productId ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Remove problematic item from cart
                    if (isset($productId)) {
                        unset($cart[$productId]);
                    }
                }
            }

            // Only update session if cart has items, otherwise clear it
            if (empty($cart)) {
                session()->forget('cart');
            } else {
                session(['cart' => $cart]);
            }

            if (!empty($messages)) {
                return redirect()->route('cart.index', absolute: false)->with('info', implode(' ', $messages));
            }

            return redirect()->route('cart.index', absolute: false)->with('success', 'Cart updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Cart update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token'])
            ]);
            
            // Try to preserve cart state even on error
            try {
                $currentCart = session('cart', []);
                if (!empty($currentCart)) {
                    session(['cart' => $currentCart]);
                }
            } catch (\Exception $sessionException) {
                \Log::warning('Failed to preserve cart on error', ['error' => $sessionException->getMessage()]);
            }
            
            return redirect()->route('cart.index', absolute: false)
                ->with('error', 'Failed to update cart. Please try again.');
        }
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