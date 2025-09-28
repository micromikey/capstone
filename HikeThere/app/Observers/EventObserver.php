<?php

namespace App\Observers;

use App\Models\Event;
use App\Services\EventBatchService;

class EventObserver
{
    protected $service;

    public function __construct()
    {
        $this->service = new EventBatchService();
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
