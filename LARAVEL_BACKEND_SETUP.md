# 🏸 Laravel Backend Setup for SmashZone Dashboard

## 📋 Complete Laravel Configuration Guide

This guide covers **everything** you need to do on the Laravel side to support the new professional mobile dashboard.

---

## 🎯 Overview

The mobile app needs a new API endpoint (`/api/dashboard`) that returns:
- User statistics (upcoming bookings, total bookings, total spent)
- List of upcoming bookings
- Court information

---

## ✅ Step 1: Create Dashboard API Controller

### Create the Controller File

**Location**: `app/Http/Controllers/Api/DashboardController.php`

Create this new file with the following content:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard data for mobile app
     * Returns user stats and upcoming bookings
     */
    public function getDashboardData(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            // Count upcoming bookings (future dates, not cancelled)
            $upcomingBookingsCount = DB::table('bookings')
                ->where('user_id', $user->id)
                ->where('date', '>=', now()->format('Y-m-d'))
                ->whereIn('status', ['confirmed', 'pending'])
                ->count();
            
            // Count total bookings
            $totalBookingsCount = DB::table('bookings')
                ->where('user_id', $user->id)
                ->count();
            
            // Calculate total spent
            $totalSpent = DB::table('bookings')
                ->where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->sum('total_price');
            
            // Format total spent
            $totalSpentFormatted = 'RM ' . number_format($totalSpent ?? 0, 2);
            
            // Get upcoming bookings with court details
            $upcomingBookings = DB::table('bookings')
                ->leftJoin('courts', 'bookings.court_id', '=', 'courts.id')
                ->select(
                    'bookings.id',
                    'courts.name as court_name',
                    'bookings.date',
                    DB::raw("CONCAT(TIME_FORMAT(bookings.start_time, '%h:%i %p'), ' - ', TIME_FORMAT(bookings.end_time, '%h:%i %p')) as time_slot"),
                    'bookings.status',
                    DB::raw('CONCAT("RM ", FORMAT(bookings.total_price, 2)) as price')
                )
                ->where('bookings.user_id', $user->id)
                ->where('bookings.date', '>=', now()->format('Y-m-d'))
                ->whereIn('bookings.status', ['confirmed', 'pending'])
                ->orderBy('bookings.date', 'asc')
                ->orderBy('bookings.start_time', 'asc')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'upcoming_bookings' => $upcomingBookingsCount,
                    'total_bookings' => $totalBookingsCount,
                    'total_spent' => $totalSpentFormatted
                ],
                'upcoming_bookings' => $upcomingBookings
            ]);
            
        } catch (\Exception $e) {
            // If there's an error, return empty data with error message
            \Log::error('Dashboard API Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'upcoming_bookings' => 0,
                    'total_bookings' => 0,
                    'total_spent' => 'RM 0.00'
                ],
                'upcoming_bookings' => []
            ]);
        }
    }
}
```

### Terminal Command:
```bash
# Navigate to your Laravel project
cd /Users/aimarhaizzad/SmashZone/SmashZone

# Create the controller directory if it doesn't exist
mkdir -p app/Http/Controllers/Api

# Create the file (then paste the content above)
touch app/Http/Controllers/Api/DashboardController.php
```

---

## ✅ Step 2: Add API Route

### Edit: `routes/api.php`

Add this route to your existing API routes:

```php
use App\Http\Controllers\Api\DashboardController;

// ... your existing routes ...

// Dashboard endpoint for mobile app
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'getDashboardData']);
});
```

### Complete Example `routes/api.php`:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CourtController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WebUrlController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
    
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
    
    // Web URL generation
    Route::get('/generate-web-url', [WebUrlController::class, 'generateWebUrl']);
});
```

---

## ✅ Step 3: Verify Database Tables

### Check Your Database Structure

Your database **must** have these tables with these columns:

### 1. **bookings** table

**Required columns:**
- `id` - Primary key
- `user_id` - Foreign key to users table
- `court_id` - Foreign key to courts table
- `date` - Booking date (DATE format)
- `start_time` - Start time (TIME format)
- `end_time` - End time (TIME format)
- `status` - Booking status (enum: 'pending', 'confirmed', 'cancelled', 'completed')
- `total_price` - Price (DECIMAL format)
- `created_at` - Timestamp
- `updated_at` - Timestamp

### 2. **courts** table

**Required columns:**
- `id` - Primary key
- `name` - Court name (VARCHAR)
- Other columns are optional

### 3. **users** table

**Required columns:**
- `id` - Primary key
- `name` - User name
- `email` - User email
- Other standard Laravel auth columns

---

## ✅ Step 4: Create Database Migration (If Needed)

If your tables don't exist or need modification, create migrations:

### Check Existing Tables:
```bash
php artisan migrate:status
```

