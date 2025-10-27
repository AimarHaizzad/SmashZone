<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FCMService
{
    private $serverKey;
    private $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = config('services.firebase.server_key');
    }

    /**
     * Send notification to a single user
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        $token = DB::table('fcm_tokens')
            ->where('user_id', $userId)
            ->value('token');

        if (!$token) {
            Log::warning("No FCM token found for user: $userId");
            return false;
        }

        return $this->sendNotification($token, $title, $body, $data);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers($userIds, $title, $body, $data = [])
    {
        $tokens = DB::table('fcm_tokens')
            ->whereIn('user_id', $userIds)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            Log::warning("No FCM tokens found for users");
            return false;
        }

        return $this->sendMulticastNotification($tokens, $title, $body, $data);
    }

    /**
     * Send notification to specific token
     */
    private function sendNotification($token, $title, $body, $data = [])
    {
        $payload = [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
            'priority' => 'high'
        ];

        $this->saveNotification($token, $title, $body, $data);
        return $this->sendFCMRequest($payload);
    }

    /**
     * Send notification to multiple tokens
     */
    private function sendMulticastNotification($tokens, $title, $body, $data = [])
    {
        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
            'priority' => 'high'
        ];

        foreach ($tokens as $token) {
            $this->saveNotification($token, $title, $body, $data);
        }

        return $this->sendFCMRequest($payload);
    }

    /**
     * Send HTTP request to FCM
     */
    private function sendFCMRequest($payload)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json'
            ])->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                Log::info('FCM notification sent successfully');
                return true;
            } else {
                Log::error('FCM notification failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('FCM notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save notification to database
     */
    private function saveNotification($token, $title, $body, $data = [])
    {
        try {
            $userId = DB::table('fcm_tokens')
                ->where('token', $token)
                ->value('user_id');

            if ($userId) {
                DB::table('notifications')->insert([
                    'user_id' => $userId,
                    'title' => $title,
                    'body' => $body,
                    'type' => $data['type'] ?? 'general',
                    'data' => json_encode($data),
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to save notification: ' . $e->getMessage());
        }
    }

    /**
     * Send booking confirmation notification
     */
    public function sendBookingConfirmation($userId, $bookingDetails)
    {
        $title = "Booking Confirmed! 🏸";
        $body = "Your booking for {$bookingDetails['court_name']} on {$bookingDetails['date']} has been confirmed.";
        
        $data = [
            'type' => 'booking_confirmed',
            'booking_id' => $bookingDetails['booking_id'],
            'court_name' => $bookingDetails['court_name'],
            'date' => $bookingDetails['date'],
            'time' => $bookingDetails['time']
        ];

        return $this->sendToUser($userId, $title, $body, $data);
    }

    /**
     * Send booking reminder notification
     */
    public function sendBookingReminder($userId, $bookingDetails)
    {
        $title = "Booking Reminder ⏰";
        $body = "Your booking at {$bookingDetails['court_name']} is tomorrow at {$bookingDetails['time']}.";
        
        $data = [
            'type' => 'booking_reminder',
            'booking_id' => $bookingDetails['booking_id'],
            'court_name' => $bookingDetails['court_name'],
            'date' => $bookingDetails['date'],
            'time' => $bookingDetails['time']
        ];

        return $this->sendToUser($userId, $title, $body, $data);
    }

    /**
     * Send payment received notification
     */
    public function sendPaymentReceived($userId, $paymentDetails)
    {
        $title = "Payment Received ✅";
        $body = "Your payment of RM {$paymentDetails['amount']} has been received successfully.";
        
        $data = [
            'type' => 'payment_received',
            'payment_id' => $paymentDetails['payment_id'],
            'amount' => $paymentDetails['amount']
        ];

        return $this->sendToUser($userId, $title, $body, $data);
    }

    /**
     * Send booking cancelled notification
     */
    public function sendBookingCancelled($userId, $bookingDetails)
    {
        $title = "Booking Cancelled ❌";
        $body = "Your booking for {$bookingDetails['court_name']} on {$bookingDetails['date']} has been cancelled.";
        
        $data = [
            'type' => 'booking_cancelled',
            'booking_id' => $bookingDetails['booking_id'],
            'court_name' => $bookingDetails['court_name'],
            'date' => $bookingDetails['date']
        ];

        return $this->sendToUser($userId, $title, $body, $data);
    }
}
