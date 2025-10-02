# Weather Notifications - Feature Documentation

## Overview
Weather notifications are automatically sent to users when they log in, showing the current weather in their location and the weather at their latest itinerary's trail location.

## Features
✅ Automatic weather notification on login  
✅ Current location weather display  
✅ Latest itinerary trail weather display  
✅ Beautiful temperature display with color coding  
✅ Direct link to view itinerary  
✅ Only sent once per day per user  
✅ Respects user notification preferences  

## How It Works

### 1. Login Trigger
When a user logs in, the system:
1. Checks if they're a hiker (organizations don't get weather notifications)
2. Checks if they've already received a weather notification today
3. Fetches current location weather
4. Fetches latest itinerary trail weather (if they have one)
5. Creates a notification with both temperatures

### 2. Weather Data Sources
- **Current Location**: Uses user's location from profile, defaults to Manila if not set
- **Trail Weather**: Uses coordinates from the trail in their latest itinerary
- **API**: OpenWeather API for real-time weather data

### 3. Notification Display

The weather notification shows a special format:
```
Weather Update
26.74° in Manila
19.4° in Mt. Pulag
View Itinerary →
```

- **Current Location Temperature**: Displayed in amber color
- **Trail Temperature**: Displayed in green color
- **View Itinerary Link**: Quick access to the itinerary details

## Visual Design

