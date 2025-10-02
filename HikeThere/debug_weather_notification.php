<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\WeatherNotificationService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

echo "===== Weather Notification Debug =====\n\n";

// Get the first hiker user
echo "Finding hiker user...\n";
$user = User::where('user_type', 'hiker')->first();

if (!$user) {
    echo "❌ No hiker user found!\n";
    exit(1);
}

echo "✅ Found user: {$user->name} (ID: {$user->id})\n";
echo "   Email: {$user->email}\n";
echo "   Location: " . ($user->location ?? 'Not set') . "\n\n";

// Check for latest itinerary
echo "Checking for latest itinerary...\n";
$itinerary = $user->latestItinerary;

if ($itinerary) {
    echo "✅ Found itinerary ID: {$itinerary->id}\n";
    echo "   Trail ID: {$itinerary->trail_id}\n";
    
    if ($itinerary->trail) {
        echo "   Trail Name: {$itinerary->trail->name}\n";
        echo "   Trail Latitude: " . ($itinerary->trail->latitude ?? 'Not set') . "\n";
        echo "   Trail Longitude: " . ($itinerary->trail->longitude ?? 'Not set') . "\n";
    } else {
        echo "   ⚠️ Trail not found!\n";
    }
} else {
    echo "⚠️ No itinerary found for this user\n";
}

echo "\n";

// Check OpenWeather API key
echo "Checking OpenWeather API configuration...\n";
$apiKey = config('services.openweather.api_key');
if ($apiKey) {
    echo "✅ API Key configured: " . substr($apiKey, 0, 10) . "...\n\n";
} else {
    echo "❌ API Key NOT configured!\n";
    echo "   Add OPENWEATHER_API_KEY to your .env file\n\n";
}

// Delete today's notifications for testing
echo "Cleaning up today's weather notifications...\n";
$deleted = $user->notifications()
    ->where('type', 'weather')
    ->whereDate('created_at', today())
    ->delete();
echo "Deleted {$deleted} notification(s)\n\n";

// Test weather notification
echo "Sending weather notification...\n";
echo str_repeat("-", 50) . "\n";

try {
    $weatherService = new WeatherNotificationService(new NotificationService());
    
    if (!$weatherService->shouldSendWeatherNotification($user)) {
        echo "⚠️ Should not send notification (already sent today?)\n";
    } else {
        echo "Attempting to send notification...\n\n";
        
        $notification = $weatherService->sendLoginWeatherNotification($user);
        
        if ($notification) {
            echo "✅ SUCCESS! Weather notification created!\n\n";
            echo "Notification Details:\n";
            echo "   ID: {$notification->id}\n";
            echo "   Title: {$notification->title}\n";
            echo "   Type: {$notification->type}\n";
            echo "   Created: {$notification->created_at}\n\n";
            
            echo "Message:\n";
            echo "   " . str_replace("\n", "\n   ", $notification->message) . "\n\n";
            
            // Handle data - it might be array or JSON string
            $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
            if ($data) {
                echo "Weather Data:\n";
                
                if (isset($data['current_temp'])) {
                    echo "   🔶 Current: {$data['current_temp']}° in {$data['current_location']}\n";
                    if (isset($data['current_condition'])) {
                        echo "      Condition: {$data['current_condition']}\n";
                    }
                }
                
                if (isset($data['trail_temp'])) {
                    echo "   🌲 Trail: {$data['trail_temp']}° in {$data['trail_name']}\n";
                    if (isset($data['trail_condition'])) {
                        echo "      Condition: {$data['trail_condition']}\n";
                    }
                    if (isset($data['itinerary_id'])) {
                        echo "      Itinerary ID: {$data['itinerary_id']}\n";
                    }
                } else {
                    echo "   ⚠️ No trail weather data (user may not have itinerary or trail coordinates missing)\n";
                }
            }
            
            echo "\n";
            echo str_repeat("=", 50) . "\n";
            echo "Preview in Notification Bell:\n";
            echo str_repeat("=", 50) . "\n";
            echo "┌────────────────────────────────────────┐\n";
            echo "│ 🌤️  Weather Update                    │\n";
            echo "├────────────────────────────────────────┤\n";
            if (isset($data['current_temp'])) {
                echo "│ {$data['current_temp']}° in {$data['current_location']}\n";
            }
            if (isset($data['trail_temp'])) {
                echo "│ {$data['trail_temp']}° in {$data['trail_name']}\n";
            }
            if (isset($data['itinerary_id'])) {
                echo "│ View Itinerary →                       │\n";
            }
            echo "└────────────────────────────────────────┘\n\n";
            
        } else {
            echo "❌ FAILED to create weather notification\n";
            echo "Check the logs below for details:\n\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
}

// Show recent logs
echo str_repeat("=", 50) . "\n";
echo "Recent Log Entries:\n";
echo str_repeat("=", 50) . "\n";

$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file($logFile);
    $recentLogs = array_slice($logs, -30);
    
    foreach ($recentLogs as $log) {
        if (stripos($log, 'Weather') !== false || stripos($log, 'SendWeather') !== false) {
            echo $log;
        }
    }
} else {
    echo "Log file not found\n";
}

echo "\n";
echo str_repeat("=", 50) . "\n";
echo "✅ Test Complete!\n";
echo str_repeat("=", 50) . "\n";
echo "\nNext Steps:\n";
echo "1. Log out and log back in to test automatic notification\n";
echo "2. Check notification bell in the top navigation\n";
echo "3. Visit /notifications to see full notification page\n";
echo "4. Check storage/logs/laravel.log for detailed logs\n";
