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

        {{-- Evacuation Points --}}
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
                        <p class="font-semibold text-gray-900">{{ $point['name'] }}</p>
                        @if (!empty($point['description']))
                            <p class="text-gray-600 text-xs">{{ $point['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
        <p class="text-xs text-yellow-900 font-medium flex items-center">
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Save these emergency contacts in your phone before starting your hike. Inform someone about your itinerary and expected return time.</span>
        </p>
    </div>
</div>
