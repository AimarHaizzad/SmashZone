# 📱 Android Studio: Shipping Notifications Setup Guide

## ✅ Backend is Ready!

The backend now automatically sends FCM notifications when shipping status changes to:
- **"Out for Delivery"** - When order is shipped and on the way
- **"Delivered"** - When order is successfully delivered

---

## 🔧 What You Need to Do in Android Studio

### Step 1: Update FirebaseMessagingService.java

Add handling for shipping update notifications in your `onMessageReceived` method:

```java
@Override
public void onMessageReceived(RemoteMessage remoteMessage) {
    Log.d(TAG, "From: " + remoteMessage.getFrom());

    // Check if message contains a data payload
    if (remoteMessage.getData().size() > 0) {
        Log.d(TAG, "Message data payload: " + remoteMessage.getData());
        handleNotificationData(remoteMessage.getData());
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

private void handleNotificationData(Map<String, String> data) {
    String type = data.get("type");
    
    if (type == null) {
        Log.w(TAG, "Notification type is null");
        return;
    }

    Log.d(TAG, "Handling notification type: " + type);

    switch (type) {
        case "shipping_update":
            handleShippingUpdate(data);
            break;
        // ... other cases
    }
}
```

---

### Step 2: Add Shipping Update Handler

Add this method to handle shipping updates:

```java
/**
 * Handle shipping update notification
 */
private void handleShippingUpdate(Map<String, String> data) {
    try {
        String orderId = data.get("order_id");
        String orderNumber = data.get("order_number");
        String status = data.get("status");
        String trackingNumber = data.get("tracking_number");
        String estimatedDelivery = data.get("estimated_delivery");
        
        Log.d(TAG, "Shipping updated - Order: " + orderNumber + ", Status: " + status);
        
        // Update local database
        updateShippingInfo(orderId, status, trackingNumber, estimatedDelivery);
        
        // Refresh orders list from server
        refreshOrdersList();
        
        // Show in-app notification or update UI
        showShippingUpdateNotification(orderNumber, status, trackingNumber);
        
        // Broadcast to update UI
        Intent intent = new Intent("com.smashzone.SHIPPING_UPDATED");
        intent.putExtra("order_id", orderId);
        intent.putExtra("status", status);
        intent.putExtra("tracking_number", trackingNumber);
        sendBroadcast(intent);
        
    } catch (Exception e) {
        Log.e(TAG, "Error handling shipping update: " + e.getMessage());
    }
}
```

---

### Step 3: Update Local Database

Add method to update shipping info in your local database:

```java
/**
 * Update shipping information in local database
 */
private void updateShippingInfo(String orderId, String status, String trackingNumber, String estimatedDelivery) {
    // TODO: Update your local database (Room, SQLite, etc.)
    // Example with Room:
    /*
    AppDatabase db = AppDatabase.getInstance(this);
    OrderDao orderDao = db.orderDao();
    
    Order order = orderDao.getById(Long.parseLong(orderId));
    if (order != null) {
        Shipping shipping = order.getShipping();
        if (shipping == null) {
            shipping = new Shipping();
            shipping.setOrderId(order.getId());
        }
        
        shipping.setStatus(status);
        shipping.setTrackingNumber(trackingNumber);
        if (estimatedDelivery != null) {
            shipping.setEstimatedDeliveryDate(estimatedDelivery);
        }
        
        order.setShipping(shipping);
        orderDao.update(order);
    }
    */
}
```

---

### Step 4: Refresh Orders List

Add method to refresh orders from server:

```java
/**
 * Refresh orders list from server
 */
private void refreshOrdersList() {
    // TODO: Call your API to refresh orders
    // Example:
    /*
    ApiService.getInstance().getOrders(new ApiCallback() {
        @Override
        public void onSuccess(Response response) {
            // Parse and update local database
            // Then notify UI to refresh
            Intent intent = new Intent("com.smashzone.ORDERS_REFRESHED");
            sendBroadcast(intent);
        }
        
        @Override
        public void onError(String error) {
            Log.e(TAG, "Failed to refresh orders: " + error);
        }
    });
    */
}
```

