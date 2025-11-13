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
            // Get products - adjust table name according to your database
            // Assuming you have a 'products' table
            $products = DB::table('products')
                ->select(
                    'id',
                    'name',
                    DB::raw('CONCAT("RM ", FORMAT(price, 2)) as price'),
                    'image',
                    'description',
                    'category'
                )
                ->where('status', 'active')
                ->orWhereNull('status')
                ->orderBy('name', 'asc')
                ->get();
            
            // Get unique categories
            $categories = DB::table('products')
                ->select('category')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->toArray();
            
            return response()->json([
                'success' => true,
                'products' => $products,
                'categories' => $categories
            ]);
            
        } catch (\Exception $e) {
            // If table doesn't exist or there's an error, return empty data
            return response()->json([
                'success' => true,
                'products' => [],
                'categories' => []
            ]);
        }
    }
}

