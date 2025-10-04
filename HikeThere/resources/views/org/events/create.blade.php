<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            <x-trail-breadcrumb />
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Create Event') }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Create Event</h2>
                
                <!-- Alert Messages -->
                <div id="ajax-alert" class="hidden mb-4 p-4 rounded-lg" role="alert">
                    <div class="flex items-center">
                        <svg id="alert-icon" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span id="alert-message" class="font-medium"></span>
                    </div>
                </div>

                <form id="event-create-form" method="POST" action="{{ route('org.events.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf

                    <div class="mb-4 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input id="event_title_input" type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Sunrise Ridge — Day Hike" class="mt-1 block w-full border border-gray-200 rounded-md shadow-sm p-2 font-bold">
                    </div>

                    <div class="mb-4 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Trail</label>
                        <select name="trail_id" id="trail_select" class="mt-1 block w-full border border-gray-200 rounded-md shadow-sm p-2">
                            <option value="" disabled {{ !old('trail_id') && !isset($preselectedTrailId) ? 'selected' : '' }}>Select Trail</option>
                            @foreach($trails as $trail)
                            <option value="{{ $trail->id }}" 
                                data-duration="{{ optional($trail->package)->duration }}" 
                                @if(old('trail_id') == $trail->id || (isset($preselectedTrailId) && $preselectedTrailId == $trail->id)) selected @endif>
                                {{ $trail->trail_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Duration</label>
                        <div class="mt-2 flex items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div id="duration_preview" class="flex items-center gap-2 px-4 py-2 rounded-full bg-[#eef6f4] text-[#0f5132] border border-[#cfe6df] shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#0f5132]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 11.88a1 1 0 101.414 1.414L11 11.414V7z" clip-rule="evenodd" />
                                    </svg>
                                    <span id="duration_preview_text" class="font-medium">—</span>
                                </div>
                                <div class="text-xs text-gray-500">per batch</div>
                            </div>

                            <div class="ml-auto">
                                <button type="button" id="toggle_override" class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-md text-sm shadow-sm hover:bg-gray-50" aria-pressed="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M4 13V7a1 1 0 011-1h6l3 3v4a1 1 0 01-1 1H5a1 1 0 01-1-1z" />
                                    </svg>
                                    Override
                                </button>
                            </div>
                        </div>
                        <div id="duration_override_row" class="mt-3 hidden">
                            <input type="text" id="duration_override_input" name="duration" value="{{ old('duration') }}" class="mt-1 block w-full border border-gray-200 rounded-md shadow-sm p-2" placeholder="e.g. 2, 2:00, 2d" />
                            <p class="text-xs text-gray-500 mt-2">Leave empty to use the trail package duration. Use decimals (e.g. <code>1.5</code>) for half hours, <code>H:MM</code> for exact time, or <code>2d</code> for days.</p>
                        </div>
                    </div>

                    <div class="mb-4 md:col-span-2">
                        @include('partials.trail-package-preview')
                    </div>

                    <div class="mb-4 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" class="mt-1 block w-full border border-gray-200 rounded-md shadow-sm p-2">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Number of batches (optional)</label>
                        <input type="number" name="batch_count" value="{{ old('batch_count') }}" min="1" class="mt-1 block w-full border border-gray-200 rounded-md shadow-sm p-2" placeholder="e.g. 10" {{ old('always_available') ? 'disabled aria-disabled=true' : '' }} />
                        <p class="text-xs text-gray-500 mt-1">If provided, the server will generate this many contiguous batches starting from the event start time using the duration (event duration override or trail package duration). Each batch will be a slot for bookings.</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Capacity (Maximum Pax per Batch)</label>
                        <input type="number" name="capacity" value="{{ old('capacity') }}" min="1" class="mt-1 block w-full border border-gray-200 rounded-md shadow-sm p-2" placeholder="Number of spots" />
                    </div>

                    <div class="mb-4 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Hiking Start Time</label>
                        <input type="time" name="hiking_start_time" value="{{ old('hiking_start_time') }}" class="mt-1 block w-full border border-gray-200 rounded-md shadow-sm p-2" placeholder="e.g. 08:00">
                        <p class="text-xs text-gray-500 mt-1">What time should the hike start? This helps participants know when to arrive at the trail.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Start</label>
                            <input type="datetime-local" name="start_at" value="{{ old('start_at') }}" class="mt-1 block w-full border border-gray-200 rounded-md shadow-sm p-2">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">End</label>
                            <input type="datetime-local" name="end_at" value="{{ old('end_at') }}" class="mt-1 block w-full border border-gray-200 rounded-md shadow-sm p-2">
                        </div>
                    </div>

                    <div class="mb-4 flex items-start gap-3">
                        <div class="flex items-center h-5">
                            <input type="hidden" name="always_available" value="0" />
                            <input id="always_available" name="always_available" type="checkbox" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                        </div>
                        <div class="text-sm">
                            <label for="always_available" class="font-medium text-gray-700">Always available</label>
                            <p class="text-xs text-gray-500">If checked, the event is continuously available and start/end will be ignored. The system will create undated (always-open) batches instead of dated slots; there is no limit on how many bookings can be made over time, but each booking still consumes one spot in a batch up to the configured <strong>Capacity</strong> (max pax per batch).</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 md:col-span-2">
                        <a href="{{ route('org.events.index') }}" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">Cancel</a>
                        <button type="submit" id="submit-btn" class="inline-flex items-center px-4 py-2 bg-[#336d66] text-white rounded hover:bg-[#2a5a54] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="submit-text">Create Event</span>
                            <svg id="submit-spinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    (function() {
        // always_available toggling and duration preview/override
        const always = document.getElementById('always_available');
        const startInput = document.querySelector('input[name="start_at"]');
        const endInput = document.querySelector('input[name="end_at"]');

        function toggleAlways() {
            if (!always) return;
            const disabled = always.checked;
            if (startInput) {
                startInput.disabled = disabled;
                startInput.setAttribute('aria-disabled', disabled ? 'true' : 'false');
                startInput.classList.toggle('opacity-50', disabled);
                startInput.classList.toggle('cursor-not-allowed', disabled);
            }
            if (endInput) {
                endInput.disabled = disabled;
                endInput.setAttribute('aria-disabled', disabled ? 'true' : 'false');
                endInput.classList.toggle('opacity-50', disabled);
                endInput.classList.toggle('cursor-not-allowed', disabled);
            }
            // also disable the batch count input when "Always available" is checked
            try {
                const bc = document.querySelector('input[name="batch_count"]');
                if (bc) {
                    bc.disabled = disabled;
                    bc.setAttribute('aria-disabled', disabled ? 'true' : 'false');
                    bc.classList.toggle('opacity-50', disabled);
                    bc.classList.toggle('cursor-not-allowed', disabled);
                    // clear value when disabled so server receives no value (null)
                    if (disabled) bc.value = '';
                }
            } catch (e) {
                console.warn('batchCountInput toggle error', e);
            }
        }

        // duration preview logic
        const trailSelect = document.getElementById('trail_select');
        const preview = document.getElementById('duration_preview');
        const toggleBtn = document.getElementById('toggle_override');
        const overrideRow = document.getElementById('duration_override_row');
        const overrideInput = document.getElementById('duration_override_input');

        function updatePreview() {
            // if override input has a value, prefer that
            const overrideVal = overrideInput?.value?.trim();
            if (overrideVal) {
                preview.textContent = overrideVal;
                return;
            }
            const sel = trailSelect?.selectedOptions?.[0];
            const dur = sel?.dataset?.duration;
            preview.textContent = dur ? dur : '—';
        }

        toggleBtn?.addEventListener('click', function() {
            if (!overrideRow) return;
            overrideRow.classList.toggle('hidden');
            // when showing, focus the input
            if (!overrideRow.classList.contains('hidden')) {
                overrideInput?.focus();
            }
            updatePreview();
        });

        trailSelect?.addEventListener('change', updatePreview);
        overrideInput?.addEventListener('input', updatePreview);

        always?.addEventListener('change', toggleAlways);
        toggleAlways();
        updatePreview();

        // auto-calc end_at from start_at, batch_count and duration (skip when always available)
        const batchCountInput = document.querySelector('input[name="batch_count"]');
        const startInputEl = document.querySelector('input[name="start_at"]');
        const endInputEl = document.querySelector('input[name="end_at"]');

        function parseDurationToMinutes(str) {
            if (!str) return 0;
            str = str.trim();
            // accept integer or decimal hours (e.g. 1, 1.5)
            if (/^\d+(?:\.\d+)?$/.test(str)) {
                return Math.round(parseFloat(str) * 60);
            }
            const m = str.match(/^(\d+):(\d{1,2})$/);
            if (m) return parseInt(m[1], 10) * 60 + parseInt(m[2], 10);
            const d = str.match(/^(\d+)\s*d$/i);
            if (d) return parseInt(d[1], 10) * 24 * 60;
            return 0;
        }

        function autoCalcEnd() {
            if (!startInputEl || !endInputEl) return;
            if (always?.checked) return; // don't auto when always available
            const startVal = startInputEl.value;
            const batchCount = parseInt(batchCountInput?.value || '0', 10);
            if (!startVal || !batchCount || batchCount <= 0) return;
            // pick duration: override input value if present, else trail package
            const durationStr = (overrideInput?.value?.trim()) || (trailSelect?.selectedOptions?.[0]?.dataset?.duration);
            const minutes = parseDurationToMinutes(durationStr) || (24 * 60);
            const totalMinutes = minutes * batchCount;
            const startDate = new Date(startVal);
            if (isNaN(startDate.getTime())) return;
            const endDate = new Date(startDate.getTime() + totalMinutes * 60 * 1000);
            // format as yyyy-MM-ddThh:mm
            const pad = s => (s < 10 ? '0' + s : s);
            const formatted = endDate.getFullYear() + '-' + pad(endDate.getMonth() + 1) + '-' + pad(endDate.getDate()) + 'T' + pad(endDate.getHours()) + ':' + pad(endDate.getMinutes());
            endInputEl.value = formatted;
        }

        // listen to both 'change' and 'input' for prompt updates
        [startInputEl, batchCountInput, overrideInput, trailSelect, always]?.forEach(el => {
            if (!el) return;
            el.addEventListener('change', autoCalcEnd);
            // for text/number inputs we also want immediate response
            el.addEventListener('input', autoCalcEnd);
        });
        // run once
        autoCalcEnd();
    })();
</script>
<script>
    (function() {
        function _callInit() {
            try {
                if (typeof window.initializeTrailPreview === 'function') window.initializeTrailPreview('trail_select');
            } catch (e) {
                console.warn(e);
            }
        }
        // Call immediately if available, otherwise wait for DOMContentLoaded (the compiled bundle attaches the initializer there).
        if (typeof window.initializeTrailPreview === 'function') {
            _callInit();
        } else {
            document.addEventListener('DOMContentLoaded', _callInit);
            // fallback in case DOMContentLoaded already fired or attach was delayed
            setTimeout(_callInit, 100);
        }
    })();
</script>

<script>
    // Dynamic title placeholder: use selected trail name and duration (if present)
    (function() {
        const trailSelect = document.getElementById('trail_select');
        const titleInput = document.getElementById('event_title_input');

        function formatDurationForPlaceholder(d) {
            if (!d) return null;
            // prefer human-friendly already-formatted duration, else raw
            return d;
        }

        function updatePlaceholder() {
            if (!titleInput) return;
            // don't overwrite user-typed title
            if (titleInput.value && titleInput.value.trim().length > 0) return;

            const selected = trailSelect?.selectedOptions?.[0];
            // ignore the placeholder/disabled option (empty value)
            if (!selected || !selected.value || selected.disabled) {
                titleInput.placeholder = 'e.g. Sunrise Ridge — Day Hike';
                return;
            }

            const trailName = selected.textContent?.trim() || '';
            const duration = selected.dataset?.duration || '';
            let suggestion = trailName || 'New Event';
            const dur = formatDurationForPlaceholder(duration);
            if (dur) suggestion += ' — ' + dur;
            titleInput.placeholder = suggestion;
        }

        trailSelect?.addEventListener('change', updatePlaceholder);
        // init on load
        document.addEventListener('DOMContentLoaded', updatePlaceholder);
        // run once in case DOMContentLoaded already fired
        setTimeout(updatePlaceholder, 50);
    })();
</script>
    <script>
        (function(){
            // Ensure the Title is populated on submit if left empty using the generated suggestion
            const form = document.querySelector('form');
            const titleInput = document.getElementById('event_title_input');
            const trailSelect = document.getElementById('trail_select');

            function buildSuggestedTitle() {
                if (!trailSelect) return titleInput?.placeholder || '';
                const selected = trailSelect.selectedOptions?.[0];
                if (!selected || !selected.value || selected.disabled) return titleInput?.placeholder || '';
                const trailName = selected.textContent?.trim() || '';
                const duration = selected.dataset?.duration || '';
                return trailName + (duration ? (' — ' + duration) : '');
            }

            form?.addEventListener('submit', function(e){
                if (!titleInput) return;
                if (!titleInput.value || titleInput.value.trim().length === 0) {
                    // populate with suggestion so server receives a useful title
                    titleInput.value = buildSuggestedTitle() || titleInput.placeholder || '';
                }
            });
        })();
    </script>
@if(isset($preselectedTrailId))
<script>
    // Trigger trail selection events when a trail is pre-selected
    document.addEventListener('DOMContentLoaded', function() {
        const trailSelect = document.getElementById('trail_select');
        if (trailSelect && trailSelect.value) {
            // Trigger change event to update duration preview and other dependent fields
            trailSelect.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Also trigger the trail preview initialization
            setTimeout(function() {
                if (typeof window.initializeTrailPreview === 'function') {
                    window.initializeTrailPreview('trail_select');
                }
            }, 200);
        }
    });
</script>
@endif

<script>
    // AJAX Event Creation Handler
    (function() {
        const form = document.getElementById('event-create-form');
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const submitSpinner = document.getElementById('submit-spinner');
        const alertBox = document.getElementById('ajax-alert');
        const alertMessage = document.getElementById('alert-message');
        const alertIcon = document.getElementById('alert-icon');

        function showAlert(message, type = 'success') {
            alertBox.classList.remove('hidden', 'bg-green-100', 'bg-red-100', 'text-green-800', 'text-red-800');
            
            if (type === 'success') {
                alertBox.classList.add('bg-green-100', 'text-green-800');
                alertIcon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>';
            } else {
                alertBox.classList.add('bg-red-100', 'text-red-800');
                alertIcon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>';
            }
            
            alertMessage.textContent = message;
            
            // Scroll to alert
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    alertBox.classList.add('hidden');
                }, 5000);
            }
        }

        function clearFieldErrors() {
            // Remove all error messages
            document.querySelectorAll('.text-red-500.text-sm').forEach(el => el.remove());
            // Remove red borders from inputs
            document.querySelectorAll('input.border-red-500, select.border-red-500, textarea.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-200');
            });
        }

        function showFieldErrors(errors) {
            clearFieldErrors();
            
            Object.keys(errors).forEach(fieldName => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    // Add red border
                    field.classList.remove('border-gray-200');
                    field.classList.add('border-red-500');
                    
                    // Add error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-red-500 text-sm mt-1';
                    errorDiv.textContent = errors[fieldName][0]; // First error message
                    
                    // Insert after the field
                    field.parentNode.insertBefore(errorDiv, field.nextSibling);
                }
            });
            
            // Scroll to first error
            const firstError = form.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }

        function setLoading(loading) {
            submitBtn.disabled = loading;
            
            if (loading) {
                submitText.textContent = 'Creating...';
                submitSpinner.classList.remove('hidden');
            } else {
                submitText.textContent = 'Create Event';
                submitSpinner.classList.add('hidden');
            }
        }

        form?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearFieldErrors();
            alertBox.classList.add('hidden');
            
            // Set loading state
            setLoading(true);
            
            // Get form data
            const formData = new FormData(form);
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (response.ok) {
                    // Success
                    showAlert(data.message || 'Event created successfully!', 'success');
                    
                    // Reset form
                    form.reset();
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route("org.events.index") }}';
                    }, 1500);
                } else {
                    // Validation errors or other errors
                    if (data.errors) {
                        showFieldErrors(data.errors);
                        showAlert('Please fix the errors below.', 'error');
                    } else {
                        showAlert(data.message || 'An error occurred while creating the event.', 'error');
                    }
                }
            } catch (error) {
                console.error('AJAX Error:', error);
                showAlert('Network error. Please check your connection and try again.', 'error');
            } finally {
                setLoading(false);
            }
        });
    })();
</script>
