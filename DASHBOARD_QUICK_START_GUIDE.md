# 🚀 SmashZone Dashboard Quick Start Guide

## ⚡ Get Your Professional Dashboard Running in 5 Minutes!

Follow this checklist to set up your new professional dashboard with all the latest features.

---

## 📋 Prerequisites Checklist

Before starting, ensure you have:
- [ ] Laravel project set up and running
- [ ] Database configured and migrated
- [ ] Android Studio with your SmashZone project
- [ ] Current IP address (check with `ipconfig` or `ifconfig`)

---

## ✅ Step 1: Verify Laravel Dashboard Controller (1 minute)

Your Laravel project already has the dashboard controller at:
```
app/Http/Controllers/DashboardController.php
```

**Current Features:**
- ✅ Professional dashboard for owners, staff, and customers
- ✅ Live badminton news integration
- ✅ Analytics cards with real-time data
- ✅ Quick actions for common tasks
- ✅ Recent bookings and activity feeds
- ✅ Mobile app authentication integration

---

## ✅ Step 2: Verify API Routes (30 seconds)

Your `routes/api.php` already includes:

```php
// API Authentication Routes for SmashZone App
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Protected API Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
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
});

// Generate authenticated web URL for mobile app
Route::middleware('auth:sanctum')->get('/generate-web-url', [WebUrlController::class, 'generateWebUrl']);
```

---

## ✅ Step 3: Verify Database Tables (1 minute)

Your database should have these tables with the correct structure:

### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('owner', 'staff', 'customer') DEFAULT 'customer',
    phone VARCHAR(255) NULL,
    position VARCHAR(255) NULL,
    profile_picture VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Courts Table
```sql
CREATE TABLE courts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    owner_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    status ENUM('active', 'maintenance', 'closed') DEFAULT 'active',
    location ENUM('middle', 'edge', 'corner', 'center', 'side', 'front', 'back') NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Bookings Table
```sql
CREATE TABLE bookings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    court_id BIGINT NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (court_id) REFERENCES courts(id) ON DELETE CASCADE
);
```

### Payments Table
```sql
CREATE TABLE payments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(255) NULL,
    transaction_id VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);
```

**To verify your tables:**
```bash
php artisan migrate:status
```

---

## ✅ Step 4: Update IP Address Configuration (1 minute)

### Current IP Address Detection
Your current IP should be: **`10.62.93.132`**

**Update these files with your current IP:**

1. **Laravel WebUrlController** (`app/Http/Controllers/Api/WebUrlController.php`):
```php
$baseUrl = 'http://YOUR_CURRENT_IP:8000';  // Update line 42
```

2. **Android ApiService** (`app/src/main/java/com/smashzone/app/ApiService.kt`):
```kotlin
private const val BASE_URL = "http://YOUR_CURRENT_IP:8000/api/"  // Update line 15
```

3. **Android Network Security Config** (`app/src/main/res/xml/network_security_config.xml`):
```xml
<domain includeSubdomains="true">YOUR_CURRENT_IP</domain>  <!-- Update line 4 -->
```

---

## ✅ Step 5: Start Laravel Server (30 seconds)

```bash
cd /Users/aimarhaizzad/SmashZone/SmashZone
php artisan serve --host=10.62.93.132 --port=8000
```

**Replace `10.62.93.132` with your current IP address.**

---

## ✅ Step 6: Test Laravel Dashboard (1 minute)

1. Open browser and go to: `http://10.62.93.132:8000/dashboard`
2. You should see the professional dashboard with:
   - ✅ Welcome message
   - ✅ Analytics cards (Total Bookings, Revenue, etc.)
   - ✅ Quick Actions section
   - ✅ Recent Bookings table
   - ✅ Live Badminton News section

---

## ✅ Step 7: Rebuild Android App (2 minutes)

In Android Studio:
1. **Build** → **Clean Project**
2. **Build** → **Rebuild Project**
3. Click **Run** (▶️) button

---

## ✅ Step 8: Test Mobile Dashboard (1 minute)

