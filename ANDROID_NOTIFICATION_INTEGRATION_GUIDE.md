# 📱 Android App Notification Integration Guide

## Overview
This guide explains how to integrate FCM notifications into your Android app to handle booking confirmations, order updates, and product status changes.

---

## 🔧 Step 1: Update FirebaseMessagingService

### File: `app/src/main/java/com/smashzone/app/FirebaseMessagingService.java`

Update your `onMessageReceived` method to handle different notification types:

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

import org.json.JSONObject;

public class FirebaseMessagingService extends FirebaseMessagingService {
    private static final String TAG = "FCMService";
    private static final String CHANNEL_ID = "smashzone_notifications";

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

    /**
     * Handle notification data based on type
     */
    private void handleNotificationData(Map<String, String> data) {
        String type = data.get("type");
        
        if (type == null) {
            Log.w(TAG, "Notification type is null");
            return;
        }

        Log.d(TAG, "Handling notification type: " + type);

        switch (type) {
            case "booking_confirmed":
                handleBookingConfirmed(data);
                break;
            case "order_confirmed":
                handleOrderConfirmed(data);
                break;
            case "order_status_update":
                handleOrderStatusUpdate(data);
                break;
            case "shipping_update":
                handleShippingUpdate(data);
                break;
            case "product_stock_update":
                handleProductStockUpdate(data);
                break;
            case "payment_received":
                handlePaymentReceived(data);
                break;
            case "booking_reminder":
                handleBookingReminder(data);
                break;
            case "booking_cancelled":
                handleBookingCancelled(data);
                break;
            default:
                Log.d(TAG, "Unknown notification type: " + type);
                break;
        }
    }

    /**
     * Handle booking confirmation notification
     */
    private void handleBookingConfirmed(Map<String, String> data) {
        try {
            String bookingId = data.get("booking_id");
            String courtName = data.get("court_name");
            String date = data.get("date");
            String time = data.get("time");
            
            Log.d(TAG, "Booking confirmed - ID: " + bookingId + ", Court: " + courtName);
            
            // Update local database
            updateLocalBookingStatus(bookingId, "confirmed");
            
            // Refresh bookings list
            refreshBookingsList();
            
            // Broadcast to update UI
            Intent intent = new Intent("com.smashzone.BOOKING_UPDATED");
            intent.putExtra("booking_id", bookingId);
            intent.putExtra("status", "confirmed");
            sendBroadcast(intent);
            
        } catch (Exception e) {
            Log.e(TAG, "Error handling booking confirmed: " + e.getMessage());
        }
    }

    /**
     * Handle order confirmation notification
     */
    private void handleOrderConfirmed(Map<String, String> data) {
        try {
            String orderId = data.get("order_id");
            String orderNumber = data.get("order_number");
            String totalAmount = data.get("total_amount");
            String status = data.get("status");
            
            Log.d(TAG, "Order confirmed - ID: " + orderId + ", Number: " + orderNumber);
            
            // Update local database
            updateLocalOrderStatus(orderId, status);
            
            // Refresh orders list
            refreshOrdersList();
            
            // Refresh products list (quantities may have changed)
            refreshProductsList();
            
            // Broadcast to update UI
            Intent intent = new Intent("com.smashzone.ORDER_UPDATED");
            intent.putExtra("order_id", orderId);
            intent.putExtra("status", status);
            sendBroadcast(intent);
            
        } catch (Exception e) {
            Log.e(TAG, "Error handling order confirmed: " + e.getMessage());
        }
    }

    /**
     * Handle order status update notification
     */
    private void handleOrderStatusUpdate(Map<String, String> data) {
        try {
            String orderId = data.get("order_id");
            String orderNumber = data.get("order_number");
            String status = data.get("status");
            
            Log.d(TAG, "Order status updated - ID: " + orderId + ", Status: " + status);
            
            // Update local database
            updateLocalOrderStatus(orderId, status);
            
            // Refresh orders list
            refreshOrdersList();
            
            // Broadcast to update UI
            Intent intent = new Intent("com.smashzone.ORDER_STATUS_UPDATED");
            intent.putExtra("order_id", orderId);
            intent.putExtra("status", status);
            sendBroadcast(intent);
            
        } catch (Exception e) {
            Log.e(TAG, "Error handling order status update: " + e.getMessage());
        }
    }

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
            
