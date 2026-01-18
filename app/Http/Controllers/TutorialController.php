<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutorialController extends Controller
{
    /**
     * Mark tutorial as completed for the authenticated user.
     */
    public function complete(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $tutorialType = $request->input('type', 'dashboard'); // default to dashboard
            
            // Mark specific tutorial as completed based on type
            switch ($tutorialType) {
                case 'booking':
                    $user->booking_tutorial_completed = true;
                    break;
                case 'products':
                    $user->products_tutorial_completed = true;
                    break;
                case 'dashboard':
                default:
                    $user->tutorial_completed = true;
                    break;
            }
            
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Tutorial marked as completed'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }
    
    /**
     * Reset tutorial for the authenticated user (restart tutorial).
     */
    public function restart(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            // Reset all tutorial flags
            $user->tutorial_completed = false;
            $user->booking_tutorial_completed = false;
            $user->products_tutorial_completed = false;
            $user->save();
            
            // Clear session flags for page-specific tutorials (legacy support)
            session()->forget(['booking_tutorial_shown', 'cart_tutorial_shown', 'products_tutorial_shown', 'courts_tutorial_shown']);
            
            return response()->json([
                'success' => true,
                'message' => 'Tutorial reset successfully. You can now see the tutorial again.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }
}
