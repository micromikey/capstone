<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-5xl mx-auto px-4 md:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('hiker.readiness.index') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to My Assessments
                </a>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6 border border-gray-100">
                <div class="bg-gradient-to-r from-emerald-600 to-green-600 p-6 text-white">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.818-4.818A4 4 0 1119 9m-7 7a2 2 0 100-4 2 2 0 000 4z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold mb-2">Trail Assessment Details</h1>
                                <p class="text-emerald-100">Emergency Readiness Feedback</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-sm">
                            {{ $readiness->readiness_level }}
                        </span>
                    </div>
                </div>
                
                <!-- Trail Info -->
                <div class="p-6 bg-gradient-to-r from-blue-50 to-emerald-50 border-b">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start flex-1">
                            <svg class="w-6 h-6 text-emerald-600 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $readiness->trail->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $readiness->trail->location }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Assessed on {{ $readiness->assessment_date->format('F d, Y \a\t g:i A') }}
                                </p>
                            </div>
                        </div>

                        <!-- Overall Score -->
                        <div class="ml-4">
                            <div class="relative w-28 h-28">
                                <svg class="transform -rotate-90 w-28 h-28">
                                    <circle cx="56" cy="56" r="50" stroke="currentColor" stroke-width="10" fill="none" class="text-gray-200" />
                                    <circle cx="56" cy="56" r="50" stroke="currentColor" stroke-width="10" fill="none" 
                                        class="@if($readiness->overall_score >= 85) text-green-500 @elseif($readiness->overall_score >= 70) text-blue-500 @elseif($readiness->overall_score >= 50) text-yellow-500 @else text-red-500 @endif"
                                        stroke-dasharray="{{ 2 * 3.14159 * 50 }}"
                                        stroke-dashoffset="{{ 2 * 3.14159 * 50 * (1 - $readiness->overall_score / 100) }}"
                                        stroke-linecap="round" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center flex-col">
                                    <span class="text-3xl font-bold text-gray-800">{{ $readiness->overall_score }}</span>
                                    <span class="text-xs text-gray-500">Overall</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Scores Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- First Aid Score -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">First Aid & Medical Preparedness</h3>
                        <span class="text-2xl font-bold text-blue-600">{{ $readiness->first_aid_score }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $readiness->first_aid_score }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Availability and quality of first aid kits, medical supplies, and emergency protocols</p>
                </div>

                <!-- Communication Score -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">Communication Systems</h3>
                        <span class="text-2xl font-bold text-purple-600">{{ $readiness->communication_score ?? 'N/A' }}</span>
                    </div>
                    @if($readiness->communication_score)
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-purple-600 h-3 rounded-full transition-all duration-500" style="width: {{ $readiness->communication_score }}%"></div>
                        </div>
                    @endif
                    <p class="text-sm text-gray-600 mt-2">Radios, mobile signal, emergency contact systems, and communication protocols</p>
                </div>

                <!-- Equipment Score -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">Safety Equipment</h3>
                        <span class="text-2xl font-bold text-green-600">{{ $readiness->equipment_score }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-600 h-3 rounded-full transition-all duration-500" style="width: {{ $readiness->equipment_score }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Quality and availability of safety gear and rescue equipment</p>
                </div>

                <!-- Staff Training Score -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">Staff Training</h3>
                        <span class="text-2xl font-bold text-orange-600">{{ $readiness->staff_training_score }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-orange-600 h-3 rounded-full transition-all duration-500" style="width: {{ $readiness->staff_training_score }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Staff emergency response training and preparedness</p>
                </div>

                <!-- Emergency Access Score -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">Emergency Access</h3>
                        <span class="text-2xl font-bold text-red-600">{{ $readiness->emergency_access_score }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-red-600 h-3 rounded-full transition-all duration-500" style="width: {{ $readiness->emergency_access_score }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Accessibility for emergency vehicles and evacuation routes</p>
                </div>
            </div>

            <!-- Comments and Notes -->
            @if($readiness->comments || $readiness->recommendations || $readiness->equipment_notes || $readiness->staff_notes || $readiness->communication_notes)
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Detailed Feedback</h3>
                    
                    @if($readiness->comments)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">General Comments</h4>
                            <p class="text-gray-600 bg-gray-50 p-4 rounded-lg">{{ $readiness->comments }}</p>
                        </div>
                    @endif

                    @if($readiness->recommendations)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Recommendations</h4>
                            <p class="text-gray-600 bg-blue-50 p-4 rounded-lg">{{ $readiness->recommendations }}</p>
                        </div>
                    @endif

                    @if($readiness->equipment_notes)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Equipment Notes</h4>
                            <p class="text-gray-600 bg-green-50 p-4 rounded-lg">{{ $readiness->equipment_notes }}</p>
                        </div>
                    @endif

                    @if($readiness->staff_notes)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Staff Notes</h4>
                            <p class="text-gray-600 bg-orange-50 p-4 rounded-lg">{{ $readiness->staff_notes }}</p>
                        </div>
                    @endif

                    @if($readiness->communication_notes)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Communication Notes</h4>
                            <p class="text-gray-600 bg-purple-50 p-4 rounded-lg">{{ $readiness->communication_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Organization Info -->
            @if($readiness->organization)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Trail Organization</h3>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $readiness->organization->name }}</p>
                            <p class="text-sm text-gray-500">Your feedback has been shared with this organization</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
