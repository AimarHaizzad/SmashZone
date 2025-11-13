<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    /**
     * Get products data for mobile app
     * Returns list of available products
     */
    public function getProducts(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            // Check if products table exists
            if (!DB::getSchemaBuilder()->hasTable('products')) {
                return response()->json([
                    'success' => true,
                    'products' => [],
                    'categories' => []
                ]);
            }
            
            // Get all products - less restrictive query
            $query = DB::table('products');
            
            // Try to get products with flexible column selection
            try {
                $products = $query->select(
                    'id',
                    'name',
                    DB::raw('COALESCE(CONCAT("RM ", FORMAT(price, 2)), "RM 0.00") as price'),
                    DB::raw('COALESCE(image, "") as image'),
                    DB::raw('COALESCE(description, "") as description'),
                    DB::raw('COALESCE(category, "") as category')
                );
                
                // Only filter by status if status column exists and is not null
                if (DB::getSchemaBuilder()->hasColumn('products', 'status')) {
                    $products = $products->where(function($q) {
                        $q->where('status', 'active')
                          ->orWhere('status', '=', '')
                          ->orWhereNull('status');
                    });
                }
                
                $products = $products->orderBy('name', 'asc')->get();
                
            } catch (\Exception $e) {
                // If column selection fails, try simpler query
                $products = $query->select('*')->get();
                
                // Format the products manually
                $products = $products->map(function($product) {
                    return (object)[
                        'id' => (string)($product->id ?? ''),
                        'name' => $product->name ?? 'Product',
                        'price' => isset($product->price) ? 'RM ' . number_format($product->price, 2) : 'RM 0.00',
                        'image' => $product->image ?? '',
                        'description' => $product->description ?? '',
                        'category' => $product->category ?? ''
                    ];
                });
            }
            
            // Get unique categories
            $categories = [];
            try {
                if (DB::getSchemaBuilder()->hasColumn('products', 'category')) {
                    $categories = DB::table('products')
                        ->select('category')
                        ->whereNotNull('category')
                        ->where('category', '!=', '')
                        ->distinct()
                        ->pluck('category')
                        ->toArray();
                }
            } catch (\Exception $e) {
                // Categories not available
            }
            
            return response()->json([
                'success' => true,
                'products' => $products,
                'categories' => $categories
            ]);
            
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('ProductsController Error: ' . $e->getMessage());
            
            // If table doesn't exist or there's an error, return empty data
            return response()->json([
                'success' => true,
                'products' => [],
                'categories' => []
            ]);
        }
    }
}
