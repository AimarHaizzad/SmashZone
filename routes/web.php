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

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Customers)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'mobile.auth'])->group(function () {
    // Tutorial
    Route::post('/tutorial/complete', [App\Http\Controllers\TutorialController::class, 'complete'])->name('tutorial.complete');
    Route::post('/tutorial/restart', [App\Http\Controllers\TutorialController::class, 'restart'])->name('tutorial.restart');
    
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

    // Seed Past Data (Owner only) - GET route for easy access
    Route::get('/owner/seed-past-data', [OwnerDashboardController::class, 'seedPastData'])
        ->middleware('auth')
        ->name('owner.seed-past-data');

    // Owner Resources
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
