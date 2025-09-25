@props(['trail', 'routeData', 'build'])

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- Trail Route -->
    <div class="p-4 border rounded">
        <h3 class="font-medium text-gray-700">Trail Route</h3>
        <p class="text-sm text-gray-600 mt-2">
            {{ $trail['route_description'] ?? 'Route details not provided.' }}
        </p>
    </div>

    <!-- Trail Details -->
    <div class="p-4 border rounded">
        <h3 class="font-medium text-gray-700">Trail Details</h3>
        @php
            $displayDistance = $trail['distance_km'] ?? ($routeData['total_distance_km'] ?? 'N/A');
            $displayElevation = $trail['elevation_m'] ?? ($routeData['elevation_gain_m'] ?? 'N/A');
            $displayDifficulty = $trail['difficulty'] ?? ($routeData['difficulty'] ?? 'Unknown');
        @endphp
        <ul class="text-sm text-gray-600 mt-2 space-y-1">
            <li><strong>Distance:</strong> {{ $displayDistance }} km</li>
            <li><strong>Elevation Gain:</strong> {{ $displayElevation }} m</li>
            <li><strong>Difficulty:</strong> {{ $displayDifficulty }}</li>
            <li><strong>Overnight Allowed:</strong> {{ (!empty($trail['overnight_allowed']) ? 'Yes' : 'No') }}</li>
            @if(!empty($routeData['legs_count']) && $routeData['legs_count'] > 0)
                <li><strong>Route Segments:</strong> {{ $routeData['legs_count'] }} (using route legs for distance split)</li>
            @endif
        </ul>
    </div>

    <!-- Build Summary -->
    @include('hiker.itinerary.partials.build_summary')
</div>