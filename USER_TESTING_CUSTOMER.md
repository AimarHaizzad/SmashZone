# SmashZone User Testing Document - Customer Role

## Document Information
- **System**: SmashZone - Badminton Court Booking Platform
- **User Role**: Customer
- **Version**: 1.0
- **Date**: 2024
- **Tester Name**: ________________
- **Test Date**: ________________

---

## Overview
This document is designed to test all customer-facing features of the SmashZone platform. Customers can book courts, shop for badminton equipment, manage bookings, and make payments.

**Testing Format**: For each test case, mark either ✅ **PASS** or ❌ **FAIL**. If it fails, document the issue in the "Issues Found" section.

---

## Pre-Testing Checklist
- [ ] Test account created with Customer role
- [ ] Browser: Chrome/Firefox/Safari/Edge
- [ ] Device: Desktop/Mobile/Tablet
- [ ] Internet connection stable
- [ ] Clear browser cache before starting

---

## 1. Authentication & Onboarding

### 1.1 Registration
**Test Steps:**
1. Navigate to the registration page
2. Fill in all required fields:
   - Full Name
   - Email Address
   - Password
   - Confirm Password
3. Accept Terms and Conditions
4. Click "Create Account"

**Expected Results:**
- Form validates all fields correctly
- Success message appears after registration
- User is redirected to login page or dashboard
- Email verification (if enabled) is sent

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 1.2 Login
**Test Steps:**
1. Navigate to login page
2. Enter email and password
3. Select "Customer" from role dropdown
4. Optionally check "Remember me"
5. Click "Sign In"

**Expected Results:**
- Login successful with correct credentials
- Error message shown for incorrect credentials
- "Remember me" functionality works
- User redirected to dashboard after login
- Role selection is required

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 1.3 Forgot Password
**Test Steps:**
1. Click "Forgot password?" link on login page
2. Enter registered email address
3. Click "Send Password Reset Link"

**Expected Results:**
- Password reset link sent successfully
- Success message displayed
- Email received with reset link
- Reset link works correctly

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 1.4 Tutorial System
**Test Steps:**
1. Login as a new customer (first time)
2. Observe tutorial popup on dashboard
3. Follow tutorial steps
4. Try skipping tutorial
5. Complete tutorial

**Expected Results:**
- Tutorial appears automatically for new users
- All tutorial steps show instruction messages clearly
- "Skip" button is visible and works (styled as button)
- Tutorial can be completed successfully
- Tutorial doesn't appear again after completion

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 2. Dashboard

### 2.1 Dashboard Overview
**Test Steps:**
1. Login and navigate to dashboard
2. Review all sections:
   - Welcome card with user name
   - Action buttons (Book a Court, My Bookings, Shop Products)
   - Upcoming Bookings section
   - Featured Products section

**Expected Results:**
- Dashboard loads correctly
- User name displayed correctly
- All action buttons are clickable
- Upcoming bookings are displayed (if any)
- Featured products are displayed
- Navigation menu is accessible

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 2.2 Navigation Menu
**Test Steps:**
1. Click each navigation item:
   - Dashboard
   - Courts
   - Bookings
   - Shop (dropdown: Products, Orders)
   - Payments
2. Verify each page loads correctly

**Expected Results:**
- All navigation links work
- Active page is highlighted
- Shop dropdown menu appears on hover
- Navigation is responsive on mobile

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 3. Court Booking

### 3.1 View Available Courts
**Test Steps:**
1. Navigate to "Courts" page
2. Review date navigation bar
3. Use date picker to select different dates
4. Use arrow buttons to navigate dates
5. Click "Today" button

**Expected Results:**
- Date navigation bar is visible
- Current date is displayed correctly
- Date picker opens calendar popup
- Arrow buttons navigate dates correctly
- "Today" button works
- Court availability table loads

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 3.2 Understanding Court Availability
**Test Steps:**
1. Review color legend:
   - Green = Available
   - Red = Booked
   - Blue = My Booking
2. Verify colors match actual slots in table

**Expected Results:**
- Color legend is clear and visible
- Colors match actual slot status
- Legend is easy to understand

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 3.3 Select Time Slot
**Test Steps:**
1. Find an available (green) slot
2. Click on a green "Select" button
3. Verify slot is highlighted/selected
4. Select multiple consecutive slots for same court
5. Try selecting slots from different courts

**Expected Results:**
- Green buttons are clickable
- Selected slots are highlighted
- Multiple slots can be selected
- Price per hour is displayed on buttons
- Selection panel appears at bottom

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 3.4 Confirm Booking
**Test Steps:**
1. Select one or more time slots
2. Review selection panel at bottom
3. Verify total price calculation
4. Click "Confirm Booking"
5. Review booking confirmation modal
6. Complete booking

**Expected Results:**
- Selection panel shows selected slots
- Total price is calculated correctly
- "Confirm Booking" button is enabled
- Confirmation modal appears
- Booking is created successfully
- Redirected to bookings page or payment

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 3.5 Booking Tutorial
**Test Steps:**
1. Navigate to Courts page as new user
2. Follow booking tutorial steps
3. Complete tutorial

