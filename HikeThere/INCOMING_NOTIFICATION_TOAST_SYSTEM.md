# 🔔 Enhanced Incoming Notification Toast System

## Overview
The incoming notification toast system has been completely redesigned with a modern, professional appearance. These toasts appear in the top-right corner when new notifications arrive (weather alerts, booking confirmations, new events, trail updates, and security alerts).

## Features

### 🎨 Visual Design
- **Gradient backgrounds** - Beautiful gradient colors for each notification type
- **Circular icon badges** - 3D-style circular backgrounds with shadow effects
- **Smooth animations** - Slide-in from right with bounce effect
- **Hover effects** - Scale up on hover with enhanced shadow
- **Progress bar** - Visual countdown timer at the bottom
- **Rounded corners** - Modern rounded-xl design

### ⏱️ Smart Interactions
- **Auto-dismiss** - Automatically disappears after 6 seconds
- **Hover to pause** - Progress bar pauses when you hover over the toast
- **Click to navigate** - Clicking the toast takes you to the notifications page
- **Close button** - Manual dismiss with close button (×)
- **Stacking support** - Multiple toasts stack vertically with spacing

### 📱 Responsive Design
- Fixed width of 420px on desktop
- Positioned at top-right corner
- Proper z-index (50) to appear above all content
- Smooth transitions and animations

## Notification Types & Styles

### 🌤️ Weather Notifications
**Color Scheme:** Amber/Yellow gradient
- Icon: Cloud with sun
- Background: `from-amber-50 to-yellow-50`
- Progress bar: Amber-500
- **Special Display:**
  - Current temperature in large bold text
  - Location name
  - Trail temperature (if available)
  - Trail name (if available)

**Example:**
```
☁️ Weather Alert
Current conditions for your upcoming hike

27° in Manila
24° at Mt. Batulao
```

---

### 🎉 New Event Notifications
**Color Scheme:** Purple/Pink gradient
- Icon: Calendar
- Background: `from-purple-50 to-pink-50`
- Progress bar: Purple-500
- **Special Display:**
  - Event title and description
  - Trail location badge (green pill)
  - Price badge (purple pill) or "FREE EVENT" (blue pill)

**Example:**
```
📅 New Event: Mountain Cleanup Day
Join us for a community trail cleanup

🏔️ Mt. Pulag    💰 ₱500
```

---

### ✅ Booking Confirmations
**Color Scheme:** Blue/Cyan gradient
- Icon: Clipboard with checkmark
- Background: `from-blue-50 to-cyan-50`
- Progress bar: Blue-500
- **Special Display:**
  - Confirmation message
  - Trail/Event name in badge with checkmark

**Example:**
```
📋 Booking Confirmed
Your hiking reservation has been confirmed

✓ Mt. Pulag Summit Trail
```

---

### 🏔️ Trail Update Notifications
**Color Scheme:** Green/Emerald gradient
- Icon: Trending up arrow
- Background: `from-green-50 to-emerald-50`
- Progress bar: Green-500
- Standard title and message display

**Example:**
```
📈 Trail Status Updated
Mt. Batulao trail conditions have been updated
```

---

### ⚠️ Security Alert Notifications
**Color Scheme:** Red/Orange gradient
- Icon: Warning triangle
- Background: `from-red-50 to-orange-50`
- Progress bar: Red-500
- Standard title and message display

**Example:**
```
⚠️ Security Alert
Unusual login attempt detected from a new device
```

---

### ℹ️ System Notifications
**Color Scheme:** Gray/Slate gradient (default)
- Icon: Information circle
- Background: `from-gray-50 to-slate-50`
- Progress bar: Gray-500
- Standard title and message display

**Example:**
```
ℹ️ System Notification
Your profile has been successfully updated
```

## How It Works

### 1. **Automatic Polling**
```javascript
// Checks for new notifications every 30 seconds
setInterval(() => this.checkForNewNotifications(), 30000);
```

### 2. **Backend API**
- Route: `notifications.latest`
- Fetches the most recent unread notification
- Returns notification data with type, title, message, and custom data

### 3. **Display Logic**
```javascript
addToast(data) {
    // Creates toast with unique ID
    // Sets 6-second auto-dismiss timer
    // Handles pause/resume on hover
}
```

### 4. **Progress Bar Animation**
- CSS animation runs for 6 seconds
- Pauses when mouse hovers over toast
- Resumes when mouse leaves

### 5. **Event Integration**
- Dispatches `notification-received` event
- Updates notification bell count
- Refreshes notification dropdown

## File Location
```
resources/views/components/toast-notification.blade.php
```

## Included In
```
resources/views/navigation-menu.blade.php
```
(Automatically loads on every page with navigation)

