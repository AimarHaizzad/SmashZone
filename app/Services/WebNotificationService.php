<?php

namespace App\Services;

use App\Models\WebNotification;
use App\Models\User;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class WebNotificationService
{
    /**
     * Create a new web notification.
     */
    public function create(array $data): WebNotification
    {
        return WebNotification::create($data);
    }

    /**
     * Notify about new booking.
     */
    public function notifyNewBooking(Booking $booking): void
    {
        $court = $booking->court;
        $customer = $booking->user;
        
        // Notify court owner
        if ($court->owner) {
            $this->create([
                'user_id' => $court->owner->id,
                'type' => 'booking_created',
                'title' => 'New Booking Received',
                'message' => "{$customer->name} has booked {$court->name} for {$booking->date} at {$booking->start_time}",
                'data' => [
                    'booking_id' => $booking->id,
                    'court_id' => $court->id,
                    'customer_id' => $customer->id,
                    'booking_date' => $booking->date,
                    'booking_time' => $booking->start_time,
                ],
            ]);
        }

        // Notify all staff members
        $staffUsers = User::where('role', 'staff')->get();
        foreach ($staffUsers as $staff) {
            $this->create([
                'user_id' => $staff->id,
                'type' => 'booking_created',
                'title' => 'New Booking Created',
                'message' => "{$customer->name} has booked {$court->name} for {$booking->date} at {$booking->start_time}",
                'data' => [
                    'booking_id' => $booking->id,
                    'court_id' => $court->id,
                    'customer_id' => $customer->id,
                    'booking_date' => $booking->date,
                    'booking_time' => $booking->start_time,
                ],
            ]);
        }

        Log::info("Web notifications sent for new booking: {$booking->id}");
    }

    /**
     * Notify about booking cancellation.
     */
    public function notifyBookingCancelled(Booking $booking): void
    {
        $court = $booking->court;
        $customer = $booking->user;
        
        // Notify court owner
        if ($court->owner) {
            $this->create([
                'user_id' => $court->owner->id,
                'type' => 'booking_cancelled',
                'title' => 'Booking Cancelled',
                'message' => "{$customer->name} has cancelled their booking for {$court->name} on {$booking->date}",
                'data' => [
                    'booking_id' => $booking->id,
                    'court_id' => $court->id,
                    'customer_id' => $customer->id,
                    'booking_date' => $booking->date,
                ],
            ]);
        }

        // Notify all staff members
        $staffUsers = User::where('role', 'staff')->get();
        foreach ($staffUsers as $staff) {
            $this->create([
                'user_id' => $staff->id,
                'type' => 'booking_cancelled',
                'title' => 'Booking Cancelled',
                'message' => "{$customer->name} has cancelled their booking for {$court->name} on {$booking->date}",
                'data' => [
                    'booking_id' => $booking->id,
                    'court_id' => $court->id,
                    'customer_id' => $customer->id,
                    'booking_date' => $booking->date,
                ],
            ]);
        }

        Log::info("Web notifications sent for cancelled booking: {$booking->id}");
    }

    /**
     * Notify about booking completion.
     */
    public function notifyBookingCompleted(Booking $booking): void
    {
        $court = $booking->court;
        $customer = $booking->user;
        
        // Notify court owner
        if ($court->owner) {
            $this->create([
                'user_id' => $court->owner->id,
                'type' => 'booking_completed',
                'title' => 'Booking Completed',
                'message' => "Booking for {$court->name} on {$booking->date} has been marked as completed",
                'data' => [
                    'booking_id' => $booking->id,
                    'court_id' => $court->id,
                    'customer_id' => $customer->id,
                    'booking_date' => $booking->date,
                ],
            ]);
        }

        // Notify customer
        $this->create([
            'user_id' => $customer->id,
            'type' => 'booking_completed',
            'title' => 'Session Completed',
            'message' => "Your booking for {$court->name} on {$booking->date} has been completed. Thank you for playing!",
            'data' => [
                'booking_id' => $booking->id,
                'court_id' => $court->id,
                'booking_date' => $booking->date,
            ],
        ]);

        Log::info("Web notifications sent for completed booking: {$booking->id}");
    }

    /**
     * Notify about payment received.
     */
    public function notifyPaymentReceived(Booking $booking): void
    {
        $court = $booking->court;
        $customer = $booking->user;
        
        // Notify court owner
        if ($court->owner) {
            $this->create([
                'user_id' => $court->owner->id,
                'type' => 'payment_received',
                'title' => 'Payment Received',
                'message' => "Payment of RM {$booking->total_price} received for booking at {$court->name}",
                'data' => [
                    'booking_id' => $booking->id,
                    'court_id' => $court->id,
                    'customer_id' => $customer->id,
                    'amount' => $booking->total_price,
                    'booking_date' => $booking->date,
                ],
            ]);
        }

        // Notify all staff members
        $staffUsers = User::where('role', 'staff')->get();
        foreach ($staffUsers as $staff) {
            $this->create([
                'user_id' => $staff->id,
                'type' => 'payment_received',
                'title' => 'Payment Received',
                'message' => "Payment of RM {$booking->total_price} received for booking at {$court->name}",
                'data' => [
                    'booking_id' => $booking->id,
                    'court_id' => $court->id,
                    'customer_id' => $customer->id,
                    'amount' => $booking->total_price,
                    'booking_date' => $booking->date,
                ],
            ]);
        }

        Log::info("Web notifications sent for payment received: {$booking->id}");
    }

    /**
     * Notify about new court added.
     */
    public function notifyNewCourt(Court $court): void
    {
        // Notify all staff members
        $staffUsers = User::where('role', 'staff')->get();
        foreach ($staffUsers as $staff) {
            $this->create([
                'user_id' => $staff->id,
                'type' => 'court_added',
                'title' => 'New Court Added',
                'message' => "A new court '{$court->name}' has been added to the system",
                'data' => [
                    'court_id' => $court->id,
                    'court_name' => $court->name,
                    'owner_id' => $court->owner_id,
                ],
            ]);
        }

        Log::info("Web notifications sent for new court: {$court->id}");
    }

    /**
     * Notify about court updated.
     */
    public function notifyCourtUpdated(Court $court): void
    {
        // Notify all staff members
        $staffUsers = User::where('role', 'staff')->get();
        foreach ($staffUsers as $staff) {
            $this->create([
                'user_id' => $staff->id,
                'type' => 'court_updated',
                'title' => 'Court Updated',
                'message' => "Court '{$court->name}' has been updated",
                'data' => [
                    'court_id' => $court->id,
                    'court_name' => $court->name,
                    'owner_id' => $court->owner_id,
                ],
            ]);
        }

        Log::info("Web notifications sent for court update: {$court->id}");
    }

    /**
     * Notify about court deleted.
     */
    public function notifyCourtDeleted(string $courtName, int $ownerId): void
    {
        // Notify all staff members
        $staffUsers = User::where('role', 'staff')->get();
        foreach ($staffUsers as $staff) {
            $this->create([
                'user_id' => $staff->id,
                'type' => 'court_deleted',
                'title' => 'Court Deleted',
                'message' => "Court '{$courtName}' has been deleted from the system",
                'data' => [
                    'court_name' => $courtName,
                    'owner_id' => $ownerId,
                ],
            ]);
        }

        Log::info("Web notifications sent for court deletion: {$courtName}");
    }

    /**
     * Notify about new order created.
     */
    public function notifyNewOrder(Order $order): void
    {
        $customer = $order->user;
        $itemCount = $order->items->sum('quantity');
        $itemText = $itemCount === 1 ? 'item' : 'items';
        
        // Notify all owners
        $owners = User::where('role', 'owner')->get();
        foreach ($owners as $owner) {
            $this->create([
                'user_id' => $owner->id,
                'type' => 'order_created',
                'title' => 'New Order Received',
                'message' => "New order #{$order->order_number} from {$customer->name} - {$itemCount} {$itemText} (RM " . number_format($order->total_amount, 2) . ")",
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'total_amount' => $order->total_amount,
                    'item_count' => $itemCount,
                    'delivery_method' => $order->delivery_method,
                    'status' => $order->status,
                ],
            ]);
        }

        // Notify all staff members
        $staffUsers = User::where('role', 'staff')->get();
        foreach ($staffUsers as $staff) {
            $this->create([
                'user_id' => $staff->id,
                'type' => 'order_created',
                'title' => 'New Order Received',
                'message' => "New order #{$order->order_number} from {$customer->name} - {$itemCount} {$itemText} (RM " . number_format($order->total_amount, 2) . ")",
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'total_amount' => $order->total_amount,
                    'item_count' => $itemCount,
                    'delivery_method' => $order->delivery_method,
                    'status' => $order->status,
                ],
            ]);
        }

        Log::info("Web notifications sent for new order: {$order->order_number}");
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = WebNotification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        return WebNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get unread notifications count for a user.
     */
    public function getUnreadCount(int $userId): int
    {
        return WebNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get recent notifications for a user.
     */
    public function getRecentNotifications(int $userId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return WebNotification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
