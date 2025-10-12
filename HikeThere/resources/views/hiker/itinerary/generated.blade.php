@php
/**
 * Refactored Generated Itinerary Blade
                     <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-teal-600 p-3 rounded-xl shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-xl font-bold bg-gradient-to-r from-blue-700 to-teal-700 bg-clip-text text-transparent">Pre-hike Transportation</h3>
                            <p class="text-sm text-blue-600 font-medium">Activities before the hiking adventure begins</p>
                        </div>
                    </div> view now uses service classes and components for better maintainability.
 * All complex logic has been moved to dedicated services.
 */

use App\Services\ItineraryGeneratorService;

// Handle variables that may not be passed from controller
$trail = $trail ?? null;
$build = $build ?? null;
$weatherData = $weatherData ?? [];

// Generate the complete itinerary data using the service
$itineraryService = app(ItineraryGeneratorService::class);
$generatedData = $itineraryService->generateItinerary($itinerary, $trail, $build, $weatherData);

// Extract the generated data for the view
$itinerary = $generatedData['itinerary'];
$trail = $generatedData['trail'];
$build = $generatedData['build'];
$weatherData = $generatedData['weatherData'];
$routeData = $generatedData['routeData'];
$dateInfo = $generatedData['dateInfo'];
$dayActivities = $generatedData['dayActivities'];
$nightActivities = $generatedData['nightActivities'];
$preHikeActivities = $generatedData['preHikeActivities'] ?? [];
$emergencyInfo = $generatedData['emergencyInfo'] ?? [];

// Ensure share token exists for sharing functionality
if (is_object($itinerary) && empty($itinerary->share_token)) {
    $itinerary->share_token = \Illuminate\Support\Str::random(32);
    $itinerary->save();
} elseif (is_array($itinerary) && empty($itinerary['share_token'])) {
    // If it's an array, we need to update the actual model
    $itineraryModel = \App\Models\Itinerary::find($itinerary['id'] ?? null);
    if ($itineraryModel && empty($itineraryModel->share_token)) {
        $itineraryModel->share_token = \Illuminate\Support\Str::random(32);
        $itineraryModel->save();
        $itinerary['share_token'] = $itineraryModel->share_token;
    }
}
@endphp

