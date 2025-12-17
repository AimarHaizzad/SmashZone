<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FCMService
{
    private $projectId;
    private $serviceAccount;
    private $fcmUrl = 'https://fcm.googleapis.com/v1/projects/%s/messages:send';
    private $accessToken;
    private $tokenExpiry;

    public function __construct()
    {
        $this->projectId = 'smashzone-dff82';
        $this->fcmUrl = sprintf($this->fcmUrl, $this->projectId);
        
        // Service Account details from your JSON file
        $this->serviceAccount = [
            'type' => 'service_account',
            'project_id' => 'smashzone-dff82',
            'private_key_id' => 'c9f21686a27012270d94b10c727bbb4ca68f2fc3',
            'private_key' => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDJQJwskJ4j58go\nM+kL8a20suAMwyIGGyo21T7UVY5wHB47KAoHGZm3GjN4nazymsuabXMo2gHmJM7V\nthsWYt+3CC5SCJLiNPFAB0iZakgFYG9Bbe+fQSfq2cbMrc5h8aLmxEvT9XiHxSHq\nqnBQBiOWTqlcty8LuOzND956TYj9m9ELSsXJwRkfc4DzBYwrUjkqQfNJCVA0dldv\nZCOqWczoCB4GdVyskUTeFNmmg+f8Nj8gyL0PF0MRF/aeN5gSqe3Uw6D98diLNyqK\n0as4bQAbP5Jpq7L/6uLcclbryddGtalEXcytkOVRhCV84QoWvyDEGOV/doibKLBe\nry1oBAGfAgMBAAECggEAV58LP/Xo/D3+bc6IERy0od9dYAEXT6xFGWGHvN/RJ6t+\nWAgrMYMqY0eDkGip82iE6+PqRyd/uylcajyil4EN4P0tWacE+HrPbK6fI7hbygd9\n+PE56k7uZ7hQby3fFNKlN67QSuTtiMNB7I8vXhAGL3tpDoZs88AcTX2ywfH/Clcl\nkpYa/bmDmJwFmCFbi6YiPvxydRXNX19dxXAfaYDPvV26SQ0Nqvgjt7azW6yaRywc\nSm6UgsJg9PqpZ2EutojA2vCqaS/y88bKcF66G3gUnA9sAAk2Sl4wXpFPg/x9wP/8\ncyrrefH9cCQiJhKVmeTh8gstpc7ERCVptpuEK+UXVQKBgQD8KpCshfGtC+w7zj8w\nYekjx8xpbvCvDTiE5bzkVgItS8t69PSagPA2p9l4cMFRJ2TkfRSHUduwQsx03xtp\neTpqsA0KYh2ncBGEoBXx5FW6bLvuWwdG53vmyhxL3YYI+hIfyTxvFw52VTBBvDXf\nuzJfg7HOAZDJVuftwRyVjz2nrQKBgQDMT+MnSPbtVoVFor/26BMqO/fIwhk3sP+g\nujgeP7fk9img7jjtajJLrNv57S6kj3PUyYNCexICV2cR0AaeAZBa3eStqKs/TC5M\nsb8pDvOhbVwkN4HmX3W4bhqKbWFpN6kL1kOAnY/Nf8LUR1ktt8EZNDDZsYJWc/tf\nattaU8Jn+wKBgDCmdasTXIEqX7VaIU7QVQ6WKZXd1YmwX0skl8Dl5x2eFe/u+pIk\ndjPVRlu6RVHG6+w5RZCl9mCXQqL5uGws+1xCAwIR0+7N7FNOH22/w9pnyApAfbLs\nTjdEnxjz8DwjIwQG6yXzqNKjtN+51BsKHrnWyqMYIjr2DWENdNpV/GsxAoGBAK/j\nxoa3hfzlE27TobeKK5WccsDeeJ89PZS9PDquWD5Ava3R6Chb2FjVw7rxucnpxapW\noS3GjcZ+QDlRgaDdb80KYiguoN6pUuKr0woh2RQL9dsn/ii53bqc7zRk3gua42lR\nWGONQZOEfdIKane1TgPIrpV6/941kx6d+6FTonWpAoGARIuiK0/1VcVgfBBnJZdy\nNevkvUEIJKWv1B0/prFjPq23L5/zYkfBCCMJNqzBnRnoLcO+gpJgHG8vmmwASjjY\nKMN3qC8/0cX42APSZYKWN8NQpAdoVoqw5E9addUp1AtJ9Pe9jcpCH7cjiPpJLBO6\nM5w6L4RZfPHoGHcgtucnyA4=\n-----END PRIVATE KEY-----\n",
            'client_email' => 'firebase-adminsdk-fbsvc@smashzone-dff82.iam.gserviceaccount.com',
            'client_id' => '104060120427533478583',
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40smashzone-dff82.iam.gserviceaccount.com',
            'universe_domain' => 'googleapis.com'
        ];
    }

    /**
     * Get OAuth 2.0 access token
     */
    private function getAccessToken()
    {
        if ($this->accessToken && $this->isTokenValid()) {
            return $this->accessToken;
        }

        try {
            // Check if private key is available
            if (empty($this->serviceAccount['private_key'])) {
                Log::error('FCM: Private key is missing');
                return false;
            }

            $jwt = [
                'iss' => $this->serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $this->serviceAccount['token_uri'],
                'iat' => time(),
                'exp' => time() + 3600
            ];
            
            $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
            $payload = json_encode($jwt);
            
            $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
            
            // Ensure private key has proper newlines
            $privateKey = $this->serviceAccount['private_key'];
            if (strpos($privateKey, '\\n') !== false) {
                $privateKey = str_replace('\\n', "\n", $privateKey);
            }
            
            $signature = '';
            $signResult = openssl_sign($base64Header . '.' . $base64Payload, $signature, $privateKey, 'SHA256');
            
            if (!$signResult) {
                $opensslError = openssl_error_string();
                Log::error('FCM: Failed to sign JWT', ['openssl_error' => $opensslError]);
                return false;
            }
            
            $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            
            $jwtToken = $base64Header . '.' . $base64Payload . '.' . $base64Signature;
            
            $response = Http::asForm()->post($this->serviceAccount['token_uri'], [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwtToken
            ]);
            
            if ($response->successful()) {
                $tokenData = $response->json();
                $this->accessToken = $tokenData['access_token'] ?? null;
                $this->tokenExpiry = time() + ($tokenData['expires_in'] ?? 3600);
                
                if ($this->accessToken) {
                    Log::info('FCM: Access token generated successfully');
                    return $this->accessToken;
                } else {
                    Log::error('FCM: Access token missing in response', ['response' => $tokenData]);
                    return false;
                }
            } else {
                Log::error('FCM: Failed to get access token', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'headers' => $response->headers()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('FCM: Access token error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Check if token is still valid
     */
    private function isTokenValid()
    {
        return isset($this->tokenExpiry) && $this->tokenExpiry > time() + 60; // Refresh 1 minute before expiry
    }

    /**
     * Send notification to a single user
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        $token = DB::table('fcm_tokens')->where('user_id', $userId)->value('token');
        
        if (!$token) {
            Log::warning("FCM: No FCM token found for user {$userId}");
            return false;
        }
        
        Log::info("FCM: Sending notification to user {$userId}", [
            'title' => $title,
            'body' => $body,
            'token_preview' => substr($token, 0, 30) . '...'
        ]);
        
        return $this->sendNotification($token, $title, $body, $data);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers($userIds, $title, $body, $data = [])
    {
        $tokens = DB::table('fcm_tokens')->whereIn('user_id', $userIds)->pluck('token')->toArray();
        
        if (empty($tokens)) {
            Log::warning("No FCM tokens found for users: " . implode(', ', $userIds));
            return false;
        }
        
        // Send to each token individually (HTTP v1 doesn't support multicast)
        $results = [];
        foreach ($tokens as $token) {
            $results[] = $this->sendNotification($token, $title, $body, $data);
        }
        
        return $results;
    }

    /**
     * Send notification to a single token using HTTP v1 API
     */
    private function sendNotification($token, $title, $body, $data = [])
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error('Failed to get access token for FCM');
            return false;
        }

        // Convert data to strings (HTTP v1 doesn't support nested JSON)
        $stringData = [];
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $stringData[$key] = json_encode($value);
            } else {
                $stringData[$key] = (string) $value;
            }
        }

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'data' => $stringData,
                'android' => [
                    'notification' => [
                        'sound' => 'default'
                    ]
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default'
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendFCMRequest($payload, $accessToken);
    }

    /**
     * Send FCM request using HTTP v1 API
     */
    private function sendFCMRequest($payload, $accessToken)
    {
        try {
            if (empty($accessToken)) {
                Log::error('FCM: Access token is empty');
                return false;
            }

            Log::info('FCM: Sending request', [
                'url' => $this->fcmUrl,
                'token_preview' => substr($payload['message']['token'] ?? '', 0, 30) . '...',
                'title' => $payload['message']['notification']['title'] ?? ''
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->timeout(30)->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('FCM: Notification sent successfully', [
                    'response' => $responseData,
                    'message_id' => $responseData['name'] ?? null
                ]);
                
                // Save notification to database
                $this->saveNotification($payload['message']['token'], $payload['message']['notification']['title'], $payload['message']['notification']['body'], $payload['message']['data']);
                
                return $responseData;
            } else {
                $errorBody = $response->body();
                $errorJson = json_decode($errorBody, true);
                
                Log::error('FCM: Notification failed', [
                    'status' => $response->status(),
                    'response' => $errorBody,
                    'error_details' => $errorJson['error'] ?? null
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('FCM: Notification exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
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