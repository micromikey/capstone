<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hiker Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="relative">
                    <!-- Cover Image Placeholder -->
                    <div class="h-48 bg-gradient-to-r from-green-400 to-blue-500"></div>

                    <!-- Profile Picture and Basic Info -->
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <div class="flex items-end space-x-6">
                            <div class="relative">
                                <img src="{{ $user->profile_picture_url }}"
                                    alt="{{ $user->name }}"
                                    class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                                <div class="absolute -bottom-2 -right-2 bg-green-500 rounded-full p-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 text-white">
                                <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                                <p class="text-white/90">{{ $user->email }}</p>
                                @if($user->location)
                                <p class="text-white/90 flex items-center mt-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $user->location }}
                                </p>
                                @endif
                            </div>
                            <div class="text-right text-white">
                                <a href="{{ route('profile.edit') }}"
                                    class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Completion Bar -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Profile Completion</h3>
                        <span class="text-sm text-gray-500">{{ $user->profile_completion_percentage }}% Complete</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full transition-all duration-300"
                            style="width: {{ $user->profile_completion_percentage }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Complete your profile to unlock more features!</p>
                </div>
            </div>

            <!-- Profile Information Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Personal Information -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Personal Information
                        </h3>

                        <div class="space-y-4">
                            @if($user->phone)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="text-gray-900">{{ $user->phone }}</span>
                            </div>
                            @endif

                            @if($user->birth_date)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-900">{{ $user->birth_date->format('F j, Y') }} ({{ $user->age }} years old)</span>
                            </div>
                            @endif

                            @if($user->gender)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-gray-900">{{ ucfirst($user->gender) }}</span>
                            </div>
                            @endif

                            @if($user->bio)
                            <div>
                                <p class="text-gray-900">{{ $user->bio }}</p>
                            </div>
                            @endif
                        </div>

                        @if(!$user->phone || !$user->bio || !$user->location)
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-700">Complete your profile by adding missing information.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Hiking Preferences -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Hiking Preferences
                        </h3>

                        @if($user->hiking_preferences && count($user->hiking_preferences) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->hiking_preferences as $preference)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                {{ $preference }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 text-sm">No hiking preferences set yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Emergency Contacts -->
                @if($user->emergency_contact_name)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            Emergency Contact
                        </h3>

                        <div class="space-y-2">
                            <p class="text-gray-900"><strong>{{ $user->emergency_contact_name }}</strong></p>
                            <p class="text-gray-600">{{ $user->emergency_contact_phone }}</p>
                            <p class="text-gray-600">{{ $user->emergency_contact_relationship }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Account Status -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Account Status
                        </h3>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Email Verification</span>
                                @if($user->hasVerifiedEmail())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Verified
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Member Since</span>
                                <span class="text-gray-900">{{ $user->created_at->format('F Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Hiking Tools & Results Section -->
            <div class="mb-8">
                <!-- Section Header with Decorative Elements -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-400 to-blue-500 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-2">Your Hiking Adventure Tools</h3>
                    <p class="text-gray-600 text-lg">Track your progress and plan your next adventure</p>
                </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                                 <!-- Enhanced Assessment Results -->
                         <div class="bg-gradient-to-br from-white via-green-50 to-emerald-50 overflow-hidden shadow-2xl sm:rounded-2xl border border-green-200 relative">
                             <!-- Decorative background elements -->
                             <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-400 to-emerald-400 rounded-full -translate-y-16 translate-x-16 opacity-10"></div>
                             <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-green-300 to-emerald-300 rounded-full translate-y-12 -translate-x-12 opacity-10"></div>
                             
                             <div class="p-8 relative z-10">
                                 <!-- Header with Icon and Title -->
                                 <div class="flex items-center mb-6">
                                     <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                                         <span class="text-3xl">🥾</span>
                                     </div>
                                     <div>
                                         <h4 class="text-xl font-bold text-gray-900">Pre-Hike Self-Assessment</h4>
                                         <p class="text-green-600 text-sm font-medium">Your readiness evaluation results</p>
                                     </div>
                                 </div>

                                @if($user->latestAssessmentResult)
                                <div class="space-y-6">
                                                                         <!-- Main Score Display -->
                                     <div class="relative p-6 bg-gradient-to-r from-green-600 via-emerald-600 to-green-700 rounded-2xl text-white overflow-hidden shadow-xl">
                                         <div class="absolute top-0 right-0 w-32 h-32 bg-white/20 rounded-full -translate-y-16 translate-x-16"></div>
                                         <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12"></div>
                                         <div class="relative z-10">
                                             <div class="flex items-center justify-between mb-3">
                                                 <span class="text-sm font-medium text-green-100 bg-white/20 px-3 py-1 rounded-full">Latest Assessment</span>
                                                 <span class="text-xs text-green-200 bg-white/10 px-2 py-1 rounded-full">{{ $user->latestAssessmentResult->completed_at->diffForHumans() }}</span>
                                             </div>
                                             <div class="flex items-center gap-4">
                                                 <div class="text-center">
                                                     <div class="text-6xl font-bold text-white drop-shadow-lg">{{ $user->latestAssessmentResult->overall_score }}%</div>
                                                     <div class="text-sm text-green-100 font-medium">Overall Score</div>
                                                 </div>
                                                 <div class="flex-1">
                                                     <div class="text-xl font-bold text-white mb-1">{{ $user->latestAssessmentResult->readiness_level }}</div>
                                                     <div class="text-2xl">{{ $user->latestAssessmentResult->readiness_level_icon }}</div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>

                                                                         <!-- Detailed Scores Grid -->
                                     <div class="grid grid-cols-3 gap-4">
                                         <div class="text-center p-4 bg-gradient-to-br from-white to-green-50 rounded-xl border border-green-200 shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                             <div class="text-2xl font-bold text-green-600 mb-1">{{ $user->latestAssessmentResult->gear_score }}%</div>
                                             <div class="text-sm font-medium text-gray-700 mb-2">Gear</div>
                                             <div class="w-full bg-gray-200 rounded-full h-2">
                                                 <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $user->latestAssessmentResult->gear_score }}%"></div>
                                             </div>
                                         </div>
                                         <div class="text-center p-4 bg-gradient-to-br from-white to-green-50 rounded-xl border border-green-200 shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                             <div class="text-2xl font-bold text-green-600 mb-1">{{ $user->latestAssessmentResult->fitness_score }}%</div>
                                             <div class="text-sm font-medium text-gray-700 mb-2">Fitness</div>
                                             <div class="w-full bg-gray-200 rounded-full h-2">
                                                 <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $user->latestAssessmentResult->fitness_score }}%"></div>
                                             </div>
                                         </div>
                                         <div class="text-center p-4 bg-gradient-to-br from-white to-green-50 rounded-xl border border-green-200 shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                             <div class="text-2xl font-bold text-green-600 mb-1">{{ $user->latestAssessmentResult->health_score }}%</div>
                                             <div class="text-sm font-medium text-gray-700 mb-2">Health</div>
                                             <div class="w-full bg-gray-200 rounded-full h-2">
                                                 <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $user->latestAssessmentResult->health_score }}%"></div>
                                             </div>
                                         </div>
                                         <div class="text-center p-4 bg-gradient-to-br from-white to-green-50 rounded-xl border border-green-200 shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                             <div class="text-2xl font-bold text-green-600 mb-1">{{ $user->latestAssessmentResult->weather_score }}%</div>
                                             <div class="text-sm font-medium text-gray-700 mb-2">Weather</div>
                                             <div class="w-full bg-gray-200 rounded-full h-2">
                                                 <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $user->latestAssessmentResult->weather_score }}%"></div>
                                             </div>
                                         </div>
                                         <div class="text-center p-4 bg-gradient-to-br from-white to-green-50 rounded-xl border border-green-200 shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                             <div class="text-2xl font-bold text-green-600 mb-1">{{ $user->latestAssessmentResult->emergency_score }}%</div>
                                             <div class="text-sm font-medium text-gray-700 mb-2">Emergency</div>
                                             <div class="w-full bg-gray-200 rounded-full h-2">
                                                 <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $user->latestAssessmentResult->emergency_score }}%"></div>
                                             </div>
                                         </div>
                                         <div class="text-center p-4 bg-gradient-to-br from-white to-green-50 rounded-xl border border-green-200 shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                             <div class="text-2xl font-bold text-green-600 mb-1">{{ $user->latestAssessmentResult->environment_score }}%</div>
                                             <div class="text-sm font-medium text-gray-700 mb-2">Environment</div>
                                             <div class="w-full bg-gray-200 rounded-full h-2">
                                                 <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $user->latestAssessmentResult->environment_score }}%"></div>
                                             </div>
                                         </div>
                                     </div>

                                                                         <!-- Action Buttons -->
                                     <div class="space-y-3">
                                         <a href="{{ route('assessment.saved-results') }}" class="block w-full text-center bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                                             <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                             </svg>
                                             View Detailed Results
                                         </a>
                                         <a href="{{ route('assessment.instruction') }}" class="block w-full text-center bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                                             <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                             </svg>
                                             Retake Assessment
                                         </a>
                                     </div>
                                </div>
                                @else
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-3xl">📋</span>
                                    </div>
                                    <h5 class="text-lg font-semibold text-gray-700 mb-2">No Assessment Yet</h5>
                                    <p class="text-gray-500 mb-6">Complete your first assessment to see your hiking readiness score</p>
                                    <a href="{{ route('assessment.instruction') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                                        Start Assessment
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Enhanced Itineraries -->
                        <div class="bg-gradient-to-br from-white to-blue-50 overflow-hidden shadow-2xl sm:rounded-2xl border border-blue-100">
                            <div class="p-8">
                                <!-- Header with Icon and Title -->
                                <div class="flex items-center mb-6">
                                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-4">
                                        <span class="text-2xl">🗺️</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-900">Personalized Itineraries</h4>
                                        <p class="text-blue-600 text-sm">Your custom hiking plans</p>
                                    </div>
                                </div>

                                @if($user->latestItinerary)
                                <div class="space-y-6">
                                    <!-- Main Itinerary Display -->
                                    <div class="relative p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl text-white overflow-hidden">
                                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
                                        <div class="relative z-10">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="text-sm font-medium text-blue-100">Latest Itinerary</span>
                                                <span class="text-xs text-blue-200">{{ $user->latestItinerary->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="space-y-3">
                                                <h5 class="text-xl font-bold">{{ $user->latestItinerary->title }}</h5>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-blue-200">{{ $user->latestItinerary->trail_name }}</span>
                                                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">{{ $user->latestItinerary->difficulty_level }}</span>
                                                </div>
                                                <div class="flex items-center gap-4 text-sm text-blue-200">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                        </svg>
                                                        {{ $user->latestItinerary->distance }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ $user->latestItinerary->estimated_duration }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Stats -->
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                                            <div class="text-lg font-bold text-blue-600">{{ $user->latestItinerary->difficulty_level }}</div>
                                            <div class="text-xs text-gray-600">Difficulty</div>
                                        </div>
                                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                                            <div class="text-lg font-bold text-blue-600">{{ $user->latestItinerary->distance }}</div>
                                            <div class="text-xs text-gray-600">Distance</div>
                                        </div>
                                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                                            <div class="text-lg font-bold text-blue-600">{{ $user->latestItinerary->estimated_duration }}</div>
                                            <div class="text-xs text-gray-600">Duration</div>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <a href="{{ route('itinerary.build') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Create New Itinerary
                                    </a>
                                </div>
                                @else
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-3xl">📝</span>
                                    </div>
                                    <h5 class="text-lg font-semibold text-gray-700 mb-2">No Itineraries Yet</h5>
                                    <p class="text-gray-500 mb-6">Create your first personalized hiking itinerary</p>
                                    <a href="{{ route('itinerary.build') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                                        Build Itinerary
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

</x-app-layout>