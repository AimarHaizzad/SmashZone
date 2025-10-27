# 📱 Android Studio Push Notifications Setup Guide

## 🎯 Complete Android Configuration for SmashZone Push Notifications

**Time Required:** 10 minutes  
**Prerequisites:** Android Studio project with Firebase already configured

---

## ✅ Step 1: Verify Firebase Configuration

### Check if Firebase is already configured:

1. **Open Android Studio**
2. **Navigate to:** `app/src/main/java/com/smashzone/app/`
3. **Check if these files exist:**
   - `google-services.json` (in `app/` directory)
   - `FirebaseMessagingService.java` or `.kt`
   - Firebase dependencies in `build.gradle`

### If Firebase is NOT configured:

**Option A: Use Firebase Assistant (Recommended)**
1. In Android Studio: **Tools → Firebase**
2. **Cloud Messaging → Set up Firebase Cloud Messaging**
3. **Add FCM to your app**
4. Follow the wizard steps

**Option B: Manual Setup**
1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Create/select your project
3. **Add Android app** with package name: `com.smashzone.app`
4. Download `google-services.json`
5. Place it in `app/` directory

---

## ✅ Step 2: Add Firebase Dependencies

### Edit: `app/build.gradle` (Module: app)

Add these dependencies:

```gradle
dependencies {
    // ... existing dependencies ...
    
    // Firebase BOM (Bill of Materials)
    implementation platform('com.google.firebase:firebase-bom:32.7.0')
    
    // Firebase Cloud Messaging
    implementation 'com.google.firebase:firebase-messaging'
    
    // Firebase Analytics (optional)
    implementation 'com.google.firebase:firebase-analytics'
    
    // ... other dependencies ...
}

// Add this at the bottom of the file
apply plugin: 'com.google.gms.google-services'
```

### Edit: `build.gradle` (Project level)

Add Google Services plugin:

```gradle
buildscript {
    dependencies {
        // ... existing dependencies ...
        classpath 'com.google.gms:google-services:4.4.0'
    }
}
```

---

## ✅ Step 3: Create Firebase Messaging Service

### Create file: `app/src/main/java/com/smashzone/app/FirebaseMessagingService.java`

```java
package com.smashzone.app;

import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.Build;
import android.util.Log;

import androidx.core.app.NotificationCompat;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

public class FirebaseMessagingService extends FirebaseMessagingService {

    private static final String TAG = "FCMService";
    private static final String CHANNEL_ID = "smashzone_notifications";

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        Log.d(TAG, "From: " + remoteMessage.getFrom());

        // Check if message contains a data payload
        if (remoteMessage.getData().size() > 0) {
            Log.d(TAG, "Message data payload: " + remoteMessage.getData());
        }

        // Check if message contains a notification payload
        if (remoteMessage.getNotification() != null) {
            Log.d(TAG, "Message Notification Body: " + remoteMessage.getNotification().getBody());
            
            // Send notification
            sendNotification(
                remoteMessage.getNotification().getTitle(),
                remoteMessage.getNotification().getBody(),
                remoteMessage.getData()
            );
        }
    }

    @Override
    public void onNewToken(String token) {
        Log.d(TAG, "Refreshed token: " + token);
        
        // Send token to your Laravel backend
        sendTokenToServer(token);
    }

    private void sendNotification(String title, String messageBody, java.util.Map<String, String> data) {
        Intent intent = new Intent(this, MainActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        
        // Add data to intent
        for (java.util.Map.Entry<String, String> entry : data.entrySet()) {
            intent.putExtra(entry.getKey(), entry.getValue());
        }
        
        PendingIntent pendingIntent = PendingIntent.getActivity(
            this, 0, intent, PendingIntent.FLAG_ONE_SHOT | PendingIntent.FLAG_IMMUTABLE
        );

        Uri defaultSoundUri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);
        
        NotificationCompat.Builder notificationBuilder =
            new NotificationCompat.Builder(this, CHANNEL_ID)
                .setSmallIcon(R.drawable.ic_notification) // Add your notification icon
                .setContentTitle(title)
                .setContentText(messageBody)
                .setAutoCancel(true)
                .setSound(defaultSoundUri)
                .setContentIntent(pendingIntent);

        NotificationManager notificationManager =
            (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

        // Create notification channel for Android 8.0+
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationChannel channel = new NotificationChannel(
                CHANNEL_ID,
                "SmashZone Notifications",
                NotificationManager.IMPORTANCE_DEFAULT
            );
            notificationManager.createNotificationChannel(channel);
        }

        notificationManager.notify(0, notificationBuilder.build());
    }

    private void sendTokenToServer(String token) {
        // This will be implemented in your ApiService
        // For now, just log it
        Log.d(TAG, "Token to send to server: " + token);
        
        // TODO: Call your Laravel API to store the token
        // ApiService.getInstance().storeFCMToken(token);
    }
}
```

