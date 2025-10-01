@props(['trail', 'dateInfo', 'routeData'])

<div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-blue-600 rounded-2xl p-8 mb-8 shadow-2xl">
    <!-- Trail Title Section -->
    <div class="flex items-start mb-6">
        <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="w-12 h-12 object-contain mr-5 mt-1">
        <div>
            <h1 class="text-4xl font-bold text-white drop-shadow-lg mb-2">
                {{ $trail['name'] ?? 'Untitled Trail' }}
            </h1>
            <div class="flex items-center">
                <svg class="w-5 h-5 text-emerald-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                @php
                    // Simple location display with fallbacks
                    $locationDisplay = 'Adventure Awaits'; // Default fallback
                    
                    // Try different location sources
                    if (is_array($trail)) {
                        // Array format - try multiple sources
                        if (!empty($trail['location']['name']) && !empty($trail['location']['province'])) {
                            $locationDisplay = $trail['location']['name'] . ', ' . $trail['location']['province'];
                        } elseif (!empty($trail['location']['name'])) {
                            $locationDisplay = $trail['location']['name'];
                        } elseif (!empty($trail['region'])) {
                            $locationDisplay = $trail['region'];
                        } elseif (!empty($trail['mountain_name'])) {
                            $locationDisplay = $trail['mountain_name'];
                        }
                    } elseif (is_object($trail)) {
                        // Object format
                        if (!empty($trail->location->name) && !empty($trail->location->province)) {
                            $locationDisplay = $trail->location->name . ', ' . $trail->location->province;
                        } elseif (!empty($trail->location->name)) {
                            $locationDisplay = $trail->location->name;
                        } elseif (!empty($trail->region)) {
                            $locationDisplay = $trail->region;
                        } elseif (!empty($trail->mountain_name)) {
                            $locationDisplay = $trail->mountain_name;
                        }
                    }
                @endphp
                <p class="text-lg text-emerald-100 font-medium">{{ $locationDisplay }}</p>
            </div>
        </div>
    </div>

    <!-- Date Information Section - Now highlighted below -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-emerald-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                </svg>
                <div>
                    <p class="text-xs text-emerald-200 uppercase tracking-wider font-semibold mb-1">Start Date</p>
                    <p class="text-lg text-white font-bold">{{ $dateInfo['start_date']->toFormattedDateString() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-xs text-blue-200 uppercase tracking-wider font-semibold mb-1">End Date</p>
                    <p class="text-lg text-white font-bold">{{ $dateInfo['end_date']->toFormattedDateString() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-orange-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-xs text-orange-200 uppercase tracking-wider font-semibold mb-1">Duration</p>
                    @php
                    $durationParser = app(\App\Services\DurationParserService::class);

                    // Try to get duration from trail package first, then trail data
                    $headerDurationLabel = null;

                    // Check trail package duration
                    if (isset($trail['package']['duration'])) {
                    $headerDurationLabel = $durationParser->formatDuration($trail['package']['duration'], 'days_nights');
                    } elseif (is_object($trail) && $trail->package && $trail->package->duration) {
                    $headerDurationLabel = $durationParser->formatDuration($trail->package->duration, 'days_nights');
                    }
                    // Check trail duration field
                    elseif (isset($trail['duration'])) {
                    $headerDurationLabel = $durationParser->formatDuration($trail['duration'], 'days_nights');
                    } elseif (is_object($trail) && $trail->duration) {
                    $headerDurationLabel = $durationParser->formatDuration($trail->duration, 'days_nights');
                    }
                    // Check route data
                    elseif (isset($routeData['duration'])) {
                    $headerDurationLabel = $durationParser->formatDuration($routeData['duration'], 'days_nights');
                    }
                    // Fall back to calculated days/nights
                    else {
                    $headerDurationLabel = $dateInfo['duration_days'] . ' day' . ($dateInfo['duration_days'] != 1 ? 's' : '') .
                    ' • ' . $dateInfo['nights'] . ' night' . ($dateInfo['nights'] != 1 ? 's' : '');
                    }
                    @endphp
                    <p class="text-lg text-white font-bold">{{ $headerDurationLabel }}</p>
                </div>
            </div>
        </div>
    </div>
</div>