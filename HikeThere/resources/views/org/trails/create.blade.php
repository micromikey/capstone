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
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Progress Steps -->
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button type="button" onclick="showStep(1)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-1-nav border-[#336d66] text-[#336d66]">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-[#336d66] text-white flex items-center justify-center text-sm font-medium mr-2">1</span>
                                Basic Info
                            </span>
                        </button>
                        <button type="button" onclick="showStep(2)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-2-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">2</span>
                                Trail Details
                            </span>
                        </button>
                        <button type="button" onclick="showStep(3)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-3-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">3</span>
                                Access & Safety
                            </span>
                        </button>
                        <button type="button" onclick="showStep(4)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-4-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">4</span>
                                Additional Info
                            </span>
                        </button>
                        <button type="button" onclick="showStep(5)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-5-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">5</span>
                                Trail Images
                            </span>
                        </button>
                    </nav>
                </div>

                <form method="POST" action="{{ route('org.trails.store') }}" class="p-6" id="trailForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <!-- Hidden field to store accepted trail geometry (array of {lat,lng,elevation}) -->
                    <input type="hidden" id="trail_coordinates" name="trail_coordinates" />
                    <!-- Hidden field for estimated_time since it was removed from the form but still expected by backend -->
                    <input type="hidden" id="estimated_time" name="estimated_time" value="" />

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
                                <x-label for="package_inclusions" value="Package Inclusions *" />
                                <textarea id="package_inclusions" name="package_inclusions" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Guide, Meals, Environmental Fee, Transportation" required></textarea>
                                <x-input-error for="package_inclusions" class="mt-2" />
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
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
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

                                <!-- Provider/Source info -->
                                <div id="preview_provider" class="mt-2 text-sm text-gray-600"></div>
                            </div>

                            <div>
                                <x-label for="duration" value="Duration *" />
                                <x-input id="duration" type="text" name="duration" class="mt-1 block w-full" placeholder="e.g., 3-4 hours" required />
                                <x-input-error for="duration" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="best_season" value="Best Season *" />
                                <x-input id="best_season" type="text" name="best_season" class="mt-1 block w-full" placeholder="e.g., November to March" required />
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
                                Next: Access & Safety
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Access & Safety -->
                    <div id="step-3" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 3: Access & Safety</h3>
                            <p class="text-gray-600 text-sm">Provide transportation details and safety information.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="departure_point" value="Departure Point *" />
                                <x-input id="departure_point" type="text" name="departure_point" class="mt-1 block w-full" placeholder="e.g., Cubao Terminal" required />
                                <x-input-error for="departure_point" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="transport_options" value="Transport Options *" />
                                <textarea id="transport_options" name="transport_options" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Bus to Tanay, Jeep to Jump-off" required></textarea>
                                <x-input-error for="transport_options" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="side_trips" value="Side Trips" />
                                <textarea id="side_trips" name="side_trips" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Tinipak River or enter N/A if none"></textarea>
                                <x-input-error for="side_trips" class="mt-2" />
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
                            <button type="button" onclick="prevStep(2)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep(4)" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                Next: Additional Info
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Additional Information -->
                    <div id="step-4" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 4: Additional Information</h3>
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

                            <div class="md:col-span-2">
                                <x-label for="description" value="Detailed Description" />
                                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Comprehensive description of the trail, highlights, and experience"></textarea>
                                <x-input-error for="description" class="mt-2" />
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
                                Next: Trail Images
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 5: Trail Images -->
                    <div id="step-5" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 5: Trail Images</h3>
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
                                    <!-- Additional Image Slots -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors"
                                        onclick="document.getElementById('additional_1').click()">
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <div class="text-xs text-gray-500">Add Photo</div>
                                        </div>
                                        <input type="file" id="additional_1" name="additional_images[]" class="hidden" accept="image/*" onchange="handleFileSelect(this, 'additional_1')">
                                        <div id="additional_1_preview" class="mt-2 hidden"></div>
                                    </div>

                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors"
                                        onclick="document.getElementById('additional_2').click()">
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <div class="text-xs text-gray-500">Add Photo</div>
                                        </div>
                                        <input type="file" id="additional_2" name="additional_images[]" class="hidden" accept="image/*" onchange="handleFileSelect(this, 'additional_2')">
                                        <div id="additional_2_preview" class="mt-2 hidden"></div>
                                    </div>

                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors"
                                        onclick="document.getElementById('additional_3').click()">
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <div class="text-xs text-gray-500">Add Photo</div>
                                        </div>
                                        <input type="file" id="additional_3" name="additional_images[]" class="hidden" accept="image/*" onchange="handleFileSelect(this, 'additional_3')">
                                        <div id="additional_3_preview" class="mt-2 hidden"></div>
                                    </div>

                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors"
                                        onclick="document.getElementById('additional_4').click()">
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <div class="text-xs text-gray-500">Add Photo</div>
                                        </div>
                                        <input type="file" id="additional_4" name="additional_images[]" class="hidden" accept="image/*" onchange="handleFileSelect(this, 'additional_4')">
                                        <div id="additional_4_preview" class="mt-2 hidden"></div>
                                    </div>

                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors"
                                        onclick="document.getElementById('additional_5').click()">
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
                            <button type="button" onclick="prevStep(4)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
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
        const totalSteps = 5;
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
                const originallyRequiredFields = ['mountain_name', 'trail_name', 'location_id', 'price', 'difficulty', 'package_inclusions', 'duration', 'best_season', 'terrain_notes', 'departure_point', 'transport_options', 'emergency_contacts', 'packing_list', 'health_fitness'];
                
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
            const locationData = {
                place_id: place.place_id,
                formatted_address: place.formatted_address,
                latitude: place.geometry.location.lat(),
                longitude: place.geometry.location.lng(),
                name: place.name || place.formatted_address.split(',')[0]
            };

            console.log('Processing Google Places location:', locationData);

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
                        searchInput.value = data.location.name + ', ' + data.location.province;
                        searchInput.dataset.lastSelectedValue = searchInput.value;

                        // Add visual feedback
                        searchInput.classList.add('border-green-500', 'bg-green-50');
                        searchInput.classList.remove('border-gray-300');

                        // Add checkmark
                        addLocationCheckmark();

                        console.log('Location processed successfully:', data.location);

                        // Update the map if it's initialized
                        if (drawingMap) {
                            drawingMap.setCenter(place.geometry.location);
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
                }, 100);
            }

            currentStep = step;
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
            // Reset all navigation
            document.querySelectorAll('.step-nav').forEach((nav, index) => {
                const stepNumber = index + 1;
                const circle = nav.querySelector('span span');
                const text = nav.querySelector('span');

                if (stepNumber <= activeStep) {
                    nav.classList.remove('border-transparent', 'text-gray-500');
                    nav.classList.add('border-[#336d66]', 'text-[#336d66]');
                    circle.classList.remove('bg-gray-300', 'text-gray-600');
                    circle.classList.add('bg-[#336d66]', 'text-white');
                } else {
                    nav.classList.remove('border-[#336d66]', 'text-[#336d66]');
                    nav.classList.add('border-transparent', 'text-gray-500');
                    circle.classList.remove('bg-[#336d66]', 'text-white');
                    circle.classList.add('bg-gray-300', 'text-gray-600');
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
            .then(response => response.json())
            .then(data => {
                document.getElementById('gpxLibraryLoading').classList.add('hidden');
                
                if (data.success) {
                    displayGPXFiles(data.files);
                    document.getElementById('gpxFilesList').classList.remove('hidden');
                } else {
                    document.getElementById('gpxLibraryError').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading GPX library:', error);
                document.getElementById('gpxLibraryLoading').classList.add('hidden');
                document.getElementById('gpxLibraryError').classList.remove('hidden');
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
        function autoRouteFromInputs() {
            const mountainName = document.getElementById('mountain_name').value.trim();
            const trailName = document.getElementById('trail_name').value.trim();
            const locationSelect = document.getElementById('location_id');
            const selectedLocation = locationSelect.options[locationSelect.selectedIndex];
            
            if (!mountainName) {
                alert('Please enter a mountain name first.');
                document.getElementById('mountain_name').focus();
                return;
            }

            showStatus('Searching for trail in GPX library...', 'blue');

            // Get location text safely
            let locationText = '';
            if (selectedLocation && selectedLocation.textContent) {
                locationText = selectedLocation.textContent;
            } else if (selectedLocation && selectedLocation.text) {
                locationText = selectedLocation.text;
            }
            
            console.log('Auto Route Debug:', {
                mountainName: mountainName,
                trailName: trailName,
                locationText: locationText,
                selectedLocation: selectedLocation
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
                    mountain_name: mountainName,
                    trail_name: trailName,
                    location: locationText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.trails && data.trails.length > 0) {
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

            // If exact match or high confidence match, auto-load the best one
            const bestMatch = foundTrails[0];
            
            if (bestMatch.match_score >= 100 || foundTrails.length === 1) {
                // Auto-load the best match
                loadTrailFromGPX(bestMatch, { filename: bestMatch.source_file });
                showStatus(`Auto-loaded trail: "${bestMatch.name}" (Score: ${bestMatch.match_score})`, 'green');
            } else {
                // Show selection dialog for multiple matches
                showTrailSelectionDialog(foundTrails, mountainName, trailName);
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