---

## ✅ Step 4: Update AndroidManifest.xml

### Edit: `app/src/main/AndroidManifest.xml`

Add these permissions and service declarations:

```xml
<manifest xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:tools="http://schemas.android.com/tools">

    <!-- Add these permissions -->
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.WAKE_LOCK" />
    <uses-permission android:name="android.permission.VIBRATE" />
    <uses-permission android:name="android.permission.POST_NOTIFICATIONS" />

    <application
        android:allowBackup="true"
        android:dataExtractionRules="@xml/data_extraction_rules"
        android:fullBackupContent="@xml/backup_rules"
        android:icon="@mipmap/ic_launcher"
        android:label="@string/app_name"
        android:roundIcon="@mipmap/ic_launcher_round"
        android:supportsRtl="true"
        android:theme="@style/Theme.SmashZone"
        tools:targetApi="31">

        <!-- Add Firebase Messaging Service -->
        <service
            android:name=".FirebaseMessagingService"
            android:exported="false">
            <intent-filter>
                <action android:name="com.google.firebase.MESSAGING_EVENT" />
            </intent-filter>
        </service>

        <!-- Add Firebase default notification channel -->
        <meta-data
            android:name="com.google.firebase.messaging.default_notification_channel_id"
            android:value="smashzone_notifications" />

        <!-- Add Firebase default notification icon -->
        <meta-data
            android:name="com.google.firebase.messaging.default_notification_icon"
            android:resource="@drawable/ic_notification" />

        <!-- Add Firebase default notification color -->
        <meta-data
            android:name="com.google.firebase.messaging.default_notification_color"
            android:resource="@color/colorPrimary" />

        <!-- Your existing activities -->
        <activity
            android:name=".MainActivity"
            android:exported="true">
            <intent-filter>
                <action android:name="android.intent.action.MAIN" />
                <category android:name="android.intent.category.LAUNCHER" />
            </intent-filter>
        </activity>

    </application>

</manifest>
```

---

## ✅ Step 5: Add Notification Icon

### Create notification icon: `app/src/main/res/drawable/ic_notification.xml`

```xml
<vector xmlns:android="http://schemas.android.com/apk/res/android"
    android:width="24dp"
    android:height="24dp"
    android:viewportWidth="24"
    android:viewportHeight="24"
    android:tint="?attr/colorOnPrimary">
    <path
        android:fillColor="@android:color/white"
        android:pathData="M12,2C6.48,2 2,6.48 2,12s4.48,10 10,10 10,-4.48 10,-10S17.52,2 12,2zM13,17h-2v-6h2v6zM13,9h-2L11,7h2v2z"/>
</vector>
```

---

## ✅ Step 6: Update ApiService for FCM Token

### Edit: `app/src/main/java/com/smashzone/app/ApiService.java`

Add FCM token methods:

