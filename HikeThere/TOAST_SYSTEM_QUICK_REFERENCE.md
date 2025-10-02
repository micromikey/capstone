# 🚀 Toast Notification System - Quick Reference Guide

## For Developers

### File Locations

```
Action Toasts (User Actions)
├─ trails/show.blade.php              # Favorites, bookings, reviews
├─ components/community-dashboard     # Follow/unfollow
└─ notifications/index.blade.php      # Mark read, delete

Incoming Toasts (Real-time Notifications)
└─ components/toast-notification      # Weather, events, bookings, etc.
```

---

## Common Tasks

### 1. Testing Incoming Notifications

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

// Event notification
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

### 2. Testing Action Toasts

On trail page (show.blade.php):
```javascript
showToast('success', 'Trail added to favorites!');
showToast('error', 'Failed to submit review');
showToast('warning', 'Please log in first');
showToast('info', 'Trail guide downloaded');
```

### 3. Recompiling Assets

```bash
npm run build
```

### 4. Clearing Cache

```bash
php artisan cache:clear
php artisan view:clear
```

---

## Quick Customizations

### Change Toast Duration

**Action Toasts:**
```javascript
setTimeout(() => hideToast(toastId), 5000); // Change to 7000 for 7 seconds
```

**Incoming Toasts:**
```javascript
setTimeout(() => this.removeToast(id), 6000); // Change to 8000 for 8 seconds

// Also update CSS animation
.toast-progress-bar {
    animation: progressBar 6s linear forwards; // Change to 8s
}
```

### Change Polling Interval

In `toast-notification.blade.php`:
```javascript
setInterval(() => this.checkForNewNotifications(), 30000); // Change to 60000 for 1 minute
```

### Add New Icon

```javascript
getIcon(type) {
    const icons = {
        'your_type': `<svg class='w-6 h-6'>...</svg>`
    };
    return icons[type] || icons['system'];
}
```

### Add New Color Scheme

**Action Toasts:**
```javascript
switch(type) {
    case 'custom':
        return {
            gradient: 'from-pink-50 to-rose-50',
            icon: 'text-pink-600',
            iconBg: 'bg-pink-100',
            border: 'border-pink-300'
        };
}
```

**Incoming Toasts:**
```javascript
getStyles(type) {
    const styles = {
        'custom_type': {
            bg: 'bg-gradient-to-r from-pink-50 to-rose-50',
            border: 'border-pink-300',
            iconBg: 'bg-pink-100',
            iconColor: 'text-pink-600',
            progress: 'bg-pink-500',
            text: 'text-pink-900'
        }
    };
    return styles[type] || styles['system'];
}
```

---

## Notification Types Reference

### Incoming Toasts (Real-time)

| Type | Color | When to Use |
|------|-------|-------------|
| `weather` | Amber | Weather conditions, forecasts |
| `new_event` | Purple | Event announcements |
| `booking` | Blue | Booking confirmations, updates |
| `trail_update` | Green | Trail status changes |
| `security_alert` | Red | Security warnings |
| `system` | Gray | System messages |

### Action Toasts (User Feedback)

| Type | Color | When to Use |
|------|-------|-------------|
| `success` | Green | Successful operations |
| `error` | Red | Failed operations |
| `warning` | Yellow | Warnings, cautions |
| `info` | Blue | Information, tips |

---

## Troubleshooting

### Toast Not Appearing
```bash
# 1. Check console for errors
# 2. Verify Alpine.js loaded
console.log(Alpine);

# 3. Test manually
showToast('info', 'Test message');
```

### Animations Not Working
```bash
# 1. Recompile assets
npm run build

# 2. Clear cache
php artisan view:clear

# 3. Hard refresh browser (Ctrl+Shift+R)
```

### Polling Not Working
```bash
# 1. Check route exists
php artisan route:list | grep notifications.latest

# 2. Test API directly
curl http://yourapp.test/notifications/latest

# 3. Check console for errors
# Open browser console → Network tab
```