---

### Step 5: Show In-App Notification (Optional)

Add method to show a custom in-app notification or toast:

```java
/**
 * Show shipping update notification to user
 */
private void showShippingUpdateNotification(String orderNumber, String status, String trackingNumber) {
    // Option 1: Show Toast
    Handler mainHandler = new Handler(Looper.getMainLooper());
    mainHandler.post(new Runnable() {
        @Override
        public void run() {
            String message = "Order #" + orderNumber + " is " + status;
            if (trackingNumber != null && !trackingNumber.isEmpty()) {
                message += "\nTracking: " + trackingNumber;
            }
            Toast.makeText(getApplicationContext(), message, Toast.LENGTH_LONG).show();
        }
    });
    
    // Option 2: Show custom in-app notification
    // You can create a custom notification view in your app
}
```

---

### Step 6: Update MainActivity to Listen for Shipping Updates

In your `MainActivity.java`, add a broadcast receiver:

```java
public class MainActivity extends AppCompatActivity {
    
    private BroadcastReceiver shippingUpdateReceiver;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        
        // ... existing code ...
        
        // Register shipping update receiver
        registerShippingUpdateReceiver();
    }

    private void registerShippingUpdateReceiver() {
        shippingUpdateReceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String orderId = intent.getStringExtra("order_id");
                String status = intent.getStringExtra("status");
                String trackingNumber = intent.getStringExtra("tracking_number");
                
                // Refresh orders fragment
                refreshOrdersFragment();
                
                // Show notification to user
                showShippingUpdateToast(orderId, status, trackingNumber);
            }
        };

        LocalBroadcastManager.getInstance(this).registerReceiver(
            shippingUpdateReceiver,
            new IntentFilter("com.smashzone.SHIPPING_UPDATED")
        );
    }

    private void refreshOrdersFragment() {
        // TODO: Refresh your orders fragment/list
        // Example:
        // if (ordersFragment != null) {
        //     ordersFragment.refresh();
        // }
    }

    private void showShippingUpdateToast(String orderId, String status, String trackingNumber) {
        String message = "Shipping update: " + status;
        if (trackingNumber != null && !trackingNumber.isEmpty()) {
            message += "\nTrack: " + trackingNumber;
        }
        Toast.makeText(this, message, Toast.LENGTH_SHORT).show();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        
        // Unregister receiver
        if (shippingUpdateReceiver != null) {
            LocalBroadcastManager.getInstance(this).unregisterReceiver(shippingUpdateReceiver);
        }
    }
}
```

---

### Step 7: Handle Notification Tap (Deep Link)

Update the `sendNotification` method in `FirebaseMessagingService.java` to deep link to order details:

```java
private void sendNotification(String title, String messageBody, Map<String, String> data) {
    Intent intent = new Intent(this, MainActivity.class);
    
    // Add data to intent for deep linking
    String type = data.get("type");
    if ("shipping_update".equals(type)) {
        intent.putExtra("fragment", "orders");
        intent.putExtra("order_id", data.get("order_id"));
        intent.putExtra("action", "show_shipping");
    }
    
    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
    PendingIntent pendingIntent = PendingIntent.getActivity(
        this, 0, intent,
        PendingIntent.FLAG_IMMUTABLE | PendingIntent.FLAG_UPDATE_CURRENT
    );

    Uri defaultSoundUri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);
    NotificationCompat.Builder notificationBuilder = new NotificationCompat.Builder(this, CHANNEL_ID)
        .setSmallIcon(R.drawable.ic_notification)
        .setContentTitle(title)
        .setContentText(messageBody)
        .setAutoCancel(true)
        .setSound(defaultSoundUri)
        .setContentIntent(pendingIntent)
        .setPriority(NotificationCompat.PRIORITY_HIGH);

    NotificationManager notificationManager =
        (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
        NotificationChannel channel = new NotificationChannel(
            CHANNEL_ID,
            "SmashZone Notifications",
            NotificationManager.IMPORTANCE_HIGH
        );
        channel.setDescription("Notifications for bookings, orders, and shipping updates");
        notificationManager.createNotificationChannel(channel);
    }

    notificationManager.notify((int) System.currentTimeMillis(), notificationBuilder.build());
}
```

