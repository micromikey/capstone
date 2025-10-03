<?php

namespace App\Http\Controllers\Hiker;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Trail;
use App\Models\Batch;
use App\Models\OrganizationPaymentCredential;
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
        // Log all incoming request data for debugging
        Log::info('Booking store called', [
            'all_data' => $request->all(),
            'user_id' => Auth::id(),
        ]);

        $data = $request->validate([
            'trail_id' => 'required|exists:trails,id',
            'batch_id' => 'nullable|exists:batches,id',
            'event_id' => 'nullable|exists:events,id',
            'date' => 'nullable|date',
            'party_size' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:2000',
        ]);

        Log::info('Validation passed', ['validated_data' => $data]);

        // Remove payment-related validation - payment handled after booking creation

        // If batch_id provided, ensure it belongs to the chosen trail
        $batch = null;
        if (!empty($data['batch_id'])) {
            Log::info('Batch ID provided', ['batch_id' => $data['batch_id']]);
            $batch = Batch::find($data['batch_id']);
            if (! $batch || $batch->trail_id != $data['trail_id']) {
                Log::warning('Batch validation failed', ['batch' => $batch, 'trail_id' => $data['trail_id']]);
                return back()->withInput()->withErrors(['batch_id' => 'Selected batch is invalid for the chosen trail.']);
            }
        } else {
            Log::info('No batch_id provided, attempting to find or create one');
            // Check if we're dealing with an undated event
            if (!empty($data['event_id'])) {
                $event = \App\Models\Event::find($data['event_id']);
                if ($event && $event->always_available && !empty($data['date'])) {
                    // For undated events, we don't use traditional batches
                    // Instead, we'll create the booking directly against the event
                    // But we still need to find or create a batch for compatibility
                    $target = Carbon::parse($data['date'])->startOfDay();
                    
                    // Check capacity for the date
                    $bookedOnDate = Booking::where('event_id', $event->id)
                        ->where('status', '!=', 'cancelled')
                        ->whereDate('date', $target)
                        ->sum('party_size');
                    
                    if ($bookedOnDate + $data['party_size'] > $event->capacity) {
                        return back()->withInput()->withErrors(['date' => 'Not enough capacity available for the selected date.']);
                    }
                    
                    // Create or find a virtual batch for undated events (for compatibility)
                    $batch = Batch::firstOrCreate([
                        'event_id' => $event->id,
                        'trail_id' => $event->trail_id,
                        'starts_at' => null,
                        'ends_at' => null,
                    ], [
                        'name' => 'Always Available: ' . ($event->title ?? 'Event'),
                        'capacity' => $event->capacity,
                    ]);
                } else {
                    // Regular batch selection logic for dated events
                    $query = Batch::where('trail_id', $data['trail_id']);

                    if (!empty($data['date'])) {
                        try {
                            $target = Carbon::parse($data['date'])->startOfDay();
                            $candidate = (clone $query)->whereNotNull('starts_at')
                                ->whereDate('starts_at', $target)
                                ->orderBy('starts_at')
                                ->get()
                                ->first(function($b){
                                    $count = Booking::where('batch_id', $b->id)->where('status','!=','cancelled')->sum('party_size');
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
                              $count = Booking::where('batch_id', $b->id)->where('status','!=','cancelled')->sum('party_size');
                              return $count < $b->capacity;
                          });

                        if ($candidate) {
                            $batch = $candidate;
                        }
                    }
                }
            } else {
                // Regular batch selection logic when no event_id provided
                $query = Batch::where('trail_id', $data['trail_id']);

                if (!empty($data['date'])) {
                    try {
                        $target = Carbon::parse($data['date'])->startOfDay();
                        $candidate = (clone $query)->whereNotNull('starts_at')
                            ->whereDate('starts_at', $target)
                            ->orderBy('starts_at')
                            ->get()
                            ->first(function($b){
                                $count = Booking::where('batch_id', $b->id)->where('status','!=','cancelled')->sum('party_size');
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
                          $count = Booking::where('batch_id', $b->id)->where('status','!=','cancelled')->sum('party_size');
                          return $count < $b->capacity;
                      });

                    if ($candidate) {
                        $batch = $candidate;
                        Log::info('Found candidate batch', ['batch_id' => $batch->id]);
                    } else {
                        Log::warning('No candidate batch found');
                    }
                }
            }
        }

        if (! $batch) {
            Log::error('No batch found', ['trail_id' => $data['trail_id'], 'date' => $data['date'] ?? null]);
            
            // Check if trail has ANY batches at all
            $trailBatchCount = Batch::where('trail_id', $data['trail_id'])->count();
            Log::info('Trail batch count', ['count' => $trailBatchCount]);
            
            if ($trailBatchCount === 0) {
                // No batches exist for this trail - create a default one
                Log::info('Creating default batch for trail', ['trail_id' => $data['trail_id']]);
                $trail = Trail::find($data['trail_id']);
                
                $batch = Batch::create([
                    'trail_id' => $data['trail_id'],
                    'name' => 'Default Batch - ' . ($trail->trail_name ?? 'Trail'),
                    'capacity' => 50, // Default capacity
                    'starts_at' => null, // Always available
                    'ends_at' => null,
                ]);
                Log::info('Default batch created', ['batch_id' => $batch->id]);
            } else {
                return back()->withInput()->withErrors(['batch_id' => 'No available batch found for the selected trail/date.']);
            }
        }

        Log::info('Proceeding with batch', ['batch_id' => $batch->id]);

    // Use a database transaction and lock the batch row to prevent race conditions
        $booking = null;
        DB::beginTransaction();
        try {
            Log::info('Starting transaction');
            // reload and lock the batch row
            $lockedBatch = Batch::where('id', $batch->id)->lockForUpdate()->first();
            if (! $lockedBatch) {
                DB::rollBack();
                return back()->withInput()->withErrors(['batch_id' => 'Selected batch no longer exists.']);
            }

            $requested = intval($data['party_size'] ?? 1);
            
            // Check if batch has enough available slots using the new slot management system
            if (!$lockedBatch->hasAvailableSlots($requested)) {
                DB::rollBack();
                
                $availableSlots = $lockedBatch->getAvailableSlots();
                
                // Suggest alternative dates if this batch is full
                $alternativeBatches = Batch::where('trail_id', $lockedBatch->trail_id)
                    ->where('id', '!=', $lockedBatch->id)
                    ->where('starts_at', '>', now())
                    ->get()
                    ->filter(function($batch) use ($requested) {
                        return $batch->hasAvailableSlots($requested);
                    })
                    ->take(3);
                
                $errorMessage = $availableSlots > 0 
                    ? "Only {$availableSlots} slot(s) available. You requested {$requested}."
                    : "This date is fully booked (0 slots available).";
                
                if ($alternativeBatches->isNotEmpty()) {
                    $dates = $alternativeBatches->map(fn($b) => $b->starts_at->format('M d, Y'))->implode(', ');
                    $errorMessage .= " Try these dates with available slots: {$dates}";
                }
                
                return back()->withInput()->withErrors(['batch_id' => $errorMessage]);
            }

            // create the booking while still in the transaction
            $data['batch_id'] = $lockedBatch->id;
            $data['user_id'] = Auth::id();
            $data['status'] = 'pending'; // Pending until payment is completed
            
            Log::info('Preparing booking data', ['batch_id' => $lockedBatch->id, 'user_id' => Auth::id()]);
            
            // Calculate price_cents: trail price × party size (converted to cents)
            $trail = Trail::with('package')->find($data['trail_id']);
            if ($trail && $trail->price) {
                $data['price_cents'] = (int) ($trail->price * $data['party_size'] * 100);
            } else {
                // Set default price if trail has no price
                $data['price_cents'] = 0;
            }

            // Don't set payment method yet - will be determined on payment page
            $data['payment_status'] = 'pending'; // Initial status (pending, verified, or rejected)
            $data['payment_method_used'] = null; // Will be set when payment is made

            // If an event_id was supplied, ensure the event exists and matches the trail_id
            if (!empty($data['event_id'])) {
                Log::info('Event ID provided', ['event_id' => $data['event_id']]);
                $event = \App\Models\Event::find($data['event_id']);
                if (! $event) {
                    Log::error('Event not found', ['event_id' => $data['event_id']]);
                    DB::rollBack();
                    return back()->withInput()->withErrors(['event_id' => 'Selected event does not exist.']);
                }

                // If the event is linked to a trail, ensure it matches the booking trail
                if ($event->trail_id && $event->trail_id != $data['trail_id']) {
                    Log::error('Event trail mismatch', ['event_trail_id' => $event->trail_id, 'booking_trail_id' => $data['trail_id']]);
                    DB::rollBack();
                    return back()->withInput()->withErrors(['event_id' => 'Selected event is not associated with the chosen trail.']);
                }
            }

            Log::info('Creating booking', ['data' => $data]);
            $booking = Booking::create($data);
            Log::info('Booking created successfully', ['booking_id' => $booking->id]);

            // NOTE: Slots are NOT reserved yet - only reserved after successful payment
            // This prevents holding slots for unpaid bookings

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the actual error for debugging
            Log::error('Booking creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            // fall back to safe error
            return back()->withInput()->withErrors(['batch_id' => 'Unable to create booking at this time. Please try again.']);
        }

        // Redirect to payment page for all bookings
        return redirect()->route('booking.payment', $booking->id)
            ->with('success', 'Booking created! Please complete your payment to confirm your reservation.');
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load('trail', 'user');

        return view('hiker.booking.show', compact('booking'));
    }

    /**
     * Show payment page for a booking
     */
    public function showPayment(Booking $booking)
    {
        $this->authorize('view', $booking);

        // Check if booking is already paid
        if ($booking->payment_status === 'verified' || $booking->payment_status === 'paid') {
            return redirect()->route('booking.show', $booking->id)
                ->with('info', 'This booking has already been paid.');
        }

        // Get organization's payment credentials
        $trail = $booking->trail;
        $credentials = OrganizationPaymentCredential::where('user_id', $trail->user_id)->first();

        // If no credentials, default to manual payment
        if (!$credentials) {
            $credentials = new OrganizationPaymentCredential([
                'payment_method' => 'manual',
                'user_id' => $trail->user_id
            ]);
        }

        $booking->load('trail', 'user');

        return view('hiker.booking.payment', compact('booking', 'credentials'));
    }

    /**
     * Process payment submission
     */
    public function submitPayment(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        // Validate payment method is provided
        $request->validate([
            'payment_method' => 'required|in:manual,automatic',
            'payment_proof' => 'nullable|required_if:payment_method,manual|image|mimes:jpeg,png,jpg|max:10240',
            'transaction_number' => 'nullable|required_if:payment_method,manual|string|max:255',
            'payment_notes' => 'nullable|string|max:1000',
        ]);

        // Handle manual payment
        if ($request->input('payment_method') === 'manual') {
            $booking->payment_method_used = 'manual';
            $booking->payment_status = 'pending'; // Pending verification by org
            
            // Handle payment proof upload
            if ($request->hasFile('payment_proof')) {
                $path = $request->file('payment_proof')->store('payment_proofs', 'public');
                $booking->payment_proof_path = $path;
            }

            if ($request->filled('transaction_number')) {
                $booking->transaction_number = $request->input('transaction_number');
            }

            if ($request->filled('payment_notes')) {
                $booking->payment_notes = $request->input('payment_notes');
            }

            $booking->save();

            return redirect()->route('booking.show', $booking->id)
                ->with('success', 'Payment proof submitted! The organization will verify your payment and confirm your booking.');
        } else {
            // For automatic payments, redirect to payment gateway
            $booking->payment_method_used = 'automatic';
            $booking->save();

            return redirect()->route('payment.create', [
                'booking_id' => $booking->id,
            ])->with('success', 'Redirecting to payment gateway...');
        }
    }

    /**
     * Show the form for editing a booking
     */
    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);

        // Only allow editing if booking is not confirmed and payment is not made
        if ($booking->status === 'confirmed' && $booking->isPaid()) {
            return redirect()->route('booking.show', $booking)
                ->with('error', 'Cannot edit a confirmed and paid booking. Please contact support if you need to make changes.');
        }

        // Only allow editing if the event hasn't started yet
        if ($booking->batch && $booking->batch->starts_at <= now()) {
            return redirect()->route('booking.show', $booking)
                ->with('error', 'Cannot edit a booking for an event that has already started.');
        }

        $booking->load(['trail.package', 'batch']);

        return view('hiker.booking.edit', compact('booking'));
    }

    /**
     * Update the specified booking
     */
    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        // Only allow updating if booking is not confirmed and paid
        if ($booking->status === 'confirmed' && $booking->isPaid()) {
            return back()->with('error', 'Cannot update a confirmed and paid booking.');
        }

        // Only allow updating if the event hasn't started yet
        if ($booking->batch && $booking->batch->starts_at <= now()) {
            return back()->with('error', 'Cannot update a booking for an event that has already started.');
        }

        $validated = $request->validate([
            'date' => 'required|date|after:today',
            'party_size' => 'required|integer|min:1|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $booking->update($validated);

        return redirect()->route('booking.show', $booking)
            ->with('success', 'Booking updated successfully!');
    }

    /**
     * Cancel/delete a booking
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        // Check if booking can be cancelled
        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        // Cancel the booking (this will also release slots if confirmed)
        $booking->cancel();

        return redirect()->route('booking.index')
            ->with('success', 'Booking cancelled successfully. Slots have been released.');
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

        // Separate undated (always available) events from dated events
        $undatedEvents = $events->filter(function($e) {
            return $e->always_available && empty($e->start_at) && empty($e->end_at);
        });

        $datedEvents = $events->filter(function($e) {
            return !($e->always_available && empty($e->start_at) && empty($e->end_at));
        });

        // Debug log: list matched events and their start times
        Log::info('trailBatches: matched events', ['trail_id' => $trail->id, 'events' => $events->map(fn($e) => ['id' => $e->id, 'start_at' => (string)$e->start_at, 'always_available' => $e->always_available])->all()]);

        $events_count = $events->count();
        if ($events_count === 0) {
            // no events => return empty array for backward compatibility, and include
            // metadata via response headers so the frontend can show a clear message.
            return response()->json([])
                ->header('X-Events-Count', 0)
                ->header('X-Booking-Enabled', 'false');
        }

        // Handle undated events specially - calculate daily slots for the target date
        $undatedSlots = [];
        if ($undatedEvents->count() > 0 && !empty($target)) {
            foreach ($undatedEvents as $event) {
                // For undated events, calculate available slots for the specific date
                $capacity = $event->capacity ?? 0;
                if ($capacity > 0) {
                    // Count existing bookings for this event on the target date
                    $bookedOnDate = \App\Models\Booking::where('event_id', $event->id)
                        ->where('status', '!=', 'cancelled')
                        ->whereDate('date', $target)
                        ->sum('party_size');

                    $remaining = max(0, $capacity - $bookedOnDate);

                    $undatedSlots[] = [
                        'type' => 'undated_event',
                        'id' => $event->id,
                        'name' => $event->title ?? $event->name ?? 'Always Available',
                        'capacity' => $capacity,
                        'remaining' => $remaining,
                        'starts_at' => null,
                        'ends_at' => null,
                        'starts_at_formatted' => null,
                        'ends_at_formatted' => null,
                        'event_id' => $event->id,
                        'event_title' => $event->title ?? $event->name ?? null,
                        'slot_label' => ($event->title ?? 'Always Available') . ' — ' . $target->format('M j, Y') . ' (' . $remaining . ' spots left)',
                        'is_always_available' => true,
                        'selected_date' => $target->format('Y-m-d'),
                    ];
                }
            }
        }

        // Only consider batches that are associated with DATED events (not undated ones)
        // Batches by themselves do not represent open slots unless they belong to an Event.
        // Eager-load the event relation so we can include event metadata in the response.
        $datedEventIds = $datedEvents->pluck('id')->toArray();
        $query = Batch::where('trail_id', $trail->id)->whereNotNull('event_id')
            ->with('event')
            ->whereIn('event_id', $datedEventIds);

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
    Log::info('trailBatches: batches fetched', ['trail_id' => $trail->id, 'batches_count' => $batchesRaw->count(), 'dated_event_ids' => $datedEventIds ?? []]);

        $batches = $batchesRaw->map(function($b){
            // Use the slots_taken field from the Batch model for accurate slot tracking
            $remaining = $b->getAvailableSlots(); // This uses: capacity - slots_taken

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

        // Map previously-fetched DATED events into the slot shape. Events will include
        // event metadata fields as well so the frontend can render consistent labels.
        // When a target date is provided, only include event 'slots' if the
        // event's own start_at occurs on that date. Otherwise, we will rely
        // on the batches for the selected date and avoid showing the event's
        // original start date as a selectable slot (which confused the UI).
        $eventsToMap = $datedEvents;
        if (!empty($target)) {
            $eventsToMap = $datedEvents->filter(function($ev) use ($target) {
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

        // Merge events, batches, and undated slots, preferring events when both have the same starts_at timestamp.
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

        // Add undated slots (these will have unique keys since they include date)
        foreach ($undatedSlots as $u) {
            $key = 'undated_' . $u['id'] . '_' . ($u['selected_date'] ?? 'nodate');
            $mergedMap[$key] = $u;
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
            ->header('X-Dated-Events-Count', $datedEvents->count())
            ->header('X-Undated-Events-Count', $undatedEvents->count())
            ->header('X-Booking-Enabled', 'true');
    }
}
