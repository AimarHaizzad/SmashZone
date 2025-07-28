<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookings (for customers)
    Route::resource('bookings', BookingController::class);

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
    Route::resource('staff', StaffController::class);

    // Payments
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');

    // TODO: Add booking and report routes here
});

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
Route::get('cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('cart/update', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::post('cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('cart/checkout', [App\Http\Controllers\StripeController::class, 'checkout'])->name('cart.checkout');
Route::get('cart/success', [App\Http\Controllers\StripeController::class, 'success'])->name('stripe.success');
Route::get('cart/cancel', [App\Http\Controllers\StripeController::class, 'cancel'])->name('stripe.cancel');