### If you need to create the bookings table:

```bash
php artisan make:migration create_bookings_table
```

**Edit the migration file** (`database/migrations/XXXX_XX_XX_XXXXXX_create_bookings_table.php`):

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])
                  ->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
```

### If you need to create the courts table:

```bash
php artisan make:migration create_courts_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'maintenance', 'closed'])
                  ->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('courts');
    }
};
```

### Run the migrations:
```bash
php artisan migrate
```

---

## ✅ Step 5: Update CORS Configuration

### Edit: `config/cors.php`

Ensure your CORS settings allow mobile app requests:

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],  // In production, specify your domain

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

### Publish CORS config if it doesn't exist:
```bash
php artisan config:publish cors
```

---

## ✅ Step 6: Configure Laravel Sanctum

### Ensure Sanctum is installed and configured:

```bash
# Install Sanctum (if not already installed)
composer require laravel/sanctum

# Publish Sanctum configuration
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run migrations
php artisan migrate
```

### Edit: `config/sanctum.php`

```php
<?php

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    'guard' => ['web'],

    'expiration' => null,

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

### Edit: `app/Http/Kernel.php`

Add Sanctum middleware to API:

```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

---

## ✅ Step 7: Update User Model

### Edit: `app/Models/User.php`

Ensure User model has Sanctum trait:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'position',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function courts()
    {
        return $this->hasMany(Court::class, 'owner_id');
    }
}
```

---

## ✅ Step 8: Clear Cache and Optimize

Run these commands to ensure everything is fresh:

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache

# Optimize for production (optional)
php artisan optimize
```

---

## ✅ Step 9: Start Laravel Server

### Start the server with your current IP:

```bash
cd /Users/aimarhaizzad/SmashZone/SmashZone
php artisan serve --host=10.62.86.15 --port=8000
```

**Replace `10.62.86.15` with your actual IP address.**

### To find your IP:

**macOS:**
```bash
ipconfig getifaddr en0  # For Wi-Fi
# or
ifconfig | grep "inet " | grep -v 127.0.0.1
```

**Windows:**
```bash
ipconfig
```

**Linux:**
```bash
hostname -I
# or
ip addr show
```

---

## ✅ Step 10: Test the API Endpoint

### Test with Browser or Postman:

**1. First, login to get a token:**

```bash
curl -X POST http://10.62.86.15:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "your-email@example.com",
    "password": "your-password"
  }'
```

**Response:**
```json
{
  "success": true,
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "user": {
    "id": 1,
    "name": "Your Name",
    "email": "your-email@example.com"
  }
}
```

**2. Test the dashboard endpoint:**

```bash
curl -X GET http://10.62.86.15:8000/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "stats": {
    "upcoming_bookings": 2,
    "total_bookings": 15,
    "total_spent": "RM 450.00"
  },
  "upcoming_bookings": [
    {
      "id": 1,
      "court_name": "Court A",
      "date": "2025-10-28",
      "time_slot": "10:00 AM - 11:00 AM",
      "status": "confirmed",
      "price": "RM 30.00"
    },
    {
      "id": 2,
      "court_name": "Court B",
      "date": "2025-10-29",
      "time_slot": "02:00 PM - 03:00 PM",
      "status": "pending",
      "price": "RM 35.00"
    }
  ]
}
```

---

## ✅ Step 11: Add Sample Test Data (Optional)

If you want to test with sample data:

```sql
-- Create a test user (password is "password")
INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at) 
VALUES (
    'Test User', 
    'test@example.com',
    NOW(),
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    NOW(),
    NOW()
);

-- Create test courts
INSERT INTO courts (owner_id, name, description, status, created_at, updated_at) 
VALUES 
    (1, 'Court A', 'Premium court', 'active', NOW(), NOW()),
    (1, 'Court B', 'Standard court', 'active', NOW(), NOW()),
    (1, 'Court C', 'Economy court', 'active', NOW(), NOW());

-- Create test bookings
INSERT INTO bookings (user_id, court_id, date, start_time, end_time, status, total_price, created_at, updated_at)
VALUES
    (1, 1, '2025-10-28', '10:00:00', '11:00:00', 'confirmed', 30.00, NOW(), NOW()),
    (1, 2, '2025-10-29', '14:00:00', '15:00:00', 'pending', 35.00, NOW(), NOW()),
    (1, 1, '2025-10-30', '16:00:00', '17:00:00', 'confirmed', 30.00, NOW(), NOW()),
    (1, 3, '2024-10-20', '09:00:00', '10:00:00', 'completed', 25.00, NOW(), NOW()),
    (1, 2, '2024-10-15', '11:00:00', '12:00:00', 'completed', 35.00, NOW(), NOW());
