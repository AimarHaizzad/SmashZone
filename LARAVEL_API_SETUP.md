# 🚀 Laravel API Setup for SmashZone App

## 📁 **Files to Create/Update in Your Laravel Project**

### 1. **API Routes** (`routes/api.php`)
Add these routes to your Laravel project:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// API Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});
```

### 2. **Auth Controller** (`app/Http/Controllers/Api/AuthController.php`)
Create this controller:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and return token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required_without:username|email',
            'username' => 'required_without:email|string',
            'password' => 'required|string',
        ]);

        // Determine login field
        $loginField = $request->has('email') ? 'email' : 'name';
        $loginValue = $request->has('email') ? $request->email : $request->username;

        // Attempt authentication
        if (Auth::attempt([$loginField => $loginValue, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('smashzone-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'position' => $user->position,
                    'role' => $user->role,
                ],
                'token' => $token
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Get authenticated user info
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'position' => $user->position,
            'role' => $user->role,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
```

### 3. **User Model** (`app/Models/User.php`)
Make sure your User model has these fields:

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
        'phone',
        'position',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

### 4. **CORS Configuration** (`config/cors.php`)
Update your CORS settings to allow the mobile app:

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // In production, specify your app's domain
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

### 5. **Sanctum Configuration** (`config/sanctum.php`)
Make sure Sanctum is properly configured for API tokens:

```php
<?php

use Laravel\Sanctum\Sanctum;

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort()
    ))),

    'guard' => ['web'],

    'expiration' => null,

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

## 🔧 **Installation Steps**

### 1. **Install Laravel Sanctum** (if not already installed)
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 2. **Add Sanctum to Kernel** (`app/Http/Kernel.php`)
```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

### 3. **Update User Model**
Add `HasApiTokens` trait to your User model (see above).

### 4. **Test the API**
Test your endpoints with Postman or curl:

```bash
# Test login
curl -X POST http://10.62.69.78:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'

# Test user info (with token)
curl -X GET http://10.62.69.78:8000/api/auth/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## 📱 **App Integration**

The Android app is already configured to work with these endpoints:

- **Login**: `POST /api/auth/login`
- **User Info**: `GET /api/auth/user`
- **Logout**: `POST /api/auth/logout`

## 🧪 **Testing**

1. **Test Laravel API** with Postman
2. **Test Android app** with real credentials
3. **Test web integration** - user should be auto-logged in

## 🔐 **Security Notes**

- API tokens are automatically generated by Sanctum
- Tokens are included in app requests for user info
- CORS is configured for mobile app access
- Password hashing is handled by Laravel

## 📞 **Need Help?**

If you need help with:
- Setting up Sanctum
- Database migrations
- API testing
- Web system integration

Just let me know!
