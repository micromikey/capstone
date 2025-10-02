# Toast Notification System

## Overview
The toast notification system provides real-time, non-intrusive notifications that slide in from the top-right corner of the screen. Toasts automatically appear when new notifications are received and disappear after 5 seconds.

## Features

### 🎨 **Visual Design**
- **Slide-in animation** from the right side
- **Color-coded by type**: Weather (amber), Events (purple), Bookings (blue), System (gray)
- **Type-specific icons** for quick recognition
- **Auto-dismiss** after 5 seconds
- **Manual dismiss** by clicking the X button
- **Click to navigate** to notifications page

### 🔄 **Automatic Polling**
- Checks for new notifications every **30 seconds**
- Only shows notifications created since last check
- Uses session storage to track last check timestamp
- Prevents duplicate toasts for the same notification

### 📱 **Special Display Templates**

#### Weather Notifications
- Shows current temperature in large text
- Displays location name
- Amber color scheme with cloud icon

#### Event Notifications
- Shows event title and organization
- Displays trail badge with location icon
- Shows "Free Event" or price badge
- Purple color scheme with calendar icon

#### Booking & System Notifications
- Standard title and message display
- Blue (booking) or gray (system) color scheme
- Appropriate icons for each type

## Files Created

### 1. **Toast Component**
```
resources/views/components/toast-notification.blade.php
```
- Alpine.js component for toast display
- Polling logic for new notifications
- Animation and styling
- Event listeners

### 2. **Controller Method**
```php
// app/Http/Controllers/NotificationController.php
public function getLatest(Request $request)
```
- Returns latest unread notification since last check
- Uses session to track last check timestamp
- Returns null if no new notifications

### 3. **Route**
```php
// routes/web.php
Route::get('/api/latest', [...], 'latest');
```

## How It Works

### 1. **Initialization**
When a user logs in, the toast component initializes and:
- Sets up event listeners
- Starts polling every 30 seconds
- Tracks last check time in session

### 2. **Polling Process**
```javascript
async checkForNewNotifications() {
    const response = await fetch('/notifications/api/latest');
    const data = await response.json();
    
    if (data.notification) {
        this.addToast(data.notification);
        window.dispatchEvent(new CustomEvent('notification-received'));
    }
}
```

### 3. **Toast Display**
```javascript
addToast(data) {
    // Create toast with unique ID
    // Trigger slide-in animation
    // Auto-remove after 5 seconds
}
```

### 4. **Integration with Notification Bell**
When a toast appears:
- Dispatches `notification-received` event
- Notification dropdown listens and refreshes
- Unread count updates automatically

## Usage

### For End Users
1. **Automatic**: Toasts appear automatically when new notifications arrive
2. **Click toast**: Navigate to notifications page
3. **Click X**: Dismiss toast immediately
4. **Wait**: Toast auto-dismisses after 5 seconds

### For Developers

#### Creating Notifications that Trigger Toasts
Any notification created through `NotificationService` will automatically trigger a toast:

```php
$notificationService = new NotificationService();

// Weather notification
$notificationService->sendWeatherNotification($user, $weatherData);

// Event notification
$notificationService->sendNewEventNotification($event);

// Custom notification
$notificationService->create(
    $user,
    'system',
    'Custom Title',
    'Custom message',
    ['custom' => 'data']
);
```

#### Testing Toasts
Visit the test route to create a test notification:
```
/test-toast-notification
```

This will create a system notification and trigger a toast.

## Toast Types & Styling

### Weather Toast
```
┌─────────────────────────────────┐
│ 🌤️  Weather Update             │
│ Current conditions for your...  │
│                                 │
│ 27° Balsic                      │
└─────────────────────────────────┘
```
- Amber background (`bg-amber-50`)
- Amber border (`border-amber-200`)
- Cloud icon

