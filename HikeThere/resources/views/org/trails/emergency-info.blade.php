<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb currentPage="Emergency Information" />
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-3">
                        <div class="bg-red-500 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        {{ __('Emergency Information') }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $trail->trail_name }}</p>
                </div>
                <a href="{{ route('org.trails.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Trails
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Info Banner -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h3 class="text-blue-800 font-semibold">About Emergency Information</h3>
                        <p class="text-blue-700 text-sm mt-1">
                            Provide accurate emergency contact information for hikers. This data will be displayed in their generated itineraries. 
                            If you don't add custom information, the system will automatically fetch data from Google Places API based on the trail location.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('org.trails.emergency-info.update', $trail) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Emergency Numbers Section -->
                <div class="bg-white shadow-lg rounded-2xl p-8 border-2 border-red-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-500 p-3 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Emergency Numbers</h3>
                        </div>
                        <button type="button" onclick="addEmergencyNumber()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Number
                        </button>
                    </div>

                    <div id="emergency-numbers-container" class="space-y-4">
                        @php
                            $emergencyNumbers = old('emergency_numbers', $trail->emergency_info['emergency_numbers'] ?? [
                                ['service' => 'National Emergency Hotline', 'number' => '911'],
                                ['service' => 'Philippine Red Cross', 'number' => '143'],
                            ]);
                        @endphp
                        
                        @forelse($emergencyNumbers as $index => $number)
                            <div class="emergency-number-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-start gap-4">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Name</label>
                                            <input type="text" name="emergency_numbers[{{ $index }}][service]" 
                                                   value="{{ $number['service'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                                   placeholder="e.g., Local Police" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                            <input type="text" name="emergency_numbers[{{ $index }}][number]" 
                                                   value="{{ $number['number'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                                   placeholder="e.g., 0917-XXX-XXXX" required>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeRow(this)" class="mt-7 text-red-600 hover:text-red-800 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No emergency numbers added. Click "Add Number" to start.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Hospitals Section -->
                <div class="bg-white shadow-lg rounded-2xl p-8 border-2 border-green-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-500 p-3 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Nearest Hospitals</h3>
                        </div>
                        <button type="button" onclick="addHospital()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Hospital
                        </button>
                    </div>

                    <div id="hospitals-container" class="space-y-4">
                        @php
                            $hospitals = old('hospitals', $trail->emergency_info['hospitals'] ?? []);
                        @endphp
                        
                        @forelse($hospitals as $index => $hospital)
                            <div class="hospital-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-start gap-4">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Hospital Name</label>
                                            <input type="text" name="hospitals[{{ $index }}][name]" 
                                                   value="{{ $hospital['name'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                   placeholder="e.g., Baguio General Hospital" required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                            <input type="text" name="hospitals[{{ $index }}][address]" 
                                                   value="{{ $hospital['address'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                   placeholder="e.g., La Trinidad, Benguet" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone (Optional)</label>
                                            <input type="text" name="hospitals[{{ $index }}][phone]" 
                                                   value="{{ $hospital['phone'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                   placeholder="e.g., (074) 424-1234">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Distance (Optional)</label>
                                            <input type="text" name="hospitals[{{ $index }}][distance]" 
                                                   value="{{ $hospital['distance'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                   placeholder="e.g., 15 km">
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeRow(this)" class="mt-7 text-red-600 hover:text-red-800 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No hospitals added. System will auto-fetch from Google Places API.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Ranger Stations Section -->
                <div class="bg-white shadow-lg rounded-2xl p-8 border-2 border-blue-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-500 p-3 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Ranger Stations</h3>
                        </div>
                        <button type="button" onclick="addRangerStation()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Station
                        </button>
                    </div>

                    <div id="ranger-stations-container" class="space-y-4">
                        @php
                            $rangerStations = old('ranger_stations', $trail->emergency_info['ranger_stations'] ?? []);
                        @endphp
                        
                        @forelse($rangerStations as $index => $station)
                            <div class="ranger-station-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-start gap-4">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Station Name</label>
                                            <input type="text" name="ranger_stations[{{ $index }}][name]" 
                                                   value="{{ $station['name'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="e.g., Mt. Pulag Ranger Station" required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Address/Location</label>
                                            <input type="text" name="ranger_stations[{{ $index }}][address]" 
                                                   value="{{ $station['address'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="e.g., Babadak, Kabayan, Benguet" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone (Optional)</label>
                                            <input type="text" name="ranger_stations[{{ $index }}][phone]" 
                                                   value="{{ $station['phone'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="e.g., 0917-XXX-XXXX">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person (Optional)</label>
                                            <input type="text" name="ranger_stations[{{ $index }}][contact_person]" 
                                                   value="{{ $station['contact_person'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="e.g., Juan Dela Cruz">
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeRow(this)" class="mt-7 text-red-600 hover:text-red-800 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No ranger stations added. System will auto-fetch from Google Places API.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Evacuation Points Section -->
                <div class="bg-white shadow-lg rounded-2xl p-8 border-2 border-yellow-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="bg-yellow-500 p-3 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Evacuation Points</h3>
                        </div>
                        <button type="button" onclick="addEvacuationPoint()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Point
                        </button>
                    </div>

                    <!-- Interactive Map for Pinning Evacuation Points -->
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="text-blue-800 font-semibold text-sm">How to add evacuation points:</h4>
                                <p class="text-blue-700 text-sm mt-1">Click on the map below to pin an evacuation point. Each click will add a new marker and automatically fill in the coordinates.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div id="evacuation-map" class="w-full h-[500px] rounded-xl border-2 border-gray-300 shadow-lg"></div>
                    </div>

                    <div id="evacuation-points-container" class="space-y-4">
                        @php
                            $evacuationPoints = old('evacuation_points', $trail->emergency_info['evacuation_points'] ?? []);
                        @endphp
                        
                        @forelse($evacuationPoints as $index => $point)
                            <div class="evacuation-point-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-start gap-4">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Point Name</label>
                                            <input type="text" name="evacuation_points[{{ $index }}][name]" 
                                                   value="{{ $point['name'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                   placeholder="e.g., Trailhead Base Camp" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Coordinates (Optional)</label>
                                            <input type="text" name="evacuation_points[{{ $index }}][coordinates]" 
                                                   value="{{ $point['coordinates'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                   placeholder="e.g., 16.5969° N, 120.8844° E">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                            <textarea name="evacuation_points[{{ $index }}][description]" rows="2"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                   placeholder="e.g., Primary evacuation point with helipad access" required>{{ $point['description'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeRow(this)" class="mt-7 text-red-600 hover:text-red-800 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No evacuation points added. System will auto-generate based on trail data.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Off-Limits Areas Section -->
                <div class="bg-white shadow-lg rounded-2xl p-8 border-2 border-red-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-500 p-3 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Off-Limits Areas</h3>
                        </div>
                        <button type="button" onclick="addOffLimitsArea()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Area
                        </button>
                    </div>

                    <!-- Interactive Map for Off-Limits Areas -->
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <h4 class="text-red-800 font-semibold text-sm">Mark dangerous or restricted areas:</h4>
                                <p class="text-red-700 text-sm mt-1">Click on the map to mark areas that hikers should avoid (landslide zones, restricted areas, dangerous cliffs, etc.).</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div id="offlimits-map" class="w-full h-[500px] rounded-xl border-2 border-gray-300 shadow-lg"></div>
                    </div>

                    <div id="offlimits-areas-container" class="space-y-4">
                        @php
                            $offLimitsAreas = old('off_limits_areas', $trail->emergency_info['off_limits_areas'] ?? []);
                        @endphp
                        
                        @forelse($offLimitsAreas as $index => $area)
                            <div class="offlimits-area-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-start gap-4">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Area Name</label>
                                            <input type="text" name="off_limits_areas[{{ $index }}][name]" 
                                                   value="{{ $area['name'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                                   placeholder="e.g., Landslide Zone A" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Coordinates <span class="text-red-600">(Click map to pin)</span></label>
                                            <input type="text" name="off_limits_areas[{{ $index }}][coordinates]" 
                                                   value="{{ $area['coordinates'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                                   placeholder="Click map or enter manually">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason / Warning</label>
                                            <textarea name="off_limits_areas[{{ $index }}][reason]" rows="2"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                                   placeholder="e.g., Active landslide area - extremely dangerous, do not enter" required>{{ $area['reason'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeOffLimitsRow(this)" class="mt-7 text-red-600 hover:text-red-800 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No off-limits areas marked yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center bg-white shadow-lg rounded-2xl p-6 border-2 border-gray-200">
                    <button type="button" onclick="document.getElementById('delete-form').submit();" class="text-gray-600 hover:text-gray-800 flex items-center gap-2 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear All Data
                    </button>

                    <div class="flex gap-3">
                        <a href="{{ route('org.trails.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-lg font-semibold shadow-lg transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Emergency Information
                        </button>
                    </div>
                </div>
            </form>

            <!-- Separate Delete Form (outside main form) -->
            <form id="delete-form" action="{{ route('org.trails.emergency-info.destroy', $trail) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to clear all custom emergency information? The system will revert to auto-generated data.');" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
    <script>
        let emergencyNumberIndex = {{ count($emergencyNumbers) }};
        let hospitalIndex = {{ count($hospitals) }};
        let rangerStationIndex = {{ count($rangerStations) }};
        let evacuationPointIndex = {{ count($evacuationPoints) }};
        let offLimitsAreaIndex = {{ count($offLimitsAreas ?? []) }};
        
        // Google Maps variables for evacuation points
        let evacuationMap;
        let evacuationMarkers = [];
        let evacuationClickListener;

        // Google Maps variables for off-limits areas
        let offLimitsMap;
        let offLimitsMarkers = [];
        let offLimitsClickListener;

        // Initialize maps when page loads
        window.addEventListener('load', function() {
            initEvacuationMap();
            loadExistingEvacuationPoints();
            
            initOffLimitsMap();
            loadExistingOffLimitsAreas();
        });

        function initEvacuationMap() {
            const trailLocation = {
                lat: {{ $trail->latitude ?? 16.4023 }},
                lng: {{ $trail->longitude ?? 120.5960 }}
            };

            evacuationMap = new google.maps.Map(document.getElementById('evacuation-map'), {
                center: trailLocation,
                zoom: 13,
                mapTypeId: 'terrain',
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: true
            });

            // Add click listener to map
            evacuationClickListener = evacuationMap.addListener('click', function(event) {
                addEvacuationPointFromMap(event.latLng);
            });

            // Add a marker for the trail location (blue)
            new google.maps.Marker({
                position: trailLocation,
                map: evacuationMap,
                title: 'Trail Location',
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 8,
                    fillColor: '#3B82F6',
                    fillOpacity: 1,
                    strokeColor: '#1E40AF',
                    strokeWeight: 2
                }
            });
        }

        function initOffLimitsMap() {
            const trailLocation = {
                lat: {{ $trail->latitude ?? 16.4023 }},
                lng: {{ $trail->longitude ?? 120.5960 }}
            };

            offLimitsMap = new google.maps.Map(document.getElementById('offlimits-map'), {
                center: trailLocation,
                zoom: 13,
                mapTypeId: 'terrain',
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: true
            });

            // Add click listener to map
            offLimitsClickListener = offLimitsMap.addListener('click', function(event) {
                addOffLimitsAreaFromMap(event.latLng);
            });

            // Add a marker for the trail location (blue)
            new google.maps.Marker({
                position: trailLocation,
                map: offLimitsMap,
                title: 'Trail Location',
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                }
            });
        }

        function addEvacuationPointFromMap(latLng) {
            const lat = latLng.lat();
            const lng = latLng.lng();
            const coordinates = lat.toFixed(6) + ', ' + lng.toFixed(6);
            const markerIndex = evacuationMarkers.length;

            // Add marker to map with YELLOW flag SVG
            const marker = new google.maps.Marker({
                position: latLng,
                map: evacuationMap,
                title: 'Evacuation Point ' + (markerIndex + 1),
                draggable: true,
                animation: google.maps.Animation.DROP,
                icon: {
                    path: 'M 0,-40 L 0,0 M 0,-40 L 20,-32 L 0,-24 Z',
                    fillColor: '#EAB308',
                    fillOpacity: 1,
                    strokeColor: '#CA8A04',
                    strokeWeight: 2.5,
                    scale: 1.2,
                    anchor: new google.maps.Point(0, 0)
                }
            });

            // Store marker index as a property
            marker.markerIndex = markerIndex;

            // Add info window with coordinates
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 8px;">
                        <p style="font-weight: bold; color: #CA8A04; margin-bottom: 4px;">Evacuation Point ${markerIndex + 1}</p>
                        <p style="font-size: 12px; color: #6B7280;">Coordinates: ${coordinates}</p>
                        <p style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Drag flag to adjust position</p>
                    </div>
                `
            });

            marker.addListener('click', function() {
                infoWindow.open(evacuationMap, marker);
            });

            marker.addListener('dragend', function(event) {
                const newLat = event.latLng.lat();
                const newLng = event.latLng.lng();
                const newCoordinates = newLat.toFixed(6) + ', ' + newLng.toFixed(6);
                const index = marker.markerIndex;
                const input = document.querySelector(`input[name="evacuation_points[${index}][coordinates]"]`);
                if (input) {
                    input.value = newCoordinates;
                }
                
                infoWindow.setContent(`
                    <div style="padding: 8px;">
                        <p style="font-weight: bold; color: #CA8A04; margin-bottom: 4px;">Evacuation Point ${index + 1}</p>
                        <p style="font-size: 12px; color: #6B7280;">Coordinates: ${newCoordinates}</p>
                        <p style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Drag flag to adjust position</p>
                    </div>
                `);
            });

            evacuationMarkers.push(marker);
            addEvacuationPoint(coordinates);
        }

        function loadExistingEvacuationPoints() {
            const existingPoints = @json($evacuationPoints);
            
            existingPoints.forEach((point, index) => {
                if (point.coordinates) {
                    const coords = point.coordinates.split(',');
                    if (coords.length === 2) {
                        const lat = parseFloat(coords[0].trim());
                        const lng = parseFloat(coords[1].trim());
                        
                        if (!isNaN(lat) && !isNaN(lng)) {
                            const marker = new google.maps.Marker({
                                position: { lat: lat, lng: lng },
                                map: evacuationMap,
                                title: point.name || 'Evacuation Point ' + (index + 1),
                                draggable: true,
                                animation: google.maps.Animation.DROP,
                                icon: {
                                    path: 'M 0,-40 L 0,0 M 0,-40 L 20,-32 L 0,-24 Z',
                                    fillColor: '#EAB308',
                                    fillOpacity: 1,
                                    strokeColor: '#CA8A04',
                                    strokeWeight: 2.5,
                                    scale: 1.2,
                                    anchor: new google.maps.Point(0, 0)
                                }
                            });

                            // Store marker index as a property

                            marker.markerIndex = index;

                            const infoWindow = new google.maps.InfoWindow({
                                content: `
                                    <div style="padding: 8px;">
                                        <p style="font-weight: bold; color: #CA8A04; margin-bottom: 4px;">${point.name || 'Evacuation Point ' + (index + 1)}</p>
                                        ${point.description ? `<p style="font-size: 12px; color: #4B5563; margin-bottom: 4px;">${point.description}</p>` : ''}
                                        <p style="font-size: 11px; color: #6B7280;">Coordinates: ${point.coordinates}</p>
                                        <p style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Drag flag to adjust position</p>
                                    </div>
                                `
                            });

                            marker.addListener('click', function() {
                                infoWindow.open(evacuationMap, marker);
                            });

                            marker.addListener('dragend', function(event) {
                                const newLat = event.latLng.lat();
                                const newLng = event.latLng.lng();
                                const newCoordinates = newLat.toFixed(6) + ', ' + newLng.toFixed(6);
                                const input = document.querySelector(`input[name="evacuation_points[${index}][coordinates]"]`);
                                if (input) {
                                    input.value = newCoordinates;
                                }
                                
                                infoWindow.setContent(`
                                    <div style="padding: 8px;">
                                        <p style="font-weight: bold; color: #CA8A04; margin-bottom: 4px;">${point.name || 'Evacuation Point ' + (index + 1)}</p>
                                        ${point.description ? `<p style="font-size: 12px; color: #4B5563; margin-bottom: 4px;">${point.description}</p>` : ''}
                                        <p style="font-size: 11px; color: #6B7280;">Coordinates: ${newCoordinates}</p>
                                        <p style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Drag flag to adjust position</p>
                                    </div>
                                `);
                            });

                            evacuationMarkers.push(marker);
                        }
                    }
                }
            });
        }

        // OFF-LIMITS AREAS Functions
        function addOffLimitsAreaFromMap(latLng) {
            const lat = latLng.lat();
            const lng = latLng.lng();
            const coordinates = lat.toFixed(6) + ', ' + lng.toFixed(6);
            const markerIndex = offLimitsMarkers.length;

            // Add marker to map with RED flag
            const marker = new google.maps.Marker({
                position: latLng,
                map: offLimitsMap,
                title: 'Off-Limits Area ' + (markerIndex + 1),
                draggable: true,
                animation: google.maps.Animation.DROP,
                icon: {
                    path: 'M 0,-40 L 0,0 M 0,-40 L 20,-32 L 0,-24 Z',
                    fillColor: '#EF4444',
                    fillOpacity: 1,
                    strokeColor: '#991B1B',
                    strokeWeight: 2.5,
                    scale: 1.2,
                    anchor: new google.maps.Point(0, 0)
                }
            });

            marker.markerIndex = markerIndex;

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 8px;">
                        <p style="font-weight: bold; color: #991B1B; margin-bottom: 4px;">🚩 Off-Limits Area ${markerIndex + 1}</p>
                        <p style="font-size: 12px; color: #6B7280;">Coordinates: ${coordinates}</p>
                        <p style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Drag flag to adjust position</p>
                    </div>
                `
            });

            marker.addListener('click', function() {
                infoWindow.open(offLimitsMap, marker);
            });

            marker.addListener('dragend', function(event) {
                const newLat = event.latLng.lat();
                const newLng = event.latLng.lng();
                const newCoordinates = newLat.toFixed(6) + ', ' + newLng.toFixed(6);
                const index = marker.markerIndex;
                const input = document.querySelector(`input[name="off_limits_areas[${index}][coordinates]"]`);
                if (input) {
                    input.value = newCoordinates;
                }
                
                infoWindow.setContent(`
                    <div style="padding: 8px;">
                        <p style="font-weight: bold; color: #991B1B; margin-bottom: 4px;">⛔ Off-Limits Area ${index + 1}</p>
                        <p style="font-size: 12px; color: #6B7280;">Coordinates: ${newCoordinates}</p>
                        <p style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Drag flag to adjust position</p>
                    </div>
                `);
            });

            offLimitsMarkers.push(marker);
            addOffLimitsArea(coordinates);
        }

        function loadExistingOffLimitsAreas() {
            const existingAreas = @json($offLimitsAreas ?? []);
            
            existingAreas.forEach((area, index) => {
                if (area.coordinates) {
                    const coords = area.coordinates.split(',');
                    if (coords.length === 2) {
                        const lat = parseFloat(coords[0].trim());
                        const lng = parseFloat(coords[1].trim());
                        
                        if (!isNaN(lat) && !isNaN(lng)) {
                            const marker = new google.maps.Marker({
                                position: { lat: lat, lng: lng },
                                map: offLimitsMap,
                                title: area.name || 'Off-Limits Area ' + (index + 1),
                                draggable: true,
                                animation: google.maps.Animation.DROP,
                                icon: {
                                    path: 'M 0,-40 L 0,0 M 0,-40 L 20,-32 L 0,-24 Z',
                                    fillColor: '#EF4444',
                                    fillOpacity: 1,
                                    strokeColor: '#991B1B',
                                    strokeWeight: 2.5,
                                    scale: 1.2,
                                    anchor: new google.maps.Point(0, 0)
                                }
                            });

                            marker.markerIndex = index;

                            const infoWindow = new google.maps.InfoWindow({
                                content: `
                                    <div style="padding: 8px;">
                                        <p style="font-weight: bold; color: #991B1B; margin-bottom: 4px;">🚩 ${area.name || 'Off-Limits Area ' + (index + 1)}</p>
                                        ${area.reason ? `<p style="font-size: 12px; color: #4B5563; margin-bottom: 4px;">${area.reason}</p>` : ''}
                                        <p style="font-size: 11px; color: #6B7280;">Coordinates: ${area.coordinates}</p>
                                        <p style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Drag flag to adjust position</p>
                                    </div>
                                `
                            });

                            marker.addListener('click', function() {
                                infoWindow.open(offLimitsMap, marker);
                            });

                            marker.addListener('dragend', function(event) {
                                const newLat = event.latLng.lat();
                                const newLng = event.latLng.lng();
                                const newCoordinates = newLat.toFixed(6) + ', ' + newLng.toFixed(6);
                                const input = document.querySelector(`input[name="off_limits_areas[${index}][coordinates]"]`);
                                if (input) {
                                    input.value = newCoordinates;
                                }
                                
                                infoWindow.setContent(`
                                    <div style="padding: 8px;">
                                        <p style="font-weight: bold; color: #991B1B; margin-bottom: 4px;">⛔ ${area.name || 'Off-Limits Area ' + (index + 1)}</p>
                                        ${area.reason ? `<p style="font-size: 12px; color: #4B5563; margin-bottom: 4px;">${area.reason}</p>` : ''}
                                        <p style="font-size: 11px; color: #6B7280;">Coordinates: ${newCoordinates}</p>
                                        <p style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Drag flag to adjust position</p>
                                    </div>
                                `);
                            });

                            offLimitsMarkers.push(marker);
                        }
                    }
                }
            });
        }

        function addEmergencyNumber() {
            const container = document.getElementById('emergency-numbers-container');
            const html = `
                <div class="emergency-number-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-start gap-4">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Service Name</label>
                                <input type="text" name="emergency_numbers[${emergencyNumberIndex}][service]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="e.g., Local Police" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="text" name="emergency_numbers[${emergencyNumberIndex}][number]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="e.g., 0917-XXX-XXXX" required>
                            </div>
                        </div>
                        <button type="button" onclick="removeRow(this)" class="mt-7 text-red-600 hover:text-red-800 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            emergencyNumberIndex++;
        }

        function addHospital() {
            const container = document.getElementById('hospitals-container');
            const html = `
                <div class="hospital-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-start gap-4">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hospital Name</label>
                                <input type="text" name="hospitals[${hospitalIndex}][name]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="e.g., Baguio General Hospital" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <input type="text" name="hospitals[${hospitalIndex}][address]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="e.g., La Trinidad, Benguet" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone (Optional)</label>
                                <input type="text" name="hospitals[${hospitalIndex}][phone]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="e.g., (074) 424-1234">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Distance (Optional)</label>
                                <input type="text" name="hospitals[${hospitalIndex}][distance]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="e.g., 15 km">
                            </div>
                        </div>
                        <button type="button" onclick="removeRow(this)" class="mt-7 text-red-600 hover:text-red-800 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            hospitalIndex++;
        }

        function addRangerStation() {
            const container = document.getElementById('ranger-stations-container');
            const html = `
                <div class="ranger-station-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-start gap-4">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Station Name</label>
                                <input type="text" name="ranger_stations[${rangerStationIndex}][name]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., Mt. Pulag Ranger Station" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address/Location</label>
                                <input type="text" name="ranger_stations[${rangerStationIndex}][address]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., Babadak, Kabayan, Benguet" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone (Optional)</label>
                                <input type="text" name="ranger_stations[${rangerStationIndex}][phone]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., 0917-XXX-XXXX">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person (Optional)</label>
                                <input type="text" name="ranger_stations[${rangerStationIndex}][contact_person]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., Juan Dela Cruz">
                            </div>
                        </div>
                        <button type="button" onclick="removeRow(this)" class="mt-7 text-red-600 hover:text-red-800 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            rangerStationIndex++;
        }

        function addEvacuationPoint(coordinates = '') {
            const container = document.getElementById('evacuation-points-container');
            const html = `
                <div class="evacuation-point-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-start gap-4">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Point Name</label>
                                <input type="text" name="evacuation_points[${evacuationPointIndex}][name]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                       placeholder="e.g., Trailhead Base Camp" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Coordinates <span class="text-yellow-600">(Click map to pin)</span></label>
                                <input type="text" name="evacuation_points[${evacuationPointIndex}][coordinates]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                       placeholder="Click map or enter manually"
                                       value="${coordinates}">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="evacuation_points[${evacuationPointIndex}][description]" rows="2"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                       placeholder="e.g., Primary evacuation point with helipad access" required></textarea>
                            </div>
                        </div>
                        <button type="button" onclick="removeRow(this)" class="mt-7 text-red-600 hover:text-red-800 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            evacuationPointIndex++;
        }

        function addOffLimitsArea(coordinates = '') {
            const container = document.getElementById('offlimits-areas-container');
            const html = `
                <div class="offlimits-area-row bg-gray-50 p-4 rounded-lg border border-gray-200" data-marker-index="${offLimitsAreaIndex}">
                    <div class="flex items-start gap-4">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Area Name</label>
                                <input type="text" name="off_limits_areas[${offLimitsAreaIndex}][name]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="e.g., Landslide Zone A" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Coordinates <span class="text-red-600">(Click map to pin)</span></label>
                                <input type="text" name="off_limits_areas[${offLimitsAreaIndex}][coordinates]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="Click map or enter manually"
                                       value="${coordinates}">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reason / Warning</label>
                                <textarea name="off_limits_areas[${offLimitsAreaIndex}][reason]" rows="2"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="e.g., Active landslide area - extremely dangerous, do not enter" required></textarea>
                            </div>
                        </div>
                        <button type="button" onclick="removeOffLimitsRow(this)" class="mt-7 text-red-600 hover:text-red-800 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            offLimitsAreaIndex++;
        }

        function removeRow(button) {
            const row = button.closest('.emergency-number-row, .hospital-row, .ranger-station-row, .evacuation-point-row');
            
            // If it's an evacuation point row, also remove the marker
            if (row && row.classList.contains('evacuation-point-row')) {
                const rows = Array.from(document.querySelectorAll('.evacuation-point-row'));
                const rowIndex = rows.indexOf(row);
                
                if (rowIndex !== -1 && evacuationMarkers[rowIndex]) {
                    evacuationMarkers[rowIndex].setMap(null); // Remove marker from map
                    evacuationMarkers.splice(rowIndex, 1); // Remove from array
                }
            }
            
            row.remove();
        }

        function removeOffLimitsRow(button) {
            const row = button.closest('.offlimits-area-row');
            
            if (row) {
                const rows = Array.from(document.querySelectorAll('.offlimits-area-row'));
                const rowIndex = rows.indexOf(row);
                
                if (rowIndex !== -1 && offLimitsMarkers[rowIndex]) {
                    offLimitsMarkers[rowIndex].setMap(null); // Remove marker from map
                    offLimitsMarkers.splice(rowIndex, 1); // Remove from array
                }
            }
            
            row.remove();
        }
    </script>
    @endpush
</x-app-layout>

