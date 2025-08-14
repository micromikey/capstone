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
                                    <input type="text" id="location_search" placeholder="Search for a location..." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]">
                                    <input type="hidden" id="location_id" name="location_id" required>
                                    <div id="location_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                                        <!-- Location results will be populated here -->
                                    </div>
                                    <div id="location_loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    <button type="button" onclick="showAllLocations()" class="text-[#336d66] hover:text-[#2a5a54] underline">
                                        Or view all locations
                                    </button>
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

                            <div>
                                <x-label for="length" value="Trail Length (km)" />
                                <div class="relative mt-1">
                                    <x-input id="length" type="number" name="length" step="0.1" min="0" class="pr-12 block w-full" placeholder="5.2" />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">km</span>
                                    </div>
                                </div>
                                <x-input-error for="length" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="elevation_gain" value="Elevation Gain (m)" />
                                <div class="relative mt-1">
                                    <x-input id="elevation_gain" type="number" name="elevation_gain" min="0" class="pr-12 block w-full" placeholder="500" />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">m</span>
                                    </div>
                                </div>
                                <x-input-error for="elevation_gain" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="elevation_high" value="Highest Point (m)" />
                                <div class="relative mt-1">
                                    <x-input id="elevation_high" type="number" name="elevation_high" min="0" class="pr-12 block w-full" placeholder="1030" />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">m</span>
                                    </div>
                                </div>
                                <x-input-error for="elevation_high" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="elevation_low" value="Lowest Point (m)" />
                                <div class="relative mt-1">
                                    <x-input id="elevation_low" type="number" name="elevation_low" min="0" class="pr-12 block w-full" placeholder="200" />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">m</span>
                                    </div>
                                </div>
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

    <script>
        let currentStep = 1;
        const totalSteps = 5;
        let locationSearchTimeout;

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
                    missingFields.push('location_id (please select a location from the search results)');
                }
                
                if (missingFields.length > 0) {
                    console.error('Missing required fields:', missingFields);
                    e.preventDefault();
                    alert('Please fill in all required fields: ' + missingFields.join(', '));
                    return false;
                }
                
                console.log('Form validation passed, submitting...');
            });

            // Initialize location search
            initializeLocationSearch();
        });

        // Location search functionality
        function initializeLocationSearch() {
            console.log('Initializing location search...');
            
            const searchInput = document.getElementById('location_search');
            const resultsDiv = document.getElementById('location_results');
            const hiddenInput = document.getElementById('location_id');

            if (!searchInput || !resultsDiv || !hiddenInput) {
                console.error('Location search elements not found:', {
                    searchInput: !!searchInput,
                    resultsDiv: !!resultsDiv,
                    hiddenInput: !!hiddenInput
                });
                return;
            }

            console.log('Location search elements found, setting up event listeners...');

            searchInput.addEventListener('input', function() {
                console.log('Input event triggered, value:', this.value);
                clearTimeout(locationSearchTimeout);
                const query = this.value.trim();
                
                // Clear previous selection if user is typing
                if (this.value !== this.dataset.lastSelectedValue) {
                    clearLocationSelection();
                }
                
                if (query.length < 2) {
                    console.log('Query too short, hiding results');
                    resultsDiv.classList.add('hidden');
                    return;
                }

                console.log('Setting timeout for search...');
                locationSearchTimeout = setTimeout(() => {
                    searchLocations(query);
                }, 300);
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                    resultsDiv.classList.add('hidden');
                }
            });

            console.log('Location search initialization complete');
        }

        function searchLocations(query) {
            const resultsDiv = document.getElementById('location_results');
            const searchInput = document.getElementById('location_search');
            const hiddenInput = document.getElementById('location_id');
            const loadingDiv = document.getElementById('location_loading');

            console.log('Searching for locations with query:', query);
            
            // Show loading state
            loadingDiv.classList.remove('hidden');
            resultsDiv.classList.add('hidden');

            fetch(`/api/locations/search?q=${encodeURIComponent(query)}`)
                .then(response => {
                    console.log('API response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('API response data:', data);
                    loadingDiv.classList.add('hidden');
                    
                    if (data.data && data.data.length > 0) {
                        console.log('Found locations:', data.data);
                        displayLocationResults(data.data);
                        resultsDiv.classList.remove('hidden');
                    } else {
                        console.log('No locations found');
                        resultsDiv.classList.add('hidden');
                        // Show "no results" message
                        resultsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500 text-center">No locations found. Try a different search term.</div>';
                        resultsDiv.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error searching locations:', error);
                    loadingDiv.classList.add('hidden');
                    resultsDiv.classList.add('hidden');
                    // Show error message
                    resultsDiv.innerHTML = '<div class="px-4 py-2 text-red-500 text-center">Error searching locations. Please try again.</div>';
                    resultsDiv.classList.remove('hidden');
                });
        }

        function displayLocationResults(locations) {
            const resultsDiv = document.getElementById('location_results');
            const searchInput = document.getElementById('location_search');
            const hiddenInput = document.getElementById('location_id');

            resultsDiv.innerHTML = locations.map(location => `
                <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                     onclick="selectLocation(${location.id}, '${location.name}, ${location.province}')">
                    <div class="font-medium text-gray-900">${location.name}</div>
                    <div class="text-sm text-gray-600">${location.province}, ${location.region}</div>
                </div>
            `).join('');
        }

        function selectLocation(id, displayName) {
            const searchInput = document.getElementById('location_search');
            const hiddenInput = document.getElementById('location_id');
            const resultsDiv = document.getElementById('location_results');
            
            searchInput.value = displayName;
            hiddenInput.value = id;
            resultsDiv.classList.add('hidden');
            
            // Add visual feedback
            searchInput.classList.add('border-green-500', 'bg-green-50');
            searchInput.classList.remove('border-gray-300');
            
            // Add a checkmark icon
            const existingCheckmark = searchInput.parentNode.querySelector('.location-selected-checkmark');
            if (!existingCheckmark) {
                const checkmark = document.createElement('div');
                checkmark.className = 'location-selected-checkmark absolute right-10 top-1/2 transform -translate-y-1/2 text-green-500';
                checkmark.innerHTML = '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>';
                searchInput.parentNode.appendChild(checkmark);
            }
            
            console.log('Location selected:', { id, displayName });
            
            // Store the selected value for comparison
            searchInput.dataset.lastSelectedValue = displayName;
        }

        function clearLocationSelection() {
            const searchInput = document.getElementById('location_search');
            const hiddenInput = document.getElementById('location_id');
            
            hiddenInput.value = '';
            searchInput.classList.remove('border-green-500', 'bg-green-50');
            searchInput.classList.add('border-gray-300');
            
            // Remove checkmark
            const checkmark = searchInput.parentNode.querySelector('.location-selected-checkmark');
            if (checkmark) {
                checkmark.remove();
            }
        }

        function showAllLocations() {
            const resultsDiv = document.getElementById('location_results');
            const searchInput = document.getElementById('location_search');
            const loadingDiv = document.getElementById('location_loading');
            
            // Show loading state
            loadingDiv.classList.remove('hidden');
            resultsDiv.classList.add('hidden');
            
            // Get all locations from the server
            fetch('/api/locations')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(locations => {
                    loadingDiv.classList.add('hidden');
                    if (locations && locations.length > 0) {
                        displayLocationResults(locations);
                        resultsDiv.classList.remove('hidden');
                        searchInput.placeholder = 'All locations shown below...';
                    } else {
                        // Fallback to showing locations from the view data
                        showLocationsFromView();
                    }
                })
                .catch(error => {
                    console.error('Error fetching all locations:', error);
                    loadingDiv.classList.add('hidden');
                    // Fallback to showing locations from the view data
                    showLocationsFromView();
                });
        }

        function showLocationsFromView() {
            const resultsDiv = document.getElementById('location_results');
            const searchInput = document.getElementById('location_search');
            
            // This will use the locations passed from the controller
            const locations = @json($locations);
            if (locations && locations.length > 0) {
                displayLocationResults(locations);
                resultsDiv.classList.remove('hidden');
                searchInput.placeholder = 'All locations shown below...';
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
    </script>
</x-app-layout>
