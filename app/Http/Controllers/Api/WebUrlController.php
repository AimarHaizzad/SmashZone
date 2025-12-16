<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebUrlController extends Controller
{
    /**
     * Generate authenticated web URL for mobile app users
     */
    public function generateWebUrl(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        // Get page type from request (default: dashboard)
        $pageType = $request->get('page', 'dashboard');
        
        // Map page types to target pages for mobile-auth route
        $targetPages = [
            'dashboard' => 'dashboard',
            'profile' => 'profile',
            'bookings' => 'bookings',
            'courts' => 'courts',
            'payments' => 'payments',
            'booking' => 'booking',
            'products' => 'products'
        ];
        
        $targetPage = $targetPages[$pageType] ?? 'dashboard';
        
        // Generate mobile-auth URL with user data
        // For Android Studio: Use APP_URL from .env or fallback to Render production URL
        // For local Android emulator testing, set APP_URL in .env to your local IP (e.g., http://192.168.1.100:8000)
        // For Android emulator, use http://10.0.2.2:8000 (10.0.2.2 is the emulator's alias for localhost)
        $baseUrl = config('app.url', 'https://smashzone-ywoa.onrender.com');
        $webUrl = $baseUrl . '/mobile-auth?' . http_build_query([
            'authenticated' => 'true',
            'user_id' => $user->id,
            'username' => $user->name,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'auth_token' => $request->bearerToken(),
            'target' => $targetPage
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Web URL generated successfully for $pageType page",
            'web_url' => $webUrl,
            'page_type' => $pageType,
            'target_page' => $targetPage,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'position' => $user->position,
                'role' => $user->role,
            ]
        ]);
    }
}
