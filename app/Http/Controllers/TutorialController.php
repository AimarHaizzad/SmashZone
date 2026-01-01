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
            $user->tutorial_completed = true;
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
}