### Dropdown Display
- **Icon**: Amber cloud icon
- **Temperature Format**: Large, bold numbers followed by location
- **Colors**: 
  - Current temp: Amber (#F59E0B)
  - Trail temp: Green (#059669)

### Full Page Display
- **Icon**: Amber cloud (🌤️)
- **Temperature Size**: 2xl font for emphasis
- **Layout**: Vertical stack with clear separation
- **Actions**: View Itinerary button prominently displayed

## Technical Implementation

### Services

#### WeatherNotificationService
Located: `app/Services/WeatherNotificationService.php`

Methods:
- `sendLoginWeatherNotification(User $user)` - Main entry point
- `prepareWeatherData(User $user)` - Fetches and formats weather data
- `getCurrentLocationWeather(User $user)` - Gets current location weather
- `getTrailWeather($trail)` - Gets trail weather
- `fetchWeatherFromAPI($lat, $lng)` - Calls OpenWeather API
- `shouldSendWeatherNotification(User $user)` - Prevents duplicate notifications

#### NotificationService Updates
New method: `sendWeatherNotification(User $user, array $weatherData)`

### Event Listener

**SendWeatherNotificationOnLogin**
- Listens to: `Illuminate\Auth\Events\Login`
- Triggered: On every user login
- Queued: Yes (runs asynchronously)

### Database Structure

Notification record for weather:
```php
[
    'type' => 'weather',
    'title' => 'Weather Update',
    'message' => '26.74° in Manila\n19.4° in Mt. Pulag',
    'data' => [
        'current_temp' => 26.74,
        'current_location' => 'Manila',
        'trail_temp' => 19.4,
        'trail_name' => 'Mt. Pulag',
        'itinerary_id' => 1,
        'weather_icon' => '01d',
        'current_condition' => 'Clear',
        'trail_condition' => 'Cloudy'
    ]
]
```

### Location Mapping

The system includes a built-in mapping for common Philippine cities:
- Manila
- Quezon City
- Cebu
- Davao  
- Baguio
- Tagaytay
- Batangas

If the user's location doesn't match these, it defaults to Manila.

## Configuration

### Environment Variables

Ensure `OPENWEATHER_API_KEY` is set in your `.env` file:
```env
OPENWEATHER_API_KEY=your_api_key_here
```

### User Requirements

For best results, users should:
1. Set their location in their profile
2. Have at least one itinerary created
3. Have notification preferences enabled

## Usage Examples

### Manual Trigger
```php
use App\Services\WeatherNotificationService;
use App\Services\NotificationService;

$weatherService = new WeatherNotificationService(
    new NotificationService()
);

$weatherService->sendLoginWeatherNotification($user);
```

### Check if Should Send
```php
if ($weatherService->shouldSendWeatherNotification($user)) {
    $weatherService->sendLoginWeatherNotification($user);
}
```

### Custom Weather Data
```php
$notificationService->sendWeatherNotification($user, [
    'current_temp' => 28.5,
    'current_location' => 'Cebu',
    'trail_temp' => 20.1,
    'trail_name' => 'Mt. Apo',
    'itinerary_id' => 5
]);
```

## Testing

### Test Weather Notification on Login
1. Make sure you have an OpenWeather API key configured
2. Create a user with a location set
3. Create an itinerary for that user
4. Log out and log back in
5. Check the notification bell - you should see a weather notification

### Manual Test Script
```php
// Run in tinker or create a test route
php artisan tinker

use App\Services\WeatherNotificationService;
use App\Services\NotificationService;
use App\Models\User;

$user = User::find(1);
$service = new WeatherNotificationService(new NotificationService());
$service->sendLoginWeatherNotification($user);
```

## Troubleshooting

### Notification Not Appearing
1. Check if user is a hiker (organizations don't get weather notifications)
2. Verify notification wasn't already sent today
3. Check Laravel logs for API errors
4. Verify OpenWeather API key is valid
5. Ensure user has notification preferences enabled

### Weather Data Not Loading
1. Check OpenWeather API key
2. Verify trail has coordinates set
3. Check network connectivity
4. Review logs for API timeout issues

### Wrong Location
1. Update user's location in their profile
2. Verify location string matches city mapping
3. Check if coordinates are being resolved correctly

## Performance Considerations

- Weather notifications are queued (asynchronous)
- API calls have 5-second timeout
- Only fetches weather once per day per user
- Caches weather data in notification record
- Fails gracefully if API is unavailable

## Future Enhancements

Possible improvements:
1. **More Detailed Weather**: Add humidity, wind speed, UV index
2. **Weather Alerts**: Warn about extreme conditions
3. **Multi-Day Forecast**: Show weather for next few days
4. **Trail-Specific Alerts**: Notify about weather affecting specific trails
5. **Customizable Timing**: Let users choose when to receive weather updates
6. **Weather Icons**: Display visual weather condition icons
7. **Historical Data**: Compare with typical weather for the season
8. **Push Notifications**: Send as browser/mobile push notifications

## Files Created/Modified

### New Files
- `app/Services/WeatherNotificationService.php`
- `app/Listeners/SendWeatherNotificationOnLogin.php`

### Modified Files
- `app/Services/NotificationService.php` - Added `sendWeatherNotification()` method
- `app/Providers/AppServiceProvider.php` - Registered login event listener
- `resources/views/components/notification-dropdown.blade.php` - Added weather notification styling
- `resources/views/notifications/index.blade.php` - Added weather notification display

## API Reference

### WeatherNotificationService

```php
// Send weather notification on login
$service->sendLoginWeatherNotification(User $user): ?Notification

// Check if notification should be sent
$service->shouldSendWeatherNotification(User $user): bool

// Get current location weather
protected function getCurrentLocationWeather(User $user): ?array

// Get trail weather
protected function getTrailWeather($trail): ?array

// Fetch weather from API
protected function fetchWeatherFromAPI(float $lat, float $lng): ?array
```

### NotificationService

```php
// Send weather notification
$service->sendWeatherNotification(User $user, array $weatherData): ?Notification
```

## Summary

The weather notification feature provides users with relevant, timely weather information when they log in, helping them plan their hiking activities. The system is designed to be:

- **Automatic**: No manual action required
- **Smart**: Only sends once per day
- **Reliable**: Handles API failures gracefully
- **Beautiful**: Clear, color-coded temperature display
- **Fast**: Queued processing doesn't slow down login

Perfect for keeping hikers informed about conditions at their destination!

---

**Note**: Make sure your OpenWeather API key has sufficient quota for the number of users you expect. The free tier allows 1,000 calls/day, which should be sufficient for most applications.
