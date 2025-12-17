<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\WebNotificationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('welcome'))->name('welcome');

// Mobile app authentication check route
Route::get('/mobile-auth-check', function() {
    $user = null;
    $token = request()->header('Authorization');
    
    if ($token) {
        $token = str_replace('Bearer ', '', $token);
        $user = \App\Models\User::whereHas('tokens', function($query) use ($token) {
            $query->where('token', hash('sha256', $token));
        })->first();
    }
    
    return response()->json([
        'authenticated' => $user !== null,
        'user' => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ] : null
    ]);
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['mobile.auth', 'auth', 'verified'])
    ->name('dashboard');

// Mobile app integration test page
Route::get('/mobile-test', fn () => view('mobile-test'))
    ->name('mobile-test');

// Mobile app authentication handler
Route::get('/mobile-auth', function(Request $request) {
    $isAuthenticated = $request->get('authenticated') === 'true';
    $userId = $request->get('user_id');
    $authToken = $request->get('auth_token');
    $targetPage = $request->get('target', 'dashboard');
    
    \Log::info('Mobile auth attempt', [
        'authenticated' => $isAuthenticated,
        'user_id' => $userId,
        'has_token' => !empty($authToken),
        'target_page' => $targetPage
    ]);
    
    if ($isAuthenticated && $userId && $authToken) {
        // Find user by ID
        $user = \App\Models\User::find($userId);
        
        if ($user) {
            \Log::info('User found, attempting login', ['user_id' => $user->id, 'user_name' => $user->name]);
            
            // Log the user in
            Auth::login($user, true); // Remember the user
            
            // Store mobile app authentication in session
            session(['mobile_app_auth' => true]);
            session(['mobile_app_token' => $authToken]);
            
            \Log::info('User logged in successfully', ['user_id' => $user->id, 'session_id' => session()->getId()]);
            
            // Redirect to target page based on the target parameter
            $redirectUrl = match($targetPage) {
                'profile' => '/profile',
                'dashboard' => '/dashboard',
                'bookings' => '/bookings',
                'courts' => '/courts',
                'payments' => '/payments',
                default => '/dashboard'
            };
            
            \Log::info('Redirecting to target page', ['target' => $targetPage, 'redirect_url' => $redirectUrl]);
            return redirect($redirectUrl);
        } else {
            \Log::error('User not found', ['user_id' => $userId]);
        }
    } else {
        \Log::error('Invalid authentication parameters', [
            'authenticated' => $isAuthenticated,
            'user_id' => $userId,
            'has_token' => !empty($authToken)
        ]);
    }
    
    // If authentication fails, redirect to login
    return redirect('/login');
})->name('mobile-auth');

Route::get('/test-owner', function() {
    return 'Owner middleware works!';
})->middleware('owner');

Route::get('/test-middleware', function() {
    return 'It works!';
})->middleware('owner');

use App\Services\FCMService;