### Event Toast
```
┌─────────────────────────────────┐
│ 📅  New Event: Mt. Pulag Hike   │
│ Sophieret is hosting a new...   │
│                                 │
│ [📍 Mt. Pulag Trail]            │
└─────────────────────────────────┘
```
- Purple background (`bg-purple-50`)
- Purple border (`border-purple-200`)
- Calendar icon
- Trail badge

### Booking Toast
```
┌─────────────────────────────────┐
│ 📋  Booking Confirmed            │
│ Your booking has been...        │
└─────────────────────────────────┘
```
- Blue background (`bg-blue-50`)
- Blue border (`border-blue-200`)
- Clipboard icon

### System Toast
```
┌─────────────────────────────────┐
│ ℹ️  System Notification          │
│ This is a system message...     │
└─────────────────────────────────┘
```
- Gray background (`bg-gray-50`)
- Gray border (`border-gray-200`)
- Info icon

## Configuration

### Polling Interval
Default: 30 seconds
To change, edit `toast-notification.blade.php`:
```javascript
setInterval(() => this.checkForNewNotifications(), 30000); // 30 seconds
```

### Auto-dismiss Time
Default: 5 seconds
To change, edit `toast-notification.blade.php`:
```javascript
setTimeout(() => this.removeToast(id), 5000); // 5 seconds
```

### Position
Default: Top-right corner
To change, edit the container class:
```html
<div class="fixed top-4 right-4 z-50 ...">
```

### Max Width
Default: 420px
To change:
```html
<div ... style="max-width: 420px;">
```

## Integration Points

### 1. Navigation Menu
The toast component is included in `navigation-menu.blade.php`:
```blade
@auth
<x-toast-notification />
@endauth
```

### 2. Notification Dropdown
The dropdown listens for the `notification-received` event:
```javascript
window.addEventListener('notification-received', () => {
    this.loadNotifications();
});
```

### 3. Event Observer
When events are created, notifications are automatically sent:
```php
// EventObserver.php
public function created(Event $event) {
    $notifications = $this->notificationService->sendNewEventNotification($event);
}
```

### 4. Login Listener
Weather notifications trigger on login:
```php
// SendWeatherNotificationOnLogin.php
public function handle(Login $event) {
    $this->weatherService->sendLoginWeatherNotification($event->user);
}
```

## Troubleshooting

### Toast Not Appearing
1. **Check console** for JavaScript errors
2. **Verify route** `/notifications/api/latest` is accessible
3. **Check session** - last check timestamp might be too recent
4. **Clear session**: `php artisan session:flush`

### Toast Appearing Multiple Times
1. **Check polling interval** - might be too frequent
2. **Verify session tracking** - last_notification_check should update
3. **Check notification creation** - ensure not creating duplicates

### Toast Not Auto-dismissing
1. **Check setTimeout** in `removeToast()` method
2. **Verify animation classes** are applied correctly
3. **Check browser console** for errors

### Toast Styling Issues
1. **Ensure Tailwind classes** are compiled
2. **Check z-index** conflicts with other elements
3. **Verify color classes** match notification types

## Browser Compatibility
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers

## Accessibility
- **Keyboard accessible**: Can dismiss with keyboard
- **Screen reader friendly**: ARIA labels on icons
- **Click targets**: Large enough for touch screens
- **Color contrast**: Meets WCAG AA standards

## Future Enhancements
- [ ] Sound notification option
- [ ] Browser notification API integration
- [ ] Notification grouping for multiple toasts
- [ ] Swipe to dismiss on mobile
- [ ] Priority levels (urgent toasts stay longer)
- [ ] Custom positioning preferences
- [ ] Do Not Disturb mode

## Related Documentation
- `WEATHER_NOTIFICATIONS.md` - Weather notification system
- `NOTIFICATION_SYSTEM.md` - Base notification system
- `EVENT_NOTIFICATIONS.md` - Event notification feature

## Summary
The toast notification system provides a modern, non-intrusive way to keep users informed of important updates. It automatically polls for new notifications, displays them with beautiful animations, and integrates seamlessly with the existing notification system. 🎉
