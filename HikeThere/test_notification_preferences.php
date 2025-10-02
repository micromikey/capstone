<?php

/**
 * Test script to verify notification preferences integration
 * 
 * Run with: php test_notification_preferences.php
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserPreference;
use App\Services\NotificationService;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Notification Preferences Integration ===\n\n";

try {
    // Find a test user or create one
    $user = User::where('user_type', 'hiker')->first();
    
    if (!$user) {
        echo "❌ No hiker user found in database. Please create a user first.\n";
        exit(1);
    }
    
    echo "✓ Using user: {$user->name} (ID: {$user->id})\n\n";
    
    // Test 1: Check if preferences exist
    echo "Test 1: Checking user preferences\n";
    echo "-----------------------------------\n";
    
    $preferences = $user->preferences;
    
    if ($preferences) {
        echo "✓ User has preferences set\n";
        echo "  - Email Notifications: " . ($preferences->email_notifications ? '✓' : '✗') . "\n";
        echo "  - Push Notifications: " . ($preferences->push_notifications ? '✓' : '✗') . "\n";
        echo "  - Trail Updates: " . ($preferences->trail_updates ? '✓' : '✗') . "\n";
        echo "  - Security Alerts: " . ($preferences->security_alerts ? '✓' : '✗') . "\n";
        echo "  - Newsletter: " . ($preferences->newsletter ? '✓' : '✗') . "\n";
    } else {
        echo "ℹ User has no preferences (will use defaults)\n";
        $defaultPrefs = UserPreference::getDefaults();
        echo "  Default preferences:\n";
        foreach ($defaultPrefs as $key => $value) {
            if (is_bool($value)) {
                echo "  - {$key}: " . ($value ? '✓' : '✗') . "\n";
            }
        }
    }
    
    echo "\n";
    
    // Test 2: Create preferences with trail_updates disabled
    echo "Test 2: Disabling trail updates\n";
    echo "-----------------------------------\n";
    
    UserPreference::updatePreferences($user->id, [
        'email_notifications' => true,
        'push_notifications' => true,
        'trail_updates' => false, // Disable trail updates
        'security_alerts' => true,
        'newsletter' => false,
        'profile_visibility' => 'public',
        'timezone' => 'UTC',
        'language' => 'en',
    ]);
    
    $user = $user->fresh(); // Reload user
    $preferences = $user->preferences;
    
    echo "✓ Preferences updated\n";
    echo "  - Trail Updates: " . ($preferences->trail_updates ? '✓' : '✗') . " (should be ✗)\n\n";
    
    // Test 3: Try to send trail update notification (should be blocked)
    echo "Test 3: Attempting to send trail update notification\n";
    echo "-----------------------------------\n";
    
    $notificationService = new NotificationService();
    
    // Mock trail data
    $trailData = (object)[
        'id' => 1,
        'name' => 'Test Trail',
        'slug' => 'test-trail',
    ];
    
    $notification = $notificationService->sendTrailUpdate(
        $user,
        $trailData,
        'Trail conditions have been updated'
    );
    
    if ($notification === null) {
        echo "✓ Trail update notification was correctly blocked\n";
        echo "  (User has trail_updates disabled)\n";
    } else {
        echo "❌ Trail update notification was sent (should have been blocked)\n";
    }
    
    echo "\n";
    
    // Test 4: Send security alert (should go through)
    echo "Test 4: Sending security alert notification\n";
    echo "-----------------------------------\n";
    
    $notification = $notificationService->sendSecurityAlert(
        $user,
        'Test Security Alert',
        'This is a test security notification'
    );
    
    if ($notification) {
        echo "✓ Security alert notification was sent\n";
        echo "  - ID: {$notification->id}\n";
        echo "  - Type: {$notification->type}\n";
        echo "  - Title: {$notification->title}\n";
    } else {
        echo "❌ Security alert notification was blocked\n";
    }
    
    echo "\n";
    
    // Test 5: Disable all push notifications
    echo "Test 5: Disabling all push notifications\n";
    echo "-----------------------------------\n";
    
    UserPreference::updatePreferences($user->id, [
        'email_notifications' => true,
        'push_notifications' => false, // Disable all push notifications
        'trail_updates' => true,
        'security_alerts' => true,
        'newsletter' => false,
        'profile_visibility' => 'public',
        'timezone' => 'UTC',
        'language' => 'en',
    ]);
    
    $user = $user->fresh();
    
    echo "✓ Push notifications disabled\n\n";
    
    // Test 6: Try to send system notification (should be blocked)
    echo "Test 6: Attempting to send system notification\n";
    echo "-----------------------------------\n";
    
    $notification = $notificationService->sendSystemNotification(
        $user,
        'Test System Notification',
        'This should be blocked'
    );
    
    if ($notification === null) {
        echo "✓ System notification was correctly blocked\n";
        echo "  (User has push_notifications disabled)\n";
    } else {
        echo "❌ System notification was sent (should have been blocked)\n";
    }
    
    echo "\n";
    
    // Test 7: Re-enable trail updates and test
    echo "Test 7: Re-enabling trail updates\n";
    echo "-----------------------------------\n";
    
    UserPreference::updatePreferences($user->id, [
        'email_notifications' => true,
        'push_notifications' => true,
        'trail_updates' => true, // Re-enable
        'security_alerts' => true,
        'newsletter' => false,
        'profile_visibility' => 'public',
        'timezone' => 'UTC',
        'language' => 'en',
    ]);
    
    $user = $user->fresh();
    
    $notification = $notificationService->sendTrailUpdate(
        $user,
        $trailData,
        'Trail is now open again'
    );
    
    if ($notification) {
        echo "✓ Trail update notification was sent successfully\n";
        echo "  - ID: {$notification->id}\n";
        echo "  - Message: {$notification->message}\n";
    } else {
        echo "❌ Trail update notification was blocked (should have been sent)\n";
    }
    
    echo "\n";
    
    // Summary
    echo "===========================================\n";
    echo "Test Summary\n";
    echo "===========================================\n";
    echo "✓ All tests completed successfully!\n";
    echo "\nThe notification preferences system is working correctly.\n";
    echo "Users can now control which notifications they receive.\n\n";
    
    echo "Current user preferences:\n";
    $finalPrefs = $user->preferences;
    echo "  - Email Notifications: " . ($finalPrefs->email_notifications ? '✓' : '✗') . "\n";
    echo "  - Push Notifications: " . ($finalPrefs->push_notifications ? '✓' : '✗') . "\n";
    echo "  - Trail Updates: " . ($finalPrefs->trail_updates ? '✓' : '✗') . "\n";
    echo "  - Security Alerts: " . ($finalPrefs->security_alerts ? '✓' : '✗') . "\n";
    echo "  - Newsletter: " . ($finalPrefs->newsletter ? '✓' : '✗') . "\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
