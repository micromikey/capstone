@props(['weather', 'forecast', 'user', 'latestAssessment', 'latestItinerary', 'followedTrails' => collect(), 'followingCount' => 0])

@php
    // Initialize the TrailImageService for dynamic images
    $imageService = app('App\Services\TrailImageService');
@endphp

{{-- Header --}}
<div class="relative p-6 lg:p-10 bg-gradient-to-r from-green-100 via-white to-white border-b border-gray-200 rounded-b-xl shadow-sm overflow-hidden min-h-[300px]">

    {{-- Vague Mountain SVG (Right Side) --}}
    <svg class="absolute bottom-0 right-0 w-full md:w-1/2 h-full opacity-75 pointer-events-none select-none"
        viewBox="0 0 800 200" preserveAspectRatio="xMinYMid slice" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="fade-mask" x1="1" y1="0" x2="0" y2="0">
                <stop offset="0.7" stop-color="white" />
                <stop offset="1" stop-color="white" stop-opacity="0" />
            </linearGradient>
            <mask id="fade">
                <rect width="100%" height="100%" fill="url(#fade-mask)" />
            </mask>
        </defs>

        <g mask="url(#fade)">
            <path d="M0 150 Q 50 120, 100 140 Q 150 160, 200 130 Q 250 100, 300 120 Q 350 140, 400 110 Q 450 80, 500 100 Q 550 120, 600 90 Q 650 60, 700 100 Q 750 130, 800 90 L 800 200 L 0 200 Z"
                class="fill-mountain-100" />
            <path d="M0 160 Q 100 130, 200 150 Q 300 170, 400 140 Q 500 110, 600 140 Q 700 170, 800 130 L 800 200 L 0 200 Z"
                class="fill-mountain-200 opacity-80" />
            <path d="M0 180 Q 100 160, 200 170 Q 300 180, 400 160 Q 500 150, 600 160 Q 700 170, 800 150 L 800 200 L 0 200 Z"
                class="fill-mountain-300 opacity-70" />
        </g>
    </svg>

    {{-- Main Content --}}
    <div class="relative z-10">
        <div class="flex items-center space-x-4">
            <x-application-logo class="h-14 w-auto" />
            <div>
                <h1 class="text-3xl font-bold text-green-800 tracking-tight">
                    HikeThere
                </h1>
                <p class="text-sm text-gray-600 font-medium leading-tight">
                    Your smart companion for safe and informed hiking.
                </p>
            </div>
        </div>

        <div class="mt-8 max-w-3xl">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800">
                Start Ready. End with Safety.
            </h2>
            <p class="mt-3 text-gray-600 text-sm md:text-base leading-relaxed">
                Plan your hike with real-time weather forecasts, personalized trail suggestions, and essential safety recommendations — all in one place.
            </p>
            
            {{-- Quick Access to Hiking Tools for Hikers --}}
            @if(isset($user) && $user && $user->user_type === 'hiker')
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('hiking-tools') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    Access Hiking Tools
                </a>
                <a href="{{ route('assessment.instruction') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Start Assessment
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Weather and Forecast Section --}}
@if(isset($weather) && $weather)
<div class="bg-white bg-opacity-90 px-6 lg:px-8 py-6 grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">

    {{-- Weather Overview Card (1/3 width on md+) --}}
    <div class="col-span-1 bg-gradient-to-r {{ $weather['gradient'] ?? 'from-indigo-500 to-yellow-300' }} rounded-xl p-5 text-white shadow-md h-full flex flex-col justify-between">

        {{-- Date --}}
        <h2 class="text-xs font-medium opacity-90">{{ now()->format('F j, Y') }}</h2>

        {{-- Icon + Temp --}}
        <div class="flex items-center justify-between mt-3 flex-1">
            <img src="https://openweathermap.org/img/wn/{{ $weather['icon'] ?? '01d' }}@2x.png"
                alt="{{ $weather['description'] ?? 'Clear sky' }}"
                class="h-16 w-16 drop-shadow-sm">

            <div class="text-right">
                <h1 class="text-4xl font-bold leading-none">{{ $weather['temp'] ?? 'N/A' }}°</h1>
                <p class="text-xs mt-1 capitalize leading-tight">{{ $weather['description'] ?? 'Clear sky' }}</p>
            </div>
        </div>

        {{-- Location --}}
        <p class="text-xs font-medium tracking-wide text-white/90 truncate mt-3">
            📍 {{ $weather['city'] ?? 'Unknown' }}
        </p>
    </div>

    {{-- Forecast Calendar Card (2/3 width on md+) --}}
    <div class="col-span-1 md:col-span-2 bg-white rounded-xl shadow-md p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
            @if(isset($forecast) && $forecast && $forecast->count() > 0)
                @foreach($forecast as $day)
                <div class="bg-blue-100 p-4 rounded-lg text-center shadow-sm hover:shadow-md transition">
                    <div class="text-sm font-medium text-gray-800 mb-2">
                        <div>{{ explode(', ', $day['date'])[0] }}</div>
                        <div class="text-xs text-gray-600">{{ explode(', ', $day['date'])[1] }}</div>
                    </div>
                    <img src="https://openweathermap.org/img/wn/{{ $day['icon'] ?? '01d' }}@2x.png"
                        alt="{{ $day['condition'] ?? 'Clear' }}"
                        class="mx-auto h-12 w-12 mt-2">
                    <div class="text-lg font-bold text-gray-900 mt-2">{{ $day['temp'] ?? 'N/A' }}°C</div>
                    <div class="capitalize text-gray-600 text-sm mt-1 truncate">{{ $day['condition'] ?? 'Clear' }}</div>
                </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-8 text-gray-500">
                    <p>Weather forecast not available</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endif


