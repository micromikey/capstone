<?php

/**
 * Test script to create sample notifications
 * Run this file to populate your notification system with test data
 * 
 * Usage: php test_notifications.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\NotificationService;

// Get a test user (first hiker)
$user = User::where('user_type', 'hiker')->first();

if (!$user) {
    echo "No hiker user found. Please create a hiker account first.\n";
    exit(1);
}

echo "Creating test notifications for user: {$user->name} ({$user->email})\n\n";

$service = new NotificationService();

// 1. Welcome notification
echo "Creating welcome notification...\n";
$service->sendWelcomeNotification($user);

// 2. Trail update notification
echo "Creating trail update notification...\n";
$service->create(
    $user,
    'trail_update',
    'Trail Condition Update - Mt. Pulag',
    'The weather conditions have improved. Trail is now open for hiking with clear skies expected.',
    [
        'trail_id' => 1,
        'trail_slug' => 'mt-pulag',
        'trail_name' => 'Mt. Pulag',
        'severity' => 'info'
    ]
);

// 3. Security alert
echo "Creating security alert...\n";
$service->sendSecurityAlert(
    $user,
    'New Login Detected',
    'A new login to your account was detected from Windows device in Philippines. If this wasn\'t you, please change your password immediately.'
);

// 4. System notification
echo "Creating system notification...\n";
$service->sendSystemNotification(
    $user,
    'New Feature: Trail Reviews',
    'You can now leave reviews and ratings for trails you\'ve hiked. Share your experience with the community!',
    ['feature' => 'trail_reviews', 'link' => '/trails']
);

// 5. Booking notification (if applicable)
echo "Creating booking confirmation notification...\n";
$service->create(
    $user,
    'booking',
    'Booking Confirmed',
    'Your hiking trip to Mt. Batulao has been confirmed for October 15, 2025.',
    [
        'booking_id' => 1,
        'trail_name' => 'Mt. Batulao',
        'date' => '2025-10-15'
    ]
);

// 6. Another trail update (this one unread)
echo "Creating another trail update notification...\n";
$service->create(
    $user,
    'trail_update',
    'Trail Closure Alert - Mt. Mayon',
    'Due to increased volcanic activity, Mt. Mayon trail is temporarily closed until further notice. Please plan alternative routes.',
    [
        'trail_slug' => 'mt-mayon',
        'trail_name' => 'Mt. Mayon',
        'severity' => 'high'
    ]
);

// Mark first two as read
echo "\nMarking first two notifications as read...\n";
$notifications = $user->notifications()->take(2)->get();
foreach ($notifications as $notification) {
    $notification->markAsRead();
}

echo "\n✅ Test notifications created successfully!\n";
echo "Total notifications: " . $user->notifications()->count() . "\n";
echo "Unread notifications: " . $user->unreadNotificationsCount() . "\n";
echo "\nYou can now view them at: " . url('/notifications') . "\n";