<x-app-layout>
    <!-- Activity Customization CSS -->
    <link rel="stylesheet" href="{{ asset('css/itinerary-customization.css') }}">
    
    <!-- Floating Navigation -->
    <x-floating-navigation :sections="[
        ['id' => 'itinerary-header', 'title' => 'Trail Overview', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 9m0 0V7m0 0l-4-3m4 3l4-3m-4 3v13\'></path>'],
        ['id' => 'trail-summary', 'title' => 'Trail Summary', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z\'></path>'],
        ['id' => 'emergency-info', 'title' => 'Emergency Info', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\'></path>'],
        ['id' => 'pre-hike-activities', 'title' => 'Pre-hike Transport', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'></path>'],
        ['id' => 'daily-itinerary', 'title' => 'Daily Itinerary', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z\'></path>'],
        ['id' => 'additional-info', 'title' => 'Additional Info', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path>']
    ]" />

	<!-- Enhanced gradient background with nature theme -->
    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-blue-50 to-teal-50" data-itinerary-id="{{ is_object($itinerary) ? $itinerary->id : ($itinerary['id'] ?? 0) }}">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div id="itinerary-header" class="bg-white/90 backdrop-blur-sm shadow-xl rounded-2xl border border-emerald-100 p-8 mb-8">
            <!-- Header: Trail Route & Details -->
            <x-itinerary.header 
                :trail="$trail" 
                :dateInfo="$dateInfo" 
                :routeData="$routeData" 
            />

            <!-- Trail Summary Boxes -->
            <div id="trail-summary">
                <x-itinerary.summary-boxes 
                    :trail="$trail" 
                    :routeData="$routeData" 
                    :build="$build" 
                    :staticMapUrl="$generatedData['staticMapUrl'] ?? null"
                />
            </div>

            <!-- Emergency Information -->
            @if (!empty($emergencyInfo))
            <div id="emergency-info" class="mb-8">
                <x-itinerary.emergency-info 
                    :emergencyInfo="$emergencyInfo" 
                    :trail="$trail"
                    :user="auth()->user()"
                />
            </div>
            @endif

            <!-- Pre-hike Transportation Activities -->
            @if (!empty($preHikeActivities))
                <div id="pre-hike-activities" class="bg-gradient-to-r from-blue-50 to-teal-50 border-2 border-blue-200/60 rounded-2xl p-8 mb-8 shadow-lg backdrop-blur-sm">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-blue-900">🚌 Pre-hike Transportation</h3>
                            <p class="text-sm text-blue-700">Activities before the hiking adventure begins</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl shadow-inner">
                        <table class="min-w-full divide-y divide-teal-200 rounded-xl overflow-hidden">
                            <thead class="bg-gradient-to-r from-emerald-500 to-teal-600">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">Activity</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white/80 backdrop-blur-sm divide-y divide-emerald-100">
                                @foreach ($preHikeActivities as $activity)
                                    <tr class="hover:bg-gradient-to-r hover:from-emerald-25 hover:to-teal-25 transition-all duration-200 border-b border-emerald-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-800 bg-emerald-50/50 rounded-l-lg">
                                            @php
                                                $minutes = $activity['minutes'] ?? 0;
                                                $isMultiDay = $minutes >= 1440; // 24 hours or more
                                                
                                                if ($isMultiDay) {
                                                    // For multi-day: get actual time on previous day (modulo 24 hours)
                                                    $actualMinutes = $minutes % 1440;
                                                    $hours = intval($actualMinutes / 60);
                                                    $mins = $actualMinutes % 60;
                                                    $dayLabel = ' (Day Before)';
                                                } else {
                                                    // For same day: normal calculation
                                                    $hours = intval($minutes / 60);
                                                    $mins = $minutes % 60;
                                                    $dayLabel = '';
                                                }
                                                
                                                $timeDisplay = sprintf('%02d:%02d%s', $hours, $mins, $dayLabel);
                                            @endphp
                                            <span class="bg-emerald-600 text-white px-2 py-1 rounded-md text-xs font-medium">{{ $timeDisplay }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800">
                                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">{{ $activity['title'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-teal-700 font-medium">
                                            📍 {{ $activity['location'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 bg-slate-50/50 rounded-r-lg">
                                            {{ $activity['description'] ?? '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Itinerary Tables per day - Full Width Container -->
    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-blue-50 to-teal-50">
        <div class="max-w-full mx-auto py-8 px-6 sm:px-8 lg:px-12">
            <div id="daily-itinerary" class="space-y-8">
                @for ($day = 1; $day <= $dateInfo['duration_days']; $day++)
                    <x-itinerary.day-table 
                        :day="$day"
                        :activities="$dayActivities[$day] ?? []"
                        :dateInfo="$dateInfo"
                        :weatherData="$weatherData"
                        :build="$build"
                        :trail="$trail"
                    />

                    {{-- Insert Night table after each day (except the last day) --}}
                    @if ($day <= $dateInfo['nights'])
                        <x-itinerary.night-table 
                            :night="$day"
                            :activities="$nightActivities[$day] ?? []"
                            :dateInfo="$dateInfo"
                            :weatherData="$weatherData"
                            :build="$build"
                            :trail="$trail"
                        />
                    @endif
                @endfor
            </div>
        </div>
    </div>

    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-blue-50 to-teal-50">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div id="additional-info" class="bg-white/90 backdrop-blur-sm shadow-xl rounded-2xl border border-emerald-100 p-8">
            <x-itinerary.additional-info 
                :trail="$trail" 
                :build="$build" 
            />
            </div>
        </div>

        <!-- Action Buttons Section -->
        <div id="itinerary-actions" class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white/90 backdrop-blur-sm shadow-xl rounded-2xl border border-emerald-100 p-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('itinerary.print', is_object($itinerary) ? $itinerary->id : ($itinerary['id'] ?? 0)) }}" target="_blank" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition-colors text-center inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Itinerary
                    </a>
                    <a href="{{ route('itinerary.ical', is_object($itinerary) ? $itinerary->id : ($itinerary['id'] ?? 0)) }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-6 rounded-lg font-medium transition-colors text-center inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Add to Calendar
                    </a>
                    <button id="share-itinerary-btn" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-3 px-6 rounded-lg font-medium transition-colors inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        Share Itinerary
                    </button>
                    <button id="book-trail-btn" 
                        data-trail-id="{{ is_array($trail) ? ($trail['id'] ?? '') : ($trail->id ?? '') }}" 
                        data-trail-slug="{{ is_array($trail) ? ($trail['slug'] ?? '') : ($trail->slug ?? '') }}" 
                        data-organization-id="{{ is_array($trail) ? ($trail['user_id'] ?? '') : ($trail->user_id ?? '') }}" 
                        data-organization-name="{{ is_array($trail) ? (($trail['user']['display_name'] ?? $trail['user']['name'] ?? '')) : (($trail->user->display_name ?? $trail->user->name ?? '')) }}" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Book This Trail
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Actions Bar -->
    <div id="floating-itinerary-actions" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg transform translate-y-full transition-transform duration-300 z-40">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row gap-4 p-4">
                <a href="{{ route('itinerary.print', is_object($itinerary) ? $itinerary->id : ($itinerary['id'] ?? 0)) }}" target="_blank" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition-colors text-center inline-flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Itinerary
                </a>
                <a href="{{ route('itinerary.ical', is_object($itinerary) ? $itinerary->id : ($itinerary['id'] ?? 0)) }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-6 rounded-lg font-medium transition-colors text-center inline-flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Add to Calendar
                </a>
                <button id="floating-share-itinerary-btn" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-3 px-6 rounded-lg font-medium transition-colors inline-flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    Share Itinerary
                </button>
                <button id="floating-book-trail-btn" 
                    data-trail-id="{{ is_array($trail) ? ($trail['id'] ?? '') : ($trail->id ?? '') }}" 
                    data-trail-slug="{{ is_array($trail) ? ($trail['slug'] ?? '') : ($trail->slug ?? '') }}" 
                    data-organization-id="{{ is_array($trail) ? ($trail['user_id'] ?? '') : ($trail->user_id ?? '') }}" 
                    data-organization-name="{{ is_array($trail) ? (($trail['user']['display_name'] ?? $trail['user']['name'] ?? '')) : (($trail->user->display_name ?? $trail->user->name ?? '')) }}" 
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Book This Trail
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const originalButtons = document.getElementById('itinerary-actions');
            const floatingButtons = document.getElementById('floating-itinerary-actions');
            
            function checkButtonsVisibility() {
                if (!originalButtons || !floatingButtons) return;
                
                const rect = originalButtons.getBoundingClientRect();
                const isOriginalVisible = rect.bottom > 0 && rect.top < window.innerHeight;
                
                if (isOriginalVisible) {
                    // Hide floating buttons when original is visible
                    floatingButtons.classList.add('translate-y-full');
                } else {
                    // Show floating buttons when original is not visible
                    floatingButtons.classList.remove('translate-y-full');
                }
            }
            
            // Check on scroll
            window.addEventListener('scroll', checkButtonsVisibility);
            
            // Check on resize
            window.addEventListener('resize', checkButtonsVisibility);
            
            // Initial check
            checkButtonsVisibility();
            
            // Book Trail buttons
            const bookBtns = [document.getElementById('book-trail-btn'), document.getElementById('floating-book-trail-btn')];
            bookBtns.forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', function() {
                        const trailId = btn.getAttribute('data-trail-id');
                        const trailSlug = btn.getAttribute('data-trail-slug');
                        const orgId = btn.getAttribute('data-organization-id');
                        const orgName = btn.getAttribute('data-organization-name');
                        
                        if (!trailId || !orgId) {
                            showNotification('Trail or organization information not found', 'error');
                            return;
                        }
                        
                        // Build the booking URL with pre-populated data
                        const bookingUrl = new URL('{{ route("booking.details") }}', window.location.origin);
                        bookingUrl.searchParams.set('trail_id', trailId);
                        bookingUrl.searchParams.set('organization_id', orgId);
                        
                        // Redirect to booking page with populated data
                        window.location.href = bookingUrl.toString();
                    });
                }
            });

            // Share Itinerary buttons
            const shareBtns = [document.getElementById('share-itinerary-btn'), document.getElementById('floating-share-itinerary-btn')];
            const itineraryId = '{{ is_object($itinerary) ? $itinerary->id : ($itinerary["id"] ?? "") }}';
            const shareToken = '{{ is_object($itinerary) ? $itinerary->share_token : ($itinerary["share_token"] ?? "") }}';
            const shareUrl = shareToken ? '{{ url("/share/itinerary") }}/' + shareToken : window.location.href;
            const trailName = '{{ is_array($trail) ? ($trail["name"] ?? "Trail") : ($trail->name ?? "Trail") }}';
            const startDate = '{{ isset($dateInfo["start_date"]) ? $dateInfo["start_date"]->format("M j, Y") : "" }}';
            const endDate = '{{ isset($dateInfo["end_date"]) ? $dateInfo["end_date"]->format("M j, Y") : "" }}';
            
            shareBtns.forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', function() {
                        const shareText = `Check out my hiking itinerary for ${trailName}! 🥾⛰️\n${startDate} to ${endDate}`;
                        
                        // Check if Web Share API is available
                        if (navigator.share) {
                            navigator.share({
                                title: `${trailName} - Hiking Itinerary`,
                                text: shareText,
                                url: shareUrl
                            }).then(() => {
                                console.log('Itinerary shared successfully');
                                showNotification('Itinerary shared successfully! 🎉');
                            }).catch((error) => {
                                console.log('Error sharing:', error);
                                // Fallback to share options modal
                                showShareModal(shareUrl, trailName, shareText);
                            });
                        } else {
                            // Fallback: Show share options modal
                            showShareModal(shareUrl, trailName, shareText);
                        }
                    });
                }
            });

            // Show share options modal
            function showShareModal(url, trailName, text) {
                // Create modal
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
                modal.innerHTML = `
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-900">Share Itinerary</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <p class="text-gray-600 mb-4">Share your ${trailName} itinerary</p>
                        
                        <div class="space-y-3">
                            <!-- Copy Link -->
                            <button onclick="copyLinkFromModal('${url}')" class="w-full flex items-center justify-center gap-3 bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Copy Link
                            </button>
                            
                            <!-- Email -->
                            <a href="mailto:?subject=${encodeURIComponent(trailName + ' - Hiking Itinerary')}&body=${encodeURIComponent(text + '\n\n' + url)}" class="w-full flex items-center justify-center gap-3 bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Share via Email
                            </a>
                            
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}" target="_blank" class="w-full flex items-center justify-center gap-3 bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Share on Facebook
                            </a>
                            
                            <!-- Twitter/X -->
                            <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}" target="_blank" class="w-full flex items-center justify-center gap-3 bg-gray-800 hover:bg-gray-900 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                                Share on X (Twitter)
                            </a>
                            
                            <!-- WhatsApp -->
                            <a href="https://wa.me/?text=${encodeURIComponent(text + '\n' + url)}" target="_blank" class="w-full flex items-center justify-center gap-3 bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                                Share on WhatsApp
                            </a>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-2">Link to share:</p>
                            <div class="flex items-center gap-2">
                                <input type="text" value="${url}" readonly class="flex-1 text-xs bg-white border border-gray-300 rounded px-2 py-1 text-gray-700">
                                <button onclick="copyLinkFromModal('${url}')" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Copy</button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                
                // Close on backdrop click
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.remove();
                    }
                });
            }

            // Copy link from modal
            window.copyLinkFromModal = function(url) {
                copyToClipboard(url);
                // Close modal after copying
                setTimeout(() => {
                    const modal = document.querySelector('.fixed.inset-0');
                    if (modal) modal.remove();
                }, 1500);
            };

            // Helper function to copy to clipboard
            function copyToClipboard(text) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(() => {
                        showNotification('Link copied to clipboard!');
                    }).catch((err) => {
                        console.error('Failed to copy: ', err);
                        fallbackCopyToClipboard(text);
                    });
                } else {
                    fallbackCopyToClipboard(text);
                }
            }

            // Fallback copy method for older browsers
            function fallbackCopyToClipboard(text) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.top = '0';
                textArea.style.left = '0';
                textArea.style.width = '2em';
                textArea.style.height = '2em';
                textArea.style.padding = '0';
                textArea.style.border = 'none';
                textArea.style.outline = 'none';
                textArea.style.boxShadow = 'none';
                textArea.style.background = 'transparent';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showNotification('Link copied to clipboard!');
                } catch (err) {
                    console.error('Fallback: Could not copy text: ', err);
                    showNotification('Failed to copy link', 'error');
                }
                document.body.removeChild(textArea);
            }

            // Show notification toast
            function showNotification(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
                toast.textContent = message;
                document.body.appendChild(toast);
                
                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 10);
                
                // Animate out and remove
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 300);
                }, 3000);
            }
        });
    </script>

    <!-- Activity Customization JavaScript -->
    <script src="{{ asset('js/itinerary-customization.js') }}"></script>
    
    <!-- SortableJS for drag and drop (optional, include from CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- Hide floating navigation when printing -->
    <style media="print">
        /* Hide UI elements that shouldn't be printed */
        #floating-navigation,
        #floating-itinerary-actions,
        #itinerary-actions {
            display: none !important;
        }
        
        /* Optimize page layout for printing */
        body {
            background: white !important;
        }
        
        .min-h-screen {
            background: white !important;
        }
        
        /* Ensure content fits nicely on pages */
        .max-w-7xl {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 10px !important;
        }
        
        /* Keep content together when possible */
        .bg-white,
        .shadow-xl,
        .rounded-2xl {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        
        /* Print backgrounds and colors */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        /* Adjust font sizes for print */
        body {
            font-size: 11pt;
        }
        
        h1 { font-size: 20pt; }
        h2 { font-size: 16pt; }
        h3 { font-size: 14pt; }
        h4 { font-size: 12pt; }
        
        /* Ensure tables don't break awkwardly */
        table {
            page-break-inside: avoid;
        }
        
        tr {
            page-break-inside: avoid;
        }
    </style>
</x-app-layout>