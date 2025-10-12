<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <span class="iconify text-emerald-600" data-icon="mdi:clipboard-check-outline" style="font-size: 2rem;"></span>
                        My Trail Assessments
                    </h1>
                    <p class="text-gray-600">View all emergency readiness feedback you've submitted for trails</p>
                </div>
                <div class="px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-200">
                    <span class="text-sm text-gray-600">Total Assessments: </span>
                    <strong class="text-lg text-emerald-600">{{ $feedbacks->total() }}</strong>
                </div>
            </div>

            @if($feedbacks->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-dashed border-gray-300 p-12 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 mx-auto mb-6 bg-emerald-50 rounded-full flex items-center justify-center">
                            <span class="iconify text-emerald-400" data-icon="mdi:clipboard-check-outline" style="font-size: 3rem;"></span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Assessments Yet</h3>
                        <p class="text-gray-600 mb-6">You haven't submitted any trail assessments yet. After completing a hike, you can share your experience about the trail's emergency readiness.</p>
                        <a href="{{ route('booking.index') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg shadow-lg hover:bg-emerald-700 transition-all transform hover:-translate-y-0.5">
                            <span class="iconify mr-2" data-icon="mdi:calendar-month" style="font-size: 1.25rem;"></span>
                            View My Bookings
                        </a>
                    </div>
                </div>
            @else
                <!-- Assessments List -->
                <div class="space-y-6">
                    @foreach($feedbacks as $feedback)
                        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                            <a href="{{ route('hiker.readiness.show', $feedback) }}" class="block p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <!-- Trail Name and Badge -->
                                        <div class="flex items-center gap-3 mb-3">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $feedback->trail->name }}
                                            </h3>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $feedback->readiness_badge_color }}">
                                                {{ $feedback->readiness_level }}
                                            </span>
                                        </div>

                                        <!-- Location -->
                                        <div class="flex items-center text-sm text-gray-600 mb-3">
                                            <span class="iconify mr-2" data-icon="mdi:map-marker" style="font-size: 1rem;"></span>
                                            {{ $feedback->trail->location }}
                                        </div>

                                        <!-- Scores Grid -->
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                                            <div class="bg-blue-50 rounded-lg p-3">
                                                <div class="text-xs text-gray-600 mb-1">First Aid</div>
                                                <div class="text-lg font-bold text-blue-600">{{ $feedback->first_aid_score }}</div>
                                            </div>
                                            <div class="bg-green-50 rounded-lg p-3">
                                                <div class="text-xs text-gray-600 mb-1">Equipment</div>
                                                <div class="text-lg font-bold text-green-600">{{ $feedback->equipment_score }}</div>
                                            </div>
                                            <div class="bg-purple-50 rounded-lg p-3">
                                                <div class="text-xs text-gray-600 mb-1">Staff Training</div>
                                                <div class="text-lg font-bold text-purple-600">{{ $feedback->staff_training_score }}</div>
                                            </div>
                                            <div class="bg-orange-50 rounded-lg p-3">
                                                <div class="text-xs text-gray-600 mb-1">Emergency Access</div>
                                                <div class="text-lg font-bold text-orange-600">{{ $feedback->emergency_access_score }}</div>
                                            </div>
                                        </div>

                                        <!-- Assessment Date -->
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span class="iconify mr-2" data-icon="mdi:calendar-month" style="font-size: 1rem;"></span>
                                            Assessed on {{ $feedback->assessment_date->format('F d, Y') }}
                                        </div>
                                    </div>

                                    <!-- Overall Score Circle -->
                                    <div class="ml-4 flex-shrink-0">
                                        <div class="relative w-24 h-24">
                                            <svg class="transform -rotate-90 w-24 h-24">
                                                <circle cx="48" cy="48" r="44" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200" />
                                                <circle cx="48" cy="48" r="44" stroke="currentColor" stroke-width="8" fill="none" 
                                                    class="@if($feedback->overall_score >= 85) text-green-500 @elseif($feedback->overall_score >= 70) text-blue-500 @elseif($feedback->overall_score >= 50) text-yellow-500 @else text-red-500 @endif"
                                                    stroke-dasharray="{{ 2 * 3.14159 * 44 }}"
                                                    stroke-dashoffset="{{ 2 * 3.14159 * 44 * (1 - $feedback->overall_score / 100) }}"
                                                    stroke-linecap="round" />
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <span class="text-2xl font-bold text-gray-800">{{ $feedback->overall_score }}</span>
                                            </div>
                                        </div>
                                        <div class="text-xs text-center text-gray-500 mt-1">Overall</div>
                                    </div>
                                </div>

                                <!-- Comments Preview -->
                                @if($feedback->comments)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-sm text-gray-600 line-clamp-2">
                                            <span class="font-medium">Your feedback:</span> {{ $feedback->comments }}
                                        </p>
                                    </div>
                                @endif

                                <!-- View Details Link -->
                                <div class="mt-4 flex items-center text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                    View Full Assessment
                                    <span class="iconify ml-1" data-icon="mdi:chevron-right" style="font-size: 1rem;"></span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $feedbacks->links() }}
                </div>
            @endif

            <!-- Info Banner -->
            <div class="mt-8 bg-gradient-to-r from-blue-50 to-emerald-50 rounded-xl p-6 border border-blue-200">
                <div class="flex items-start">
                    <span class="iconify text-blue-600 mr-3 flex-shrink-0 mt-0.5" data-icon="mdi:information" style="font-size: 1.5rem;"></span>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">Thank You for Your Feedback!</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Your trail assessments help organizations improve their emergency preparedness and safety measures. 
                            Trail managers review your feedback to make continuous improvements for all hikers.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
