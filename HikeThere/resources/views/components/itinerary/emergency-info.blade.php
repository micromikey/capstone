@props(['emergencyInfo'])

<div class="bg-red-50 border-2 border-red-200 rounded-xl p-6 shadow-sm">
    <div class="flex items-center mb-4">
        <div class="flex-shrink-0 bg-red-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-bold text-red-900">🚨 Emergency Information</h3>
            <p class="text-sm text-red-700">Keep this information accessible during your hike</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Emergency Numbers --}}
        <div class="bg-white rounded-lg p-4 border border-red-100">
            <h4 class="font-semibold text-red-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Emergency Numbers
            </h4>
            <div class="space-y-2">
                @foreach ($emergencyInfo['emergency_numbers'] ?? [] as $number)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-700">{{ $number['service'] }}</span>
                        <a href="tel:{{ $number['number'] }}" class="font-bold text-red-600 hover:text-red-800">
                            {{ $number['number'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Hospitals --}}
        <div class="bg-white rounded-lg p-4 border border-red-100">
            <h4 class="font-semibold text-red-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Nearest Hospitals
            </h4>
            <div class="space-y-2">
                @foreach (array_slice($emergencyInfo['hospitals'] ?? [], 0, 2) as $hospital)
                    <div class="text-sm">
                        <p class="font-semibold text-gray-900">{{ $hospital['name'] }}</p>
                        <p class="text-gray-600 text-xs">{{ $hospital['address'] }}</p>
                        @if (!empty($hospital['distance']))
                            <p class="text-red-600 text-xs font-semibold">~{{ $hospital['distance'] }} away</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Ranger Stations --}}
        <div class="bg-white rounded-lg p-4 border border-red-100">
            <h4 class="font-semibold text-red-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Ranger Stations
            </h4>
            <div class="space-y-2">
                @foreach ($emergencyInfo['ranger_stations'] ?? [] as $station)
                    <div class="text-sm">
                        <p class="font-semibold text-gray-900">{{ $station['name'] }}</p>
                        <p class="text-gray-600 text-xs">{{ $station['address'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Evacuation Points and Off-Limits Areas Side by Side --}}
        <div class="bg-white rounded-lg p-4 border border-red-100">
            <h4 class="font-semibold text-red-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 9m0 0V7m0 0l-4-3m4 3l4-3m-4 3v13" />
                </svg>
                Evacuation Points
            </h4>
            <div class="space-y-2">
                @foreach ($emergencyInfo['evacuation_points'] ?? [] as $point)
                    <div class="text-sm">
                        <p class="font-semibold text-gray-900">📍 {{ $point['name'] }}</p>
                        @if (!empty($point['coordinates']))
                            <p class="text-gray-500 text-xs font-mono">{{ $point['coordinates'] }}</p>
                        @endif
                        @if (!empty($point['description']))
                            <p class="text-gray-600 text-xs">{{ $point['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Off-Limits Areas --}}
        @if (!empty($emergencyInfo['off_limits_areas']) && count($emergencyInfo['off_limits_areas']) > 0)
        <div class="bg-white rounded-lg p-4 border border-red-100">
            <h4 class="font-semibold text-red-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Off-Limits Areas
            </h4>
            <div class="space-y-2">
                @foreach ($emergencyInfo['off_limits_areas'] ?? [] as $area)
                    <div class="text-sm bg-red-50 p-2 rounded border border-red-200">
                        <p class="font-semibold text-red-900">🚫 {{ $area['name'] }}</p>
                        @if (!empty($area['coordinates']))
                            <p class="text-red-600 text-xs font-mono">{{ $area['coordinates'] }}</p>
                        @endif
                        @if (!empty($area['reason']))
                            <p class="text-red-700 text-xs font-medium">⚠️ {{ $area['reason'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @else
        {{-- Empty placeholder to maintain grid layout when no off-limits areas --}}
        <div class="bg-white rounded-lg p-4 border border-red-100 opacity-0 pointer-events-none"></div>
        @endif
    </div>

    {{-- Combined Map for Evacuation Points and Off-Limits Areas --}}
    @php
        $hasEvacuationPoints = !empty($emergencyInfo['evacuation_points']) && count(array_filter($emergencyInfo['evacuation_points'], fn($p) => !empty($p['coordinates']))) > 0;
        $hasOffLimitsAreas = !empty($emergencyInfo['off_limits_areas']) && count(array_filter($emergencyInfo['off_limits_areas'], fn($a) => !empty($a['coordinates']))) > 0;
    @endphp

    @if ($hasEvacuationPoints || $hasOffLimitsAreas)
    <div class="mt-4 bg-white rounded-lg p-4 border border-red-100">
        <h4 class="font-semibold text-red-900 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            Safety Map
        </h4>
        <div class="mb-3 flex gap-4 text-xs">
            @if ($hasEvacuationPoints)
            <div class="flex items-center gap-1">
                <span class="text-yellow-600 font-bold text-lg">🟡</span>
                <span class="text-gray-700">Evacuation Points (Safe Zones)</span>
            </div>
            @endif
            @if ($hasOffLimitsAreas)
            <div class="flex items-center gap-1">
                <span class="text-red-600 font-bold text-lg">🔴</span>
                <span class="text-gray-700">Off-Limits Areas (Danger Zones)</span>
            </div>
            @endif
        </div>
        <div id="safety-map" class="w-full h-[400px] rounded-lg border-2 border-gray-300"></div>
    </div>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const evacuationPoints = @json($emergencyInfo['evacuation_points'] ?? []);
            const offLimitsAreas = @json($emergencyInfo['off_limits_areas'] ?? []);
            
            // Filter points with valid coordinates
            const validEvacuationPoints = evacuationPoints.filter(point => {
                if (!point.coordinates) return false;
                const coords = point.coordinates.split(',');
                return coords.length === 2 && !isNaN(parseFloat(coords[0])) && !isNaN(parseFloat(coords[1]));
            });

            const validOffLimitsAreas = offLimitsAreas.filter(area => {
                if (!area.coordinates) return false;
                const coords = area.coordinates.split(',');
                return coords.length === 2 && !isNaN(parseFloat(coords[0])) && !isNaN(parseFloat(coords[1]));
            });

            const allPoints = [...validEvacuationPoints, ...validOffLimitsAreas];
            if (allPoints.length === 0) return;

            // Calculate center point from all markers
            let centerLat = 0, centerLng = 0;
            allPoints.forEach(point => {
                const coords = point.coordinates.split(',');
                centerLat += parseFloat(coords[0].trim());
                centerLng += parseFloat(coords[1].trim());
            });
            centerLat /= allPoints.length;
            centerLng /= allPoints.length;

            // Initialize map
            const map = new google.maps.Map(document.getElementById('safety-map'), {
                center: { lat: centerLat, lng: centerLng },
                zoom: 13,
                mapTypeId: 'terrain'
            });

            const bounds = new google.maps.LatLngBounds();

            // Add YELLOW flag markers for evacuation points
            validEvacuationPoints.forEach((point, index) => {
                const coords = point.coordinates.split(',');
                const lat = parseFloat(coords[0].trim());
                const lng = parseFloat(coords[1].trim());
                const position = { lat: lat, lng: lng };

                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: point.name,
                    icon: {
                        path: 'M 0,-40 L 0,0 M 0,-40 L 20,-32 L 0,-24 Z',
                        fillColor: '#EAB308',
                        fillOpacity: 1,
                        strokeColor: '#CA8A04',
                        strokeWeight: 2.5,
                        scale: 1.2,
                        anchor: new google.maps.Point(0, 0)
                    }
                });

                const infoContent = `
                    <div style="padding: 8px; max-width: 250px;">
                        <h3 style="font-weight: bold; color: #CA8A04; margin-bottom: 4px;">🟡 ${point.name}</h3>
                        <p style="font-size: 11px; color: #059669; font-weight: 600; margin-bottom: 4px;">EVACUATION POINT - SAFE ZONE</p>
                        ${point.description ? `<p style="font-size: 12px; color: #4b5563; margin-bottom: 4px;">${point.description}</p>` : ''}
                        <p style="font-size: 11px; color: #6b7280; font-family: monospace;">${point.coordinates}</p>
                    </div>
                `;

                const infoWindow = new google.maps.InfoWindow({
                    content: infoContent
                });

                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });

                bounds.extend(position);
            });

            // Add RED markers for off-limits areas
            validOffLimitsAreas.forEach((area, index) => {
                const coords = area.coordinates.split(',');
                const lat = parseFloat(coords[0].trim());
                const lng = parseFloat(coords[1].trim());
                const position = { lat: lat, lng: lng };

                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: area.name,
                    icon: {
                        path: 'M 0,-40 L 0,0 M 0,-40 L 20,-32 L 0,-24 Z',
                        fillColor: '#EF4444',
                        fillOpacity: 1,
                        strokeColor: '#991B1B',
                        strokeWeight: 2.5,
                        scale: 1.2,
                        anchor: new google.maps.Point(0, 0)
                    }
                });

                const infoContent = `
                    <div style="padding: 8px; max-width: 250px;">
                        <h3 style="font-weight: bold; color: #DC2626; margin-bottom: 4px;">🔴 ${area.name}</h3>
                        <p style="font-size: 11px; color: #DC2626; font-weight: 600; margin-bottom: 4px;">⚠️ OFF-LIMITS - DANGER ZONE</p>
                        ${area.reason ? `<p style="font-size: 12px; color: #991B1B; margin-bottom: 4px;"><strong>Reason:</strong> ${area.reason}</p>` : ''}
                        <p style="font-size: 11px; color: #6b7280; font-family: monospace;">${area.coordinates}</p>
                    </div>
                `;

                const infoWindow = new google.maps.InfoWindow({
                    content: infoContent
                });

                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });

                bounds.extend(position);
            });

            // Fit map to show all markers
            if (allPoints.length > 1) {
                map.fitBounds(bounds);
            }
        });
    </script>
    @endpush
    @endif

    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
        <p class="text-xs text-yellow-900 font-medium flex items-center">
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Save these emergency contacts in your phone before starting your hike. Inform someone about your itinerary and expected return time. @if ($hasOffLimitsAreas)<strong class="text-red-700">Avoid all marked off-limits areas for your safety.</strong>@endif</span>
        </p>
    </div>
</div>
