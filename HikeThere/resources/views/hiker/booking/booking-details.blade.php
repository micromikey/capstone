<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Create Booking</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">Reserve a spot for a guided hike or campsite. We'll keep your booking details here.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 p-6 max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div class="md:col-span-1">
                        <form action="{{ route('booking.store') }}" method="POST" class="space-y-4">
                    @csrf
                    {{-- Prefill event association (optional) --}}
                    @if(!empty($prefill['event_id'] ?? null))
                        <input type="hidden" name="event_id" value="{{ old('event_id', $prefill['event_id']) }}" />
                    @endif

                    @if(!empty($organizations) && $organizations->isNotEmpty())
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Select Organization</label>
                            <select id="organization_select" name="organization_id" class="mt-1 block w-full border rounded p-2">
                                <option value="" disabled selected>Select organization</option>
                                @foreach($organizations as $org)
                                    {{-- controller now returns users.id as organization_id to avoid ambiguous column names --}}
                                    <option value="{{ $org->organization_id }}">{{ $org->organization_name ?? $org->name ?? $org->display_name ?? 'Organization' }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Trail</label>
                        <select id="trail_select" name="trail_id" required class="mt-1 block w-full border rounded p-2">
                            <option value="" disabled selected>Select trail</option>
                            @foreach($trails as $trail)
                                <option value="{{ $trail->id }}" {{ old('trail_id') == $trail->id ? 'selected' : '' }}>{{ $trail->trail_name }}</option>
                            @endforeach
                        </select>
                        @error('trail_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror

                        {{-- preview is shown in the sticky aside; keep DOM IDs available by including markup only once below the aside --}}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                                @php $today = \Carbon\Carbon::now()->toDateString(); @endphp
                                <input type="date" name="date" min="{{ $today }}" value="{{ old('date') }}" class="mt-1 block w-full border rounded p-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Party size</label>
                            <input type="number" name="party_size" value="1" min="1" class="mt-1 block w-full border rounded p-2" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Choose time slot</label>
                        <select id="batch_select" name="batch_id" class="mt-1 block w-full border rounded p-2">
                            <option value="" disabled selected>Pick a trail and date to see available slots</option>
                        </select>
                        <p id="batch_help" class="text-sm text-gray-500 mt-1">Remaining spots will be shown per slot. If a slot is full it will be listed as unavailable.</p>
                        @error('batch_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="4" class="mt-1 block w-full border rounded p-2" placeholder="Any special requests or accessibility needs"></textarea>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-emerald-800">Create Booking</button>
                        <a href="{{ route('booking.index') }}" class="text-gray-600">Cancel</a>
                    </div>
                </form>
            </div>

                    <aside class="md:col-span-1 self-start sticky top-6">
                        <div class="bg-gray-50 border border-gray-100 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-gray-800">Preview</h4>
                            <p class="text-sm text-gray-600">Select a trail to see package details and available times.</p>
                            <div class="mt-3">
                                @include('partials.trail-package-preview')
                            </div>
                            <div class="mt-4 text-sm text-gray-500">
                                <div><strong>Selected slot:</strong> <span id="selected_slot_label">—</span></div>
                                <div class="mt-2"><strong>Remaining spots:</strong> <span id="selected_slot_remaining">—</span></div>
                            </div>
                        </div>
                    </aside>
                </div>
                @if(!empty($prefill))
                    <script>
                        window.BOOKING_PREFILL = @json($prefill);
                    </script>
                @endif
            </div>
        </div>
    </div>
    <script>
        (function(){
            const defaultTrailImage = '{{ asset("img/default-trail.jpg") }}';
            const orgSelect = document.getElementById('organization_select');
            const trailSelect = document.getElementById('trail_select');
            const dateInput = document.querySelector('input[name="date"]');
            const batchSelect = document.getElementById('batch_select');

                    orgSelect?.addEventListener('change', async function(){
                        const orgId = this.value;
                // clear previous trails; insert a disabled placeholder so users must pick a trail
                trailSelect.innerHTML = '<option value="" disabled selected>Select trail</option>';

                if (!orgId) return;

                try {
                    const res = await fetch(`{{ url('/') }}/hiker/api/organization/${orgId}/trails`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });

                    // When trail selection changes, fetch package preview
            const packagePreview = document.getElementById('trail_package_preview');
            const previewTitle = document.getElementById('preview_title');
            const previewSummary = document.getElementById('preview_summary');
            const previewDuration = document.getElementById('preview_duration');
            const previewPrice = document.getElementById('preview_price');
            const previewInclusions = document.getElementById('preview_inclusions');
            const previewSideTrips = document.getElementById('preview_side_trips');
            // missing DOM refs
            const previewSpinner = document.getElementById('preview_spinner');
            const previewError = document.getElementById('preview_error');
            const previewImage = document.getElementById('preview_image');

            trailSelect?.addEventListener('change', async function(){
                const trailId = this.value;
                // hide preview when nothing selected
                if (!trailId) {
                    packagePreview.classList.add('hidden');
                    return;
                }

                // show spinner
                previewSpinner.style.display = '';
                previewError.classList.add('hidden');

                try {
                    const res = await fetch(`{{ url('/') }}/hiker/api/trail/${trailId}/package`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                    previewSpinner.style.display = 'none';
                    packagePreview.classList.remove('hidden');

                    if (res.status === 403) {
                        previewError.textContent = 'You must follow this organization to view package details.';
                        previewError.classList.remove('hidden');
                        return;
                    }

                    if (!res.ok) {
                        previewError.textContent = 'Unable to load package details. (Error ' + res.status + ')';
                        previewError.classList.remove('hidden');
                        return;
                    }

                    const pkg = await res.json();
                    previewTitle.textContent = pkg.trail_name || 'Trail Package Preview';
                    previewSummary.textContent = pkg.summary || pkg.description || '';
                    previewDuration.textContent = pkg.duration ?? '—';
                    previewPrice.textContent = pkg.price ? (pkg.price + ' PHP') : 'Free / N/A';

                    // opening/closing times from package (HH:MM expected)
                    // Prefer server-side formatted times when available
                    const opening = pkg.opening_time_formatted ?? pkg.opening_time ?? null;
                    const closing = pkg.closing_time_formatted ?? pkg.closing_time ?? null;
                    const opener = window.formatTimeForPH ?? (v => (v || '—'));
                    document.getElementById('preview_opening').textContent = opener(opening);
                    document.getElementById('preview_closing').textContent = opener(closing);

                    // estimated time comes from trail (minutes) and a formatted string
                    const estFormatted = pkg.estimated_time_formatted ?? null;
                    const estRaw = pkg.estimated_time ?? null;
                    document.getElementById('preview_estimated_time').textContent = estFormatted || (estRaw ? (estRaw + ' m') : '—');

                    // image
                    previewImage.src = pkg.image || defaultTrailImage;

                    // package_inclusions (array expected)
                    previewInclusions.innerHTML = '';
                    if (pkg.package_inclusions && Array.isArray(pkg.package_inclusions) && pkg.package_inclusions.length) {
                        pkg.package_inclusions.forEach(i => { const li = document.createElement('li'); li.textContent = i; previewInclusions.appendChild(li); });
                    } else if (pkg.package_inclusions && typeof pkg.package_inclusions === 'string') {
                        const li = document.createElement('li'); li.textContent = pkg.package_inclusions; previewInclusions.appendChild(li);
                    } else {
                        const li = document.createElement('li'); li.textContent = '—'; previewInclusions.appendChild(li);
                    }

                    // side_trips
                    previewSideTrips.innerHTML = '';
                    if (pkg.side_trips && Array.isArray(pkg.side_trips) && pkg.side_trips.length) {
                        pkg.side_trips.forEach(i => { const li = document.createElement('li'); li.textContent = i; previewSideTrips.appendChild(li); });
                    } else if (pkg.side_trips && typeof pkg.side_trips === 'string') {
                        const li = document.createElement('li'); li.textContent = pkg.side_trips; previewSideTrips.appendChild(li);
                    } else {
                        const li = document.createElement('li'); li.textContent = '—'; previewSideTrips.appendChild(li);
                    }
                } catch (err) {
                    previewSpinner.style.display = 'none';
                    previewError.textContent = 'Unable to load package details.';
                    previewError.classList.remove('hidden');
                    console.error(err);
                }
            });
                // when trail changes, also refresh available batches
                trailSelect?.addEventListener('change', fetchBatches);
                // when date changes, refresh batches
                dateInput?.addEventListener('change', fetchBatches);

                async function fetchBatches(){
                    const trailId = trailSelect?.value;
                    const date = dateInput?.value;
                    if (!trailId) {
                        batchSelect.innerHTML = '<option value="" disabled selected>Pick a trail and date to see available slots</option>';
                        return;
                    }

                    batchSelect.innerHTML = '<option value="" disabled>Loading slots...</option>';

                    try {
                        const url = new URL(`{{ url('/') }}/hiker/api/trail/${trailId}/batches`);
                        if (date) url.searchParams.set('date', date);

                        const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                        if (!res.ok) {
                            batchSelect.innerHTML = '<option value="" disabled selected>Unable to load slots</option>';
                            return;
                        }

                        const slots = await res.json();
                        if (!slots || !slots.length) {
                            batchSelect.innerHTML = '<option value="" disabled selected>No available slots for selected date</option>';
                            return;
                        }

                        batchSelect.innerHTML = '<option value="" disabled selected>Select a slot</option>';
                        slots.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.id;
                            // attach helpful data attributes for preview
                            if (typeof s.remaining !== 'undefined') opt.dataset.remaining = s.remaining;
                            // Prefer server-provided normalized label when available
                            let label = s.slot_label ?? '';
                            if (!label) {
                                label = s.name || '';
                                if (s.starts_at_formatted) label += ' — ' + s.starts_at_formatted + (s.ends_at_formatted ? (' to ' + s.ends_at_formatted) : '');
                                label += ' (' + (s.remaining ?? 0) + ' spots left)';
                            }
                            opt.textContent = label;
                            batchSelect.appendChild(opt);
                        });
                        // update selected slot preview when options are loaded
                        batchSelect.dispatchEvent(new Event('change'));
                    } catch (err) {
                        console.error(err);
                        batchSelect.innerHTML = '<option value="" disabled selected>Unable to load slots</option>';
                    }
                }
                // when user picks a slot, update the preview panel with remaining info
                batchSelect?.addEventListener('change', function(){
                    const sel = batchSelect.selectedOptions[0];
                    const labelEl = document.getElementById('selected_slot_label');
                    const remEl = document.getElementById('selected_slot_remaining');
                    if (!sel) {
                        if (labelEl) labelEl.textContent = '—';
                        if (remEl) remEl.textContent = '—';
                        return;
                    }
                    // prefer option text for label
                    if (labelEl) labelEl.textContent = sel.textContent || '—';
                    if (remEl) remEl.textContent = (sel.dataset.remaining !== undefined) ? sel.dataset.remaining : '—';
                });
                    if (!res.ok) throw new Error('Failed to load');
                    const trails = await res.json();
                    trails.forEach(t => {
                        const opt = document.createElement('option');
                        opt.value = t.id;
                        opt.textContent = t.trail_name || t.name || 'Trail';
                        trailSelect.appendChild(opt);
                    });
                } catch (err) {
                    console.error(err);
                }
            });
        })();
    </script>
            <script>
                // Initialize shared preview (if the compiled assets expose it). Use safe call to handle attach timing.
                (function(){
                    function _callInit() {
                        try { if (typeof window.initializeTrailPreview === 'function') window.initializeTrailPreview('trail_select'); }
                        catch (e) { console.warn(e); }
                    }
                    if (typeof window.initializeTrailPreview === 'function') {
                        _callInit();
                    } else {
                        document.addEventListener('DOMContentLoaded', _callInit);
                        setTimeout(_callInit, 100);
                    }
                })();

                // Auto-apply prefill when provided
                (function(){
                    const pre = window.BOOKING_PREFILL || null;
                    if (!pre) return;

                    document.addEventListener('DOMContentLoaded', async function(){
                        try {
                            const orgSelect = document.getElementById('organization_select');
                            const trailSelect = document.getElementById('trail_select');
                            const dateInput = document.querySelector('input[name="date"]');

                            // If organization_id provided, select it and trigger change to load trails
                            if (pre.organization_id && orgSelect) {
                                const opt = Array.from(orgSelect.options).find(o => o.value == pre.organization_id);
                                if (opt) {
                                    orgSelect.value = opt.value;
                                    orgSelect.dispatchEvent(new Event('change'));
                                }
                            }

                            // Wait a short time for trails to load
                            if (pre.trail_id && trailSelect) {
                                // poll for trail option
                                const pickOption = () => {
                                    const opt = Array.from(trailSelect.options).find(o => o.value == pre.trail_id);
                                    if (opt) {
                                        trailSelect.value = opt.value;
                                        trailSelect.dispatchEvent(new Event('change'));
                                        return true;
                                    }
                                    return false;
                                };

                                // try for up to ~2s
                                let attempts = 0;
                                while(attempts < 40) {
                                    if (pickOption()) break;
                                    await new Promise(r => setTimeout(r, 50));
                                    attempts++;
                                }
                            }

                            if (pre.date && dateInput) {
                                dateInput.value = pre.date;
                                dateInput.dispatchEvent(new Event('change'));
                            }
                        } catch (err) {
                            console.error('Failed to apply booking prefill', err);
                        }
                    });
                })();
            </script>
</x-app-layout>
