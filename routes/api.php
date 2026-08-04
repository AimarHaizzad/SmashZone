<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BookingNotificationController;
use App\Http\Controllers\Api\BookingsController;
use App\Http\Controllers\Api\CourtController;
use App\Http\Controllers\Api\CourtsController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentsController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\WebUrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'getDashboardData']);

    Route::get('/products', [ProductsController::class, 'getProducts']);
    Route::get('/bookings', [BookingsController::class, 'getBookings']);
    Route::get('/payments', [PaymentsController::class, 'getPayments']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::get('/courts', [CourtsController::class, 'getCourts']);
    Route::get('/courts/availability', [CourtsController::class, 'getAvailability']);

    Route::get('/courts/{court}', [CourtController::class, 'show']);

    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);

    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::post('/payments/{payment}/process', [PaymentController::class, 'processPayment']);

    Route::get('/generate-web-url', [WebUrlController::class, 'generateWebUrl']);

    Route::post('/fcm-token', [NotificationController::class, 'storeFCMToken']);
    Route::delete('/fcm-token', [NotificationController::class, 'deleteFCMToken']);
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);

    Route::post('/booking/{id}/confirm-notification', [BookingNotificationController::class, 'sendBookingConfirmation']);
    Route::post('/booking/{id}/reminder-notification', [BookingNotificationController::class, 'sendBookingReminder']);
    Route::post('/booking/{id}/starting-soon-notification', [BookingNotificationController::class, 'sendBookingStartingSoon']);
    Route::post('/booking/{id}/cancelled-notification', [BookingNotificationController::class, 'sendBookingCancelled']);
    Route::post('/booking/{id}/payment-reminder', [BookingNotificationController::class, 'sendPaymentReminder']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

if (app()->environment('local')) {
    Route::get('/send-pending-reminders', [BookingNotificationController::class, 'sendPendingReminders']);
    Route::get('/send-starting-soon-notifications', [BookingNotificationController::class, 'sendStartingSoonNotifications']);
}