**Expected Results:**
- Tutorial appears on first visit
- All steps show clear instructions
- Tutorial covers: date selection, table understanding, slot selection
- Can skip or complete tutorial

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 4. Shopping (Products)

### 4.1 Browse Products
**Test Steps:**
1. Navigate to Shop > Products
2. Review product grid
3. Use category filters (Shoes, Clothing, Rackets, etc.)
4. Click "All Products" to remove filters
5. Scroll through products

**Expected Results:**
- Products page loads correctly
- All products are displayed
- Category filters work correctly
- Selected category is highlighted
- "All Products" button works
- Products are responsive on mobile

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 4.2 View Product Details
**Test Steps:**
1. Click on a product card
2. Review product information:
   - Product image
   - Brand name
   - Product name
   - Price
   - Stock availability
   - Category badge

**Expected Results:**
- Product details are displayed correctly
- Image loads properly
- Price is clearly shown
- Stock status is accurate
- Product information is complete

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 4.3 Add to Cart
**Test Steps:**
1. Select quantity using quantity input
2. Click "Add to Cart" button
3. Verify success notification
4. Check cart icon in navigation (should show item count)
5. Add multiple different products

**Expected Results:**
- Quantity selector works (type number or use arrows)
- "Add to Cart" button is enabled for in-stock items
- Success notification appears
- Cart icon updates with item count
- Multiple products can be added
- Out of stock items cannot be added

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 4.4 Products Tutorial
**Test Steps:**
1. Navigate to Products page as new user
2. Follow products tutorial
3. Complete tutorial

**Expected Results:**
- Tutorial appears on first visit
- Tutorial covers: categories, product cards, quantity, add to cart
- All instructions are clear
- Can skip or complete tutorial

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 5. Shopping Cart

### 5.1 View Cart
**Test Steps:**
1. Click cart icon in navigation
2. Review cart items
3. Verify product details in cart
4. Check order summary

**Expected Results:**
- Cart page loads correctly
- All added items are displayed
- Product details are correct
- Order summary shows correct totals
- Cart is empty message if no items

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 5.2 Update Cart
**Test Steps:**
1. Change quantity of items in cart
2. Click "Update Cart" button
3. Verify quantities updated
4. Verify total price recalculated
5. Remove an item using trash icon
6. Update cart again

**Expected Results:**
- Quantity can be changed
- "Update Cart" button works
- Prices recalculate correctly
- Items can be removed
- Cart updates immediately
- Empty cart message if all items removed

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 5.3 Proceed to Checkout
**Test Steps:**
1. Review order summary
2. Verify total amount
3. Click "Proceed to Checkout" button
4. Verify redirect to checkout/payment page

**Expected Results:**
- Order summary is accurate
- Total price is correct
- "Proceed to Checkout" button is enabled
- Redirects to payment/checkout page
- Secure payment message is visible

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 5.4 Cart Tutorial
**Test Steps:**
1. Navigate to Cart page as new user
2. Follow cart tutorial
3. Complete tutorial

**Expected Results:**
- Tutorial appears on first visit
- Tutorial covers: cart items, order summary, checkout
- Instructions are clear
- Can skip or complete tutorial

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 6. My Bookings

### 6.1 View Bookings
**Test Steps:**
1. Navigate to "Bookings" or "My Bookings"
2. Review list of bookings
3. Check booking details:
   - Court name
   - Date and time
   - Status
   - Payment status
   - Total price

**Expected Results:**
- Bookings page loads correctly
- All bookings are displayed
- Booking details are accurate
- Status is clearly shown
- Past and upcoming bookings are separated (if applicable)

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 6.2 View Booking Details
**Test Steps:**
1. Click on a booking to view details
2. Review all booking information
3. Check payment status
4. Review court information

**Expected Results:**
- Booking details modal/page opens
- All information is displayed correctly
- Payment status is accurate
- Court details are shown

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 6.3 Cancel Booking
**Test Steps:**
1. Find an upcoming booking
2. Click "Cancel" or "Cancel Booking" button
3. Confirm cancellation
4. Verify booking status changes
5. Check if refund is processed (if applicable)

**Expected Results:**
- Cancel button is visible for eligible bookings
- Confirmation dialog appears
- Booking is cancelled successfully
- Status updates to "Cancelled"
- Refund is processed (if applicable)
- Notification is sent

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 7. Payments

### 7.1 View Payment History
**Test Steps:**
1. Navigate to "Payments" page
2. Review payment list
3. Check payment details:
   - Amount
   - Date
   - Status
   - Related booking/order

**Expected Results:**
- Payments page loads correctly
- All payments are listed
- Payment details are accurate
- Status is clearly displayed
- Payments are sorted by date (newest first)

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 7.2 Make Payment
**Test Steps:**
1. Navigate to a pending payment
2. Click "Pay Now" or payment button
3. Complete payment process (Stripe integration)
4. Verify payment confirmation

