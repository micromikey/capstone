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
            <x-trail-breadcrumb currentPage="Create New Trail" />
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Create New Trail') }}
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

    <!-- Small layout fixes for step UI (defensive) -->
    <style>
        /* Ensure hidden step-content is truly hidden and visible ones have compact spacing */
        .step-content.hidden { 
            display: none !important; 
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
        }
        .step-content { 
            padding: 1rem 0; 
            display: block;
            visibility: visible;
            opacity: 1;
            height: auto;
        }

        /* Prevent large implicit spacing below tab nav */
        .border-b[aria-hidden] { margin-bottom: 0; }

        /* Keep the map container visible and avoid collapsing heights */
        #trail_drawing_map { min-height: 220px; }
        
        /* Ensure only one step is visible at a time */
        .step-content:not(.hidden) ~ .step-content.hidden {
            display: none !important;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Progress Steps -->
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button type="button" data-step="1" onclick="showStep(1)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-1-nav border-[#336d66] text-[#336d66]">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-[#336d66] text-white flex items-center justify-center text-sm font-medium mr-2">1</span>
                                Basic Info
                            </span>
                        </button>
                        <button type="button" data-step="2" onclick="showStep(2)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-2-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">2</span>
                                Trail Details
                            </span>
                        </button>
                        <button type="button" data-step="3" onclick="showStep(3)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-3-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">3</span>
                                Trail Package
                            </span>
                        </button>
                        <button type="button" data-step="4" onclick="showStep(4)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-4-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">4</span>
                                Access & Safety
                            </span>
                        </button>
                        <button type="button" data-step="5" onclick="showStep(5)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-5-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">5</span>
                                Additional Info
                            </span>
                        </button>
                        <button type="button" data-step="6" onclick="showStep(6)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-6-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">6</span>
                                Trail Images
                            </span>
                        </button>
                    </nav>
                </div>

                <form method="POST" action="{{ route('org.trails.store') }}" class="p-6" id="trailForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <!-- Hidden field to store accepted trail geometry (array of {lat,lng,elevation}) -->
                    <input type="hidden" id="trail_coordinates" name="trail_coordinates" />

                    <!-- Debug: Display any validation errors at the top -->
                    @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There were some errors with your submission:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Debug: Display any success/error messages -->
                    @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ session('success') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">
                                    {{ session('error') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Step 1: Basic Information -->
                    <div id="step-1" class="step-content">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 1: Basic Information</h3>
                            <p class="text-gray-600 text-sm">Start with the essential details about your trail.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="mountain_name" value="Mountain Name *" />
                                <x-input id="mountain_name" type="text" name="mountain_name" class="mt-1 block w-full" placeholder="e.g., Mount Arayat" required />
                                <x-input-error for="mountain_name" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="trail_name" value="Trail Name *" />
                                <x-input id="trail_name" type="text" name="trail_name" class="mt-1 block w-full" placeholder="e.g., Arayat Trail" required />
                                <x-input-error for="trail_name" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="location_search" value="Location *" />
                                <div class="relative mt-1">
                                    <input type="text" id="location_search" placeholder="Search for mountains, parks, cities, or landmarks..."
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" autocomplete="off">
                                    <input type="hidden" id="location_id" name="location_id" required>
                                    <input type="hidden" id="location_latitude" name="location_latitude">
                                    <input type="hidden" id="location_longitude" name="location_longitude">
                                    <div id="location_loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    🏔️ Search for mountains, national parks, tourist spots, or any location in the Philippines
                                </div>

                                <!-- Manual coordinate entry option -->
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Can't find your location?</span>
                                        <button type="button" onclick="toggleManualCoordinates()" class="text-xs text-blue-600 hover:text-blue-800">
                                            Enter coordinates manually
                                        </button>
                                    </div>
                                    <div id="manual_coordinates" class="hidden">
                                        <div class="grid grid-cols-2 gap-3 mt-2">
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Latitude</label>
                                                <input type="number" id="manual_lat" placeholder="e.g., 14.6091" step="any"
                                                    class="w-full text-sm border-gray-300 rounded focus:ring-[#336d66] focus:border-[#336d66]">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Longitude</label>
                                                <input type="number" id="manual_lng" placeholder="e.g., 121.0223" step="any"
                                                    class="w-full text-sm border-gray-300 rounded focus:ring-[#336d66] focus:border-[#336d66]">
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <input type="text" id="manual_location_name" placeholder="Location name (e.g., Mount Arayat)"
                                                class="w-full text-sm border-gray-300 rounded focus:ring-[#336d66] focus:border-[#336d66]">
                                        </div>
                                        <button type="button" onclick="useManualCoordinates()"
                                            class="mt-2 w-full bg-blue-600 text-white text-xs py-2 rounded hover:bg-blue-700">
                                            Use These Coordinates
                                        </button>
                                    </div>
                                </div>

                                <x-input-error for="location_id" class="mt-2" />
                            </div>

                            

                            <div>
                                <x-label for="difficulty" value="Difficulty Level *" />
                                <select id="difficulty" name="difficulty" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" required>
                                    <option value="">Select Difficulty</option>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                                <x-input-error for="difficulty" class="mt-2" />
                            </div>
                            
                            <div class="md:col-span-2">
                                <x-label for="description" value="Trail Description" />
                                <textarea id="description" name="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Short description shown in listings (this will be saved to the trail description field)"></textarea>
                                <x-input-error for="description" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="button" onclick="nextStep(2)" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                Next: Trail Details
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Trail Details -->
                    <div id="step-2" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 2: Trail Details</h3>
                            <p class="text-gray-600 text-sm">Configure your trail route, then define the difficulty, duration, and terrain characteristics.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Trail Route Configuration -->
                            <div class="md:col-span-2">
                                <x-label value="Trail Route Configuration" />
                                <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex flex-wrap gap-4 mb-4">
                                        <button type="button" id="draw_trail_btn" onclick="enableTrailDrawing()"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center"
                                            title="Draw Trail Manually"
                                            aria-label="Draw Trail Manually">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            Draw Trail Manually
                                        </button>

                                        <button type="button" id="load_gpx_library_btn" onclick="openGPXLibrary()"
                                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            Load from GPX Library
                                        </button>

                                        <button type="button" id="upload_gpx_btn" onclick="document.getElementById('gpx_file').click()"
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            Upload GPX File
                                        </button>

                                        <button type="button" id="clear_trail_btn" onclick="clearTrail()"
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Clear
                                        </button>
                                    </div>

                                    <!-- Preview feedback: provider and message -->
                                    <div id="preview_feedback" class="mt-2 text-sm text-gray-600">
                                        <span id="preview_provider" class="font-medium"></span>
                                        <span id="preview_message" class="ml-2"></span>
                                    </div>

                                    <!-- Auto-route debug output removed in production -->

                                    <!-- Hidden GPX file input -->
                                    <input type="file" id="gpx_file" name="gpx_file" accept=".gpx,.kml,.kmz" style="display: none;" onchange="handleGPXUpload(this)">

                                    <!-- Trail Drawing Map -->
                                    <div id="trail_drawing_map" class="h-96 w-full rounded-lg border-2 border-dashed border-gray-300 bg-white"></div>

                                    <!-- Trail Statistics -->
                                    <div id="trail_stats" class="mt-4 p-3 bg-white rounded border hidden">
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium">Distance:</span>
                                                <span id="trail_distance">0 km</span>
                                            </div>
                                            <div>
                                                <span class="font-medium">Points:</span>
                                                <span id="trail_points">0</span>
                                            </div>
                                            <div>
                                                <span class="font-medium">Elevation Gain:</span>
                                                <span id="trail_elevation_gain">0 m</span>
                                            </div>
                                            <div>
                                                <span class="font-medium">Highest Point:</span>
                                                <span id="trail_highest_point">N/A</span>
                                            </div>
                                            <div>
                                                <span class="font-medium">Lowest Point:</span>
                                                <span id="trail_lowest_point">N/A</span>
                                            </div>
                                            <div>
                                                <span class="font-medium">Source:</span>
                                                <span id="trail_source">Manual</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Instructions -->
                                    <div class="mt-3 text-sm text-gray-600">
                                        <p><strong>Drawing Instructions:</strong> Click "Draw Trail Manually" then click on the map to add points along your trail. Double-click to finish drawing.</p>
                                        <p><strong>GPX Library:</strong> Load pre-defined accurate trails from our Philippine trail database with verified coordinates and routing.</p>
                                        <p><strong>GPX Upload:</strong> Upload a GPX file exported from GPS devices or apps like Garmin Connect, Strava, or AllTrails.</p>
                                    </div>
                                </div>

                                    <!-- Provider/Source info (kept inside preview_feedback above) -->
                            </div>



                            <!-- Duration moved to Step 3: Trail Package -->

                            <!-- Estimated Time (auto-populated from route stats) -->
                            <div>
                                <x-label for="estimated_time" value="Estimated Time (minutes)" />
                                <x-input id="estimated_time" type="number" name="estimated_time" step="1" min="0" class="mt-1 block w-full" placeholder="Auto-calculated from route (server will override client value when route metrics exist)" />
                                <div class="mt-1 text-sm text-gray-500">This is a suggested total hiking time in minutes. The server will recompute and override this value if route distance/elevation data are available.</div>
                                <x-input-error for="estimated_time" class="mt-2" />
                            </div>

                            <!-- Trail Opening and Closing Time -->
                               <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                   <div>
                                       <x-label for="opening_time" value="Trail Opening Time" />
                                       <x-input id="opening_time" type="time" name="opening_time" class="mt-1 block w-full" />
                                   </div>
                                   <div>
                                       <x-label for="closing_time" value="Trail Closing Time" />
                                       <x-input id="closing_time" type="time" name="closing_time" class="mt-1 block w-full" />
                                   </div>
                               </div>

                            <div>
                                <x-label for="best_season" value="Best Season *" />
                                <div class="mt-1 grid grid-cols-2 gap-2" id="best-season-selects" data-old="@json(old('best_season', ''))">
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

                            <div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <x-label for="length" value="Trail Length (km)" />
                                        <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full" title="This field will be auto-calculated when you draw or upload a trail route">Auto</span>
                                    </div>
                                    <button type="button" onclick="resetFieldToAuto('length')" class="reset-auto-btn text-xs text-gray-500 hover:text-blue-600 hidden" title="Reset to auto-calculation">
                                        🔄 Reset
                                    </button>
                                </div>
                                <div class="relative mt-1">
                                    <x-input id="length" type="number" name="length" step="0.1" min="0" class="pr-12 block w-full" placeholder="5.2" />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">km</span>
                                    </div>
                                </div>
                                <x-input-error for="length" class="mt-2" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <x-label for="elevation_gain" value="Elevation Gain (m)" />
                                        <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full" title="This field will be auto-calculated from trail elevation data">Auto</span>
                                    </div>
                                    <button type="button" onclick="resetFieldToAuto('elevation_gain')" class="reset-auto-btn text-xs text-gray-500 hover:text-blue-600 hidden" title="Reset to auto-calculation">
                                        🔄 Reset
                                    </button>
                                </div>
                                <div class="relative mt-1">
                                    <x-input id="elevation_gain" type="number" name="elevation_gain" min="0" class="pr-12 block w-full" placeholder="500" />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">m</span>
                                    </div>
                                </div>
                                <x-input-error for="elevation_gain" class="mt-2" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <x-label for="elevation_high" value="Highest Point (m)" />
                                        <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full" title="This field will be auto-calculated from trail elevation data">Auto</span>
                                    </div>
                                    <button type="button" onclick="resetFieldToAuto('elevation_high')" class="reset-auto-btn text-xs text-gray-500 hover:text-blue-600 hidden" title="Reset to auto-calculation">
                                        🔄 Reset
                                    </button>
                                </div>
                                <div class="relative mt-1">
                                    <x-input id="elevation_high" type="number" name="elevation_high" min="0" step="1" class="pr-12 block w-full" placeholder="1030" />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">m</span>
                                    </div>
                                </div>
                                <x-input-error for="elevation_high" class="mt-2" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <x-label for="elevation_low" value="Lowest Point (m)" />
                                        <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full" title="This field will be auto-calculated from trail elevation data">Auto</span>
                                    </div>
                                    <button type="button" onclick="resetFieldToAuto('elevation_low')" class="reset-auto-btn text-xs text-gray-500 hover:text-blue-600 hidden" title="Reset to auto-calculation">
                                        🔄 Reset
                                    </button>
                                </div>
                                <div class="relative mt-1">
                                    <x-input id="elevation_low" type="number" name="elevation_low" min="0" step="1" class="pr-12 block w-full" placeholder="200" />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">m</span>
                                    </div>
                                </div>
                                <x-input-error for="elevation_low" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="difficulty_description" value="Difficulty Description" />
                                <textarea id="difficulty_description" name="difficulty_description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Describe what makes this trail beginner/intermediate/advanced"></textarea>
                                <x-input-error for="difficulty_description" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="terrain_notes" value="Terrain Notes *" />
                                <textarea id="terrain_notes" name="terrain_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Rocky, River Crossings, Dense Forest, Steep Slopes" required></textarea>
                                <x-input-error for="terrain_notes" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="other_trail_notes" value="Other Trail Notes" />
                                <textarea id="other_trail_notes" name="other_trail_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., limited hikers, hike cut-off time, curfew, trail rules, or safety reminders"></textarea>
                                <x-input-error for="other_trail_notes" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="prevStep(1)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep(3)" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                Next: Trail Package
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Trail Package -->
                    <div id="step-3" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 3: Trail Package</h3>
                            <p class="text-gray-600 text-sm">Define package pricing, inclusions, duration, permits and transportation options.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="price" value="Price (₱) *" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <x-input id="price" type="number" name="price" step="0.01" min="0" class="pl-8 block w-full" placeholder="0.00" required />
                                </div>
                                <x-input-error for="price" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="duration" value="Duration *" />
                                <x-input id="duration" type="text" name="duration" class="mt-1 block w-full" placeholder="e.g., 36 hours or 2 days" required />
                                <x-input-error for="duration" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500">Enter total package duration (hours or days). Examples: "36 hours" or "2 days". The system will parse this to days/nights and minutes for display and scheduling.</p>
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="package_inclusions" value="Package Inclusions *" />
                                <textarea id="package_inclusions" name="package_inclusions" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Guide, Meals, Environmental Fee, Transportation" required></textarea>
                                <x-input-error for="package_inclusions" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input type="hidden" name="transport_included" value="0" />
                                    <input id="transport_included" type="checkbox" name="transport_included" value="1"
                                           class="h-4 w-4 text-[#336d66] focus:ring-[#336d66] border-gray-300 rounded">
                                    <x-label for="transport_included" value="Transportation Included?" class="ml-2" />
                                </div>
                                <p class="mt-2 text-sm text-gray-600">Check if transportation from the meeting point (e.g., pickup/return transfer) is included in the package.</p>
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="transportation_details" value="Transport Details" />
                                <div class="flex gap-2 items-center">
                                    <input id="transportation_details_visible" type="text"
                                        class="mt-1 flex-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66] hidden"
                                        placeholder="Start typing a pickup location (e.g., Cubao Terminal)" autocomplete="off" />

                                    <select id="transportation_vehicle_visible" class="mt-1 w-48 border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66] hidden" name="transportation_vehicle">
                                        <option value="" disabled selected>{{ __('Vehicle (optional)') }}</option>
                                        <option value="van">{{ __('Van') }}</option>
                                        <option value="jeep">{{ __('Jeep') }}</option>
                                        <option value="bus">{{ __('Bus') }}</option>
                                        <option value="car">{{ __('Car') }}</option>
                                        <option value="motorbike">{{ __('Motorbike') }}</option>
                                    </select>
                                </div>

                                <!-- Pickup/Departure Time -->
                                <div id="pickup_time_container" class="mb-4">
                                    <label for="pickup_time" class="block text-sm font-medium text-gray-700">Pickup Time</label>
                                    <input type="time" id="pickup_time" name="pickup_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                                </div>
                                <div id="departure_time_container" class="mb-4 hidden">
                                    <label for="departure_time" class="block text-sm font-medium text-gray-700">Departure Time</label>
                                    <input type="time" id="departure_time" name="departure_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                                </div>

                                <!-- Commute UI -->
                                <div id="commute_ui" class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Commute Guide (From → To)</label>
                                    <div id="commute_legs" class="space-y-2"></div>
                                    <div class="flex gap-2 mt-2">
                                        <button type="button" id="add_commute_leg_btn" class="px-3 py-1 bg-[#336d66] text-white text-xs rounded">Add Leg</button>
                                        <button type="button" id="clear_commute_legs_btn" class="px-3 py-1 bg-gray-200 text-xs rounded">Clear</button>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">Add one or more legs to show how hikers should commute from a meeting point to the trail (example: "Cubao Terminal → Baguio Bus Terminal"). This is informational only.</p>
                                </div>

                                <!-- Legacy visible textarea is preserved but hidden: we keep writing a human-friendly summary into it for the server/validation. -->
                                <textarea id="transport_details" name="transport_details" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66] hidden" placeholder="e.g., Bus to Tanay, Jeep to Jump-off">{{ old('transport_details') }}</textarea>

                                <!-- Always-submitted canonical field -->
                                <input type="hidden" id="transportation_details" name="transportation_details" value="">
                                <!-- Hidden canonical vehicle field for pickup mode when transport included -->
                                <input type="hidden" id="transportation_vehicle" name="transportation_vehicle" value="">
                                <!-- Optional: store pickup place metadata when transport included (place_id, name, lat/lng) -->
                                <input type="hidden" id="transportation_pickup_place" name="transportation_pickup_place" value="">

                                <x-input-error for="transportation_details" class="mt-2" />
                            </div>

                            <!-- Side trips moved to Step 3: Trail Package -->
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="prevStep(3)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep(4)" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                Next: Access & Safety
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Access & Safety -->
                    <div id="step-4" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 4: Access & Safety</h3>
                            <p class="text-gray-600 text-sm">Provide safety, access and permit information. Transport details are handled in Step 3 (Trail Package).</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Transport details intentionally omitted here; managed in Step 3 -->

                            <!-- Transport UI intentionally removed from Step 4 (managed in Step 3) -->

                            <div class="md:col-span-2">
                                <x-label for="side_trips" value="Side Trips" />

                                <div id="side-trips-list" class="space-y-2 mt-1" data-old="@json(old('side_trips', []))" data-stored="@json("")">
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

                                <!-- Hidden field to store per-side-trip place metadata for itinerary builder -->
                                <input type="hidden" id="side_trips_meta" name="side_trips_meta" value="">

                                <!-- Diagnostic banner for Google Maps/Places loader (visible in dev) -->
                                <div id="gmaps-diagnostic" class="mt-3 p-3 rounded border hidden">
                                    <strong id="gmaps-diagnostic-title">Maps status:</strong>
                                    <div id="gmaps-diagnostic-msg" class="mt-1 text-sm text-gray-700"></div>
                                    <div id="gmaps-diagnostic-actions" class="mt-2 text-sm"></div>
                                </div>

                                <x-input-error for="side_trips" class="mt-2" />

                                <!-- Side trips Places Autocomplete initialization -->
                                <script>
                                    (function(){
                                        // Ensure Google Places dropdown appears above other UI
                                        try{
                                            var style = document.createElement('style');
                                            style.innerHTML = '.pac-container{ z-index:10000 !important; }';
                                            document.head.appendChild(style);
                                        }catch(e){ /* noop */ }

                                        function attachToInput(input){
                                            if (!input) return;
                                            // don't attach to the hidden template
                                            if (input.closest && input.closest('#side-trip-row-template')) return;
                                            if (input.dataset._placesAttached) return;
                                            input.dataset._placesAttached = '1';

                                            function doAttach(){
                                                try{
                                                    if (typeof initPlaceAutocomplete === 'function'){
                                                        initPlaceAutocomplete(input);
                                                        return;
                                                    }
                                                    if (window.google && google.maps && google.maps.places){
                                                        var ac = new google.maps.places.Autocomplete(input, { types: ['geocode'] });
                                                        ac.setFields(['place_id','name','formatted_address','geometry']);
                                                        ac.addListener('place_changed', function(){
                                                            var place = ac.getPlace();
                                                            if (!place) return;
                                                            var meta = {
                                                                place_id: place.place_id || null,
                                                                name: place.name || place.formatted_address || input.value || null,
                                                                lat: place.geometry && place.geometry.location ? place.geometry.location.lat() : null,
                                                                lng: place.geometry && place.geometry.location ? place.geometry.location.lng() : null,
                                                                address_components: place.address_components || null
                                                            };
                                                            input.dataset.place = JSON.stringify(meta);
                                                        });
                                                    }
                                                }catch(e){ console.warn('side-trip autocomplete attach failed', e); }
                                            }

                                            if (typeof loadMapsScript === 'function'){
                                                loadMapsScript(doAttach);
                                            } else {
                                                // If loader not present, try immediate attach and rely on retry inside doAttach
                                                setTimeout(doAttach, 50);
                                            }
                                        }

                                        function initAll(){
                                            document.querySelectorAll('.side-trip-input').forEach(function(inp){
                                                attachToInput(inp);
                                            });
                                        }

                                        // Expose initializer so other page code (e.g., when showing Step 3) can trigger it
                                        window.initSideTrips = initAll;

                                        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initAll);
                                        else initAll();

                                        // Hook into add-side-trip so newly appended rows get initialized immediately
                                        document.addEventListener('click', function(e){
                                            var t = e.target;
                                            if (!t) return;
                                            if (t.id === 'add-side-trip' || t.closest && t.closest('#add-side-trip')){
                                                // allow existing add logic to run, then attach to the newest input
                                                setTimeout(function(){
                                                    var inputs = document.querySelectorAll('.side-trip-input');
                                                    if (!inputs.length) return;
                                                    var last = inputs[inputs.length - 1];
                                                    // clear any cloned dataset flags and value placeholder
                                                    try{ delete last.dataset._placesAttached; }catch(e){}
                                                    last.dataset.place = '';
                                                    attachToInput(last);
                                                }, 80);
                                            }

                                            // (showStep already initializes side-trip autocompletes when Step 3 is shown)
                                        });

                                        // Serialize metadata on submit
                                        var form = document.getElementById('trailForm');
                                        if (form){
                                            form.addEventListener('submit', function(){
                                                var metas = [];
                                                document.querySelectorAll('.side-trip-input').forEach(function(input){
                                                    var text = (input.value || '').trim();
                                                    if (!text) return;
                                                    var place = null;
                                                    try{ if (input.dataset.place) place = JSON.parse(input.dataset.place); }catch(e){ place = null; }
                                                    metas.push({ text: text, place: place });
                                                });
                                                var hidden = document.getElementById('side_trips_meta');
                                                if (hidden) hidden.value = JSON.stringify(metas);
                                            });
                                        }
                                    })();
                                </script>
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="emergency_contacts" value="Emergency Contacts *" />
                                <textarea id="emergency_contacts" name="emergency_contacts" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Barangay Rescue – 0917xxxxxxx, Local Police – 0998xxxxxxx" required></textarea>
                                <x-input-error for="emergency_contacts" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="campsite_info" value="Campsite Information" />
                                <textarea id="campsite_info" name="campsite_info" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Tent area at summit or No campsite"></textarea>
                                <x-input-error for="campsite_info" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="guide_info" value="Guide Information" />
                                <textarea id="guide_info" name="guide_info" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Information about guides"></textarea>
                                <x-input-error for="guide_info" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="environmental_practices" value="Environmental Practices" />
                                <textarea id="environmental_practices" name="environmental_practices" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Leave No Trace, Pack In Pack Out"></textarea>
                                <x-input-error for="environmental_practices" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="prevStep(3)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep(5)" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                Next: Additional Info
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 5: Additional Information -->
                    <div id="step-5" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 5: Additional Information</h3>
                            <p class="text-gray-600 text-sm">Complete the trail profile with permits, requirements, and feedback.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input id="permit_required" type="checkbox" name="permit_required" value="1" class="h-4 w-4 text-[#336d66] focus:ring-[#336d66] border-gray-300 rounded">
                                    <x-label for="permit_required" value="Permit Required?" class="ml-2" />
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="permit_process" value="Permit Process" />
                                <textarea id="permit_process" name="permit_process" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Apply at Municipal Hall / Online LGU Form"></textarea>
                                <x-input-error for="permit_process" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="packing_list" value="Packing List *" />
                                <textarea id="packing_list" name="packing_list" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Water (3L), Flashlight, Raincoat, First Aid Kit, Snacks" required></textarea>
                                <x-input-error for="packing_list" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="health_fitness" value="Health/Fitness Requirements *" />
                                <textarea id="health_fitness" name="health_fitness" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Moderate fitness recommended, Beginner-friendly" required></textarea>
                                <x-input-error for="health_fitness" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="requirements" value="Other Requirements" />
                                <textarea id="requirements" name="requirements" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Any other specific requirements"></textarea>
                                <x-input-error for="requirements" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="customers_feedback" value="Customers Feedback" />
                                <textarea id="customers_feedback" name="customers_feedback" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Juan Dela Cruz: Sobrang ganda ng tanawin paakyat, I'm definitely going back here!"></textarea>
                                <x-input-error for="customers_feedback" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="testimonials_faqs" value="Testimonials / Common FAQs" />
                                <textarea id="testimonials_faqs" name="testimonials_faqs" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Most frequently asked questions from hikers, especially beginners"></textarea>
                                <x-input-error for="testimonials_faqs" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="summary" value="Trail Summary" />
                                <textarea id="summary" name="summary" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Brief overview of the trail experience"></textarea>
                                <x-input-error for="summary" class="mt-2" />
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
                                        $selected = old('activities', []);
                                    @endphp

                                    @foreach($activityOptions as $value => $label)
                                        <label class="inline-flex items-center space-x-2 text-sm">
                                            <input type="checkbox" name="activities[]" value="{{ $value }}" class="h-4 w-4 text-[#336d66] focus:ring-[#336d66] border-gray-300 rounded" {{ in_array($value, $selected) ? 'checked' : '' }}>
                                            <span class="text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-xs text-gray-500">These tags help hikers find trails that support specific activities and improve recommendation matching.</p>
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="description" value="Detailed Description" />
                                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Comprehensive description of the trail, highlights, and experience"></textarea>
                                <x-input-error for="description" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="prevStep(4)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep(6)" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                Next: Trail Images
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 6: Trail Images -->
                    <div id="step-6" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 6: Trail Images</h3>
                            <p class="text-gray-600 text-sm">Upload beautiful photos of your trail to attract hikers. Add multiple images to showcase different views and trail features.</p>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="space-y-6">
                            <!-- Primary Image -->
                            <div class="border border-gray-300 rounded-lg p-6 bg-gray-50">
                                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Primary Trail Image (Required)
                                </h4>
                                <p class="text-sm text-gray-600 mb-4">This will be the main image displayed on the explore page</p>

                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                                    id="primary-drop-zone"
                                    ondrop="handleDrop(event, 'primary')"
                                    ondragover="handleDragOver(event)"
                                    ondragleave="handleDragLeave(event)">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="primary_image" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                                <span>Upload a file</span>
                                                <input id="primary_image" name="primary_image" type="file" class="sr-only" accept="image/*" onchange="handleFileSelect(this, 'primary')">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                    </div>
                                </div>
                                <div id="primary-preview" class="mt-4 hidden"></div>
                            </div>

                            <!-- Additional Images -->
                            <div class="border border-gray-300 rounded-lg p-6">
                                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Additional Trail Photos (Optional)
                                </h4>
                                <p class="text-sm text-gray-600 mb-4">Add more photos to showcase different views, trail features, or seasonal variations (up to 5 images)</p>

                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <!-- Slot 1 -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors" onclick="document.getElementById('additional_1').click()">
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <div class="text-xs text-gray-500">Add Photo</div>
                                        </div>
                                        <input type="file" id="additional_1" name="additional_images[]" class="hidden" accept="image/*" onchange="handleFileSelect(this, 'additional_1')">
                                        <div id="additional_1_preview" class="mt-2 hidden"></div>
                                    </div>

                                    <!-- Slot 2 -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors" onclick="document.getElementById('additional_2').click()">
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <div class="text-xs text-gray-500">Add Photo</div>
                                        </div>
                                        <input type="file" id="additional_2" name="additional_images[]" class="hidden" accept="image/*" onchange="handleFileSelect(this, 'additional_2')">
                                        <div id="additional_2_preview" class="mt-2 hidden"></div>
                                    </div>

                                    <!-- Slot 3 -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors" onclick="document.getElementById('additional_3').click()">
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <div class="text-xs text-gray-500">Add Photo</div>
                                        </div>
                                        <input type="file" id="additional_3" name="additional_images[]" class="hidden" accept="image/*" onchange="handleFileSelect(this, 'additional_3')">
                                        <div id="additional_3_preview" class="mt-2 hidden"></div>
                                    </div>

                                    <!-- Slot 4 -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors" onclick="document.getElementById('additional_4').click()">
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <div class="text-xs text-gray-500">Add Photo</div>
                                        </div>
                                        <input type="file" id="additional_4" name="additional_images[]" class="hidden" accept="image/*" onchange="handleFileSelect(this, 'additional_4')">
                                        <div id="additional_4_preview" class="mt-2 hidden"></div>
                                    </div>

                                    <!-- Slot 5 -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors" onclick="document.getElementById('additional_5').click()">
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <div class="text-xs text-gray-500">Add Photo</div>
                                        </div>
                                        <input type="file" id="additional_5" name="additional_images[]" class="hidden" accept="image/*" onchange="handleFileSelect(this, 'additional_5')">
                                        <div id="additional_5_preview" class="mt-2 hidden"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Trail Map Upload -->
                            <div class="border border-gray-300 rounded-lg p-6 bg-blue-50">
                                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12 1.586l-4 4v12.828l4-4 4 4V5.586l-4-4zM2 5l6-2 4 4 4-4 2 .5v13L12 18l-4-4-4 4L2 17V5z" clip-rule="evenodd"></path>
                                    </svg>
                                    Trail Map (Optional)
                                </h4>
                                <p class="text-sm text-gray-600 mb-4">Upload a trail map, elevation profile, or route diagram</p>

                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-blue-300 border-dashed rounded-md"
                                    id="map-drop-zone"
                                    ondrop="handleDrop(event, 'map')"
                                    ondragover="handleDragOver(event)"
                                    ondragleave="handleDragLeave(event)">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-blue-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M24 8l-8 8h16l-8-8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M8 24h32M8 32h32" stroke-width="2" stroke-linecap="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="map_image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Upload trail map</span>
                                                <input id="map_image" name="map_image" type="file" class="sr-only" accept="image/*" onchange="handleFileSelect(this, 'map')">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, PDF up to 10MB</p>
                                    </div>
                                </div>
                                <div id="map-preview" class="mt-4 hidden"></div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="prevStep(5)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            <button type="submit" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Create Trail
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    let currentStep = 1;
    const totalSteps = 6;
        let locationSearchTimeout;


        // Add form submission debugging and dynamic required attribute handling
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('trailForm');

            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default form submission to handle validation manually
                
                console.log('Form submission started');

                // Custom validation - only check visible fields
                let missingFields = [];
                const currentVisibleStep = document.querySelector('.step-content:not(.hidden)');
                
                // Get all originally required field names (stored as data attributes)
                const originallyRequiredFields = ['mountain_name', 'trail_name', 'location_id', 'price', 'difficulty', 'package_inclusions', 'duration', 'best_season', 'terrain_notes', 'transport_details', 'emergency_contacts', 'packing_list', 'health_fitness'];
                
                if (currentVisibleStep) {
                    // Check visible required fields only
                    currentVisibleStep.querySelectorAll('input, select, textarea').forEach(field => {
                        // Check if this field should be required
                        const shouldBeRequired = originallyRequiredFields.includes(field.name);
                        
                        if (shouldBeRequired && (!field.value || !field.value.trim())) {
                            missingFields.push(field.name);
                        }
                    });
                }

                // Special check for location_id if we're on step 1
                if (currentStep === 1) {
                    const locationId = document.getElementById('location_id').value;
                    const locationSearch = document.getElementById('location_search').value;
                    if (!locationId && locationSearch) {
                        missingFields.push('location_id (please select a location from the search results)');
                    }
                }

                if (missingFields.length > 0) {
                    console.error('Missing required fields:', missingFields);
                    alert('Please fill in all required fields: ' + missingFields.join(', '));
                    return false;
                }

                console.log('Form validation passed, submitting...');
                
                // Clean up all elevation fields - convert to integers or remove if empty/invalid
                const elevationGain = document.getElementById('elevation_gain');
                const elevationHigh = document.getElementById('elevation_high');
                const elevationLow = document.getElementById('elevation_low');
                
                console.log('Before cleanup - elevation_gain value:', elevationGain ? elevationGain.value : 'null');
                console.log('Before cleanup - elevation_high value:', elevationHigh ? elevationHigh.value : 'null');
                console.log('Before cleanup - elevation_low value:', elevationLow ? elevationLow.value : 'null');
                
                // Handle elevation_gain
                if (elevationGain) {
                    if (elevationGain.value && elevationGain.value.trim()) {
                        const intValue = parseInt(elevationGain.value, 10);
                        if (!isNaN(intValue) && intValue >= 0) {
                            elevationGain.value = intValue;
                            console.log('Set elevation_gain to:', intValue);
                        } else {
                            console.log('Invalid elevation_gain value, removing from submission');
                            elevationGain.removeAttribute('name');
                        }
                    } else {
                        console.log('Empty elevation_gain, removing from submission');
                        elevationGain.removeAttribute('name');
                    }
                }
                
                // Handle elevation_high
                if (elevationHigh) {
                    if (elevationHigh.value && elevationHigh.value.trim()) {
                        const intValue = parseInt(elevationHigh.value, 10);
                        if (!isNaN(intValue) && intValue >= 0) {
                            elevationHigh.value = intValue;
                            console.log('Set elevation_high to:', intValue);
                        } else {
                            console.log('Invalid elevation_high value, removing from submission');
                            elevationHigh.removeAttribute('name');
                        }
                    } else {
                        console.log('Empty elevation_high, removing from submission');
                        elevationHigh.removeAttribute('name');
                    }
                }
                
                // Handle elevation_low
                if (elevationLow) {
                    if (elevationLow.value && elevationLow.value.trim()) {
                        const intValue = parseInt(elevationLow.value, 10);
                        if (!isNaN(intValue) && intValue >= 0) {
                            elevationLow.value = intValue;
                            console.log('Set elevation_low to:', intValue);
                        } else {
                            console.log('Invalid elevation_low value, removing from submission');
                            elevationLow.removeAttribute('name');
                        }
                    } else {
                        console.log('Empty elevation_low, removing from submission');
                        elevationLow.removeAttribute('name');
                    }
                }
                
                // Before submit, ensure transportation_details hidden input contains the correct payload.
                try {
                    if (typeof prepareTransportationDetailsBeforeSubmit === 'function') {
                        prepareTransportationDetailsBeforeSubmit();
                    }
                } catch (err) {
                    console.error('Error preparing transportation details before submit', err);
                }

                // Submit the form without browser validation
                this.submit();
            });

            // Initialize location search
            initializeLocationSearch();

            // Add event listeners for trail metrics fields to detect manual edits
            const trailMetricFields = ['length', 'elevation_gain', 'elevation_high', 'elevation_low'];
            trailMetricFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    // Mark as manually edited when user changes the value
                    field.addEventListener('input', function() {
                        if (this.value && this.value.trim() !== '') {
                            markFieldAsManuallyEdited(fieldId);
                        }
                    });

                    // Also detect when user focuses and then changes the field
                    field.addEventListener('change', function() {
                        if (this.value && this.value.trim() !== '') {
                            markFieldAsManuallyEdited(fieldId);
                        }
                    });
                }
            });
        });

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
                    const val = pickupVisible && pickupVisible.value ? pickupVisible.value.trim() : '';
                    transportDetailsTextarea.value = val ? ('Pick-Up Point: ' + val) : '';
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
            // store place metadata on the element
            fromInput.dataset.place = '';

            const arrow = document.createElement('span');
            arrow.className = 'text-xs text-gray-500';
            arrow.textContent = '→';

            const toInput = document.createElement('input');
            toInput.type = 'text';
            toInput.placeholder = 'To (e.g., Baguio Bus Terminal)';
            toInput.className = 'flex-1 text-sm border-gray-300 rounded px-2 py-1';
            toInput.value = to;
            // store place metadata on the element
            toInput.dataset.place = '';

            // Vehicle select per leg (values use translated labels from the pickup select)
            const vehicleSelect = document.createElement('select');
            vehicleSelect.className = 'text-xs border-gray-300 rounded px-2 py-1';
            // Try to clone options from the pickup vehicle select so server-side translations and placeholder state are used
            try {
                const pickupSelect = document.getElementById('transportation_vehicle_visible');
                if (pickupSelect && pickupSelect.options && pickupSelect.options.length > 0) {
                    Array.from(pickupSelect.options).forEach(function(opt){
                        const newOpt = new Option(opt.text, opt.value);
                        // preserve placeholder disabled/selected state
                        if (opt.disabled) newOpt.disabled = true;
                        if (opt.selected) newOpt.selected = true;
                        vehicleSelect.appendChild(newOpt);
                    });
                } else {
                    // Fallback: create a disabled placeholder then real options using stable keys
                    const placeholder = new Option('Vehicle (optional)', '');
                    placeholder.disabled = true;
                    placeholder.selected = true;
                    vehicleSelect.appendChild(placeholder);
                    vehicleSelect.appendChild(new Option('Van','van'));
                    vehicleSelect.appendChild(new Option('Jeep','jeep'));
                    vehicleSelect.appendChild(new Option('Bus','bus'));
                    vehicleSelect.appendChild(new Option('Car','car'));
                    vehicleSelect.appendChild(new Option('Motorbike','motorbike'));
                }
            } catch(e) {
                // Safe programmatic fallback: construct options so the placeholder isn't selectable
                const placeholder = new Option('Vehicle (optional)', '');
                placeholder.disabled = true;
                placeholder.selected = true;
                vehicleSelect.appendChild(placeholder);
                vehicleSelect.appendChild(new Option('Van','van'));
                vehicleSelect.appendChild(new Option('Jeep','jeep'));
                vehicleSelect.appendChild(new Option('Bus','bus'));
                vehicleSelect.appendChild(new Option('Car','car'));
                vehicleSelect.appendChild(new Option('Motorbike','motorbike'));
            }

            // When 'to' changes, try to auto-populate the next leg's 'from'
            toInput.addEventListener('input', () => {
                // find this row's index
                const container = document.getElementById('commute_legs');
                const rows = Array.from(container.children);
                const idx = rows.indexOf(row);
                if (idx >= 0 && idx < rows.length - 1) {
                    const nextRow = rows[idx + 1];
                    if (nextRow) {
                        const nextFrom = nextRow.querySelectorAll('input')[0];
                        if (nextFrom && !nextFrom.value.trim()) {
                            nextFrom.value = toInput.value;
                        }
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
                // Update preview after removing a leg
                if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview();
            });

            // Update preview when inputs or vehicle change
            fromInput.addEventListener('input', () => { if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview(); });
            toInput.addEventListener('input', () => { if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview(); });
            vehicleSelect.addEventListener('change', () => { if (typeof updateTransportDetailsPreview === 'function') updateTransportDetailsPreview(); });

            // Initialize Google Places autocomplete after Maps API is loaded
            loadMapsScript(() => {
                try {
                    if (typeof initPlaceAutocomplete === 'function') {
                        initPlaceAutocomplete(fromInput);
                        initPlaceAutocomplete(toInput);
                    }
                } catch (e) {
                    console.debug('initPlaceAutocomplete failed for commute inputs', e);
                }
            });

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
                const last = container.lastElementChild;
                if (last) {
                    const lastInputs = last.querySelectorAll('input');
                    if (lastInputs && lastInputs.length >= 2) {
                        const lastTo = lastInputs[1].value.trim();
                        if (lastTo) from = lastTo;
                    }
                }
            }
            const row = createCommuteLegRow(from, to);
            container.appendChild(row);
            // ensure Places autocomplete initialized and focus the 'to' input for immediate editing
            try{
                const toInput = row.querySelectorAll('input')[1];
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

        function prepareTransportationDetailsBeforeSubmit() {
            const checkbox = document.getElementById('transport_included');
            const hidden = document.getElementById('transportation_details');
            const pickupVisible = document.getElementById('transportation_details_visible');
            const transportDetailsTextarea = document.getElementById('transport_details');

            if (!hidden) return;

            if (checkbox && checkbox.checked) {
                // Transport included: submit pickup as canonical JSON so vehicle can be persisted
                const pickupVal = pickupVisible ? pickupVisible.value.trim() : '';
                // store pickup place metadata if available
                const pickupPlaceField = document.getElementById('transportation_pickup_place');
                if (pickupPlaceField) {
                    pickupPlaceField.value = pickupVisible && pickupVisible.dataset && pickupVisible.dataset.place ? pickupVisible.dataset.place : '';
                }
                // pickup vehicle if selected (key)
                const pickupVehicleVisible = document.getElementById('transportation_vehicle_visible');
                const pickupVehicleField = document.getElementById('transportation_vehicle');
                const vehicleKey = pickupVehicleVisible ? pickupVehicleVisible.value : '';
                if (pickupVehicleField) pickupVehicleField.value = vehicleKey;

                // Build canonical payload so backend stores both pickup place and vehicle
                const canonical = { type: 'pickup', pickup_place: pickupVal || '', vehicle: vehicleKey || '' };
                hidden.value = JSON.stringify(canonical);

                // Keep the user-facing textarea as a readable notice (include human label if available)
                if (transportDetailsTextarea) {
                    let vehicleLabelText = '';
                    try {
                        if (pickupVehicleVisible && pickupVehicleVisible.selectedIndex > -1) {
                            vehicleLabelText = pickupVehicleVisible.options[pickupVehicleVisible.selectedIndex].text || '';
                        }
                    } catch(e){}
                    transportDetailsTextarea.value = pickupVal ? ('Pick-Up Point: ' + pickupVal + (vehicleLabelText ? ' (' + vehicleLabelText + ')' : '')) : '';
                }
            } else {
                // No transport included: serialize commute legs array
                const container = document.getElementById('commute_legs');
                const legs = [];
                if (container) {
                    Array.from(container.children).forEach(row => {
                        const inputs = row.querySelectorAll('input');
                        if (inputs.length >= 2) {
                            const from = inputs[0].value.trim();
                            const to = inputs[1].value.trim();
                            // vehicle is the select element (may be present)
                            const vehicleEl = row.querySelector('select');
                            const vehicle = vehicleEl ? vehicleEl.value : '';
                            if (from || to || vehicle) legs.push({ from, to, vehicle });
                        }
                    });
                }
                // For each leg we may have place metadata saved on inputs (dataset.place)
                const legsWithPlace = legs.map((leg, idx) => {
                    const row = container.children[idx];
                    if (!row) return leg;
                    const inputs = row.querySelectorAll('input');
                    const fromMeta = inputs[0] && inputs[0].dataset && inputs[0].dataset.place ? JSON.parse(inputs[0].dataset.place) : null;
                    const toMeta = inputs[1] && inputs[1].dataset && inputs[1].dataset.place ? JSON.parse(inputs[1].dataset.place) : null;
                    return Object.assign({}, leg, { from_place: fromMeta, to_place: toMeta });
                });

                hidden.value = JSON.stringify({ type: 'commute', legs: legsWithPlace });
                // Also populate the transport_details textarea with a human-friendly summary
                if (transportDetailsTextarea) {
                    // Build map of vehicle key -> label from pickup select
                    const vehicleLabelMap = {};
                    const pickupSelect = document.getElementById('transportation_vehicle_visible');
                    if (pickupSelect) {
                        for (let i = 0; i < pickupSelect.options.length; i++) {
                            const opt = pickupSelect.options[i];
                            vehicleLabelMap[opt.value] = opt.text;
                        }
                    }

                    const summaryParts = [];
                    legs.forEach(leg => {
                        const f = leg.from || '';
                        const t = leg.to || '';
                        let v = '';
                        if (leg.vehicle) {
                            v = ' (' + (vehicleLabelMap[leg.vehicle] || leg.vehicle) + ')';
                        }
                        if (f && t) summaryParts.push(f + ' → ' + t + v);
                        else if (f) summaryParts.push(f + ' → (unknown)' + v);
                        else if (t) summaryParts.push('(unknown) → ' + t + v);
                    });
                    transportDetailsTextarea.value = summaryParts.length ? summaryParts.join('; ') : '';
                }
            }
        }

        // Update the Step-3 transport_details textarea live when commute legs change
        function updateTransportDetailsPreview() {
            const container = document.getElementById('commute_legs');
            const transportDetailsTextarea = document.getElementById('transport_details');
            if (!container || !transportDetailsTextarea) return;
            const legs = [];
            Array.from(container.children).forEach(row => {
                const inputs = row.querySelectorAll('input');
                const vehicleEl = row.querySelector('select');
                const vehicle = vehicleEl ? vehicleEl.value : '';
                if (inputs.length >= 2) {
                    const from = inputs[0].value.trim();
                    const to = inputs[1].value.trim();
                    if (from || to || vehicle) legs.push({ from, to, vehicle });
                }
            });

            // Build map of vehicle key -> label from pickup select
            const vehicleLabelMap = {};
            const pickupSelect = document.getElementById('transportation_vehicle_visible');
            if (pickupSelect) {
                for (let i = 0; i < pickupSelect.options.length; i++) {
                    const opt = pickupSelect.options[i];
                    vehicleLabelMap[opt.value] = opt.text;
                }
            }

            const summaryParts = [];
            legs.forEach(leg => {
                const f = leg.from || '';
                const t = leg.to || '';
                let v = '';
                if (leg.vehicle) {
                    v = ' (' + (vehicleLabelMap[leg.vehicle] || leg.vehicle) + ')';
                }
                if (f && t) summaryParts.push(f + ' → ' + t + v);
                else if (f) summaryParts.push(f + ' → (unknown)' + v);
                else if (t) summaryParts.push('(unknown) → ' + t + v);
            });
            transportDetailsTextarea.value = summaryParts.length ? summaryParts.join('; ') : '';
        }

        // Attach handlers for add/clear buttons and toggle checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const addBtn = document.getElementById('add_commute_leg_btn');
            const clearBtn = document.getElementById('clear_commute_legs_btn');
            const checkbox = document.getElementById('transport_included');
            const pickupVisible = document.getElementById('transportation_details_visible');

            if (addBtn) addBtn.addEventListener('click', () => addCommuteLeg());
            if (clearBtn) clearBtn.addEventListener('click', () => clearCommuteLegs());
            if (checkbox) {
                checkbox.addEventListener('change', toggleTransportUI);
            }

            // Initialize UI and Places after Maps API is loaded
            loadMapsScript(() => {
                // Initialize UI state on load
                toggleTransportUI();
                // Attach autocomplete to pickup input
                if (pickupVisible && typeof initPlaceAutocomplete === 'function') {
                    initPlaceAutocomplete(pickupVisible);
                }
            });
        });

        // Initialize Google Places Autocomplete on a text input element
        function initPlaceAutocomplete(inputEl) {
            try {
                if (!window.google || !window.google.maps || !window.google.maps.places) return;
                const autocomplete = new google.maps.places.Autocomplete(inputEl, { types: ['geocode'] });
                autocomplete.setFields(['place_id','name','formatted_address','geometry']);
                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (!place || !place.place_id) return;
                    // Save place metadata as JSON on the input's dataset
                    const meta = {
                        place_id: place.place_id,
                        name: place.name || place.formatted_address || '',
                        lat: place.geometry && place.geometry.location ? place.geometry.location.lat() : null,
                        lng: place.geometry && place.geometry.location ? place.geometry.location.lng() : null,
                    };
                    inputEl.dataset.place = JSON.stringify(meta);
                });
            } catch (e) {
                console.debug('Places API not available or init failed', e);
            }
        }

        // Auto-load GPX when mountain_name and trail_name are provided
        (function setupAutoGPXLoad(){
            const mountainInput = document.getElementById('mountain_name');
            const trailInput = document.getElementById('trail_name');
            let debounceTimer = null;

            function tryAutoRoute() {
                if (!mountainInput || !trailInput) return;
                const mountain = mountainInput.value.trim();
                const trail = trailInput.value.trim();
                if (mountain.length + trail.length < 3) return;

                // Call search / load routine
                try {
                    autoRouteFromInputs();
                } catch (e) {
                    console.debug('autoRouteFromInputs not available yet');
                }
            }

            function scheduleAutoRoute() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(tryAutoRoute, 600);
            }

            if (mountainInput) mountainInput.addEventListener('input', scheduleAutoRoute);
            if (trailInput) trailInput.addEventListener('input', scheduleAutoRoute);

            // Triggering auto-route is handled inside the primary showStep implementation elsewhere.
        })();

        // Location search functionality using Google Places
        function initializeLocationSearch() {
            console.log('Initializing Google Places location search...');

            loadMapsScript(() => {
                const searchInput = document.getElementById('location_search');
                if (!searchInput) {
                    console.error('Location search input not found');
                    return;
                }

                // Initialize Places Autocomplete with enhanced search for hiking locations
                const autocomplete = new google.maps.places.Autocomplete(searchInput, {
                    componentRestrictions: {
                        country: 'PH'
                    },
                    // Remove restrictive types to allow ALL place types including natural features, mountains, parks
                    // This will include: natural_feature, park, establishment, point_of_interest, tourist_attraction, etc.
                    fields: ['place_id', 'formatted_address', 'geometry', 'name', 'address_components', 'types', 'plus_code']
                    // No types restriction = search everything
                });

                // Set bias to Philippines bounds
                const philippinesBounds = new google.maps.LatLngBounds(
                    new google.maps.LatLng(4.2158064, 114.0952145), // Southwest
                    new google.maps.LatLng(21.3217806, 127.6062314) // Northeast
                );
                autocomplete.setBounds(philippinesBounds);

                // Handle place selection
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();

                    if (!place.geometry) {
                        console.warn('No geometry for selected place');
                        // Instead of clearing, offer text search fallback
                        handleTextSearchFallback(searchInput.value);
                        return;
                    }

                    console.log('Google Places selection:', place);
                    handleGooglePlaceSelection(place);
                });

                // Add input event listener for text search fallback
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const query = this.value.trim();
                        if (query.length >= 3 && !document.getElementById('location_id').value) {
                            // Show suggestion for text search after 2 seconds of no Google Places match
                            showTextSearchSuggestion(query);
                        }
                    }, 2000);
                });

                console.log('Google Places Autocomplete initialized');
            });
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
                    // Helper to extract province / city from address_components
                    function extractAddrComponents(ac) {
                        const comps = ac || [];
                        let province = '';
                        let city = '';
                        comps.forEach(c => {
                            if (!c.types) return;
                            if (c.types.indexOf('administrative_area_level_1') !== -1) {
                                province = c.long_name;
                            }
                            if (c.types.indexOf('locality') !== -1 || c.types.indexOf('postal_town') !== -1 || c.types.indexOf('administrative_area_level_2') !== -1) {
                                if (!city) city = c.long_name;
                            }
                        });
                        return { province, city };
                    }

                    const coords = {
                        lat: place.geometry && place.geometry.location ? place.geometry.location.lat() : null,
                        lng: place.geometry && place.geometry.location ? place.geometry.location.lng() : null
                    };

                    const comp = extractAddrComponents(place.address_components);
                    const initialProvince = comp.province || '';
                    const initialCity = comp.city || '';

                    const locationData = {
                        place_id: place.place_id,
                        formatted_address: place.formatted_address || '',
                        latitude: coords.lat,
                        longitude: coords.lng,
                        name: place.name || (place.formatted_address ? place.formatted_address.split(',')[0] : ''),
                        province: initialProvince,
                        city: initialCity
                    };

            console.log('Processing Google Places location:', locationData);
                    // Function to finalize UI and send to backend
                    function finalizeAndSend(ld) {
                        // Update search display (prefer province, then city)
                        const displayParts = [];
                        if (ld.name) displayParts.push(ld.name);
                        if (ld.province) displayParts.push(ld.province);
                        else if (ld.city) displayParts.push(ld.city);

                        const display = displayParts.join(', ');
                        if (display) {
                            searchInput.value = display;
                            searchInput.dataset.lastSelectedValue = display;
                        } else if (ld.formatted_address) {
                            searchInput.value = ld.formatted_address;
                            searchInput.dataset.lastSelectedValue = ld.formatted_address;
                        }

                        // Prepare payload to send
                        const payload = Object.assign({}, ld);

                        fetch('/api/locations/google-places', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(response => response.json())
                        .then(data => {
                            loadingDiv.classList.add('hidden');

                            if (data.success && data.location) {
                                hiddenInput.value = data.location.id;
                                latInput.value = ld.latitude;
                                lngInput.value = ld.longitude;

                                // Visual feedback
                                searchInput.classList.add('border-green-500', 'bg-green-50');
                                searchInput.classList.remove('border-gray-300');
                                addLocationCheckmark();

                                // Center map if available
                                if (drawingMap && ld.latitude && ld.longitude) {
                                    drawingMap.setCenter({ lat: ld.latitude, lng: ld.longitude });
                                    drawingMap.setZoom(13);
                                }
                            } else {
                                console.error('Failed to process location:', data.error || 'Unknown error');
                                clearLocationSelection();
                                alert('Failed to process the selected location. Please try again.');
                            }
                        })
                        .catch(error => {
                            loadingDiv.classList.add('hidden');
                            console.error('Error processing location:', error);
                            clearLocationSelection();
                            alert('Error processing location. Please check your connection and try again.');
                        });
                    }

                    // If province is available immediately, finalize; otherwise attempt reverse geocode
                    if (locationData.province || locationData.city) {
                        finalizeAndSend(locationData);
                    } else if (coords.lat !== null && typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                        // Try reverse geocoding to get administrative levels (province/city)
                        const geocoder = new google.maps.Geocoder();
                        geocoder.geocode({ location: new google.maps.LatLng(coords.lat, coords.lng) }, function(results, status) {
                            if (status === 'OK' && results && results.length > 0) {
                                const r = results[0];
                                const parsed = extractAddrComponents(r.address_components);
                                locationData.province = parsed.province || '';
                                locationData.city = parsed.city || '';
                            }
                            // Finalize regardless of reverse geocode success (we updated province if found)
                            finalizeAndSend(locationData);
                        });
                    } else {
                        // No province and cannot reverse geocode — finalize with what we have
                        finalizeAndSend(locationData);
                    }
        }

        // Remove visual selection/checkmark if user edits the search input after selecting a location
        (function bindLocationInputEditHandler(){
            const searchInput = document.getElementById('location_search');
            if (!searchInput) return;

            // When user types and the value differs from the last confirmed selection,
            // clear the hidden location id and remove the visual checkmark/loading.
            searchInput.addEventListener('input', function() {
                const last = this.dataset.lastSelectedValue || '';
                if (this.value && this.value.trim() !== last) {
                    // Clear the hidden id so the form validation knows the selection is no longer valid
                    const hid = document.getElementById('location_id');
                    if (hid) hid.value = '';

                    // Remove green success styles and checkmark
                    this.classList.remove('border-green-500', 'bg-green-50');
                    this.classList.add('border-gray-300');
                    const existing = this.parentNode.querySelector('.location-selected-checkmark');
                    if (existing) existing.remove();
                }
            });
        })();

        function addLocationCheckmark() {
            const searchInput = document.getElementById('location_search');
            const existingCheckmark = searchInput.parentNode.querySelector('.location-selected-checkmark');

            if (!existingCheckmark) {
                const checkmark = document.createElement('div');
                checkmark.className = 'location-selected-checkmark absolute right-10 top-1/2 transform -translate-y-1/2 text-green-500';
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

        function showStep(step) {
            // Hide all steps with stronger enforcement
            document.querySelectorAll('.step-content').forEach((content, index) => {
                content.classList.add('hidden');
                content.style.display = 'none';
                content.style.visibility = 'hidden';
                content.style.opacity = '0';
            });

            // Show selected step with strong visibility
            const activeStep = document.getElementById(`step-${step}`);
            if (activeStep) {
                activeStep.classList.remove('hidden');
                activeStep.style.display = 'block';
                activeStep.style.visibility = 'visible';
                activeStep.style.opacity = '1';
            }

            // Update navigation styling
            updateNavigation(step);

            // Initialize drawing map when step 2 is shown
            if (step === 2) {
                setTimeout(() => {
                    ensureDrawingMapInitialized();
                    // Also attempt auto-route using current form inputs once the map is ready
                    try {
                        // Delay slightly to ensure Maps API callbacks finished
                        setTimeout(() => {
                            if (typeof autoRouteFromInputs === 'function') {
                                autoRouteFromInputs();
                            }
                        }, 300);
                    } catch (e) {
                        console.debug('autoRouteFromInputs not available at showStep time');
                    }
                }, 100);
            }

            currentStep = step;

            // Ensure the form submit button is only visible on the final step
            try {
                const submitBtns = document.querySelectorAll('button[type="submit"]');
                submitBtns.forEach(btn => btn.classList.add('hidden'));
                // Reveal submit only when on the last step
                if (step === totalSteps) {
                    submitBtns.forEach(btn => btn.classList.remove('hidden'));
                }
            } catch (e) { /* no-op */ }

            // Ensure only a single Previous button exists in the visible navigation area
            try {
                if (activeStep) {
                    const prevButtons = activeStep.querySelectorAll('button[onclick^="prevStep("]');
                    if (prevButtons.length > 1) {
                        // Remove duplicates leaving the first
                        for (let i = 1; i < prevButtons.length; i++) {
                            prevButtons[i].remove();
                        }
                    }
                }
            } catch (e) { /* no-op */ }

            // Additional safety: hide any prev/next buttons that are not inside the active step
            try {
                // Hide global duplicate prev/next buttons and reveal only those inside the active step
                document.querySelectorAll('button[onclick^="prevStep("]').forEach(function(btn){
                    if (!activeStep || !activeStep.contains(btn)) {
                        btn.style.display = 'none';
                    } else {
                        btn.style.display = '';
                    }
                });

                document.querySelectorAll('button[onclick^="nextStep("]').forEach(function(btn){
                    if (!activeStep || !activeStep.contains(btn)) {
                        btn.style.display = 'none';
                    } else {
                        btn.style.display = '';
                    }
                });
            } catch (e) { /* no-op */ }
        }

        function nextStep(step) {
            if (step <= totalSteps) {
                showStep(step);
            }
        }

        function prevStep(step) {
            if (step >= 1) {
                showStep(step);
            }
        }

        function updateNavigation(activeStep) {
            // Reset all navigation using data-step attribute so insertion of steps won't break numbering
            document.querySelectorAll('.step-nav').forEach((nav) => {
                const stepAttr = nav.getAttribute('data-step');
                const stepNumber = stepAttr ? parseInt(stepAttr, 10) : NaN;
                const circle = nav.querySelector('span span');

                if (!isNaN(stepNumber) && stepNumber <= activeStep) {
                    nav.classList.remove('border-transparent', 'text-gray-500');
                    nav.classList.add('border-[#336d66]', 'text-[#336d66]');
                    if (circle) {
                        circle.classList.remove('bg-gray-300', 'text-gray-600');
                        circle.classList.add('bg-[#336d66]', 'text-white');
                    }
                } else {
                    nav.classList.remove('border-[#336d66]', 'text-[#336d66]');
                    nav.classList.add('border-transparent', 'text-gray-500');
                    if (circle) {
                        circle.classList.remove('bg-[#336d66]', 'text-white');
                        circle.classList.add('bg-gray-300', 'text-gray-600');
                    }
                }
            });
        }

        // Initialize first step
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure clean state on page load
            currentStep = 1;
            showStep(1);
            
            // Additional safety: force hide all non-first steps
            for (let i = 2; i <= 5; i++) {
                const stepElement = document.getElementById(`step-${i}`);
                if (stepElement) {
                    stepElement.classList.add('hidden');
                    stepElement.style.display = 'none';
                }
            }
            
            // Ensure step 1 is visible
            const step1 = document.getElementById('step-1');
            if (step1) {
                step1.classList.remove('hidden');
                step1.style.display = 'block';
            }
        });

        // Image Upload Handling Functions
        function handleFileSelect(input, type) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    showImagePreview(e.target.result, type, file.name);
                };
                reader.readAsDataURL(file);
            }
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
            e.dataTransfer.dropEffect = 'copy';
            e.currentTarget.classList.add('border-green-400', 'bg-green-50');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.stopPropagation();
            e.currentTarget.classList.remove('border-green-400', 'bg-green-50');
        }

        function handleDrop(e, type) {
            e.preventDefault();
            e.stopPropagation();
            e.currentTarget.classList.remove('border-green-400', 'bg-green-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    const input = document.getElementById(type === 'primary' ? 'primary_image' : 'map_image');
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        showImagePreview(e.target.result, type, file.name);
                    };
                    reader.readAsDataURL(file);
                }
            }
        }

        function showImagePreview(src, type, fileName) {
            const previewId = type + (type.includes('additional') ? '_preview' : '-preview');
            const previewElement = document.getElementById(previewId);

            previewElement.innerHTML = `
                <div class="relative inline-block">
                    <img src="${src}" alt="Preview" class="h-32 w-32 object-cover rounded-lg shadow-md">
                    <button type="button" onclick="removeImage('${type}')" 
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                        ×
                    </button>
                    <p class="mt-2 text-xs text-gray-600 truncate w-32">${fileName}</p>
                </div>
            `;
            previewElement.classList.remove('hidden');
        }

        function removeImage(type) {
            const input = type === 'primary' ? 'primary_image' :
                type === 'map' ? 'map_image' : type;
            const previewId = type + (type.includes('additional') ? '_preview' : '-preview');

            document.getElementById(input).value = '';
            document.getElementById(previewId).classList.add('hidden');
            document.getElementById(previewId).innerHTML = '';
        }

        // Scroll to first error field
        function scrollToFirstError() {
            const firstError = document.querySelector('.border-red-300, .border-red-500, [class*="error"]');
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstError.focus();
            }
        }

        // Form validation before submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('trailForm');
            // Mark all originally required fields for later restoration
            form.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
                field.setAttribute('data-original-required', 'true');
                field.removeAttribute('required');
            });
            const submitBtn = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', function(e) {
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating Trail...
                `;
            });

            // Add error highlighting to fields
            const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                        this.classList.remove('border-gray-300', 'focus:border-green-500', 'focus:ring-green-500');
                    } else {
                        this.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                        this.classList.add('border-gray-300', 'focus:border-green-500', 'focus:ring-green-500');
                    }
                });
            });
        });

        // --- Trail Drawing and Mapping Functions ---
        let drawingMap = null;
        let drawingPolyline = null;
        let isDrawing = false;
        let drawingPath = [];
        let currentTrailCoords = [];
        let drawingModeEnabled = false;

        function loadMapsScript(cb) {
            if (typeof google !== 'undefined' && google.maps) {
                cb();
                return;
            }
            if (window.__gmapsLoading) {
                (window.__gmapsCallbacks || []).push(cb);
                return;
            }
            window.__gmapsLoading = true;
            window.__gmapsCallbacks = [cb];
            const key = document.querySelector('meta[name="google-maps-api-key"]');
            if (!key || !key.content) {
                console.warn('No Google Maps key');
                cb();
                return;
            }
            const s = document.createElement('script');
            s.src = `https://maps.googleapis.com/maps/api/js?key=${key.content}&libraries=geometry,places&v=weekly`;
            s.async = true;
            s.defer = true;
            s.onload = () => {
                (window.__gmapsCallbacks || []).forEach(fn => fn());
                window.__gmapsCallbacks = [];
            };
            document.head.appendChild(s);
        }

        // Diagnostic helper: show a visible dev banner if Maps/Places fail to load
        (function gmapsDiagnostic(){
            var diag = document.getElementById('gmaps-diagnostic');
            var title = document.getElementById('gmaps-diagnostic-title');
            var msg = document.getElementById('gmaps-diagnostic-msg');
            var actions = document.getElementById('gmaps-diagnostic-actions');
            if (!diag) return;

            function show(statusText, detail, level){
                diag.classList.remove('hidden');
                diag.classList.remove('border-red-200','bg-red-50','border-yellow-200','bg-yellow-50','border-green-200','bg-green-50');
                if (level === 'error') diag.classList.add('border-red-200','bg-red-50');
                else if (level === 'warn') diag.classList.add('border-yellow-200','bg-yellow-50');
                else diag.classList.add('border-green-200','bg-green-50');
                title.textContent = 'Maps status: ' + statusText;
                msg.textContent = detail;
            }

            // If meta tag missing, show immediate warning
            var keyMeta = document.querySelector('meta[name="google-maps-api-key"]');
            if (!keyMeta || !keyMeta.content){
                show('No API key', 'No <meta name="google-maps-api-key"> found. Add your key to the page for Places autocomplete to work. See README or environment settings.', 'warn');
                actions.innerHTML = '<a href="javascript:void(0)" id="gmaps-refresh" class="text-blue-600 underline">Retry check</a>';
                document.getElementById('gmaps-refresh').addEventListener('click', function(){ location.reload(); });
                return;
            }

            // Wait for loadMapsScript to actually load the script. We'll consider it failed if google.maps doesn't exist after timeout
            var timedOut = false;
            var t = setTimeout(function(){
                timedOut = true;
                if (!window.google || !window.google.maps || !window.google.maps.places){
                    show('Load failed', 'Google Maps/Places script did not load within 6 seconds. Check network, API key restrictions, or billing status. Open DevTools Console/Network for details.', 'error');
                    actions.innerHTML = '<a href="javascript:void(0)" id="gmaps-retry" class="text-blue-600 underline">Retry loading script</a>';
                    document.getElementById('gmaps-retry').addEventListener('click', function(){ location.reload(); });
                }
            }, 6000);

            // Use loadMapsScript to attempt to load the script; when successful clear timeout and show success briefly
            try{
                loadMapsScript(function(){
                    if (timedOut) return; // already timed out and banner shown
                    clearTimeout(t);
                    if (window.google && window.google.maps && window.google.maps.places){
                        show('OK', 'Google Maps & Places loaded successfully.', 'ok');
                        setTimeout(function(){ diag.classList.add('hidden'); }, 2500);
                    } else {
                        // Unexpected: loader called but maps missing
                        show('Partial load', 'Loader executed but Google Maps/Places object missing. Check console for errors.', 'warn');
                    }
                });
            }catch(e){
                clearTimeout(t);
                show('Loader error', 'Error when invoking loadMapsScript: '+(e && e.message ? e.message : String(e)), 'error');
            }
        })();

        function initializeDrawingMap() {
            loadMapsScript(() => {
                const mapElement = document.getElementById('trail_drawing_map');
                if (!mapElement) return;

                // Initialize map centered on Philippines
                const defaultCenter = {
                    lat: 14.6091,
                    lng: 121.0223
                };
                drawingMap = new google.maps.Map(mapElement, {
                    center: defaultCenter,
                    zoom: 10,
                    mapTypeId: google.maps.MapTypeId.HYBRID,
                    gestureHandling: 'greedy'
                });

                // Try to center on selected location using stored coordinates
                const latInput = document.getElementById('location_latitude');
                const lngInput = document.getElementById('location_longitude');
                if (latInput && lngInput && latInput.value && lngInput.value) {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        drawingMap.setCenter({
                            lat: lat,
                            lng: lng
                        });
                        drawingMap.setZoom(13);
                    }
                }
            });
        }

        function enableTrailDrawing() {
            if (!drawingMap) {
                initializeDrawingMap();
                setTimeout(() => enableTrailDrawing(), 1000);
                return;
            }

            drawingModeEnabled = true;
            isDrawing = true;
            drawingPath = [];

            // Update button states
            updateDrawingButtons();

            // Change cursor
            drawingMap.setOptions({
                draggableCursor: 'crosshair'
            });

            // Add click listener for drawing
            const clickListener = drawingMap.addListener('click', (event) => {
                if (!isDrawing) return;

                const lat = event.latLng.lat();
                const lng = event.latLng.lng();

                addPointToPath(lat, lng);
            });

            // Add double-click listener to finish drawing
            const dblClickListener = drawingMap.addListener('dblclick', (event) => {
                if (!isDrawing) return;

                finishDrawing();
                google.maps.event.removeListener(clickListener);
                google.maps.event.removeListener(dblClickListener);
            });

            showStatus('Click on the map to add trail points. Double-click to finish.', 'blue');
        }

        function addPointToPath(lat, lng) {
            drawingPath.push({
                lat: lat,
                lng: lng
            });

            // Update or create polyline
            if (drawingPolyline) {
                drawingPolyline.setPath(drawingPath);
            } else {
                drawingPolyline = new google.maps.Polyline({
                    path: drawingPath,
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 3,
                    map: drawingMap
                });
            }

            // Add marker for the point
            new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: lng
                },
                map: drawingMap,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 4,
                    fillColor: '#FF0000',
                    fillOpacity: 1,
                    strokeWeight: 1,
                    strokeColor: '#FFFFFF'
                }
            });

            updateTrailStats();
        }

        function finishDrawing() {
            if (drawingPath.length < 2) {
                showStatus('Please add at least 2 points to create a trail.', 'red');
                return;
            }

            isDrawing = false;
            drawingModeEnabled = false;
            currentTrailCoords = [...drawingPath];

            // Update hidden input
            document.getElementById('trail_coordinates').value = JSON.stringify(currentTrailCoords);

            // Change cursor back
            drawingMap.setOptions({
                draggableCursor: 'default'
            });

            updateDrawingButtons();
            updateTrailStats();
            showStatus(`Trail drawn successfully! ${currentTrailCoords.length} points added.`, 'green');
        }

        function clearTrail() {
            // Clear drawing state
            isDrawing = false;
            drawingModeEnabled = false;
            drawingPath = [];
            currentTrailCoords = [];

            // Clear map
            if (drawingPolyline) {
                drawingPolyline.setMap(null);
                drawingPolyline = null;
            }

            // Clear markers
            if (drawingMap) {
                // Clear all markers and polylines
                const bounds = new google.maps.LatLngBounds();
                drawingMap.setCenter({
                    lat: 14.6091,
                    lng: 121.0223
                });
                drawingMap.setZoom(10);

                // Remove all overlays
                google.maps.event.trigger(drawingMap, 'resize');
            }

            // Clear hidden input
            document.getElementById('trail_coordinates').value = '';

            // Update UI
            updateDrawingButtons();
            updateTrailStats();
            hideTrailStats();
            showStatus('Trail cleared.', 'gray');
        }

        // GPX Library Functions
        function openGPXLibrary() {
            document.getElementById('gpxLibraryModal').classList.remove('hidden');
            loadGPXLibrary();
        }

        function closeGPXLibrary() {
            document.getElementById('gpxLibraryModal').classList.add('hidden');
            // Reset modal state
            document.getElementById('gpxLibraryLoading').classList.remove('hidden');
            document.getElementById('gpxFilesList').classList.add('hidden');
            document.getElementById('trailSelectionSection').classList.add('hidden');
            document.getElementById('gpxLibraryError').classList.add('hidden');
        }

        function loadGPXLibrary() {
            // Show loading state
            document.getElementById('gpxLibraryLoading').classList.remove('hidden');
            document.getElementById('gpxFilesList').classList.add('hidden');
            document.getElementById('gpxLibraryError').classList.add('hidden');

            fetch('/api/gpx-library', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || `HTTP ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('gpxLibraryLoading').classList.add('hidden');
                
                if (data.success) {
                    if (data.files && data.files.length > 0) {
                        displayGPXFiles(data.files);
                        document.getElementById('gpxFilesList').classList.remove('hidden');
                    } else {
                        const errorEl = document.getElementById('gpxLibraryError');
                        errorEl.innerHTML = '<p class="text-yellow-600">No GPX files found in the library. Please upload GPX files to the geojson/ folder in your storage.</p>';
                        errorEl.classList.remove('hidden');
                    }
                } else {
                    const errorEl = document.getElementById('gpxLibraryError');
                    errorEl.innerHTML = `<p class="text-red-600">${data.message || 'Failed to load GPX library'}</p>`;
                    if (data.debug) {
                        errorEl.innerHTML += `<p class="text-sm text-gray-600 mt-2">Disk: ${data.debug.disk}<br>Error: ${data.debug.error}</p>`;
                    }
                    errorEl.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading GPX library:', error);
                document.getElementById('gpxLibraryLoading').classList.add('hidden');
                const errorEl = document.getElementById('gpxLibraryError');
                errorEl.innerHTML = `<p class="text-red-600">Error: ${error.message}</p><p class="text-sm text-gray-600 mt-2">Check the browser console and server logs for more details.</p>`;
                errorEl.classList.remove('hidden');
            });
        }

        function displayGPXFiles(files) {
            const container = document.getElementById('gpxFilesContainer');
            const searchInput = document.getElementById('gpxSearchInput');
            
            function renderFiles(filteredFiles) {
                container.innerHTML = '';
                
                if (filteredFiles.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 text-center py-4">No GPX files found</p>';
                    return;
                }

                filteredFiles.forEach(file => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors';
                    fileItem.onclick = () => selectGPXFile(file);
                    
                    const fileSize = (file.size / 1024).toFixed(1) + ' KB';
                    const modifiedDate = new Date(file.modified * 1000).toLocaleDateString();
                    
                    fileItem.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">${file.name}</h4>
                                <p class="text-sm text-gray-500">Size: ${fileSize} • Modified: ${modifiedDate}</p>
                            </div>
                            <div class="flex items-center text-[#336d66]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                        </div>
                    `;
                    
                    container.appendChild(fileItem);
                });
            }

            // Initial render
            renderFiles(files);

            // Search functionality
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const filteredFiles = files.filter(file => 
                    file.name.toLowerCase().includes(searchTerm)
                );
                renderFiles(filteredFiles);
            });
        }

        function selectGPXFile(file) {
            // Show loading for trail selection
            document.getElementById('trailSelectionSection').classList.add('hidden');
            
            // Parse the GPX file
            fetch('/api/gpx-library/parse', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ filename: file.filename })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayTrailSelection(data.data, file);
                } else {
                    alert('Failed to parse GPX file: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error parsing GPX file:', error);
                alert('Error parsing GPX file. Please try again.');
            });
        }

        function displayTrailSelection(gpxData, file) {
            const trailsList = document.getElementById('trailsList');
            const trailSelectionSection = document.getElementById('trailSelectionSection');
            
            trailsList.innerHTML = '';
            
            if (gpxData.trails.length === 0) {
                trailsList.innerHTML = '<p class="text-gray-500 text-center py-4">No trails found in this GPX file</p>';
            } else {
                gpxData.trails.forEach((trail, index) => {
                    const trailItem = document.createElement('div');
                    trailItem.className = 'p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors';
                    trailItem.onclick = () => loadTrailFromGPX(trail, file);
                    
                    trailItem.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">${trail.name}</h4>
                                <p class="text-sm text-gray-600 mt-1">${trail.description}</p>
                                <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500">
                                    <span>Distance: ${(trail.distance / 1000).toFixed(1)} km</span>
                                    <span>Points: ${trail.coordinates ? trail.coordinates.length : 0}</span>
                                    <span>Elevation Gain: ${trail.elevation_gain || 0} m</span>
                                    <span>Max Elevation: ${trail.max_elevation || 0} m</span>
                                </div>
                            </div>
                            <div class="flex items-center text-[#336d66] ml-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                    `;
                    
                    trailsList.appendChild(trailItem);
                });
            }
            
            trailSelectionSection.classList.remove('hidden');
        }

        function loadTrailFromGPX(trail, file) {
            try {
                // Convert coordinate format from [lat, lng] to {lat: x, lng: y}
                const formattedCoordinates = trail.coordinates.map(coord => {
                    if (Array.isArray(coord)) {
                        return { lat: coord[0], lng: coord[1] };
                    }
                    return coord; // Already in correct format
                });
                
                // Load trail coordinates
                loadTrailCoordinates(formattedCoordinates, `GPX Library: ${trail.name}`);
                
                // Auto-populate form fields if they're empty
                const mountainNameInput = document.getElementById('mountain_name');
                const trailNameInput = document.getElementById('trail_name');
                
                if (!mountainNameInput.value && trail.name) {
                    // Try to extract mountain name from trail name
                    const nameParts = trail.name.split(/[-–—]/);
                    if (nameParts.length >= 2) {
                        mountainNameInput.value = nameParts[0].trim();
                        trailNameInput.value = nameParts.slice(1).join('-').trim();
                    } else {
                        trailNameInput.value = trail.name;
                    }
                }
                
                // Auto-populate trail stats if not already filled
                const lengthInput = document.getElementById('length');
                const elevationGainInput = document.getElementById('elevation_gain');
                const elevationHighInput = document.getElementById('elevation_high');
                const elevationLowInput = document.getElementById('elevation_low');
                
                if (!lengthInput.value && trail.distance) {
                    lengthInput.value = (trail.distance / 1000).toFixed(1); // Convert meters to km
                }
                if (!elevationGainInput.value && trail.elevation_gain) {
                    elevationGainInput.value = trail.elevation_gain;
                }
                if (!elevationHighInput.value && trail.max_elevation) {
                    elevationHighInput.value = trail.max_elevation;
                }
                if (!elevationLowInput.value && trail.min_elevation) {
                    elevationLowInput.value = trail.min_elevation;
                }
                
                // Show success message
                showStatus(`Trail "${trail.name}" loaded successfully from GPX library! ${trail.coordinates.length} points imported.`, 'green');
                
                // Close modal
                closeGPXLibrary();
                
            } catch (error) {
                console.error('Error loading trail from GPX:', error);
                alert('Error loading trail. Please try again.');
            }
        }

        // Auto-Route from Form Inputs using GPX Library
        // Clean a user-provided name for use in GPX library searches.
        function cleanAutoRouteKeyword(raw) {
            if (!raw || typeof raw !== 'string') return '';
            // Lowercase, remove punctuation, and strip common generic tokens
            let s = raw.toLowerCase();
            // Replace punctuation with spaces
            s = s.replace(/[.,/#!$%^&*;:{}=_`"~()\[\]-]/g, ' ');
            // Remove common generic words that add noise
            const generic = ['mount', 'mountain', 'mt', 'mtn', 'trail', 'trails', 'hill', 'peak', 'range'];
            const parts = s.split(/\s+/).filter(Boolean).filter(part => !generic.includes(part));
            // Rejoin and trim
            return parts.join(' ').trim();
        }

        function autoRouteFromInputs() {
            const mountainNameRaw = document.getElementById('mountain_name').value.trim();
            const trailNameRaw = document.getElementById('trail_name').value.trim();
            const mountainName = cleanAutoRouteKeyword(mountainNameRaw);
            const trailName = cleanAutoRouteKeyword(trailNameRaw);
            // `location_id` is a hidden input (not a select). Read the visible `location_search`
            // value and the hidden id safely to avoid runtime errors when a select isn't present.
            const locationSearchEl = document.getElementById('location_search');
            const locationIdEl = document.getElementById('location_id');
            let locationTextFallback = '';
            if (locationSearchEl && locationSearchEl.value) locationTextFallback = locationSearchEl.value;
            // If the UI stored a last selected display value, prefer that
            if (locationSearchEl && locationSearchEl.dataset && locationSearchEl.dataset.lastSelectedValue) {
                locationTextFallback = locationSearchEl.dataset.lastSelectedValue;
            }
            
            if (!mountainNameRaw) {
                // If the raw field is empty, ask user to fill it. If it's non-empty but cleaned result is empty it means
                // the user typed only generic tokens (e.g. "Mount"), so we'll proceed but warn in console.
                alert('Please enter a mountain name first.');
                document.getElementById('mountain_name').focus();
                return;
            }

            if (mountainNameRaw && !mountainName) {
                console.debug('Auto-route: mountain name cleaned to empty after stripping generic tokens. Using raw input for search.');
            }

            showStatus('Searching for trail in GPX library...', 'blue');

            // Use the previously-determined location text fallback
            let locationText = locationTextFallback || '';
            
            console.log('Auto Route Debug:', {
                mountainName: mountainName,
                trailName: trailName,
                locationText: locationText,
                location_id: locationIdEl ? locationIdEl.value : null
            });

            // Use the direct search endpoint instead of parsing all files
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const csrfValue = csrfToken ? csrfToken.getAttribute('content') : '';
            
            fetch('/api/gpx-library/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfValue
                },
                body: JSON.stringify({
                    // Prefer cleaned tokens; fall back to raw if cleaning removed everything
                    mountain_name: mountainName || mountainNameRaw,
                    trail_name: trailName || trailNameRaw,
                    location: locationText
                })
            })
            .then(response => response.json())
                .then(data => {
                if (data.success && data.trails && data.trails.length > 0) {
                    // Also log to console for developers
                    console.log('Auto Route Results:', data.trails);
                    handleAutoRouteResults(data.trails, mountainName, trailName);
                } else {
                    showStatus('No matching trails found in GPX library. Try manual coordinate entry.', 'orange');
                }
            })
            .catch(error => {
                console.error('Error searching GPX library:', error);
                showStatus('Error searching GPX library. Please try manual selection.', 'red');
            });
        }

        function searchTrailsInGPXFiles(gpxFiles, mountainName, trailName, locationName) {
            let foundTrails = [];
            let filesProcessed = 0;
            const totalFiles = gpxFiles.length;

            if (totalFiles === 0) {
                showStatus('No GPX files available for auto-routing.', 'red');
                return;
            }

            // Process each GPX file to find matching trails
            gpxFiles.forEach(file => {
                fetch('/api/gpx-library/parse', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ filename: file.filename })
                })
                .then(response => response.json())
                .then(data => {
                    filesProcessed++;
                    
                    if (data.success && data.data.trails) {
                        // Search for matching trails in this file
                        const matches = findMatchingTrails(data.data.trails, mountainName, trailName, locationName);
                        foundTrails = foundTrails.concat(matches.map(trail => ({
                            ...trail,
                            source_file: file.filename
                        })));
                    }

                    // If all files processed, show results
                    if (filesProcessed === totalFiles) {
                        handleAutoRouteResults(foundTrails, mountainName, trailName);
                    }
                })
                .catch(error => {
                    console.error(`Error parsing ${file.filename}:`, error);
                    filesProcessed++;
                    
                    if (filesProcessed === totalFiles) {
                        handleAutoRouteResults(foundTrails, mountainName, trailName);
                    }
                });
            });
        }

        function findMatchingTrails(trails, mountainName, trailName, locationName) {
            const matches = [];
            
            trails.forEach(trail => {
                let score = 0;
                const trailNameLower = trail.name.toLowerCase();
                const mountainNameLower = mountainName.toLowerCase();
                const trailNameInputLower = trailName.toLowerCase();
                const locationNameLower = locationName.toLowerCase();

                // Exact mountain name match (highest score)
                if (trailNameLower.includes(mountainNameLower)) {
                    score += 100;
                }

                // Partial mountain name match
                const mountainWords = mountainNameLower.split(/\s+/);
                mountainWords.forEach(word => {
                    if (word.length > 2 && trailNameLower.includes(word)) {
                        score += 30;
                    }
                });

                // Trail name match (if provided)
                if (trailNameInputLower && trailNameLower.includes(trailNameInputLower)) {
                    score += 50;
                }

                // Location match (if available)
                if (locationNameLower && trailNameLower.includes(locationNameLower)) {
                    score += 20;
                }

                // Special keywords for better matching
                const keywords = ['mount', 'mt', 'peak', 'summit', 'trail', 'hike'];
                keywords.forEach(keyword => {
                    if (mountainNameLower.includes(keyword) && trailNameLower.includes(keyword)) {
                        score += 10;
                    }
                });

                // Only include trails with a reasonable match score
                if (score >= 50) {
                    matches.push({
                        ...trail,
                        match_score: score
                    });
                }
            });

            // Sort by match score (highest first)
            return matches.sort((a, b) => b.match_score - a.match_score);
        }

        function handleAutoRouteResults(foundTrails, mountainName, trailName) {
            if (foundTrails.length === 0) {
                showStatus(`No matching trails found for "${mountainName}${trailName ? ' - ' + trailName : ''}". Try browsing the GPX Library manually.`, 'orange');
                return;
            }

            // Decide whether to auto-load the top match or prompt user selection.
            const bestMatch = foundTrails[0];
            const secondMatch = foundTrails.length > 1 ? foundTrails[1] : null;

            const ABSOLUTE_THRESHOLD = 80; // absolute score to auto-load (relaxed from 90 for better auto-load success)
            const DOMINANCE_GAP = 25; // score gap vs next best to auto-load

            const shouldAutoLoad = (
                foundTrails.length === 1 ||
                bestMatch.match_score >= ABSOLUTE_THRESHOLD ||
                (secondMatch && (bestMatch.match_score - secondMatch.match_score) >= DOMINANCE_GAP)
            );

            if (shouldAutoLoad) {
                // Auto-load the best match into the map
                try {
                    // If bestMatch doesn't include coordinates, request server parse for the source file
                    if (!bestMatch.coordinates || bestMatch.coordinates.length === 0) {
                        fetch('/api/gpx-library/parse', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ filename: bestMatch.source_file })
                        })
                        .then(r => r.json())
                        .then(parseData => {
                            if (parseData.success && Array.isArray(parseData.data.trails)) {
                                const parsedTrail = parseData.data.trails.find(t => t.name === bestMatch.name) || parseData.data.trails[0];
                                if (parsedTrail && parsedTrail.coordinates && parsedTrail.coordinates.length > 0) {
                                    parsedTrail.match_score = bestMatch.match_score;
                                    parsedTrail.source_file = bestMatch.source_file;
                                    loadTrailFromGPX(parsedTrail, { filename: bestMatch.source_file });
                                    showStatus(`Auto-loaded trail: "${parsedTrail.name}" (Score: ${bestMatch.match_score})`, 'green');
                                } else {
                                    throw new Error('Parsed trail contained no coordinates');
                                }
                            } else {
                                throw new Error('Failed to parse GPX on server');
                            }
                        })
                        .catch(err => {
                            console.error('Failed to auto-parse GPX for auto-load:', err);
                            showStatus('Found a good match but failed to parse the GPX file. Open the GPX Library to select manually.', 'red');
                            // Do not automatically open the selection dialog; require manual user action
                        });
                    } else {
                        loadTrailFromGPX(bestMatch, { filename: bestMatch.source_file });
                        showStatus(`Auto-loaded trail: "${bestMatch.name}" (Score: ${bestMatch.match_score})`, 'green');
                    }
                } catch (e) {
                    console.error('Failed to auto-load GPX:', e);
                    showStatus('Found a good match but failed to auto-load the GPX. Open the GPX Library to select manually.', 'red');
                    // Not opening the dialog automatically
                }
            } else {
                // Inform the user there are multiple plausible matches but do not open a popup
                showStatus(`Multiple matching trails found for "${mountainName}${trailName ? ' - ' + trailName : ''}". Open the GPX Library to choose the correct trail.`, 'yellow');
            }
        }

        function showTrailSelectionDialog(trails, mountainName, trailName) {
            const dialogHtml = `
                <div id="trailSelectionDialog" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                        <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                            <div class="flex items-center justify-between pb-4 border-b">
                                <h3 class="text-lg font-medium text-gray-900">Multiple Trails Found</h3>
                                <button type="button" onclick="closeTrailSelectionDialog()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-4">Found ${trails.length} matching trails for "${mountainName}${trailName ? ' - ' + trailName : ''}". Select the best match:</p>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    ${trails.map((trail, index) => `
                                        <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" onclick="selectAutoRouteTrail(${index})">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <h4 class="font-medium text-gray-900">${trail.name}</h4>
                                                    <p class="text-sm text-gray-600 mt-1">${trail.description}</p>
                                                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500">
                                                        <span>Match: ${trail.match_score}%</span>
                                                        <span>Distance: ${(trail.distance / 1000).toFixed(1)} km</span>
                                                        <span>Elevation: ${trail.elevation_gain || 0} m</span>
                                                        <span>Source: ${trail.source_file}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Store trails for selection
            window.autoRouteTrails = trails;

            // Add dialog to page
            document.body.insertAdjacentHTML('beforeend', dialogHtml);
        }

        function selectAutoRouteTrail(index) {
            const trail = window.autoRouteTrails[index];
            loadTrailFromGPX(trail, { filename: trail.source_file });
            closeTrailSelectionDialog();
            showStatus(`Loaded trail: "${trail.name}"`, 'green');
        }

        function closeTrailSelectionDialog() {
            const dialog = document.getElementById('trailSelectionDialog');
            if (dialog) {
                dialog.remove();
            }
            delete window.autoRouteTrails;
        }

        function handleGPXUpload(input) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const gpxContent = e.target.result;
                    const coordinates = parseGPX(gpxContent);

                    if (coordinates && coordinates.length > 0) {
                        loadTrailCoordinates(coordinates, 'GPX Upload');
                        showStatus(`GPX file loaded successfully! ${coordinates.length} points imported.`, 'green');
                    } else {
                        showStatus('Invalid GPX file or no track points found.', 'red');
                    }
                } catch (error) {
                    console.error('GPX parsing error:', error);
                    showStatus('Error parsing GPX file.', 'red');
                }
            };
            reader.readAsText(file);
        }

        function parseGPX(gpxContent) {
            try {
                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(gpxContent, 'text/xml');

                const coordinates = [];

                // Try to find track points (trkpt)
                const trackPoints = xmlDoc.getElementsByTagName('trkpt');
                for (let i = 0; i < trackPoints.length; i++) {
                    const point = trackPoints[i];
                    const lat = parseFloat(point.getAttribute('lat'));
                    const lng = parseFloat(point.getAttribute('lon'));

                    if (!isNaN(lat) && !isNaN(lng)) {
                        const coord = {
                            lat: lat,
                            lng: lng
                        };

                        // Try to get elevation
                        const eleElement = point.getElementsByTagName('ele')[0];
                        if (eleElement && eleElement.textContent) {
                            coord.elevation = parseFloat(eleElement.textContent);
                        }

                        coordinates.push(coord);
                    }
                }

                // If no track points, try waypoints (wpt)
                if (coordinates.length === 0) {
                    const waypoints = xmlDoc.getElementsByTagName('wpt');
                    for (let i = 0; i < waypoints.length; i++) {
                        const point = waypoints[i];
                        const lat = parseFloat(point.getAttribute('lat'));
                        const lng = parseFloat(point.getAttribute('lon'));

                        if (!isNaN(lat) && !isNaN(lng)) {
                            coordinates.push({
                                lat: lat,
                                lng: lng
                            });
                        }
                    }
                }

                return coordinates;
            } catch (error) {
                console.error('GPX parsing error:', error);
                return [];
            }
        }

        function loadTrailCoordinates(coordinates, source = 'Manual') {
            if (!drawingMap) {
                initializeDrawingMap();
                setTimeout(() => loadTrailCoordinates(coordinates, source), 1000);
                return;
            }

            currentTrailCoords = coordinates;
            document.getElementById('trail_coordinates').value = JSON.stringify(coordinates);

            // Clear existing polyline
            if (drawingPolyline) {
                drawingPolyline.setMap(null);
            }

            // Create new polyline
            drawingPolyline = new google.maps.Polyline({
                path: coordinates,
                geodesic: true,
                strokeColor: '#0066CC',
                strokeOpacity: 1.0,
                strokeWeight: 4,
                map: drawingMap
            });

            // Fit bounds to show entire trail
            const bounds = new google.maps.LatLngBounds();
            coordinates.forEach(coord => {
                bounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
            });
            drawingMap.fitBounds(bounds);

            // Add start and end markers
            if (coordinates.length > 0) {
                new google.maps.Marker({
                    position: coordinates[0],
                    map: drawingMap,
                    title: 'Trail Start',
                    icon: {
                        url: 'data:image/svg+xml;base64,' + btoa('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="green"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" fill="white" font-size="12">S</text></svg>'),
                        scaledSize: new google.maps.Size(24, 24)
                    }
                });

                new google.maps.Marker({
                    position: coordinates[coordinates.length - 1],
                    map: drawingMap,
                    title: 'Trail End',
                    icon: {
                        url: 'data:image/svg+xml;base64,' + btoa('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="red"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" fill="white" font-size="12">E</text></svg>'),
                        scaledSize: new google.maps.Size(24, 24)
                    }
                });
            }

            updateTrailStats(source);
        }

        function previewRoute() {
            const previewBtn = document.getElementById('preview_route_btn');
            previewBtn.disabled = true;
            previewBtn.textContent = 'Loading...';

            const payload = {
                mountain_name: document.getElementById('mountain_name').value || '',
                trail_name: document.getElementById('trail_name').value || '',
                location_name: document.getElementById('location_search').value || '',
                location_id: document.getElementById('location_id').value || ''
            };

            // Debug logging
            console.log('Auto-route request payload:', payload);
            showStatus('Searching for trail data...', 'blue');

            const previewUrl = "{{ route('org.trails.preview-coordinates') }}";

            fetch(previewUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    previewBtn.disabled = false;
                    previewBtn.textContent = 'Preview Auto-Route';

                    // Debug logging
                    console.log('Auto-route response:', data);

                    const previewProviderEl = document.getElementById('preview_provider');
                    const previewMessageEl = document.getElementById('preview_message');

                    if (!data || (!data.coordinates && !data.data?.coordinates)) {
                        // Clear preview feedback
                        if (previewProviderEl) previewProviderEl.textContent = '';
                        if (previewMessageEl) previewMessageEl.textContent = 'No preview available. Try spelling, more specific location, or manual drawing.';

                        showStatus('No preview available for auto-route. Try: 1) Check trail/mountain spelling, 2) Use a more specific location, 3) Try manual drawing instead.', 'yellow');
                        return;
                    }

                    const coords = data.coordinates || data.data?.coordinates;
                    const provider = data.provider || data.data?.provider || data.data?.generation_method || 'unknown';
                    const confidence = data.metrics_confidence || data.data?.metrics_confidence || '';

                    if (previewProviderEl) previewProviderEl.textContent = `Provider: ${provider}`;
                    if (previewMessageEl) previewMessageEl.textContent = data.message || '';

                    loadTrailCoordinates(coords, `Auto-Route (${provider})`);
                    showStatus(`Auto-route loaded from ${provider} ${confidence ? `— confidence: ${confidence}` : ''}`, 'blue');
                })
                .catch(err => {
                    console.error('Preview error', err);
                    previewBtn.disabled = false;
                    previewBtn.textContent = 'Preview Auto-Route';
                    showStatus('Error generating preview route. Check console for details.', 'red');
                });
        }

        function updateTrailStats(source = 'Manual') {
            const statsDiv = document.getElementById('trail_stats');

            if (!currentTrailCoords || currentTrailCoords.length === 0) {
                hideTrailStats();
                return;
            }

            // Calculate distance
            let totalDistance = 0;
            for (let i = 1; i < currentTrailCoords.length; i++) {
                totalDistance += calculateDistance(
                    currentTrailCoords[i - 1].lat,
                    currentTrailCoords[i - 1].lng,
                    currentTrailCoords[i].lat,
                    currentTrailCoords[i].lng
                );
            }

            // Calculate elevation statistics if elevation data available
            let elevationGain = 0;
            let highestPoint = null;
            let lowestPoint = null;
            const hasElevation = currentTrailCoords.some(coord => coord.elevation !== undefined);

            if (hasElevation) {
                let lastElevation = null;
                const elevations = [];

                for (const coord of currentTrailCoords) {
                    if (coord.elevation !== undefined) {
                        elevations.push(coord.elevation);

                        // Calculate elevation gain (cumulative upward elevation changes)
                        if (lastElevation !== null && coord.elevation > lastElevation) {
                            elevationGain += coord.elevation - lastElevation;
                        }
                        lastElevation = coord.elevation;
                    }
                }

                // Find highest and lowest points
                if (elevations.length > 0) {
                    highestPoint = Math.max(...elevations);
                    lowestPoint = Math.min(...elevations);
                }
            }

            // Update stats display
            document.getElementById('trail_distance').textContent = (totalDistance / 1000).toFixed(2) + ' km';
            document.getElementById('trail_points').textContent = currentTrailCoords.length;
            document.getElementById('trail_elevation_gain').textContent = hasElevation ? Math.round(elevationGain) + ' m' : 'N/A';
            document.getElementById('trail_highest_point').textContent = hasElevation && highestPoint !== null ? Math.round(highestPoint) + ' m' : 'N/A';
            document.getElementById('trail_lowest_point').textContent = hasElevation && lowestPoint !== null ? Math.round(lowestPoint) + ' m' : 'N/A';
            document.getElementById('trail_source').textContent = source;

            // Auto-populate form fields (only if they appear to be auto-generated, not manually edited)
            updateFieldIfAutoGenerated('length', (totalDistance / 1000).toFixed(1));

            if (hasElevation) {
                updateFieldIfAutoGenerated('elevation_gain', Math.round(elevationGain));
                updateFieldIfAutoGenerated('elevation_high', Math.round(highestPoint));
                updateFieldIfAutoGenerated('elevation_low', Math.round(lowestPoint));
            }

            // Estimate trail time (start-to-end) using a Naismith-like rule:
            // - Base pace: 5 km/h on flat (12 min per km)
            // - Add 10 minutes for every 100 m of ascent (approx)
            // The result is in minutes and stored in the hidden `estimated_time` (integer minutes).
            try {
                const distanceKm = totalDistance / 1000;
                const baseMinutes = distanceKm * 60 / 5; // distanceKm * 12
                const climbMinutes = hasElevation ? (elevationGain / 100.0) * 10 : 0; // 10 min per 100m
                const estimatedMinutes = Math.max(1, Math.round(baseMinutes + climbMinutes));

                const hiddenEstimated = document.getElementById('estimated_time');
                if (hiddenEstimated) {
                    // store as integer minutes
                    hiddenEstimated.value = String(estimatedMinutes);
                }
                // Also update visible estimated time text in the stats panel
                try {
                    const estEl = document.getElementById('trail_estimated_time');
                    if (estEl) {
                        const m = parseInt(estimatedMinutes, 10);
                        let display = 'N/A';
                        if (!isNaN(m) && m > 0) {
                            if (m >= 60*24) {
                                const days = Math.floor(m / (60*24));
                                const hours = Math.floor((m % (60*24)) / 60);
                                display = days + ' day' + (days>1 ? 's' : '') + (hours ? ' ' + hours + ' h' : '');
                            } else if (m >= 60) {
                                const hours = Math.floor(m / 60);
                                const mins = m % 60;
                                display = hours + ' h' + (mins ? ' ' + mins + ' m' : '');
                            } else {
                                display = m + ' m';
                            }
                        }
                        estEl.textContent = display;
                    }
                } catch (err) { /* ignore */ }
            } catch (e) {
                // silently ignore
            }

            statsDiv.classList.remove('hidden');
        }

        // Helper function to intelligently update fields that haven't been manually edited
        function updateFieldIfAutoGenerated(fieldId, newValue) {
            const field = document.getElementById(fieldId);
            if (!field) return;

            const currentValue = field.value;
            const isAutoGenerated = field.dataset.autoGenerated === 'true';
            const isEmpty = !currentValue || currentValue.trim() === '';

            // Update if field is empty OR if it was previously auto-generated
            if (isEmpty || isAutoGenerated) {
                field.value = newValue;
                field.dataset.autoGenerated = 'true';

                // Add visual indicator that this field was auto-populated
                addAutoPopulatedIndicator(field);
            }
        }

        // Add visual indicator for auto-populated fields
        function addAutoPopulatedIndicator(field) {
            // Remove existing indicators
            removeAllIndicators(field);

            // Find the relative container (should be the div.relative that contains the input)
            const relativeContainer = field.closest('.relative');
            if (!relativeContainer) {
                console.warn('No relative container found for field:', field.id);
                return;
            }

            // Add new indicator - position it to the left of the unit label (km, m, etc.)
            const indicator = document.createElement('div');
            indicator.className = 'auto-populated-indicator absolute right-14 top-1/2 transform -translate-y-1/2 text-blue-500 text-xs z-10';
            indicator.innerHTML = '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            indicator.title = 'Auto-calculated from trail data';

            relativeContainer.appendChild(indicator);
        }

        // Remove all indicators from a field
        function removeAllIndicators(field) {
            const relativeContainer = field.closest('.relative');
            if (relativeContainer) {
                const indicators = relativeContainer.querySelectorAll('.auto-populated-indicator, .manual-edit-indicator');
                indicators.forEach(indicator => indicator.remove());
            }
        }

        // Reset field to auto-calculation mode
        function resetFieldToAuto(fieldId) {
            const field = document.getElementById(fieldId);
            if (field) {
                // Clear the field value
                field.value = '';

                // Mark as auto-generated
                field.dataset.autoGenerated = 'true';

                // Remove all indicators
                removeAllIndicators(field);

                // Hide the reset button
                const resetBtn = field.closest('div').querySelector('.reset-auto-btn');
                if (resetBtn) {
                    resetBtn.classList.add('hidden');
                }

                // If we have trail coordinates, recalculate and populate immediately
                if (currentTrailCoords && currentTrailCoords.length > 0) {
                    updateTrailStats('Manual');
                }
            }
        }

        // Show/hide reset button for auto-calculated fields
        function toggleResetButton(fieldId, show) {
            const field = document.getElementById(fieldId);
            if (field) {
                const resetBtn = field.closest('div').querySelector('.reset-auto-btn');
                if (resetBtn) {
                    if (show) {
                        resetBtn.classList.remove('hidden');
                    } else {
                        resetBtn.classList.add('hidden');
                    }
                }
            }
        }

        // Force recalculate all auto fields from current trail data
        function recalculateAllFields() {
            if (!currentTrailCoords || currentTrailCoords.length === 0) {
                alert('No trail data available to calculate from. Please draw a trail, upload a GPX file, or use the auto-route feature first.');
                return;
            }

            const autoFields = ['length', 'elevation_gain', 'elevation_high', 'elevation_low'];
            let recalculatedCount = 0;

            autoFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    // Reset to auto mode
                    field.dataset.autoGenerated = 'true';
                    removeAllIndicators(field);
                    toggleResetButton(fieldId, false);
                    recalculatedCount++;
                }
            });

            // Recalculate everything
            updateTrailStats('Recalculated');

            // Show feedback
            showStatus(`Recalculated ${recalculatedCount} fields from trail data`, 'green');
        }

        // Mark field as manually edited when user changes it
        function markFieldAsManuallyEdited(fieldId) {
            const field = document.getElementById(fieldId);
            if (field) {
                field.dataset.autoGenerated = 'false';

                // Remove all existing indicators
                removeAllIndicators(field);

                // Show the reset button
                toggleResetButton(fieldId, true);

                // Find the relative container
                const relativeContainer = field.closest('.relative');
                if (!relativeContainer) {
                    console.warn('No relative container found for field:', fieldId);
                    return;
                }

                // Add manual edit indicator
                const manualIndicator = document.createElement('div');
                manualIndicator.className = 'manual-edit-indicator absolute right-14 top-1/2 transform -translate-y-1/2 text-green-600 text-xs z-10';
                manualIndicator.innerHTML = '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>';
                manualIndicator.title = 'Manually edited';

                relativeContainer.appendChild(manualIndicator);
            }
        }

        function hideTrailStats() {
            document.getElementById('trail_stats').classList.add('hidden');
        }

        function updateDrawingButtons() {
            const drawBtn = document.getElementById('draw_trail_btn');
            const clearBtn = document.getElementById('clear_trail_btn');

            if (isDrawing) {
                drawBtn.textContent = 'Drawing... (double-click to finish)';
                drawBtn.disabled = true;
                drawBtn.classList.add('bg-orange-500');
                drawBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            } else {
                drawBtn.textContent = 'Draw Trail Manually';
                drawBtn.disabled = false;
                drawBtn.classList.remove('bg-orange-500');
                drawBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }

            clearBtn.disabled = currentTrailCoords.length === 0 && drawingPath.length === 0;
        }

        function showStatus(message, type = 'info') {
            const providerDiv = document.getElementById('preview_provider');
            const colors = {
                'green': 'text-green-600',
                'red': 'text-red-600',
                'blue': 'text-blue-600',
                'yellow': 'text-yellow-600',
                'gray': 'text-gray-600'
            };

            providerDiv.className = `mt-2 text-sm ${colors[type] || 'text-gray-600'}`;
            providerDiv.textContent = message;
        }

        function calculateDistance(lat1, lng1, lat2, lng2) {
            const R = 6371000; // Earth's radius in meters
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // Initialize drawing map when step 2 is shown
        function ensureDrawingMapInitialized() {
            if (!drawingMap) {
                initializeDrawingMap();
            }
        }

        // Text search fallback functions
        function handleTextSearchFallback(query) {
            console.log('Attempting text search fallback for:', query);

            // Create a simple geocoding request using Google Maps Geocoding API
            if (typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                const geocoder = new google.maps.Geocoder();

                geocoder.geocode({
                    address: query + ', Philippines',
                    componentRestrictions: {
                        country: 'PH'
                    }
                }, function(results, status) {
                    if (status === 'OK' && results.length > 0) {
                        const result = results[0];
                        console.log('Geocoding fallback successful:', result);

                        // Convert geocoding result to place-like object
                        const fallbackPlace = {
                            place_id: result.place_id || 'geocoding_' + Date.now(),
                            formatted_address: result.formatted_address,
                            geometry: result.geometry,
                            name: query,
                            types: result.types || ['geocode']
                        };

                        handleGooglePlaceSelection(fallbackPlace);
                    } else {
                        console.log('Geocoding fallback failed:', status);
                        showTextSearchSuggestion(query);
                    }
                });
            } else {
                showTextSearchSuggestion(query);
            }
        }

        function showTextSearchSuggestion(query) {
            // Show a suggestion to use manual coordinates
            const searchInput = document.getElementById('location_search');
            const parent = searchInput.parentNode;

            // Remove existing suggestion
            const existingSuggestion = parent.querySelector('.search-suggestion');
            if (existingSuggestion) {
                existingSuggestion.remove();
            }

            // Auto-remove after 10 seconds
            setTimeout(() => {
                if (suggestion.parentNode) {
                    suggestion.remove();
                }
            }, 10000);
        }

        function openManualCoordinatesForSearch(query) {
            // Pre-fill location name and open manual coordinates
            document.getElementById('manual_location_name').value = query;
            toggleManualCoordinates();

            // Remove the suggestion
            const suggestion = document.querySelector('.search-suggestion');
            if (suggestion) {
                suggestion.remove();
            }
        }

        // Manual coordinate entry functions
        function toggleManualCoordinates() {
            const manualDiv = document.getElementById('manual_coordinates');
            if (manualDiv.classList.contains('hidden')) {
                manualDiv.classList.remove('hidden');
            } else {
                manualDiv.classList.add('hidden');
            }
        }

        function useManualCoordinates() {
            const latInput = document.getElementById('manual_lat');
            const lngInput = document.getElementById('manual_lng');
            const nameInput = document.getElementById('manual_location_name');

            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            const locationName = nameInput.value.trim();

            // Validate inputs
            if (isNaN(lat) || isNaN(lng)) {
                alert('Please enter valid latitude and longitude values.');
                return;
            }

            if (lat < 4.0 || lat > 21.5 || lng < 114.0 || lng > 127.0) {
                alert('Coordinates must be within the Philippines bounds.');
                return;
            }

            if (!locationName) {
                alert('Please enter a location name.');
                return;
            }

            // Create a fake place object similar to Google Places result
            const manualPlace = {
                place_id: 'manual_' + Date.now(),
                formatted_address: locationName + ', Philippines',
                geometry: {
                    location: {
                        lat: () => lat,
                        lng: () => lng
                    }
                },
                name: locationName,
                types: ['establishment']
            };

            // Process the manual location
            handleManualLocationSelection(manualPlace);

            // Hide manual input section
            toggleManualCoordinates();

            // Clear manual inputs
            latInput.value = '';
            lngInput.value = '';
            nameInput.value = '';
        }

        function handleManualLocationSelection(place) {
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
                name: place.name,
                is_manual: true
            };

            console.log('Processing manual location:', locationData);

            // Send to backend to create/find location
            fetch('/api/locations/google-places', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(locationData)
                })
                .then(response => response.json())
                .then(data => {
                    loadingDiv.classList.add('hidden');

                    if (data.success && data.location) {
                        // Update form fields
                        hiddenInput.value = data.location.id;
                        latInput.value = locationData.latitude;
                        lngInput.value = locationData.longitude;

                        // Update search input display
                        searchInput.value = data.location.name + (data.location.province ? ', ' + data.location.province : '');
                        searchInput.dataset.lastSelectedValue = searchInput.value;

                        // Add visual feedback
                        searchInput.classList.add('border-green-500', 'bg-green-50');
                        searchInput.classList.remove('border-gray-300');

                        // Add checkmark
                        addLocationCheckmark();

                        console.log('Manual location processed successfully:', data.location);

                        // Update the map if it's initialized
                        if (drawingMap) {
                            const coords = {
                                lat: locationData.latitude,
                                lng: locationData.longitude
                            };
                            drawingMap.setCenter(coords);
                            drawingMap.setZoom(13);

                            // Add a marker for the manual location
                            new google.maps.Marker({
                                position: coords,
                                map: drawingMap,
                                title: place.name,
                                icon: {
                                    url: 'data:image/svg+xml;base64,' + btoa('<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="red"><path d="M16 2C10.477 2 6 6.477 6 12c0 7.5 10 18 10 18s10-10.5 10-18c0-5.523-4.477-10-10-10zm0 14c-2.209 0-4-1.791-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4z"/></svg>'),
                                    scaledSize: new google.maps.Size(32, 32)
                                }
                            });
                        }

                    } else {
                        console.error('Failed to process manual location:', data.error || 'Unknown error');
                        clearLocationSelection();
                        alert('Failed to process the location. Please try again.');
                    }
                })
                .catch(error => {
                    loadingDiv.classList.add('hidden');
                    console.error('Error processing manual location:', error);
                    clearLocationSelection();
                    alert('Error processing location. Please check your connection and try again.');
                });
        }
    </script>

    <!-- GPX Library Modal -->
    <div id="gpxLibraryModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeGPXLibrary()"></div>

            <!-- Modal panel -->
            <div class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Select Trail from GPX Library</h3>
                    <button type="button" onclick="closeGPXLibrary()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mt-4">
                    <!-- Loading state -->
                    <div id="gpxLibraryLoading" class="text-center py-8">
                        <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-[#336d66] mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-2 text-gray-600">Loading GPX files...</p>
                    </div>

                    <!-- GPX Files List -->
                    <div id="gpxFilesList" class="hidden">
                        <div class="mb-4">
                            <input type="text" id="gpxSearchInput" placeholder="Search trails..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]">
                        </div>
                        
                        <div class="max-h-96 overflow-y-auto">
                            <div id="gpxFilesContainer" class="space-y-2">
                                <!-- GPX files will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Trail Selection -->
                    <div id="trailSelectionSection" class="hidden">
                        <div class="border-t pt-4 mt-4">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Select Trail from File:</h4>
                            <div id="trailsList" class="space-y-2 max-h-60 overflow-y-auto">
                                <!-- Trails will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Error state -->
                    <div id="gpxLibraryError" class="hidden text-center py-8">
                        <svg class="w-12 h-12 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <p class="text-gray-600">Failed to load GPX library. Please try again.</p>
                        <button onclick="loadGPXLibrary()" class="mt-2 text-[#336d66] hover:text-[#2a5a54] font-medium">Retry</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
    // Initialize Google Places Autocomplete for transportation details input
    (function initTransportAutocomplete(){
        function attachAutocomplete(){
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                // Wait and retry
                setTimeout(attachAutocomplete, 200);
                return;
            }

            const input = document.getElementById('transportation_details');
            if (!input) return;
            try {
                const autocomplete = new google.maps.places.Autocomplete(input, {types: ['establishment', 'geocode']});
                // Optionally bias to the map location if available
                if (window.itineraryMap && window.itineraryMap.map) {
                    const map = window.itineraryMap.map;
                    autocomplete.bindTo('bounds', map);
                }

                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (place && place.formatted_address) {
                        input.value = place.formatted_address;
                    }
                });
            } catch (e) {
                console.warn('Places autocomplete not initialized:', e);
            }
        }

        attachAutocomplete();
    })();
