<?php

namespace App\Observers;

use App\Models\Event;
use App\Services\EventBatchService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class EventObserver
{
    protected $service;
    protected $notificationService;

    public function __construct()
    {
        $this->service = new EventBatchService();
        $this->notificationService = new NotificationService();
    }

    public function created(Event $event)
    {
        // Send notifications to hikers when a new event is created
        try {
            Log::info('EventObserver: New event created, sending notifications', [
                'event_id' => $event->id,
                'event_title' => $event->title,
                'is_public' => $event->is_public
            ]);

            // Only send notifications for public events (treat null as true for backward compatibility)
            if ($event->is_public !== false) {
                $notifications = $this->notificationService->sendNewEventNotification($event);
                Log::info('EventObserver: Notifications sent', [
                    'count' => count($notifications)
                ]);
            } else {
                Log::info('EventObserver: Event is private, skipping notifications');
            }
        } catch (\Exception $e) {
            Log::error('EventObserver: Failed to send event notifications', [
                'error' => $e->getMessage(),
                'event_id' => $event->id
            ]);
        }
    }

    public function saved(Event $event)
    {
        $this->service->syncForEvent($event);
    }

    public function deleted(Event $event)
    {
        // remove any batches
        \App\Models\Batch::where('event_id', $event->id)->delete();
    }
}
