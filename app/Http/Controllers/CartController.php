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
            
            // Ensure cart is an array
            if (!is_array($cart)) {
                $cart = [];
                session(['cart' => $cart]);
            }
            
            // Try to get products, but handle database errors gracefully
            $products = collect([]);
            if (!empty($cart)) {
                try {
                    $productIds = array_keys($cart);
                    if (!empty($productIds)) {
                        $products = Product::whereIn('id', $productIds)->get();
                        
                        // Remove products from cart that no longer exist in database
                        $existingProductIds = $products->pluck('id')->toArray();
                        $missingProductIds = array_diff($productIds, $existingProductIds);
                        
                        if (!empty($missingProductIds)) {
                            foreach ($missingProductIds as $missingId) {
                                unset($cart[$missingId]);
                            }
                            if (empty($cart)) {
                                session()->forget('cart');
                            } else {
                                session(['cart' => $cart]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to load products for cart', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'cart_keys' => array_keys($cart)
                    ]);
                    $products = collect([]);
                }
            }
            
            // Check if user should see cart page tutorial (first time users only)
            $user = auth()->user();
            $showTutorial = $user && $user->isCustomer() && !$user->tutorial_completed && !session('cart_tutorial_shown', false) && count($cart) > 0;
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
            // Ensure cart session exists and is an array
            $cart = session('cart', []);
            if (!is_array($cart)) {
                $cart = [];
                session(['cart' => $cart]);
            }
            
            // Get quantities and ensure it's an array
            $quantities = $request->input('quantities', []);
            if (!is_array($quantities)) {
                $quantities = [];
            }
            
            $messages = [];

            if (empty($quantities) && empty($cart)) {
                // Cart is already empty, just redirect
                return redirect()->route('cart.index')->with('info', 'Your cart is empty.');
            }
            
            if (empty($quantities)) {
                // No quantities provided but cart has items - might be a form issue
                \Log::warning('Cart update called with empty quantities', [
                    'cart_items' => array_keys($cart),
                    'request_all' => $request->all()
                ]);
                return redirect()->route('cart.index')->with('error', 'No quantities provided. Please try again.');
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
            try {
                if (empty($cart)) {
                    session()->forget('cart');
                } else {
                    session(['cart' => $cart]);
                    // Ensure session is saved
                    session()->save();
                }
            } catch (\Exception $sessionException) {
                \Log::error('Failed to save cart to session', [
                    'error' => $sessionException->getMessage(),
                    'cart_count' => count($cart)
                ]);
                // Continue anyway - try to redirect
            }

            // Build redirect with messages
            if (!empty($messages)) {
                return redirect()->route('cart.index')->with('info', implode(' ', $messages));
            } else {
                return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
            }
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
            
            return redirect()->route('cart.index')
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
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $total = 0;
        foreach ($products as $product) {
            $total += $product->price * ($cart[$product->id] ?? 0);
        }

        return view('cart.checkout', compact('cart', 'products', 'total'));
    }
} 