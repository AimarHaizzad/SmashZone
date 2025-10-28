<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\User;
use App\Services\FCMService;
use Carbon\Carbon;

class BookingNotificationController extends Controller
{
    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Send booking confirmation notification
     */
    public function sendBookingConfirmation($bookingId)
    {
        $booking = Booking::with(['user', 'court'])->find($bookingId);
        
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $user = $booking->user;
        $courtName = $booking->court->name ?? 'Unknown Court';
        $bookingDate = Carbon::parse($booking->date)->format('M d, Y');
        $startTime = Carbon::parse($booking->start_time)->format('h:i A');

        $title = "Booking Confirmed! 🎾";
        $body = "Your booking at {$courtName} on {$bookingDate} at {$startTime} has been confirmed.";
        
        $data = [
            'type' => 'booking_confirmed',
            'booking_id' => $booking->id,
            'court_name' => $courtName,
            'booking_date' => $booking->date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'total_price' => $booking->total_price
        ];

        $result = $this->fcmService->sendToUser($user->id, $title, $body, $data);

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmation sent',
            'notification_result' => $result
        ]);
    }

    /**
     * Send booking reminder notification (24 hours before)
     */
    public function sendBookingReminder($bookingId)
    {
        $booking = Booking::with(['user', 'court'])->find($bookingId);
        
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $user = $booking->user;
        $courtName = $booking->court->name ?? 'Unknown Court';
        $bookingDate = Carbon::parse($booking->date)->format('M d, Y');
        $startTime = Carbon::parse($booking->start_time)->format('h:i A');

        $title = "Booking Reminder ⏰";
        $body = "Don't forget! Your booking at {$courtName} is tomorrow at {$startTime}.";
        
        $data = [
            'type' => 'booking_reminder',
            'booking_id' => $booking->id,
            'court_name' => $courtName,
            'booking_date' => $booking->date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time
        ];

        $result = $this->fcmService->sendToUser($user->id, $title, $body, $data);

        return response()->json([
            'success' => true,
            'message' => 'Booking reminder sent',
            'notification_result' => $result
        ]);
    }

    /**
     * Send booking starting soon notification (30 minutes before)
     */
    public function sendBookingStartingSoon($bookingId)
    {
        $booking = Booking::with(['user', 'court'])->find($bookingId);
        
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $user = $booking->user;
        $courtName = $booking->court->name ?? 'Unknown Court';
        $startTime = Carbon::parse($booking->start_time)->format('h:i A');

        $title = "Booking Starting Soon! 🚀";
        $body = "Your booking at {$courtName} starts in 30 minutes at {$startTime}.";
        
        $data = [
            'type' => 'booking_starting_soon',
            'booking_id' => $booking->id,
            'court_name' => $courtName,
            'booking_date' => $booking->date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time
        ];

        $result = $this->fcmService->sendToUser($user->id, $title, $body, $data);

        return response()->json([
            'success' => true,
            'message' => 'Booking starting soon notification sent',
            'notification_result' => $result
        ]);
    }

    /**
     * Send booking cancelled notification
     */
    public function sendBookingCancelled($bookingId)
    {
        $booking = Booking::with(['user', 'court'])->find($bookingId);
        
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $user = $booking->user;
        $courtName = $booking->court->name ?? 'Unknown Court';
        $bookingDate = Carbon::parse($booking->date)->format('M d, Y');
        $startTime = Carbon::parse($booking->start_time)->format('h:i A');

        $title = "Booking Cancelled ❌";
        $body = "Your booking at {$courtName} on {$bookingDate} at {$startTime} has been cancelled.";
        
        $data = [
            'type' => 'booking_cancelled',
            'booking_id' => $booking->id,
            'court_name' => $courtName,
            'booking_date' => $booking->date,
            'start_time' => $booking->start_time,
            'refund_amount' => $booking->total_price
        ];

        $result = $this->fcmService->sendToUser($user->id, $title, $body, $data);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled notification sent',
            'notification_result' => $result
        ]);
    }

    /**
     * Send payment reminder notification
     */
    public function sendPaymentReminder($bookingId)
    {
        $booking = Booking::with(['user', 'court'])->find($bookingId);
        
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $user = $booking->user;
        $courtName = $booking->court->name ?? 'Unknown Court';
        $totalPrice = number_format($booking->total_price, 2);

        $title = "Payment Reminder 💳";
        $body = "Please complete payment of RM {$totalPrice} for your booking at {$courtName}.";
        
        $data = [
            'type' => 'payment_reminder',
            'booking_id' => $booking->id,
            'court_name' => $courtName,
            'total_price' => $booking->total_price,
            'payment_due_date' => $booking->created_at->addDays(1)->format('Y-m-d')
        ];

        $result = $this->fcmService->sendToUser($user->id, $title, $body, $data);

        return response()->json([
            'success' => true,
            'message' => 'Payment reminder sent',
            'notification_result' => $result
        ]);
    }

    /**
     * Send all pending booking reminders (cron job)
     */
    public function sendPendingReminders()
    {
        $now = Carbon::now();
        $tomorrow = $now->addDay();
        
        // Get bookings for tomorrow
        $tomorrowBookings = Booking::with(['user', 'court'])
            ->whereDate('date', $tomorrow->format('Y-m-d'))
            ->where('status', 'confirmed')
            ->get();

        $sentCount = 0;
        $errors = [];

        foreach ($tomorrowBookings as $booking) {
            try {
                $this->sendBookingReminder($booking->id);
                $sentCount++;
            } catch (\Exception $e) {
                $errors[] = "Booking {$booking->id}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Sent {$sentCount} booking reminders",
            'errors' => $errors
        ]);
    }

    /**
     * Send all starting soon notifications (cron job)
     */
    public function sendStartingSoonNotifications()
    {
        $now = Carbon::now();
        $in30Minutes = $now->addMinutes(30);
        
        // Get bookings starting in 30 minutes
        $startingSoonBookings = Booking::with(['user', 'court'])
            ->whereDate('date', $now->format('Y-m-d'))
            ->whereTime('start_time', '<=', $in30Minutes->format('H:i:s'))
            ->whereTime('start_time', '>', $now->format('H:i:s'))
            ->where('status', 'confirmed')
            ->get();

        $sentCount = 0;
        $errors = [];

        foreach ($startingSoonBookings as $booking) {
            try {
                $this->sendBookingStartingSoon($booking->id);
                $sentCount++;
            } catch (\Exception $e) {
                $errors[] = "Booking {$booking->id}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Sent {$sentCount} starting soon notifications",
            'errors' => $errors
        ]);
    }

    /**
     * Test booking notification
     */
    public function testBookingNotification(Request $request)
    {
        $userId = $request->input('user_id', 1);
        $type = $request->input('type', 'booking_confirmed');
        
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $title = "Test Booking Notification 🧪";
        $body = "This is a test {$type} notification for user {$user->name}.";
        
        $data = [
            'type' => $type,
            'booking_id' => 999,
            'court_name' => 'Test Court',
            'booking_date' => now()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00'
        ];

        $result = $this->fcmService->sendToUser($user->id, $title, $body, $data);

        return response()->json([
            'success' => true,
            'message' => 'Test booking notification sent',
            'notification_result' => $result
        ]);
    }
}
