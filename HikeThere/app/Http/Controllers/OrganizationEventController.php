<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationEventController extends Controller
{
    public function index()
    {
        $events = Event::where('user_id', Auth::id())->orderBy('start_at','desc')->paginate(12);
        return view('org.events.index', compact('events'));
    }

    public function create(Request $request)
    {
        // Provide a list of this organization's trails for selection
        $trails = \App\Models\Trail::where('user_id', Auth::id())->orderBy('trail_name')->get();
        
        // Get pre-selected trail ID from query parameter (e.g., from trail creation flow)
        $preselectedTrailId = $request->query('trail_id');
        
        return view('org.events.create', compact('trails', 'preselectedTrailId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Title is now optional; trail_id is required for organizer events
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'trail_id' => 'required|exists:trails,id',
            'capacity' => 'nullable|integer|min:1',
            'duration' => 'nullable|string',
            'always_available' => 'boolean',
            'batch_count' => 'nullable|integer|min:1',
            // batches[] manual creation removed
            'is_public' => 'boolean',
        ]);

        $data['user_id'] = Auth::id();
    // Normalize checkbox booleans (hidden input + checkbox) to boolean
    $data['always_available'] = $request->boolean('always_available');

        // If always_available is set, ensure start/end and batch_count are treated as null
        if (!empty($data['always_available'])) {
            $data['start_at'] = null;
            $data['end_at'] = null;
            $data['batch_count'] = null;
        }
        // If a trail_id was supplied, ensure it belongs to this org
        if (!empty($data['trail_id'])) {
            $trailOwner = \App\Models\Trail::where('id', $data['trail_id'])->value('user_id');
            if ($trailOwner !== Auth::id()) {
                return back()->withInput()->withErrors(['trail_id' => 'Selected trail does not belong to your organization.']);
            }
        }

        // Server-side validation: if batch_count and start_at provided (and not always available), ensure end_at matches start + duration*batch_count
        if (empty($data['always_available']) && !empty($data['batch_count']) && !empty($data['start_at'])) {
            $durationMinutes = $this->computeDurationMinutesFromInput($data['duration'] ?? null, $data['trail_id'] ?? null);
            if ($durationMinutes <= 0) $durationMinutes = 24 * 60;
            $expectedEnd = \Carbon\Carbon::parse($data['start_at'])->copy()->addMinutes(intval($durationMinutes) * intval($data['batch_count']));
            if (!empty($data['end_at'])) {
                try {
                    $providedEnd = \Carbon\Carbon::parse($data['end_at']);
                    if (!$providedEnd->equalTo($expectedEnd)) {
                        return back()->withInput()->withErrors(['end_at' => 'End date must equal start + (duration × batch_count). Expected: ' . $expectedEnd->format('Y-m-d H:i')]);
                    }
                } catch (\Exception $e) {
                    return back()->withInput()->withErrors(['end_at' => 'Provided end date is invalid.']);
                }
            } else {
                // populate end_at automatically if missing
                $data['end_at'] = $expectedEnd->format('Y-m-d H:i:s');
            }
        }

        // Server-side fallback: ensure title is populated if client-side JS didn't set it
        if (empty($data['title'])) {
            $trail = null;
            if (!empty($data['trail_id'])) {
                $trail = \App\Models\Trail::find($data['trail_id']);
            }
            $trailName = $trail?->trail_name ?? '';
            $dur = $data['duration'] ?? null;
            if (empty($dur) && $trail) {
                $dur = $trail->package?->duration ?? null;
            }
            $suggest = $trailName ?: 'Event';
            if (!empty($dur)) $suggest .= ' — ' . $dur;
            $data['title'] = $suggest;
        }

        $event = Event::create($data);

    // If batch_count provided, auto-generate contiguous batches
    if (!empty($data['batch_count']) && !empty($data['start_at'])) {
            // compute duration in minutes from event.duration or trail package
            $durationMinutes = 0;
            try {
                if (!empty($data['duration'])) {
                    $duration = trim((string)$data['duration']);
                    if (is_numeric($duration)) {
                        $durationMinutes = intval(round(floatval($duration) * 60));
                    } elseif (preg_match('/^(\d+):(\d{1,2})$/', $duration, $m)) {
                        $durationMinutes = intval($m[1]) * 60 + intval($m[2]);
                    } elseif (preg_match('/^(\d+)\s*d$/i', $duration, $m)) {
                        $durationMinutes = intval($m[1]) * 24 * 60;
                    }
                } else {
                    $package = \App\Models\Trail::find($event->trail_id)?->package;
                    if ($package && !empty($package->duration)) {
                        $duration = trim((string)$package->duration);
                        if (is_numeric($duration)) {
                            $durationMinutes = intval(round(floatval($duration) * 60));
                        } elseif (preg_match('/^(\d+):(\d{1,2})$/', $duration, $m)) {
                            $durationMinutes = intval($m[1]) * 60 + intval($m[2]);
                        } elseif (preg_match('/^(\d+)\s*d$/i', $duration, $m)) {
                            $durationMinutes = intval($m[1]) * 24 * 60;
                        }
                    }
                }
            } catch (\Exception $e) {
                $durationMinutes = 0;
            }

            if ($durationMinutes <= 0) {
                // fallback to 24h
                $durationMinutes = 24 * 60;
            }

            $cursor = \Carbon\Carbon::parse($data['start_at']);
            for ($i = 0; $i < intval($data['batch_count']); $i++) {
                $slotEnd = $cursor->copy()->addMinutes($durationMinutes);
                \App\Models\Batch::create([
                    'event_id' => $event->id,
                    'trail_id' => $event->trail_id,
                    'name' => ($event->title ?? 'Event') . ' #' . ($i + 1) . ' @ ' . $cursor->toDateTimeString(),
                    'capacity' => $event->capacity ?? null,
                    'starts_at' => $cursor->copy(),
                    'ends_at' => $slotEnd,
                ]);
                $cursor = $slotEnd->copy();
            }
        }

        // manual batch creation removed; batches will be auto-generated from batch_count or created as a single undated batch by the EventBatchService when always_available is set



        return redirect()->route('org.events.index')->with('success','Event created');
    }

    public function edit(Event $event)
    {
        if ($event->user_id !== Auth::id()) abort(403);
        $trails = \App\Models\Trail::where('user_id', Auth::id())->orderBy('trail_name')->get();
        return view('org.events.edit', compact('event','trails'));
    }

    public function update(Request $request, Event $event)
    {
        if ($event->user_id !== Auth::id()) abort(403);

        $data = $request->validate([
            // Title optional on update as well; trail selection is required
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'trail_id' => 'required|exists:trails,id',
            'capacity' => 'nullable|integer|min:1',
            'duration' => 'nullable|string',
            'always_available' => 'boolean',
            'batch_count' => 'nullable|integer|min:1',
            // if manual_batches is true, expect batches json (array) – optional
            // manual batches removed
            'is_public' => 'boolean',
        ]);

    // Normalize checkboxes
    $data['always_available'] = $request->boolean('always_available');

        // If always_available is set on update, clear start/end and batch_count so they are null
        if (!empty($data['always_available'])) {
            $data['start_at'] = null;
            $data['end_at'] = null;
            $data['batch_count'] = null;
        }

        // Server-side validation: if batch_count and start_at provided (and not always available), ensure end_at matches start + duration*batch_count
        if (empty($data['always_available']) && !empty($data['batch_count']) && !empty($data['start_at'])) {
            $durationMinutes = $this->computeDurationMinutesFromInput($data['duration'] ?? null, $data['trail_id'] ?? $event->trail_id);
            if ($durationMinutes <= 0) $durationMinutes = 24 * 60;
            $expectedEnd = \Carbon\Carbon::parse($data['start_at'])->copy()->addMinutes(intval($durationMinutes) * intval($data['batch_count']));
            if (!empty($data['end_at'])) {
                try {
                    $providedEnd = \Carbon\Carbon::parse($data['end_at']);
                    if (!$providedEnd->equalTo($expectedEnd)) {
                        return back()->withInput()->withErrors(['end_at' => 'End date must equal start + (duration × batch_count). Expected: ' . $expectedEnd->format('Y-m-d H:i')]);
                    }
                } catch (\Exception $e) {
                    return back()->withInput()->withErrors(['end_at' => 'Provided end date is invalid.']);
                }
            } else {
                // populate end_at automatically if missing
                $data['end_at'] = $expectedEnd->format('Y-m-d H:i:s');
            }
        }

        // Server-side fallback for update: ensure title populated if client didn't set it
        if (empty($data['title'])) {
            $trail = null;
            if (!empty($data['trail_id'])) {
                $trail = \App\Models\Trail::find($data['trail_id']);
            } elseif ($event->trail_id) {
                $trail = \App\Models\Trail::find($event->trail_id);
            }
            $trailName = $trail?->trail_name ?? '';
            $dur = $data['duration'] ?? null;
            if (empty($dur) && $trail) {
                $dur = $trail->package?->duration ?? null;
            }
            $suggest = $trailName ?: 'Event';
            if (!empty($dur)) $suggest .= ' — ' . $dur;
            $data['title'] = $suggest;
        }

        $event->update($data);
    // If batch_count provided, regenerate contiguous batches
    if (!empty($data['batch_count']) && !empty($data['start_at'])) {
            // delete existing event batches
            \App\Models\Batch::where('event_id', $event->id)->delete();

            $durationMinutes = 0;
            try {
                if (!empty($data['duration'])) {
                    $duration = trim((string)$data['duration']);
                    if (is_numeric($duration)) {
                        $durationMinutes = intval(round(floatval($duration) * 60));
                    } elseif (preg_match('/^(\d+):(\d{1,2})$/', $duration, $m)) {
                        $durationMinutes = intval($m[1]) * 60 + intval($m[2]);
                    } elseif (preg_match('/^(\d+)\s*d$/i', $duration, $m)) {
                        $durationMinutes = intval($m[1]) * 24 * 60;
                    }
                } else {
                    $package = \App\Models\Trail::find($event->trail_id)?->package;
                    if ($package && !empty($package->duration)) {
                        $duration = trim((string)$package->duration);
                        if (is_numeric($duration)) {
                            $durationMinutes = intval(round(floatval($duration) * 60));
                        } elseif (preg_match('/^(\d+):(\d{1,2})$/', $duration, $m)) {
                            $durationMinutes = intval($m[1]) * 60 + intval($m[2]);
                        } elseif (preg_match('/^(\d+)\s*d$/i', $duration, $m)) {
                            $durationMinutes = intval($m[1]) * 24 * 60;
                        }
                    }
                }
            } catch (\Exception $e) {
                $durationMinutes = 0;
            }

            if ($durationMinutes <= 0) {
                $durationMinutes = 24 * 60;
            }

            $cursor = \Carbon\Carbon::parse($data['start_at']);
            for ($i = 0; $i < intval($data['batch_count']); $i++) {
                $slotEnd = $cursor->copy()->addMinutes($durationMinutes);
                \App\Models\Batch::create([
                    'event_id' => $event->id,
                    'trail_id' => $event->trail_id,
                    'name' => ($event->title ?? 'Event') . ' #' . ($i + 1) . ' @ ' . $cursor->toDateTimeString(),
                    'capacity' => $event->capacity ?? null,
                    'starts_at' => $cursor->copy(),
                    'ends_at' => $slotEnd,
                ]);
                $cursor = $slotEnd->copy();
            }
        }
        // manual batch recreation removed
        return redirect()->route('org.events.index')->with('success','Event updated');
    }

    public function destroy(Event $event)
    {
        if ($event->user_id !== Auth::id()) abort(403);
        $event->delete();
        // remove any linked batch
        \App\Models\Batch::where('event_id', $event->id)->delete();
        return redirect()->route('org.events.index')->with('success','Event deleted');
    }

    /**
     * Compute duration in minutes. Accepts an explicit override string (like "2", "2:30", "2d")
     * or looks up the trail package duration if trail_id supplied.
     */
    protected function computeDurationMinutesFromInput($durationOverride, $trailId = null)
    {
        $durationMinutes = 0;
        try {
            if (!empty($durationOverride)) {
                $duration = trim((string)$durationOverride);
                if (is_numeric($duration)) {
                    $durationMinutes = intval(round(floatval($duration) * 60));
                } elseif (preg_match('/^(\d+):(\d{1,2})$/', $duration, $m)) {
                    $durationMinutes = intval($m[1]) * 60 + intval($m[2]);
                } elseif (preg_match('/^(\d+)\s*d$/i', $duration, $m)) {
                    $durationMinutes = intval($m[1]) * 24 * 60;
                }
            } else {
                if (!empty($trailId)) {
                    $package = \App\Models\Trail::find($trailId)?->package;
                    if ($package && !empty($package->duration)) {
                        $duration = trim((string)$package->duration);
                        if (is_numeric($duration)) {
                            $durationMinutes = intval(round(floatval($duration) * 60));
                        } elseif (preg_match('/^(\d+):(\d{1,2})$/', $duration, $m)) {
                            $durationMinutes = intval($m[1]) * 60 + intval($m[2]);
                        } elseif (preg_match('/^(\d+)\s*d$/i', $duration, $m)) {
                            $durationMinutes = intval($m[1]) * 24 * 60;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $durationMinutes = 0;
        }
        return $durationMinutes;
    }
}
