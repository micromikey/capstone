# Weather Notification Debugging Guide

## Issue: Weather notifications not appearing on login

### Changes Made

1. **Removed Queue Processing** (Primary Fix)
   - Changed `SendWeatherNotificationOnLogin` from queued to synchronous execution
   - Removed `ShouldQueue` interface and `InteractsWithQueue` trait
   - **Why**: Queued jobs require a queue worker (`php artisan queue:work`) to be running
   - **Impact**: Weather notifications now execute immediately on login

2. **Added Comprehensive Logging**
   - Added debug logs at every step of the login event handling
   - Logs user information, checks, and success/failure states
   - **Location**: Check `storage/logs/laravel.log` for debug output

3. **Added Test Route**
   - Created `/test-weather-notification` route for manual testing
   - Allows testing without logging out/in repeatedly

## How to Test

### Method 1: Login Test (Recommended)
1. Log out of the application
2. Log back in as a hiker user
3. Check the notification bell in the top navigation
4. Weather notification should appear immediately

### Method 2: Manual Test Route
1. Make sure you're logged in as a hiker
2. Visit: `http://your-app-url/test-weather-notification`
3. You'll see a JSON response indicating success or failure
4. Check the notification bell after visiting

### Method 3: Run Test Script
```bash
php test_weather_notifications.php
```

### Method 4: Check Logs
After attempting to log in, check the Laravel logs:

**Windows PowerShell:**
```powershell
Get-Content "storage\logs\laravel.log" -Tail 50
```

**Or open in editor:**
```powershell
code storage\logs\laravel.log
```

Look for these log entries:
- `SendWeatherNotificationOnLogin: Login event received`
- `SendWeatherNotificationOnLogin: User` (with user details)
- `SendWeatherNotificationOnLogin: Attempting to send weather notification`
- `SendWeatherNotificationOnLogin: Weather notification sent successfully`

## Common Issues & Solutions

### Issue 1: Event Not Firing
**Symptoms**: No logs appear in laravel.log when logging in

**Check:**
```bash
# Verify event listener is registered
php artisan event:list | findstr Login
```

**Solution:**
- Clear config cache: `php artisan config:clear`
- Clear route cache: `php artisan route:clear`
- Restart dev server

### Issue 2: "User is not a hiker, skipping"
**Symptoms**: Log shows user type check failed

**Check:**
```sql
SELECT id, name, email, user_type FROM users WHERE email = 'your@email.com';
```

**Solution:**
- Ensure the logged-in user has `user_type = 'hiker'`
- Organizations don't receive weather notifications

### Issue 3: "Weather notification already sent today"
**Symptoms**: Log shows daily limit reached

**Check:**
```sql
SELECT * FROM notifications 
WHERE user_id = YOUR_USER_ID 
AND type = 'weather' 
AND DATE(created_at) = CURDATE()
ORDER BY created_at DESC;
```

**Solution:**
- Delete today's notification to test again:
```sql
DELETE FROM notifications 
WHERE user_id = YOUR_USER_ID 
AND type = 'weather' 
AND DATE(created_at) = CURDATE();
```

### Issue 4: API Key Missing
**Symptoms**: Error about OpenWeather API

**Check `.env`:**
```env
OPENWEATHER_API_KEY=your_api_key_here
```

**Solution:**
- Add valid OpenWeather API key
- Clear config cache: `php artisan config:clear`

### Issue 5: No Itinerary
**Symptoms**: Only shows current location weather

**This is normal behavior!** Weather notifications work in two modes:
1. **With Itinerary**: Shows current location + trail weather
2. **Without Itinerary**: Shows only current location weather

**To get full weather notification:**
1. Create an itinerary for your user
2. Log out and log back in

### Issue 6: Wrong Location
**Symptoms**: Weather shows wrong city or defaults to Manila

**Check user profile:**
```sql
SELECT id, name, location FROM users WHERE id = YOUR_USER_ID;
```

**Supported Cities:**
- Manila, Quezon City, Cebu, Davao, Baguio, Tagaytay, Batangas

**Solution:**
- Update user location to one of the supported cities
- Or modify `WeatherNotificationService.php` to add more city mappings

## Verification Checklist

Run through this checklist to verify the system is working:

