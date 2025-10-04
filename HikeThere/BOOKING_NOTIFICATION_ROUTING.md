# Booking Notification Routing Fix

## Overview
Fixed the booking notification click behavior to route users to the correct booking view based on their user type (organization vs. hiker).

## Problem
Previously, when clicking on a booking notification, all users (both organizations and hikers) were redirected to `/hiker/booking`, which was incorrect for organizations who should see their booking management interface at `/org/bookings/{booking_id}`.

## Solution Implemented

### 1. Updated Notification Dropdown Component
**File**: `resources/views/components/notification-dropdown.blade.php`

#### Changes Made:

1. **Added user type prop to component**
   ```php
   @props(['userType' => 'hiker'])
   <div x-data="notificationDropdown('{{ $userType }}')" ...>
   ```

2. **Updated Alpine.js function to accept userType parameter**
   ```javascript
   function notificationDropdown(userType = 'hiker') {
       return {
           open: false,
           loading: false,
           notifications: [],
           unreadCount: 0,
           userType: userType,  // Store user type
           ...
       }
   }
   ```

3. **Enhanced handleNotificationClick() to route based on user type**
   ```javascript
   async handleNotificationClick(notification) {
       // Mark as read...
       
       // Handle navigation based on notification data
       if (notification.data?.trail_slug) {
           window.location.href = `/trails/${notification.data.trail_slug}`;
       } else if (notification.data?.booking_id) {
           // Route based on user type
           if (this.userType === 'organization') {
               window.location.href = `/org/bookings/${notification.data.booking_id}`;
           } else {
               window.location.href = `/hiker/booking`;
           }
       }
   }
   ```

4. **Added support for booking notification types**
   - `booking_created` - Sent to organizations when a hiker creates a booking
   - `booking_updated` - Sent to organizations when a hiker updates a booking
   - `booking_status_updated` - Sent to hikers when organization changes booking status
   
   All these types now display the same booking icon (blue clipboard).

### 2. Updated Navigation Menu
**File**: `resources/views/navigation-menu.blade.php`

Pass the authenticated user's type to the notification dropdown component:
```php
<x-notification-dropdown :userType="Auth::user()->user_type ?? 'hiker'" />
```

### 3. Updated Booking Details Form
**File**: `resources/views/hiker/booking/booking-details.blade.php`

Fixed the submit button reference issue where `submitBtn` was `null`:
```javascript
// Changed from:
const submitBtn = form.querySelector('button[type="submit"]');

// To:
const submitBtn = document.querySelector('button[type="submit"][form="booking-form"]');
if (!submitBtn) {
    console.error('Submit button not found');
    return false;
}
```

This was necessary because the submit button is located outside the form element (in the sidebar) but associated via the `form` attribute.

## Routing Behavior

### For Organizations (`user_type === 'organization'`)
- Clicking a booking notification navigates to: `/org/bookings/{booking_id}`
- This shows the detailed booking management view with options to:
  - View full booking details
  - Update booking status
  - Verify/reject payment proof
  - Communicate with the hiker

### For Hikers (`user_type === 'hiker'`)
- Clicking a booking notification navigates to: `/hiker/booking`
- This shows the hiker's booking list where they can:
  - View all their bookings
  - Upload payment proof
  - Track booking status
  - Access individual booking details

## Notification Types Handled

| Type | Recipient | Navigation |
|------|-----------|------------|
| `booking_created` | Organization | `/org/bookings/{booking_id}` |
| `booking_updated` | Organization | `/org/bookings/{booking_id}` |
| `booking_status_updated` | Hiker | `/hiker/booking` |
| `booking` (generic) | Either | Context-dependent routing |

## Testing

To test the fix:

1. **As a Hiker:**
   - Create a booking
   - Wait for organization to update the booking status
   - Click on the notification in the bell dropdown
   - Should navigate to `/hiker/booking`

2. **As an Organization:**
   - Wait for a hiker to create a booking on one of your trails
   - Check the notification bell (should show unread count)
   - Click on the "New Booking Received" notification
   - Should navigate to `/org/bookings/{booking_id}` showing the specific booking details

## Files Modified

1. `resources/views/components/notification-dropdown.blade.php`
   - Added userType prop and parameter
   - Updated handleNotificationClick() logic
   - Added support for booking_created, booking_updated, booking_status_updated types

2. `resources/views/navigation-menu.blade.php`
   - Pass user_type to notification component

3. `resources/views/hiker/booking/booking-details.blade.php`
   - Fixed submit button selector issue

## Related Routes

```php
// Organization booking routes (routes/web.php)
Route::get('/org/bookings', [OrganizationBookingController::class, 'index'])->name('org.bookings.index');
Route::get('/org/bookings/{booking}', [OrganizationBookingController::class, 'show'])->name('org.bookings.show');

// Hiker booking routes
Route::get('/hiker/booking', [BookingController::class, 'index'])->name('booking.index');
```

## Benefits

1. **Improved UX**: Users are now directed to the appropriate interface based on their role
2. **Context-Aware**: Organizations see detailed booking management, hikers see their booking list
3. **Type Safety**: Added null check to prevent JavaScript errors
4. **Consistent**: All booking-related notification types now handle routing correctly
5. **Maintainable**: Clear separation of concerns between user types

## Future Enhancements

- Add hover preview of booking details in notification dropdown
- Add quick actions (approve/reject) directly from notification for organizations
- Add notification grouping for multiple bookings from the same trail
- Add sound/vibration alerts for high-priority booking notifications
