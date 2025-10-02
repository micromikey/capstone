# 🔔 Notification Preferences - Quick Reference

## For Developers

### Sending Notifications (The Right Way)

```php
use App\Services\NotificationService;

$notificationService = app(NotificationService::class);

// ✅ GOOD - Uses service methods that check preferences
$notificationService->sendTrailUpdate($user, $trail, 'Trail closed');
$notificationService->sendSecurityAlert($user, 'New Login', 'Login detected');
$notificationService->sendNewEventNotification($event);
$notificationService->sendWeatherNotification($user, $weatherData);

// ❌ BAD - Direct creation bypasses preference check
Notification::create([...]);  // Don't do this!
```

### Quick Preference Check

```php
// Check if user has a specific notification enabled
$user = Auth::user();

if ($user->preferences && $user->preferences->trail_updates) {
    // User wants trail updates
}

// Or use the helper method
if ($user->preferences && $user->preferences->hasNotification('trail_updates')) {
    // User wants trail updates
}
```

### Update User Preferences

```php
use App\Models\UserPreference;

UserPreference::updatePreferences($userId, [
    'email_notifications' => true,
    'push_notifications' => false,
    'trail_updates' => true,
    'security_alerts' => true,
    'newsletter' => false,
    // ... other preferences
]);
```

## Notification Type Mapping

| When You Call | Checks This Preference |
|--------------|------------------------|
| `sendTrailUpdate()` | `trail_updates` |
| `sendSecurityAlert()` | `security_alerts` |
| `sendNewEventNotification()` | `push_notifications` |
| `sendWeatherNotification()` | `push_notifications` |
| `sendBookingConfirmation()` | `push_notifications` |
| `sendSystemNotification()` | `push_notifications` |
| Newsletter emails | `newsletter` |

## Default Preferences (New Users)

```php
email_notifications   = true   ✅
push_notifications    = true   ✅
trail_updates         = true   ✅
security_alerts       = true   ✅
newsletter            = false  ❌
```

## Common Patterns

### 1. Send to Multiple Users (Respecting Preferences)

```php
// ✅ Automatically filters users based on preferences
$hikers = User::where('user_type', 'hiker')->get();
$notificationService->sendNewEventNotification($event, $hikers);
// Only sends to users who have push_notifications enabled
```

### 2. Check Before Expensive Operations

```php
// If notification won't be sent anyway, skip expensive work
if (!$user->preferences || !$user->preferences->weather) {
    return; // Don't fetch weather data
}

$weatherData = fetchExpensiveWeatherData();
$notificationService->sendWeatherNotification($user, $weatherData);
```

### 3. Security Alerts (Always Important)

```php
// Security alerts should generally be sent regardless
// But still respect user preference
$notificationService->sendSecurityAlert($user, 'Critical', 'Your password was changed');

// For CRITICAL security issues, you might want to send anyway:
Notification::create([
    'user_id' => $user->id,
    'type' => 'critical_security',
    'title' => 'Account Compromised',
    'message' => 'Immediate action required',
]);
```

## Testing Checklist

- [ ] User can view preferences at `/account/preferences`
- [ ] Unchecking preference saves as `false`
- [ ] Checking preference saves as `true`
- [ ] Trail update doesn't send when `trail_updates = false`
- [ ] Trail update DOES send when `trail_updates = true`
- [ ] Push notification doesn't send when `push_notifications = false`
- [ ] Specific preference overrides general preference
- [ ] New users get default preferences
- [ ] Reset to defaults works

## Troubleshooting

### Notifications not being blocked?

```php
// Check if NotificationService is being used
// ❌ Wrong:
Notification::create([...]);

// ✅ Correct:
$notificationService->create($user, 'type', 'title', 'message');
```

### User preferences not saving?

```php
// Check checkbox handling in controller
// ✅ Should use $request->has('field_name')
'trail_updates' => $request->has('trail_updates')
```

### Preferences showing wrong values?

```php
// Make sure to pass array to view, not model
// ✅ Correct:
$preferences = $user->preferences ? $user->preferences->toArray() : UserPreference::getDefaults();
```

## Routes

```php
GET  /account/preferences        → View preferences
POST /account/preferences        → Update preferences  
POST /account/preferences/reset  → Reset to defaults
GET  /account/preferences/export → Export user data
```

## Database

```sql
-- Check user preferences
SELECT * FROM user_preferences WHERE user_id = 1;

-- Count users by preference
SELECT 
    COUNT(*) as total_users,
    SUM(email_notifications) as email_enabled,
    SUM(push_notifications) as push_enabled,
    SUM(trail_updates) as trail_enabled
FROM user_preferences;

-- Find users who want trail updates
SELECT u.* 
FROM users u
JOIN user_preferences up ON u.id = up.user_id
WHERE up.trail_updates = 1;
```

## Files Modified

- ✅ `app/Services/NotificationService.php` - Fixed preference checking
- ✅ `app/Http/Controllers/AccountSettings/PreferencesController.php` - Fixed checkbox handling
- ✅ `resources/views/account/preferences.blade.php` - Enhanced UI

## Files Created

- 📄 `NOTIFICATION_PREFERENCES_INTEGRATION.md` - Full documentation
- 📄 `NOTIFICATION_PREFERENCES_SETUP.md` - Setup summary
- 📄 `NOTIFICATION_FLOW_DIAGRAM.md` - Visual flow diagram
- 📄 `test_notification_preferences.php` - Test script
- 📄 `NOTIFICATION_PREFERENCES_QUICK_REFERENCE.md` - This file

---

**Need help?** Check the full documentation in `NOTIFICATION_PREFERENCES_INTEGRATION.md`
