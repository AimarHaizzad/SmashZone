<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FCMService
{
    private ?string $projectId;

    private array $serviceAccount = [];

    private string $fcmUrl;

    private $accessToken;

    private $tokenExpiry;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->fcmUrl = sprintf(
            'https://fcm.googleapis.com/v1/projects/%s/messages:send',
            $this->projectId ?? 'unset'
        );
        $this->serviceAccount = $this->loadServiceAccount() ?? [];
    }

    private function loadServiceAccount(): ?array
    {
        $json = null;

        $path = config('firebase.credentials_path');
        if ($path && is_readable($path)) {
            $json = file_get_contents($path);
        }

        if (! $json) {
            $json = config('firebase.credentials');
        }

        if (! $json && config('firebase.credentials_base64')) {
            $decoded = base64_decode((string) config('firebase.credentials_base64'), true);
            if ($decoded !== false) {
                $json = $decoded;
            }
        }

        if (! $json) {
            return null;
        }

        $account = json_decode($json, true);
        if (! is_array($account) || empty($account['private_key']) || empty($account['client_email'])) {
            Log::error('FCM: Invalid Firebase credentials configuration');

            return null;
        }

        return $account;
    }

    private function isConfigured(): bool
    {
        return ! empty($this->projectId) && ! empty($this->serviceAccount['private_key']);
    }

    /**
     * Get OAuth 2.0 access token
     */
    private function getAccessToken()
    {
        if (! $this->isConfigured()) {
            Log::warning('FCM: Push notifications are not configured');

            return false;
        }

        if ($this->accessToken && $this->isTokenValid()) {
            return $this->accessToken;
        }

        try {
            $jwt = [
                'iss' => $this->serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $this->serviceAccount['token_uri'],
                'iat' => time(),
                'exp' => time() + 3600,
            ];

            $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
            $payload = json_encode($jwt);

            $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

            $privateKey = $this->serviceAccount['private_key'];
            if (strpos($privateKey, '\\n') !== false) {
                $privateKey = str_replace('\\n', "\n", $privateKey);
            }

            $signature = '';
            $signResult = openssl_sign($base64Header.'.'.$base64Payload, $signature, $privateKey, 'SHA256');

            if (! $signResult) {
                Log::error('FCM: Failed to sign JWT', ['openssl_error' => openssl_error_string()]);

                return false;
            }

            $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            $jwtToken = $base64Header.'.'.$base64Payload.'.'.$base64Signature;

            $response = Http::asForm()->post($this->serviceAccount['token_uri'], [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwtToken,
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();
                $this->accessToken = $tokenData['access_token'] ?? null;
                $this->tokenExpiry = time() + ($tokenData['expires_in'] ?? 3600);

                return $this->accessToken ?: false;
            }

            Log::error('FCM: Failed to get access token', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('FCM: Access token error', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function isTokenValid(): bool
    {
        return isset($this->tokenExpiry) && $this->tokenExpiry > time() + 60;
    }

    public function sendToUser($userId, $title, $body, $data = [])
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $token = DB::table('fcm_tokens')->where('user_id', $userId)->value('token');

        if (! $token) {
            Log::warning("FCM: No FCM token found for user {$userId}");

            return false;
        }

        return $this->sendNotification($token, $title, $body, $data);
    }

    public function sendToUsers($userIds, $title, $body, $data = [])
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $tokens = DB::table('fcm_tokens')->whereIn('user_id', $userIds)->pluck('token')->toArray();

        if (empty($tokens)) {
            return false;
        }

        $results = [];
        foreach ($tokens as $token) {
            $results[] = $this->sendNotification($token, $title, $body, $data);
        }

        return $results;
    }

    private function sendNotification($token, $title, $body, $data = [])
    {
        $accessToken = $this->getAccessToken();
        if (! $accessToken) {
            return false;
        }

        $stringData = [];
        foreach ($data as $key => $value) {
            $stringData[$key] = is_array($value) || is_object($value)
                ? json_encode($value)
                : (string) $value;
        }

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $stringData,
                'android' => [
                    'notification' => [
                        'sound' => 'default',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        return $this->sendFCMRequest($payload, $accessToken);
    }

    private function sendFCMRequest($payload, $accessToken)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                $this->saveNotification(
                    $payload['message']['token'],
                    $payload['message']['notification']['title'],
                    $payload['message']['notification']['body'],
                    $payload['message']['data']
                );

                return $responseData;
            }

            Log::error('FCM: Notification failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('FCM: Notification exception', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

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
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to save notification: '.$e->getMessage());
        }
    }

    public function sendBookingConfirmation($userId, $bookingDetails)
    {
        return $this->sendToUser(
            $userId,
            'Booking Confirmed!',
            "Your booking for {$bookingDetails['court_name']} on {$bookingDetails['date']} has been confirmed.",
            [
                'type' => 'booking_confirmed',
                'booking_id' => $bookingDetails['booking_id'],
                'court_name' => $bookingDetails['court_name'],
                'date' => $bookingDetails['date'],
                'time' => $bookingDetails['time'],
            ]
        );
    }

    public function sendBookingReminder($userId, $bookingDetails)
    {
        return $this->sendToUser(
            $userId,
            'Booking Reminder',
            "Your booking at {$bookingDetails['court_name']} is tomorrow at {$bookingDetails['time']}.",
            [
                'type' => 'booking_reminder',
                'booking_id' => $bookingDetails['booking_id'],
                'court_name' => $bookingDetails['court_name'],
                'date' => $bookingDetails['date'],
                'time' => $bookingDetails['time'],
            ]
        );
    }

    public function sendPaymentReceived($userId, $paymentDetails)
    {
        return $this->sendToUser(
            $userId,
            'Payment Received',
            "Your payment of RM {$paymentDetails['amount']} has been received successfully.",
            [
                'type' => 'payment_received',
                'payment_id' => $paymentDetails['payment_id'],
                'amount' => $paymentDetails['amount'],
            ]
        );
    }

    public function sendBookingCancelled($userId, $bookingDetails)
    {
        return $this->sendToUser(
            $userId,
            'Booking Cancelled',
            "Your booking for {$bookingDetails['court_name']} on {$bookingDetails['date']} has been cancelled.",
            [
                'type' => 'booking_cancelled',
                'booking_id' => $bookingDetails['booking_id'],
                'court_name' => $bookingDetails['court_name'],
                'date' => $bookingDetails['date'],
            ]
        );
    }

    public function sendOrderConfirmation($userId, $orderDetails)
    {
        return $this->sendToUser(
            $userId,
            'Order Confirmed!',
            "Your order #{$orderDetails['order_number']} has been confirmed. Total: RM ".number_format($orderDetails['total_amount'], 2),
            [
                'type' => 'order_confirmed',
                'order_id' => $orderDetails['order_id'],
                'order_number' => $orderDetails['order_number'],
                'total_amount' => $orderDetails['total_amount'],
                'status' => 'confirmed',
            ]
        );
    }

    public function sendOrderStatusUpdate($userId, $orderDetails)
    {
        $statusMessages = [
            'processing' => 'Your order is being processed',
            'shipped' => 'Your order has been shipped',
            'delivered' => 'Your order has been delivered',
            'cancelled' => 'Your order has been cancelled',
        ];

        $status = $orderDetails['status'] ?? 'processing';
        $message = $statusMessages[$status] ?? 'Your order status has been updated';

        return $this->sendToUser(
            $userId,
            'Order Update',
            "{$message} for order #{$orderDetails['order_number']}",
            [
                'type' => 'order_status_update',
                'order_id' => $orderDetails['order_id'],
                'order_number' => $orderDetails['order_number'],
                'status' => $status,
            ]
        );
    }

    public function sendShippingUpdate($userId, $shippingDetails)
    {
        $status = $shippingDetails['status'] ?? 'updated';
        $orderNumber = $shippingDetails['order_number'] ?? '';
        $trackingNumber = $shippingDetails['tracking_number'] ?? null;

        if ($status === 'Out for Delivery' || $status === 'out_for_delivery') {
            $title = 'Your Order is Out for Delivery!';
            $body = "Order #{$orderNumber} is on its way to you";
        } elseif ($status === 'Delivered' || $status === 'delivered') {
            $title = 'Order Delivered!';
            $body = "Your order #{$orderNumber} has been delivered successfully!";
        } else {
            $title = 'Shipping Update';
            $body = "Your order #{$orderNumber} is {$status}";
        }

        if ($trackingNumber && str_contains($body, 'Track') === false) {
            $body .= ". Track it: {$trackingNumber}";
        }

        return $this->sendToUser($userId, $title, $body, [
            'type' => 'shipping_update',
            'order_id' => $shippingDetails['order_id'],
            'order_number' => $orderNumber,
            'status' => strtolower(str_replace(' ', '_', $status)),
            'tracking_number' => $trackingNumber,
            'estimated_delivery' => $shippingDetails['estimated_delivery'] ?? null,
        ]);
    }

    public function sendProductStockUpdate($userId, $productDetails)
    {
        return $this->sendToUser(
            $userId,
            'Product Update',
            "{$productDetails['product_name']} is now {$productDetails['status']}",
            [
                'type' => 'product_stock_update',
                'product_id' => $productDetails['product_id'],
                'product_name' => $productDetails['product_name'],
                'status' => $productDetails['status'],
                'quantity' => $productDetails['quantity'] ?? null,
            ]
        );
    }

    public function sendGeneralNotification($userId, $title, $body, $data = [])
    {
        return $this->sendToUser($userId, $title, $body, array_merge([
            'type' => 'general',
            'timestamp' => now()->toIso8601String(),
        ], $data));
    }
}
