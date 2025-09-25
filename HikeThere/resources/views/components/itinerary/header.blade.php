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
            $headerDurationLabel = $trail['duration'] ?? $routeData['duration'] ?? null;
            if (empty($headerDurationLabel)) {
                $headerDurationLabel = $dateInfo['duration_days'] . ' day(s) • ' . $dateInfo['nights'] . ' night(s)';
            }
        @endphp
        <p class="text-sm text-gray-600">Duration: {{ $headerDurationLabel }}</p>
    </div>
</div>