---

## Quick Checks

### ✅ Deployment Checklist

- [ ] Run `npm run build`
- [ ] Test each toast type
- [ ] Check on mobile devices
- [ ] Verify hover pause works
- [ ] Test progress bar animation
- [ ] Check auto-dismiss timing
- [ ] Verify click navigation
- [ ] Test stacking (multiple toasts)
- [ ] Check notification bell updates
- [ ] Test polling interval
- [ ] Clear browser cache
- [ ] Test in different browsers

---

## Common Patterns

### Creating Backend Notification

```php
use App\Services\NotificationService;

// In your controller
$notificationService = app(NotificationService::class);

// Weather notification
$notificationService->sendWeatherNotification($user, [
    'current_temp' => 27,
    'current_location' => 'Manila',
    'trail_temp' => 24,
    'trail_name' => 'Mt. Batulao'
]);

// Event notification
$notificationService->sendNewEventNotification($event, $hikers);

// Booking confirmation
$notificationService->sendBookingConfirmation($user, $booking);
```

### Session Flash to Toast (Automatic)

```php
// In controller
return redirect()->back()->with('success', 'Action completed!');
// Automatically converts to toast on pages with conversion script
```

---

## Performance Tips

1. **Limit simultaneous toasts** - Max 3-4 visible at once
2. **Optimize polling** - 30s is good balance
3. **Use appropriate durations** - 6s for complex info, 4s for simple
4. **Clean up properly** - Toasts auto-remove from DOM
5. **Minimize DOM queries** - Cache references when possible

---

## Browser Support

| Browser | Version | Status |
|---------|---------|--------|
| Chrome | 90+ | ✅ Full support |
| Firefox | 88+ | ✅ Full support |
| Safari | 14+ | ✅ Full support |
| Edge | 90+ | ✅ Full support |

---

## Key Features Summary

### All Toast Systems
- ✅ Auto-dismiss with timer
- ✅ Manual close button
- ✅ Stacking support
- ✅ Smooth animations
- ✅ Type-based styling
- ✅ Responsive design

### Incoming Toasts Only
- ✅ Hover to pause
- ✅ Click to navigate
- ✅ Progress bar
- ✅ Rich content (temps, badges)
- ✅ Automatic polling
- ✅ 6+ notification types

### Action Toasts Only
- ✅ Immediate feedback
- ✅ Session flash integration
- ✅ 4 standard types
- ✅ Page-specific contexts

---

## Documentation Files

| File | Purpose |
|------|---------|
| `TOAST_SYSTEM_DOCUMENTATION.md` | Trail page implementation |
| `COMMUNITY_TOAST_IMPLEMENTATION.md` | Community page implementation |
| `NOTIFICATIONS_PAGE_TOAST_SYSTEM.md` | Notifications page implementation |
| `INCOMING_NOTIFICATION_TOAST_SYSTEM.md` | Real-time toast implementation |
| `TOAST_SYSTEM_COMPLETE_SUMMARY.md` | Complete system overview |
| `TOAST_SYSTEM_BEFORE_AFTER.md` | Visual comparisons |
| `TOAST_SYSTEM_QUICK_REFERENCE.md` | This guide |

---

## Contact & Support

For issues or questions:
1. Check documentation files above
2. Review browser console for errors
3. Test with manual triggers
4. Check backend logs for API issues

---

## Version History

- **v1.0.0** - Initial enhanced implementation
  - Action toasts for trail, community, notifications pages
  - Incoming notification toasts with rich content
  - Comprehensive documentation

---

**Quick Access Commands**

```bash
# Rebuild assets
npm run build

# Clear Laravel cache
php artisan cache:clear && php artisan view:clear

# Test route
php artisan route:list | grep notifications

# View logs
tail -f storage/logs/laravel.log
```

---

**Status:** ✅ Production Ready
**Last Updated:** December 2024
