<?php

namespace App\Http\Controllers;

use App\Models\WebNotification;
use App\Services\WebNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WebNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(WebNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->get('limit', 20);
        $unreadOnly = $request->get('unread_only', false);

        $query = $user->webNotifications()->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->unread();
        }

        $notifications = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $user->unreadWebNotificationsCount(),
        ]);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = $user->unreadWebNotificationsCount();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $success = $this->notificationService->markAsRead($id, $user->id);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification not found',
        ], 404);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user->id);

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notifications as read",
            'marked_count' => $count,
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $notification = WebNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($notification) {
            $notification->delete();
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification not found',
        ], 404);
    }

    /**
     * Get notification statistics.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();
        
        $stats = [
            'total' => $user->webNotifications()->count(),
            'unread' => $user->unreadWebNotificationsCount(),
            'read' => $user->webNotifications()->read()->count(),
            'today' => $user->webNotifications()->whereDate('created_at', today())->count(),
            'this_week' => $user->webNotifications()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get notifications by type.
     */
    public function byType(Request $request, string $type): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->get('limit', 20);

        $notifications = $user->webNotifications()
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'type' => $type,
        ]);
    }
}