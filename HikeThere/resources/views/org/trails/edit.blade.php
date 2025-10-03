<script>
    document.addEventListener('DOMContentLoaded', function() {
        const transportCheckbox = document.getElementById('transport_included');
        const pickupTimeContainer = document.getElementById('pickup_time_container');
        const departureTimeContainer = document.getElementById('departure_time_container');
        if (transportCheckbox) {
            transportCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // Transport included: show pickup time (organization picks up)
                    pickupTimeContainer.classList.remove('hidden');
                    departureTimeContainer.classList.add('hidden');
                } else {
                    // Transport not included: show departure time (self-commute)
                    pickupTimeContainer.classList.add('hidden');
                    departureTimeContainer.classList.remove('hidden');
                }
            });
            // Initial state
            if (transportCheckbox.checked) {
                // Transport included: show pickup time (organization picks up)
                pickupTimeContainer.classList.remove('hidden');
                departureTimeContainer.classList.add('hidden');
            } else {
                // Transport not included: show departure time (self-commute)
                pickupTimeContainer.classList.add('hidden');
                departureTimeContainer.classList.remove('hidden');
            }
        }
    });
</script>

<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb currentPage="Edit Trail" />
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Trail') }}: {{ $trail->trail_name }}
                </h2>
                <a href="{{ route('org.trails.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="inline-block w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Trails
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Google Maps API Key Meta Tag -->
    <meta name="google-maps-api-key" content="{{ config('services.google.maps_api_key') }}">
    <!-- CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <form method="POST" action="{{ route('org.trails.update', $trail) }}" class="p-6" id="trailEditForm" novalidate>
                    @csrf
                    @method('PUT')
                    <!-- Hidden field for estimated_time (integer hours). Kept hidden and defaults to 0 -->
                    <input type="hidden" id="estimated_time" name="estimated_time" value="{{ old('estimated_time', $trail->estimated_time ?? '0') }}" />
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        </div>

                        <div>
                            <x-label for="mountain_name" value="Mountain Name *" />
                            <x-input id="mountain_name" type="text" name="mountain_name" class="mt-1 block w-full" value="{{ old('mountain_name', $trail->mountain_name) }}" required />
                            <x-input-error for="mountain_name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="trail_name" value="Trail Name *" />
                            <x-input id="trail_name" type="text" name="trail_name" class="mt-1 block w-full" value="{{ old('trail_name', $trail->trail_name) }}" required />
                            <x-input-error for="trail_name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="location_search" value="Location *" />
                            <div class="relative mt-1">
                                <input type="text" id="location_search" placeholder="Search for a location using Google Places..." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" value="{{ old('location_search', $trail->location ? $trail->location->name . ', ' . $trail->location->province : '') }}">
                                <input type="hidden" id="location_id" name="location_id" value="{{ old('location_id', $trail->location_id) }}" required>
                                <input type="hidden" id="location_latitude" name="location_latitude" value="{{ old('location_latitude', $trail->location ? $trail->location->latitude : '') }}">
                                <input type="hidden" id="location_longitude" name="location_longitude" value="{{ old('location_longitude', $trail->location ? $trail->location->longitude : '') }}">
                            </div>
                            <div id="location_loading" class="mt-2 text-sm text-blue-600 hidden">
                                Processing location...
                            </div>
                            <div class="mt-2 text-sm text-gray-600">
                                <p>Start typing to search for locations using Google Places Autocomplete</p>
                            </div>
                            <x-input-error for="location_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="price" value="Price (₱) *" />
                            <x-input id="price" type="number" name="price" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('price', optional($trail->package)->price ?? $trail->price) }}" required />
                            <x-input-error for="price" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="package_inclusions" value="Package Inclusions *" />
                            <textarea id="package_inclusions" name="package_inclusions" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Guide, Meals, Environmental Fee, Transportation" required>{{ old('package_inclusions', optional($trail->package)->package_inclusions ?? $trail->package_inclusions) }}</textarea>
                            <x-input-error for="package_inclusions" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="hidden" name="transport_included" value="0" />
                                <input id="transport_included" type="checkbox" name="transport_included" value="1" 
                                       {{ old('transport_included', optional($trail->package)->transport_included ?? $trail->transport_included) ? 'checked' : '' }}
                                       class="h-4 w-4 text-[#336d66] focus:ring-[#336d66] border-gray-300 rounded">
                                <x-label for="transport_included" value="Transportation Included?" class="ml-2" />
                            </div>
                            <p class="mt-2 text-sm text-gray-600">Check if transportation from the meeting point (e.g., pickup/return transfer) is included in the package.</p>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="transportation_details" value="Transport Details" />
                            <div class="flex gap-2 items-center">
                                <input id="transportation_details_visible" type="text"
                                    class="mt-1 flex-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66] {{ old('transport_included', optional($trail->package)->transport_included ?? $trail->transport_included) ? '' : 'hidden' }}"
                                    placeholder="Start typing a pickup location (e.g., Cubao Terminal)" 
                                    value="{{ old('transportation_details_visible', optional($trail->package)->transportation_details ?? $trail->transportation_details ?? '') }}"
                                    autocomplete="off" />

                                <select id="transportation_vehicle_visible" class="mt-1 w-48 border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66] {{ old('transport_included', optional($trail->package)->transport_included ?? $trail->transport_included) ? '' : 'hidden' }}" name="transportation_vehicle">
                                    @php
                                        // Vehicle field doesn't exist in database yet, only use old() for form validation
                                        $selectedVehicle = old('transportation_vehicle', '');
                                        
                                        // TODO: If vehicle info needs to be extracted from transportation_details,
                                        // it would be done here. For now, vehicle dropdown starts empty for existing trails.
                                    @endphp
                                    <option value="" disabled {{ !$selectedVehicle ? 'selected' : '' }}>{{ __('Vehicle (optional)') }}</option>
                                    <option value="van" {{ $selectedVehicle == 'van' ? 'selected' : '' }}>{{ __('Van') }}</option>
                                    <option value="jeep" {{ $selectedVehicle == 'jeep' ? 'selected' : '' }}>{{ __('Jeep') }}</option>
                                    <option value="bus" {{ $selectedVehicle == 'bus' ? 'selected' : '' }}>{{ __('Bus') }}</option>
                                    <option value="car" {{ $selectedVehicle == 'car' ? 'selected' : '' }}>{{ __('Car') }}</option>
                                    <option value="motorbike" {{ $selectedVehicle == 'motorbike' ? 'selected' : '' }}>{{ __('Motorbike') }}</option>
                                </select>
                            </div>

                            <!-- Pickup/Departure Time -->
                            <div id="pickup_time_container" class="mb-4 {{ old('transport_included', optional($trail->package)->transport_included ?? $trail->transport_included) ? '' : 'hidden' }}">
                                <label for="pickup_time" class="block text-sm font-medium text-gray-700">Pickup Time</label>
                                <input type="time" id="pickup_time" name="pickup_time" 
                                       value="{{ old('pickup_time', optional($trail->package)->pickup_time ?? $trail->pickup_time ?? '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                            </div>
                            <div id="departure_time_container" class="mb-4 {{ old('transport_included', optional($trail->package)->transport_included ?? $trail->transport_included) ? 'hidden' : '' }}">
                                <label for="departure_time" class="block text-sm font-medium text-gray-700">Departure Time</label>
                                <input type="time" id="departure_time" name="departure_time" 
                                       value="{{ old('departure_time', optional($trail->package)->departure_time ?? $trail->departure_time ?? '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                            </div>

                            <!-- Commute UI -->
                            <div id="commute_ui" class="mt-3 {{ old('transport_included', $trail->transport_included) ? 'hidden' : '' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Commute Guide (From → To)</label>
                                <div id="commute_legs" class="space-y-2"></div>
                                <div class="flex gap-2 mt-2">
                                    <button type="button" id="add_commute_leg_btn" class="px-3 py-1 bg-[#336d66] text-white text-xs rounded">Add Leg</button>
                                    <button type="button" id="clear_commute_legs_btn" class="px-3 py-1 bg-gray-200 text-xs rounded">Clear</button>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Add one or more legs to show how hikers should commute from a meeting point to the trail (example: "Cubao Terminal → Baguio Bus Terminal"). This is informational only.</p>
                            </div>

                            <!-- Transport details textarea for summary -->
                            <textarea id="transport_details" name="transport_details" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66] hidden" placeholder="e.g., Bus to Tanay, Jeep to Jump-off">{{ old('transport_details', optional($trail->package)->transport_details ?? $trail->transport_details ?? '') }}</textarea>

                            <!-- Always-submitted canonical field -->
                            <input type="hidden" id="transportation_details" name="transportation_details" value="{{ old('transportation_details', optional($trail->package)->transportation_details ?? $trail->transportation_details ?? '') }}">
                            <!-- Hidden canonical vehicle field for pickup mode when transport included -->
                            <input type="hidden" id="transportation_vehicle" name="transportation_vehicle" value="{{ old('transportation_vehicle', optional($trail->package)->transportation_vehicle ?? $trail->transportation_vehicle ?? '') }}">
                            <!-- Optional: store pickup place metadata when transport included (place_id, name, lat/lng) -->
                            <input type="hidden" id="transportation_pickup_place" name="transportation_pickup_place" value="{{ old('transportation_pickup_place', optional($trail->package)->transportation_pickup_place ?? $trail->transportation_pickup_place ?? '') }}">

                            <x-input-error for="transportation_details" class="mt-2" />
                        </div>

                        <!-- Difficulty & Duration -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Difficulty & Duration</h3>
                        </div>

                        <div>
                            <x-label for="difficulty" value="Difficulty Level *" />
                            <select id="difficulty" name="difficulty" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" required>
                                <option value="">Select Difficulty</option>
                                <option value="beginner" {{ old('difficulty', $trail->difficulty) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('difficulty', $trail->difficulty) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('difficulty', $trail->difficulty) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            <x-input-error for="difficulty" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="difficulty_description" value="Difficulty Description" />
                            <textarea id="difficulty_description" name="difficulty_description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Describe what makes this trail beginner/intermediate/advanced">{{ old('difficulty_description', $trail->difficulty_description) }}</textarea>
                            <x-input-error for="difficulty_description" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="duration" value="Duration *" />
                            <x-input id="duration" type="text" name="duration" class="mt-1 block w-full" value="{{ old('duration', optional($trail->package)->duration ?? $trail->duration) }}" placeholder="e.g., 36 hours or 2 days" required />
                            <x-input-error for="duration" class="mt-2" />
                            <p id="duration-summary" class="mt-1 text-sm text-gray-600">Estimated: <span id="duration-summary-text">N/A</span></p>
                        </div>

                        <div>
                            <x-label for="best_season" value="Best Season *" />

                            <div class="mt-1 grid grid-cols-2 gap-2" id="best-season-selects-edit" data-old="@json(old('best_season', $trail->best_season))">
                                <select id="best_season_from" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]">
                                    <option value="">From (month)</option>
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>
                                <select id="best_season_to" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]">
                                    <option value="">To (month)</option>
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>
                            </div>
                            <input type="hidden" id="best_season" name="best_season" value="" />
                            <p id="best-season-help" class="mt-1 text-sm text-gray-600">Select the best season months (From - To)</p>
                            <x-input-error for="best_season" class="mt-2" />
                        </div>

                        <!-- Trail Details -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Trail Details</h3>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="terrain_notes" value="Terrain Notes *" />
                            <textarea id="terrain_notes" name="terrain_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Rocky, River Crossings, Dense Forest" required>{{ old('terrain_notes', $trail->terrain_notes) }}</textarea>
                            <x-input-error for="terrain_notes" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="other_trail_notes" value="Other Trail Notes" />
                            <textarea id="other_trail_notes" name="other_trail_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., limited hikers, hike cut-off time, curfew, trail rules, or safety reminders">{{ old('other_trail_notes', $trail->other_trail_notes) }}</textarea>
                            <x-input-error for="other_trail_notes" class="mt-2" />
                        </div>

                        <!-- Permit Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Permit Information</h3>
                        </div>

                        <div class="flex items-center">
                            <input id="permit_required" type="checkbox" name="permit_required" value="1" class="h-4 w-4 text-[#336d66] focus:ring-[#336d66] border-gray-300 rounded" {{ old('permit_required', $trail->permit_required) ? 'checked' : '' }}>
                            <x-label for="permit_required" value="Permit Required?" class="ml-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="permit_process" value="Permit Process" />
                            <textarea id="permit_process" name="permit_process" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Apply at Municipal Hall / Online LGU Form">{{ old('permit_process', $trail->permit_process) }}</textarea>
                            <x-input-error for="permit_process" class="mt-2" />
                        </div>

                        <!-- Additional Trail Details -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Trail Details</h3>
                        </div>



                        <div class="md:col-span-2">
                            <x-label for="side_trips" value="Side Trips" />


                            <div id="side-trips-list" class="space-y-2 mt-1" data-old="@json(old('side_trips', []))" data-stored="@json(optional($trail->package)->side_trips ?? $trail->side_trips ?? null)">
                                <!-- Template row (hidden) -->
                                <div id="side-trip-row-template" class="hidden">
                                    <div class="flex items-center space-x-2">
                                        <input type="text" name="side_trips[]" class="side-trip-input block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Tinipak River or enter N/A if none">
                                        <button type="button" class="remove-side-trip inline-flex items-center px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">Remove</button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 flex space-x-2">
                                <button type="button" id="add-side-trip" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Add Side Trip
                                </button>
                                <button type="button" id="clear-side-trips" class="inline-flex items-center px-3 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                                    Clear All
                                </button>
                            </div>

                            <x-input-error for="side_trips" class="mt-2" />
                        </div>

                        <!-- Hiker Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Hiker Information</h3>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="packing_list" value="Packing List *" />
                            <textarea id="packing_list" name="packing_list" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Water, Flashlight, Raincoat" required>{{ old('packing_list', $trail->packing_list) }}</textarea>
                            <x-input-error for="packing_list" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="health_fitness" value="Health/Fitness Requirements *" />
                            <textarea id="health_fitness" name="health_fitness" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Moderate fitness recommended, Beginner-friendly" required>{{ old('health_fitness', $trail->health_fitness) }}</textarea>
                            <x-input-error for="health_fitness" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="requirements" value="Other Requirements" />
                            <textarea id="requirements" name="requirements" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Any other specific requirements">{{ old('requirements', $trail->requirements) }}</textarea>
                            <x-input-error for="requirements" class="mt-2" />
                        </div>

                        <!-- Safety & Support -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Safety & Support</h3>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="emergency_contacts" value="Emergency Contacts *" />
                            <textarea id="emergency_contacts" name="emergency_contacts" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Barangay Rescue – 0917xxxxxxx" required>{{ old('emergency_contacts', $trail->emergency_contacts) }}</textarea>
                            <x-input-error for="emergency_contacts" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="campsite_info" value="Campsite Information" />
                            <textarea id="campsite_info" name="campsite_info" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Tent area at summit or No campsite">{{ old('campsite_info', $trail->campsite_info) }}</textarea>
                            <x-input-error for="campsite_info" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="guide_info" value="Guide Information" />
                            <textarea id="guide_info" name="guide_info" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Information about guides">{{ old('guide_info', $trail->guide_info) }}</textarea>
                            <x-input-error for="guide_info" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="environmental_practices" value="Environmental Practices" />
                            <textarea id="environmental_practices" name="environmental_practices" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Leave No Trace">{{ old('environmental_practices', $trail->environmental_practices) }}</textarea>
                            <x-input-error for="environmental_practices" class="mt-2" />
                        </div>

                        <!-- Feedback & Testimonials -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Feedback & Testimonials</h3>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="customers_feedback" value="Customers Feedback" />
                            <textarea id="customers_feedback" name="customers_feedback" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Juan Dela Cruz: Sobrang ganda ng tanawin paakyat, I'm definitely going back here!">{{ old('customers_feedback', $trail->customers_feedback) }}</textarea>
                            <x-input-error for="customers_feedback" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="testimonials_faqs" value="Testimonials / Common FAQs" />
                            <textarea id="testimonials_faqs" name="testimonials_faqs" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Most frequently asked questions from hikers, especially beginners">{{ old('testimonials_faqs', $trail->testimonials_faqs) }}</textarea>
                            <x-input-error for="testimonials_faqs" class="mt-2" />
                        </div>

                        <!-- Activities / Supported Uses -->
                        <div class="md:col-span-2">
                            <x-label value="Supported Activities (select all that apply)" />
                            <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @php
                                    $activityOptions = [
                                        'hiking' => 'Hiking',
                                        'day_hike' => 'Day Hike',
                                        'trail_running' => 'Trail Running',
                                        'camping' => 'Camping',
                                        'overnight_camping' => 'Overnight Camping',
                                        'mountaineering' => 'Mountaineering',
                                        'birding' => 'Birding',
                                        'photography' => 'Photography',
                                        'water_activities' => 'Water Activities (river/shore)'
                                    ];
                                    $selected = old('activities', $trail->activities ?? []);
                                @endphp

                                @foreach($activityOptions as $value => $label)
                                    <label class="inline-flex items-center space-x-2 text-sm">
                                        <input type="checkbox" name="activities[]" value="{{ $value }}" class="h-4 w-4 text-[#336d66] focus:ring-[#336d66] border-gray-300 rounded" {{ in_array($value, (array) $selected) ? 'checked' : '' }}>
                                        <span class="text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-gray-500">These tags help hikers find trails that support specific activities and improve recommendation matching.</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('org.trails.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-4 rounded-lg transition-colors">
                            Update Trail
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Load Google Maps JavaScript API directly -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&v=weekly"></script>

    <script>
        // --- Transport / Commute UI Handling ---
        function toggleTransportUI() {
            const checkbox = document.getElementById('transport_included');
            const pickupVisible = document.getElementById('transportation_details_visible');
            const pickupVehicleVisible = document.getElementById('transportation_vehicle_visible');
            const commuteUI = document.getElementById('commute_ui');
            const transportDetailsTextarea = document.getElementById('transport_details');

            if (!checkbox) return;

            if (checkbox.checked) {
                // When transport included, show pickup input and hide commute composer
                if (pickupVisible) pickupVisible.classList.remove('hidden');
                if (pickupVehicleVisible) pickupVehicleVisible.classList.remove('hidden');
                if (commuteUI) commuteUI.classList.add('hidden');
                // Show a readable notice in the transport_details textarea
                if (transportDetailsTextarea) {
                    transportDetailsTextarea.value = 'Transportation pickup will be arranged by the organization.';
                }
            } else {
                // When not included, hide pickup input and show commute composer
                if (pickupVisible) pickupVisible.classList.add('hidden');
                if (pickupVehicleVisible) pickupVehicleVisible.classList.add('hidden');
                if (commuteUI) commuteUI.classList.remove('hidden');
                // Ensure at least one empty leg exists
                ensureAtLeastOneCommuteLeg();
                // Update preview (commute summary) into transport_details textarea
                if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview();
            }
        }

        function ensureAtLeastOneCommuteLeg() {
            const container = document.getElementById('commute_legs');
            if (!container) return;
            if (container.children.length === 0) {
                addCommuteLeg();
            }
        }

        function createCommuteLegRow(from = '', to = '') {
            const row = document.createElement('div');
            row.className = 'flex gap-2 items-center';

            const fromInput = document.createElement('input');
            fromInput.type = 'text';
            fromInput.placeholder = 'From (e.g., Cubao Terminal)';
            fromInput.className = 'flex-1 text-sm border-gray-300 rounded px-2 py-1';
            fromInput.value = from;
            fromInput.dataset.place = '';

            const arrow = document.createElement('span');
            arrow.className = 'text-xs text-gray-500';
            arrow.textContent = '→';

            const toInput = document.createElement('input');
            toInput.type = 'text';
            toInput.placeholder = 'To (e.g., Baguio Bus Terminal)';
            toInput.className = 'flex-1 text-sm border-gray-300 rounded px-2 py-1';
            toInput.value = to;
            toInput.dataset.place = '';

            // Vehicle select per leg
            const vehicleSelect = document.createElement('select');
            vehicleSelect.className = 'text-xs border-gray-300 rounded px-2 py-1';
            try {
                const pickupSelect = document.getElementById('transportation_vehicle_visible');
                if (pickupSelect && pickupSelect.options && pickupSelect.options.length > 0) {
                    Array.from(pickupSelect.options).forEach(option => {
                        const newOption = new Option(option.textContent, option.value);
                        if (option.disabled) newOption.disabled = true;
                        vehicleSelect.appendChild(newOption);
                    });
                } else {
                    const placeholder = new Option('Vehicle (optional)', '');
                    placeholder.disabled = true;
                    placeholder.selected = true;
                    vehicleSelect.appendChild(placeholder);
                    vehicleSelect.appendChild(new Option('Van', 'van'));
                    vehicleSelect.appendChild(new Option('Jeep', 'jeep'));
                    vehicleSelect.appendChild(new Option('Bus', 'bus'));
                    vehicleSelect.appendChild(new Option('Car', 'car'));
                    vehicleSelect.appendChild(new Option('Motorbike', 'motorbike'));
                }
            } catch(e) {
                const placeholder = new Option('Vehicle (optional)', '');
                placeholder.disabled = true;
                placeholder.selected = true;
                vehicleSelect.appendChild(placeholder);
                vehicleSelect.appendChild(new Option('Van', 'van'));
                vehicleSelect.appendChild(new Option('Jeep', 'jeep'));
                vehicleSelect.appendChild(new Option('Bus', 'bus'));
                vehicleSelect.appendChild(new Option('Car', 'car'));
                vehicleSelect.appendChild(new Option('Motorbike', 'motorbike'));
            }

            // When 'to' changes, try to auto-populate the next leg's 'from'
            toInput.addEventListener('input', () => {
                const nextRow = row.nextElementSibling;
                if (nextRow && toInput.value.trim()) {
                    const nextFromInput = nextRow.querySelector('input[placeholder*="From"]');
                    if (nextFromInput && !nextFromInput.value.trim()) {
                        nextFromInput.value = toInput.value.trim();
                    }
                }
                if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview();
            });

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'text-xs text-red-600 px-2 py-1';
            removeBtn.textContent = 'Remove';
            removeBtn.addEventListener('click', () => {
                row.remove();
                if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview();
            });

            // Update preview when inputs or vehicle change
            fromInput.addEventListener('input', () => { if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview(); });
            toInput.addEventListener('input', () => { if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview(); });
            vehicleSelect.addEventListener('change', () => { if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview(); });

            // Initialize Google Places autocomplete for the new inputs
            setTimeout(() => {
                initializeAutocompleteForInput(fromInput);
                initializeAutocompleteForInput(toInput);
            }, 100);

            row.appendChild(fromInput);
            row.appendChild(arrow);
            row.appendChild(toInput);
            row.appendChild(vehicleSelect);
            row.appendChild(removeBtn);

            return row;
        }

        function addCommuteLeg(from = '', to = '') {
            const container = document.getElementById('commute_legs');
            if (!container) return;
            // If no explicit from supplied, try to use previous leg's to
            if (!from) {
                const rows = container.children;
                if (rows.length > 0) {
                    const lastRow = rows[rows.length - 1];
                    const lastToInput = lastRow.querySelector('input[placeholder*="To"]');
                    if (lastToInput && lastToInput.value.trim()) {
                        from = lastToInput.value.trim();
                    }
                }
            }
            const row = createCommuteLegRow(from, to);
            container.appendChild(row);
            // Focus the 'to' input for immediate editing
            try{
                const toInput = row.querySelector('input[placeholder*="To"]');
                if (toInput) toInput.focus();
            }catch(e){}
            if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview();
        }

        function clearCommuteLegs() {
            const container = document.getElementById('commute_legs');
            if (!container) return;
            container.innerHTML = '';
            if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview();
        }

        // Update the transport_details textarea live when commute legs change
        function updateTransportDetailsPreview() {
            const container = document.getElementById('commute_legs');
            const transportDetailsTextarea = document.getElementById('transport_details');
            if (!container || !transportDetailsTextarea) return;
            const legs = [];
            Array.from(container.children).forEach(row => {
                const fromInput = row.querySelector('input[placeholder*="From"]');
                const toInput = row.querySelector('input[placeholder*="To"]');
                const vehicleSelect = row.querySelector('select');
                const f = fromInput ? fromInput.value.trim() : '';
                const t = toInput ? toInput.value.trim() : '';
                const v = vehicleSelect && vehicleSelect.value ? ` (${vehicleSelect.options[vehicleSelect.selectedIndex].text})` : '';
                if (f && t) legs.push({ from: f, to: t, vehicle: v });
                else if (t) legs.push({ from: '(unknown)', to: t, vehicle: v });
            });

            // Build map of vehicle key -> label from pickup select
            const vehicleLabelMap = {};
            const pickupSelect = document.getElementById('transportation_vehicle_visible');
            if (pickupSelect) {
                Array.from(pickupSelect.options).forEach(option => {
                    if (option.value) vehicleLabelMap[option.value] = option.textContent;
                });
            }

            const summaryParts = [];
            legs.forEach(leg => {
                if (leg.from && leg.to) summaryParts.push(leg.from + ' → ' + leg.to + leg.vehicle);
                else if (leg.to) summaryParts.push('(unknown) → ' + leg.to + leg.vehicle);
            });
            transportDetailsTextarea.value = summaryParts.length ? summaryParts.join('; ') : '';
        }

        function loadMapsScript(cb) {
            if (typeof google !== 'undefined' && google.maps) {
                cb();
                return;
            }
            const key = document.querySelector('meta[name="google-maps-api-key"]');
            if (!key || !key.content) {
                console.error('Google Maps API key not found');
                return;
            }
            const s = document.createElement('script');
            s.src = `https://maps.googleapis.com/maps/api/js?key=${key.content}&libraries=geometry,places&v=weekly`;
            s.async = true;
            s.defer = true;
            s.onload = cb;
            document.head.appendChild(s);
        }

        // Location search functionality using Google Places
        function initializeLocationSearch() {
            console.log('Initializing Google Places location search...');

            const searchInput = document.getElementById('location_search');
            const hiddenInput = document.getElementById('location_id');
            const latInput = document.getElementById('location_latitude');
            const lngInput = document.getElementById('location_longitude');

            if (!searchInput || !hiddenInput) {
                console.error('Location search elements not found');
                return;
            }

            // Check if Google Maps is loaded
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                console.error('Google Maps API not loaded');
                return;
            }

            console.log('Setting up Google Places Autocomplete...');

            try {
                const autocomplete = new google.maps.places.Autocomplete(searchInput, {
                    types: ['geocode'],
                    componentRestrictions: { country: 'PH' },
                    fields: ['place_id', 'formatted_address', 'geometry', 'name']
                });

                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    console.log('Place selected:', place);
                    if (!place || !place.geometry) {
                        console.log('Invalid place selected');
                        return;
                    }
                    handleGooglePlaceSelection(place);
                });

                // Clear selection when user edits
                searchInput.addEventListener('input', () => {
                    if (hiddenInput.value) clearLocationSelection();
                });

                console.log('Google Places Autocomplete initialized successfully');

                // Show existing location checkmark if already selected
                if (hiddenInput.value) {
                    addLocationCheckmark();
                    searchInput.classList.add('border-green-500', 'bg-green-50');
                    searchInput.classList.remove('border-gray-300');
                }

            } catch (e) {
                console.error('Google Places Autocomplete initialization failed:', e);
            }
        }

        function handleGooglePlaceSelection(place) {
            const searchInput = document.getElementById('location_search');
            const hiddenInput = document.getElementById('location_id');
            const latInput = document.getElementById('location_latitude');
            const lngInput = document.getElementById('location_longitude');
            const loadingDiv = document.getElementById('location_loading');

            // Show loading
            loadingDiv.classList.remove('hidden');

            // Prepare data for backend processing
            const locationData = {
                place_id: place.place_id,
                formatted_address: place.formatted_address,
                latitude: place.geometry.location.lat(),
                longitude: place.geometry.location.lng(),
                name: place.name
            };

            console.log('Processing Google Places location:', locationData);

            fetch('/api/locations/google-places', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(locationData)
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.classList.add('hidden');

                if (data.success) {
                    // Update form fields
                    searchInput.value = place.formatted_address || place.name;
                    hiddenInput.value = data.location.id;
                    latInput.value = place.geometry.location.lat();
                    lngInput.value = place.geometry.location.lng();

                    // Visual feedback
                    searchInput.classList.add('border-green-500', 'bg-green-50');
                    searchInput.classList.remove('border-gray-300');
                    addLocationCheckmark();

                    console.log('Location processed successfully:', data.location);
                } else {
                    console.error('Location processing failed:', data.message);
                    alert('Failed to process location: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                loadingDiv.classList.add('hidden');
                console.error('Location processing error:', error);
                alert('Error processing location. Please try again.');
            });
        }

        function addLocationCheckmark() {
            const searchInput = document.getElementById('location_search');
            const existingCheckmark = searchInput.parentNode.querySelector('.location-selected-checkmark');

            if (!existingCheckmark) {
                const checkmark = document.createElement('div');
                checkmark.className = 'location-selected-checkmark absolute right-3 top-1/2 transform -translate-y-1/2 text-green-500';
                checkmark.innerHTML = '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>';
                searchInput.parentNode.appendChild(checkmark);
            }
        }

        function clearLocationSelection() {
            const searchInput = document.getElementById('location_search');
            const hiddenInput = document.getElementById('location_id');
            const latInput = document.getElementById('location_latitude');
            const lngInput = document.getElementById('location_longitude');

            hiddenInput.value = '';
            latInput.value = '';
            lngInput.value = '';
            searchInput.classList.remove('border-green-500', 'bg-green-50');
            searchInput.classList.add('border-gray-300');

            // Remove checkmark
            const checkmark = searchInput.parentNode.querySelector('.location-selected-checkmark');
            if (checkmark) {
                checkmark.remove();
            }
        }

        // Initialize Google Places autocomplete on input elements
        function initializeAutocompleteForInput(input) {
            if (!input || typeof google === 'undefined' || !google.maps || !google.maps.places) {
                return;
            }

            try {
                const autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['geocode'],
                    componentRestrictions: { country: 'PH' },
                    fields: ['place_id', 'formatted_address', 'geometry', 'name']
                });

                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (place && place.formatted_address) {
                        input.value = place.formatted_address;
                    } else if (place && place.name) {
                        input.value = place.name;
                    }
                });

                console.log('Autocomplete initialized for:', input.id || input.placeholder);
            } catch (e) {
                console.error('Failed to initialize autocomplete for input:', e);
            }
        }

        // Initialize autocomplete for all transport-related inputs
        function initializeAllTransportAutocomplete() {
            console.log('Initializing transport autocomplete...');
            
            // Transport Details (pickup location)
            const transportDetailsInput = document.getElementById('transportation_details_visible');
            if (transportDetailsInput) {
                initializeAutocompleteForInput(transportDetailsInput);
            }



            // Initialize autocomplete for existing commute legs
            document.querySelectorAll('#commute_legs input[type="text"]').forEach(input => {
                initializeAutocompleteForInput(input);
            });
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Edit Form');
            
            const checkbox = document.getElementById('transport_included');
            const addBtn = document.getElementById('add_commute_leg_btn');
            const clearBtn = document.getElementById('clear_commute_legs_btn');

            if (checkbox) {
                checkbox.addEventListener('change', toggleTransportUI);
                toggleTransportUI(); // Initialize on page load
            }
            if (addBtn) addBtn.addEventListener('click', () => addCommuteLeg());
            if (clearBtn) clearBtn.addEventListener('click', () => clearCommuteLegs());

            // Initialize existing commute legs if any
            if (!checkbox || !checkbox.checked) {
                // Try to parse existing transport_details into commute legs
                const transportDetails = document.getElementById('transport_details');
                if (transportDetails && transportDetails.value && transportDetails.value.trim()) {
                    const existingDetails = transportDetails.value.trim();
                    console.log('Existing transport details:', existingDetails);
                    
                    // Try to parse "From → To (Vehicle); From2 → To2 (Vehicle2)" format
                    const legs = existingDetails.split(';').map(leg => leg.trim()).filter(leg => leg.length > 0);
                    
                    if (legs.length > 0) {
                        legs.forEach(leg => {
                            const arrowMatch = leg.match(/^(.+?)\s*→\s*(.+?)(?:\s*\((.+?)\))?$/);
                            if (arrowMatch) {
                                const from = arrowMatch[1].trim();
                                const to = arrowMatch[2].trim();
                                const vehicle = arrowMatch[3] ? arrowMatch[3].trim() : '';
                                addCommuteLeg(from, to);
                            }
                        });
                        // Initialize autocomplete for the populated legs after they are added
                        setTimeout(() => {
                            document.querySelectorAll('#commute_legs input[type="text"]').forEach(input => {
                                initializeAutocompleteForInput(input);
                            });
                        }, 200);
                    } else {
                        ensureAtLeastOneCommuteLeg();
                    }
                } else {
                    ensureAtLeastOneCommuteLeg();
                }
            }
        });

        // Initialize Google Maps after everything loads
        window.addEventListener('load', function() {
            console.log('Window loaded - initializing Google Maps');
            setTimeout(() => {
                initializeLocationSearch();
                initializeAllTransportAutocomplete();
            }, 500);
        });
    </script>

    <script>
    // Side Trips per-item inputs (matching create view behavior)
    (function(){
        function createRow(value){
            const template = document.getElementById('side-trip-row-template');
            const row = template.cloneNode(true);
            row.id = '';
            row.classList.remove('hidden');
            const input = row.querySelector('.side-trip-input');
            input.value = value;
            
            // Add Google Places autocomplete to side trip input
            try {
                if (window.google && window.google.maps && window.google.maps.places) {
                    const autocomplete = new google.maps.places.Autocomplete(input, {
                        componentRestrictions: { country: 'ph' },
                        fields: ['place_id', 'name', 'formatted_address', 'geometry'],
                        types: ['establishment', 'geocode']
                    });
                    
                    autocomplete.addListener('place_changed', function() {
                        const place = autocomplete.getPlace();
                        if (place && place.name) {
                            input.value = place.name;
                        }
                    });
                }
            } catch (e) {
                console.log('Google Maps not ready for side trip autocomplete');
            }
            
            const removeBtn = row.querySelector('.remove-side-trip');
            removeBtn.addEventListener('click', function(){
                row.remove();
            });
            return row;
        }

        function addSideTrip(value){
            const list = document.getElementById('side-trips-list');
            const row = createRow(value);
            list.appendChild(row);
        }

        function clearSideTrips(){
            const list = document.getElementById('side-trips-list');
            list.querySelectorAll(':scope > div').forEach(el => el.remove());
        }

        document.addEventListener('DOMContentLoaded', function(){
            const addBtn = document.getElementById('add-side-trip');
            const clearBtn = document.getElementById('clear-side-trips');
            addBtn.addEventListener('click', function(){ addSideTrip(''); });
            clearBtn.addEventListener('click', function(){
                clearSideTrips();
                // ensure at least one empty row remains
                addSideTrip('');
            });

            // initialize from server-side old input or existing DB value
            try{
                // Prefer old input (validation fail); fall back to trail's stored comma-separated string
                let oldValues = [];
                try {
                    const container = document.getElementById('side-trips-list');
                    const rawOld = container.getAttribute('data-old');
                    const storedRaw = container.getAttribute('data-stored');
                    console.log('Side Trips Debug - Edit Form:', { rawOld, storedRaw });
                    
                    // Handle old form data first (from validation errors)
                    if (rawOld && rawOld !== 'null' && rawOld !== '[]') {
                        try {
                            const parsed = JSON.parse(rawOld);
                            if (Array.isArray(parsed) && parsed.length > 0) {
                                oldValues = parsed;
                            }
                        } catch (e) {
                            // Error parsing rawOld, continue
                        }
                    }
                    
                    // If no old form data, try stored database data
                    if (!Array.isArray(oldValues) || oldValues.length === 0) {
                        if (storedRaw && storedRaw !== 'null' && storedRaw !== '' && storedRaw.trim().length > 0) {
                            try {
                                // First try to parse as JSON
                                const parsedStored = JSON.parse(storedRaw);
                                if (Array.isArray(parsedStored)) {
                                    oldValues = parsedStored.filter(v => v && v.trim().length > 0);
                                } else if (typeof parsedStored === 'string' && parsedStored.trim().length > 0) {
                                    // Parse comma-separated string
                                    oldValues = parsedStored.split(',').map(s => s.trim()).filter(s => s.length > 0);
                                }
                            } catch (jsonError) {
                                // Fall back to treating as comma-separated string
                                oldValues = storedRaw.split(',').map(s => s.trim()).filter(s => s.length > 0);
                            }
                        }
                    }
                    console.log('Side Trips Final Values:', oldValues);
                } catch(e){
                    console.error('Side Trips Parse Error:', e);
                    oldValues = [];
                }

                if(Array.isArray(oldValues) && oldValues.length){
                    console.log('Populating side trips:', oldValues);
                    oldValues.forEach(v => addSideTrip(v));
                } else {
                    console.log('No side trips data, adding empty row');
                    // if no values, add a single empty row so users can start typing
                    addSideTrip('');
                }
            }catch(e){
                // fallback: add one empty row
                addSideTrip('');
            }

            // Duration parsing helper (same behavior as create view)
            function parseDurationInput(raw){
                if(!raw || !raw.toString) return null;
                const s = raw.toString().trim().toLowerCase();
                const hourMatch = s.match(/^(\d+(?:\.\d+)?)\s*(h|hr|hrs|hour|hours)?$/i);
                if(hourMatch){
                    const hours = parseFloat(hourMatch[1]);
                    return { hours };
                }
                const explicitHours = s.match(/(\d+(?:\.\d+)?)\s*(hours|hour|hrs|h|hr)/i);
                if(explicitHours){
                    return { hours: parseFloat(explicitHours[1]) };
                }
                const daysMatch = s.match(/(\d+(?:\.\d+)?)\s*(days|day|d)/i);
                if(daysMatch){
                    const days = parseFloat(daysMatch[1]);
                    return { days };
                }
                const nightsMatch = s.match(/(\d+(?:\.\d+)?)\s*(nights|night|n)/i);
                if(nightsMatch){
                    const nights = parseFloat(nightsMatch[1]);
                    return { nights };
                }
                const combined = s.match(/(?:(\d+)\s*d(?:ays?)?)?\s*(?:(\d+)\s*n(?:ights?)?)?/i);
                if(combined && (combined[1] || combined[2])){
                    return { days: combined[1] ? parseInt(combined[1],10) : 0, nights: combined[2] ? parseInt(combined[2],10) : 0 };
                }
                return null;
            }

            function normalizeDuration(value){
                const parsed = parseDurationInput(value);
                if(!parsed) return null;
                if(parsed.hours !== undefined){
                    const hours = parsed.hours;
                    let days = 0;
                    let nights = 0;
                    if(hours >= 24){
                        days = Math.ceil(hours / 24);
                        nights = Math.max(0, days - 1);
                    }
                    return { hours, days, nights };
                }
                if(parsed.days !== undefined){
                    const days = parsed.days;
                    const nights = Math.max(0, Math.floor(days) - 1);
                    return { days, nights, hours: days * 24 };
                }
                if(parsed.nights !== undefined){
                    const nights = parsed.nights;
                    const days = nights + 1;
                    return { nights, days, hours: days * 24 };
                }
                return null;
            }

            function updateDurationSummary(){
                const input = document.getElementById('duration');
                const hidden = document.getElementById('estimated_time');
                const summaryText = document.getElementById('duration-summary-text');
                if(!input || !hidden || !summaryText) return;
                const raw = input.value;
                const norm = normalizeDuration(raw);
                if(!norm){
                    summaryText.textContent = 'N/A';
                    // ensure estimated_time is an integer (0 when unknown)
                    hidden.value = '0';
                    return;
                }
                const parts = [];
                if(norm.days !== undefined && norm.days > 0) parts.push(norm.days + ' day' + (norm.days>1?'s':''));
                if(norm.nights !== undefined && norm.nights > 0) parts.push(norm.nights + ' night' + (norm.nights>1?'s':''));
                if(parts.length === 0 && norm.hours !== undefined) parts.push(norm.hours + ' hour' + (norm.hours>1?'s':''));
                summaryText.textContent = parts.join(', ');
                // Save estimated_time as integer hours (rounded) for backend use
                const hours = typeof norm.hours === 'number' ? Math.round(norm.hours) : (typeof norm.days === 'number' ? Math.round(norm.days * 24) : 0);
                hidden.value = String(hours);
            }

            // wire duration input on edit page
            try{
                const durInput = document.getElementById('duration');
                if(durInput){
                    durInput.addEventListener('input', updateDurationSummary);
                    // initialize
                    updateDurationSummary();
                }
            }catch(e){}

            // Best season syncing for edit form
            try{
                const from = document.getElementById('best_season_from');
                const to = document.getElementById('best_season_to');
                const hidden = document.getElementById('best_season');
                function syncBest(){
                    if(!hidden) return;
                    const vFrom = from ? from.value : '';
                    const vTo = to ? to.value : '';
                    hidden.value = vFrom && vTo ? `${vFrom} to ${vTo}` : (vFrom ? `${vFrom}` : (vTo ? `${vTo}` : ''));
                }
                if(from) from.addEventListener('change', syncBest);
                if(to) to.addEventListener('change', syncBest);

                // initialize from data-old
                const container = document.getElementById('best-season-selects-edit');
                const existing = container ? container.getAttribute('data-old') : '';
                if(existing && existing.indexOf('to') !== -1){
                    const parts = existing.split('to').map(s => s.trim());
                    if(parts[0] && from) from.value = parts[0];
                    if(parts[1] && to) to.value = parts[1];
                } else if(existing){
                    if(from) from.value = existing;
                }
                syncBest();
            }catch(e){}
        });
    })();
    </script>
</x-app-layout>