**Expected Results:**
- Payment page loads correctly
- Stripe payment form appears
- Payment can be completed
- Payment status updates to "Paid"
- Confirmation message appears
- Receipt is generated (if applicable)

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 8. Profile & Settings

### 8.1 View Profile
**Test Steps:**
1. Click on user profile icon/name
2. Navigate to profile page
3. Review profile information

**Expected Results:**
- Profile page loads correctly
- User information is displayed
- Profile picture is shown (if uploaded)

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 8.2 Edit Profile
**Test Steps:**
1. Click "Edit Profile" button
2. Update profile information
3. Save changes
4. Verify updates are saved

**Expected Results:**
- Edit form loads correctly
- Changes can be saved
- Success message appears
- Profile updates are reflected

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 9. Notifications

### 9.1 View Notifications
**Test Steps:**
1. Click notification bell icon
2. Review notification list
3. Check unread notification count

**Expected Results:**
- Notification dropdown/modal opens
- Notifications are displayed
- Unread count is accurate
- Notifications are sorted by date

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 9.2 Mark Notifications as Read
**Test Steps:**
1. Click on a notification
2. Verify it's marked as read
3. Check unread count decreases

**Expected Results:**
- Notification can be clicked
- Status changes to "read"
- Unread count updates
- Notification redirects to relevant page

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 10. Mobile Responsiveness

### 10.1 Mobile Navigation
**Test Steps:**
1. Open site on mobile device
2. Test navigation menu
3. Test all pages on mobile
4. Check touch interactions

**Expected Results:**
- Navigation is mobile-friendly
- All pages are responsive
- Touch targets are adequate size
- Text is readable
- Forms are usable on mobile

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 10.2 Mobile Booking
**Test Steps:**
1. Test court booking on mobile
2. Select date and time slots
3. Complete booking process

**Expected Results:**
- Date navigation works on mobile
- Court table is scrollable
- Slot selection works on touch
- Booking process is smooth

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 11. Error Handling

### 11.1 Invalid Actions
**Test Steps:**
1. Try to book already booked slot
2. Try to add out-of-stock product
3. Try to access invalid pages
4. Test with expired session

**Expected Results:**
- Appropriate error messages appear
- User is informed of the issue
- System prevents invalid actions
- Error messages are clear and helpful

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 12. Performance & Usability

### 12.1 Page Load Times
**Test Steps:**
1. Measure load times for key pages:
   - Dashboard
   - Courts/Booking
   - Products
   - Cart
   - Bookings

**Expected Results:**
- Pages load within 3 seconds
- No significant delays
- Images load properly

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 12.2 User Experience
**Test Steps:**
1. Complete full user journey:
   - Register → Login → Book Court → Shop → Checkout
2. Note any confusing elements
3. Check for broken links
4. Verify all buttons work

**Expected Results:**
- User journey is smooth
- No broken links
- All buttons functional
- Interface is intuitive
- Helpful tooltips/instructions available

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## Overall Assessment

### Critical Issues (Blocking)
1. _________________________________________________
2. _________________________________________________
3. _________________________________________________

### Major Issues (High Priority)
1. _________________________________________________
2. _________________________________________________
3. _________________________________________________

### Minor Issues (Low Priority)
1. _________________________________________________
2. _________________________________________________
3. _________________________________________________

### Suggestions for Improvement
1. _________________________________________________
2. _________________________________________________
3. _________________________________________________

---

## Test Completion Summary

### Test Results Summary Table

| Test Category | Total Tests | Passed | Failed | Pass Rate |
|--------------|-------------|--------|--------|-----------|
| Authentication & Onboarding | 4 | ___ | ___ | ___% |
| Dashboard | 2 | ___ | ___ | ___% |
| Court Booking | 5 | ___ | ___ | ___% |
| Shopping (Products) | 4 | ___ | ___ | ___% |
| Shopping Cart | 4 | ___ | ___ | ___% |
| My Bookings | 3 | ___ | ___ | ___% |
| Payments | 2 | ___ | ___ | ___% |
| Profile & Settings | 2 | ___ | ___ | ___% |
| Notifications | 2 | ___ | ___ | ___% |
| Mobile Responsiveness | 2 | ___ | ___ | ___% |
| Error Handling | 1 | ___ | ___ | ___% |
| Performance & Usability | 2 | ___ | ___ | ___% |
| **TOTAL** | **33** | **___** | **___** | **___%** |

---

## Test Completion

- **Total Test Cases**: 33
- **Passed**: ______
- **Failed**: ______
- **Blocked**: ______
- **Test Duration**: ______ hours
- **Pass Rate**: ______%

**Tester Signature**: ________________  
**Date**: ________________

---

## Notes
_Use this space for any additional observations, comments, or feedback:_

_________________________________________________
_________________________________________________
_________________________________________________

