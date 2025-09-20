<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    PERSONALIZED ITINERARY BUILDER
                </h2>
                <h1 class="text-gray-600 text-lg font-medium flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-800 text-sm font-bold rounded-full">0</span>
                    Instructions & Overview
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-4">

        <!-- Intro Card -->
        <div class="bg-gradient-to-br from-white via-green-50 to-blue-50 rounded-2xl shadow-lg p-10 border border-green-100 text-center">
            <div class="mb-8 relative flex justify-center">
                <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-blue-400 rounded-full blur-3xl opacity-20 animate-pulse"></div>
                <span class="text-7xl animate-bounce relative z-10 drop-shadow-lg">🗺️</span>
            </div>
            <h1 class="text-4xl font-bold mb-6 bg-gradient-to-r from-gray-800 via-green-900 to-blue-800 bg-clip-text text-transparent">
                Personalized Itinerary Builder
            </h1>
            <p class="text-lg text-gray-700 mb-6 leading-relaxed font-medium">
                Create your perfect hiking adventure with our intelligent planning system. Customize every aspect of your journey across <strong class="text-green-600">seven key planning areas</strong>:
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 text-sm font-semibold justify-center">
                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full">🏔️ Mountain Selection</span>
                <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full">⏰ Time Planning</span>
                <span class="bg-purple-100 text-purple-800 px-4 py-2 rounded-full">📅 Date Setting</span>
                <span class="bg-cyan-100 text-cyan-800 px-4 py-2 rounded-full">📍 Location Setup</span>
                <span class="bg-orange-100 text-orange-800 px-4 py-2 rounded-full">🛤️ Route Planning</span>
                <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full">🚗 Transportation</span>
                <span class="bg-red-100 text-red-800 px-4 py-2 rounded-full">🎯 Trail Matching</span>
            </div>
            <p class="text-md text-gray-600 mt-8 leading-relaxed">
                Generate comprehensive travel plans with optimized routes, safety protocols, and emergency contacts tailored to your assessment results.
            </p>
        </div>

        <!-- How It Works Section -->
        <div class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 border-l-4 border-green-400 rounded-2xl shadow-lg p-10 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-200 to-emerald-200 rounded-full opacity-20 -mr-16 -mt-16"></div>
            <div class="flex items-start relative z-10">
                <div class="mr-8 p-4 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full shadow-lg flex items-center justify-center h-14 w-14">
                    <span class="text-3xl">🎯</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        How the Itinerary Builder Works
                        <span class="ml-3 text-sm bg-green-200 text-green-800 px-3 py-1 rounded-full font-medium">INTELLIGENT</span>
                    </h3>
                    <p class="text-gray-700 mb-6 text-lg leading-relaxed">
                        <strong>Our advanced planning system generates customized travel plans based on your preferences and assessment results.</strong>
                        Every itinerary is tailored to your physical readiness, equipment level, and chosen adventure parameters.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="group hover:scale-105 transition-all duration-300">
                            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl border border-green-200 h-full flex flex-col justify-between">
                                <div>
                                    <div class="text-3xl mb-4 group-hover:animate-pulse">🏔️</div>
                                    <h4 class="font-bold text-gray-800 mb-3 text-lg">Smart Mountain Matching</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        Select from curated mountain destinations matched to your fitness level and trail preferences from your pre-hike assessment.
                                    </p>
                                </div>
                                <div class="mt-4 flex items-center text-xs text-green-700">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Assessment-Based Recommendations
                                </div>
                            </div>
                        </div>
                        <div class="group hover:scale-105 transition-all duration-300">
                            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl border border-blue-200 h-full flex flex-col justify-between">
                                <div>
                                    <div class="text-3xl mb-4 group-hover:animate-pulse">🗺️</div>
                                    <h4 class="font-bold text-gray-800 mb-3 text-lg">Optimized Route Planning</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        Get detailed travel routes with timing, transportation options, and optional stopovers to maximize your adventure experience.
                                    </p>
                                </div>
                                <div class="mt-4 flex items-center text-xs text-blue-700">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    Multi-Modal Transportation
                                </div>
                            </div>
                        </div>
                        <div class="group hover:scale-105 transition-all duration-300">
                            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl border border-purple-200 h-full flex flex-col justify-between">
                                <div>
                                    <div class="text-3xl mb-4 group-hover:animate-pulse">🛡️</div>
                                    <h4 class="font-bold text-gray-800 mb-3 text-lg">Comprehensive Safety Integration</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        Every itinerary includes safety reminders, emergency contacts, and risk assessments based on current conditions and your readiness level.
                                    </p>
                                </div>
                                <div class="mt-4 flex items-center text-xs text-purple-700">
                                    <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                                    Real-Time Safety Updates
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 bg-gradient-to-r from-green-100 to-emerald-100 border border-green-300 rounded-lg p-4">
                        <p class="text-sm text-gray-700 flex items-center">
                            <span class="mr-2 text-green-600">💡</span>
                            <strong>Smart Planning: </strong> 
                            Our system reduces planning time by 85% while ensuring 100% safety compliance with your assessment results.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Planning Features Section -->
        <div class="bg-gradient-to-br from-white to-green-50 rounded-2xl shadow-lg p-10 border border-green-100">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span class="mr-4 p-2 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow-md">
                        <span class="text-2xl text-white">⚙️</span>
                    </span>
                    Customization Features
                </h3>
                <div class="bg-gradient-to-r from-green-600 to-emerald-700 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                    Fully Customizable
                </div>
            </div>
            <p class="text-gray-600 mb-8 text-lg leading-relaxed text-left">
                Tailor every aspect of your hiking adventure with our comprehensive planning tools,
                designed to create <strong>personalized experiences</strong> that match your <strong>skill level and preferences</strong>.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $planningFeatures = [
                        [
                            'name' => 'Mountain Selection',
                            'icon' => '🏔️',
                            'description' => 'Choose from curated mountain destinations based on your assessment grade and experience level',
                            'features' => ['Difficulty matching', 'Scenic preferences', 'Trail type options', 'Accessibility levels'],
                            'color' => '#10b981',
                            'priority' => 'PRIMARY'
                        ],
                        [
                            'name' => 'Time & Date Planning',
                            'icon' => '📅',
                            'description' => 'Set your preferred hiking schedule with intelligent timing recommendations',
                            'features' => ['Optimal departure times', 'Weather windows', 'Seasonal considerations', 'Duration planning'],
                            'color' => '#3b82f6',
                            'priority' => 'ESSENTIAL'
                        ],
                        [
                            'name' => 'Location & Routes',
                            'icon' => '📍',
                            'description' => 'Configure starting points and destinations with optimized routing options',
                            'features' => ['GPS coordinates', 'Multiple start points', 'Route optimization', 'Distance calculations'],
                            'color' => '#8b5cf6',
                            'priority' => 'CRITICAL'
                        ],
                        [
                            'name' => 'Transportation Options',
                            'icon' => '🚗',
                            'description' => 'Select and plan your travel methods with detailed logistics support',
                            'features' => ['Multi-modal transport', 'Cost estimates', 'Time calculations', 'Booking assistance'],
                            'color' => '#f59e0b',
                            'priority' => 'SUPPORT'
                        ],
                        [
                            'name' => 'Stopover Planning',
                            'icon' => '🛤️',
                            'description' => 'Add optional stops and side trips to enhance your adventure experience',
                            'features' => ['Scenic viewpoints', 'Rest stations', 'Supply points', 'Cultural sites'],
                            'color' => '#ef4444',
                            'priority' => 'OPTIONAL'
                        ],
                        [
                            'name' => 'Trail Recommendations',
                            'icon' => '🎯',
                            'description' => 'Get personalized trail suggestions based on your pre-hike assessment results',
                            'features' => ['Skill-based matching', 'Difficulty progression', 'Safety ratings', 'Condition updates'],
                            'color' => '#06b6d4',
                            'priority' => 'INTELLIGENT'
                        ],
                        [
                            'name' => 'Safety Integration',
                            'icon' => '🛡️',
                            'description' => 'Comprehensive safety planning with emergency protocols and contacts',
                            'features' => ['Emergency contacts', 'Safety checkpoints', 'Risk assessments', 'Weather alerts'],
                            'color' => '#dc2626',
                            'priority' => 'CRITICAL'
                        ],
                        [
                            'name' => 'Custom Preferences',
                            'icon' => '⚡',
                            'description' => 'Fine-tune your experience with advanced customization options',
                            'features' => ['Activity preferences', 'Group size settings', 'Equipment matching', 'Experience goals'],
                            'color' => '#7c3aed',
                            'priority' => 'ADVANCED'
                        ]
                    ];
                @endphp
                @foreach($planningFeatures as $feature)
                    <div class="group border rounded-xl p-6 hover:shadow-xl transition-all duration-300 bg-white hover:scale-105 relative overflow-hidden flex flex-col justify-between col-span-1 md:col-span-1">
                        <div class="flex flex-col mb-4 relative z-10">
                            <div class="p-3 rounded-lg mb-4 shadow-md self-start" style="background: linear-gradient(135deg, {{ $feature['color'] }}, {{ $feature['color'] }}aa);">
                                <span class="text-2xl text-white drop-shadow">{{ $feature['icon'] }}</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 mb-2 text-lg group-hover:text-green-700 transition-colors">{{ $feature['name'] }}</h4>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                        @if($feature['priority'] === 'CRITICAL') bg-red-100 text-red-800 border border-red-200
                                        @elseif($feature['priority'] === 'PRIMARY') bg-green-100 text-green-800 border border-green-200
                                        @elseif($feature['priority'] === 'ESSENTIAL') bg-blue-100 text-blue-800 border border-blue-200
                                        @elseif($feature['priority'] === 'INTELLIGENT') bg-cyan-100 text-cyan-800 border border-cyan-200
                                        @elseif($feature['priority'] === 'ADVANCED') bg-purple-100 text-purple-800 border border-purple-200
                                        @elseif($feature['priority'] === 'SUPPORT') bg-yellow-100 text-yellow-800 border border-yellow-200
                                        @else bg-gray-100 text-gray-800 border border-gray-200
                                        @endif">
                                        {{ $feature['priority'] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 leading-relaxed mb-4 text-left">{{ $feature['description'] }}</p>
                                <ul class="space-y-1">
                                    @foreach($feature['features'] as $featureItem)
                                        <li class="text-xs text-gray-500 flex items-center">
                                            <span class="w-1 h-1 rounded-full mr-2" style="background-color: {{ $feature['color'] }};"></span>
                                            {{ $featureItem }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Assessment Requirement Notice -->
        <div class="bg-gradient-to-br from-yellow-50 via-orange-50 to-red-50 border-l-4 border-yellow-400 rounded-2xl shadow-lg p-10 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-yellow-200 to-orange-200 rounded-full opacity-20 -mr-16 -mt-16"></div>
            <div class="flex items-start relative z-10">
                <div class="mr-8 p-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full shadow-lg flex items-center justify-center h-14 w-14">
                    <span class="text-3xl">📋</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        Pre-Assessment Requirement
                        <span class="ml-3 text-sm bg-yellow-200 text-yellow-800 px-3 py-1 rounded-full font-medium">MANDATORY</span>
                    </h3>
                    <p class="text-gray-700 mb-6 text-lg leading-relaxed">
                        <strong>To access the Personalized Itinerary Builder, you must first complete the Pre-Hike Self-Assessment Tool.</strong>
                        This ensures your itinerary is not only tailored to your preferences but also aligned with your physical readiness, equipment level, and safety requirements.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="bg-white rounded-xl p-6 shadow-md border border-yellow-200">
                            <div class="text-3xl mb-4">🎯</div>
                            <h4 class="font-bold text-gray-800 mb-3 text-lg">Why Assessment First?</h4>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-start">
                                    <span class="mr-2 text-yellow-600">•</span>
                                    Ensures trail recommendations match your skill level
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2 text-yellow-600">•</span>
                                    Validates equipment readiness for chosen destinations
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2 text-yellow-600">•</span>
                                    Identifies potential safety gaps before planning
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2 text-yellow-600">•</span>
                                    Provides personalized safety recommendations
                                </li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-xl p-6 shadow-md border border-green-200">
                            <div class="text-3xl mb-4">✅</div>
                            <h4 class="font-bold text-gray-800 mb-3 text-lg">What You Get</h4>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-start">
                                    <span class="mr-2 text-green-600">•</span>
                                    Trails matched to your fitness and experience level
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2 text-green-600">•</span>
                                    Safety protocols specific to your preparation level
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2 text-green-600">•</span>
                                    Equipment recommendations for your chosen route
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2 text-green-600">•</span>
                                    Emergency plans tailored to your risk profile
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- What You'll Get Section -->
        <div class="bg-white rounded-2xl shadow p-8 border border-gray-100">
            <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                <span class="mr-3">📊</span>
                Your Complete Itinerary Package
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <span class="mr-3 text-green-600">🗺️</span>
                        <p class="text-gray-700"><strong>Optimized Travel Routes:</strong> Turn-by-turn directions with timing and distance calculations</p>
                    </div>
                    <div class="flex items-start">
                        <span class="mr-3 text-green-600">⏰</span>
                        <p class="text-gray-700"><strong>Ideal Timing:</strong> Optimal departure and return times based on conditions and preferences</p>
                    </div>
                    <div class="flex items-start">
                        <span class="mr-3 text-green-600">🎯</span>
                        <p class="text-gray-700"><strong>Trail Recommendations:</strong> Personalized suggestions based on your assessment grade</p>
                    </div>
                    <div class="flex items-start">
                        <span class="mr-3 text-green-600">🛤️</span>
                        <p class="text-gray-700"><strong>Optional Side Trips:</strong> Scenic detours and points of interest along your route</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <span class="mr-3 text-blue-600">🚨</span>
                        <p class="text-gray-700"><strong>Safety Reminders:</strong> Personalized safety protocols and risk assessments</p>
                    </div>
                    <div class="flex items-start">
                        <span class="mr-3 text-blue-600">📞</span>
                        <p class="text-gray-700"><strong>Emergency Contacts:</strong> Relevant local emergency services and rescue contacts</p>
                    </div>
                    <div class="flex items-start">
                        <span class="mr-3 text-blue-600">🌤️</span>
                        <p class="text-gray-700"><strong>Weather Integration:</strong> Current conditions and forecasts for your chosen dates</p>
                    </div>
                    <div class="flex items-start">
                        <span class="mr-3 text-blue-600">📱</span>
                        <p class="text-gray-700"><strong>Mobile-Friendly Format:</strong> Access your itinerary offline on any device</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call-to-Action Section -->
        <div class="bg-gradient-to-br from-white via-green-50 to-emerald-50 rounded-2xl shadow-2xl p-10 border-2 border-green-200 relative overflow-hidden text-center">
            <div class="mb-8 flex justify-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full shadow-xl mb-4 animate-bounce">
                    <span class="text-4xl text-white">🚀</span>
                </div>
            </div>
            <h3 class="text-3xl font-bold mb-6 bg-gradient-to-r from-gray-800 via-green-700 to-emerald-700 bg-clip-text text-transparent">
                Ready to Plan Your Perfect Adventure?
            </h3>
            <p class="text-gray-700 mb-8 max-w-3xl mx-auto text-lg leading-relaxed">
                Complete your Pre-Hike Assessment first, then build a personalized itinerary that matches your readiness level and adventure goals.
            </p>
            <div class="space-y-4 flex flex-col items-center">
                <a href="{{ route('assessment.gear') }}"
                   class="inline-flex items-center px-10 py-5 text-white text-xl font-bold rounded-xl shadow-2xl transition-all duration-300 hover:shadow-3xl hover:scale-110 transform-gpu group relative overflow-hidden"
                   style="background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);">
                    <span class="relative z-10">Start Pre-Hike Assessment</span>
                    <svg class="ml-4 w-6 h-6 group-hover:translate-x-1 transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <p class="text-sm text-gray-500">
                    Already completed your assessment? 
                    <a href="{{ route('dashboard') }}" class="text-green-600 hover:text-green-700 font-semibold underline">
                        Build Your Itinerary Here
                    </a>
                </p>
            </div>
        </div>

        <!-- Planning Tips Section -->
        <div class="bg-white rounded-2xl shadow p-8 border border-gray-100">
            <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                <span class="mr-3">💡</span>
                Planning Tips for Best Results
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-700">Before You Start:</h4>
                    <div class="space-y-2">
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-green-500">•</span>
                            Complete your Pre-Hike Assessment for personalized recommendations
                        </p>
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-green-500">•</span>
                            Have your preferred dates and destinations in mind
                        </p>
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-green-500">•</span>
                            Consider your group size and experience levels
                        </p>
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-green-500">•</span>
                            Check current weather and trail conditions
                        </p>
                    </div>
                </div>
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-700">Using Your Itinerary:</h4>
                    <div class="space-y-2">
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-blue-500">•</span>
                            Download and save your itinerary for offline access
                        </p>
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-blue-500">•</span>
                            Share your plans with trusted contacts before departing
                        </p>
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-blue-500">•</span>
                            Keep emergency contact information easily accessible
                        </p>
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-blue-500">•</span>
                            Review safety reminders and adjust plans if needed
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Privacy Notice Section -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mt-10">
            <div class="flex items-start">
                <span class="mr-4 text-2xl">🔒</span>
                <div class="flex-1">
                    <h4 class="font-semibold text-green-900 mb-2">Privacy & Data Protection</h4>
                    <p class="text-green-800 text-sm leading-relaxed">
                        Your itinerary preferences and travel plans are kept <strong>completely confidential and secure</strong>. 
                        All data is used solely to generate your personalized itinerary and provide relevant safety information. 
                        We do not share, sell, or store your location data or travel plans beyond the scope of generating your itinerary. 
                        Your privacy and security remain our highest priority throughout the planning process.
                    </p>
                </div>
            </div>
        </div>
        
    </div>

    <style>
        .transition-all { transition: all 0.3s ease; }
        .hover\:scale-105:hover { transform: scale(1.05); }
        .animate-bounce { animation: bounce 1s infinite; }
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
            40%, 43% { transform: translateY(-30px); }
            70% { transform: translateY(-15px); }
            90% { transform: translateY(-4px); }
        }
        .hover\:shadow-md:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .hover\:shadow-xl:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .hover\:shadow-3xl:hover {
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
    </style>
</x-app-layout>