<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\SeederController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\WebNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('welcome'))->name('welcome');

Route::get('/mobile-auth-check', function () {
    $user = null;
    $token = request()->header('Authorization');

    if ($token) {
        $token = str_replace('Bearer ', '', $token);
        $user = \App\Models\User::whereHas('tokens', function ($query) use ($token) {
            $query->where('token', hash('sha256', $token));
        })->first();
    }

    return response()->json([
        'authenticated' => $user !== null,
        'user' => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ] : null,
    ]);
});

Route::get('/mobile-auth', function (Request $request) {
    $isAuthenticated = $request->get('authenticated') === 'true';
    $userId = $request->get('user_id');
    $authToken = $request->get('auth_token');
    $targetPage = $request->get('target', 'dashboard');

    if ($isAuthenticated && $userId && $authToken) {
        $user = \App\Models\User::find($userId);

        if ($user) {
            Auth::login($user, true);
            session(['mobile_app_auth' => true, 'mobile_app_token' => $authToken]);

            return redirect()->route($targetPage === 'dashboard' ? 'dashboard' : $targetPage);
        }
    }

    return redirect('/login');
})->name('mobile-auth');

Route::post('stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

Route::resource('products', ProductController::class);
Route::resource('courts', CourtController::class);
Route::get('courts-availability', [CourtController::class, 'availability'])->name('courts.availability');
Route::get('booking-availability', [BookingController::class, 'availability'])->name('bookings.availability');
Route::get('booking-grid-availability', [BookingController::class, 'gridAvailability'])->name('bookings.gridAvailability');
Route::get('booking-details/{id}', [BookingController::class, 'showDetails'])->name('bookings.details');
Route::get('user-bookings', [BookingController::class, 'userBookings'])->name('bookings.userBookings');
Route::post('bookings/multi', [BookingController::class, 'storeMulti'])->name('bookings.store-multi');

Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('stripe/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout');
Route::get('cart/success', [StripeController::class, 'success'])->name('stripe.success');
Route::get('cart/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['mobile.auth', 'auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'mobile.auth'])->group(function () {
    Route::post('/tutorial/complete', [TutorialController::class, 'complete'])->name('tutorial.complete');
    Route::post('/tutorial/restart', [TutorialController::class, 'restart'])->name('tutorial.restart');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('staff/bookings', [StaffController::class, 'bookings'])->name('staff.bookings');

    Route::resource('bookings', BookingController::class)->except(['create']);
    Route::get('my/bookings', [BookingController::class, 'my'])->name('bookings.my');
    Route::patch('bookings/{booking}/mark-completed', [BookingController::class, 'markCompleted'])->name('bookings.mark-completed');
    Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');
    Route::get('/owner/bookings', [OwnerDashboardController::class, 'bookings'])->name('owner.bookings');

    Route::resource('courts', CourtController::class)->except(['index', 'show']);

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}/pay', [PaymentController::class, 'showPaymentForm'])->name('payments.pay');
    Route::post('payments/{payment}/process', [PaymentController::class, 'processPayment'])->name('payments.process');
    Route::patch('payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payments.mark-paid');
    Route::get('payments/{payment}/success', [PaymentController::class, 'paymentSuccess'])->name('payments.success');
    Route::get('payments/{payment}/cancel', [PaymentController::class, 'paymentCancel'])->name('payments.cancel');

    Route::get('refunds', function () {
        abort_unless(auth()->user()->isOwner(), 403);
        return app(RefundController::class)->index();
    })->name('refunds.index');

    Route::get('refunds/{refund}', function ($refund) {
        abort_unless(auth()->user()->isOwner(), 403);
        return app(RefundController::class)->show($refund);
    })->name('refunds.show');

    Route::post('refunds/{refund}/retry', function ($refund) {
        abort_unless(auth()->user()->isOwner(), 403);
        return app(RefundController::class)->retry($refund);
    })->name('refunds.retry');

    Route::post('refunds/{refund}/manual', function ($refund) {
        abort_unless(auth()->user()->isOwner(), 403);
        return app(RefundController::class)->manualRefund(request(), $refund);
    })->name('refunds.manual');

    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export-pdf', [AnalyticsController::class, 'exportPDF'])->name('analytics.export-pdf');

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/track', [OrderController::class, 'track'])->name('orders.track');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/update-shipping', [OrderController::class, 'updateShippingStatus'])->name('orders.update-shipping');
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/mark-received', [OrderController::class, 'markAsReceived'])->name('orders.mark-received');
    Route::post('orders/{order}/request-return', [OrderController::class, 'requestReturn'])->name('orders.request-return');
    Route::post('orders/{order}/approve-return', [OrderController::class, 'approveReturn'])->name('orders.approve-return');
    Route::post('orders/{order}/reject-return', [OrderController::class, 'rejectReturn'])->name('orders.reject-return');

    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::post('/staff/activate-all', [StaffController::class, 'activateAll'])->name('staff.activate-all');
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::patch('/staff/{staff}/activate', [StaffController::class, 'activate'])->name('staff.activate');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

    Route::get('/notifications', [WebNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [WebNotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::patch('/notifications/{id}/read', [WebNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/mark-all-read', [WebNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [WebNotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/stats', [WebNotificationController::class, 'stats'])->name('notifications.stats');
    Route::get('/notifications/type/{type}', [WebNotificationController::class, 'byType'])->name('notifications.by-type');
});

if (app()->environment('local')) {
    Route::middleware(['auth'])->group(function () {
        Route::post('seed/past-bookings', [SeederController::class, 'runPastDataSeeder'])->name('seed.past-bookings');
        Route::post('seed/delete-past-data', [SeederController::class, 'deletePastData'])->name('seed.delete-past-data');
    });
}
