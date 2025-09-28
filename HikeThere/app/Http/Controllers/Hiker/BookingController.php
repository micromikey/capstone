<?php

namespace App\Http\Controllers\Hiker;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Trail;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User as UserModel;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())->with('trail')->latest()->get();
        return view('hiker.booking.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        // Get organizations this hiker follows (only organization users)
        $user = Auth::user();
        // select fully qualified columns to avoid ambiguous `id` when joining with pivot table
        $organizations = $user->following()
            ->where('user_type', 'organization')
            ->get(["users.id as organization_id", 'organization_name']);

        // Start with empty trails — client will load trails for the selected organization via AJAX
        $trails = collect();

        // Prefill support: accept organization_id, trail_id, date
        $prefill = [
            'organization_id' => $request->query('organization_id'),
            'trail_id' => $request->query('trail_id'),
            'date' => $request->query('date'),
            'event_id' => $request->query('event_id'),
        ];

        return view('hiker.booking.booking-details', compact('trails', 'organizations', 'prefill'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trail_id' => 'required|exists:trails,id',
            'batch_id' => 'nullable|exists:batches,id',
            'event_id' => 'nullable|exists:events,id',
            'date' => 'nullable|date',
            'party_size' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:2000',
        ]);

        // If batch_id provided, ensure it belongs to the chosen trail
        $batch = null;
        if (!empty($data['batch_id'])) {
            $batch = Batch::find($data['batch_id']);
            if (! $batch || $batch->trail_id != $data['trail_id']) {
                return back()->withInput()->withErrors(['batch_id' => 'Selected batch is invalid for the chosen trail.']);
            }
        } else {
            // Auto-select an available batch for the trail.
            // Prefer batches that have starts_at matching the selected date (if provided), else pick next available.
            $query = Batch::where('trail_id', $data['trail_id']);

            if (!empty($data['date'])) {
                try {
                    $target = Carbon::parse($data['date'])->startOfDay();
                    $candidate = (clone $query)->whereNotNull('starts_at')
                        ->whereDate('starts_at', $target)
                        ->orderBy('starts_at')
                        ->get()
                        ->first(function($b){
                            $count = Booking::where('batch_id', $b->id)->where('status','!=','cancelled')->count();
                            return $count < $b->capacity;
                        });

                    if ($candidate) {
                        $batch = $candidate;
                    }
                } catch (\Exception $e) {
                    // ignore parse failures and fall back
                }
            }

            if (! $batch) {
                // pick the next batch with available capacity (future or undated)
                $candidate = (clone $query)->where(function($q){
                    $q->whereNull('starts_at')->orWhere('starts_at','>=', now());
                })->orderBy('starts_at')
                  ->get()
                  ->first(function($b){
                      $count = Booking::where('batch_id', $b->id)->where('status','!=','cancelled')->count();
                      return $count < $b->capacity;
                  });

                if ($candidate) {
                    $batch = $candidate;
                }
            }
        }

        if (! $batch) {
            return back()->withInput()->withErrors(['batch_id' => 'No available batch found for the selected trail/date.']);
        }

    // Use a database transaction and lock the batch row to prevent race conditions
        $booking = null;
        DB::beginTransaction();
        try {
            // reload and lock the batch row
            $lockedBatch = Batch::where('id', $batch->id)->lockForUpdate()->first();
            if (! $lockedBatch) {
                DB::rollBack();
                return back()->withInput()->withErrors(['batch_id' => 'Selected batch no longer exists.']);
            }

            // Sum existing confirmed bookings' party_size for accurate capacity usage
            $currentSpots = Booking::where('batch_id', $lockedBatch->id)
                ->where('status', '!=', 'cancelled')
                ->sum('party_size');

            $requested = intval($data['party_size'] ?? 1);
            $available = intval($lockedBatch->capacity ?? 0) - intval($currentSpots);

            if ($requested > $available) {
                DB::rollBack();
                return back()->withInput()->withErrors(['batch_id' => 'Selected batch does not have enough available spots. Try a different slot or reduce party size.']);
            }

            // create the booking while still in the transaction
            $data['batch_id'] = $lockedBatch->id;
            $data['user_id'] = Auth::id();
            $data['status'] = 'confirmed';

            // If an event_id was supplied, ensure the event exists and matches the trail_id
            if (!empty($data['event_id'])) {
                $event = \App\Models\Event::find($data['event_id']);
                if (! $event) {
                    DB::rollBack();
                    return back()->withInput()->withErrors(['event_id' => 'Selected event does not exist.']);
                }

                // If the event is linked to a trail, ensure it matches the booking trail
                if ($event->trail_id && $event->trail_id != $data['trail_id']) {
                    DB::rollBack();
                    return back()->withInput()->withErrors(['event_id' => 'Selected event is not associated with the chosen trail.']);
                }
            }

            $booking = Booking::create($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // fall back to safe error
            return back()->withInput()->withErrors(['batch_id' => 'Unable to create booking at this time. Please try again.']);
        }

        return redirect()->route('booking.show', $booking)->with('success', 'Booking created.');
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load('trail', 'user');

        return view('hiker.booking.show', compact('booking'));
    }

    public function packageDetails()
    {
        return view('hiker.booking.package-details');
    }

    /**
     * Return the trails for a given organization (only if followed by current hiker).
     */
    public function organizationTrails(UserModel $organization)
    {
        $user = Auth::user();

        // Ensure the current user follows this organization
        $follows = $user->following()->where('users.id', $organization->id)->exists();
        if (! $follows) {
            return response()->json(['message' => 'Not authorized to view these trails'], 403);
        }

        $trails = Trail::where('user_id', $organization->id)->orderBy('trail_name')->get(['id', 'trail_name']);

        return response()->json($trails);
    }

    /**
     * Return package details for a given trail (JSON) — used by booking preview.
     */
    public function trailPackage($trailId)
    {
        $user = Auth::user();

        $trail = Trail::with('package', 'user')->findOrFail($trailId);

        // If the current user is a hiker, ensure they follow the organization owning the trail
        if ($user && $user->user_type === 'hiker') {
            if (! $user->isFollowing($trail->user_id)) {
                return response()->json(['message' => 'Not authorized to view this package'], 403);
            }
        }

        $payload = [
            'id' => $trail->id,
            'trail_name' => $trail->trail_name,
            'mountain_name' => $trail->mountain_name,
            'duration' => $trail->duration,
            'price' => $trail->price,
            // schedule / opening hours come from the related package when present
            'opening_time' => $trail->package?->opening_time ?? null,
            'closing_time' => $trail->package?->closing_time ?? null,
            // Friendly formatted times (e.g. 8:00 AM). Parsing is best-effort; leave null on parse failure.
            'opening_time_formatted' => null,
            'closing_time_formatted' => null,
            // estimated_time is stored on the trail (minutes) — also provide a formatted string
            'estimated_time' => $trail->estimated_time ?? null,
            'estimated_time_formatted' => $trail->estimated_time_formatted ?? null,
            // Prefer the structured JSON fields when available
            'package_inclusions' => $trail->package?->package_inclusions_json ?? ($trail->package_inclusions ?? null),
            'side_trips' => $trail->package?->side_trips_json ?? ($trail->side_trips ?? null),
            'permit_required' => (bool) $trail->permit_required,
            'permit_process' => $trail->permit_process,
            'transport_included' => (bool) $trail->transport_included,
            'transport_details' => $trail->transport_details,
            'summary' => $trail->summary,
            'description' => $trail->description,
            'organization' => $trail->user?->display_name,
            'image' => $trail->primaryImage?->url ?? asset('img/default-trail.jpg'),
        ];

        // Try to format opening/closing times without applying timezone conversions.
        // We expect the DB stores simple time strings like H:i or H:i:s. Parse those
        // formats directly and format them to a friendly representation (g:i A).
        $timeFormats = ['H:i:s', 'H:i'];

        if (!empty($payload['opening_time'])) {
            foreach ($timeFormats as $fmt) {
                try {
                    $dt = Carbon::createFromFormat($fmt, $payload['opening_time']);
                    if ($dt !== false) {
                        $payload['opening_time_formatted'] = $dt->format('g:i A');
                        break;
                    }
                } catch (\Exception $e) {
                    // try next format
                }
            }
            // if still null, fall back to the raw value for display
            if (empty($payload['opening_time_formatted'])) {
                $payload['opening_time_formatted'] = $payload['opening_time'];
            }
        }

        if (!empty($payload['closing_time'])) {
            foreach ($timeFormats as $fmt) {
                try {
                    $dt = Carbon::createFromFormat($fmt, $payload['closing_time']);
                    if ($dt !== false) {
                        $payload['closing_time_formatted'] = $dt->format('g:i A');
                        break;
                    }
                } catch (\Exception $e) {
                    // try next format
                }
            }
            if (empty($payload['closing_time_formatted'])) {
                $payload['closing_time_formatted'] = $payload['closing_time'];
            }
        }

        return response()->json($payload);
    }

    /**
     * Return available batches for a given trail (optionally filtered by date).
     * GET /hiker/api/trail/{trail}/batches?date=YYYY-MM-DD
     */
    public function trailBatches($trailId, Request $request)
    {
        $user = Auth::user();

        $trail = Trail::with('user')->findOrFail($trailId);

        // If the current user is a hiker, ensure they follow the organization owning the trail
        if ($user && $user->user_type === 'hiker') {
            if (! $user->isFollowing($trail->user_id)) {
                return response()->json(['message' => 'Not authorized to view these batches'], 403);
            }
        }

        $date = $request->query('date');

        // Parse requested date (if any) once and reuse
        $target = null;
        if (!empty($date)) {
            try {
                $target = Carbon::parse($date)->startOfDay();
            } catch (\Exception $e) {
                $target = null;
            }
        }

        // Debug log: record the incoming date and target for diagnosis
        Log::info('trailBatches: requested date', ['trail_id' => $trail->id, 'date' => $date, 'target' => $target?->toDateString()]);

        // First fetch events for this trail/date — if no events exist, there are no slots.
        $events = \App\Models\Event::where('trail_id', $trail->id)
            ->where(function($q) use ($target) {
                if (!empty($target)) {
                    // When a specific date is selected, match events where that date
                    // falls within the event's start_at..end_at range (inclusive).
                    // Also include 'always available' events (start_at null).
                    $q->where(function($q2) use ($target) {
                        $q2->whereNull('start_at')
                           ->orWhere(function($q3) use ($target) {
                               $q3->whereDate('start_at', '<=', $target)
                                  ->where(function($q4) use ($target) {
                                      $q4->whereNull('end_at')->orWhereDate('end_at', '>=', $target);
                                  });
                           });
                    });
                } else {
                    // otherwise include undated events or future events based on 'start_at'
                    $q->where(function($q2){
                        $q2->whereNull('start_at')->orWhere('start_at','>=', now());
                    });
                }
            })->orderBy('start_at')->get();

        // Debug log: list matched events and their start times
        Log::info('trailBatches: matched events', ['trail_id' => $trail->id, 'events' => $events->map(fn($e) => ['id' => $e->id, 'start_at' => (string)$e->start_at])->all()]);

        $events_count = $events->count();
        if ($events_count === 0) {
            // no events => return empty array for backward compatibility, and include
            // metadata via response headers so the frontend can show a clear message.
            return response()->json([])
                ->header('X-Events-Count', 0)
                ->header('X-Booking-Enabled', 'false');
        }

        // Only consider batches that are associated with events. Batches by themselves do not
        // represent open slots unless they belong to an Event. Eager-load the event relation
        // so we can include event metadata in the response.
        $eventIds = $events->pluck('id')->toArray();
        $query = Batch::where('trail_id', $trail->id)->whereNotNull('event_id')
            ->with('event')
            ->withCount(['bookings as booked_count' => function($q){ $q->where('status','!=','cancelled'); }])
            ->whereIn('event_id', $eventIds);

        if (!empty($target)) {
            // when a date is selected, only include batches that occur on that date
            $query->whereNotNull('starts_at')->whereDate('starts_at', $target);
        } else {
            // default: undated batches or future-dated batches
            $query->where(function($q){
                $q->whereNull('starts_at')->orWhere('starts_at','>=', now());
            });
        }

        // Fetch batches per existing logic
    $batchesRaw = $query->orderBy('starts_at')->get();

    // Debug log: batches found for matched events
    Log::info('trailBatches: batches fetched', ['trail_id' => $trail->id, 'batches_count' => $batchesRaw->count(), 'event_ids' => $eventIds ?? []]);

        $batches = $batchesRaw->map(function($b){
            $remaining = max(0, ($b->capacity ?? 0) - ($b->booked_count ?? 0));

            $starts = $b->starts_at ? Carbon::parse($b->starts_at)->setTimezone('Asia/Manila') : null;
            $ends = $b->ends_at ? Carbon::parse($b->ends_at)->setTimezone('Asia/Manila') : null;

            $labelBase = $b->name ?? '';
            // if event title exists, prefer that as base label (remove any leading 'Event:' prefix)
            if (!empty($b->event?->title)) {
                $labelBase = preg_replace('/^Event:\s*/i', '', $b->event->title);
            }

            // compose a consistent slot label: prefer batch start, and include the
            // parent event's start/end range when present so all slots show event dates.
            $slotLabel = trim($labelBase);
            if ($starts) {
                $slotLabel .= ' — ' . $starts->format('M j, g:i A');
            }

            // include the event's overall date range when available, but avoid
            // repeating the same start timestamp (e.g. when batch starts_at === event start_at).
            $eventStarts = $b->event?->start_at ? Carbon::parse($b->event->start_at)->setTimezone('Asia/Manila') : null;
            $eventEnds = $b->event?->end_at ? Carbon::parse($b->event->end_at)->setTimezone('Asia/Manila') : null;
            if ($eventStarts) {
                // Compare up to minute precision to avoid duplicating the same
                // start time when seconds or timezone rounding differs.
                $sameStart = false;
                if ($starts && $eventStarts) {
                    // treat starts within 1 minute as identical to avoid duplicate labels
                    $sameStart = ($starts->diffInMinutes($eventStarts) <= 1);
                }
                $differentStart = ! $sameStart;
                if ($differentStart) {
                    $slotLabel .= ' — ' . $eventStarts->format('M j, g:i A');
                }
                if ($eventEnds) {
                    // if the end date is the same day, show only the time for the end
                    if ($eventStarts->toDateString() === $eventEnds->toDateString()) {
                        $slotLabel .= ' to ' . $eventEnds->format('g:i A');
                    } else {
                        $slotLabel .= ' to ' . $eventEnds->format('M j, g:i A');
                    }
                }
            }

            if (!is_null($remaining)) {
                $slotLabel .= ' (' . $remaining . ' spots left)';
            }

            return [
                'type' => 'batch',
                'id' => $b->id,
                'name' => $b->name,
                'capacity' => $b->capacity,
                'remaining' => $remaining,
                'starts_at' => $b->starts_at ? $b->starts_at->toDateTimeString() : null,
                'ends_at' => $b->ends_at ? $b->ends_at->toDateTimeString() : null,
                'starts_at_formatted' => $starts ? $starts->format('M j, g:i A') : null,
                'ends_at_formatted' => $ends ? $ends->format('g:i A') : null,
                // include event metadata so frontend can label slots consistently
                'event_id' => $b->event_id ?? null,
                'event_title' => $b->event?->title ?? $b->event?->name ?? null,
                'slot_label' => $slotLabel,
            ];
        })->filter(function($b){
            // only include batches with at least one spot remaining
            return ($b['remaining'] ?? 0) > 0;
        })->values()->all();

        // Map previously-fetched events into the slot shape. Events will include
        // event metadata fields as well so the frontend can render consistent labels.
        // When a target date is provided, only include event 'slots' if the
        // event's own start_at occurs on that date. Otherwise, we will rely
        // on the batches for the selected date and avoid showing the event's
        // original start date as a selectable slot (which confused the UI).
        $eventsToMap = $events;
        if (!empty($target)) {
            $eventsToMap = $events->filter(function($ev) use ($target) {
                if (empty($ev->start_at)) return false;
                try {
                    return Carbon::parse($ev->start_at)->isSameDay($target);
                } catch (\Exception $ex) {
                    return false;
                }
            });
        }

        $eventsMapped = $eventsToMap->map(function($e){
            $starts = $e->start_at ? Carbon::parse($e->start_at)->setTimezone('Asia/Manila') : null;
            $ends = $e->end_at ? Carbon::parse($e->end_at)->setTimezone('Asia/Manila') : null;

            // Events may have capacity fields; if not present, set null.
            $capacity = $e->capacity ?? null;

            // booked_count for events — count non-cancelled bookings linked to the event (if relation exists)
            // Event may not have direct bookings relation; sum bookings on its batches as a fallback
            $booked = \App\Models\Booking::where('event_id', $e->id)->where('status','!=','cancelled')->sum('party_size');
            if (empty($booked)) {
                // fallback to bookings through batches linked to this event
                $booked = \App\Models\Booking::whereIn('batch_id', \App\Models\Batch::where('event_id', $e->id)->pluck('id'))->where('status','!=','cancelled')->sum('party_size');
            }
            $remaining = is_null($capacity) ? null : max(0, $capacity - $booked);

            $labelBase = $e->title ?? $e->name ?? '';
            $labelBase = preg_replace('/^Event:\s*/i', '', $labelBase);
            $slotLabel = trim($labelBase);
            if ($starts) {
                $slotLabel .= ' — ' . $starts->format('M j, g:i A');
                if ($ends) {
                    // show end as time if same day, else include date
                    if ($starts->toDateString() === $ends->toDateString()) {
                        $slotLabel .= ' to ' . $ends->format('g:i A');
                    } else {
                        $slotLabel .= ' to ' . $ends->format('M j, g:i A');
                    }
                }
            }
            if (!is_null($remaining)) {
                $slotLabel .= ' (' . $remaining . ' spots left)';
            }

            return [
                'type' => 'event',
                'id' => $e->id,
                'name' => $e->name ?? ('Event: ' . ($e->title ?? '')),
                'capacity' => $capacity,
                'remaining' => $remaining,
                'starts_at' => $e->start_at ? $e->start_at->toDateTimeString() : null,
                'ends_at' => $e->end_at ? $e->end_at->toDateTimeString() : null,
                'starts_at_formatted' => $starts ? $starts->format('M j, g:i A') : null,
                'ends_at_formatted' => $ends ? $ends->format('g:i A') : null,
                // explicit event metadata fields for consistency with batch slots
                'event_id' => $e->id,
                'event_title' => $e->title ?? $e->name ?? null,
                'slot_label' => $slotLabel,
            ];
        })->values()->all();

        // Merge events and batches, preferring events when both have the same starts_at timestamp.
        // Use an associative map keyed by starts_at (string) where possible. If starts_at is null,
        // fall back to unique key by type+id to avoid accidental collision.
        $mergedMap = [];

        foreach ($batches as $b) {
            $key = $b['starts_at'] ?? ('batch_' . $b['id']);
            // only insert if there is at least one remaining spot
            if (($b['remaining'] ?? 0) > 0) {
                $mergedMap[$key] = $b;
            }
        }

        foreach ($eventsMapped as $e) {
            $key = $e['starts_at'] ?? ('event_' . $e['id']);
            // Prefer events over batches when keys conflict — always include the event
            // entry so trails with events are visible to the frontend.
            $mergedMap[$key] = $e;
        }

        // Finally, sort merged entries by starts_at (nulls last) and return as array
        $merged = array_values($mergedMap);

        usort($merged, function($a, $b){
            if (empty($a['starts_at']) && empty($b['starts_at'])) return 0;
            if (empty($a['starts_at'])) return 1;
            if (empty($b['starts_at'])) return -1;
            return strcmp($a['starts_at'], $b['starts_at']);
        });

        // Debug: log merged slots (id,type,starts_at,slot_label) for diagnosis
        try {
            Log::info('trailBatches: merged slots', ['trail_id' => $trail->id, 'slots' => array_map(fn($s) => ['id' => $s['id'], 'type' => $s['type'], 'starts_at' => $s['starts_at'] ?? null, 'slot_label' => $s['slot_label'] ?? null], $merged)]);
        } catch (\Exception $_e) {
            // ignore logging failures
        }

        // Return the legacy top-level array (for existing clients) and provide
        // metadata in response headers for newer clients.
        return response()->json(array_values($merged))
            ->header('X-Events-Count', $events_count)
            ->header('X-Booking-Enabled', 'true');
    }
}