```

Or use Laravel Seeder:

```bash
php artisan make:seeder BookingSeeder
```

---

## 🔍 Verify Everything is Working

### Checklist:

- [ ] DashboardController.php file exists in `app/Http/Controllers/Api/`
- [ ] Route is added to `routes/api.php`
- [ ] Database tables exist (bookings, courts, users)
- [ ] Laravel Sanctum is installed and configured
- [ ] CORS is configured properly
- [ ] Server is running on correct IP and port
- [ ] API endpoint returns JSON response
- [ ] Authentication works (login returns token)
- [ ] Dashboard endpoint returns data with valid token

### Test Commands:

```bash
# Check if route exists
php artisan route:list | grep dashboard

# Check database tables
php artisan migrate:status

# Check for errors
tail -f storage/logs/laravel.log

# Test API
php artisan tinker
>>> $user = User::first();
>>> $token = $user->createToken('test')->plainTextToken;
>>> echo $token;
```

---

## 🐛 Troubleshooting

### Issue: "Class DashboardController not found"

**Solution:**
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Issue: "Table 'bookings' doesn't exist"

**Solution:**
```bash
php artisan migrate
# or
php artisan migrate:fresh --seed
```

### Issue: "Unauthenticated" error

**Solution:**
- Check if user is logged in
- Verify token is being sent in Authorization header
- Check Sanctum configuration
- Verify user model has `HasApiTokens` trait

### Issue: API returns empty data

**Solution:**
- Check if bookings exist in database
- Verify user_id matches between app and database
- Check the SQL query in DashboardController
- Look at Laravel logs: `storage/logs/laravel.log`

### Issue: CORS error

**Solution:**
- Update `config/cors.php`
- Clear config cache: `php artisan config:clear`
- Restart Laravel server

---

## 📂 File Structure Summary

After completing this setup, you should have:

```
your-laravel-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php (existing)
│   │   │   │   ├── BookingController.php (existing)
│   │   │   │   ├── CourtController.php (existing)
│   │   │   │   ├── DashboardController.php (NEW! ← Created)
│   │   │   │   ├── PaymentController.php (existing)
│   │   │   │   └── WebUrlController.php (existing)
│   ├── Models/
│   │   ├── User.php (updated)
│   │   ├── Booking.php
│   │   └── Court.php
├── config/
│   ├── cors.php (verified)
│   └── sanctum.php (verified)
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   ├── create_bookings_table.php
│   │   └── create_courts_table.php
├── routes/
│   ├── api.php (updated ← Added dashboard route)
│   └── web.php
└── storage/
    └── logs/
        └── laravel.log
```

---

## 📋 Quick Command Reference

```bash
# Navigate to Laravel project
cd /Users/aimarhaizzad/SmashZone/SmashZone

# Create controller directory
mkdir -p app/Http/Controllers/Api

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Check routes
php artisan route:list | grep dashboard

# Check migrations
php artisan migrate:status

# Run migrations
php artisan migrate

# Start server
php artisan serve --host=10.62.86.15 --port=8000

# View logs
tail -f storage/logs/laravel.log

# Test in Tinker
php artisan tinker
```

---

## 🎯 What the API Returns

### Success Response:
```json
{
  "success": true,
  "stats": {
    "upcoming_bookings": 3,
    "total_bookings": 15,
    "total_spent": "RM 450.00"
  },
  "upcoming_bookings": [
    {
      "id": "1",
      "court_name": "Court A",
      "date": "2025-10-28",
      "time_slot": "10:00 AM - 11:00 AM",
      "status": "confirmed",
      "price": "RM 30.00"
    }
  ]
}
```

### Error Response (No Auth):
```json
{
  "success": false,
  "message": "User not authenticated"
}
```

### Empty Data Response:
```json
{
  "success": true,
  "stats": {
    "upcoming_bookings": 0,
    "total_bookings": 0,
    "total_spent": "RM 0.00"
  },
  "upcoming_bookings": []
}
```

---

## ✅ Final Checklist

Before testing with mobile app:

- [ ] Laravel project is at: `/Users/aimarhaizzad/SmashZone/SmashZone`
- [ ] DashboardController.php created in correct location
- [ ] Route added to `routes/api.php`
- [ ] Database tables exist and have correct structure
- [ ] Sample data added (optional)
- [ ] Server running: `php artisan serve --host=YOUR_IP --port=8000`
- [ ] API tested with curl or Postman
- [ ] Logs are clean (no errors in `storage/logs/laravel.log`)

---

## 🚀 You're Ready!

Once all steps are complete:

1. Keep Laravel server running
2. Open Android Studio
3. Build and run the mobile app
4. Login and view your professional dashboard!

**The mobile app will now fetch real data from your Laravel backend! 🏸**

---

*Last Updated: January 2025*  
*Compatible with: Laravel 10+, Laravel 11+*