1. Open the app on your device/emulator
2. Login with your credentials
3. Navigate to the Dashboard/Home screen
4. You should see:
   - ✅ Professional dashboard layout
   - ✅ Real-time data from Laravel
   - ✅ Quick action buttons
   - ✅ Upcoming bookings (if any)

---

## 🎯 Dashboard Features Overview

### For Owners:
- ✅ **Analytics Cards**: Courts Owned, Total Bookings, Total Revenue
- ✅ **Quick Actions**: Manage Bookings, Payment Management, Court Management
- ✅ **Recent Bookings**: Latest 5 bookings with full details
- ✅ **Live News**: Badminton news and updates
- ✅ **Team Management**: Staff management tools

### For Staff:
- ✅ **Analytics Cards**: Total Bookings, Today's Bookings, Pending Payments, Total Revenue
- ✅ **Quick Actions**: Manage Bookings, Payment Management, Court Management
- ✅ **Recent Bookings**: Latest 5 bookings with full details
- ✅ **Recent Activity**: Activity feed with booking updates
- ✅ **Full Court Access**: Can edit/delete all courts

### For Customers:
- ✅ **Analytics Cards**: Upcoming Bookings, Total Bookings, Total Spent
- ✅ **Quick Actions**: Book Court, My Bookings, Shop
- ✅ **Upcoming Bookings**: Next scheduled bookings
- ✅ **Live News**: Badminton news and updates

---

## 🐛 Quick Troubleshooting

### Dashboard shows "Loading..." forever
**Solutions:**
- Check if Laravel server is running: `php artisan serve --host=YOUR_IP --port=8000`
- Verify IP address in Android `ApiService.kt` matches your server IP
- Check Laravel logs: `storage/logs/laravel.log`

### "Network error" message
**Solutions:**
- Verify Laravel server is accessible: Open browser to `http://YOUR_IP:8000`
- Check `network_security_config.xml` has your IP address
- Ensure firewall allows connections on port 8000

### Dashboard shows "No data"
**Solutions:**
- This is normal if you haven't created any bookings yet
- Add test data using the sample data section below
- Check database connection: `php artisan migrate:status`

### "Failed to load dashboard data"
**Solutions:**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify API routes are registered: `php artisan route:list`
- Check if user is authenticated properly
- Verify Sanctum tokens are working

### Mobile app can't connect to Laravel
**Solutions:**
- Update IP address in all configuration files
- Check network security config allows your IP
- Verify Laravel CORS settings
- Test API endpoints directly in browser

---

## 🧪 Test with Sample Data (Optional)

Want to see the dashboard with data? Add test data to your database:

### 1. Create Test User
```sql
INSERT INTO users (name, email, password, role) VALUES 
('Test Owner', 'owner@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'owner'),
('Test Staff', 'staff@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff'),
('Test Customer', 'customer@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');
```

### 2. Create Test Courts
```sql
INSERT INTO courts (owner_id, name, description, status) VALUES 
(1, 'Court 1', 'Main badminton court', 'active'),
(1, 'Court 2', 'Secondary court', 'active'),
(1, 'Court 3', 'Training court', 'maintenance');
```

### 3. Create Test Bookings
```sql
INSERT INTO bookings (user_id, court_id, date, start_time, end_time, status, total_price) VALUES 
(3, 1, '2025-01-15', '10:00:00', '11:00:00', 'confirmed', 30.00),
(3, 2, '2025-01-16', '14:00:00', '15:00:00', 'pending', 30.00),
(3, 1, '2025-01-17', '16:00:00', '17:00:00', 'completed', 30.00);
```

### 4. Create Test Payments
```sql
INSERT INTO payments (booking_id, amount, status, payment_method) VALUES 
(1, 30.00, 'paid', 'cash'),
(2, 30.00, 'pending', 'card'),
(3, 30.00, 'paid', 'cash');
```

**Then refresh the app to see your test data!**

---

## 📱 Current Configuration

### Laravel Server
- **URL**: `http://10.62.93.132:8000`
- **API Base**: `http://10.62.93.132:8000/api/`
- **Dashboard**: `http://10.62.93.132:8000/dashboard`