</script>
<script>
    (function(){
        function createRow(value = ''){
            const template = document.getElementById('side-trip-row-template');
            const row = template.cloneNode(true);
            row.id = '';
            row.classList.remove('hidden');
            const input = row.querySelector('.side-trip-input');
            input.value = value;
            // Clear dataset flags on cloned inputs so autocomplete can be attached
            try{ delete input.dataset._placesAttached; }catch(e){}
            input.dataset.place = '';
            const removeBtn = row.querySelector('.remove-side-trip');
            removeBtn.addEventListener('click', function(){
                row.remove();
            });
            // Ensure Places Autocomplete is attached for this new input
            try{
                if (typeof window.initSideTrips === 'function'){
                    // Global initializer will attach only to inputs not yet attached
                    try{ window.initSideTrips(); }catch(e){ console.warn('window.initSideTrips failed', e); }
                } else if (typeof initPlaceAutocomplete === 'function'){
                    try{ initPlaceAutocomplete(input); }catch(e){ console.warn('initPlaceAutocomplete failed for side-trip input', e); }
                } else if (typeof loadMapsScript === 'function'){
                    // loadMapsScript will call our callback after Maps loads
                    loadMapsScript(function(){
                        try{
                            if (typeof window.initSideTrips === 'function'){
                                window.initSideTrips();
                            } else if (typeof initPlaceAutocomplete === 'function'){
                                initPlaceAutocomplete(input);
                            } else if (window.google && google.maps && google.maps.places){
                                var ac = new google.maps.places.Autocomplete(input, {componentRestrictions: {country: 'ph'}});
                                ac.addListener('place_changed', function(){
                                    var place = ac.getPlace();
                                    var obj = {
                                        place_id: place.place_id || null,
                                        name: place.name || input.value || null,
                                        lat: place.geometry && place.geometry.location ? place.geometry.location.lat() : null,
                                        lng: place.geometry && place.geometry.location ? place.geometry.location.lng() : null,
                                        address_components: place.address_components || null,
                                    };
                                    input.dataset.place = JSON.stringify(obj);
                                });
                            }
                        }catch(e){ console.warn('side-trip autocomplete init failed', e); }
                    });
                }
            }catch(e){ console.warn('side-trip autocomplete attach error', e); }
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

            // initialize from server-side old input or empty (read from data-attributes to avoid Blade-in-script issues)
            try{
                let oldValues = [];
                try{
                    const container = document.getElementById('side-trips-list');
                    const rawOld = container.getAttribute('data-old');
                    if(rawOld){
                        const parsed = JSON.parse(rawOld);
                        if(Array.isArray(parsed)) oldValues = parsed;
                    }
                }catch(e){ oldValues = []; }

                if(Array.isArray(oldValues) && oldValues.length){
                    oldValues.forEach(v => addSideTrip(v));
                } else {
                    // if no old values, add a single empty row so users can start typing
                    addSideTrip('');
                }
            }catch(e){
                // fallback: add one empty row
                addSideTrip('');
            }

            // Ensure any rows added programmatically get autocomplete attached.
            // Sometimes other DOMContentLoaded handlers run after this and the Maps loader isn't ready yet;
            // call initAll after a short delay and attempt a direct attach for safety.
            setTimeout(function(){
                try{
                    if (typeof initAll === 'function') initAll();
                    else if (typeof window.initSideTrips === 'function') window.initSideTrips();
                }catch(ignore){}
                try{
                    document.querySelectorAll('.side-trip-input').forEach(function(inp){
                        try{ delete inp.dataset._placesAttached; }catch(e){}
                        try{ attachToInput(inp); }catch(e){}
                    });
                }catch(ignore){}
            }, 120);
        });
        // Duration parsing helper
        function parseDurationInput(raw){
            if(!raw || !raw.toString) return null;
            const s = raw.toString().trim().toLowerCase();

            // Patterns: numbers + units
            // examples: "36hours", "36 hours", "36h", "2 days", "2d", "1 night", "48"
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

            // Mixed e.g. "2 days 1 night" or "2d1n"
            const combined = s.match(/(?:(\d+)\s*d(?:ays?)?)?\s*(?:(\d+)\s*n(?:ights?)?)?/i);
            if(combined && (combined[1] || combined[2])){
                return { days: combined[1] ? parseInt(combined[1],10) : 0, nights: combined[2] ? parseInt(combined[2],10) : 0 };
            }

            return null;
        }

        function normalizeDuration(value){
            const parsed = parseDurationInput(value);
            if(!parsed) return null;

            // If hours provided, convert to days/nights heuristically: 24 hours -> 1 day, nights = Math.floor((hours-1)/24)
            if(parsed.hours !== undefined){
                const hours = parsed.hours;
                let days = 0;
                let nights = 0;
                if(hours >= 24){
                    // round up days for partial days
                    days = Math.ceil(hours / 24);
                    // If hours is an exact multiple of 24 (e.g. 48 -> 2 days),
                    // infer nights equal to days (organizer likely means whole days with nights per day).
                    // Otherwise (partial-day rounding) infer nights as days - 1 (typical travel logic).
                    if (Math.abs(hours % 24) < 1e-9) {
                        nights = days; // exact multiples: nights match days
                    } else {
                        nights = Math.max(0, days - 1);
                    }
                }
                return { hours, days, nights };
            }

            // If days provided, nights default to days - 1 (typical travel logic: 2 days = 1 night)
            if(parsed.days !== undefined){
                const days = parsed.days;
                const nights = Math.max(0, Math.floor(days) - 1);
                return { days, nights, hours: days * 24 };
            }

            if(parsed.nights !== undefined){
                const nights = parsed.nights;
                const days = nights + 1; // infer days
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
                // Do NOT modify the hidden `estimated_time` here. Leave it at the server-provided
                // default so the hiker-facing side does not receive a value derived from this
                // free-text `duration` field.
                return;
            }

            // Build human summary
            const parts = [];
            if(norm.days !== undefined && norm.days > 0) parts.push(norm.days + ' day' + (norm.days>1?'s':''));
            if(norm.nights !== undefined && norm.nights > 0) parts.push(norm.nights + ' night' + (norm.nights>1?'s':''));
            if(parts.length === 0 && norm.hours !== undefined) parts.push(norm.hours + ' hour' + (norm.hours>1?'s':''));

            summaryText.textContent = parts.join(', ');
            // Previously we saved an integer hour estimate into the hidden `estimated_time`.
            // That caused the hiker-facing record to inherit this value from the organizer's
            // duration free-text. We intentionally do NOT set `hidden.value` here to avoid
            // copying the `duration` into `estimated_time`.
        }

        // Attach listener
        document.addEventListener('DOMContentLoaded', function(){
            const dur = document.getElementById('duration');
            if(dur){
                dur.addEventListener('input', updateDurationSummary);
                // initialize summary if there is old input
                updateDurationSummary();
            }
        });
        // Best season month selects syncing
        document.addEventListener('DOMContentLoaded', function(){
            const from = document.getElementById('best_season_from');
            const to = document.getElementById('best_season_to');
            const hidden = document.getElementById('best_season');
            function syncBestSeason(){
                if(!hidden) return;
                const vFrom = from ? from.value : '';
                const vTo = to ? to.value : '';
                hidden.value = vFrom && vTo ? `${vFrom} to ${vTo}` : (vFrom ? `${vFrom}` : (vTo ? `${vTo}` : ''));
            }
            if(from) from.addEventListener('change', syncBestSeason);
            if(to) to.addEventListener('change', syncBestSeason);

            // initialize selects from old input if available (server-side old will populate a string like "November to March")
            try{
                const container = document.getElementById('best-season-selects');
                const existing = container ? container.getAttribute('data-old') : '';
                if(existing && existing.indexOf('to') !== -1){
                    const parts = existing.split('to').map(s => s.trim());
                    if(parts[0] && from) from.value = parts[0];
                    if(parts[1] && to) to.value = parts[1];
                } else if(existing){
                    if(from) from.value = existing;
                }
                syncBestSeason();
            }catch(e){ syncBestSeason(); }
        });
    })();
</script>