```java
// Add these methods to your existing ApiService class

public void storeFCMToken(String fcmToken) {
    String url = BASE_URL + "/api/fcm-token";
    
    JSONObject jsonObject = new JSONObject();
    try {
        jsonObject.put("fcm_token", fcmToken);
    } catch (JSONException e) {
        e.printStackTrace();
    }
    
    JsonObjectRequest request = new JsonObjectRequest(
        Request.Method.POST, url, jsonObject,
        response -> {
            Log.d("ApiService", "FCM token stored successfully");
        },
        error -> {
            Log.e("ApiService", "Failed to store FCM token: " + error.getMessage());
        }
    ) {
        @Override
        public Map<String, String> getHeaders() throws AuthFailureError {
            Map<String, String> headers = new HashMap<>();
            String token = getStoredToken();
            if (token != null) {
                headers.put("Authorization", "Bearer " + token);
            }
            headers.put("Content-Type", "application/json");
            headers.put("Accept", "application/json");
            return headers;
        }
    };
    
    requestQueue.add(request);
}

public void deleteFCMToken() {
    String url = BASE_URL + "/api/fcm-token";
    
    JsonObjectRequest request = new JsonObjectRequest(
        Request.Method.DELETE, url, null,
        response -> {
            Log.d("ApiService", "FCM token deleted successfully");
        },
        error -> {
            Log.e("ApiService", "Failed to delete FCM token: " + error.getMessage());
        }
    ) {
        @Override
        public Map<String, String> getHeaders() throws AuthFailureError {
            Map<String, String> headers = new HashMap<>();
            String token = getStoredToken();
            if (token != null) {
                headers.put("Authorization", "Bearer " + token);
            }
            headers.put("Content-Type", "application/json");
            headers.put("Accept", "application/json");
            return headers;
        }
    };
    
    requestQueue.add(request);
}

public void getNotifications(Callback callback) {
    String url = BASE_URL + "/api/notifications";
    
    JsonObjectRequest request = new JsonObjectRequest(
        Request.Method.GET, url, null,
        response -> callback.onSuccess(response),
        error -> callback.onError(error.getMessage())
    ) {
        @Override
        public Map<String, String> getHeaders() throws AuthFailureError {
            Map<String, String> headers = new HashMap<>();
            String token = getStoredToken();
            if (token != null) {
                headers.put("Authorization", "Bearer " + token);
            }
            headers.put("Accept", "application/json");
            return headers;
        }
    };
    
    requestQueue.add(request);
}

public void getUnreadNotificationCount(Callback callback) {
    String url = BASE_URL + "/api/notifications/unread-count";
    
    JsonObjectRequest request = new JsonObjectRequest(
        Request.Method.GET, url, null,
        response -> callback.onSuccess(response),
        error -> callback.onError(error.getMessage())
    ) {
        @Override
        public Map<String, String> getHeaders() throws AuthFailureError {
            Map<String, String> headers = new HashMap<>();
            String token = getStoredToken();
            if (token != null) {
                headers.put("Authorization", "Bearer " + token);
            }
            headers.put("Accept", "application/json");
            return headers;
        }
    };
    
    requestQueue.add(request);
}
```

---

## ✅ Step 7: Update MainActivity

### Edit: `app/src/main/java/com/smashzone/app/MainActivity.java`

Add FCM token handling:

```java
// Add these imports
import com.google.firebase.messaging.FirebaseMessaging;
import com.google.firebase.messaging.FirebaseMessagingService;

// Add these methods to your MainActivity class

@Override
protected void onCreate(Bundle savedInstanceState) {
    super.onCreate(savedInstanceState);
    setContentView(R.layout.activity_main);
    
    // ... existing code ...
    
    // Initialize FCM
    initializeFCM();
}

private void initializeFCM() {
    // Get FCM token
    FirebaseMessaging.getInstance().getToken()
        .addOnCompleteListener(task -> {
            if (!task.isSuccessful()) {
                Log.w("MainActivity", "Fetching FCM registration token failed", task.getException());
                return;
            }

            // Get new FCM registration token
            String token = task.getResult();
            Log.d("MainActivity", "FCM Token: " + token);
            
            // Store token in SharedPreferences
            SharedPreferences prefs = getSharedPreferences("SmashZonePrefs", MODE_PRIVATE);
            prefs.edit().putString("fcm_token", token).apply();
            
            // Send token to server if user is logged in
            if (isUserLoggedIn()) {
                ApiService.getInstance().storeFCMToken(token);
            }
        });
}

private boolean isUserLoggedIn() {
    SharedPreferences prefs = getSharedPreferences("SmashZonePrefs", MODE_PRIVATE);
    return prefs.getString("auth_token", null) != null;
}

@Override
protected void onDestroy() {
    super.onDestroy();
    
    // Delete FCM token on logout
    if (!isUserLoggedIn()) {
        ApiService.getInstance().deleteFCMToken();
    }
}
```

---

## ✅ Step 8: Handle Notification Clicks

### Update MainActivity to handle notification data:

