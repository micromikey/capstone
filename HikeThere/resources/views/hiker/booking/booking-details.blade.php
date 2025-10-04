<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Create Booking</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">Reserve a spot for a guided hike or campsite. We'll keep your booking details here.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 p-8 max-w-7xl mx-auto transform hover:shadow-2xl transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div class="md:col-span-1">
                        {{-- Display general errors at the top --}}
                        @if($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-red-800 font-semibold mb-2">There were some errors with your booking:</h3>
                                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form id="booking-form" action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    {{-- Prefill event association (optional) --}}
                    @if(!empty($prefill['event_id'] ?? null))
                        <input type="hidden" name="event_id" value="{{ old('event_id', $prefill['event_id']) }}" />
                    @endif

                    @if(!empty($organizations) && $organizations->isNotEmpty())
                        <div>
                            <label class="flex items-center text-sm font-semibold text-gray-800 mb-3">
                                <svg class="w-5 h-5 text-cyan-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Select Organization
                            </label>
                            <div class="relative">
                                <select id="organization_select" name="organization_id" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 appearance-none text-gray-900 font-medium">
                                    <option value="" disabled selected>Select organization</option>
                                    @foreach($organizations as $org)
                                        {{-- controller now returns users.id as organization_id to avoid ambiguous column names --}}
                                        <option value="{{ $org->organization_id }}">{{ $org->organization_name ?? $org->name ?? $org->display_name ?? 'Organization' }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-800 mb-3">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Select Trail
                        </label>
                        <div class="relative">
                            <select id="trail_select" name="trail_id" required class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 appearance-none text-gray-900 font-medium">
                                <option value="" disabled selected>Select trail</option>
                            @foreach($trails as $trail)
                                <option value="{{ $trail->id }}" {{ old('trail_id') == $trail->id ? 'selected' : '' }}>{{ $trail->trail_name }}</option>
                            @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('trail_id')
                            <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm text-red-800 font-medium">{{ $message }}</p>
                                </div>
                            </div>
                        @enderror

                        {{-- preview is shown in the sticky aside; keep DOM IDs available by including markup only once below the aside --}}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="flex items-center text-sm font-semibold text-gray-800 mb-3">
                                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Select Date
                            </label>
                            @php $today = \Carbon\Carbon::now()->toDateString(); @endphp
                            <div class="relative">
                                <input type="date" id="date_input" name="date" min="{{ $today }}" value="{{ old('date') }}" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900 font-medium" />
                            </div>
                            <div id="date_status_indicator" class="mt-2 hidden">
                                <!-- Will be populated dynamically -->
                            </div>
                        </div>
                        <div>
                            <label class="flex items-center text-sm font-semibold text-gray-800 mb-3">
                                <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Party Size
                            </label>
                            <div class="relative">
                                <input type="number" name="party_size" value="1" min="1" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-gray-900 font-medium" />
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                                        <!-- Time slot selection for dated events -->
                    <div id="dated_slots_section" class="mt-4">
                        <label class="flex items-center text-sm font-semibold text-gray-800 mb-3">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Choose Time Slot
                        </label>
                        <div class="relative">
                            <select id="batch_select" name="batch_id" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none text-gray-900 font-medium">
                                <option value="" disabled selected>Select a time slot</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div id="slot_selection_info" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg transition-all duration-300">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p id="batch_help" class="text-sm text-blue-800 leading-relaxed">
                                    Remaining spots will be shown per slot. If a slot is full it will be listed as unavailable.
                                </p>
                            </div>
                        </div>
                        @error('batch_id')
                            <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm text-red-800 font-medium">{{ $message }}</p>
                                </div>
                            </div>
                        @enderror
                    </div>

                    <!-- Always available preview for undated events -->
                    <div id="undated_slots_section" class="mt-4 hidden">
                        <label class="flex items-center text-sm font-semibold text-gray-800 mb-3">
                            <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Always Available Booking
                        </label>
                        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 border border-emerald-200 rounded-xl shadow-sm">
                            <!-- Background decoration -->
                            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-emerald-200/30 to-teal-200/30 rounded-full blur-2xl"></div>
                            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-16 h-16 bg-gradient-to-tr from-green-200/30 to-emerald-200/30 rounded-full blur-xl"></div>
                            
                            <div class="relative p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <div class="w-3 h-3 bg-emerald-400 rounded-full mr-2 animate-pulse"></div>
                                            <h4 class="text-lg font-semibold text-emerald-900" id="undated_event_title">Always Available</h4>
                                        </div>
                                        <div class="flex items-center text-emerald-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-sm font-medium">
                                                Selected: <span id="undated_selected_date" class="text-emerald-800 font-semibold">-</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right ml-4">
                                        <div class="bg-white/70 backdrop-blur-sm rounded-lg px-3 py-2 border border-emerald-200/50">
                                            <div class="text-2xl font-bold text-emerald-800" id="undated_remaining_spots">- spots</div>
                                            <div class="text-xs text-emerald-600 font-medium uppercase tracking-wide">Available</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white/50 backdrop-blur-sm rounded-lg p-4 border border-emerald-100">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-emerald-800 font-medium leading-relaxed">
                                                This trail is available every day with limited capacity. Book your adventure for any date that works for you!
                                            </p>
                                            <div class="mt-2 flex items-center text-xs text-emerald-600">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Instant confirmation
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="undated_event_id" name="event_id" value="" />
                        @error('event_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-800 mb-3">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Additional Notes
                        </label>
                        <div class="relative">
                            <textarea name="notes" rows="4" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-gray-900 resize-none" placeholder="Any special requests, dietary restrictions, or accessibility needs..."></textarea>
                            <div class="absolute top-3 right-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Removed Payment Section - Now handled after booking creation -->

                    <div class="flex items-center justify-start pt-6 border-t border-gray-200">
                        <a href="{{ route('booking.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

                    <aside class="md:col-span-1 self-start sticky top-6">
                        <div class="bg-gradient-to-br from-gray-50 via-slate-50 to-blue-50 border border-gray-200 rounded-xl shadow-sm p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900">Booking Preview</h4>
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">Select a trail to see package details and available booking options.</p>
                            <div class="mt-3">
                                @include('partials.trail-package-preview')
                            </div>
                            <div id="slot_selection_info" class="mt-6 p-4 bg-white/60 backdrop-blur-sm border border-gray-200 rounded-lg">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700 flex items-center">
                                            <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Selected slot:
                                        </span>
                                        <span id="selected_slot_label" class="text-sm font-semibold text-gray-900">—</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700 flex items-center">
                                            <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Hiking start time:
                                        </span>
                                        <span id="selected_slot_start_time" class="text-sm font-semibold text-gray-900">—</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700 flex items-center">
                                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            Remaining spots:
                                        </span>
                                        <span id="selected_slot_remaining" class="inline-flex items-center px-2 py-1 text-xs font-bold text-green-800 bg-green-100 rounded-full">—</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Create Booking Button -->
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <button type="submit" form="booking-form" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 text-white font-semibold rounded-xl shadow-lg hover:from-emerald-700 hover:via-emerald-800 hover:to-teal-800 transform hover:scale-105 transition-all duration-200 focus:ring-4 focus:ring-emerald-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Create Booking
                                </button>
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
        document.addEventListener('DOMContentLoaded', function() {
            const defaultTrailImage = '{{ asset("img/default-trail.jpg") }}';
            const orgSelect = document.getElementById('organization_select');
            const trailSelect = document.getElementById('trail_select');
            const dateInput = document.querySelector('input[name="date"]');
            const batchSelect = document.getElementById('batch_select');

            // Initially show dated section, hide undated section
            document.getElementById('dated_slots_section').classList.remove('hidden');
            document.getElementById('undated_slots_section').classList.add('hidden');
            
            // Initially hide slot selection info until trail is selected
            const slotInfoEl = document.getElementById('slot_selection_info');
            if (slotInfoEl) slotInfoEl.classList.add('hidden');

            // Set up organization change handler
            if (orgSelect) {
                orgSelect.addEventListener('change', async function(){
                    const orgId = this.value;
                    console.log('Organization changed:', orgId);
                    
                    // clear previous trails; insert a disabled placeholder so users must pick a trail
                    trailSelect.innerHTML = '<option value="" disabled selected>Select trail</option>';

                    if (!orgId) return;

                    try {
                        const res = await fetch(`{{ url('/') }}/hiker/api/organization/${orgId}/trails`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });

                        if (!res.ok) {
                            console.error('Failed to fetch trails');
                            return;
                        }

                        const trails = await res.json();
                        console.log('Trails fetched:', trails.length);
                        
                        // Populate trail select with fetched trails
                        trails.forEach(trail => {
                            const option = document.createElement('option');
                            option.value = trail.id;
                            option.textContent = trail.trail_name;
                            trailSelect.appendChild(option);
                        });

                        // Trigger prefill trail selection if needed
                        const pre = window.BOOKING_PREFILL || null;
                        if (pre && pre.trail_id) {
                            console.log('Looking for trail:', pre.trail_id);
                            const opt = Array.from(trailSelect.options).find(o => o.value == pre.trail_id);
                            if (opt) {
                                console.log('Trail found, selecting:', opt.textContent);
                                trailSelect.value = opt.value;
                                trailSelect.dispatchEvent(new Event('change'));
                            } else {
                                console.warn('Trail not found in options:', pre.trail_id);
                            }
                        }

                    } catch (error) {
                        console.error('Error fetching trails:', error);
                    }
                });
            }

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
                        batchSelect.innerHTML = '<option value="" disabled selected>Select trail first</option>';
                        // Hide slot selection info when no trail selected
                        const slotInfoEl = document.getElementById('slot_selection_info');
                        if (slotInfoEl) slotInfoEl.classList.add('hidden');
                        return;
                    }

                    batchSelect.innerHTML = '<option value="" disabled>Loading slots...</option>';

                    try {
                        const url = new URL(`{{ url('/') }}/hiker/api/trail/${trailId}/batches`);
                        if (date) url.searchParams.set('date', date);

                        const res = await fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });

                        if (!res.ok) {
                            batchSelect.innerHTML = '<option value="" disabled selected>Error loading slots</option>';
                            // Hide slot selection info on error
                            const slotInfoEl = document.getElementById('slot_selection_info');
                            if (slotInfoEl) slotInfoEl.classList.add('hidden');
                            return;
                        }

                        const slots = await res.json();
                        const undatedEventsCount = parseInt(res.headers.get('X-Undated-Events-Count') || '0');
                        const datedEventsCount = parseInt(res.headers.get('X-Dated-Events-Count') || '0');

                        // Separate undated and dated slots
                        const undatedSlots = slots.filter(s => s.is_always_available === true);
                        const datedSlots = slots.filter(s => s.is_always_available !== true);

                        // Handle undated events (show as preview, not dropdown)
                        if (undatedSlots.length > 0) {
                            const undatedSlot = undatedSlots[0]; // Take the first undated slot
                            
                            // Show undated section, hide dated section
                            document.getElementById('undated_slots_section').classList.remove('hidden');
                            document.getElementById('dated_slots_section').classList.add('hidden');
                            
                            // Hide slot selection info for undated events
                            const slotInfoEl = document.getElementById('slot_selection_info');
                            if (slotInfoEl) slotInfoEl.classList.add('hidden');
                            
                            // Update undated preview
                            document.getElementById('undated_event_title').textContent = undatedSlot.event_title || 'Always Available';
                            document.getElementById('undated_selected_date').textContent = date ? new Date(date).toLocaleDateString() : 'Please select date';
                            document.getElementById('undated_remaining_spots').textContent = undatedSlot.remaining + ' spots';
                            document.getElementById('undated_event_id').value = undatedSlot.event_id || '';
                            
                            // Clear batch selection since we're using event booking
                            batchSelect.innerHTML = '<option value="" disabled selected>Not applicable for always available</option>';
                        } else {
                            // Show dated section, hide undated section
                            document.getElementById('dated_slots_section').classList.remove('hidden');
                            document.getElementById('undated_slots_section').classList.add('hidden');
                            
                            // Show slot selection info for dated events
                            const slotInfoEl = document.getElementById('slot_selection_info');
                            if (slotInfoEl) slotInfoEl.classList.remove('hidden');
                            
                            // Clear undated event id
                            document.getElementById('undated_event_id').value = '';
                            
                            // Populate batch dropdown with dated slots
                            batchSelect.innerHTML = '<option value="" disabled selected>Select time slot</option>';
                            
                            // Check if all slots are full
                            const hasAvailableSlots = datedSlots.some(slot => (slot.remaining ?? 0) > 0);
                            const dateStatusIndicator = document.getElementById('date_status_indicator');
                            
                            if (!hasAvailableSlots && datedSlots.length > 0) {
                                // All slots are fully booked for this date
                                batchSelect.innerHTML = '<option value="" disabled selected>⛔ All slots fully booked for this date</option>';
                                batchSelect.disabled = true;
                                batchSelect.classList.add('bg-red-50', 'border-red-300', 'text-red-700');
                                
                                // Show date unavailable indicator
                                if (dateStatusIndicator) {
                                    dateStatusIndicator.classList.remove('hidden');
                                    dateStatusIndicator.innerHTML = `
                                        <div class="flex items-center p-3 bg-red-50 border border-red-300 rounded-lg">
                                            <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-semibold text-red-800">Fully Booked (0 spots remaining)</span>
                                        </div>
                                    `;
                                }
                                
                                // Show unavailable notice
                                const slotInfoEl = document.getElementById('slot_selection_info');
                                if (slotInfoEl) {
                                    slotInfoEl.classList.remove('hidden', 'bg-blue-50', 'border-blue-200');
                                    slotInfoEl.classList.add('bg-red-50', 'border-red-300');
                                    const helpText = slotInfoEl.querySelector('p');
                                    if (helpText) {
                                        helpText.innerHTML = `
                                            <span class="text-red-800 font-semibold">⛔ This date is fully booked!</span><br/>
                                            <span class="text-red-700">All slots have reached maximum capacity (0 spots remaining). Please select a different date to see available options.</span>
                                        `;
                                    }
                                }
                            } else {
                                // Reset styles
                                batchSelect.disabled = false;
                                batchSelect.classList.remove('bg-red-50', 'border-red-300', 'text-red-700');
                                
                                // Show date available indicator
                                if (dateStatusIndicator && hasAvailableSlots) {
                                    dateStatusIndicator.classList.remove('hidden');
                                    const totalAvailable = datedSlots.reduce((sum, slot) => sum + (slot.remaining ?? 0), 0);
                                    dateStatusIndicator.innerHTML = `
                                        <div class="flex items-center p-3 bg-green-50 border border-green-300 rounded-lg">
                                            <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-semibold text-green-800">Available (${totalAvailable} spots remaining)</span>
                                        </div>
                                    `;
                                } else if (dateStatusIndicator) {
                                    dateStatusIndicator.classList.add('hidden');
                                }
                                
                                // Re-enable the info box
                                const slotInfoEl = document.getElementById('slot_selection_info');
                                if (slotInfoEl) {
                                    slotInfoEl.classList.remove('bg-red-50', 'border-red-300');
                                    slotInfoEl.classList.add('bg-blue-50', 'border-blue-200');
                                    const helpText = slotInfoEl.querySelector('p');
                                    if (helpText) {
                                        helpText.innerHTML = 'Remaining spots will be shown per slot. If a slot is full it will be listed as unavailable.';
                                    }
                                }
                                
                                // Add available slots to dropdown
                                datedSlots.forEach(slot => {
                                    const remaining = slot.remaining ?? 0;
                                    
                                    if (remaining > 0) {
                                        // Available slot - show normally
                                        const opt = document.createElement('option');
                                        opt.value = slot.type === 'batch' ? slot.id : '';
                                        opt.textContent = slot.slot_label || `${slot.name} (${remaining} spots left)`;
                                        opt.dataset.remaining = remaining;
                                        opt.dataset.startTime = slot.starts_at_formatted || '—';
                                        
                                        if (slot.type === 'event') {
                                            opt.dataset.eventId = slot.event_id;
                                        }
                                        
                                        batchSelect.appendChild(opt);
                                    }
                                });
                                
                                // If no available slots were added, show message
                                if (batchSelect.options.length === 1) {
                                    batchSelect.innerHTML += '<option value="" disabled>No available slots for this date</option>';
                                }
                            }
                        }
                        
                        batchSelect.dispatchEvent(new Event('change'));
                    } catch (err) {
                        console.error('Error fetching batches:', err);
                        batchSelect.innerHTML = '<option value="" disabled selected>Error loading slots</option>';
                        // Hide slot selection info on error
                        const slotInfoEl = document.getElementById('slot_selection_info');
                        if (slotInfoEl) slotInfoEl.classList.add('hidden');
                    }
                }
                                // when user picks a slot, update the preview panel with remaining info
                batchSelect?.addEventListener('change', function(){
                    const sel = batchSelect.selectedOptions[0];
                    const labelEl = document.getElementById('selected_slot_label');
                    const remEl = document.getElementById('selected_slot_remaining');
                    const startTimeEl = document.getElementById('selected_slot_start_time');
                    if (!sel) {
                        if (labelEl) labelEl.textContent = '—';
                        if (remEl) remEl.textContent = '—';
                        if (startTimeEl) startTimeEl.textContent = '—';
                        return;
                    }
                    
                    // prefer option text for label
                    if (labelEl) labelEl.textContent = sel.textContent || '—';
                    if (remEl) remEl.textContent = (sel.dataset.remaining !== undefined) ? sel.dataset.remaining : '—';
                    if (startTimeEl) startTimeEl.textContent = sel.dataset.startTime || '—';
                    
                    // If this is an event slot, set the event_id in hidden field
                    if (sel.dataset.eventId) {
                        const eventIdField = document.querySelector('input[name="event_id"]');
                        if (eventIdField) {
                            eventIdField.value = sel.dataset.eventId;
                        }
                    }
                });

            // Apply prefill if provided
            const pre = window.BOOKING_PREFILL || null;
            if (pre) {
                console.log('Applying prefill:', pre);
                
                // If organization_id provided, select it and trigger change to load trails
                if (pre.organization_id && orgSelect) {
                    const opt = Array.from(orgSelect.options).find(o => o.value == pre.organization_id);
                    if (opt) {
                        console.log('Setting organization to:', pre.organization_id);
                        orgSelect.value = opt.value;
                        // Trigger change - this will also handle trail selection
                        orgSelect.dispatchEvent(new Event('change'));
                    }
                }

                // Set date if provided
                if (pre.date && dateInput) {
                    setTimeout(() => {
                        dateInput.value = pre.date;
                        dateInput.dispatchEvent(new Event('change'));
                    }, 500);
                }
            }
        });
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

                // Form validation and AJAX submission
                (function() {
                    const form = document.getElementById('booking-form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault(); // Always prevent default to handle via AJAX
                            
                            const trailId = document.getElementById('trail_select')?.value;
                            const partySize = document.querySelector('input[name="party_size"]')?.value;
                            const date = document.querySelector('input[name="date"]')?.value;
                            const batchId = document.getElementById('batch_select')?.value;
                            const eventId = document.querySelector('input[name="event_id"]')?.value;
                            
                            console.log('Form submission data:', {
                                trail_id: trailId,
                                party_size: partySize,
                                date: date,
                                batch_id: batchId,
                                event_id: eventId
                            });
                            
                            if (!trailId) {
                                alert('Please select a trail before creating a booking.');
                                return false;
                            }

                            // Show loading state
                            const submitBtn = document.querySelector('button[type="submit"][form="booking-form"]');
                            if (!submitBtn) {
                                console.error('Submit button not found');
                                return false;
                            }
                            const originalText = submitBtn.innerHTML;
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline-block" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Creating...';

                            // Prepare form data
                            const formData = new FormData(form);

                            // Submit via AJAX
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show success message
                                    const successDiv = document.createElement('div');
                                    successDiv.className = 'fixed top-4 right-4 z-50 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-lg';
                                    successDiv.innerHTML = `
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-green-800 font-semibold">${data.message}</p>
                                            </div>
                                        </div>
                                    `;
                                    document.body.appendChild(successDiv);
                                    
                                    // Redirect after short delay
                                    setTimeout(() => {
                                        window.location.href = data.redirect_url;
                                    }, 1500);
                                } else {
                                    throw new Error(data.message || 'Booking creation failed');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                
                                // Show error message
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'fixed top-4 right-4 z-50 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-lg';
                                errorDiv.innerHTML = `
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-red-800 font-semibold">${error.message || 'Unable to create booking. Please try again.'}</p>
                                        </div>
                                    </div>
                                `;
                                document.body.appendChild(errorDiv);
                                
                                // Reset button
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                                
                                // Auto-remove error after 5 seconds
                                setTimeout(() => errorDiv.remove(), 5000);
                            });
                        });
                    }
                })();
            </script>
</x-app-layout>