Route::get('/test-notification', function () {
    try {
        $userId = request('user_id', 1);
        
        // Check if FCM token exists
        $fcmToken = DB::table('fcm_tokens')->where('user_id', $userId)->value('token');
        
        if (!$fcmToken) {
            $allTokens = DB::table('fcm_tokens')->get(['user_id', 'token', 'created_at']);
            return response()->json([
                'success' => false,
                'error' => 'No FCM token found',
                'message' => "No FCM token found for user ID {$userId}",
                'help' => [
                    'step_1' => 'Make sure you have logged in to the Android app',
                    'step_2' => 'The app should automatically register the FCM token',
                    'step_3' => 'Check if any tokens exist in the database',
                    'available_tokens' => $allTokens->map(function($token) {
                        return [
                            'user_id' => $token->user_id,
                            'token_preview' => substr($token->token, 0, 30) . '...',
                            'created_at' => $token->created_at
                        ];
                    })
                ]
            ], 404);
        }
        
        // Initialize FCM Service
        $fcm = new FCMService();
        
        // Test access token generation first
        $reflection = new \ReflectionClass($fcm);
        $getAccessTokenMethod = $reflection->getMethod('getAccessToken');
        $getAccessTokenMethod->setAccessible(true);
        
        $accessToken = $getAccessTokenMethod->invoke($fcm);
        
        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate access token',
                'message' => 'Could not generate OAuth 2.0 access token for Firebase',
                'help' => [
                    'step_1' => 'Check Firebase service account credentials',
                    'step_2' => 'Verify private key is correctly formatted',
                    'step_3' => 'Check Laravel logs for detailed error: storage/logs/laravel.log'
                ],
                'token_found' => true,
                'token_preview' => substr($fcmToken, 0, 30) . '...'
            ], 500);
        }
        
        // Send notification
        $result = $fcm->sendToUser(
            $userId,
            "Test Notification 🔔",
            "If you see this, Firebase is working!",
            ['type' => 'test', 'timestamp' => now()->toIso8601String()]
        );
        
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully! Check your phone.',
                'user_id' => $userId,
                'token_preview' => substr($fcmToken, 0, 30) . '...',
                'result' => $result,
                'access_token_preview' => substr($accessToken, 0, 20) . '...'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Notification sending failed',
                'message' => 'FCM service returned false. Check Laravel logs for details.',
                'user_id' => $userId,
                'token_found' => true,
                'access_token_generated' => true,
                'help' => [
                    'step_1' => 'Check storage/logs/laravel.log for detailed error',
                    'step_2' => 'Verify FCM token is still valid (not expired)',
                    'step_3' => 'Check Firebase project settings and permissions'
                ]
            ], 500);
        }
        
    } catch (\Exception $e) {
        \Log::error('FCM Test Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => 'Exception occurred',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

Route::get('/test-notification-simple', function () {
    // Get FCM token from database
    $fcmTokenRecord = DB::table('fcm_tokens')->where('user_id', 1)->first();
    
    if (!$fcmTokenRecord) {
        return response()->json([
            'error' => 'No FCM token found for user ID 1',
            'message' => 'Please login to the Android app first to generate an FCM token'
        ]);
    }
    
    $fcmToken = $fcmTokenRecord->token;
    
    // Firebase Server Key
    $serverKey = 'AIzaSyA-SNazjDMqucspdRCRaDcmPJ_yG6yV7Ko';
    
    // Notification data
    $data = [
        'to' => $fcmToken,
        'notification' => [
            'title' => 'SmashZone Test 🔔',
            'body' => 'This is a test notification from Laravel!',
            'sound' => 'default'
        ],
        'data' => [
            'type' => 'test',
            'message' => 'Simple test notification'
        ]
    ];
    
    // Send notification
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Parse response
    $responseData = json_decode($response, true);
    
    return response()->json([
        'message' => $httpCode === 200 ? 'Notification sent! Check your phone.' : 'Failed to send notification',
        'http_code' => $httpCode,
        'response' => $responseData ?: $response,
        'error' => $error,
        'token_preview' => substr($fcmToken, 0, 30) . '...',
        'server_key_preview' => substr($serverKey, 0, 20) . '...'
    ]);
});

Route::get('/test-notification-v1', function () {
    try {
        $fcm = new FCMService();
        
        // Test notification using HTTP v1 API
        $result = $fcm->sendToUser(
            1,  // User ID
            "SmashZone Test v1 🔔",
            "This is a test notification using FCM HTTP v1 API!",
            ['type' => 'test_v1', 'message' => 'HTTP v1 API test']
        );
        
        return response()->json([
            'message' => 'FCM HTTP v1 notification sent! Check your phone.',
            'result' => $result,
            'api_version' => 'HTTP v1',
            'project_id' => 'smashzone-dff82'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'FCM HTTP v1 test failed',
            'message' => $e->getMessage(),
            'api_version' => 'HTTP v1'
        ]);
    }
});

Route::get('/test-firebase-key', function () {
    // Test Firebase Server Key validity
    $serverKey = 'AIzaSyA-SNazjDMqucspdRCRaDcmPJ_yG6yV7Ko';
    
    // Test with a dummy token to check if server key is valid
    $data = [
        'to' => 'dummy_token_for_testing',
        'notification' => [
            'title' => 'Test',
            'body' => 'Test'
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $responseData = json_decode($response, true);
    
    return response()->json([
        'message' => 'Firebase Server Key Test',
        'http_code' => $httpCode,
        'response' => $responseData ?: $response,
        'error' => $error,
        'server_key_preview' => substr($serverKey, 0, 20) . '...',
        'key_length' => strlen($serverKey),
        'expected_http_codes' => [
            '200' => 'Success (but notification not sent due to invalid token)',
            '400' => 'Bad Request (invalid server key or request format)',
            '401' => 'Unauthorized (invalid server key)',
            '404' => 'Not Found (invalid endpoint or server key)'
        ],
        'troubleshooting' => [
            'step_1' => 'Check Firebase Console > Project Settings > Cloud Messaging > Server Key',
            'step_2' => 'Ensure the server key is from the correct Firebase project',
            'step_3' => 'Verify the Android app is registered in the same Firebase project',
            'step_4' => 'Check if FCM is enabled in Firebase Console'
        ]
    ]);
});

Route::get('/firebase-key-help', function () {
    return response()->json([
        'message' => 'Firebase Key Type Identification',
        'key_analysis' => [
            'provided_key' => 'AIzaSyA-SNazjDMqucspdRCRaDcmPJ_yG6yV7Ko',
            'key_length' => 39,
            'key_type' => 'Web API Key (NOT Server Key)',
            'explanation' => 'This appears to be a Web API Key, not a Server Key for FCM'
        ],
        'firebase_key_types' => [
            'web_api_key' => [
                'format' => 'AIzaSy...',
                'length' => '~39 characters',
                'purpose' => 'Used for web applications, not FCM',
                'location' => 'Firebase Console > Project Settings > General > Web API Key'
            ],
            'server_key' => [
                'format' => 'AAAA...',
                'length' => '~150+ characters',
                'purpose' => 'Used for FCM push notifications',
                'location' => 'Firebase Console > Project Settings > Cloud Messaging > Server Key'
            ]
        ],
        'how_to_find_server_key' => [
            'step_1' => 'Open Firebase Console: https://console.firebase.google.com',
            'step_2' => 'Select your SmashZone project',
            'step_3' => 'Click the gear icon (⚙️) > Project Settings',
            'step_4' => 'Go to "Cloud Messaging" tab',
            'step_5' => 'Look for "Server Key" (NOT "Web API Key")',
            'step_6' => 'Copy the Server Key (starts with AAAA...)'
        ],
        'current_issue' => [
            'problem' => 'Using Web API Key instead of Server Key',
            'solution' => 'Get the correct Server Key from Cloud Messaging section',
            'note' => 'Web API Key is for web apps, Server Key is for FCM'
        ]
    ]);
});

Route::get('/get-server-key', function () {
    return response()->json([
        'message' => 'Firebase Service Account Analysis',
        'project_info' => [
            'project_id' => 'smashzone-dff82',
            'client_email' => 'firebase-adminsdk-fbsvc@smashzone-dff82.iam.gserviceaccount.com',
            'key_type' => 'Service Account (NOT Server Key)',
            'explanation' => 'This is a Service Account JSON file, not a Server Key for FCM'
        ],
        'key_types_explained' => [
            'service_account' => [
                'format' => 'JSON file with private key',
                'purpose' => 'Server-to-server authentication',
                'usage' => 'Not for FCM push notifications'
            ],
            'server_key' => [
                'format' => 'AAAA... (long string)',
                'purpose' => 'FCM push notifications',
                'location' => 'Firebase Console > Cloud Messaging'
            ]
        ],
        'how_to_get_server_key' => [
            'step_1' => 'Open Firebase Console: https://console.firebase.google.com',
            'step_2' => 'Select project: smashzone-dff82',
            'step_3' => 'Click gear icon (⚙️) > Project Settings',
            'step_4' => 'Go to "Cloud Messaging" tab',
            'step_5' => 'Look for "Server Key" (NOT "Web API Key")',
            'step_6' => 'Copy the Server Key (starts with AAAA...)',
            'step_7' => 'Update your .env file: FIREBASE_SERVER_KEY=YOUR_SERVER_KEY'
        ],
        'current_issue' => [
            'problem' => 'You have Service Account JSON, but need Server Key',
            'solution' => 'Get Server Key from Firebase Console Cloud Messaging section',
            'note' => 'Service Account is for server authentication, Server Key is for FCM'
        ],
        'firebase_console_url' => 'https://console.firebase.google.com/project/smashzone-dff82/settings/cloudmessaging'
    ]);
});

Route::get('/test-courts', function() {
    try {
        $courts = App\Models\Court::all();
        return response()->json([
            'success' => true,
            'count' => $courts->count(),
            'courts' => $courts->toArray()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

Route::get('/test-booking-create', function() {
    try {
        $courts = App\Models\Court::all();
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'courts_count' => $courts->count(),
            'user_role' => $user ? $user->role : 'not logged in',
            'user_id' => $user ? $user->id : null
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

Route::get('/test-complete-bookings', function() {
    try {
        // Run the command manually for testing
        \Artisan::call('bookings:complete-past');
        $output = \Artisan::output();
        
        return response()->json([
            'success' => true,
            'message' => 'Booking completion command executed',
            'output' => $output
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

Route::get('/test-bookings-status', function() {
    try {
        $bookings = App\Models\Booking::with(['user', 'court'])->get();
        $now = now();
        
        $bookingData = $bookings->map(function($booking) use ($now) {
            $bookingDateTime = \Carbon\Carbon::parse($booking->date . ' ' . $booking->end_time);
            $isPast = $bookingDateTime->isPast();
            
            return [
                'id' => $booking->id,
                'court' => $booking->court->name ?? 'N/A',
                'date' => $booking->date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'status' => $booking->status,
                'is_past' => $isPast,
                'should_be_completed' => $isPast && $booking->status === 'confirmed'
            ];
        });
        
        return response()->json([
            'success' => true,
            'current_time' => $now->format('Y-m-d H:i:s'),
            'total_bookings' => $bookings->count(),
            'bookings' => $bookingData,
            'past_bookings' => $bookingData->where('is_past', true)->count(),
            'confirmed_past_bookings' => $bookingData->where('should_be_completed', true)->count()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

Route::get('/create-owner-account', function() {
    try {
        // Create or update the main owner account
        $owner = App\Models\User::firstOrCreate(
            ['email' => 'AimarHaizzad@gmail.com'],
            [
                'name' => 'Owner',
                'password' => bcrypt('Aimar123'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]
        );
        
        // If owner already exists, update password to ensure it's correct
        if ($owner->wasRecentlyCreated === false) {
            $owner->password = bcrypt('Aimar123');
            $owner->role = 'owner';
            $owner->email_verified_at = now();
            $owner->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Owner account created/updated successfully!',
            'account' => [
                'email' => $owner->email,
                'password' => 'Aimar123',
                'role' => $owner->role,
                'id' => $owner->id,
            ],
            'login_url' => route('login', absolute: false)
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::get('/create-test-data', function() {
    try {
        // Create test owner
        $owner = App\Models\User::create([
            'name' => 'Test Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'role' => 'owner'
        ]);
        
        // Create test customer
        $customer = App\Models\User::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);
        
        // Create test court
        $court = App\Models\Court::create([
            'name' => 'Test Court',
            'owner_id' => $owner->id,
            'description' => 'Test court description'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Test data created successfully',
            'owner_id' => $owner->id,
            'customer_id' => $customer->id,
            'court_id' => $court->id
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Customers)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'mobile.auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Staff Management Routes
    Route::get('staff/bookings', [StaffController::class, 'bookings'])->name('staff.bookings');

    // Bookings (for customers)
    Route::resource('bookings', BookingController::class)->except(['create']);
    // Customer bookings list page
    Route::get('my/bookings', [BookingController::class, 'my'])->name('bookings.my');
    // Mark booking as completed
    Route::patch('bookings/{booking}/mark-completed', [BookingController::class, 'markCompleted'])->name('bookings.mark-completed');
    // Cancel booking (for late customers)
    Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Courts (view and show only for customers)
    //Route::get('courts', [CourtController::class, 'index'])->name('courts.index');
    //Route::get('courts/{court}', [CourtController::class, 'show'])->name('courts.show');

    // Products (view and show for all users)
    //Route::get('products', [ProductController::class, 'index'])->name('products.index');
    //Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
});

/*
|--------------------------------------------------------------------------
| Owner-Specific Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Owner Dashboard
    Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');
    
    // Owner Bookings View
    Route::get('/owner/bookings', [OwnerDashboardController::class, 'bookings'])->name('owner.bookings');

    // Owner Resources
  //  Route::resource('products', ProductController::class)->except(['index', 'show']);
    Route::resource('courts', CourtController::class)->except(['index', 'show']);

    // Payments
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}/pay', [PaymentController::class, 'showPaymentForm'])->name('payments.pay');
    Route::post('payments/{payment}/process', [PaymentController::class, 'processPayment'])->name('payments.process');
    Route::get('payments/{payment}/success', [PaymentController::class, 'paymentSuccess'])->name('payments.success');
    Route::get('payments/{payment}/cancel', [PaymentController::class, 'paymentCancel'])->name('payments.cancel');
    Route::patch('payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payments.mark-paid');

    // Refunds (Staff and Owners only)
    Route::middleware(['auth'])->group(function () {
        Route::get('refunds', function() {
            $user = auth()->user();
            if (!$user->isOwner() && !$user->isStaff()) {
                abort(403, 'Unauthorized access to refunds.');
            }
            return app(App\Http\Controllers\RefundController::class)->index();
        })->name('refunds.index');
        
        Route::get('refunds/{refund}', function($refund) {
            $user = auth()->user();
            if (!$user->isOwner() && !$user->isStaff()) {
                abort(403, 'Unauthorized access to refunds.');
            }
            return app(App\Http\Controllers\RefundController::class)->show($refund);
        })->name('refunds.show');
        
        Route::post('refunds/{refund}/retry', function($refund) {
            $user = auth()->user();
            if (!$user->isOwner() && !$user->isStaff()) {
                abort(403, 'Unauthorized access to refunds.');
            }
            return app(App\Http\Controllers\RefundController::class)->retry($refund);
        })->name('refunds.retry');
        
        Route::post('refunds/{refund}/manual', function($refund) {
            $user = auth()->user();
            if (!$user->isOwner() && !$user->isStaff()) {
                abort(403, 'Unauthorized access to refunds.');
            }
            return app(App\Http\Controllers\RefundController::class)->manualRefund(request(), $refund);
        })->name('refunds.manual');
    });

    // Analytics & Reports (Owner only)
    Route::middleware(['auth'])->group(function () {
        Route::get('analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/export-pdf', [App\Http\Controllers\AnalyticsController::class, 'exportPDF'])->name('analytics.export-pdf');
        Route::get('analytics/export-excel', [App\Http\Controllers\AnalyticsController::class, 'exportExcel'])->name('analytics.export-excel');
    });
});

// Stripe webhook (outside auth middleware)
Route::post('stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

Route::resource('products', App\Http\Controllers\ProductController::class);
Route::resource('courts', App\Http\Controllers\CourtController::class);
Route::get('courts-availability', [App\Http\Controllers\CourtController::class, 'availability'])->name('courts.availability');
Route::get('booking-availability', [App\Http\Controllers\BookingController::class, 'availability'])->name('bookings.availability');
Route::get('booking-grid-availability', [App\Http\Controllers\BookingController::class, 'gridAvailability'])->name('bookings.gridAvailability');
Route::get('booking-details/{id}', [App\Http\Controllers\BookingController::class, 'showDetails'])->name('bookings.details');
Route::get('user-bookings', [App\Http\Controllers\BookingController::class, 'userBookings'])->name('bookings.userBookings');
Route::post('bookings/multi', [App\Http\Controllers\BookingController::class, 'storeMulti'])->name('bookings.store-multi');
Route::get('cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('cart/update', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::post('cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::get('cart/checkout', [App\Http\Controllers\CartController::class, 'checkout'])->name('cart.checkout');
Route::post('stripe/checkout', [App\Http\Controllers\StripeController::class, 'checkout'])->name('stripe.checkout');
Route::get('cart/success', [App\Http\Controllers\StripeController::class, 'success'])->name('stripe.success');
Route::get('cart/cancel', [App\Http\Controllers\StripeController::class, 'cancel'])->name('stripe.cancel');

// Order routes
Route::middleware(['auth'])->group(function () {
    Route::get('orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/track', [App\Http\Controllers\OrderController::class, 'track'])->name('orders.track');
    Route::post('orders/{order}/update-shipping', [App\Http\Controllers\OrderController::class, 'updateShippingStatus'])->name('orders.update-shipping');
    Route::post('orders/{order}/update-status', [App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.update-status');
            Route::post('orders/{order}/mark-received', [App\Http\Controllers\OrderController::class, 'markAsReceived'])->name('orders.mark-received');
            Route::post('orders/{order}/request-return', [App\Http\Controllers\OrderController::class, 'requestReturn'])->name('orders.request-return');
            Route::post('orders/{order}/approve-return', [App\Http\Controllers\OrderController::class, 'approveReturn'])->name('orders.approve-return');
            Route::post('orders/{order}/reject-return', [App\Http\Controllers\OrderController::class, 'rejectReturn'])->name('orders.reject-return');
});

// Test route for Stripe configuration
Route::get('test-stripe-config', function() {
    $stripeKey = config('services.stripe.key');
    $stripeSecret = config('services.stripe.secret');
    
    return response()->json([
        'stripe_key_exists' => !empty($stripeKey),
        'stripe_secret_exists' => !empty($stripeSecret),
        'stripe_key_preview' => $stripeKey ? substr($stripeKey, 0, 10) . '...' : 'Not set',
        'stripe_secret_preview' => $stripeSecret ? substr($stripeSecret, 0, 10) . '...' : 'Not set',
    ]);
});

// Test route for payment data
Route::get('test-payment/{id}', function($id) {
    $payment = \App\Models\Payment::with(['user', 'bookings.court'])->find($id);
    
    if (!$payment) {
        return response()->json(['error' => 'Payment not found'], 404);
    }
    
    return response()->json($payment);
});



// Test route to show all payments for current user
Route::get('my-payments', function() {
    $payments = \App\Models\Payment::with(['bookings.court'])
        ->where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();
    
    return response()->json([
        'user_id' => auth()->id(),
        'total_payments' => $payments->count(),
        'payments' => $payments->map(function($payment) {
            $primaryBooking = $payment->bookings->first();
            return [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'court_name' => $primaryBooking->court->name ?? 'Unknown',
                'booking_date' => $primaryBooking->date ?? 'Unknown',
                'booking_count' => $payment->bookings->count(),
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'payment_url' => route('payments.pay', $payment->id)
            ];
        })
    ]);
});

// Test route to create a sample payment
Route::get('create-test-payment', function() {
    // Find or create a court
    $court = \App\Models\Court::first();
    if (!$court) {
        return response()->json(['error' => 'No courts found. Please create a court first.'], 404);
    }
    
    // Create a test booking
    $booking = \App\Models\Booking::create([
        'user_id' => auth()->id(),
        'court_id' => $court->id,
        'date' => now()->addDays(1)->format('Y-m-d'),
        'start_time' => '10:00:00',
        'end_time' => '11:00:00',
        'total_price' => 25.00,
        'status' => 'pending'
    ]);
    
    // Create a test payment
    $payment = \App\Models\Payment::create([
        'user_id' => auth()->id(),
        'booking_id' => $booking->id,
        'amount' => 25.00,
        'payment_method' => 'stripe',
        'status' => 'pending',
        'payment_date' => now()
    ]);
    
    return response()->json([
        'message' => 'Test payment created successfully',
        'payment_id' => $payment->id,
        'payment_url' => route('payments.pay', $payment->id),
        'booking' => [
            'id' => $booking->id,
            'court_name' => $court->name,
            'date' => $booking->date,
            'time' => $booking->start_time . ' - ' . $booking->end_time,
            'amount' => $payment->amount
        ]
    ]);
});

// Test route for owner middleware
Route::get('/test-owner', function () {
    return response()->json([
        'user_id' => auth()->id(),
        'user_role' => auth()->user()->role,
        'is_owner' => auth()->user()->role === 'owner'
    ]);
})->middleware(['auth', 'owner']);

// Test email notifications
Route::get('/test-email-notifications', function() {
    if (!auth()->check()) {
        return response()->json(['error' => 'Please login first'], 401);
    }

    $user = auth()->user();
    $testBooking = null;
    $testPayment = null;

    try {
        // Create a test booking if none exists
        $testBooking = \App\Models\Booking::first();
        if (!$testBooking) {
            $court = \App\Models\Court::first();
            if (!$court) {
                return response()->json(['error' => 'No courts found. Please create a court first.'], 404);
            }
            
            $testBooking = \App\Models\Booking::create([
                'user_id' => $user->id,
                'court_id' => $court->id,
                'date' => now()->addDays(1)->format('Y-m-d'),
                'start_time' => '10:00:00',
                'end_time' => '11:00:00',
                'total_price' => 25.00,
                'status' => 'confirmed'
            ]);
        }

        // Create a test payment if none exists
        $testPayment = \App\Models\Payment::first();
        if (!$testPayment) {
            $testPayment = \App\Models\Payment::create([
                'user_id' => $user->id,
                'booking_id' => $testBooking->id,
                'amount' => 25.00,
                'status' => 'paid',
                'payment_date' => now()
            ]);
        }

        return response()->json([
            'message' => 'Test data ready for email notifications',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'test_booking' => [
                'id' => $testBooking->id,
                'court_name' => $testBooking->court->name,
                'date' => $testBooking->date,
                'time' => $testBooking->start_time . ' - ' . $testBooking->end_time
            ],
            'test_payment' => [
                'id' => $testPayment->id,
                'amount' => $testPayment->amount,
                'status' => $testPayment->status
            ],
            'available_tests' => [
                'welcome_email' => 'Send welcome email to current user',
                'booking_confirmation' => 'Send booking confirmation for test booking',
                'payment_confirmation' => 'Send payment confirmation for test payment',
                'booking_reminder' => 'Send booking reminder for test booking',
                'booking_cancellation' => 'Send booking cancellation for test booking'
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to setup test data: ' . $e->getMessage()], 500);
    }
})->middleware('auth');

// Test individual email notifications
Route::get('/test-email/{type}', function($type) {
    if (!auth()->check()) {
        return response()->json(['error' => 'Please login first'], 401);
    }

    $user = auth()->user();

    try {
        switch ($type) {
            case 'welcome':
                $user->notify(new \App\Notifications\WelcomeEmail($user));
                return response()->json(['message' => 'Welcome email sent successfully']);
                
            case 'booking-confirmation':
                $booking = \App\Models\Booking::first();
                if (!$booking) {
                    return response()->json(['error' => 'No bookings found'], 404);
                }
                $user->notify(new \App\Notifications\BookingConfirmation($booking));
                return response()->json(['message' => 'Booking confirmation email sent successfully']);
                
            case 'payment-confirmation':
                $payment = \App\Models\Payment::first();
                if (!$payment) {
                    return response()->json(['error' => 'No payments found'], 404);
                }
                $user->notify(new \App\Notifications\PaymentConfirmation($payment));
                return response()->json(['message' => 'Payment confirmation email sent successfully']);
                
            case 'booking-reminder':
                $booking = \App\Models\Booking::first();
                if (!$booking) {
                    return response()->json(['error' => 'No bookings found'], 404);
                }
                $user->notify(new \App\Notifications\BookingReminder($booking));
                return response()->json(['message' => 'Booking reminder email sent successfully']);
                
            case 'booking-cancellation':
                $booking = \App\Models\Booking::first();
                if (!$booking) {
                    return response()->json(['error' => 'No bookings found'], 404);
                }
                $user->notify(new \App\Notifications\BookingCancellation($booking, 'Test cancellation'));
                return response()->json(['message' => 'Booking cancellation email sent successfully']);
                
            default:
                return response()->json(['error' => 'Invalid email type'], 400);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to send email: ' . $e->getMessage()], 500);
    }
})->middleware('auth');

// Staff Management Routes (Owner only)
Route::middleware(['auth'])->group(function () {
    Route::get('/staff', [App\Http\Controllers\StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [App\Http\Controllers\StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [App\Http\Controllers\StaffController::class, 'store'])->name('staff.store');
    Route::post('/staff/activate-all', [App\Http\Controllers\StaffController::class, 'activateAll'])->name('staff.activate-all');
    Route::get('/staff/{staff}/edit', [App\Http\Controllers\StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staff}', [App\Http\Controllers\StaffController::class, 'update'])->name('staff.update');
    Route::patch('/staff/{staff}/activate', [App\Http\Controllers\StaffController::class, 'activate'])->name('staff.activate');
    Route::delete('/staff/{staff}', [App\Http\Controllers\StaffController::class, 'destroy'])->name('staff.destroy');
});

Route::get('/android-notification-debug', function () {
    try {
        // Get FCM tokens from database
        $tokens = DB::table('fcm_tokens')->get();
        
        // Test sending to each token
        $results = [];
        $fcm = new FCMService();
        
        foreach ($tokens as $tokenRecord) {
            $result = $fcm->sendToUser(
                $tokenRecord->user_id,
                "Android Debug Test 🔔",
                "Testing notification delivery to your Android device",
                ['type' => 'debug', 'timestamp' => now()->toISOString()]
            );
            
            $results[] = [
                'user_id' => $tokenRecord->user_id,
                'token_preview' => substr($tokenRecord->token, 0, 30) . '...',
                'device_type' => $tokenRecord->device_type ?? 'Not set',
                'created_at' => $tokenRecord->created_at,
                'notification_sent' => $result ? 'Success' : 'Failed',
                'result' => $result
            ];
        }
        
        // Check recent notifications
        $recentNotifications = DB::table('notifications')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json([
            'message' => 'Android Notification Debug Test',
            'server_status' => 'Running on 10.62.86.15:8000',
            'fcm_tokens_found' => $tokens->count(),
            'test_results' => $results,
            'recent_notifications' => $recentNotifications,
            'troubleshooting_steps' => [
                'step_1' => 'Check if Android app is running in foreground',
                'step_2' => 'Check if notifications are enabled in Android settings',
                'step_3' => 'Verify FCM token is valid and not expired',
                'step_4' => 'Check Android app logs for FCM errors',
                'step_5' => 'Ensure Firebase project matches Android app package name',
                'step_6' => 'Test with a simple notification from Firebase Console'
            ],
            'firebase_console_test' => [
                'url' => 'https://console.firebase.google.com/project/smashzone-dff82/messaging',
                'instructions' => 'Send a test message from Firebase Console to verify FCM setup'
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Debug test failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test all booking notification types
Route::get('/test-booking-notifications', function () {
    $results = [];
    
    // Test booking confirmed
    $response1 = Http::get('http://10.62.86.15:8000/test-booking-confirmed');
    $results['booking_confirmed'] = $response1->json();
    
    // Test booking reminder
    $response2 = Http::get('http://10.62.86.15:8000/test-booking-reminder');
    $results['booking_reminder'] = $response2->json();
    
    // Test booking starting soon
    $response3 = Http::get('http://10.62.86.15:8000/test-booking-starting-soon');
    $results['booking_starting_soon'] = $response3->json();
    
    // Test booking cancelled
    $response4 = Http::get('http://10.62.86.15:8000/test-booking-cancelled');
    $results['booking_cancelled'] = $response4->json();
    
    // Test payment reminder
    $response5 = Http::get('http://10.62.86.15:8000/test-payment-reminder');
    $results['payment_reminder'] = $response5->json();
    
    return response()->json([
        'message' => 'All booking notification tests completed!',
        'results' => $results,
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Quick test for specific notification type
Route::get('/test-notification/{type}', function ($type) {
    $validTypes = ['booking_confirmed', 'booking_reminder', 'booking_starting_soon', 'booking_cancelled', 'payment_reminder'];
    
    if (!in_array($type, $validTypes)) {
        return response()->json(['error' => 'Invalid notification type'], 400);
    }
    
    $response = Http::get("http://10.62.86.15:8000/test-{$type}");
    return $response->json();
});

/*
|--------------------------------------------------------------------------
| Web Notification Routes
|--------------------------------------------------------------------------
*/

// Web notification routes (authenticated users only)
Route::middleware(['auth'])->group(function () {
    // Get notifications
    Route::get('/notifications', [WebNotificationController::class, 'index'])->name('notifications.index');
    
    // Get unread count
    Route::get('/notifications/unread-count', [WebNotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    
    // Mark notification as read
    Route::patch('/notifications/{id}/read', [WebNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    
    // Mark all notifications as read
    Route::patch('/notifications/mark-all-read', [WebNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Delete notification
    Route::delete('/notifications/{id}', [WebNotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Get notification statistics
    Route::get('/notifications/stats', [WebNotificationController::class, 'stats'])->name('notifications.stats');
    
    // Get notifications by type
    Route::get('/notifications/type/{type}', [WebNotificationController::class, 'byType'])->name('notifications.by-type');
});

// Test routes for web notifications (remove in production)
Route::get('/test-web-notifications', function () {
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }

    $notificationService = new \App\Services\WebNotificationService();

    // Create sample notifications
    $notifications = [
        [
            'user_id' => $user->id,
            'type' => 'booking_created',
            'title' => 'New Booking Received',
            'message' => 'John Doe has booked Court 1 for 2024-10-29 at 14:00',
            'data' => [
                'booking_id' => 1,
                'court_id' => 1,
                'customer_id' => 2,
                'booking_date' => '2024-10-29',
                'booking_time' => '14:00',
            ],
        ],
        [
            'user_id' => $user->id,
            'type' => 'payment_received',
            'title' => 'Payment Received',
            'message' => 'Payment of RM 40.00 received for booking at Court 2',
            'data' => [
                'booking_id' => 2,
                'court_id' => 2,
                'customer_id' => 3,
                'amount' => 40.00,
                'booking_date' => '2024-10-29',
            ],
        ],
        [
            'user_id' => $user->id,
            'type' => 'court_added',
            'title' => 'New Court Added',
            'message' => 'A new court \'Court 3\' has been added to the system',
            'data' => [
                'court_id' => 3,
                'court_name' => 'Court 3',
                'owner_id' => 1,
            ],
        ],
    ];

    foreach ($notifications as $notificationData) {
        $notificationService->create($notificationData);
    }

    return response()->json([
        'success' => true,
        'message' => 'Sample web notifications created successfully!',
        'notifications_created' => count($notifications),
    ]);
});
