# Notification Preferences Integration

## Overview
The notification preferences system is now fully integrated with the HikeThere notification system. Users can control which types of notifications they receive through their account preferences.

## Features

### Notification Preference Types

1. **Email Notifications** (`email_notifications`)
   - Controls whether the user receives email notifications
   - Default: `true`

2. **Push Notifications** (`push_notifications`)
   - Master control for in-app push notifications
   - Affects: system notifications, events, weather, bookings
   - Default: `true`

3. **Trail Updates** (`trail_updates`)
   - Controls notifications about trail condition changes, closures, etc.
   - Default: `true`

4. **Security Alerts** (`security_alerts`)
   - Controls important security notifications (login attempts, password changes, etc.)
   - Default: `true` (recommended to keep enabled)

5. **Newsletter** (`newsletter`)
   - Controls marketing and newsletter emails
   - Default: `false`

## How It Works

### 1. User Preferences Storage
Preferences are stored in the `user_preferences` table with a one-to-one relationship to users:

```php
// Access user preferences
$user = Auth::user();
$preferences = $user->preferences;

// Check specific preference
if ($preferences && $preferences->trail_updates) {
    // Send trail update notification
}
```

### 2. Notification Type Mapping
The `NotificationService` automatically checks user preferences before creating notifications:

```php
protected function shouldNotify($preferences, string $type): bool
{
    $typeMapping = [
        'trail_update' => 'trail_updates',
        'security_alert' => 'security_alerts',
        'newsletter' => 'newsletter',
        'new_event' => 'push_notifications',
        'weather' => 'push_notifications',
        'booking' => 'push_notifications',
        'system' => 'push_notifications',
    ];
    
    // Returns false if user has disabled this notification type
}
```

### 3. Automatic Preference Checking
When creating a notification, the service automatically checks preferences:

```php
public function create(User $user, string $type, string $title, string $message, array $data = [])
{
    // Check user preferences before creating notification
    $preferences = $user->preferences;
    
    if ($preferences && !$this->shouldNotify($preferences, $type)) {
        return null; // Don't create notification if user has it disabled
    }
    
    // Create notification...
}
```

## Usage Examples

### Sending a Trail Update
```php
use App\Services\NotificationService;

$notificationService = app(NotificationService::class);
$notificationService->sendTrailUpdate($user, $trail, 'Trail conditions have changed');
// Will only send if user has trail_updates enabled
```

### Sending a Security Alert
```php
$notificationService->sendSecurityAlert($user, 'New Login', 'Login from new device detected');
// Will only send if user has security_alerts enabled
```

### Sending an Event Notification
```php
$notificationService->sendNewEventNotification($event);
// Will only send to users who have push_notifications enabled
```

### Sending Weather Notifications
```php
$notificationService->sendWeatherNotification($user, $weatherData);
// Will only send if user has push_notifications enabled
```

## User Interface

### Preferences Page
Users can manage their preferences at `/account/preferences`:

- **Notification Preferences Section**: Control all notification types
- **Privacy Settings Section**: Control profile visibility
- **Account Settings Section**: Timezone, language, 2FA

### Updating Preferences
The form uses checkboxes that properly handle checked/unchecked states:

```php
<input type="checkbox" name="email_notifications" value="1" 
    {{ $preferences['email_notifications'] ?? true ? 'checked' : '' }}>
```

### Default Values
When a user first registers, default preferences are used:
- Email Notifications: ✓ Enabled
- Push Notifications: ✓ Enabled
- Trail Updates: ✓ Enabled
- Security Alerts: ✓ Enabled
- Newsletter: ✗ Disabled

## API Reference

### NotificationService Methods

#### `create($user, $type, $title, $message, $data = [])`
Creates a notification for a user (respects preferences)

**Parameters:**
- `$user` (User): The user to notify
- `$type` (string): Notification type (trail_update, security_alert, etc.)
- `$title` (string): Notification title
- `$message` (string): Notification message
- `$data` (array): Additional data

**Returns:** Notification|null

#### `sendTrailUpdate($user, $trail, $updateMessage)`
Sends a trail update notification

#### `sendSecurityAlert($user, $alertTitle, $alertMessage)`
Sends a security alert notification

#### `sendNewEventNotification($event, $hikers = null)`
Sends event notification to hikers

#### `sendWeatherNotification($user, $weatherData)`
Sends weather notification

### UserPreference Methods

#### `hasNotification($type)`
Check if user has specific notification enabled

```php
$user->preferences->hasNotification('trail_updates'); // Returns boolean
```

#### `updatePreferences($userId, $preferences)`
Update or create user preferences

```php
UserPreference::updatePreferences($userId, [
    'email_notifications' => true,
    'push_notifications' => false,
    // ...
]);
```

## Database Schema

```sql
CREATE TABLE user_preferences (
    id BIGINT PRIMARY KEY,
    user_id BIGINT UNIQUE,
    
    -- Notification preferences
    email_notifications BOOLEAN DEFAULT TRUE,
    push_notifications BOOLEAN DEFAULT TRUE,
    trail_updates BOOLEAN DEFAULT TRUE,
    security_alerts BOOLEAN DEFAULT TRUE,
    newsletter BOOLEAN DEFAULT FALSE,
    
    -- Privacy settings
    profile_visibility ENUM('public', 'friends', 'private') DEFAULT 'public',
    show_email BOOLEAN DEFAULT FALSE,
    show_phone BOOLEAN DEFAULT FALSE,
    show_location BOOLEAN DEFAULT TRUE,
    show_birth_date BOOLEAN DEFAULT FALSE,
    show_hiking_preferences BOOLEAN DEFAULT TRUE,
    
    -- Account settings
    two_factor_required BOOLEAN DEFAULT FALSE,
    timezone VARCHAR(50) DEFAULT 'UTC',
    language VARCHAR(10) DEFAULT 'en',
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Routes

```php
// View preferences
Route::get('/account/preferences', [PreferencesController::class, 'index'])
    ->name('preferences.index');

// Update preferences
Route::post('/account/preferences', [PreferencesController::class, 'update'])
    ->name('preferences.update');

// Reset to defaults
Route::post('/account/preferences/reset', [PreferencesController::class, 'reset'])
    ->name('preferences.reset');

// Export user data (GDPR)
Route::get('/account/preferences/export', [PreferencesController::class, 'export'])
    ->name('preferences.export');
```

## Testing

### Manual Testing
1. Navigate to `/account/preferences`
2. Uncheck "Trail Updates"
3. Save preferences
4. Try to trigger a trail update notification
5. Verify no notification is created

### Programmatic Testing
```php
// Create test user with preferences
$user = User::factory()->create();
UserPreference::updatePreferences($user->id, [
    'trail_updates' => false,
    'push_notifications' => true,
]);

// Try to send trail update
$notification = $notificationService->sendTrailUpdate($user, $trail, 'Test');

// Should return null because trail_updates is disabled
assert($notification === null);
```

## Best Practices

1. **Always respect user preferences**: Use NotificationService methods that automatically check preferences
2. **Security alerts should be carefully considered**: Allow users to disable them but warn about implications
3. **Provide clear descriptions**: Help users understand what each preference controls
4. **Use appropriate notification types**: Map notification types correctly to preference settings
5. **Test preference changes**: Verify notifications respect user choices

## Future Enhancements

- [ ] Email notification frequency settings (instant, daily digest, weekly)
- [ ] Quiet hours (don't send notifications during specific times)
- [ ] Notification channels (email, SMS, in-app)
- [ ] Category-specific preferences (difficulty level, location-based)
- [ ] Notification preview before subscribing
