# Quick Start - Notifications System

## 🚀 Getting Started

### 1. View Notifications
- Visit `/notifications` to see all your notifications
- Click the bell icon (🔔) in the navigation bar to see recent notifications

### 2. Test the System

Run the test script to create sample notifications:
```bash
php test_notifications.php
```

### 3. Send Your First Notification

```php
use App\Services\NotificationService;

$service = new NotificationService();
$user = Auth::user(); // or any user

$service->sendSystemNotification(
    $user,
    'Hello!',
    'This is your first notification'
);
```

## 📋 Common Use Cases

### Trail Update
```php
$service->sendTrailUpdate($user, $trail, 'Trail is now open!');
```

### Security Alert
```php
$service->sendSecurityAlert($user, 'Password Changed', 'Your password was successfully updated.');
```

### Booking Confirmation
```php
$service->sendBookingConfirmation($user, $booking);
```

### Custom Notification
```php
$service->create($user, 'custom_type', 'Title', 'Message', ['key' => 'value']);
```

## 🔧 Integration Points

### After User Registration
```php
// In RegisteredUserController or similar
$service->sendWelcomeNotification($user);
```

### When Trail is Updated
```php
// In TrailController@update
$affectedUsers = User::whereHas('bookings', function($q) use ($trail) {
    $q->where('trail_id', $trail->id);
})->get();

foreach ($affectedUsers as $user) {
    $service->sendTrailUpdate($user, $trail, 'Trail info updated');
}
```

### On Password Change
```php
// In PasswordController
$service->sendSecurityAlert($user, 'Password Changed', 'Your password was updated successfully');
```

## 📱 User Features

Users can:
- ✅ View all notifications in dropdown (bell icon)
- ✅ See full notification list at `/notifications`
- ✅ Filter by read/unread status
- ✅ Filter by notification type
- ✅ Mark as read/unread
- ✅ Delete notifications
- ✅ Clear all read notifications
- ✅ See unread count badge

## 🎨 Notification Types

- `trail_update` - Green icon, for trail-related updates
- `security_alert` - Red icon, for security/account alerts
- `booking` - Blue icon, for booking confirmations
- `system` - Gray icon, for general system messages
- Custom types - Add your own!

## 📊 Statistics

```php
// Get notification counts
$total = $user->notifications()->count();
$unread = $user->unreadNotificationsCount();
$read = $user->notifications()->read()->count();

// Get recent notifications
$recent = $user->notifications()->take(10)->get();

// Get by type
$trailNotifications = $user->notifications()->ofType('trail_update')->get();
```

## 🔔 Next: Add to Your Navigation

The notification bell is already added to `navigation-menu.blade.php`!

If you need to add it elsewhere:
```blade
<x-notification-dropdown />
```

## 📝 Need Help?

See `NOTIFICATIONS_SYSTEM.md` for complete documentation.

## 🧪 Test Commands

```bash
# Run test script
php test_notifications.php

# Clear cache if needed
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

Happy notifying! 🎉
