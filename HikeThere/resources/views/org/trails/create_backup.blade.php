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
                                Trail Route
                            </span>
                        </button>
                        <button type="button" onclick="showStep(3)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-3-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">3</span>
                                Trail Details
                            </span>
                        </button>
                        <button type="button" onclick="showStep(4)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-4-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">4</span>
                                Access & Safety
                            </span>
                        </button>
                        <button type="button" onclick="showStep(5)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-5-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">5</span>
                                Additional Info
                            </span>
                        </button>
                        <button type="button" onclick="showStep(6)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-6-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">6</span>
                                Trail Images
                            </span>
                        </button>
                    </nav>
                </div>

                <form method="POST" action="{{ route('org.trails.store') }}" class="p-6" id="trailForm" enctype="multipart/form-data">
                    @csrf
                    
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
                                    <input type="text" id="location_search" placeholder="Search for a location using Google Places..." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]">
                                    <input type="hidden" id="location_id" name="location_id" required>
                                    <input type="hidden" id="location_name" name="location_name">
                                    <input type="hidden" id="location_lat" name="location_lat">
                                    <input type="hidden" id="location_lng" name="location_lng">
                                    <div id="location_loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    <p>Start typing to search for locations using Google Places Autocomplete</p>
                                </div>
                                <x-input-error for="location_id" class="mt-2" />
                            </div>



                                    <div class="flex items-center">
                                        <div id="coordinate-loading" class="hidden">
                                            <svg class="animate-spin h-5 w-5 text-blue-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                        <div id="coordinate-success-icon" class="hidden mr-3 text-green-500">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div id="coordinate-error-icon" class="hidden mr-3 text-red-500">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 id="coordinate-title" class="font-medium text-gray-900">Trail Route Coordinates</h4>
                                            <p id="coordinate-message" class="text-sm text-gray-600">Coordinates will be automatically generated when you fill in the trail details above</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Coordinate details when generated -->
                                    <div id="coordinate-details" class="hidden mt-3 p-3 bg-white rounded border text-sm">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <span class="font-medium text-gray-700">Start Point:</span>
                                                <span id="start-point-text" class="text-gray-600 block"></span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-700">End Point:</span>
                                                <span id="end-point-text" class="text-gray-600 block"></span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-700">Route Points:</span>
                                                <span id="route-points-count" class="text-gray-600 block"></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action buttons -->
                                    <div class="mt-3 flex gap-2 flex-wrap">
                                        <button type="button" onclick="generateEnhancedCoordinatesFromForm()" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8A6 6 0 006 8c0 7-6 9-6 9s6-2 6-9zM14 8a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            <span id="enhanced-btn-text">Enhanced Google Maps Route</span>
                                        </button>
                                        <button type="button" onclick="generateCoordinatesFromForm()" class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8A6 6 0 006 8c0 7-6 9-6 9s6-2 6-9zM14 8a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            <span id="osm-btn-text">Generate Trail Route</span>
                                        </button>
                                        <button type="button" onclick="regenerateGoogleMapsCoordinates()" class="px-3 py-1 text-sm bg-orange-600 text-white rounded hover:bg-orange-700">
                                            <span id="regenerate-btn-text">Try Alternative Route</span>
                                        </button>
                                        <button type="button" onclick="toggleCustomRoute()" class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700">
                                            <span id="custom-route-btn-text">Use Custom Route</span>
                                        </button>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <span class="inline-flex items-center gap-1">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                            <strong>Enhanced:</strong> Multi-API Google Maps with elevation, POI, and detailed analysis
                                        </span>
                                        <span class="ml-3 inline-flex items-center gap-1">
                                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                            <strong>Standard:</strong> Known trail database with OpenStreetMap fallback
                                        </span>
                                        <span class="ml-3 inline-flex items-center gap-1">
                                            <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                            Alternative: Google Maps basic routing
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Custom route section -->
                                <div id="custom-route-section" class="hidden mt-3 p-4 bg-gray-50 rounded border">
                                    <h5 class="font-medium text-gray-900 mb-3">Custom Route Points</h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label for="custom_start_point" class="block text-sm font-medium text-gray-700">Start Point</label>
                                            <input type="text" id="custom_start_point" name="custom_start_point" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Mt. Arayat Trailhead, Arayat, Pampanga">
                                        </div>
                                        <div>
                                            <label for="custom_end_point" class="block text-sm font-medium text-gray-700">End Point</label>
                                            <input type="text" id="custom_end_point" name="custom_end_point" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Mt. Arayat Summit, Arayat, Pampanga">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="custom_waypoints" class="block text-sm font-medium text-gray-700">Waypoints (Optional)</label>
                                        <textarea id="custom_waypoints" name="custom_waypoints" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter waypoints separated by new lines (e.g., Camp 1, Mt. Arayat)"></textarea>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" onclick="generateCustomCoordinates()" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                            Generate Custom Route
                                        </button>
                                        <button type="button" onclick="cancelCustomRoute()" class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Hidden input to store generated coordinates -->
                                <input type="hidden" id="trail_coordinates" name="trail_coordinates" />
                                <input type="hidden" id="coordinate_generation_method" name="coordinate_generation_method" value="auto" />
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
                            <p class="text-gray-600 text-sm">Define the difficulty, duration, and terrain characteristics.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                            <!-- Auto-calculated measurements section -->
                            <div class="md:col-span-2 mb-6">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div id="measurements-loading" class="hidden">
                                            <svg class="animate-spin h-5 w-5 text-blue-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                        <div id="measurements-success-icon" class="hidden mr-3 text-green-500">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div id="measurements-info-icon" class="mr-3 text-blue-500">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">Trail Measurements</h4>
                                            <p id="measurements-message" class="text-sm text-gray-600">These measurements will be automatically calculated from your trail coordinates</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" onclick="recalculateMeasurements()" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700" id="recalculate-btn">
                                            Recalculate Measurements
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <x-label for="length" value="Trail Length (km)" />
                                <div class="relative mt-1">
                                    <x-input id="length" type="number" name="length" step="0.1" min="0" class="pr-12 block w-full bg-gray-50" placeholder="Auto-calculated" readonly />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">km</span>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">📏 Calculated from trail route distance</p>
                                <x-input-error for="length" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="elevation_gain" value="Elevation Gain (m)" />
                                <div class="relative mt-1">
                                    <x-input id="elevation_gain" type="number" name="elevation_gain" min="0" class="pr-12 block w-full bg-gray-50" placeholder="Auto-calculated" readonly />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">m</span>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">⛰️ Total upward elevation along the trail</p>
                                <x-input-error for="elevation_gain" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="elevation_high" value="Highest Point (m)" />
                                <div class="relative mt-1">
                                    <x-input id="elevation_high" type="number" name="elevation_high" min="0" class="pr-12 block w-full bg-gray-50" placeholder="Auto-calculated" readonly />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">m</span>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">🏔️ Highest elevation point on the trail</p>
                                <x-input-error for="elevation_high" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="elevation_low" value="Lowest Point (m)" />
                                <div class="relative mt-1">
                                    <x-input id="elevation_low" type="number" name="elevation_low" min="0" class="pr-12 block w-full bg-gray-50" placeholder="Auto-calculated" readonly />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">m</span>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">🏞️ Lowest elevation point on the trail</p>
                                <x-input-error for="elevation_low" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="estimated_time" value="Estimated Time (minutes)" />
                                <div class="relative mt-1">
                                    <x-input id="estimated_time" type="number" name="estimated_time" min="0" class="pr-20 block w-full" placeholder="180" />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">min</span>
                                    </div>
                                </div>
                                <x-input-error for="estimated_time" class="mt-2" />
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
                                            <path d="M24 8l-8 8h16l-8-8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 24h32M8 32h32" stroke-width="2" stroke-linecap="round"/>
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

    <!-- Google Maps JavaScript API (Legacy Places Autocomplete) -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&v=weekly"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 6;
        let locationSearchTimeout;
    let autocomplete; // legacy autocomplete

        // Add form submission debugging
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('trailForm');
            
            form.addEventListener('submit', function(e) {
                console.log('Form submission started');
                
                // Log form data
                const formData = new FormData(form);
                console.log('Form data:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }
                
                // Check if all required fields are filled
                const requiredFields = form.querySelectorAll('[required]');
                let missingFields = [];
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        missingFields.push(field.name);
                    }
                });
                
                // Special check for location_id
                const locationId = document.getElementById('location_id').value;
                const locationSearch = document.getElementById('location_search').value;
                
                if (!locationId && locationSearch) {
                    missingFields.push('location_id (please select a location from Google Places)');
                }
                
                if (missingFields.length > 0) {
                    console.error('Missing required fields:', missingFields);
                    e.preventDefault();
                    alert('Please fill in all required fields: ' + missingFields.join(', '));
                    return false;
                }
                
                console.log('Form validation passed, submitting...');
            });

            // Autocomplete now initialized via initPlaces() callback once Maps JS loads
        });

    document.addEventListener('DOMContentLoaded', () => { initializeLocationSearch(); });

        // Google Places Autocomplete functionality
        function initializeLocationSearch() {
            console.log('Initializing legacy Google Places Autocomplete...');
            
            const searchInput = document.getElementById('location_search');
            const hiddenInput = document.getElementById('location_id');
            const locationNameInput = document.getElementById('location_name');
            const locationLatInput = document.getElementById('location_lat');
            const locationLngInput = document.getElementById('location_lng');

            if (!searchInput || !hiddenInput) {
                console.error('Location search elements not found:', {
                    searchInput: !!searchInput,
                    hiddenInput: !!hiddenInput
                });
                return;
            }

            console.log('Location search elements found, setting up legacy widget...');
            try {
                autocomplete = new google.maps.places.Autocomplete(searchInput, {
                    types: ['geocode','establishment'],
                    componentRestrictions: { country: 'PH' },
                    fields: ['place_id','formatted_address','geometry','name']
                });
            } catch(err) {
                console.error('Legacy Autocomplete init failed:', err);
            }

            if (autocomplete) {
                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (!place || !place.geometry) {
                        console.warn('No geometry for selected place');
                        return;
                    }
                    handleLegacyPlaceSelection(place, searchInput, hiddenInput, locationNameInput, locationLatInput, locationLngInput);
                });
            }

            searchInput.addEventListener('input', () => { if (hiddenInput.value) clearLocationSelection(); });

            console.log('Legacy Google Places Autocomplete initialization complete');
        }
        function handleLegacyPlaceSelection(place, searchInput, hiddenInput, locationNameInput, locationLatInput, locationLngInput) {
            const loadingDiv = document.getElementById('location_loading');
            loadingDiv.classList.remove('hidden');
            fetch('/api/locations/google-places', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    place_id: place.place_id,
                    formatted_address: place.formatted_address,
                    latitude: place.geometry.location.lat(),
                    longitude: place.geometry.location.lng(),
                    name: place.name
                })
            })
            .then(r => r.json())
            .then(data => {
                loadingDiv.classList.add('hidden');
                if (data.success) {
                    locationNameInput.value = place.formatted_address || place.name;
                    locationLatInput.value = place.geometry.location.lat();
                    locationLngInput.value = place.geometry.location.lng();
                    hiddenInput.value = data.location.id;
                    searchInput.classList.add('border-green-500','bg-green-50');
                    searchInput.classList.remove('border-gray-300');
                    if (!searchInput.parentNode.querySelector('.location-selected-checkmark')) {
                        const checkmark = document.createElement('div');
                        checkmark.className='location-selected-checkmark absolute right-10 top-1/2 transform -translate-y-1/2 text-green-500';
                        checkmark.innerHTML='<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>';
                        searchInput.parentNode.appendChild(checkmark);
                    }
                    checkAutoCoordinateGeneration();
                } else {
                    alert('Failed to process location. Please try again.');
                }
            })
            .catch(err => {
                loadingDiv.classList.add('hidden');
                console.error('Error processing location:', err);
            });
        }

        function clearLocationSelection() {
            const searchInput = document.getElementById('location_search');
            const hiddenInput = document.getElementById('location_id');
            const locationNameInput = document.getElementById('location_name');
            const locationLatInput = document.getElementById('location_lat');
            const locationLngInput = document.getElementById('location_lng');
            
            hiddenInput.value = '';
            locationNameInput.value = '';
            locationLatInput.value = '';
            locationLngInput.value = '';
            searchInput.classList.remove('border-green-500', 'bg-green-50');
            searchInput.classList.add('border-gray-300');
            
            // Remove checkmark
            const checkmark = searchInput.parentNode.querySelector('.location-selected-checkmark');
            if (checkmark) {
                checkmark.remove();
            }
        }

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show selected step
            document.getElementById(`step-${step}`).classList.remove('hidden');
            
            // Update navigation styling
            updateNavigation(step);
            
            currentStep = step;
            
            // Special handling for step 3 (Trail Details) - check if measurements need calculation
            if (step === 3) {
                checkAndCalculateMeasurements();
            }
        }

        function nextStep(step) {
            // Validate current step before proceeding
            if (!validateStep(currentStep)) {
                return; // Don't proceed if validation fails
            }
            
            if (step <= totalSteps) {
                showStep(step);
            }
        }

        function prevStep(step) {
            if (step >= 1) {
                showStep(step);
            }
        }

        // Step validation function
        function validateStep(stepNumber) {
            let isValid = true;
            let missingFields = [];

            switch (stepNumber) {
                case 1: // Basic Information
                    const mountainName = document.getElementById('mountain_name').value.trim();
                    const trailName = document.getElementById('trail_name').value.trim();
                    const locationId = document.getElementById('location_id').value;
                    const price = document.getElementById('price').value.trim();
                    const packageInclusions = document.getElementById('package_inclusions').value.trim();
                    const difficulty = document.getElementById('difficulty').value;

                    if (!mountainName) missingFields.push('Mountain Name');
                    if (!trailName) missingFields.push('Trail Name');
                    if (!locationId) missingFields.push('Location (please select from Google Places)');
                    if (!price) missingFields.push('Price');
                    if (!packageInclusions) missingFields.push('Package Inclusions');
                    if (!difficulty) missingFields.push('Difficulty Level');
                    
                    break;

                case 2: // Trail Route (coordinates should be generated)
                    const coordinates = document.getElementById('trail_coordinates').value;
                    if (!coordinates) {
                        missingFields.push('Trail Route Coordinates (please wait for auto-generation or use custom route)');
                    }
                    break;

                case 3: // Trail Details
                    const duration = document.getElementById('duration').value.trim();
                    const bestSeason = document.getElementById('best_season').value.trim();
                    const terrainNotes = document.getElementById('terrain_notes').value.trim();

                    if (!duration) missingFields.push('Duration');
                    if (!bestSeason) missingFields.push('Best Season');
                    if (!terrainNotes) missingFields.push('Terrain Notes');
                    break;

                case 4: // Access & Safety
                    const departurePoint = document.getElementById('departure_point').value.trim();
                    const transportOptions = document.getElementById('transport_options').value.trim();
                    const packingList = document.getElementById('packing_list').value.trim();
                    const healthFitness = document.getElementById('health_fitness').value.trim();
                    const emergencyContacts = document.getElementById('emergency_contacts').value.trim();

                    if (!departurePoint) missingFields.push('Departure Point');
                    if (!transportOptions) missingFields.push('Transport Options');
                    if (!packingList) missingFields.push('Packing List');
                    if (!healthFitness) missingFields.push('Health & Fitness Requirements');
                    if (!emergencyContacts) missingFields.push('Emergency Contacts');
                    break;

                case 5: // Additional Info (mostly optional, but check for at least some content)
                    // This step is mostly optional, so we can be lenient
                    break;

                case 6: // Trail Images (optional but recommended)
                    // Images are optional, so no strict validation needed
                    break;
            }

            if (missingFields.length > 0) {
                isValid = false;
                showValidationError(stepNumber, missingFields);
            } else {
                hideValidationError(stepNumber);
            }

            return isValid;
        }

        function showValidationError(stepNumber, missingFields) {
            // Remove any existing error
            hideValidationError(stepNumber);

            // Create error message
            const errorDiv = document.createElement('div');
            errorDiv.id = `step-${stepNumber}-validation-error`;
            errorDiv.className = 'mt-4 p-4 bg-red-50 border border-red-200 rounded-md';
            errorDiv.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Please complete the following required fields:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                ${missingFields.map(field => `<li>${field}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                </div>
            `;

            // Insert error at the end of current step
            const currentStepDiv = document.getElementById(`step-${stepNumber}`);
            currentStepDiv.appendChild(errorDiv);

            // Scroll to error
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function hideValidationError(stepNumber) {
            const existingError = document.getElementById(`step-${stepNumber}-validation-error`);
            if (existingError) {
                existingError.remove();
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
            showStep(1);
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
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }

        // Form validation before submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('trailForm');
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

        // Auto-coordinate generation functions
        let coordinateGenerationTimeout;

        function checkAutoCoordinateGeneration() {
            // Clear any existing timeout
            clearTimeout(coordinateGenerationTimeout);
            
            // Wait a bit to avoid too many rapid requests
            coordinateGenerationTimeout = setTimeout(() => {
                const mountainName = document.getElementById('mountain_name').value.trim();
                const trailName = document.getElementById('trail_name').value.trim();
                const locationName = document.getElementById('location_name').value.trim();
                const locationLat = document.getElementById('location_lat').value;
                const locationLng = document.getElementById('location_lng').value;
                
                // Show coordinate status panel if we have some data
                if (mountainName || trailName || locationName) {
                    document.getElementById('coordinate-status').classList.remove('hidden');
                }
                
                // Only generate if we have all required fields
                if (mountainName && trailName && locationName && locationLat && locationLng) {
                    generateAutoCoordinates(mountainName, trailName, locationName, locationLat, locationLng);
                } else {
                    updateCoordinateStatus('waiting', 'Please fill in all trail details above to auto-generate coordinates');
                }
            }, 1000); // Wait 1 second after user stops typing
        }

        function generateAutoCoordinates(mountainName, trailName, locationName, lat, lng) {
            updateCoordinateStatus('loading', 'Generating trail route coordinates...');
            
            // Try Enhanced Google Maps first, then fallback to OpenStreetMap
            generateEnhancedCoordinatesFromForm();
        }

        function generateEnhancedCoordinatesFromForm() {
            const mountainName = document.getElementById('mountain_name').value.trim();
            const trailName = document.getElementById('trail_name').value.trim();
            const locationName = document.getElementById('location_name').value.trim();
            const locationLat = document.getElementById('location_lat').value;
            const locationLng = document.getElementById('location_lng').value;
            
            if (!mountainName || !trailName || !locationName) {
                alert('Please fill in Mountain Name, Trail Name, and Location before generating enhanced coordinates.');
                return;
            }
            
            updateCoordinateStatus('loading', 'Generating enhanced coordinates using Google Maps APIs...');
            
            fetch('{{ route("org.trails.generate-enhanced-coordinates") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    mountain_name: mountainName,
                    trail_name: trailName,
                    location_name: locationName,
                    location_lat: locationLat,
                    location_lng: locationLng
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.coordinates) {
                    // Store coordinates in hidden input
                    document.getElementById('trail_coordinates').value = JSON.stringify(data.coordinates);
                    document.getElementById('coordinate_generation_method').value = 'enhanced_google_maps';
                    
                    // Update measurements with enhanced data
                    if (data.trail_info && data.trail_info.estimated_length_km) {
                        updateMeasurementFields(
                            data.trail_info.estimated_length_km,
                            data.trail_info.elevation_data ? calculateElevationGainFromData(data.trail_info.elevation_data) : null,
                            data.trail_info.elevation_data ? Math.max(...data.trail_info.elevation_data.map(e => e.elevation)) : null,
                            data.trail_info.elevation_data ? Math.min(...data.trail_info.elevation_data.map(e => e.elevation)) : null
                        );
                        
                        // Update estimated time if available
                        if (data.trail_info.estimated_duration_minutes) {
                            document.getElementById('estimated_time').value = data.trail_info.estimated_duration_minutes;
                        }
                    } else {
                        calculateTrailMeasurements(data.coordinates);
                    }
                    
                    // Update UI with enhanced information
                    const message = `Enhanced Google Maps route generated with ${data.coordinates.length} points, elevation data, and POI analysis`;
                    updateCoordinateStatus('success', message);
                    updateEnhancedCoordinateDetails(data);
                    
                    // Show trail difficulty analysis if available
                    if (data.trail_info && data.trail_info.difficulty_indicators) {
                        showDifficultyAnalysis(data.trail_info.difficulty_indicators);
                    }
                    
                    console.log('Enhanced Google Maps coordinates:', {
                        coordinates: data.coordinates,
                        trail_info: data.trail_info,
                        data_source: data.data_source
                    });
                } else {
                    // Fallback to standard generation
                    console.log('Enhanced generation failed, falling back to standard method');
                    updateCoordinateStatus('loading', 'Enhanced method failed, trying standard route generation...');
                    generateCoordinatesFromForm();
                }
            })
            .catch(error => {
                console.error('Error generating enhanced coordinates:', error);
                // Fallback to standard generation
                updateCoordinateStatus('loading', 'Enhanced method error, trying standard route generation...');
                generateCoordinatesFromForm();
            });
        }

        function generateGoogleMapsCoordinates(mountainName, trailName, locationName, lat, lng) {
            updateCoordinateStatus('loading', 'Generating route using Google Maps...');
            
            // Create search queries for Google Directions API
            const startQuery = `${trailName} trailhead, ${mountainName}, ${locationName}`;
            const endQuery = `${mountainName} summit, ${locationName}`;
            
            // Make request to our backend to generate coordinates
            fetch('{{ route('org.trails.generate-google-coordinates') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    start_point: startQuery,
                    end_point: endQuery,
                    location_lat: lat,
                    location_lng: lng,
                    mountain_name: mountainName,
                    trail_name: trailName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.coordinates) {
                    // Store coordinates in hidden input
                    document.getElementById('trail_coordinates').value = JSON.stringify(data.coordinates);
                    document.getElementById('coordinate_generation_method').value = 'google_maps';
                    
                    // Calculate and update measurements (use enhanced data if available)
                    if (data.estimated_length_km) {
                        calculateTrailMeasurementsWithKnownLength(data.coordinates, data.estimated_length_km);
                    } else {
                        calculateTrailMeasurements(data.coordinates);
                    }
                    
                    // Update UI
                    const message = data.trail_type === 'estimated' 
                        ? `Google Maps route generated with ${data.coordinates.length} points`
                        : `Google Maps route with known trail data: ${data.coordinates.length} points`;
                    updateCoordinateStatus('success', message);
                    updateCoordinateDetails(data);
                    
                    console.log('Google Maps coordinates:', data.coordinates);
                    if (data.estimated_length_km) {
                        console.log('Enhanced trail data:', {
                            length: data.estimated_length_km + ' km',
                            type: data.trail_type
                        });
                    }
                } else {
                    updateCoordinateStatus('error', data.message || 'Failed to generate coordinates using Google Maps');
                }
            })
            .catch(error => {
                console.error('Error generating Google Maps coordinates:', error);
                updateCoordinateStatus('error', 'Error connecting to Google Maps coordinate service');
            });
        }

        function regenerateAutoCoordinates() {
            const mountainName = document.getElementById('mountain_name').value.trim();
            const trailName = document.getElementById('trail_name').value.trim();
            const locationName = document.getElementById('location_name').value.trim();
            const locationLat = document.getElementById('location_lat').value;
            const locationLng = document.getElementById('location_lng').value;
            
            if (mountainName && trailName && locationName && locationLat && locationLng) {
                generateAutoCoordinates(mountainName, trailName, locationName, locationLat, locationLng);
            } else {
                alert('Please fill in all trail details (Mountain Name, Trail Name, and Location) before generating coordinates.');
            }
        }

        function regenerateGoogleMapsCoordinates() {
            const mountainName = document.getElementById('mountain_name').value.trim();
            const trailName = document.getElementById('trail_name').value.trim();
            const locationName = document.getElementById('location_name').value.trim();
            const locationLat = document.getElementById('location_lat').value;
            const locationLng = document.getElementById('location_lng').value;
            
            if (mountainName && trailName && locationName && locationLat && locationLng) {
                generateGoogleMapsCoordinates(mountainName, trailName, locationName, locationLat, locationLng);
            } else {
                alert('Please fill in all trail details (Mountain Name, Trail Name, and Location) before generating coordinates.');
            }
        }

        function toggleCustomRoute() {
            const customSection = document.getElementById('custom-route-section');
            const button = document.querySelector('[onclick="toggleCustomRoute()"]');
            
            if (customSection.classList.contains('hidden')) {
                customSection.classList.remove('hidden');
                button.querySelector('#custom-route-btn-text').textContent = 'Use Auto Route';
                document.getElementById('coordinate_generation_method').value = 'custom';
            } else {
                customSection.classList.add('hidden');
                button.querySelector('#custom-route-btn-text').textContent = 'Use Custom Route';
                document.getElementById('coordinate_generation_method').value = 'auto';
            }
        }

        function cancelCustomRoute() {
            document.getElementById('custom-route-section').classList.add('hidden');
            document.querySelector('[onclick="toggleCustomRoute()"] #custom-route-btn-text').textContent = 'Use Custom Route';
            document.getElementById('coordinate_generation_method').value = 'auto';
            
            // Clear custom inputs
            document.getElementById('custom_start_point').value = '';
            document.getElementById('custom_end_point').value = '';
            document.getElementById('custom_waypoints').value = '';
        }

        function generateCustomCoordinates() {
            const startPoint = document.getElementById('custom_start_point').value.trim();
            const endPoint = document.getElementById('custom_end_point').value.trim();
            const waypoints = document.getElementById('custom_waypoints').value.trim();
            
            if (!startPoint || !endPoint) {
                alert('Please provide both start and end points for custom route generation.');
                return;
            }
            
            updateCoordinateStatus('loading', 'Generating custom trail route...');
            
            fetch('{{ route('org.trails.generate-custom-coordinates') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    start_point: startPoint,
                    end_point: endPoint,
                    waypoints: waypoints.split('\n').filter(w => w.trim())
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.coordinates) {
                    // Store coordinates in hidden input
                    document.getElementById('trail_coordinates').value = JSON.stringify(data.coordinates);
                    document.getElementById('coordinate_generation_method').value = 'custom';
                    
                    // Calculate and update measurements
                    calculateTrailMeasurements(data.coordinates);
                    
                    // Update UI
                    updateCoordinateStatus('success', `Custom route generated successfully with ${data.coordinates.length} points`);
                    updateCoordinateDetails(data);
                    
                    console.log('Custom-generated coordinates:', data.coordinates);
                } else {
                    updateCoordinateStatus('error', data.message || 'Failed to generate custom coordinates');
                }
            })
            .catch(error => {
                console.error('Error generating custom coordinates:', error);
                updateCoordinateStatus('error', 'Error connecting to coordinate generation service');
            });
        }

        function generateCoordinatesFromForm() {
            const mountainName = document.getElementById('mountain_name').value.trim();
            const trailName = document.getElementById('trail_name').value.trim();
            const locationName = document.getElementById('location_name').value.trim();
            const locationLat = document.getElementById('location_lat').value;
            const locationLng = document.getElementById('location_lng').value;
            
            if (!mountainName || !trailName || !locationName) {
                alert('Please fill in Mountain Name, Trail Name, and Location before generating coordinates.');
                return;
            }
            
            updateCoordinateStatus('loading', 'Generating coordinates...');
            
            fetch('{{ route('org.trails.generate-coordinates') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    trail_name: trailName,
                    mountain_name: mountainName,
                    start_location: locationName,
                    end_location: locationName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.coordinates) {
                    // Store coordinates in hidden input
                    document.getElementById('trail_coordinates').value = JSON.stringify(data.data.coordinates);
                    document.getElementById('coordinate_generation_method').value = 'google_maps';
                    
                    // Update measurements with accurate data
                    if (data.data.distance_km) {
                        document.getElementById('distance_km').value = data.data.distance_km;
                        updateMeasurementFields({
                            distance_km: data.data.distance_km,
                            max_elevation: data.data.max_elevation
                        }, data.source.includes('enhanced') || data.source.includes('verified'), data.data.distance_km);
                    } else {
                        calculateTrailMeasurements(data.data.coordinates);
                    }
                    
                    // Update UI with detailed information
                    const sourceInfo = {
                        'openstreetmap': 'OpenStreetMap trail data',
                        'openstreetmap_enhanced': 'OpenStreetMap + verified trail data',
                        'known_trail_enhanced': 'Verified trail database',
                        'google_maps': 'Google Maps (fallback)'
                    };
                    
                    const message = `${sourceInfo[data.source] || 'Trail coordinates'} loaded with ${data.coordinate_count} points`;
                    updateCoordinateStatus('success', message);
                    updateCoordinateDetails(data.data);
                    
                    console.log('OpenStreetMap coordinates:', {
                        coordinates: data.data.coordinates,
                        source: data.source,
                        accuracy: data.accuracy || data.data.accuracy,
                        distance: data.data.distance_km + ' km'
                    });
                } else {
                    // Try fallback to Google Maps
                    console.log('OpenStreetMap failed, falling back to Google Maps');
                    updateCoordinateStatus('loading', 'OpenStreetMap data not found, trying Google Maps...');
                    
                    const mountainName = document.getElementById('mountain_name').value.trim();
                    const trailName = document.getElementById('trail_name').value.trim();
                    const locationName = document.getElementById('location_name').value.trim();
                    const locationLat = document.getElementById('location_lat').value;
                    const locationLng = document.getElementById('location_lng').value;
                    
                    generateGoogleMapsCoordinates(mountainName, trailName, locationName, locationLat, locationLng);
                }
            })
            .catch(error => {
                console.error('Error with OpenStreetMap generation:', error);
                // Fallback to Google Maps
                console.log('Error occurred, falling back to Google Maps');
                updateCoordinateStatus('loading', 'Trying Google Maps alternative...');
                
                const mountainName = document.getElementById('mountain_name').value.trim();
                const trailName = document.getElementById('trail_name').value.trim();
                const locationName = document.getElementById('location_name').value.trim();
                const locationLat = document.getElementById('location_lat').value;
                const locationLng = document.getElementById('location_lng').value;
                
                generateGoogleMapsCoordinates(mountainName, trailName, locationName, locationLat, locationLng);
            });
        }

        function updateCoordinateStatus(type, message) {
            const statusPanel = document.getElementById('coordinate-status');
            const loadingIcon = document.getElementById('coordinate-loading');
            const successIcon = document.getElementById('coordinate-success-icon');
            const errorIcon = document.getElementById('coordinate-error-icon');
            const messageEl = document.getElementById('coordinate-message');
            const regenerateBtn = document.getElementById('regenerate-btn-text');
            
            // Hide all icons first
            loadingIcon.classList.add('hidden');
            successIcon.classList.add('hidden');
            errorIcon.classList.add('hidden');
            
            // Reset panel styling
            statusPanel.className = 'p-4 rounded-lg border';
            
            switch (type) {
                case 'loading':
                    statusPanel.classList.add('bg-blue-50', 'border-blue-200');
                    loadingIcon.classList.remove('hidden');
                    regenerateBtn.textContent = 'Generating...';
                    break;
                case 'success':
                    statusPanel.classList.add('bg-green-50', 'border-green-200');
                    successIcon.classList.remove('hidden');
                    regenerateBtn.textContent = 'Regenerate Route';
                    break;
                case 'error':
                    statusPanel.classList.add('bg-red-50', 'border-red-200');
                    errorIcon.classList.remove('hidden');
                    regenerateBtn.textContent = 'Try Again';
                    break;
                default:
                    statusPanel.classList.add('bg-blue-50', 'border-blue-200');
                    regenerateBtn.textContent = 'Generate Route';
            }
            
            messageEl.textContent = message;
            statusPanel.classList.remove('hidden');
        }

        function updateCoordinateDetails(data) {
            const detailsDiv = document.getElementById('coordinate-details');
            const startPointText = document.getElementById('start-point-text');
            const endPointText = document.getElementById('end-point-text');
            const routePointsCount = document.getElementById('route-points-count');
            
            if (data.start_address && data.end_address) {
                startPointText.textContent = data.start_address;
                endPointText.textContent = data.end_address;
                routePointsCount.textContent = `${data.coordinates.length} points`;
                detailsDiv.classList.remove('hidden');
            } else {
                detailsDiv.classList.add('hidden');
            }
        }

        function updateEnhancedCoordinateDetails(data) {
            const detailsDiv = document.getElementById('coordinate-details');
            const startPointText = document.getElementById('start-point-text');
            const endPointText = document.getElementById('end-point-text');
            const routePointsCount = document.getElementById('route-points-count');
            
            // Update basic details
            if (data.trailhead_info) {
                startPointText.textContent = data.trailhead_info.name || 'Trailhead found';
            } else {
                startPointText.textContent = 'Generated start point';
            }
            
            if (data.summit_info) {
                endPointText.textContent = data.summit_info.name || 'Summit found';
            } else {
                endPointText.textContent = 'Generated end point';
            }
            
            routePointsCount.textContent = `${data.coordinates.length} points with elevation data`;
            
            // Add enhanced details
            if (!document.getElementById('enhanced-details')) {
                const enhancedDetails = document.createElement('div');
                enhancedDetails.id = 'enhanced-details';
                enhancedDetails.className = 'mt-3 p-3 bg-blue-50 rounded border text-sm';
                enhancedDetails.innerHTML = `
                    <h5 class="font-medium text-gray-800 mb-2">Enhanced Trail Analysis</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <span class="font-medium text-gray-700">Distance:</span>
                            <span id="enhanced-distance" class="text-blue-600 block">${data.trail_info?.estimated_length_km ? (data.trail_info.estimated_length_km + ' km') : 'Calculating...'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Est. Duration:</span>
                            <span id="enhanced-duration" class="text-blue-600 block">${data.trail_info?.estimated_duration_minutes ? Math.round(data.trail_info.estimated_duration_minutes / 60 * 10) / 10 + ' hours' : 'Calculating...'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Data Source:</span>
                            <span id="enhanced-source" class="text-green-600 block">${data.data_source || 'Enhanced Google Maps'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">POI Found:</span>
                            <span id="enhanced-poi" class="text-purple-600 block">${data.points_of_interest?.length || 0} points of interest</span>
                        </div>
                    </div>
                `;
                detailsDiv.appendChild(enhancedDetails);
            } else {
                // Update existing enhanced details
                document.getElementById('enhanced-distance').textContent = data.trail_info?.estimated_length_km ? (data.trail_info.estimated_length_km + ' km') : 'Calculating...';
                document.getElementById('enhanced-duration').textContent = data.trail_info?.estimated_duration_minutes ? Math.round(data.trail_info.estimated_duration_minutes / 60 * 10) / 10 + ' hours' : 'Calculating...';
                document.getElementById('enhanced-source').textContent = data.data_source || 'Enhanced Google Maps';
                document.getElementById('enhanced-poi').textContent = (data.points_of_interest?.length || 0) + ' points of interest';
            }
            
            detailsDiv.classList.remove('hidden');
        }

        function showDifficultyAnalysis(difficultyData) {
            if (!document.getElementById('difficulty-analysis')) {
                const analysisDiv = document.createElement('div');
                analysisDiv.id = 'difficulty-analysis';
                analysisDiv.className = 'mt-3 p-3 bg-yellow-50 rounded border text-sm';
                analysisDiv.innerHTML = `
                    <h5 class="font-medium text-gray-800 mb-2">AI Difficulty Analysis</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <span class="font-medium text-gray-700">Calculated Difficulty:</span>
                            <span class="text-orange-600 block font-semibold">${difficultyData.difficulty || 'Unknown'}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Steepest Grade:</span>
                            <span class="text-red-600 block">${difficultyData.steepest_grade_percent || 0}%</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Difficulty Score:</span>
                            <span class="text-blue-600 block">${difficultyData.difficulty_score || 0}/10</span>
                        </div>
                    </div>
                `;
                document.getElementById('coordinate-details').appendChild(analysisDiv);
            }
        }

        function calculateElevationGainFromData(elevationData) {
            if (!elevationData || elevationData.length < 2) return null;
            
            let gain = 0;
            for (let i = 1; i < elevationData.length; i++) {
                const change = elevationData[i].elevation - elevationData[i-1].elevation;
                if (change > 0) gain += change;
            }
            return gain;
        }

        // Trail measurements calculation functions
        function calculateTrailMeasurements(coordinates) {
            if (!coordinates || coordinates.length < 2) {
                updateMeasurementsStatus('error', 'Insufficient coordinate data for measurements');
                return;
            }

            updateMeasurementsStatus('loading', 'Calculating trail measurements...');

            try {
                // Calculate trail length (distance)
                const length = calculateTrailLength(coordinates);
                
                // Get elevation data and calculate elevation metrics
                getElevationDataForMeasurements(coordinates, length);
                
            } catch (error) {
                console.error('Error calculating measurements:', error);
                updateMeasurementsStatus('error', 'Error calculating trail measurements');
            }
        }

        // Enhanced function that uses known trail length when available
        function calculateTrailMeasurementsWithKnownLength(coordinates, knownLengthKm) {
            if (!coordinates || coordinates.length < 2) {
                updateMeasurementsStatus('error', 'Insufficient coordinate data for measurements');
                return;
            }

            updateMeasurementsStatus('loading', 'Calculating trail measurements with known trail data...');

            try {
                // Use the known length instead of calculating from coordinates
                console.log('Using known trail length:', knownLengthKm + ' km');
                
                // Get elevation data and calculate elevation metrics
                getElevationDataForMeasurements(coordinates, knownLengthKm);
                
            } catch (error) {
                console.error('Error calculating measurements:', error);
                updateMeasurementsStatus('error', 'Error calculating trail measurements');
            }
        }

        function calculateTrailLength(coordinates) {
            let totalDistance = 0;
            
            for (let i = 1; i < coordinates.length; i++) {
                const prev = coordinates[i - 1];
                const curr = coordinates[i];
                
                // Calculate distance between two points using Haversine formula
                const distance = getDistanceBetweenPoints(prev.lat, prev.lng, curr.lat, curr.lng);
                totalDistance += distance;
            }
            
            // Convert from meters to kilometers
            return totalDistance / 1000;
        }

        function getDistanceBetweenPoints(lat1, lng1, lat2, lng2) {
            const R = 6371000; // Earth's radius in meters
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                     Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                     Math.sin(dLng/2) * Math.sin(dLng/2);
            
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c; // Distance in meters
        }

        function getElevationDataForMeasurements(coordinates, length) {
            // Create elevation service
            const elevationService = new google.maps.ElevationService();
            
            // Convert coordinates to LatLng objects
            const locations = coordinates.map(coord => new google.maps.LatLng(coord.lat, coord.lng));
            
            elevationService.getElevationForLocations({
                locations: locations
            }, function(results, status) {
                if (status === 'OK' && results) {
                    const elevations = results.map(result => result.elevation);
                    const elevationGain = calculateElevationGain(elevations);
                    const highestPoint = Math.max(...elevations);
                    const lowestPoint = Math.min(...elevations);
                    
                    // Update the form fields
                    updateMeasurementFields(length, elevationGain, highestPoint, lowestPoint);
                    updateMeasurementsStatus('success', 'Trail measurements calculated successfully');
                } else {
                    console.error('Elevation service failed:', status);
                    // Still update length even if elevation fails
                    updateMeasurementFields(length, null, null, null);
                    updateMeasurementsStatus('partial', 'Trail length calculated, elevation data unavailable');
                }
            });
        }

        function calculateElevationGain(elevations) {
            let totalGain = 0;
            
            for (let i = 1; i < elevations.length; i++) {
                const gain = elevations[i] - elevations[i - 1];
                if (gain > 0) {
                    totalGain += gain;
                }
            }
            
            return totalGain;
        }

        function updateMeasurementFields(length, elevationGain, highestPoint, lowestPoint) {
            // Update length
            if (length !== null) {
                document.getElementById('length').value = length.toFixed(2);
                document.getElementById('length').classList.remove('bg-gray-50');
                document.getElementById('length').classList.add('bg-green-50');
                
                // Add indicator if this is from known trail data
                const lengthHelp = document.querySelector('#length').parentNode.querySelector('p');
                if (length > 10) { // Likely from known trail data
                    lengthHelp.innerHTML = '📏 <strong>Known trail data</strong> - Accurate trail distance';
                } else {
                    lengthHelp.innerHTML = '📏 Calculated from trail route distance';
                }
            }
            
            // Update elevation fields if available
            if (elevationGain !== null) {
                document.getElementById('elevation_gain').value = Math.round(elevationGain);
                document.getElementById('elevation_gain').classList.remove('bg-gray-50');
                document.getElementById('elevation_gain').classList.add('bg-green-50');
            }
            
            if (highestPoint !== null) {
                document.getElementById('elevation_high').value = Math.round(highestPoint);
                document.getElementById('elevation_high').classList.remove('bg-gray-50');
                document.getElementById('elevation_high').classList.add('bg-green-50');
            }
            
            if (lowestPoint !== null) {
                document.getElementById('elevation_low').value = Math.round(lowestPoint);
                document.getElementById('elevation_low').classList.remove('bg-gray-50');
                document.getElementById('elevation_low').classList.add('bg-green-50');
            }
        }

        function updateMeasurementsStatus(type, message) {
            const loadingIcon = document.getElementById('measurements-loading');
            const successIcon = document.getElementById('measurements-success-icon');
            const infoIcon = document.getElementById('measurements-info-icon');
            const messageEl = document.getElementById('measurements-message');
            
            // Hide all icons first
            loadingIcon.classList.add('hidden');
            successIcon.classList.add('hidden');
            infoIcon.classList.add('hidden');
            
            switch (type) {
                case 'loading':
                    loadingIcon.classList.remove('hidden');
                    break;
                case 'success':
                    successIcon.classList.remove('hidden');
                    break;
                case 'partial':
                    successIcon.classList.remove('hidden');
                    break;
                case 'error':
                    infoIcon.classList.remove('hidden');
                    break;
                default:
                    infoIcon.classList.remove('hidden');
            }
            
            messageEl.textContent = message;
        }

        // Check if measurements need to be calculated when visiting step 3
        function checkAndCalculateMeasurements() {
            const coordinatesInput = document.getElementById('trail_coordinates');
            const lengthInput = document.getElementById('length');
            
            // If we have coordinates but no length calculated yet
            if (coordinatesInput.value && !lengthInput.value) {
                try {
                    const coordinates = JSON.parse(coordinatesInput.value);
                    if (coordinates && coordinates.length > 1) {
                        calculateTrailMeasurements(coordinates);
                    }
                } catch (error) {
                    console.error('Error parsing coordinates for measurements:', error);
                }
            }
        }

        // Manual recalculate measurements
        function recalculateMeasurements() {
            const coordinatesInput = document.getElementById('trail_coordinates');
            
            if (!coordinatesInput.value) {
                alert('No trail coordinates available. Please generate route coordinates first in Step 2.');
                return;
            }
            
            try {
                const coordinates = JSON.parse(coordinatesInput.value);
                if (coordinates && coordinates.length > 1) {
                    // Clear existing values
                    document.getElementById('length').value = '';
                    document.getElementById('elevation_gain').value = '';
                    document.getElementById('elevation_high').value = '';
                    document.getElementById('elevation_low').value = '';
                    
                    // Reset field styling
                    ['length', 'elevation_gain', 'elevation_high', 'elevation_low'].forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        field.classList.remove('bg-green-50');
                        field.classList.add('bg-gray-50');
                    });
                    
                    calculateTrailMeasurements(coordinates);
                } else {
                    alert('Invalid coordinate data. Please regenerate route coordinates.');
                }
            } catch (error) {
                console.error('Error parsing coordinates for recalculation:', error);
                alert('Error reading coordinate data. Please regenerate route coordinates.');
            }
        }
    </script>
</x-app-layout>