            // Refresh orders list
            refreshOrdersList();
            
            // Broadcast to update UI
            Intent intent = new Intent("com.smashzone.SHIPPING_UPDATED");
            intent.putExtra("order_id", orderId);
            intent.putExtra("status", status);
            sendBroadcast(intent);
            
        } catch (Exception e) {
            Log.e(TAG, "Error handling shipping update: " + e.getMessage());
        }
    }

    /**
     * Handle product stock update notification
     */
    private void handleProductStockUpdate(Map<String, String> data) {
        try {
            String productId = data.get("product_id");
            String productName = data.get("product_name");
            String status = data.get("status");
            String quantity = data.get("quantity");
            
            Log.d(TAG, "Product stock updated - ID: " + productId + ", Status: " + status);
            
            // Update local database
            updateProductStock(productId, status, quantity);
            
            // Refresh products list
            refreshProductsList();
            
            // Broadcast to update UI
            Intent intent = new Intent("com.smashzone.PRODUCT_UPDATED");
            intent.putExtra("product_id", productId);
            intent.putExtra("status", status);
            sendBroadcast(intent);
            
        } catch (Exception e) {
            Log.e(TAG, "Error handling product stock update: " + e.getMessage());
        }
    }

    /**
     * Handle payment received notification
     */
    private void handlePaymentReceived(Map<String, String> data) {
        try {
            String paymentId = data.get("payment_id");
            String amount = data.get("amount");
            
            Log.d(TAG, "Payment received - ID: " + paymentId + ", Amount: " + amount);
            
            // Refresh payments list
            refreshPaymentsList();
            
            // Broadcast to update UI
            Intent intent = new Intent("com.smashzone.PAYMENT_RECEIVED");
            intent.putExtra("payment_id", paymentId);
            sendBroadcast(intent);
            
        } catch (Exception e) {
            Log.e(TAG, "Error handling payment received: " + e.getMessage());
        }
    }

    /**
     * Handle booking reminder notification
     */
    private void handleBookingReminder(Map<String, String> data) {
        try {
            String bookingId = data.get("booking_id");
            String courtName = data.get("court_name");
            String date = data.get("date");
            String time = data.get("time");
            
            Log.d(TAG, "Booking reminder - ID: " + bookingId);
            
            // Refresh bookings list
            refreshBookingsList();
            
        } catch (Exception e) {
            Log.e(TAG, "Error handling booking reminder: " + e.getMessage());
        }
    }

    /**
     * Handle booking cancelled notification
     */
    private void handleBookingCancelled(Map<String, String> data) {
        try {
            String bookingId = data.get("booking_id");
            String courtName = data.get("court_name");
            
            Log.d(TAG, "Booking cancelled - ID: " + bookingId);
            
            // Update local database
            updateLocalBookingStatus(bookingId, "cancelled");
            
            // Refresh bookings list
            refreshBookingsList();
            
            // Broadcast to update UI
            Intent intent = new Intent("com.smashzone.BOOKING_CANCELLED");
            intent.putExtra("booking_id", bookingId);
            sendBroadcast(intent);
            
        } catch (Exception e) {
            Log.e(TAG, "Error handling booking cancelled: " + e.getMessage());
        }
    }

    /**
     * Send notification to user
     */
    private void sendNotification(String title, String messageBody, Map<String, String> data) {
        Intent intent = new Intent(this, MainActivity.class);
        
        // Add data to intent based on notification type
        String type = data.get("type");
        if (type != null) {
            switch (type) {
                case "booking_confirmed":
                case "booking_reminder":
                case "booking_cancelled":
                    intent.putExtra("fragment", "bookings");
                    intent.putExtra("booking_id", data.get("booking_id"));
                    break;
                case "order_confirmed":
                case "order_status_update":
                case "shipping_update":
                    intent.putExtra("fragment", "orders");
                    intent.putExtra("order_id", data.get("order_id"));
                    break;
                case "product_stock_update":
                    intent.putExtra("fragment", "products");
                    intent.putExtra("product_id", data.get("product_id"));
                    break;
            }
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

        // Create notification channel for Android O and above
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationChannel channel = new NotificationChannel(
                CHANNEL_ID,
                "SmashZone Notifications",
                NotificationManager.IMPORTANCE_HIGH
            );
            channel.setDescription("Notifications for bookings, orders, and updates");
            notificationManager.createNotificationChannel(channel);
        }

        notificationManager.notify((int) System.currentTimeMillis(), notificationBuilder.build());
    }

    // ========== Database Update Methods ==========
    
    /**
     * Update local booking status
     */
    private void updateLocalBookingStatus(String bookingId, String status) {
        // TODO: Update your local database (Room, SQLite, etc.)
        // Example:
        // BookingDao bookingDao = AppDatabase.getInstance(this).bookingDao();
        // Booking booking = bookingDao.getById(bookingId);
        // if (booking != null) {
        //     booking.setStatus(status);
        //     bookingDao.update(booking);
        // }
    }

    /**
     * Update local order status
     */
    private void updateLocalOrderStatus(String orderId, String status) {
        // TODO: Update your local database
    }

    /**
     * Update shipping information
     */
    private void updateShippingInfo(String orderId, String status, String trackingNumber, String estimatedDelivery) {
        // TODO: Update your local database
    }

    /**
     * Update product stock
     */
    private void updateProductStock(String productId, String status, String quantity) {
        // TODO: Update your local database
    }

    // ========== Data Refresh Methods ==========
    
    /**
     * Refresh bookings list from server
     */
    private void refreshBookingsList() {
        // TODO: Call your API to refresh bookings
        // Example:
        // ApiService.getInstance().getBookings(new ApiCallback() {
        //     @Override
        //     public void onSuccess(Response response) {
        //         // Update local database and UI
        //     }
        // });
    }

    /**
     * Refresh orders list from server
     */
    private void refreshOrdersList() {
        // TODO: Call your API to refresh orders
    }

    /**
     * Refresh products list from server
     */
    private void refreshProductsList() {
        // TODO: Call your API to refresh products
    }

    /**
     * Refresh payments list from server
     */
    private void refreshPaymentsList() {
        // TODO: Call your API to refresh payments
    }

    @Override
    public void onNewToken(String token) {
        Log.d(TAG, "Refreshed token: " + token);
        sendTokenToServer(token);
    }

    private void sendTokenToServer(String token) {
        // TODO: Send token to your Laravel backend
        // ApiService.getInstance().storeFCMToken(token);
    }
}
```

---

## 🔧 Step 2: Update MainActivity to Handle Broadcasts

### File: `app/src/main/java/com/smashzone/app/MainActivity.java`

Add broadcast receivers to update UI when notifications arrive:

```java
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.os.Bundle;
import androidx.localbroadcastmanager.content.LocalBroadcastManager;