{{-- Hiker's Unique Features Section --}}
@if(isset($user) && $user && $user->user_type === 'hiker')
<div class="px-6 lg:px-8 py-12 bg-gradient-to-br from-green-50 via-blue-50 to-emerald-50 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 left-10 w-32 h-32 bg-green-400 rounded-full"></div>
        <div class="absolute top-32 right-20 w-24 h-24 bg-blue-400 rounded-full"></div>
        <div class="absolute bottom-20 left-1/4 w-20 h-20 bg-emerald-400 rounded-full"></div>
    </div>
    
    <div class="relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-500 to-blue-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                </svg>
            </div>
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Your Essential Hiking Tools</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Two powerful tools designed to ensure your hiking adventures are safe, enjoyable, and perfectly planned.
            </p>
        </div>
        
        <!-- Tools Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Pre-Hike Self-Assessment Card -->
            <div class="group bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 overflow-hidden relative">
                <!-- Status Badge -->
                <div class="absolute top-6 right-6 z-20">
                    @if($latestAssessment)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Completed
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Required
                        </span>
                    @endif
                </div>
                
                <div class="relative h-56">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500 via-emerald-600 to-teal-700 group-hover:from-green-600 group-hover:via-emerald-700 group-hover:to-teal-800 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-7xl group-hover:scale-110 transition-transform duration-500">🥾</span>
                    </div>
                    <div class="absolute bottom-6 left-6 right-6">
                        <h3 class="text-2xl font-bold text-white mb-2">Pre-Hike Self-Assessment</h3>
                        <p class="text-white/90 text-sm leading-relaxed">Comprehensive readiness evaluation for safe hiking</p>
                    </div>
                </div>
                
                <div class="p-8">
                    @if($latestAssessment)
                        <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-200">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-green-800 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Latest Assessment
                                </span>
                                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">{{ $latestAssessment->completed_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-green-600">{{ $latestAssessment->overall_score }}%</div>
                                    <div class="text-xs text-green-700">Overall Score</div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-green-800 mb-1">{{ $latestAssessment->readiness_level }}</div>
                                    <div class="w-full bg-green-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $latestAssessment->overall_score }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <a href="{{ route('assessment.saved-results') }}" class="block w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white text-center py-4 px-6 rounded-2xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                View Detailed Results
                            </a>
                            <a href="{{ route('assessment.instruction') }}" class="block w-full bg-gray-100 text-gray-700 text-center py-3 px-6 rounded-2xl font-medium hover:bg-gray-200 transition-all duration-300">
                                Retake Assessment
                            </a>
                        </div>
                    @else
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Assessment Required</h4>
                                    <p class="text-sm text-gray-600">Complete this before your first hike</p>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Evaluate your fitness, gear, health, weather awareness, emergency preparedness, and environmental factors to ensure you're ready for safe hiking.
                            </p>
                        </div>
                        <a href="{{ route('assessment.instruction') }}" class="block w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white text-center py-4 px-6 rounded-2xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Start Your Assessment
                        </a>
                    @endif
                </div>
            </div>

            <!-- Itinerary Builder Card -->
            <div class="group bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 overflow-hidden relative">
                <!-- Status Badge -->
                <div class="absolute top-6 right-6 z-20">
                    @if($latestItinerary)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Ready to Build
                        </span>
                    @endif
                </div>
                
                <div class="relative h-56">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-700 group-hover:from-blue-600 group-hover:via-indigo-700 group-hover:to-purple-800 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-7xl group-hover:scale-110 transition-transform duration-500">🗺️</span>
                    </div>
                    <div class="absolute bottom-6 left-6 right-6">
                        <h3 class="text-2xl font-bold text-white mb-2">Itinerary Builder</h3>
                        <p class="text-white/90 text-sm leading-relaxed">Create personalized hiking plans and routes</p>
                    </div>
                </div>
                
                <div class="p-8">
                    @if($latestItinerary)
                        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-blue-800 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Latest Itinerary
                                </span>
                                <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">{{ $latestItinerary->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-semibold text-blue-800 text-lg">{{ $latestItinerary->title }}</h4>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="text-blue-700 bg-blue-100 px-2 py-1 rounded-full">{{ $latestItinerary->trail_name }}</span>
                                    <span class="text-blue-700 bg-blue-100 px-2 py-1 rounded-full">{{ $latestItinerary->difficulty_level }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <a href="{{ route('itinerary.build') }}" class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center py-4 px-6 rounded-2xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create New Itinerary
                            </a>
                            <a href="{{ route('itinerary.build') }}" class="block w-full bg-gray-100 text-gray-700 text-center py-3 px-6 rounded-2xl font-medium hover:bg-gray-200 transition-all duration-300">
                                View All Itineraries
                            </a>
                        </div>
                    @else
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Plan Your Adventure</h4>
                                    <p class="text-sm text-gray-600">Build your perfect hiking route</p>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Create personalized hiking itineraries with optimized routes, safety protocols, emergency contacts, and offline access for your adventures.
                            </p>
                        </div>
                        <a href="{{ route('itinerary.build') }}" class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center py-4 px-6 rounded-2xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Start Building Your Itinerary
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Access Bar -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-center md:text-left">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Quick Access</h3>
                    <p class="text-sm text-gray-600">Get to your tools faster</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('hiking-tools') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        All Tools
                    </a>
                    <a href="{{ route('explore') }}" class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-xl font-medium hover:bg-green-200 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Explore Trails
                    </a>
                    <a href="{{ route('community.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl font-medium hover:bg-emerald-200 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Community
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Community Section for Hikers --}}
@if(isset($user) && $user && $user->user_type === 'hiker' && (isset($followedTrails) && $followedTrails->count() > 0))
<div class="px-6 lg:px-8 py-12 bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 right-10 w-32 h-32 bg-purple-400 rounded-full"></div>
        <div class="absolute top-32 left-20 w-24 h-24 bg-pink-400 rounded-full"></div>
        <div class="absolute bottom-20 right-1/4 w-20 h-20 bg-indigo-400 rounded-full"></div>
    </div>
    
    <div class="relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Community Trails</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Latest trails from organizations you follow
                @if(isset($followingCount) && $followingCount > 0)
                    <span class="text-purple-600 font-semibold">({{ $followingCount }} {{ $followingCount === 1 ? 'organization' : 'organizations' }})</span>
                @endif
            </p>
        </div>
        
        <!-- Community Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 border border-purple-100">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $followingCount ?? 0 }}</div>
                <div class="text-sm text-gray-600">Organizations Following</div>
            </div>
            <div class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 border border-pink-100">
                <div class="text-3xl font-bold text-pink-600 mb-2">{{ $followedTrails->count() ?? 0 }}</div>
                <div class="text-sm text-gray-600">New Trails Available</div>
            </div>
            <div class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 border border-indigo-100">
                <div class="text-3xl font-bold text-indigo-600 mb-2">
                    {{ $followedTrails->sum(function($trail) { return $trail->reviews()->where('user_id', auth()->id())->count(); }) }}
                </div>
                <div class="text-sm text-gray-600">Your Reviews</div>
            </div>
        </div>
        
        <!-- Followed Trails Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($followedTrails as $trail)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden group">
                    <div class="relative h-48">
                        @php
                            // Get dynamic image from enhanced TrailImageService
                            $primaryImage = $imageService->getPrimaryTrailImage($trail);
                            $trailImage = $primaryImage['url'];
                        @endphp
                        <img src="{{ $trailImage }}" 
                             alt="{{ $trail->trail_name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        
                        <!-- Image source badge for API images -->
                        @if($primaryImage['source'] !== 'organization')
                            <div class="absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded">
                                {{ ucfirst($primaryImage['source']) }}
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-white px-2 py-1 rounded-full {{ $trail->difficulty === 'easy' ? 'bg-green-600' : ($trail->difficulty === 'moderate' ? 'bg-yellow-600' : 'bg-red-600') }}">
                                    {{ ucfirst($trail->difficulty) }}
                                </span>
                                <div class="flex items-center text-white text-xs">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Following
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-bold text-gray-800 group-hover:text-purple-600 transition-colors duration-300 truncate">
                                {{ $trail->trail_name }}
                            </h3>
                        </div>
                                                        <p class="text-sm text-gray-500 mb-2">by {{ $trail->user->display_name }}</p>
                        <p class="text-xs text-gray-400 mb-4">{{ $trail->location->name ?? 'Location not set' }}</p>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= round($trail->average_rating) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600 ml-2">{{ number_format($trail->average_rating, 1) }} ({{ $trail->total_reviews }})</span>
                            </div>
                            <span class="text-lg font-bold text-purple-600">₱{{ number_format($trail->price, 0) }}</span>
                        </div>
                        
                        <a href="{{ route('trails.show', $trail->slug) }}" 
                           class="block w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:scale-105">
                            View & Review Trail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Community Actions -->
        <div class="text-center">
            <div class="inline-flex flex-col sm:flex-row gap-4">
                <a href="{{ route('community.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Explore Community
                </a>
                <a href="{{ route('explore') }}" 
                   class="inline-flex items-center px-6 py-3 bg-white text-purple-600 border-2 border-purple-600 rounded-xl font-semibold hover:bg-purple-600 hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Discover Organizations
                </a>
            </div>
        </div>
    </div>
</div>
@elseif(isset($user) && $user && $user->user_type === 'hiker')
{{-- Show community invitation for hikers not following anyone yet --}}
<div class="px-6 lg:px-8 py-12 bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 relative overflow-hidden">
    <div class="relative z-10 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Join the Community</h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
            Connect with hiking organizations, discover exclusive trails, and share your adventures through reviews and experiences.
        </p>
        <div class="bg-white rounded-2xl p-8 shadow-xl border border-purple-100 max-w-md mx-auto mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Community Features</h3>
            <div class="space-y-4 text-left">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-gray-700">Follow trusted hiking organizations</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <span class="text-gray-700">Review trails you've experienced</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                        </svg>
                    </div>
                    <span class="text-gray-700">Access exclusive trails & content</span>
                </div>
            </div>
        </div>
        <a href="{{ route('community.index') }}" 
           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 shadow-lg text-lg">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Start Building Your Network
        </a>
    </div>
</div>
@endif

{{-- Trail Recommendations --}}
<div class="px-6 lg:px-8 py-10 bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-3">Trail Recommendations</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">Discover amazing hiking trails tailored to your preferences and current conditions</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Trail Card 1 --}}
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden group">
            <div class="relative h-48">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Mount_Pulag_Summit_2014.jpg/640px-Mount_Pulag_Summit_2014.jpg"
                    alt="Ambangeg Trail"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-4 left-4 right-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-white bg-green-600 px-2 py-1 rounded-full">Beginner-Friendly</span>
                        <span class="text-white text-sm">19° 🌤️</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-green-600 transition-colors duration-300">Ambangeg Trail</h3>
                <p class="text-sm text-gray-500 mb-3">Kabayan, Benguet, Philippines</p>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="flex text-yellow-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-600 ml-2">4.7 (80)</span>
                    </div>
                </div>
                <a href="#" class="block w-full bg-green-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:bg-green-700 transition-all duration-300">
                    View Details
                </a>
            </div>
        </div>

        {{-- Trail Card 2 --}}
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden group">
            <div class="relative h-48">
                <img src="https://www.choosephilippines.com/uploads/2020/02/18/Tinipak-River.jpg"
                    alt="Tinipak Trail"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-4 left-4 right-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-white bg-yellow-600 px-2 py-1 rounded-full">Moderate</span>
                        <span class="text-white text-sm">21° ☀️</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300">Tinipak Trail</h3>
                <p class="text-sm text-gray-500 mb-3">Tanay, Rizal, Philippines</p>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="flex text-yellow-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-600 ml-2">4.6 (180)</span>
                    </div>
                </div>
                <a href="#" class="block w-full bg-blue-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:bg-blue-700 transition-all duration-300">
                    View Details
                </a>
            </div>
        </div>

        {{-- Trail Card 3 --}}
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden group">
            <div class="relative h-48">
                <img src="https://upload.wikimedia.org/wikipedia/commons/3/30/Kidapawan_Lake_Agco.jpg"
                    alt="Kidapawan Trail"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-4 left-4 right-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-white bg-red-600 px-2 py-1 rounded-full">Advanced</span>
                        <span class="text-white text-sm">25° ☀️</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-red-600 transition-colors duration-300">Kidapawan Trail</h3>
                <p class="text-sm text-gray-500 mb-3">North Cotabato, Philippines</p>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="flex text-yellow-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-600 ml-2">4.3 (310)</span>
                    </div>
                </div>
                <a href="#" class="block w-full bg-red-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:bg-red-700 transition-all duration-300">
                    View Details
                </a>
            </div>
        </div>

    </div>
    
    <!-- View All Trails Button -->
    <div class="text-center mt-8">
        <a href="{{ route('explore') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Explore More Trails
        </a>
    </div>
</div>

<!-- Floating Action Button for Hiking Tools -->
@if(isset($user) && $user && $user->user_type === 'hiker')
<div class="fixed bottom-4 right-4 md:bottom-6 md:right-6 z-50">
    <div class="relative group">
        <!-- Main FAB -->
        <button id="hiking-tools-fab" class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-110 flex items-center justify-center">
            <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
        </button>
        
        <!-- Tooltip (Hidden on mobile) -->
        <div class="absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap hidden md:block">
            Hiking Tools
            <div class="absolute top-full right-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
        </div>
        
        <!-- Quick Actions Menu -->
        <div id="quick-actions-menu" class="absolute bottom-full right-0 mb-4 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 md:block">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-3 md:p-4 min-w-[180px] md:min-w-[200px]">
                <div class="text-center mb-3">
                    <h4 class="font-semibold text-gray-800 text-sm md:text-base">Quick Access</h4>
                    <p class="text-xs text-gray-500">Essential hiking tools</p>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('assessment.instruction') }}" class="flex items-center p-2 md:p-3 rounded-xl hover:bg-green-50 transition-colors duration-200 group/item">
                        <div class="w-7 h-7 md:w-8 md:h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2 md:mr-3 group-hover/item:bg-green-200 transition-colors duration-200">
                            <svg class="w-3 h-3 md:w-4 md:h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-800 text-xs md:text-sm">Assessment</div>
                            <div class="text-xs text-gray-500">Check readiness</div>
                        </div>
                    </a>
                    <a href="{{ route('itinerary.build') }}" class="flex items-center p-2 md:p-3 rounded-xl hover:bg-blue-50 transition-colors duration-200 group/item">
                        <div class="w-7 h-7 md:w-8 md:h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2 md:mr-3 group-hover/item:bg-blue-200 transition-colors duration-200">
                            <svg class="w-3 h-3 md:w-4 md:h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-800 text-xs md:text-sm">Itinerary</div>
                            <div class="text-xs text-gray-500">Plan your hike</div>
                        </div>
                    </a>
                    <a href="{{ route('hiking-tools') }}" class="flex items-center p-2 md:p-3 rounded-xl hover:bg-gray-50 transition-colors duration-200 group/item">
                        <div class="w-7 h-7 md:w-8 md:h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-2 md:mr-3 group-hover/item:bg-gray-200 transition-colors duration-200">
                            <svg class="w-3 h-3 md:w-4 md:h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-800 text-xs md:text-sm">All Tools</div>
                            <div class="text-xs text-gray-500">Complete toolkit</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    fetch(`/location-weather?lat=${position.coords.latitude}&lon=${position.coords.longitude}`);
                });
            }
            
            // Add smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Add intersection observer for fade-in animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Observe all hiking tool cards and trail cards
            document.querySelectorAll('.hiking-tool-card, .trail-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
                observer.observe(card);
            });
        });
    </script>
@endif