## Technical Details

### CSS Animations
```css
@keyframes slideInRight {
    /* Slides in from right with fade */
}

@keyframes progressBar {
    /* Countdown timer effect */
}
```

### Alpine.js Data Structure
```javascript
{
    id: 1,                    // Unique identifier
    type: 'weather',          // Notification type
    title: 'Weather Alert',   // Main heading
    message: 'Description',   // Body text
    data: {                   // Custom data
        current_temp: 27,
        trail_name: 'Mt. Pulag'
    },
    show: true,               // Visibility state
    isPaused: false,          // Progress bar state
    timeoutId: null          // Auto-dismiss timer
}
```

### Notification Data Structure
Backend should return:
```json
{
    "notification": {
        "type": "weather|new_event|booking|trail_update|security_alert|system",
        "title": "Notification Title",
        "message": "Notification message text",
        "data": {
            // Custom fields based on type
            "current_temp": 27,
            "current_location": "Manila",
            "trail_temp": 24,
            "trail_name": "Mt. Batulao",
            "itinerary_id": 123,
            "event_slug": "mountain-cleanup",
            "is_free": false,
            "price": 500
        }
    }
}
```

## Customization

### Changing Duration
```javascript
// In addToast() method, change 6000 to desired milliseconds
setTimeout(() => this.removeToast(id), 6000); // 6 seconds
```

### Changing Polling Interval
```javascript
// Change 30000 to desired milliseconds
setInterval(() => this.checkForNewNotifications(), 30000); // 30 seconds
```

### Adding New Notification Types

1. **Add icon in getIcon() method:**
```javascript
'custom_type': `<svg class='w-6 h-6'>...</svg>`
```

2. **Add styles in getStyles() method:**
```javascript
'custom_type': {
    bg: 'bg-gradient-to-r from-color-50 to-color-50',
    border: 'border-color-300',
    iconBg: 'bg-color-100',
    iconColor: 'text-color-600',
    progress: 'bg-color-500',
    text: 'text-color-900'
}
```

3. **Add custom display in template:**
```html
<template x-if="toast.type === 'custom_type'">
    <div class="mt-3">
        <!-- Custom content here -->
    </div>
</template>
```

## Browser Compatibility
- Chrome 90+ ✅
- Firefox 88+ ✅
- Safari 14+ ✅
- Edge 90+ ✅

Requires:
- CSS Grid support
- CSS Animations
- Alpine.js
- Fetch API

## Testing

### Manual Testing
Open browser console and run:
```javascript
// Weather notification
window.dispatchEvent(new CustomEvent('show-toast', {
    detail: {
        type: 'weather',
        title: 'Weather Alert',
        message: 'Current conditions for your upcoming hike',
        data: {
            current_temp: 27,
            current_location: 'Manila',
            trail_temp: 24,
            trail_name: 'Mt. Batulao'
        }
    }
}));

// New event notification
window.dispatchEvent(new CustomEvent('show-toast', {
    detail: {
        type: 'new_event',
        title: 'New Event: Mountain Cleanup',
        message: 'Join us for a community trail cleanup',
        data: {
            trail_name: 'Mt. Pulag',
            is_free: false,
            price: 500
        }
    }
}));

// Booking notification
window.dispatchEvent(new CustomEvent('show-toast', {
    detail: {
        type: 'booking',
        title: 'Booking Confirmed',
        message: 'Your hiking reservation has been confirmed',
        data: {
            trail_name: 'Mt. Pulag Summit Trail'
        }
    }
}));
```

## Advantages Over Previous System

### Before ❌
- Basic flat design
- Simple border colors
- 5-second timeout
- No pause on hover
- Basic slide animation
- Smaller icons
- No progress indicator

### After ✅
- Modern gradient backgrounds
- 3D circular icon badges
- 6-second timeout
- Pause/resume on hover
- Bounce slide animation + scale on hover
- Larger, clearer icons
- Visual progress bar countdown
- Enhanced shadow effects
- Better spacing and typography
- Richer data display (weather temps, event badges, etc.)

## Related Files
- `resources/views/components/toast-notification.blade.php` - Main component
- `resources/views/components/notification-dropdown.blade.php` - Bell dropdown
- `resources/views/navigation-menu.blade.php` - Includes toast component
- `app/Services/NotificationService.php` - Creates notifications
- `app/Http/Controllers/NotificationController.php` - API endpoints

## Support
For issues or questions, please refer to:
- Trail show page toast: `TOAST_SYSTEM_DOCUMENTATION.md`
- Community dashboard toast: `COMMUNITY_TOAST_IMPLEMENTATION.md`
- Notifications page toast: `NOTIFICATIONS_PAGE_TOAST_SYSTEM.md`