public class MainActivity extends AppCompatActivity {
    
    // Broadcast receivers
    private BroadcastReceiver bookingUpdateReceiver;
    private BroadcastReceiver orderUpdateReceiver;
    private BroadcastReceiver productUpdateReceiver;
    private BroadcastReceiver shippingUpdateReceiver;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        
        // ... existing code ...
        
        // Register broadcast receivers
        registerBroadcastReceivers();
        
        // Handle notification deep links
        handleNotificationIntent(getIntent());
    }

    /**
     * Register broadcast receivers for notification updates
     */
    private void registerBroadcastReceivers() {
        // Booking update receiver
        bookingUpdateReceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String bookingId = intent.getStringExtra("booking_id");
                String status = intent.getStringExtra("status");
                
                // Refresh bookings fragment
                refreshBookingsFragment();
                
                // Show toast or update UI
                if (status != null) {
                    showToast("Booking " + status);
                }
            }
        };

        // Order update receiver
        orderUpdateReceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String orderId = intent.getStringExtra("order_id");
                String status = intent.getStringExtra("status");
                
                // Refresh orders fragment
                refreshOrdersFragment();
                
                // Show toast or update UI
                if (status != null) {
                    showToast("Order " + status);
                }
            }
        };

        // Product update receiver
        productUpdateReceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String productId = intent.getStringExtra("product_id");
                String status = intent.getStringExtra("status");
                
                // Refresh products fragment
                refreshProductsFragment();
                
                // Show toast or update UI
                showToast("Product updated");
            }
        };

        // Shipping update receiver
        shippingUpdateReceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String orderId = intent.getStringExtra("order_id");
                String status = intent.getStringExtra("status");
                
                // Refresh orders fragment
                refreshOrdersFragment();
                
                // Show toast or update UI
                showToast("Shipping " + status);
            }
        };

        // Register receivers
        LocalBroadcastManager.getInstance(this).registerReceiver(
            bookingUpdateReceiver,
            new IntentFilter("com.smashzone.BOOKING_UPDATED")
        );
        
        LocalBroadcastManager.getInstance(this).registerReceiver(
            orderUpdateReceiver,
            new IntentFilter("com.smashzone.ORDER_UPDATED")
        );
        
        LocalBroadcastManager.getInstance(this).registerReceiver(
            productUpdateReceiver,
            new IntentFilter("com.smashzone.PRODUCT_UPDATED")
        );
        
        LocalBroadcastManager.getInstance(this).registerReceiver(
            shippingUpdateReceiver,
            new IntentFilter("com.smashzone.SHIPPING_UPDATED")
        );
    }

    /**
     * Handle notification deep links
     */
    private void handleNotificationIntent(Intent intent) {
        if (intent != null && intent.hasExtra("fragment")) {
            String fragment = intent.getStringExtra("fragment");
            
            switch (fragment) {
                case "bookings":
                    navigateToBookingsFragment();
                    if (intent.hasExtra("booking_id")) {
                        String bookingId = intent.getStringExtra("booking_id");
                        openBookingDetails(bookingId);
                    }
                    break;
                case "orders":
                    navigateToOrdersFragment();
                    if (intent.hasExtra("order_id")) {
                        String orderId = intent.getStringExtra("order_id");
                        openOrderDetails(orderId);
                    }
                    break;
                case "products":
                    navigateToProductsFragment();
                    if (intent.hasExtra("product_id")) {
                        String productId = intent.getStringExtra("product_id");
                        openProductDetails(productId);
                    }
                    break;
            }
        }
    }

    // Helper methods
    private void refreshBookingsFragment() {
        // TODO: Refresh bookings fragment
        // Example: if (bookingsFragment != null) bookingsFragment.refresh();
    }

    private void refreshOrdersFragment() {
        // TODO: Refresh orders fragment
    }

    private void refreshProductsFragment() {
        // TODO: Refresh products fragment
    }

    private void navigateToBookingsFragment() {
        // TODO: Navigate to bookings tab/fragment
    }

    private void navigateToOrdersFragment() {
        // TODO: Navigate to orders tab/fragment
    }

    private void navigateToProductsFragment() {
        // TODO: Navigate to products tab/fragment
    }

    private void openBookingDetails(String bookingId) {
        // TODO: Open booking details screen
    }

    private void openOrderDetails(String orderId) {
        // TODO: Open order details screen
    }

    private void openProductDetails(String productId) {
        // TODO: Open product details screen
    }

    private void showToast(String message) {
        Toast.makeText(this, message, Toast.LENGTH_SHORT).show();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        
        // Unregister receivers
        if (bookingUpdateReceiver != null) {
            LocalBroadcastManager.getInstance(this).unregisterReceiver(bookingUpdateReceiver);
        }
        if (orderUpdateReceiver != null) {
            LocalBroadcastManager.getInstance(this).unregisterReceiver(orderUpdateReceiver);
        }
        if (productUpdateReceiver != null) {
            LocalBroadcastManager.getInstance(this).unregisterReceiver(productUpdateReceiver);
        }
        if (shippingUpdateReceiver != null) {
            LocalBroadcastManager.getInstance(this).unregisterReceiver(shippingUpdateReceiver);
        }
    }
}
```

---

## 🔧 Step 3: Update ApiService to Refresh Data

### File: `app/src/main/java/com/smashzone/app/ApiService.java`

Add methods to refresh data when notifications arrive:

```java
public class ApiService {
    // ... existing code ...
    
