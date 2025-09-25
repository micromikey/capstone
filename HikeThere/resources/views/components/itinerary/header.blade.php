@props(['trail', 'dateInfo', 'routeData'])

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">
            Itinerary: {{ $trail['name'] ?? 'Untitled Trail' }}
        </h1>
        <p class="text-sm text-gray-500">{{ $trail['region'] ?? '' }}</p>
    </div>
    <div class="text-right">
        <p class="text-sm text-gray-600">Start: {{ $dateInfo['start_date']->toFormattedDateString() }}</p>
        <p class="text-sm text-gray-600">End: {{ $dateInfo['end_date']->toFormattedDateString() }}</p>
        
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
        <p class="text-sm text-gray-600">Duration: {{ $headerDurationLabel }}</p>
    </div>
</div>