### Android App
- **API Service**: Points to `http://10.62.93.132:8000/api/`
- **Network Config**: Allows `10.62.93.132`
- **Authentication**: Uses Laravel Sanctum tokens

---

## ✨ Features Checklist

After setup, verify these features work:

### Dashboard Loading
- [ ] Dashboard loads with welcome message
- [ ] Analytics cards show correct numbers
- [ ] Loading spinner appears while fetching data
- [ ] Error handling works for network issues

### Navigation
- [ ] Dashboard link appears in navigation (for staff)
- [ ] Quick action buttons work correctly
- [ ] "View All" buttons navigate properly
- [ ] Mobile app authentication works

### Data Display
- [ ] Upcoming bookings display with correct data
- [ ] Status badges show correct colors
- [ ] Recent activity feed updates properly
- [ ] Live news section loads (if configured)

### User Roles
- [ ] Owner dashboard shows owner-specific features
- [ ] Staff dashboard shows staff-specific features
- [ ] Customer dashboard shows customer-specific features
- [ ] Role-based permissions work correctly

---

## 📚 Additional Resources

### Documentation Files
- **Complete Setup Guide**: `PROFESSIONAL_DASHBOARD_SETUP.md`
- **What's New**: `WHATS_NEW.md`
- **Troubleshooting**: `TROUBLESHOOTING_BOOKING.md`
- **API Setup**: `LARAVEL_API_SETUP.md`
- **Mobile Integration**: `MOBILE_APP_INTEGRATION_GUIDE.md`

### Key Files to Know
- **Dashboard Controller**: `app/Http/Controllers/DashboardController.php`
- **Dashboard View**: `resources/views/dashboard.blade.php`
- **API Routes**: `routes/api.php`
- **Web Routes**: `routes/web.php`
- **Mobile Auth**: `app/Http/Controllers/Api/WebUrlController.php`

---

## 🎉 You're Done!

Your SmashZone app now has a professional dashboard that displays:
- ✅ Real-time booking statistics
- ✅ Upcoming bookings preview
- ✅ Quick action buttons
- ✅ Modern, professional design
- ✅ Role-based content
- ✅ Live badminton news
- ✅ Mobile app integration

**Enjoy your upgraded app! 🏸**

---

## 💡 Pro Tips

1. **Auto-refresh**: Dashboard refreshes automatically when you open the Home screen
2. **Pull from database**: All data comes from your Laravel database
3. **Secure**: Uses Laravel Sanctum tokens for authentication
4. **Responsive**: Works on all screen sizes
5. **Customizable**: Colors and text can be easily changed in the Blade templates
6. **Role-based**: Different content for owners, staff, and customers
7. **Mobile-friendly**: Seamless integration between mobile app and web dashboard

---

## 🆘 Need Help?

If something isn't working:

### Check Android Logcat
1. Open Android Studio
2. Go to **View** → **Tool Windows** → **Logcat**
3. Look for error messages related to API calls

### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Verify Database
```bash
php artisan migrate:status
php artisan db:show
```

### Test API Endpoints
```bash
# Test authentication
curl -X POST http://YOUR_IP:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# Test dashboard data
curl -X GET http://YOUR_IP:8000/api/bookings \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Common Issues
1. **IP Address Changed**: Update all configuration files
2. **Database Issues**: Run `php artisan migrate:fresh --seed`
3. **Permission Issues**: Check file permissions on storage/logs
4. **Network Issues**: Verify firewall and network security config

---

## 🔄 Updates and Maintenance

### Regular Updates
- Check for Laravel updates: `composer update`
- Update Android dependencies in `build.gradle`
- Monitor Laravel logs for errors
- Backup database regularly

### Performance Optimization
- Enable Laravel caching: `php artisan config:cache`
- Optimize database queries
- Use Laravel Telescope for debugging
- Monitor API response times

---

**Happy Booking! 🏸**

---

*Last Updated: January 2025*
*Version: 2.0*
*Compatible with: Laravel 10+, Android API 21+*