    /**
     * Refresh bookings from server
     */
    public void refreshBookings(ApiCallback callback) {
        String url = BASE_URL + "/api/bookings";
        
        JsonObjectRequest request = new JsonObjectRequest(
            Request.Method.GET, url, null,
            response -> {
                try {
                    // Parse response and update local database
                    // Then call callback
                    callback.onSuccess(response);
                } catch (Exception e) {
                    callback.onError(e.getMessage());
                }
            },
            error -> callback.onError(error.getMessage())
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

    /**
     * Refresh orders from server
     */
    public void refreshOrders(ApiCallback callback) {
        // Similar implementation for orders
    }

    /**
     * Refresh products from server
     */
    public void refreshProducts(ApiCallback callback) {
        // Similar implementation for products
    }
}
```

---

## 📋 Step 4: Notification Data Structure Reference

### Booking Confirmed:
```json
{
  "type": "booking_confirmed",
  "booking_id": "123",
  "court_name": "Court 1",
  "date": "2025-01-15",
  "time": "10:00 AM - 11:00 AM",
  "amount": "50.00"
}
```

### Order Confirmed:
```json
{
  "type": "order_confirmed",
  "order_id": "456",
  "order_number": "ORD-20250101-ABC123",
  "total_amount": "150.00",
  "status": "confirmed",
  "item_count": "3"
}
```

### Order Status Update:
```json
{
  "type": "order_status_update",
  "order_id": "456",
  "order_number": "ORD-20250101-ABC123",
  "status": "shipped"
}
```

### Shipping Update:
```json
{
  "type": "shipping_update",
  "order_id": "456",
  "order_number": "ORD-20250101-ABC123",
  "status": "in_transit",
  "tracking_number": "TRACK123456",
  "estimated_delivery": "2025-01-05"
}
```

### Product Stock Update:
```json
{
  "type": "product_stock_update",
  "product_id": "789",
  "product_name": "Badminton Racket",
  "status": "in_stock",
  "quantity": "10"
}
```

---

## ✅ Checklist

- [ ] Update `FirebaseMessagingService.java` with notification handlers
- [ ] Implement database update methods for each notification type
- [ ] Implement data refresh methods (API calls)
- [ ] Add broadcast receivers in `MainActivity.java`
- [ ] Handle notification deep links
- [ ] Update UI fragments to refresh when broadcasts received
- [ ] Test each notification type
- [ ] Handle edge cases (null data, missing fields)

---

## 🧪 Testing

1. **Test Booking Confirmation:**
   - Complete a booking payment
   - Check notification appears
   - Verify bookings list refreshes
   - Verify booking status updates

2. **Test Order Confirmation:**
   - Complete a product purchase
   - Check notification appears
   - Verify orders list refreshes
   - Verify product quantities update

3. **Test Order Status Update:**
   - Change order status on web
   - Check notification appears
   - Verify order status updates in app

---

## 📝 Notes

- All notification data is sent as strings, so parse numbers/dates as needed
- Handle null values gracefully
- Update local database before refreshing UI
- Use broadcasts to update UI across fragments
- Deep link to relevant screens when notification is tapped

---

## 🎯 Summary

1. **Parse notification type** in `onMessageReceived`
2. **Update local database** based on notification type
3. **Refresh data from server** to ensure consistency
4. **Broadcast updates** to UI components
5. **Handle deep links** to navigate to relevant screens

This ensures your app stays in sync with the backend and provides a seamless user experience!

