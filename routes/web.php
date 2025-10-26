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

Route::get('/test-hello', function() {
    return 'Hello World';
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
    Route::resource('bookings', BookingController::class);
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

    // Refunds
    Route::get('refunds', [App\Http\Controllers\RefundController::class, 'index'])->name('refunds.index');
    Route::get('refunds/{refund}', [App\Http\Controllers\RefundController::class, 'show'])->name('refunds.show');
    Route::post('refunds/{refund}/retry', [App\Http\Controllers\RefundController::class, 'retry'])->name('refunds.retry');
    Route::post('refunds/{refund}/manual', [App\Http\Controllers\RefundController::class, 'manualRefund'])->name('refunds.manual');

    // Analytics & Reports
    Route::get('analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export-pdf', [App\Http\Controllers\AnalyticsController::class, 'exportPDF'])->name('analytics.export-pdf');
    Route::get('analytics/export-excel', [App\Http\Controllers\AnalyticsController::class, 'exportExcel'])->name('analytics.export-excel');
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
Route::post('cart/checkout', [App\Http\Controllers\StripeController::class, 'checkout'])->name('cart.checkout');
Route::get('cart/success', [App\Http\Controllers\StripeController::class, 'success'])->name('stripe.success');
Route::get('cart/cancel', [App\Http\Controllers\StripeController::class, 'cancel'])->name('stripe.cancel');

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
    $payment = \App\Models\Payment::with(['user', 'booking.court'])->find($id);
    
    if (!$payment) {
        return response()->json(['error' => 'Payment not found'], 404);
    }
    
    return response()->json($payment);
});



// Test route to show all payments for current user
Route::get('my-payments', function() {
    $payments = \App\Models\Payment::with(['booking.court'])
        ->where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();
    
    return response()->json([
        'user_id' => auth()->id(),
        'total_payments' => $payments->count(),
        'payments' => $payments->map(function($payment) {
            return [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'court_name' => $payment->booking->court->name ?? 'Unknown',
                'booking_date' => $payment->booking->date ?? 'Unknown',
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
    Route::get('/staff/{staff}/edit', [App\Http\Controllers\StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staff}', [App\Http\Controllers\StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [App\Http\Controllers\StaffController::class, 'destroy'])->name('staff.destroy');
});
