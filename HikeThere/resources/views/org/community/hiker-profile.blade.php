<x-app-layout>
    <div>
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Hiker Profile') }}
                </h2>
            </div>
        </x-slot>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Hiker Profile Section -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-10">
                <!-- Gradient Header -->
                <div class="h-40 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 relative">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-4 left-8 w-2 h-2 bg-white rounded-full animate-pulse"></div>
                        <div class="absolute top-8 right-16 w-1.5 h-1.5 bg-white rounded-full animate-pulse" style="animation-delay: 0.3s;"></div>
                        <div class="absolute bottom-6 left-1/4 w-2 h-2 bg-white rounded-full animate-pulse" style="animation-delay: 0.6s;"></div>
                    </div>
                </div>
                
                <div class="px-6 sm:px-10 pb-10">
                    <div class="-mt-20 mb-8 relative z-10">
                        <div class="flex flex-col sm:flex-row items-start gap-6">
                            <!-- Avatar -->
                            <div class="relative">
                                @if($hiker->profile_picture)
                                    <img src="{{ $hiker->profile_picture_url }}" 
                                         alt="{{ $hiker->name }}" 
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-2xl object-cover">
                                @else
                                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-2xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center">
                                        <span class="text-4xl font-bold text-white">
                                            {{ strtoupper(substr($hiker->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Content Section -->
                            <div class="flex-1 min-w-0">
                                <div class="bg-white rounded-xl px-6 py-5 shadow-lg border border-gray-100">
                                    <!-- Name and Email -->
                                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                                        {{ $hiker->name }}
                                        @if($hiker->email_verified_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Verified
                                            </span>
                                        @endif
                                    </h1>
                                    
                                    <!-- Contact Info -->
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                            </svg>
                                            <span>{{ $hiker->email }}</span>
                                        </div>
                                        @if($hiker->phone)
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                                </svg>
                                                <span>{{ $hiker->phone }}</span>
                                            </div>
                                        @endif
                                        @if($hiker->address)
                                            <div class="flex items-start gap-2">
                                                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>{{ $hiker->address }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Member Since -->
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <p class="text-sm text-gray-500">
                                            Member since {{ $hiker->created_at->format('F Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Info Badge -->
                            <div class="flex-shrink-0 w-full sm:w-auto">
                                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-xl p-4 text-center">
                                    <div class="flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-emerald-800">Confirmed Booking</p>
                                    <p class="text-xs text-emerald-600 mt-1">Payment Verified</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assessment Score Section -->
                    @if($latestAssessment)
                    <div class="border-t border-gray-100 pt-8 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Pre-Hike Self-Assessment Results
                        </h2>

                        <!-- Overall Score -->
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-6 mb-6 border border-emerald-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Overall Readiness Score</h3>
                                    <p class="text-sm text-gray-600">Completed on {{ $latestAssessment->completed_at->format('F d, Y') }}</p>
                                </div>
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-emerald-600">{{ number_format($latestAssessment->overall_score, 0) }}%</div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        @if($latestAssessment->overall_score >= 80)
                                            <span class="text-green-600 font-semibold">Excellent</span>
                                        @elseif($latestAssessment->overall_score >= 60)
                                            <span class="text-yellow-600 font-semibold">Good</span>
                                        @else
                                            <span class="text-red-600 font-semibold">Needs Improvement</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assessment Categories -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @php
                                $categories = [
                                    ['name' => 'Health', 'score' => $latestAssessment->health_score, 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                                    ['name' => 'Fitness', 'score' => $latestAssessment->fitness_score, 'icon' => 'M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z'],
                                    ['name' => 'Experience', 'score' => $latestAssessment->experience_score, 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                                    ['name' => 'Weather', 'score' => $latestAssessment->weather_score, 'icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z'],
                                    ['name' => 'Emergency', 'score' => $latestAssessment->emergency_score, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z'],
                                    ['name' => 'Environment', 'score' => $latestAssessment->environment_score, 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ];
                            @endphp

                            @foreach($categories as $category)
                            <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="p-2 bg-emerald-100 rounded-lg">
                                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="{{ $category['icon'] }}" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">{{ $category['name'] }}</h4>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ $category['score'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">{{ number_format($category['score'], 0) }}%</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Emergency Contact Section -->
                    @if($latestAssessment && $latestAssessment->emergency_contact_name)
                    <div class="border-t border-gray-100 pt-8 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                            Emergency Contact Information
                        </h2>

                        <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
                                    <p class="text-base text-gray-900 font-semibold">{{ $latestAssessment->emergency_contact_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                                    <p class="text-base text-gray-900 font-semibold">{{ $latestAssessment->emergency_contact_relationship }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary Phone</label>
                                    <p class="text-base text-gray-900 font-semibold">{{ $latestAssessment->emergency_contact_phone }}</p>
                                </div>
                                @if($latestAssessment->emergency_contact_phone_alt)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alternative Phone</label>
                                    <p class="text-base text-gray-900 font-semibold">{{ $latestAssessment->emergency_contact_phone_alt }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="border-t border-gray-100 pt-8 mb-8">
                        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8 text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Assessment Data Available</h3>
                            <p class="text-gray-600">This hiker hasn't completed a pre-hike self-assessment yet.</p>
                        </div>
                    </div>
                    @endif

                    <!-- Itinerary Section -->
                    @if($latestItinerary)
                    <div class="border-t border-gray-100 pt-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            Hiking Itinerary for {{ $booking->trail->trail_name }}
                        </h2>

                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                            <!-- Itinerary Header -->
                            <div class="mb-6 pb-6 border-b border-blue-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
                                        <p class="text-base text-gray-900 font-semibold">
                                            {{ \Carbon\Carbon::parse($latestItinerary->start_datetime)->format('M d, Y g:i A') }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Expected Return</label>
                                        <p class="text-base text-gray-900 font-semibold">
                                            {{ \Carbon\Carbon::parse($latestItinerary->end_datetime)->format('M d, Y g:i A') }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                                        <p class="text-base text-gray-900 font-semibold">
                                            {{ \Carbon\Carbon::parse($latestItinerary->start_datetime)->diffInHours(\Carbon\Carbon::parse($latestItinerary->end_datetime)) }} hours
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group Information -->
                            @if($latestItinerary->group_size)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Group Information</label>
                                <div class="bg-white rounded-lg p-4">
                                    <p class="text-gray-900"><span class="font-semibold">Group Size:</span> {{ $latestItinerary->group_size }} person(s)</p>
                                    @if($latestItinerary->group_members)
                                    <p class="text-gray-900 mt-2"><span class="font-semibold">Members:</span> {{ $latestItinerary->group_members }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Route Details -->
                            @if($latestItinerary->route_description)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Planned Route</label>
                                <div class="bg-white rounded-lg p-4">
                                    <p class="text-gray-900 whitespace-pre-line">{{ $latestItinerary->route_description }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Equipment List -->
                            @if($latestItinerary->equipment_list)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Equipment & Gear</label>
                                <div class="bg-white rounded-lg p-4">
                                    <p class="text-gray-900 whitespace-pre-line">{{ $latestItinerary->equipment_list }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Special Notes -->
                            @if($latestItinerary->special_notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Special Notes</label>
                                <div class="bg-white rounded-lg p-4">
                                    <p class="text-gray-900 whitespace-pre-line">{{ $latestItinerary->special_notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="border-t border-gray-100 pt-8">
                        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8 text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Itinerary Available</h3>
                            <p class="text-gray-600">This hiker hasn't submitted an itinerary for this booking yet.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Booking Details Section -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 sm:px-10 py-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        Booking Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trail</label>
                            <p class="text-base text-gray-900 font-semibold">{{ $booking->trail->trail_name }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Booking Date</label>
                            <p class="text-base text-gray-900 font-semibold">{{ $booking->created_at->format('F d, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hiking Date</label>
                            <p class="text-base text-gray-900 font-semibold">{{ \Carbon\Carbon::parse($booking->hike_date)->format('F d, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Number of Hikers</label>
                            <p class="text-base text-gray-900 font-semibold">{{ $booking->number_of_people }} person(s)</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Booking Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
