<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MobileAppAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is already authenticated
        if (Auth::check()) {
            return $next($request);
        }

        // Check for mobile app authentication parameters
        $isAuthenticated = $request->get('authenticated') === 'true';
        $userId = $request->get('user_id');
        $authToken = $request->get('auth_token');

        if ($isAuthenticated && $userId && $authToken) {
            // Find user by ID
            $user = User::find($userId);
            
            if ($user) {
                // Verify the token (you might want to add more validation here)
                $user->tokens()->where('token', hash('sha256', $authToken))->first();
                
                // Log the user in
                Auth::login($user);
                
                // Store mobile app authentication in session
                session(['mobile_app_auth' => true]);
                session(['mobile_app_token' => $authToken]);
                
                // Clean URL parameters
                $cleanUrl = $request->url();
                return redirect($cleanUrl);
            }
        }

        return $next($request);
    }
}
