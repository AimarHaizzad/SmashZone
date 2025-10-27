<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Store or update user's FCM token
     */
    public function storeFCMToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = $request->user();
        $fcmToken = $request->input('fcm_token');

        try {
            $existing = DB::table('fcm_tokens')
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                DB::table('fcm_tokens')
                    ->where('user_id', $user->id)
                    ->update([
                        'token' => $fcmToken,
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('fcm_tokens')->insert([
                    'user_id' => $user->id,
                    'token' => $fcmToken,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'FCM token stored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store FCM token'
            ], 500);
        }
    }

    /**
     * Delete user's FCM token (for logout)
     */
    public function deleteFCMToken(Request $request)
    {
        $user = $request->user();

        try {
            DB::table('fcm_tokens')
                ->where('user_id', $user->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'FCM token deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete FCM token'
            ], 500);
        }
    }

    /**
     * Get user's notification history
     */
    public function getNotifications(Request $request)
    {
        $user = $request->user();

        try {
            $notifications = DB::table('notifications')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications'
            ], 500);
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(Request $request)
    {
        $user = $request->user();

        try {
            $count = DB::table('notifications')
                ->where('user_id', $user->id)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count'
            ], 500);
        }
    }
}
