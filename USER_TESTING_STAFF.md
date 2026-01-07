# SmashZone User Testing Document - Staff Role

## Document Information
- **System**: SmashZone - Badminton Court Booking Platform
- **User Role**: Staff
- **Version**: 1.0
- **Date**: 2024
- **Tester Name**: ________________
- **Test Date**: ________________

---

## Overview
This document is designed to test all staff-facing features of the SmashZone platform. Staff members assist owners in managing courts, bookings, and customer interactions.

**Testing Format**: For each test case, mark either ✅ **PASS** or ❌ **FAIL**. If it fails, document the issue in the "Issues Found" section.

---

## Pre-Testing Checklist
- [ ] Test account created with Staff role
- [ ] Browser: Chrome/Firefox/Safari/Edge
- [ ] Device: Desktop/Mobile/Tablet
- [ ] Internet connection stable
- [ ] Clear browser cache before starting
- [ ] Owner account exists with courts for testing

---

## 1. Authentication & Onboarding

### 1.1 Staff Account Creation
**Test Steps:**
1. Receive staff account invitation from owner (or login with provided credentials)
2. Complete account setup if required
3. Set initial password

**Expected Results:**
- Staff account can be created/accessed
- Password can be set
- Account setup is complete
- Staff can login successfully

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 1.2 Login
**Test Steps:**
1. Navigate to login page
2. Enter email and password
3. Select "Staff" from role dropdown
4. Click "Sign In"

**Expected Results:**
- Login successful with correct credentials
- Error message shown for incorrect credentials
- Role selection is required
- User redirected to staff dashboard after login

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 1.3 Dashboard Overview
**Test Steps:**
1. Login and navigate to staff dashboard
2. Review dashboard sections:
   - Welcome message
   - Today's bookings
   - Upcoming bookings
   - Quick actions

**Expected Results:**
- Dashboard loads correctly
- Staff-specific information is displayed
- Quick actions are accessible
- Navigation menu is visible

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 2. Bookings Management

### 2.1 View Bookings
**Test Steps:**
1. Navigate to "Bookings" section
2. Review all bookings
3. Filter bookings by:
   - Date
   - Court
   - Status
   - Customer

**Expected Results:**
- Bookings page loads correctly
- All bookings are displayed
- Filters work correctly
- Booking details are visible

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 2.2 View Booking Details
**Test Steps:**
1. Click on a booking to view details
2. Review:
   - Customer information
   - Court details
   - Date and time
   - Payment status
   - Booking status

**Expected Results:**
- Booking details are displayed
- All information is accurate
- Customer contact info is shown
- Payment status is clear

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 2.3 Update Booking Status
**Test Steps:**
1. Select a booking
2. Change booking status:
   - Pending → Confirmed
   - Confirmed → Completed
   - Cancel booking
3. Verify status change

**Expected Results:**
- Status can be changed
- Status updates correctly
- Customer is notified (if applicable)
- Changes are saved immediately

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 2.4 Check-in/Check-out Bookings
**Test Steps:**
1. Find an upcoming booking
2. Click "Check-in" when customer arrives
3. Click "Check-out" when booking ends
4. Verify status updates

**Expected Results:**
- Check-in functionality works
- Check-out functionality works
- Status updates correctly
- Time is recorded

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 2.5 Handle Booking Cancellations
**Test Steps:**
1. Cancel a booking on behalf of customer
2. Process refund if payment was made
3. Verify cancellation is recorded
4. Check customer notification

**Expected Results:**
- Booking can be cancelled
- Refund can be processed
- Cancellation is recorded
- Customer is notified

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 3. Court Management

### 3.1 View Courts
**Test Steps:**
1. Navigate to "Courts" section
2. Review list of courts
3. Check court status and availability

**Expected Results:**
- Courts page loads correctly
- All courts are displayed
- Court status is shown
- Availability is visible

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 3.2 Update Court Status
**Test Steps:**
1. Select a court
2. Change court status:
   - Available
   - Maintenance
   - Closed
3. Verify status change

**Expected Results:**
- Court status can be updated
- Status change is saved
- Status affects booking availability
- Changes are reflected immediately

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 3.3 View Court Schedule
**Test Steps:**
1. Select a court
2. View court schedule/calendar
3. Check bookings for the court
4. Review availability

**Expected Results:**
- Court schedule is displayed
- Bookings are shown correctly
- Availability is clear
- Schedule is easy to read

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 4. Customer Management

### 4.1 View Customers
**Test Steps:**
1. Navigate to "Customers" section (if available)
2. Review customer list
3. Search for specific customers

**Expected Results:**
- Customers page loads (if feature exists)
- Customer list is displayed
- Search functionality works
- Customer information is shown

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 4.2 View Customer Details
**Test Steps:**
1. Click on a customer
2. Review customer information:
   - Contact details
   - Booking history
   - Payment history
   - Account status

**Expected Results:**
- Customer details are displayed
- Booking history is shown
- Information is accurate
- Can contact customer

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 4.3 Assist Customer Bookings
**Test Steps:**
1. Help customer create booking
2. Select court and time slot
3. Complete booking process
4. Verify booking is created

**Expected Results:**
- Can create booking for customer
- Booking process works
- Booking is confirmed
- Customer receives confirmation

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 5. Payments

### 5.1 View Payments
**Test Steps:**
1. Navigate to "Payments" section
2. Review payment list
3. Filter payments by status/date

