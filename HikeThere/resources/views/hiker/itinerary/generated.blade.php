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
@endphp

<x-app-layout>
    <!-- Floating Navigation -->
    <x-floating-navigation :sections="[
        ['id' => 'itinerary-header', 'title' => 'Trail Overview', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 9m0 0V7m0 0l-4-3m4 3l4-3m-4 3v13\'></path>'],
        ['id' => 'trail-summary', 'title' => 'Trail Summary', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z\'></path>'],
        ['id' => 'pre-hike-activities', 'title' => 'Pre-hike Transport', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'></path>'],
        ['id' => 'daily-itinerary', 'title' => 'Daily Itinerary', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z\'></path>'],
        ['id' => 'additional-info', 'title' => 'Additional Info', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path>']
    ]" />

	<!-- Enhanced gradient background with nature theme -->
    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-blue-50 to-teal-50">
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
                                                    // For multi-day: subtract 24 hours to get actual time on previous day
                                                    $actualMinutes = $minutes - 1440;
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

            <!-- Itinerary Tables per day -->
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
            shareBtns.forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', function() {
                        const url = window.location.href;
                        const trailName = '{{ $trail->name ?? "Trail" }}';
                        const shareText = `Check out my hiking itinerary for ${trailName}!`;
                        
                        // Check if Web Share API is available
                        if (navigator.share) {
                            navigator.share({
                                title: `${trailName} Itinerary`,
                                text: shareText,
                                url: url
                            }).then(() => {
                                console.log('Itinerary shared successfully');
                            }).catch((error) => {
                                console.log('Error sharing:', error);
                                // Fallback to copy link
                                copyToClipboard(url);
                            });
                        } else {
                            // Fallback: Copy link to clipboard
                            copyToClipboard(url);
                        }
                    });
                }
            });

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