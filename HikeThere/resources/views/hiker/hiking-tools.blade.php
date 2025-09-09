<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
            <!-- Search Bar Section -->
            <div class="mb-8">
                <form class="flex items-center max-w-lg mx-auto" action="{{ route('trails.search') }}" method="GET">   
                    <label for="trail-search" class="sr-only">Search Trails</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 21 21">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.15 5.6h.01m3.337 1.913h.01m-6.979 0h.01M5.541 11h.01M15 15h2.706a1.957 1.957 0 0 0 1.883-1.325A9 9 0 1 0 2.043 11.89 9.1 9.1 0 0 0 7.2 19.1a8.62 8.62 0 0 0 3.769.9A2.013 2.013 0 0 0 13 18v-.857A2.034 2.034 0 0 1 15 15Z"/>
                            </svg>
                        </div>
                        <input type="text" id="trail-search" name="q" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full ps-10 p-2.5 shadow-sm" placeholder="Search trails, locations..." value="{{ request('q') }}" />
                    </div>
                    <button type="submit" class="inline-flex items-center py-2.5 px-3 ms-2 text-sm font-medium text-white bg-green-700 rounded-lg border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300">
                        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
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
                        <img src="{{ asset('img/1.png') }}" alt="Build Itineraries" class="w-full h-full object-cover">
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
                        <img src="{{ asset('img/2.png') }}" alt="Self Assessment" class="w-full h-full object-cover">
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
                        <img src="{{ asset('img/3.png') }}" alt="Bookings" class="w-full h-full object-cover">
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
                        <a href="{{ route('booking.details') }}" 
                           class="block w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white text-center py-3 px-4 rounded-xl font-semibold hover:from-purple-700 hover:to-purple-800 transition-all duration-200 transform hover:scale-105">
                            View Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Info Section -->
            <div class="mt-16 text-center">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Ready to Start Your Adventure?</h3>
                    <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                        These tools are designed to help you plan safe and enjoyable hiking experiences. 
                        Start with the assessment to understand your readiness level, then build your perfect itinerary.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('assessment.instruction') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Start Assessment First
                        </a>
                        <a href="{{ route('itinerary.build') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                            </svg>
                            Build Itinerary
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>