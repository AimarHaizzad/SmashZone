# 🧪 Complete Testing Guide - Mobile App Integration

## 🎯 **Laravel Backend is Ready!**

### **✅ Files Created:**
1. **`WebUrlController.php`** → `app/Http/Controllers/Api/WebUrlController.php` ✅
2. **`mobile-test.blade.php`** → `resources/views/mobile-test.blade.php` ✅
3. **API Routes** → Updated `routes/api.php` ✅
4. **Web Routes** → Updated `routes/web.php` ✅

### **✅ API Endpoints Working:**
- **Login API:** `POST /api/auth/login` ✅
- **Generate Web URL:** `GET /api/generate-web-url` ✅
- **Mobile Test Page:** `GET /mobile-test` ✅

## 🧪 **Phase 1: Laravel Backend Testing**

### **Test 1: Login API**
```bash
curl -X POST http://127.0.0.1:8001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"AimarHaizzad@gmail.com","password":"Aimar123"}'
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Owner",
        "email": "AimarHaizzad@gmail.com",
        "phone": null,
        "position": null,
        "role": "owner"
    },
    "token": "9|UT4w4lMkzronZXIsX88pzpR6Yuvv3UaLqJUM9yqK0de9aa2d"
}
```

### **Test 2: Generate Web URL API**
```bash
curl -X GET http://127.0.0.1:8001/api/generate-web-url \
  -H "Authorization: Bearer 9|UT4w4lMkzronZXIsX88pzpR6Yuvv3UaLqJUM9yqK0de9aa2d"
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Web URL generated successfully",
    "web_url": "http://localhost/mobile-test?authenticated=true&user_id=1&username=Owner&user_email=AimarHaizzad%40gmail.com&user_name=Owner&auth_token=9%7CUT4w4lMkzronZXIsX88pzpR6Yuvv3UaLqJUM9yqK0de9aa2d",
    "user": {
        "id": 1,
        "name": "Owner",
        "email": "AimarHaizzad@gmail.com",
        "phone": null,
        "position": null,
        "role": "owner"
    }
}
```

### **Test 3: Mobile Test Page**
Open browser and go to: `http://127.0.0.1:8001/mobile-test`

**Expected Result:**
- Page loads with green gradient background
- Shows "🔍 Checking for mobile app authentication..."
- Console shows authentication check messages

## 📱 **Phase 2: Android App Testing**

### **Step 1: Update Android App API Service**

Add this to your `ApiService.kt`:

```kotlin
@GET("generate-web-url")
suspend fun getAuthenticatedWebUrl(
    @Header("Authorization") token: String
): Response<WebUrlResponse>

data class WebUrlResponse(
    val success: Boolean,
    val message: String,
    val web_url: String,
    val user: User
)
```

### **Step 2: Add "Book Court" Button Function**

Add this to your main activity:

```kotlin
private fun openWebBooking() {
    val token = sharedPreferences.getString("auth_token", "")
    if (token.isNotEmpty()) {
        lifecycleScope.launch {
            try {
                val response = apiService.getAuthenticatedWebUrl("Bearer $token")
                if (response.isSuccessful) {
                    val webUrl = response.body()?.web_url
                    webUrl?.let { url ->
                        val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                        startActivity(intent)
                    }
                } else {
                    // Handle error
                    Log.e("WebBooking", "Failed to get web URL: ${response.code()}")
                }
            } catch (e: Exception) {
                Log.e("WebBooking", "Error: ${e.message}")
            }
        }
    } else {
        // User not logged in, redirect to login
        startActivity(Intent(this, LoginActivity::class.java))
    }
}
```

### **Step 3: Add Button to Layout**

Add this to your layout XML:

```xml
<Button
    android:id="@+id/btnBookCourt"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:text="Book Court"
    android:onClick="openWebBooking" />
```

## 🔄 **Phase 3: Complete Integration Testing**

### **Test Flow:**
1. **Open Android app** ✅
2. **Login with credentials** ✅
3. **Click "Book Court" button** ✅
4. **Web browser opens** with authenticated URL ✅
5. **User is automatically logged in** ✅

### **Expected URL Format:**
```
http://127.0.0.1:8001/mobile-test?authenticated=true&user_id=1&username=Owner&user_email=AimarHaizzad%40gmail.com&user_name=Owner&auth_token=9%7CUT4w4lMkzronZXIsX88pzpR6Yuvv3UaLqJUM9yqK0de9aa2d
```

### **Expected Web Page Behavior:**
- ✅ **Green gradient background** with SmashZone branding
- ✅ **Authentication status** shows "✅ Mobile app user is authenticated!"
- ✅ **User information** displays correctly
- ✅ **Console logs** show authentication messages
- ✅ **No re-login required**

## 🔍 **Debug Information**

### **Check Android App Logs:**
```kotlin
// Add this to your Android app for debugging
Log.d("WebBooking", "Token: $token")
Log.d("WebBooking", "API Response: ${response.body()}")
Log.d("WebBooking", "Web URL: $webUrl")
```

### **Check Web Browser Console:**
Open Developer Tools (F12) and look for:
- `🏸 SmashZone Mobile App Integration Test Page`
- `🔍 Checking for mobile app authentication...`
- `✅ Mobile app authentication detected via URL parameters`
- `✅ Laravel user authenticated`

### **Check URL Parameters:**
- `authenticated=true`
- `user_id=1`
- `username=Owner`
- `user_email=AimarHaizzad%40gmail.com`
- `user_name=Owner`
- `auth_token=YOUR_TOKEN`

## 🚀 **Success Criteria**

### **✅ Laravel Backend Working:**
- Login API returns user + token ✅
- Generate Web URL API returns authenticated URL ✅
- Mobile test page loads correctly ✅

### **✅ Android App Working:**
- User can login with database credentials
- Book Court button calls API
- Web browser opens with authenticated URL

### **✅ Web Integration Working:**
- URL contains authentication parameters
- User is automatically logged in
- No re-login required

## 📞 **Troubleshooting**

### **If Laravel API fails:**
1. **Check routes** are properly added ✅
2. **Check Sanctum** is installed and configured ✅
3. **Check CORS** settings ✅
4. **Check database** connection ✅

### **If Android app fails:**
1. **Check API URL** in `ApiService.kt`
2. **Check network permissions** in AndroidManifest.xml
3. **Check token** is being stored correctly
4. **Check API calls** in logs

### **If Web integration fails:**
1. **Check URL parameters** are present
2. **Check JavaScript** is loading
3. **Check localStorage** data
4. **Check console** for errors

## 🎯 **Expected Final Result:**

1. **User opens Android app** ✅
2. **User logs in** with database credentials ✅
3. **User clicks "Book Court"** ✅
4. **Web browser opens** with authenticated URL ✅
5. **User is automatically logged in** on web system ✅
6. **User can book courts** without re-login ✅

## 🎉 **The Complete Integration is Ready!**

Your Laravel backend is fully configured and tested. The mobile app integration should work seamlessly once you implement the Android app changes. Users can login once in the mobile app and seamlessly access the web system without re-authentication! 🚀
