<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventPollingController extends Controller
{
    /**
     * Get latest public events for polling
     * Returns events created after the provided timestamp
     */
    public function getLatestEvents(Request $request)
    {
        $request->validate([
            'since' => 'nullable|date',
            'trail_id' => 'nullable|exists:trails,id',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $query = Event::where('is_public', true);

        // Filter by timestamp - only return events created after the given time
        if ($request->filled('since')) {
            $query->where('created_at', '>', $request->since);
        }

        // Filter by specific trail if provided (for trails/show.blade.php)
        if ($request->filled('trail_id')) {
            $query->where('trail_id', $request->trail_id);
        }

        // Get events with trail relationship
        $events = $query->with(['trail:id,trail_name,mountain_name,slug'])
            ->orderBy('created_at', 'desc')
            ->limit($request->get('limit', 10))
            ->get();

        // Return event data formatted for frontend consumption
        return response()->json([
            'success' => true,
            'count' => $events->count(),
            'events' => $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'slug' => $event->slug,
                    'description' => $event->description,
                    'start_at' => $event->start_at,
                    'end_at' => $event->end_at,
                    'always_available' => $event->always_available,
                    'hiking_start_time' => $event->hiking_start_time,
                    'capacity' => $event->capacity,
                    'created_at' => $event->created_at,
                    'trail' => [
                        'id' => $event->trail->id ?? null,
                        'name' => $event->trail->trail_name ?? null,
                        'mountain' => $event->trail->mountain_name ?? null,
                        'slug' => $event->trail->slug ?? null,
                    ],
                    'url' => route('trails.show', $event->trail->slug ?? '#'),
                ];
            }),
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Get event count for specific conditions
     * Useful for showing "X new events" notification
     */
    public function getEventCount(Request $request)
    {
        $request->validate([
            'since' => 'nullable|date',
            'trail_id' => 'nullable|exists:trails,id'
        ]);

        $query = Event::where('is_public', true);

        if ($request->filled('since')) {
            $query->where('created_at', '>', $request->since);
        }

        if ($request->filled('trail_id')) {
            $query->where('trail_id', $request->trail_id);
        }

        $count = $query->count();

        return response()->json([
            'success' => true,
            'count' => $count,
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
