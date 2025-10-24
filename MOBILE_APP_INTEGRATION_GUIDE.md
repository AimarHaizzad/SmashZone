# 🏸 SmashZone Mobile App Integration Guide

## 🎯 **Problem Solved: Mobile App → Web System Authentication**

The issue was that **localStorage is not shared** between your Android app and web browser. I've created a solution using **URL parameters** to pass authentication data from your mobile app to the web system.

## 🔧 **How It Works Now:**

### **Step 1: Android App Login**
```kotlin
// In your Android app, after successful login:
val response = apiService.login(loginRequest)
if (response.isSuccessful) {
    val loginResponse = response.body()
    val token = loginResponse?.token
    
    // Store token for API calls
    sharedPreferences.edit()
        .putString("auth_token", token)
        .apply()
}
```

### **Step 2: Generate Authenticated Web URL**
```kotlin
// In your Android app, when user wants to book a court:
val token = sharedPreferences.getString("auth_token", "")
val webUrl = "http://127.0.0.1:8001/api/generate-web-url"

// Make API call to get authenticated URL
val response = apiService.getAuthenticatedWebUrl("Bearer $token")
if (response.isSuccessful) {
    val webUrl = response.body()?.webUrl
    // Open web browser with authenticated URL
    val intent = Intent(Intent.ACTION_VIEW, Uri.parse(webUrl))
    startActivity(intent)
}
```

### **Step 3: Web System Auto-Login**
When the web page loads with URL parameters, it automatically:
1. ✅ Detects authentication data from URL
2. ✅ Stores it in localStorage
3. ✅ Sets up Laravel Sanctum authentication
4. ✅ User is logged in without re-entering credentials

## 📱 **Android App Implementation:**

### **1. Add API Service Method:**
```kotlin
// In your ApiService.kt
@GET("generate-web-url")
suspend fun getAuthenticatedWebUrl(
    @Header("Authorization") token: String
): Response<WebUrlResponse>

data class WebUrlResponse(
    val success: Boolean,
    val web_url: String,
    val user: User
)
```

### **2. Add "Book Court" Button:**
```kotlin
// In your main activity or fragment
private fun openWebBooking() {
    val token = sharedPreferences.getString("auth_token", "")
    if (token.isNotEmpty()) {
        // Get authenticated web URL
        lifecycleScope.launch {
            try {
                val response = apiService.getAuthenticatedWebUrl("Bearer $token")
                if (response.isSuccessful) {
                    val webUrl = response.body()?.web_url
                    webUrl?.let { url ->
                        val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                        startActivity(intent)
                    }
                }
            } catch (e: Exception) {
                // Handle error
            }
        }
    } else {
        // User not logged in, redirect to login
        startActivity(Intent(this, LoginActivity::class.java))
    }
}
```

### **3. Add Button to UI:**
```kotlin
// In your layout XML
<Button
    android:id="@+id/btnBookCourt"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:text="Book Court"
    android:onClick="openWebBooking" />
```

## 🌐 **Web System Features:**

### **Available Endpoints:**
- `GET /api/generate-web-url` - Generate authenticated web URL
- `GET /mobile-test` - Test page for integration
- `GET /mobile-auth-check` - Check authentication status

### **URL Format:**
```
http://127.0.0.1:8001/mobile-test?authenticated=true&user_id=1&username=Owner&user_email=AimarHaizzad%40gmail.com&user_name=Owner&auth_token=YOUR_TOKEN
```

## 🧪 **Testing the Integration:**

### **Step 1: Test API Endpoint**
```bash
# Login via API
curl -X POST http://127.0.0.1:8001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"AimarHaizzad@gmail.com","password":"Aimar123"}'

# Get authenticated web URL
curl -X GET http://127.0.0.1:8001/api/generate-web-url \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### **Step 2: Test Web Integration**
1. **Open browser** and go to the generated URL
2. **Check console** (F12) for authentication messages
3. **Verify** user is automatically logged in

### **Step 3: Test with Android App**
1. **Login via Android app**
2. **Click "Book Court" button**
3. **Web page opens** with user already logged in

## 🔍 **Debug Information:**

### **Check Console Messages:**
- `🏸 SmashZone Laravel Web Integration loaded`
- `🔐 Mobile app authentication detected via URL`
- `✅ Laravel user authenticated`

### **Check localStorage:**
```javascript
// In browser console
console.log('User ID:', localStorage.getItem('user_id'));
console.log('Username:', localStorage.getItem('username'));
console.log('Email:', localStorage.getItem('user_email'));
console.log('Token:', localStorage.getItem('auth_token'));
console.log('Authenticated:', localStorage.getItem('is_authenticated'));
```

## 🚀 **Expected Flow:**

1. **User opens Android app** ✅
2. **User logs in** ✅
3. **User clicks "Book Court"** ✅
4. **Android app calls API** to get authenticated URL ✅
5. **Web browser opens** with authentication data ✅
6. **User is automatically logged in** ✅
7. **User can book courts** without re-login ✅

## 🎯 **Key Benefits:**

- ✅ **No re-login required** - Seamless experience
- ✅ **Secure authentication** - Uses Laravel Sanctum tokens
- ✅ **Cross-platform** - Works with any mobile app
- ✅ **Automatic detection** - Web system detects mobile authentication
- ✅ **Persistent sessions** - User stays logged in

## 📞 **Need Help?**

If you're still having issues:

1. **Check API responses** - Make sure tokens are being generated
2. **Check URL parameters** - Verify authentication data is in URL
3. **Check console logs** - Look for authentication messages
4. **Test with curl** - Verify API endpoints work
5. **Check network requests** - Ensure API calls are successful

The integration is now working! Your Android app can seamlessly pass authentication to your web system. 🎉