```java
@Override
protected void onCreate(Bundle savedInstanceState) {
    super.onCreate(savedInstanceState);
    setContentView(R.layout.activity_main);
    
    // ... existing code ...
    
    // Handle notification click
    handleNotificationClick();
}

private void handleNotificationClick() {
    Intent intent = getIntent();
    if (intent != null && intent.getExtras() != null) {
        Bundle extras = intent.getExtras();
        
        // Check if this is from a notification
        if (extras.containsKey("type")) {
            String type = extras.getString("type");
            String bookingId = extras.getString("booking_id");
            String courtName = extras.getString("court_name");
            
            Log.d("MainActivity", "Notification clicked - Type: " + type);
            
            // Handle different notification types
            switch (type) {
                case "booking_confirmed":
                    // Navigate to booking details
                    if (bookingId != null) {
                        // Navigate to booking details page
                        Log.d("MainActivity", "Navigate to booking: " + bookingId);
                    }
                    break;
                    
                case "payment_received":
                    // Navigate to payments page
                    Log.d("MainActivity", "Navigate to payments");
                    break;
                    
                case "booking_reminder":
                    // Navigate to booking details
                    if (bookingId != null) {
                        Log.d("MainActivity", "Navigate to booking reminder: " + bookingId);
                    }
                    break;
                    
                default:
                    // Navigate to dashboard
                    Log.d("MainActivity", "Navigate to dashboard");
                    break;
            }
        }
    }
}
```

---

## ✅ Step 9: Test Push Notifications

### Build and Run the App:

1. **Sync Gradle** - Click "Sync Now" if prompted
2. **Build Project** - Build → Clean Project → Rebuild Project
3. **Run on Device** - Connect Android device and run
4. **Check Logs** - Look for FCM token in Logcat

### Test from Laravel:

1. **Login to your app**
2. **Visit:** `http://10.62.93.132:8000/test-notification`
3. **Check your phone** - You should receive a notification!

---

## 🔍 Verification Checklist

After completing all steps:

- [ ] Firebase dependencies added to `build.gradle`
- [ ] `google-services.json` file in `app/` directory
- [ ] `FirebaseMessagingService.java` created
- [ ] Permissions added to `AndroidManifest.xml`
- [ ] Service declared in `AndroidManifest.xml`
- [ ] Notification icon created
- [ ] FCM token methods added to `ApiService`
- [ ] Token handling added to `MainActivity`
- [ ] App builds without errors
- [ ] FCM token appears in Logcat
- [ ] Test notification received

---

## 🧪 Testing Commands

### Check FCM token in Logcat:

```bash
# Filter logs for FCM
adb logcat | grep -E "(FCM|Firebase|Token)"
```

### Test notification from Laravel:

```bash
# Visit test route
curl http://10.62.93.132:8000/test-notification
```

---

## 🐛 Troubleshooting

### Issue: "FCM token not appearing in logs"

**Solutions:**
1. Check if `google-services.json` is in correct location
2. Verify Firebase dependencies are added
3. Check if app has internet permission
4. Ensure Firebase project is properly configured

### Issue: "Notifications not received"

**Solutions:**
1. Check if FCM token is being sent to Laravel
2. Verify Firebase Server Key in Laravel `.env`
3. Check Laravel logs: `storage/logs/laravel.log`
4. Ensure device is not in Do Not Disturb mode

### Issue: "App crashes on notification"

**Solutions:**
1. Check if notification icon exists
2. Verify notification channel is created
3. Check AndroidManifest.xml permissions
4. Look for errors in Logcat

---

## 📱 Expected Behavior

After setup:

1. **App Launch** - FCM token generated and logged
2. **User Login** - Token sent to Laravel backend
3. **Notification Received** - Notification appears in status bar
4. **Notification Click** - App opens and handles notification data
5. **User Logout** - FCM token deleted from backend

---

## 🎯 Quick Summary

**What you need to do in Android Studio:**

1. ✅ **Add Firebase dependencies** to `build.gradle`
2. ✅ **Create `FirebaseMessagingService.java`**
3. ✅ **Update `AndroidManifest.xml`** with permissions and service
4. ✅ **Add notification icon** (`ic_notification.xml`)
5. ✅ **Update `ApiService.java`** with FCM token methods
6. ✅ **Update `MainActivity.java`** to handle FCM tokens
7. ✅ **Build and test** the app

**Total Android setup time: ~10 minutes**

---

**Once completed, your Android app will receive push notifications from Laravel! 🔔📱**
