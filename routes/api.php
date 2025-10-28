<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourtController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WebUrlController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\BookingNotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API Authentication Routes for SmashZone App
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Protected API Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Dashboard - NEW!
    Route::get('/dashboard', [DashboardController::class, 'getDashboardData']);
    
    // Courts
    Route::get('/courts', [CourtController::class, 'index']);
    Route::get('/courts/{court}', [CourtController::class, 'show']);
    
    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
    
    // Payments
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::post('/payments/{payment}/process', [PaymentController::class, 'processPayment']);
    
    // Generate authenticated web URL for mobile app
    Route::get('/generate-web-url', [WebUrlController::class, 'generateWebUrl']);
    
    // Push Notifications
    Route::post('/fcm-token', [NotificationController::class, 'storeFCMToken']);
    Route::delete('/fcm-token', [NotificationController::class, 'deleteFCMToken']);
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    
    // Booking Notifications
    Route::post('/booking/{id}/confirm-notification', [BookingNotificationController::class, 'sendBookingConfirmation']);
    Route::post('/booking/{id}/reminder-notification', [BookingNotificationController::class, 'sendBookingReminder']);
    Route::post('/booking/{id}/starting-soon-notification', [BookingNotificationController::class, 'sendBookingStartingSoon']);
    Route::post('/booking/{id}/cancelled-notification', [BookingNotificationController::class, 'sendBookingCancelled']);
    Route::post('/booking/{id}/payment-reminder', [BookingNotificationController::class, 'sendPaymentReminder']);
});

// Public routes for testing and cron jobs
Route::get('/test-booking-notification', [BookingNotificationController::class, 'testBookingNotification']);
Route::get('/send-pending-reminders', [BookingNotificationController::class, 'sendPendingReminders']);
Route::get('/send-starting-soon-notifications', [BookingNotificationController::class, 'sendStartingSoonNotifications']);

// Test routes for different notification types
Route::get('/test-booking-confirmed', function () {
    return app(BookingNotificationController::class)->testBookingNotification(request()->merge(['type' => 'booking_confirmed']));
});

Route::get('/test-booking-reminder', function () {
    return app(BookingNotificationController::class)->testBookingNotification(request()->merge(['type' => 'booking_reminder']));
});

Route::get('/test-booking-starting-soon', function () {
    return app(BookingNotificationController::class)->testBookingNotification(request()->merge(['type' => 'booking_starting_soon']));
});

Route::get('/test-booking-cancelled', function () {
    return app(BookingNotificationController::class)->testBookingNotification(request()->merge(['type' => 'booking_cancelled']));
});

Route::get('/test-payment-reminder', function () {
    return app(BookingNotificationController::class)->testBookingNotification(request()->merge(['type' => 'payment_reminder']));
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
