@props(['trail', 'routeData', 'build', 'staticMapUrl' => null])

<!-- Trail Map Section - Full Width Above Other Boxes -->
@if($staticMapUrl)
<div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200/60 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 mb-8">
    <div class="flex items-center mb-6">
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-4 rounded-xl shadow-lg mr-5">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-700 bg-clip-text text-transparent">Trail Path Visualization</h3>
            <p class="text-emerald-600 font-medium mt-1">Interactive route map showing your hiking trail</p>
        </div>
    </div>
    
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-lg">
        <img src="{{ $staticMapUrl }}" 
             alt="Trail Map for {{ $trail['name'] ?? 'Trail' }}" 
             class="w-full h-[500px] object-contain rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border-2 border-white"
             loading="lazy">
    </div>
    
    <!-- Route Description Below Map -->
    @if(!empty($trail['route_description']))
    <div class="mt-6 bg-white/60 backdrop-blur-sm p-4 rounded-xl border border-emerald-200/50">
        <h4 class="text-sm font-bold text-emerald-800 uppercase tracking-wider mb-2">Route Details</h4>
        <p class="text-slate-700 leading-relaxed">{{ $trail['route_description'] }}</p>
    </div>
    @endif
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

    <!-- Trail Details -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200/60 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
        <div class="flex items-center mb-4">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-xl shadow-lg mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold bg-gradient-to-r from-blue-700 to-indigo-700 bg-clip-text text-transparent">Trail Details</h3>
        </div>
        @php
            $displayDistance = $trail['distance_km'] ?? ($routeData['total_distance_km'] ?? 'N/A');
            $displayElevation = $trail['elevation_m'] ?? ($routeData['elevation_gain_m'] ?? 'N/A');
            $displayDifficulty = $trail['difficulty'] ?? ($routeData['difficulty'] ?? 'Unknown');
        @endphp
        <div class="bg-white/60 backdrop-blur-sm p-4 rounded-xl space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-600">Distance:</span>
                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">{{ $displayDistance }} km</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-600">Elevation:</span>
                <span class="bg-indigo-600 text-white px-3 py-1 rounded-full text-xs font-bold">{{ $displayElevation }} m</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-600">Difficulty:</span>
                @php
                    $difficultyColor = match(strtolower($displayDifficulty)) {
                        'easy' => 'bg-emerald-500',
                        'moderate' => 'bg-orange-500',
                        'hard', 'difficult' => 'bg-red-500',
                        default => 'bg-slate-500'
                    };
                @endphp
                <span class="{{ $difficultyColor }} text-white px-3 py-1 rounded-full text-xs font-bold">{{ $displayDifficulty }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-600">Overnight:</span>
                <span class="bg-{{ (!empty($trail['overnight_allowed']) ? 'emerald' : 'slate') }}-600 text-white px-3 py-1 rounded-full text-xs font-bold">{{ (!empty($trail['overnight_allowed']) ? 'Yes' : 'No') }}</span>
            </div>
            @if(!empty($routeData['legs_count']) && $routeData['legs_count'] > 0)
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-600">Segments:</span>
                    <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold">{{ $routeData['legs_count'] }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Build Summary - Enhanced -->
    <div class="bg-gradient-to-br from-orange-50 to-amber-50 border-2 border-orange-200/60 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
        <div class="flex items-center mb-4">
            <div class="bg-gradient-to-br from-orange-500 to-amber-600 p-3 rounded-xl shadow-lg mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold bg-gradient-to-r from-orange-700 to-amber-700 bg-clip-text text-transparent">Build Summary</h3>
        </div>
        @include('hiker.itinerary.partials.build_summary')
    </div>
</div>