- [ ] OpenWeather API key is configured in `.env`
- [ ] User is a hiker (not organization)
- [ ] No weather notification sent today for this user
- [ ] Event listener is registered in `AppServiceProvider`
- [ ] Logs show "Login event received" when logging in
- [ ] Notification appears in bell dropdown after login
- [ ] Notification shows on `/notifications` page
- [ ] Weather data is accurate and formatted correctly

## Expected Behavior

### On Login:
1. User logs in successfully
2. Laravel fires `Illuminate\Auth\Events\Login` event
3. `SendWeatherNotificationOnLogin` listener catches the event
4. Listener checks if user is a hiker
5. Listener checks if notification already sent today
6. `WeatherNotificationService` fetches weather data:
   - Current location weather from user's location
   - Latest itinerary trail weather (if exists)
7. Notification is created in database
8. Notification appears in bell dropdown (red badge with count)
9. User can view in dropdown or full notifications page

### What You Should See:

**Notification Bell:**
- Red badge with number of unread notifications
- Click to open dropdown
- Weather notification at top with:
  - 🌤️ Weather Update
  - Temperature in current location (amber color)
  - Temperature in trail location (green color)
  - "View Itinerary" link

**Notifications Page:**
- Weather notification with amber cloud icon
- Large temperature displays
- "View Itinerary" button
- Filter by "Weather" type available

## Manual Trigger via Tinker

If you want to test the service directly:

```bash
php artisan tinker
```

```php
use App\Models\User;
use App\Services\WeatherNotificationService;
use App\Services\NotificationService;

$user = User::find(1); // Replace with your user ID
$service = new WeatherNotificationService(new NotificationService());

// Check if should send
$service->shouldSendWeatherNotification($user); // Should return true

// Send notification
$notification = $service->sendLoginWeatherNotification($user);

// Verify
$notification->id; // Should show notification ID
$notification->title; // "Weather Update"
$notification->message; // Temperature string
```

## Database Queries for Debugging

### Check all weather notifications:
```sql
SELECT 
    n.id,
    n.user_id,
    u.name as user_name,
    n.title,
    n.message,
    n.created_at,
    n.read_at
FROM notifications n
JOIN users u ON n.user_id = u.id
WHERE n.type = 'weather'
ORDER BY n.created_at DESC;
```

### Check today's weather notifications:
```sql
SELECT * FROM notifications 
WHERE type = 'weather' 
AND DATE(created_at) = CURDATE();
```

### Delete all weather notifications (for testing):
```sql
DELETE FROM notifications WHERE type = 'weather';
```

## API Response Examples

### Successful Weather Notification Data:
```json
{
    "current_temp": 26.74,
    "current_location": "Manila",
    "trail_temp": 19.4,
    "trail_name": "Mt. Pulag",
    "itinerary_id": 1,
    "weather_icon": "01d",
    "current_condition": "Clear",
    "trail_condition": "Cloudy"
}
```

### Without Itinerary:
```json
{
    "current_temp": 26.74,
    "current_location": "Manila",
    "weather_icon": "01d",
    "current_condition": "Clear"
}
```

## Performance Notes

- **Synchronous Execution**: Weather notifications now run during login
- **API Call Time**: ~1-3 seconds for OpenWeather API responses
- **Impact on Login**: Minimal (happens after authentication completes)
- **Daily Limit**: One notification per user per day

## Re-enabling Queue Processing (Production)

If you want to move weather notifications back to queued processing for production:

1. **Restore `ShouldQueue` interface:**
```php
class SendWeatherNotificationOnLogin implements ShouldQueue
{
    use InteractsWithQueue;
    // ... rest of class
}
```

2. **Start queue worker:**
```bash
php artisan queue:work
```

3. **Use Supervisor (recommended for production):**
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
directory=/path/to/project
user=www-data
numprocs=1
autostart=true
autorestart=true
```

## Contact for Support

If you're still experiencing issues after following this guide:

1. Check `storage/logs/laravel.log` for error details
2. Verify all checklist items above
3. Run the test script: `php test_weather_notifications.php`
4. Test the manual route: `/test-weather-notification`

---

**Last Updated**: After removing ShouldQueue for immediate execution
**Status**: Weather notifications should now fire immediately on login for hiker users
