<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "===== Weather Notification Test =====\n\n";

// Test 1: Check if OpenWeather API key is configured
echo "Test 1: Check OpenWeather API Key Configuration\n";
$apiKey = config('services.openweather.key');
if ($apiKey) {
    echo "✅ OpenWeather API key is configured\n";
    echo "   Key: " . substr($apiKey, 0, 10) . "...\n\n";
} else {
    echo "❌ OpenWeather API key is NOT configured\n";
    echo "   Please add OPENWEATHER_API_KEY to your .env file\n\n";
}

// Test 2: Find a test user (hiker)
echo "Test 2: Find Test User (Hiker)\n";
$user = DB::table('users')
    ->where('user_type', 'hiker')
    ->first();

if ($user) {
    echo "✅ Found test user: {$user->name} (ID: {$user->id})\n";
    echo "   Email: {$user->email}\n";
    echo "   Location: " . ($user->location ?? 'Not set') . "\n\n";
} else {
    echo "❌ No hiker users found in database\n\n";
    exit(1);
}

// Test 3: Check if user has itineraries
echo "Test 3: Check User Itineraries\n";
$itinerary = DB::table('itineraries')
    ->where('user_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->first();

if ($itinerary) {
    echo "✅ User has itineraries\n";
    echo "   Latest Itinerary ID: {$itinerary->id}\n";
    echo "   Created: {$itinerary->created_at}\n\n";
} else {
    echo "⚠️  User has no itineraries\n";
    echo "   Weather notification will only show current location\n\n";
}

// Test 4: Check if trail has coordinates (if itinerary exists)
if ($itinerary) {
    echo "Test 4: Check Trail Coordinates\n";
    $trail = DB::table('trails')
        ->where('id', $itinerary->trail_id)
        ->first();
    
    if ($trail) {
        echo "✅ Trail found: {$trail->name}\n";
        echo "   Latitude: " . ($trail->latitude ?? 'Not set') . "\n";
        echo "   Longitude: " . ($trail->longitude ?? 'Not set') . "\n\n";
        
        if (!$trail->latitude || !$trail->longitude) {
            echo "⚠️  Trail coordinates not set - weather fetch may fail\n\n";
        }
    } else {
        echo "❌ Trail not found\n\n";
    }
}

// Test 5: Check existing weather notifications
echo "Test 5: Check Existing Weather Notifications\n";
$existingNotifications = DB::table('notifications')
    ->where('user_id', $user->id)
    ->where('type', 'weather')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($existingNotifications->count() > 0) {
    echo "Found {$existingNotifications->count()} existing weather notification(s):\n";
    foreach ($existingNotifications as $notif) {
        $data = json_decode($notif->data, true);
        $createdAt = date('Y-m-d H:i:s', strtotime($notif->created_at));
        $isToday = date('Y-m-d', strtotime($notif->created_at)) === date('Y-m-d');
        
        echo "   - Created: {$createdAt}";
        if ($isToday) {
            echo " ⚠️  (TODAY - may prevent new notification)";
        }
        echo "\n";
        
        if (isset($data['current_temp'])) {
            echo "     Current: {$data['current_temp']}° in {$data['current_location']}\n";
        }
        if (isset($data['trail_temp'])) {
            echo "     Trail: {$data['trail_temp']}° in {$data['trail_name']}\n";
        }
    }
    echo "\n";
} else {
    echo "✅ No existing weather notifications - user will receive one on next login\n\n";
}

// Test 6: Manually trigger weather notification
echo "Test 6: Manually Trigger Weather Notification\n";
echo "Attempting to send weather notification...\n";

try {
    $userModel = App\Models\User::find($user->id);
    $weatherService = new App\Services\WeatherNotificationService(
        new App\Services\NotificationService()
    );
    
    // Check if should send
    if (!$weatherService->shouldSendWeatherNotification($userModel)) {
        echo "⚠️  Weather notification already sent today\n";
        echo "   Wait until tomorrow or delete today's notification to test\n\n";
    } else {
        $notification = $weatherService->sendLoginWeatherNotification($userModel);
        
        if ($notification) {
            echo "✅ Weather notification sent successfully!\n";
            echo "   Notification ID: {$notification->id}\n";
            echo "   Title: {$notification->title}\n";
            echo "   Message:\n";
            echo "   " . str_replace("\n", "\n   ", $notification->message) . "\n\n";
            
            $data = json_decode($notification->data, true);
            if ($data) {
                echo "   Weather Data:\n";
                if (isset($data['current_temp'])) {
                    echo "   - Current: {$data['current_temp']}° in {$data['current_location']} ({$data['current_condition']})\n";
                }
                if (isset($data['trail_temp'])) {
                    echo "   - Trail: {$data['trail_temp']}° in {$data['trail_name']} ({$data['trail_condition']})\n";
                }
                if (isset($data['itinerary_id'])) {
                    echo "   - Itinerary ID: {$data['itinerary_id']}\n";
                }
            }
            echo "\n";
        } else {
            echo "❌ Failed to send weather notification\n";
            echo "   Check Laravel logs for errors\n\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ Error sending weather notification:\n";
    echo "   " . $e->getMessage() . "\n\n";
}

// Test 7: Verify notification in dropdown format
echo "Test 7: Preview Notification Display Format\n";
$latestNotification = DB::table('notifications')
    ->where('user_id', $user->id)
    ->where('type', 'weather')
    ->orderBy('created_at', 'desc')
    ->first();

if ($latestNotification) {
    $data = json_decode($latestNotification->data, true);
    
    echo "Dropdown Preview:\n";
    echo "┌────────────────────────────────────────┐\n";
    echo "│ 🌤️  Weather Update                    │\n";
    echo "├────────────────────────────────────────┤\n";
    if (isset($data['current_temp'])) {
        $currentTemp = number_format($data['current_temp'], 1);
        echo "│ 🔶 {$currentTemp}° in {$data['current_location']}\n";
    }
    if (isset($data['trail_temp'])) {
        $trailTemp = number_format($data['trail_temp'], 1);
        echo "│ 🌲 {$trailTemp}° in {$data['trail_name']}\n";
    }
    echo "│                                        │\n";
    echo "│ View Itinerary →                       │\n";
    echo "└────────────────────────────────────────┘\n\n";
} else {
    echo "No weather notification found to preview\n\n";
}

// Summary
echo "===== Test Summary =====\n";
echo "API Key: " . ($apiKey ? "✅" : "❌") . "\n";
echo "Test User: " . ($user ? "✅" : "❌") . "\n";
echo "User Itinerary: " . ($itinerary ? "✅" : "⚠️") . "\n";
echo "Notification Sent: Check Test 6 results above\n\n";

echo "===== Next Steps =====\n";
echo "1. If API key is missing, add it to .env:\n";
echo "   OPENWEATHER_API_KEY=your_api_key_here\n\n";
echo "2. To test automatic sending on login:\n";
echo "   - Log out of the application\n";
echo "   - Log back in as a hiker user\n";
echo "   - Check the notification bell\n\n";
echo "3. To view notifications in the UI:\n";
echo "   - Visit /notifications route\n";
echo "   - Filter by 'Weather' type\n\n";
echo "4. To debug issues:\n";
echo "   - Check storage/logs/laravel.log\n";
echo "   - Verify queue worker is running (if using queues)\n";
echo "   - Test OpenWeather API manually\n\n";

echo "Test complete!\n";
