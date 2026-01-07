# SmashZone User Testing Document - Owner Role

## Document Information
- **System**: SmashZone - Badminton Court Booking Platform
- **User Role**: Owner
- **Version**: 1.0
- **Date**: 2024
- **Tester Name**: ________________
- **Test Date**: ________________

---

## Overview
This document is designed to test all owner-facing features of the SmashZone platform. Owners can manage courts, view analytics, manage staff, view bookings, and handle payments.

**Testing Format**: For each test case, mark either ✅ **PASS** or ❌ **FAIL**. If it fails, document the issue in the "Issues Found" section.

---

## Pre-Testing Checklist
- [ ] Test account created with Owner role
- [ ] Browser: Chrome/Firefox/Safari/Edge
- [ ] Device: Desktop/Mobile/Tablet
- [ ] Internet connection stable
- [ ] Clear browser cache before starting
- [ ] At least one court created for testing

---

## 1. Authentication & Onboarding

### 1.1 Login
**Test Steps:**
1. Navigate to login page
2. Enter email and password
3. Select "Owner" from role dropdown
4. Click "Sign In"

**Expected Results:**
- Login successful with correct credentials
- Error message shown for incorrect credentials
- Role selection is required
- User redirected to owner dashboard after login

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 1.2 Dashboard Overview
**Test Steps:**
1. Login and navigate to owner dashboard
2. Review all sections:
   - Welcome message
   - Analytics/Statistics cards
   - Recent bookings
   - Revenue information
   - Court management options

**Expected Results:**
- Dashboard loads correctly
- All statistics are displayed
- Data is accurate
- Navigation menu is accessible
- Owner-specific features are visible

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 2. Court Management

### 2.1 View Courts
**Test Steps:**
1. Navigate to "My Courts" or "Courts" section
2. Review list of owned courts
3. Check court details:
   - Court name
   - Location
   - Status
   - Number of bookings

**Expected Results:**
- Courts page loads correctly
- All owned courts are displayed
- Court information is accurate
- Court status is shown correctly

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 2.2 Create New Court
**Test Steps:**
1. Click "Add Court" or "Create Court" button
2. Fill in court details:
   - Court name
   - Description
   - Location
   - Upload image (optional)
3. Save court

**Expected Results:**
- Create court form loads
- All required fields are validated
- Image upload works (if applicable)
- Court is created successfully
- Success message appears
- New court appears in list

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 2.3 Edit Court
**Test Steps:**
1. Select a court to edit
2. Click "Edit" button
3. Update court information
4. Save changes

**Expected Results:**
- Edit form loads with current data
- Changes can be saved
- Success message appears
- Updated information is reflected

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 2.4 Set Court Pricing Rules
**Test Steps:**
1. Navigate to court pricing settings
2. Add pricing rules:
   - Time-based pricing (peak hours, off-peak)
   - Day-based pricing (weekend, weekday)
   - Special rates
3. Save pricing rules

**Expected Results:**
- Pricing rules can be set
- Different rates for different times
- Rules are saved correctly
- Prices are applied to bookings

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 2.5 Court Status Management
**Test Steps:**
1. Change court status (Active/Inactive/Maintenance)
2. Verify status change is saved
3. Check if bookings are affected

**Expected Results:**
- Status can be changed
- Status is saved correctly
- Inactive courts don't accept new bookings
- Existing bookings are handled appropriately

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 3. Bookings Management

### 3.1 View All Bookings
**Test Steps:**
1. Navigate to "Bookings" section
2. Review all bookings for owned courts
3. Filter bookings by:
   - Date range
   - Court
   - Status
   - Payment status

**Expected Results:**
- Bookings page loads correctly
- All bookings are displayed
- Filters work correctly
- Booking details are accurate

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 3.2 View Booking Details
**Test Steps:**
1. Click on a booking to view details
2. Review customer information
3. Check booking time and date
4. Review payment status

**Expected Results:**
- Booking details are displayed
- Customer information is shown
- All booking data is accurate
- Payment status is clear

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 3.3 Manage Booking Status
**Test Steps:**
1. Change booking status (Pending/Confirmed/Cancelled)
2. Verify status change
3. Check if customer is notified

**Expected Results:**
- Status can be changed
- Status updates correctly
- Customer receives notification
- Changes are reflected immediately

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 3.4 Cancel/Refund Booking
**Test Steps:**
1. Cancel a booking
2. Process refund if payment was made
3. Verify refund is processed
4. Check customer notification

**Expected Results:**
- Booking can be cancelled
- Refund is processed correctly
- Customer is notified
- Booking status updates

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 4. Analytics & Reports

### 4.1 View Analytics Dashboard
**Test Steps:**
1. Navigate to Analytics/Reports section
2. Review key metrics:
   - Total revenue
   - Number of bookings
   - Court utilization
   - Peak hours
   - Popular courts

**Expected Results:**
- Analytics page loads correctly
- All metrics are displayed
- Data is accurate
- Charts/graphs render properly
- Date range filters work

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 4.2 Generate Reports
**Test Steps:**
1. Select report type (Revenue, Bookings, etc.)
2. Set date range
3. Generate report
4. Export report (if available)

**Expected Results:**
- Reports can be generated
- Date range selection works
- Report data is accurate
- Export functionality works (PDF/Excel)
- Reports are formatted correctly

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 4.3 View Revenue Reports
**Test Steps:**
1. Navigate to revenue/earnings section
2. Review revenue by:
   - Date range
   - Court
   - Payment method
3. Check payment breakdown

**Expected Results:**
- Revenue data is displayed
- Totals are calculated correctly
- Breakdown by court is accurate
- Payment methods are shown

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 5. Staff Management

