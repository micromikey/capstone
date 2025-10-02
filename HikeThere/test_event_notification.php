<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;
use App\Services\NotificationService;

echo "=== Testing Event Notification System ===\n\n";

// Get the latest event
$event = Event::latest()->first();

if (!$event) {
    echo "❌ No events found in database\n";
    exit;
}

echo "📅 Found event:\n";
echo "   ID: {$event->id}\n";
echo "   Title: {$event->title}\n";
echo "   Is Public: " . ($event->is_public ? 'Yes' : ($event->is_public === null ? 'NULL' : 'No')) . "\n";
echo "   Trail ID: {$event->trail_id}\n";
echo "   Price: " . ($event->is_free ? 'Free' : "₱{$event->price}") . "\n";
echo "   Organization: {$event->user->name}\n\n";

// Manually trigger notification
echo "🔔 Sending notifications...\n";
$notificationService = new NotificationService();
$notifications = $notificationService->sendNewEventNotification($event);

echo "✅ Sent " . count($notifications) . " notifications\n\n";

// Show sample notification
if (count($notifications) > 0) {
    $sample = $notifications[0];
    echo "📨 Sample notification:\n";
    echo "   To: {$sample->user->name}\n";
    echo "   Title: {$sample->title}\n";
    echo "   Message: {$sample->message}\n";
    echo "   Data: " . json_encode($sample->data, JSON_PRETTY_PRINT) . "\n";
}
