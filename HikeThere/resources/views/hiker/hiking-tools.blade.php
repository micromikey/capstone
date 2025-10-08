<x-app-layout>
    @php
        // Smart image URL resolution for GCS or local fallback
        $getImageUrl = function($imageName) {
            if (env('GCS_BUCKET')) {
                return 'https://storage.googleapis.com/' . env('GCS_BUCKET') . '/img/' . $imageName;
            }
            return asset('img/' . $imageName);
        };
    @endphp
    
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
            <!-- Search Bar Section -->
            <div class="mb-8">
                <form class="flex items-center max-w-2xl w-full mx-auto relative" action="{{ route('trails.search') }}" method="GET">
                    <label for="trail-search" class="sr-only">Search Trails</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 21 21">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.15 5.6h.01m3.337 1.913h.01m-6.979 0h.01M5.541 11h.01M15 15h2.706a1.957 1.957 0 0 0 1.883-1.325A9 9 0 1 0 2.043 11.89 9.1 9.1 0 0 0 7.2 19.1a8.62 8.62 0 0 0 3.769.9A2.013 2.013 0 0 0 13 18v-.857A2.034 2.034 0 0 1 15 15Z" />
                            </svg>
                        </div>
                        <input type="text" id="header-search-input" name="q" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full ps-10 p-3 shadow-sm" placeholder="Search trails, locations..." value="{{ request('q') }}" autocomplete="off" />
                        @include('partials.header-search-dropdown')
                    </div>
                    <button type="submit" class="inline-flex items-center py-2.5 px-3 ms-2 text-sm font-medium text-white bg-green-700 rounded-lg border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300">
                        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>Search
                    </button>
                </form>
            </div>

            <!-- Header Section -->
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Hiking Tools</h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Access all the essential tools you need for safe and enjoyable hiking adventures.
                    From planning your route to assessing your readiness, we've got you covered.
                </p>
            </div>

            <!-- Tools Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
                <!-- Build Itineraries Card -->
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden h-full">
                    <div class="relative h-48">
                        <img src="{{ $getImageUrl('1.png') }}" alt="Build Itineraries" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <h3 class="text-2xl font-bold text-white mb-2">Build Itineraries</h3>
                            <p class="text-white/90 text-sm">Plan your perfect hiking adventure</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Itinerary Builder</h4>
                        </div>
                        <p class="text-gray-600 mb-4 leading-relaxed">
                            Create personalized hiking itineraries tailored to your fitness level, experience, and preferences.
                            Get optimized routes, safety protocols, and emergency contacts.
                        </p>
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Personalized route planning
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Safety protocols included
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Offline access available
                            </div>
                        </div>
                        <a href="{{ route('itinerary.instructions') }}"
                            class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white text-center py-3 px-4 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:scale-105">
                            Start Building
                        </a>
                    </div>
                </div>

                <!-- Self Assessment Card -->
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden h-full">
                    <div class="relative h-48">
                        <img src="{{ $getImageUrl('2.png') }}" alt="Self Assessment" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <h3 class="text-2xl font-bold text-white mb-2">Self Assessment</h3>
                            <p class="text-white/90 text-sm">Evaluate your hiking readiness</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Readiness Check</h4>
                        </div>
                        <p class="text-gray-600 mb-4 leading-relaxed">
                            Complete a comprehensive assessment covering fitness, gear, health, weather, emergency preparedness,
                            and environmental factors to ensure you're ready for your hike.
                        </p>
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                6 comprehensive categories
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Personalized recommendations
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Safety-focused evaluation
                            </div>
                        </div>
                        <a href="{{ route('assessment.instruction') }}"
                            class="block w-full bg-gradient-to-r from-green-600 to-green-700 text-white text-center py-3 px-4 rounded-xl font-semibold hover:from-green-700 hover:to-green-800 transition-all duration-200 transform hover:scale-105">
                            Start Assessment
                        </a>
                    </div>
                </div>

                <!-- Bookings Card -->
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden h-full">
                    <div class="relative h-48">
                        <img src="{{ $getImageUrl('3.png') }}" alt="Bookings" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <h3 class="text-2xl font-bold text-white mb-2">Bookings</h3>
                            <p class="text-white/90 text-sm">Manage your hiking reservations</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Reservation Manager</h4>
                        </div>
                        <p class="text-gray-600 mb-4 leading-relaxed">
                            Book and manage campsites, guided hikes, and adventure packages.
                            Keep track of all your reservations in one convenient location.
                        </p>
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Campsite reservations
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Guided hike packages
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Centralized management
                            </div>
                        </div>
                        <div class="space-y-3">
                            <button onclick="openBookingModal()"
                                class="block w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white text-center py-3 px-4 rounded-xl font-semibold hover:from-purple-700 hover:to-purple-800 transition-all duration-200 transform hover:scale-105">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create Booking
                            </button>
                            <a href="{{ route('booking.index') }}"
                                class="block w-full bg-white border-2 border-purple-600 text-purple-600 text-center py-2.5 px-4 rounded-xl font-medium hover:bg-purple-50 transition-all duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                                View All Bookings
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Info Section -->
            <div class="mt-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Trail Assessment Card -->
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-white mb-2">Trail Assessment</h3>
                                    <p class="text-emerald-100">Share your trail experience and conditions</p>
                                </div>
                                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.818-4.818A4 4 0 1119 9m-7 7a2 2 0 100-4 2 2 0 000 4z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4 leading-relaxed">
                                After completing a hike, help the community by assessing the trail's emergency readiness, 
                                equipment availability, and overall safety conditions.
                            </p>
                            <div class="space-y-2 mb-6">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Rate emergency equipment
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Assess staff availability
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Provide feedback & recommendations
                                </div>
                            </div>
                            <a href="{{ route('hiker.readiness.index') }}"
                                class="block w-full bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-center py-3 px-4 rounded-xl font-semibold hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200 transform hover:scale-105">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                View My Assessments
                            </a>
                        </div>
                    </div>

                    <!-- Report a Problem Card -->
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-600 to-red-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-white mb-2">Report a Problem</h3>
                                    <p class="text-red-100">Help keep trails safe for everyone</p>
                                </div>
                                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4 leading-relaxed">
                                Encountered a safety issue, trail damage, or hazard during your hike? 
                                Report it immediately to help protect other hikers and maintain trail quality.
                            </p>
                            <div class="space-y-2 mb-6">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Report safety incidents
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Document trail hazards
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Quick incident reporting
                                </div>
                            </div>
                            <a href="{{ route('hiker.incidents.index') }}"
                                class="block w-full bg-gradient-to-r from-red-600 to-red-700 text-white text-center py-3 px-4 rounded-xl font-semibold hover:from-red-700 hover:to-red-800 transition-all duration-200 transform hover:scale-105">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                View My Reports
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Banner -->
                <div class="mt-6 bg-gradient-to-r from-blue-50 to-emerald-50 rounded-xl p-6 border border-blue-200">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Help Us Keep Trails Safe</h4>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Your feedback and incident reports are crucial for maintaining trail safety and quality. 
                                By sharing your experiences, you help create a safer hiking environment for the entire community.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Method Selection Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden z-50 items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="bookingModalContent">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-2">Choose Your Booking Method</h2>
                        <p class="text-purple-100">Select how you'd like to create your hiking reservation</p>
                    </div>
                    <button onclick="closeBookingModal()" class="text-white hover:text-purple-200 transition-colors duration-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Direct Booking Card -->
                    <div class="group bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-2xl p-6 hover:border-blue-400 hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:-translate-y-1" onclick="selectBookingMethod('direct')">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl mx-auto mb-4 flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Direct Booking</h3>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                Quick and straightforward booking for specific trails and dates. Perfect for planned adventures.
                            </p>
                            <div class="space-y-2 text-xs text-gray-500">
                                <div class="flex items-center justify-center">
                                    <svg class="w-3 h-3 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Fastest option
                                </div>
                                <div class="flex items-center justify-center">
                                    <svg class="w-3 h-3 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Immediate availability
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Itinerary Builder Card -->
                    <div class="group bg-gradient-to-br from-emerald-50 to-emerald-100 border-2 border-emerald-200 rounded-2xl p-6 hover:border-emerald-400 hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:-translate-y-1" onclick="selectBookingMethod('itinerary')">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl mx-auto mb-4 flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Itinerary Builder</h3>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                Plan your complete hiking experience with detailed itineraries, then book your preferred options.
                            </p>
                            <div class="space-y-2 text-xs text-gray-500">
                                <div class="flex items-center justify-center">
                                    <svg class="w-3 h-3 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Comprehensive planning
                                </div>
                                <div class="flex items-center justify-center">
                                    <svg class="w-3 h-3 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Personalized routes
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Events Booking Card -->
                    <div class="group bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-200 rounded-2xl p-6 hover:border-orange-400 hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:-translate-y-1" onclick="selectBookingMethod('events')">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl mx-auto mb-4 flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Events Booking</h3>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                Join organized hiking events, group adventures, and special hiking experiences with guides.
                            </p>
                            <div class="space-y-2 text-xs text-gray-500">
                                <div class="flex items-center justify-center">
                                    <svg class="w-3 h-3 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Guided experiences
                                </div>
                                <div class="flex items-center justify-center">
                                    <svg class="w-3 h-3 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Group adventures
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500 text-center">
                        <p>💡 <strong>Tip:</strong> Start with Direct Booking for quick reservations, or use Itinerary Builder for comprehensive trip planning.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openBookingModal() {
            const modal = document.getElementById('bookingModal');
            const content = document.getElementById('bookingModalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Trigger animation
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            const content = document.getElementById('bookingModalContent');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function selectBookingMethod(method) {
            // Close modal with animation
            closeBookingModal();

            // Navigate after modal closes
            setTimeout(() => {
                switch (method) {
                    case 'direct':
                        window.location.href = '{{ route("booking.details") }}';
                        break;
                    case 'itinerary':
                        window.location.href = '{{ route("itinerary.instructions") }}';
                        break;
                    case 'events':
                        // TODO: Replace with actual events route when available
                        // Example: window.location.href = '{{ route("events.index") }}';
                        alert('Events booking feature coming soon!');
                        break;
                }
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('bookingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookingModal();
            }
        });
    </script>
</x-app-layout>