**Expected Results:**
- Payments page loads correctly
- All payments are listed
- Payment details are accurate
- Filters work correctly

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 5.2 Process Payments
**Test Steps:**
1. Find a pending payment
2. Process payment manually (if feature exists)
3. Mark payment as received
4. Verify payment status updates

**Expected Results:**
- Payments can be processed (if applicable)
- Payment status can be updated
- Changes are saved
- Receipt can be generated

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 5.3 Handle Refunds
**Test Steps:**
1. Select a paid booking
2. Initiate refund process
3. Process refund
4. Verify refund status

**Expected Results:**
- Refunds can be processed (if permitted)
- Refund amount can be specified
- Refund is recorded
- Customer is notified

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 6. Reports & Analytics

### 6.1 View Daily Reports
**Test Steps:**
1. Navigate to Reports section (if available)
2. View daily booking summary
3. Review revenue for the day
4. Check court utilization

**Expected Results:**
- Reports are accessible (if feature exists)
- Daily data is displayed
- Information is accurate
- Reports are easy to understand

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 6.2 Export Data
**Test Steps:**
1. Generate a report
2. Export report (if available)
3. Verify export format

**Expected Results:**
- Reports can be exported (if feature exists)
- Export format is correct
- Data is complete

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 7. Notifications

### 7.1 View Notifications
**Test Steps:**
1. Click notification bell icon
2. Review notifications:
   - New bookings
   - Booking changes
   - Customer requests
   - System alerts

**Expected Results:**
- Notifications are displayed
- Unread count is accurate
- Notifications are relevant
- Can mark as read

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 7.2 Respond to Notifications
**Test Steps:**
1. Click on a notification
2. Navigate to related page
3. Take appropriate action
4. Verify notification is marked as read

**Expected Results:**
- Can click on notifications
- Navigation works correctly
- Notification is marked as read
- Unread count updates

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 8. Profile & Settings

### 8.1 View Profile
**Test Steps:**
1. Click on profile icon
2. Navigate to profile page
3. Review profile information

**Expected Results:**
- Profile page loads correctly
- Staff information is displayed
- Profile picture is shown (if uploaded)

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 8.2 Edit Profile
**Test Steps:**
1. Click "Edit Profile"
2. Update profile information
3. Change password
4. Save changes

**Expected Results:**
- Profile can be edited
- Changes can be saved
- Password can be changed
- Success message appears

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 9. Permissions & Access Control

### 9.1 Verify Permissions
**Test Steps:**
1. Attempt to access owner-only features
2. Verify staff cannot access restricted areas
3. Check that staff can access assigned features

**Expected Results:**
- Staff cannot access owner-only features
- Staff can access assigned features
- Access control is enforced
- Appropriate error messages shown for restricted access

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 9.2 Feature Access
**Test Steps:**
1. Test access to:
   - Court management (view/edit)
   - Booking management
   - Payment processing
   - Customer management
   - Reports

**Expected Results:**
- Staff has appropriate access
- Restricted features are blocked
- Error messages are clear

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 10. Mobile Responsiveness

### 10.1 Mobile Access
**Test Steps:**
1. Access system on mobile device
2. Test key features on mobile:
   - View bookings
   - Update booking status
   - Check court availability
   - View notifications

**Expected Results:**
- System is mobile-friendly
- All features are accessible
- Touch interactions work
- Data is readable

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 10.2 Mobile Booking Management
**Test Steps:**
1. Test booking management on mobile
2. Update booking status
3. Check-in/check-out customers
4. View court schedules

**Expected Results:**
- Booking management works on mobile
- Status updates work
- Check-in/out is functional
- Interface is usable

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 11. Error Handling

### 11.1 Invalid Actions
**Test Steps:**
1. Try to access restricted features
2. Try invalid operations
3. Test with expired session

**Expected Results:**
- Appropriate error messages appear
- Access is denied for restricted features
- Error messages are clear
- System prevents invalid actions

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 12. Performance & Usability

### 12.1 Page Load Times
**Test Steps:**
1. Measure load times for key pages
2. Test with multiple bookings

**Expected Results:**
- Pages load within acceptable time
- Performance is good

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 12.2 User Experience
**Test Steps:**
1. Complete typical staff workflow:
   - Login → View bookings → Update status → Check-in customer
2. Note any confusing elements
3. Check for usability issues

**Expected Results:**
- Workflow is smooth
- Interface is intuitive
- Actions are clear
- No major usability issues

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
| Authentication & Onboarding | 3 | ___ | ___ | ___% |
| Bookings Management | 5 | ___ | ___ | ___% |
| Court Management | 3 | ___ | ___ | ___% |
| Customer Management | 3 | ___ | ___ | ___% |
| Payments | 3 | ___ | ___ | ___% |
| Reports & Analytics | 2 | ___ | ___ | ___% |
| Notifications | 2 | ___ | ___ | ___% |
| Profile & Settings | 2 | ___ | ___ | ___% |
| Permissions & Access Control | 2 | ___ | ___ | ___% |
| Mobile Responsiveness | 2 | ___ | ___ | ___% |
| Error Handling | 1 | ___ | ___ | ___% |
| Performance & Usability | 2 | ___ | ___ | ___% |
| **TOTAL** | **30** | **___** | **___** | **___%** |

---

## Test Completion

- **Total Test Cases**: 30
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