---

### Step 8: Handle Deep Link in MainActivity

In `MainActivity.java`, handle the deep link when app opens from notification:

```java
@Override
protected void onCreate(Bundle savedInstanceState) {
    super.onCreate(savedInstanceState);
    setContentView(R.layout.activity_main);
    
    // ... existing code ...
    
    // Handle notification deep link
    handleNotificationIntent(getIntent());
}

private void handleNotificationIntent(Intent intent) {
    if (intent != null && intent.hasExtra("fragment")) {
        String fragment = intent.getStringExtra("fragment");
        
        if ("orders".equals(fragment) && intent.hasExtra("order_id")) {
            String orderId = intent.getStringExtra("order_id");
            navigateToOrdersFragment();
            openOrderDetails(orderId);
        }
    }
}

private void navigateToOrdersFragment() {
    // TODO: Navigate to orders tab/fragment
    // Example: bottomNavigationView.setSelectedItemId(R.id.nav_orders);
}

private void openOrderDetails(String orderId) {
    // TODO: Open order details screen
    // Example:
    // Intent intent = new Intent(this, OrderDetailsActivity.class);
    // intent.putExtra("order_id", orderId);
    // startActivity(intent);
}
```

---

## 📋 Notification Data Structure

When shipping status changes, the backend sends this data:

### Out for Delivery:
```json
{
  "type": "shipping_update",
  "order_id": "123",
  "order_number": "ORD-20250120-ABC123",
  "status": "out_for_delivery",
  "tracking_number": "TRK-20250120-12345678",
  "estimated_delivery": "2025-01-23"
}
```

### Delivered:
```json
{
  "type": "shipping_update",
  "order_id": "123",
  "order_number": "ORD-20250120-ABC123",
  "status": "delivered",
  "tracking_number": "TRK-20250120-12345678",
  "estimated_delivery": null
}
```

---

## ✅ Checklist

- [ ] Add `shipping_update` case in `handleNotificationData()`
- [ ] Implement `handleShippingUpdate()` method
- [ ] Update local database when shipping status changes
- [ ] Refresh orders list from server
- [ ] Add broadcast receiver in MainActivity
- [ ] Handle notification tap (deep link to order details)
- [ ] Test with "Out for Delivery" status
- [ ] Test with "Delivered" status
- [ ] Verify tracking number is displayed
- [ ] Verify orders list refreshes automatically

---

## 🧪 Testing

1. **Test Out for Delivery:**
   - Update shipping status to "Out for Delivery" on web
   - Check mobile app receives notification
   - Verify orders list shows updated status
   - Verify tracking number is displayed

2. **Test Delivered:**
   - Update shipping status to "Delivered" on web
   - Check mobile app receives notification
   - Verify orders list shows "Delivered" status
   - Tap notification to open order details

---

## 📝 Summary

**What happens:**
1. Owner/Staff updates shipping status on web → "Out for Delivery" or "Delivered"
2. Backend sends FCM notification to customer's mobile app
3. Mobile app receives notification and:
   - Updates local database
   - Refreshes orders list from server
   - Shows notification to user
   - Allows user to tap notification to view order details

**You need to implement:**
- Handle `shipping_update` notification type
- Update local database
- Refresh orders list
- Show notification/update UI
- Handle deep links

That's it! The backend is already sending the notifications. You just need to handle them in your Android app! 🚀

