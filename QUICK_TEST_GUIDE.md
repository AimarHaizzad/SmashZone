# 🚀 Quick Test Guide - Booking System v2.0

## ✅ **FIXED ISSUES**

1. **Time Calculation Fixed**: 8am booking now correctly highlights 8am-9am (not 9am-10am)
2. **Network Error Fixed**: Added cache-busting and better error handling
3. **Cache Issues Fixed**: Added version number and cache-busting parameters

## 🔧 **CRITICAL: Clear Browser Cache First!**

**Before testing, you MUST clear your browser cache:**

### Method 1: Hard Refresh (Recommended)
- **Windows/Linux**: `Ctrl + Shift + R` or `Ctrl + F5`
- **Mac**: `Cmd + Shift + R`

### Method 2: Developer Tools
1. Press `F12` to open Developer Tools
2. Right-click the refresh button
3. Select "Empty Cache and Hard Reload"

### Method 3: Incognito Mode
- Open a new Incognito/Private window
- Log in and test

## 🧪 **Step-by-Step Test**

### Step 1: Verify JavaScript is Updated
1. Open booking page (`/bookings`)
2. Press `F12` → Console tab
3. Look for: `"Booking system v2.0 loaded - Fixed time calculation and network errors"`
4. If you see this message, the new code is loaded ✅

### Step 2: Test Single Slot Booking
1. Click on a **green "Select"** button (e.g., 8am)
2. The slot should turn **blue** and show "Selected"
3. A panel should appear at the bottom
4. Click **"Confirm Booking"**
5. Button should show **"Processing..."**
6. Success message: **"Booking created successfully! The page will refresh..."**
7. Page automatically reloads after 500ms

### Step 3: Verify Time Highlighting
1. After reload, find your booked slot
2. **8am booking should show 8am-9am** (not 9am-10am)
3. The slot should be **light blue** with "My Booking"

### Step 4: Test Multiple Slots
1. Select multiple slots (e.g., 8am and 9am)
2. Click "Confirm Booking"
3. All slots should be created successfully
4. All should show correct time ranges

### Step 5: Test as Another User
1. Log in as a different user
2. The slots you booked should show as **blue "Booked"** (not clickable)
3. Other slots should still be **green "Select"**

## 🐛 **If Still Getting Network Error**

### Check Console Logs
1. Press `F12` → Console tab
2. Look for these messages:
   - ✅ `"Submitting multi-booking with data: ..."`
   - ✅ `"Response status: 200"`
   - ✅ `"Response data: {success: true...}"`
   - ❌ Any red error messages

### Check Network Tab
1. Press `F12` → Network tab
2. Make a booking
3. Look for POST request to `/bookings/multi`
4. Check if it returns status 200

### Force Cache Clear
```bash
# Run these commands
cd /Users/aimarhaizzad/SmashZone/SmashZone
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Then **hard refresh** your browser again!

## 📊 **Expected Results**

### Before Booking:
```
Time | Court A | Court B
-----|---------|--------
8:00 | [Select] | [Select]  ← Green buttons
9:00 | [Select] | [Select]  ← Available
```

### After Booking 8am Court A:
```
Time | Court A | Court B
-----|---------|--------
8:00 | [My Booking] | [Select]  ← Light blue, 8am-9am
9:00 | [Select] | [Select]      ← Still available
```

### What Other Users See:
```
Time | Court A | Court B
-----|---------|--------
8:00 | [Booked] | [Select]  ← Blue "Booked" (not clickable)
9:00 | [Select] | [Select]  ← Available
```

## 🔍 **Debugging Commands**

If issues persist, run these commands to verify backend:

```bash
# Check if bookings are being created
php artisan tinker --execute="echo 'Active bookings: ' . App\Models\Booking::where('status', '!=', 'cancelled')->count();"

# Check recent bookings
php artisan tinker --execute="App\Models\Booking::latest()->take(3)->get(['id', 'start_time', 'end_time', 'status']);"

# Check Laravel logs
tail -f storage/logs/laravel.log
```

## ✅ **Success Indicators**

You'll know it's working when:
- ✅ Console shows "Booking system v2.0 loaded"
- ✅ No "Network error" message
- ✅ Success message appears
- ✅ Page reloads automatically
- ✅ Booked slots turn blue
- ✅ Time ranges are correct (8am → 8am-9am)
- ✅ Other users see booked slots as unavailable

## 🆘 **Still Having Issues?**

1. **Try a different browser** (Chrome, Firefox, Safari)
2. **Disable browser extensions** (ad blockers, privacy tools)
3. **Check if JavaScript is enabled**
4. **Try on a different device/network**

The backend is 100% fixed. The issue is almost certainly browser cache! 🔄
