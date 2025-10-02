# Notifications System - HikeThere

## Overview
A complete notification system for HikeThere that allows you to send and manage in-app notifications to users.

## Features
- ✅ Database-backed notifications
- ✅ Multiple notification types (trail updates, security alerts, bookings, system)
- ✅ Read/Unread status tracking
- ✅ User preference integration
- ✅ Dropdown notification component
- ✅ Full notifications page with filters
- ✅ AJAX-based real-time updates
- ✅ Mark as read/unread functionality
- ✅ Bulk actions (mark all as read, clear read)

## Database Structure

The `notifications` table includes:
- `user_id` - Foreign key to users table
- `type` - Notification type (trail_update, security_alert, booking, system, etc.)
- `title` - Notification title
- `message` - Notification message
- `data` - JSON field for additional data (trail_id, links, etc.)
- `read_at` - Timestamp when notification was read (null = unread)
- `created_at` / `updated_at` - Timestamps

## Usage

### 1. Adding the Notification Dropdown to Navigation

Add this to your navigation header (e.g., `navigation-menu.blade.php` or `app.blade.php`):

```blade
<x-notification-dropdown />
```

Make sure Alpine.js is loaded in your layout.

### 2. Creating Notifications

#### Using the NotificationService (Recommended)

```php
use App\Services\NotificationService;

$notificationService = new NotificationService();

// Send a trail update
$notificationService->sendTrailUpdate($user, $trail, 'Trail conditions updated');

// Send a security alert
$notificationService->sendSecurityAlert($user, 'Security Alert', 'Suspicious activity detected');

// Send a booking confirmation
$notificationService->sendBookingConfirmation($user, $booking);

// Send a system notification
$notificationService->sendSystemNotification($user, 'Welcome!', 'Thanks for joining HikeThere');

// Send to multiple users
$users = User::where('user_type', 'hiker')->get();
$notificationService->createForMany($users, 'system', 'Announcement', 'System maintenance scheduled');
```

#### Direct Model Creation

```php
use App\Models\Notification;

Notification::create([
    'user_id' => $user->id,
    'type' => 'trail_update',
    'title' => 'Trail Closed',
    'message' => 'Mt. Pulag trail is temporarily closed due to weather',
    'data' => [
        'trail_id' => $trail->id,
        'trail_slug' => $trail->slug,
        'severity' => 'high'
    ]
]);
```

### 3. Notification Types

Available notification types:
- `trail_update` - Trail condition updates, closures, etc.
- `security_alert` - Security-related notifications
- `booking` - Booking confirmations and updates
- `system` - System announcements and general messages
- Custom types - You can create your own types

### 4. Checking User Notifications

```php
// Get all notifications
$notifications = $user->notifications;

// Get unread notifications
$unreadNotifications = $user->unreadNotifications;

// Get unread count
$count = $user->unreadNotificationsCount();

// Query with filters
$trailNotifications = $user->notifications()->ofType('trail_update')->get();
$recentUnread = $user->notifications()->unread()->take(5)->get();
```

### 5. Marking Notifications as Read

```php
// Mark single notification as read
$notification->markAsRead();

// Mark as unread
$notification->markAsUnread();

// Mark all user notifications as read
$notificationService->markAllAsRead($user);
// or
$user->notifications()->unread()->update(['read_at' => now()]);
```

### 6. Example Integration - Send Notification When Trail is Updated

In your Trail update controller:

```php
use App\Services\NotificationService;

public function update(Request $request, Trail $trail)
{
    $trail->update($request->validated());
    
    // Notify users who have bookings for this trail
    $notificationService = new NotificationService();
    $usersWithBookings = User::whereHas('bookings', function($query) use ($trail) {
        $query->where('trail_id', $trail->id);
    })->get();
    
    foreach ($usersWithBookings as $user) {
        $notificationService->sendTrailUpdate(
            $user, 
            $trail, 
            'The trail information has been updated. Please review the changes.'
        );
    }
    
    return redirect()->back()->with('success', 'Trail updated and users notified');
}
```

### 7. Example Integration - Welcome Notification on Registration

In your registration controller or event listener:

```php
use App\Services\NotificationService;

// After user is created
$notificationService = new NotificationService();
$notificationService->sendWelcomeNotification($user);
```

### 8. User Preferences Integration

The NotificationService automatically checks user preferences before sending notifications. Users can control their notification settings in the preferences page:

- `email_notifications` - Receive email notifications
- `push_notifications` - Receive push notifications (in-app)
- `trail_updates` - Receive trail update notifications
- `security_alerts` - Receive security alerts
- `newsletter` - Receive newsletter notifications

### 9. Available Routes

- `GET /notifications` - View all notifications page
- `GET /notifications/api/get` - Get notifications (AJAX)
- `POST /notifications/{id}/read` - Mark notification as read
- `POST /notifications/{id}/unread` - Mark notification as unread
- `POST /notifications/read-all` - Mark all as read
- `DELETE /notifications/{id}` - Delete notification
- `DELETE /notifications/read/clear` - Clear all read notifications

### 10. Cleanup Old Notifications

You can clean up old read notifications using the service:

```php
// Delete read notifications older than 30 days
$notificationService->deleteOldReadNotifications(30);
```

Consider adding this to a scheduled task in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $service = new NotificationService();
        $service->deleteOldReadNotifications(30);
    })->daily();
}
```

## Customization

### Adding New Notification Types

1. Add a new method to `NotificationService`:

```php
public function sendNewFeatureAlert(User $user, $featureName)
{
    return $this->create(
        $user,
        'new_feature',
        'New Feature Available',
        "Check out our new feature: {$featureName}",
        ['feature' => $featureName]
    );
}
```

2. Add icon styling in the notification dropdown component for the new type.

3. Add preference mapping in `NotificationService::shouldNotify()` if needed.

### Customizing Notification Icons

Edit `resources/views/components/notification-dropdown.blade.php` and add your custom icon templates.

## Next Steps (Optional Enhancements)

1. **Real-time Notifications** - Integrate Laravel Echo with Pusher or WebSockets for real-time push
2. **Email Notifications** - Send email versions of notifications using Laravel's mail system
3. **Push Notifications** - Implement browser push notifications using service workers
4. **Notification Preferences** - Allow users to customize which types of notifications they receive
5. **Notification Templates** - Create reusable notification templates for common scenarios

## Testing

Create some test notifications:

```php
use App\Services\NotificationService;

$service = new NotificationService();
$user = Auth::user();

// Create test notifications
$service->sendSystemNotification($user, 'Test Notification', 'This is a test message');
$service->sendSecurityAlert($user, 'Security Test', 'This is a test security alert');
```

Then visit `/notifications` to see them in action!

## Files Created

- `database/migrations/2025_10_02_012407_create_notifications_table.php`
- `app/Models/Notification.php`
- `app/Http/Controllers/NotificationController.php`
- `app/Services/NotificationService.php`
- `resources/views/components/notification-dropdown.blade.php`
- `resources/views/notifications/index.blade.php`
- Routes added to `routes/web.php`

## Summary

You now have a fully functional notification system! Users can:
- Receive notifications in the dropdown bell icon
- View all notifications on a dedicated page
- Filter notifications by status and type
- Mark notifications as read/unread
- Delete individual or bulk notifications
- Have their notification preferences respected