### 5.1 View Staff List
**Test Steps:**
1. Navigate to "Staff" section
2. Review list of staff members
3. Check staff details:
   - Name
   - Email
   - Position
   - Status

**Expected Results:**
- Staff page loads correctly
- All staff members are listed
- Staff information is accurate
- Status is displayed

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 5.2 Add New Staff
**Test Steps:**
1. Click "Add Staff" button
2. Fill in staff details:
   - Name
   - Email
   - Position
   - Password (or send invitation)
3. Save staff member

**Expected Results:**
- Add staff form loads
- All fields are validated
- Staff account is created
- Staff receives login credentials/invitation
- Success message appears

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 5.3 Edit Staff
**Test Steps:**
1. Select a staff member
2. Click "Edit" button
3. Update staff information
4. Save changes

**Expected Results:**
- Edit form loads with current data
- Changes can be saved
- Success message appears
- Updated information is reflected

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 5.4 Deactivate/Remove Staff
**Test Steps:**
1. Select a staff member
2. Click "Deactivate" or "Remove" button
3. Confirm action
4. Verify staff is deactivated/removed

**Expected Results:**
- Staff can be deactivated
- Confirmation dialog appears
- Staff status updates
- Deactivated staff cannot login

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 6. Payments & Financials

### 6.1 View Payment History
**Test Steps:**
1. Navigate to "Payments" section
2. Review all payments for bookings
3. Filter payments by:
   - Date range
   - Court
   - Status
   - Payment method

**Expected Results:**
- Payments page loads correctly
- All payments are listed
- Filters work correctly
- Payment details are accurate

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 6.2 Process Refunds
**Test Steps:**
1. Select a paid booking
2. Initiate refund
3. Enter refund amount
4. Process refund
5. Verify refund status

**Expected Results:**
- Refund can be initiated
- Refund amount can be specified
- Refund is processed correctly
- Customer is notified
- Refund status updates

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 6.3 View Financial Summary
**Test Steps:**
1. Navigate to financial summary
2. Review:
   - Total earnings
   - Pending payments
   - Refunds issued
   - Net revenue

**Expected Results:**
- Financial summary is displayed
- All amounts are calculated correctly
- Data is up-to-date
- Summary is clear and understandable

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 7. Product Management (if applicable)

### 7.1 View Products
**Test Steps:**
1. Navigate to Products section (if owner can manage)
2. Review product list
3. Check product details

**Expected Results:**
- Products page loads (if applicable)
- Products are displayed correctly
- Owner can manage products (if feature exists)

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 7.2 Add/Edit Products
**Test Steps:**
1. Add a new product
2. Fill in product details
3. Set price and stock
4. Save product
5. Edit existing product

**Expected Results:**
- Products can be added/edited (if applicable)
- All fields work correctly
- Images can be uploaded
- Products are saved successfully

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 8. Notifications & Communication

### 8.1 View Notifications
**Test Steps:**
1. Click notification bell icon
2. Review notifications:
   - New bookings
   - Payment received
   - Booking cancellations
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

### 8.2 Booking Notifications
**Test Steps:**
1. Wait for new booking (or create test booking)
2. Verify notification is received
3. Check notification details

**Expected Results:**
- Notification is received for new bookings
- Notification contains booking details
- Can navigate to booking from notification

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 9. Settings & Configuration

### 9.1 Account Settings
**Test Steps:**
1. Navigate to Settings
2. Update account information
3. Change password
4. Update profile picture

**Expected Results:**
- Settings page loads
- Account info can be updated
- Password can be changed
- Profile picture can be uploaded
- Changes are saved

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

### 9.2 Business Settings
**Test Steps:**
1. Configure business settings:
   - Business name
   - Contact information
   - Operating hours
   - Policies

**Expected Results:**
- Business settings can be configured
- Settings are saved
- Settings affect booking system

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 10. Mobile Responsiveness

### 10.1 Mobile Dashboard
**Test Steps:**
1. Access dashboard on mobile
2. Test all features on mobile device
3. Check touch interactions

**Expected Results:**
- Dashboard is mobile-friendly
- All features are accessible
- Touch targets are adequate
- Data is readable

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 11. Security & Permissions

### 11.1 Access Control
**Test Steps:**
1. Verify owner can access all owner features
2. Verify owner cannot access customer-only features inappropriately
3. Test session timeout

**Expected Results:**
- Owner has access to all owner features
- Access control is enforced
- Session management works correctly

**Test Result**: ⬜ PASS  ⬜ FAIL

**Issues Found:**
_________________________________________________
_________________________________________________

---

## 12. Performance & Usability

### 12.1 Page Load Times
**Test Steps:**
1. Measure load times for key pages
2. Test with large datasets

**Expected Results:**
- Pages load within acceptable time
- Performance is good with large data

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
| Authentication & Onboarding | 2 | ___ | ___ | ___% |
| Court Management | 5 | ___ | ___ | ___% |
| Bookings Management | 4 | ___ | ___ | ___% |
| Analytics & Reports | 3 | ___ | ___ | ___% |
| Staff Management | 4 | ___ | ___ | ___% |
| Payments & Financials | 3 | ___ | ___ | ___% |
| Product Management | 2 | ___ | ___ | ___% |
| Notifications & Communication | 2 | ___ | ___ | ___% |
| Settings & Configuration | 2 | ___ | ___ | ___% |
| Mobile Responsiveness | 1 | ___ | ___ | ___% |
| Security & Permissions | 1 | ___ | ___ | ___% |
| Performance & Usability | 1 | ___ | ___ | ___% |
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

