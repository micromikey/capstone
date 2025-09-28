<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventBatchService
{
    /**
     * Generate/sync batches for an Event.
     * Rules:
     * - Duration is taken from the TrailPackage.duration by default.
     * - Create contiguous batches starting at event.start_at stepping by duration until (ends_at <= event.end_at).
     * - If event is 'always available' (no start_at/end_at), create a single undated batch (starts_at null).
     */
    public function syncForEvent(Event $event)
    {
        // Remove future batches tied to this event first
        Batch::where('event_id', $event->id)->delete();

        // If no start and no end, create a single undated batch
        if (empty($event->start_at) && empty($event->end_at)) {
            Batch::create([
                'event_id' => $event->id,
                'trail_id' => $event->trail_id,
                'name' => 'Event: ' . ($event->title ?? 'Event'),
                'capacity' => $event->capacity ?? null,
                'starts_at' => null,
                'ends_at' => null,
            ]);
            return;
        }

        // Determine duration from trail package if available (supporting days/hours parsing similar to BatchService)
        $durationHours = 0;
        try {
            $package = $event->trail?->package;
            if ($package && !empty($package->duration)) {
                // reuse the same parse logic as BatchService-ish: accepts numeric hours, H:MM, or Xd
                $duration = trim((string)$package->duration);
                if (is_numeric($duration)) {
                    $durationHours = floatval($duration);
                } elseif (preg_match('/^(\d+):(\d{1,2})$/', $duration, $m)) {
                    $durationHours = intval($m[1]) + (intval($m[2]) / 60);
                } elseif (preg_match('/^(\d+)\s*d$/i', $duration, $m)) {
                    $durationHours = intval($m[1]) * 24;
                }
            }
        } catch (\Exception $e) {
            $durationHours = 0;
        }

        // Fallback duration: 24 hours if cannot parse
        if ($durationHours <= 0) $durationHours = 24;

        // Use event's start and end (as Carbon instances)
        $start = $event->start_at ? Carbon::parse($event->start_at) : null;
        $endBoundary = $event->end_at ? Carbon::parse($event->end_at) : null;

        if (! $start || ! $endBoundary) {
            // if either missing, create single batch with provided start/end
            Batch::create([
                'event_id' => $event->id,
                'trail_id' => $event->trail_id,
                'name' => 'Event: ' . ($event->title ?? 'Event'),
                'capacity' => $event->capacity ?? null,
                'starts_at' => $event->start_at ?? null,
                'ends_at' => $event->end_at ?? null,
            ]);
            return;
        }

        $slotMinutes = intval(round($durationHours * 60));
        $cursor = $start->copy();

        while (true) {
            $slotEnd = $cursor->copy()->addMinutes($slotMinutes);
            // Strict rule: only create slot if slotEnd <= event.end_at
            if ($slotEnd->gt($endBoundary)) break;

            Batch::create([
                'event_id' => $event->id,
                'trail_id' => $event->trail_id,
                'name' => 'Event: ' . ($event->title ?? 'Event') . ' @ ' . $cursor->toDateTimeString(),
                'capacity' => $event->capacity ?? null,
                'starts_at' => $cursor->copy(),
                'ends_at' => $slotEnd,
            ]);

            // advance cursor
            $cursor = $slotEnd->copy();
        }
    }
}
