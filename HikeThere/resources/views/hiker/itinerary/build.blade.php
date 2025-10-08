<x-app-layout>

  <form action="{{ route('hiker.itinerary.generate') }}" method="POST" class="relative min-h-screen overflow-hidden">
    @csrf

    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
      <div class="absolute -top-24 -left-24 h-80 w-80 rounded-full bg-gradient-to-br from-emerald-200/40 via-teal-200/30 to-cyan-200/30 blur-3xl"></div>
      <div class="absolute top-32 -right-16 h-72 w-72 rounded-full bg-gradient-to-br from-indigo-200/30 via-purple-200/30 to-fuchsia-200/30 blur-3xl"></div>
      <div class="absolute bottom-0 left-1/3 h-64 w-64 rounded-full bg-gradient-to-br from-amber-200/30 via-rose-200/30 to-emerald-200/30 blur-3xl"></div>
    </div>

    <!-- Page backdrop wrapper (kept light) -->
    <div class="relative z-10 bg-gradient-to-br from-emerald-50 via-white to-cyan-50">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 py-8">

        <!--Header-->
        <div class="mb-6 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white shadow-lg ring-1 ring-emerald-400/50">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 20h18l-9-15L3 20z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v15"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 20l4-8 4 8"/>
              </svg>
            </span>
            <div>
              <h1 class="text-2xl font-black tracking-tight">
                <span class="bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700 bg-clip-text text-transparent">
                  HikeThere: Itinerary Builder
                </span>
              </h1>
              <p class="mt-0.5 text-xs text-gray-600">Plan smarter. Hike happier.</p>
            </div>
          </div>
        </div>

        <!-- Display Validation Errors -->
        @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                There were some errors with your submission:
              </h3>
              <div class="mt-2 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
                        </div>
                        <!-- Debug panel (temporary) -->
                        <div id="transport-debug-panel" class="mt-2 text-xs text-red-600">Transport debug: (open console for more details)</div>
        </div>
        @endif

        <!--Outer Card-->
        <div class="rounded-2xl p-[1px] bg-gradient-to-r from-emerald-300/40 via-cyan-300/40 to-indigo-300/40">
          <div class="rounded-2xl bg-white/85 backdrop-blur-xl ring-1 ring-black/5 p-5 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

              <!--LEFT: Search + Map -->
              <div class="flex flex-col space-y-3">
                <!-- Search - EXACT SAME AS MAIN MAP -->
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Search & Explore Map</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                  </div>
                  <input
                    type="text"
                    id="itinerary-search-input"
                    placeholder="Search trails by name, mountain, or location..."
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
                <p class="text-xs text-gray-500 mt-1">Search for locations to explore on the map and add to your itinerary</p>

                <!-- Interactive Map Card -->
                <div class="relative rounded-xl border border-white/70 bg-white/70 shadow-sm ring-1 ring-black/5 overflow-hidden">
                  <div id="itinerary-map" class="h-80 w-full rounded-xl bg-gray-100 flex items-center justify-center">
                    <!-- Map Loading State -->
                    <div id="map-loading-state" class="text-center p-4">
                      <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 mb-3"></div>
                      <p class="text-sm text-gray-600 font-medium">Loading interactive map...</p>
                      <p class="text-xs text-gray-500">Preparing your hiking adventure</p>
                    </div>
                    
                    <!-- Map Fallback -->
                    <div id="map-fallback" class="text-center p-4 hidden">
                      <div class="text-gray-400 text-4xl mb-2">🗺️</div>
                      <p class="text-sm text-gray-600 font-medium">Interactive Map</p>
                      <p class="text-xs text-gray-500">Click to explore trails</p>
                    </div>
                  </div>
                  
                              <!-- Map Controls Overlay -->
            <div class="absolute top-2 right-2 flex flex-col gap-2">
              <!-- Map Type Toggle -->
              <button type="button" id="map-type-toggle" class="w-8 h-8 bg-white/90 rounded-lg shadow-md border border-gray-200 flex items-center justify-center text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-200" title="Switch Map Type">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"/>
                </svg>
              </button>
                    <button type="button" id="map-zoom-in" class="w-8 h-8 bg-white/90 rounded-lg shadow-md border border-gray-200 flex items-center justify-center text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-200" title="Zoom In">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                      </svg>
                    </button>
                    <button type="button" id="map-zoom-out" class="w-8 h-8 bg-white/90 rounded-lg shadow-md border border-gray-200 flex items-center justify-center text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-200" title="Zoom Out">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H4"/>
                      </svg>
                    </button>
                    <button type="button" id="map-reset" class="w-8 h-8 bg-white/90 rounded-lg shadow-md border border-gray-200 flex items-center justify-center text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-200" title="Reset View">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.001 0 01-15.357-2m15.357 2H15"/>
                      </svg>
                    </button>
                  </div>
                  
                  <!-- Map Status Bar -->
                  <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-3">
                    <div class="flex items-center justify-between text-white">
                      <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/>
                        </svg>
                        <span class="text-xs font-medium" id="map-status">Ready to plan your hike</span>
                      </div>
                      <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1">
                          <span class="text-xs text-emerald-300" id="trail-count">0 trails</span>
                        </div>
                        <div class="flex items-center gap-1">
                          <span class="text-xs text-blue-300" id="map-type-indicator">Satellite</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!--Suggested Trail - Back to original position -->
                <div class="rounded-xl border border-white/70 bg-white/85 p-4 ring-1 ring-black/5 backdrop-blur">
                  <div class="mb-4">
                    <p class="text-sm font-semibold text-gray-800">Suggested Trails</p>
                    @if($assessment)
                    <div class="mt-2 space-y-2">
                      <p class="bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700 bg-clip-text text-lg font-extrabold tracking-tight text-transparent">
                        Personalized for you based on your activity
                      </p>
                      <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500">Assessment Score:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                          {{ $assessment->overall_score >= 80 ? 'bg-red-100 text-red-800' : 
                             ($assessment->overall_score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                          {{ $assessment->overall_score }}/100 - {{ $assessment->overall_score >= 80 ? 'Hard' : ($assessment->overall_score >= 60 ? 'Moderate' : 'Easy') }}
                        </span>
                      </div>
                      <p class="text-xs text-gray-600">
                        Recommendations based on your booking history, reviews, saved itineraries, and fitness level.
                      </p>
                    </div>
                    @else
                    <div class="mt-2">
                      <p class="bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700 bg-clip-text text-lg font-extrabold tracking-tight text-transparent">
                        Complete your assessment for personalized recommendations
                      </p>
                      <p class="mt-0.5 text-xs text-gray-500">We'll suggest trails based on your fitness level and experience.</p>
                      <a href="{{ route('assessment.instruction') }}" class="inline-flex items-center mt-2 px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700 transition-colors">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Take Assessment
                      </a>
                    </div>
                    @endif
                  </div>

                  <!-- ML Recommended Trails -->
                  @if(isset($recommendedTrails) && $recommendedTrails->count() > 0)
                  <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                      <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        <svg class="w-4 h-4 inline-block mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Top Picks for You
                      </h4>
                      <span class="text-xs text-gray-500">ML Powered</span>
                    </div>
                    
                    <div class="space-y-2 max-h-80 overflow-y-auto pr-2">
                      @foreach($recommendedTrails as $recommended)
                      <div class="group relative bg-gradient-to-r from-emerald-50 to-cyan-50 rounded-lg p-3 border border-emerald-200 hover:shadow-md transition-all duration-200 cursor-pointer" 
                           onclick="selectRecommendedTrail({{ $recommended['trail_id'] }}, '{{ addslashes($recommended['trail_name']) }}')">
                        <!-- Recommendation Badge -->
                        <div class="absolute top-2 right-2 flex items-center gap-1 bg-emerald-600 text-white text-xs px-2 py-0.5 rounded-full">
                          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                          </svg>
                          <span class="font-medium">{{ number_format($recommended['score'] * 100, 0) }}%</span>
                        </div>
                        
                        <div class="pr-16">
                          <h5 class="font-semibold text-gray-900 text-sm mb-1">
                            {{ $recommended['trail_name'] }}
                          </h5>
                          
                          <div class="flex items-center gap-2 text-xs text-gray-600 mb-2">
                            @if($recommended['mountain_name'])
                            <span class="flex items-center gap-1">
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
                              </svg>
                              {{ $recommended['mountain_name'] }}
                            </span>
                            @endif
                            
                            @if($recommended['average_rating'] > 0)
                            <span class="flex items-center gap-1">
                              <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                              </svg>
                              {{ number_format($recommended['average_rating'], 1) }}
                            </span>
                            @endif
                          </div>
                          
                          @if($recommended['location_label'])
                          <p class="text-xs text-gray-500 mb-2">
                            <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            {{ $recommended['location_label'] }}
                          </p>
                          @endif
                          
                          @if($recommended['explanation'])
                          <details class="text-xs text-gray-600 bg-white/50 rounded px-2 py-1 mt-1">
                            <summary class="cursor-pointer text-emerald-700 font-medium">Why this trail?</summary>
                            <p class="mt-1 text-gray-600">{{ $recommended['explanation'] }}</p>
                          </details>
                          @else
                          <p class="text-xs text-gray-500 italic">
                            Recommended based on your activity history and preferences.
                          </p>
                          @endif
                        </div>
                      </div>
                      @endforeach
                    </div>
                  </div>
                  @endif

                  <!-- Region Filter -->
                  <div class="mb-4">
                    <label for="regionFilter" class="block text-sm font-medium text-gray-700 mb-2">
                      <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      Filter by Region
                    </label>
                    <select id="regionFilter" class="w-full sm:w-72 rounded-md bg-white px-3 py-2 text-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-emerald-400 transition-all">
                      <option value="">All Regions</option>
                      @php
                        $regions = $trails->pluck('location.region')->filter()->unique()->sort()->values();
                      @endphp
                      @foreach($regions as $region)
                        <option value="{{ $region }}">{{ $region }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative w-full sm:w-72">
                      <div class="rounded-md bg-gradient-to-r from-emerald-300/40 to-cyan-300/40 p-[1px]">
                        <select name="trail" id="trailSelect" class="appearance-none bg-none shadow-none pr-8 w-full rounded-md bg-white px-3 py-2 text-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-emerald-200">
                          <option value="" {{ old('trail') == '' ? 'selected' : '' }} disabled>Select a trail...</option>
                          @if($trails && $trails->count() > 0)
                            @php
                              // Filter trails based on assessment score if available
                              $filteredTrails = $trails;
                              if ($assessment) {
                                $difficulty = $assessment->overall_score >= 80 ? 'advanced' : 
                                             ($assessment->overall_score >= 60 ? 'intermediate' : 'beginner');
                                $filteredTrails = $trails->filter(function($trail) use ($difficulty) {
                                  return strtolower($trail->difficulty) === $difficulty;
                                });
                              }
                            @endphp
                            
                            @if($filteredTrails->count() > 0)
                              <optgroup label="Recommended for your level ({{ $filteredTrails->count() }} trails)">
                                @foreach($filteredTrails as $trail)
                                    @php
                                      // side_trips may be stored as JSON array, newline-separated list, or a single string.
                                      if ($trail->side_trips) {
                                        $decoded = json_decode($trail->side_trips, true);
                                        if (is_array($decoded)) {
                                          $sideTripsArray = array_values(array_filter(array_map('trim', $decoded)));
                                        } elseif (strpos($trail->side_trips, "\n") !== false) {
                                          $sideTripsArray = array_values(array_filter(array_map('trim', explode("\n", $trail->side_trips))));
                                        } else {
                                          // Treat entire string as a single entry (prevents splitting Google Places with commas)
                                          $sideTripsArray = [trim($trail->side_trips)];
                                        }
                                      } else {
                                        $sideTripsArray = [];
                                      }
                                      // Gather canonical transport fields used in admin create view
                                      $canonical = $trail->transportation_details ?? $trail->transportation ?? $trail->transport ?? $trail->transport_details ?? null;
                                      // Also include explicit pickup/vehicle/place fields as separate attributes so the preview has deterministic access
                                      $pickup_place = $trail->transportation_pickup_place ?? null;
                                      // transportation_vehicle usually stores the key; transportation_vehicle_label may store the human label
                                      $vehicle_key = $trail->transportation_vehicle ?? null;
                                      $vehicle_label = $trail->transportation_vehicle_label ?? ($vehicle_key ? ucfirst($vehicle_key) : null);

                                      // Build a unified transport payload that preserves canonical payload and explicit fields
                                      $transportPayload = (object)[];
                                      if ($canonical) $transportPayload->canonical = $canonical;
                                      if ($pickup_place) $transportPayload->pickup_place = $pickup_place;
                                      if ($vehicle_key) $transportPayload->vehicle = $vehicle_key;
                                      if ($vehicle_label) $transportPayload->vehicle_label = $vehicle_label;
                                    @endphp
                                    @php
                                      $hours = null;
                                      // Primary: explicit trail fields
                                      if (!empty($trail->opening_time) || !empty($trail->closing_time)) {
                                        $hours = [
                                          'open' => $trail->opening_time ?? null,
                                          'close' => $trail->closing_time ?? null,
                                        ];
                                      }

                                      // Secondary: trail.hours JSON
                                      if (!$hours && !empty($trail->hours)) {
                                        $decodedHours = json_decode($trail->hours, true);
                                        if (is_array($decodedHours)) $hours = $decodedHours;
                                      }

                                      // Tertiary: try the related package (package table) if available
                                      if (!$hours && isset($trail->package) && $trail->package) {
                                        // package may have opening_time/closing_time or hours JSON
                                        if (!empty($trail->package->opening_time) || !empty($trail->package->closing_time)) {
                                          $hours = [
                                            'open' => $trail->package->opening_time ?? null,
                                            'close' => $trail->package->closing_time ?? null,
                                          ];
                                        } elseif (!empty($trail->package->hours)) {
                                          $decodedPkgHours = json_decode($trail->package->hours, true);
                                          if (is_array($decodedPkgHours)) $hours = $decodedPkgHours;
                                        }
                                      }
                                    @endphp
                                    @php
                                      $isSelected = (old('trail') == $trail->trail_name) || (isset($preselectedTrail) && $preselectedTrail->id == $trail->id);
                                      // Get hiking start time from the trail's most recent event
                                      $hikingStartTime = $trail->events->first()?->hiking_start_time ?? null;
                                    @endphp
                                    <option value="{{ $trail->trail_name }}" 
                                      data-trail-id="{{ $trail->id }}" 
                                      data-region="{{ $trail->location ? $trail->location->region : '' }}"
                                      data-province="{{ $trail->location ? $trail->location->province : '' }}"
                                      data-mountain="{{ $trail->mountain_name ?? '' }}"
                                      data-full-address="{{ $trail->location ? $trail->location->province . ', ' . $trail->location->region : '' }}"
                                      data-difficulty="{{ ucfirst($trail->difficulty ?? 'Unknown') }}"
                                      {{ $isSelected ? 'selected' : '' }} 
                                      data-side-trips='@json($sideTripsArray)' 
                                      data-transport='@json($transportPayload)'
                                      @if($hours) data-hours='{{ htmlspecialchars(json_encode($hours), ENT_QUOTES, 'UTF-8') }}' @endif
                                      @if(!empty($trail->opening_time)) data-opening="{{ e($trail->opening_time) }}" @endif
                                      @if(!empty($trail->closing_time)) data-closing="{{ e($trail->closing_time) }}" @endif
                                      @if($hikingStartTime) data-hiking-start-time="{{ e($hikingStartTime) }}" @endif
                                      @if(!empty($trail->pickup_time)) data-pickup-time="{{ e($trail->pickup_time) }}" @elseif(!empty($trail->pickup_time)) data-pickup-time="{{ e($trail->pickup_time) }}" @endif
                                      @if(!empty($trail->departure_time)) data-departure-time="{{ e($trail->departure_time) }}" @elseif(!empty($trail->departure_time)) data-departure-time="{{ e($trail->departure_time) }}" @endif
                                      @if(!empty($trail->pickup_time)) data-pickup-time-short="{{ e(\Carbon\Carbon::parse($trail->pickup_time)->format('H:i')) }}" @elseif(!empty($trail->pickup_time)) data-pickup-time-short="{{ e(\Carbon\Carbon::parse($trail->pickup_time)->format('H:i')) }}" @endif
                                      @if(!empty($trail->departure_time)) data-departure-time-short="{{ e(\Carbon\Carbon::parse($trail->departure_time)->format('H:i')) }}" @elseif(!empty($trail->departure_time)) data-departure-time-short="{{ e(\Carbon\Carbon::parse($trail->departure_time)->format('H:i')) }}" @endif>
                                      {{ $trail->trail_name }} - {{ $trail->location ? $trail->location->province . ', ' . $trail->location->region : ($trail->mountain_name ?? 'Location N/A') }} ({{ ucfirst($trail->difficulty ?? 'Unknown') }})
                                    </option>
                                  @endforeach
                              </optgroup>
                            @endif
                            
                            @if($filteredTrails->count() < $trails->count())
                              <optgroup label="Other trails ({{ $trails->count() - $filteredTrails->count() }} trails)">
                                @foreach($trails->whereNotIn('id', $filteredTrails->pluck('id')) as $trail)
                                  @php
                                    // side_trips may be stored as JSON array, newline-separated list, or a single string.
                                    if ($trail->side_trips) {
                                      $decoded = json_decode($trail->side_trips, true);
                                      if (is_array($decoded)) {
                                        $sideTripsArray = array_values(array_filter(array_map('trim', $decoded)));
                                      } elseif (strpos($trail->side_trips, "\n") !== false) {
                                        $sideTripsArray = array_values(array_filter(array_map('trim', explode("\n", $trail->side_trips))));
                                      } else {
                                        // Treat entire string as a single entry (prevents splitting Google Places with commas)
                                        $sideTripsArray = [trim($trail->side_trips)];
                                      }
                                    } else {
                                      $sideTripsArray = [];
                                    }
                                    $canonical = $trail->transportation_details ?? $trail->transportation ?? $trail->transport ?? $trail->transport_details ?? null;
                                    $pickup_place = $trail->transportation_pickup_place ?? null;
                                    $vehicle_label = $trail->transportation_vehicle ?? null;
                                    $vehicle_key = $trail->transportation_vehicle ?? null;
                                    $vehicle_label = $trail->transportation_vehicle_label ?? ($vehicle_key ? ucfirst($vehicle_key) : null);
                                    $transportPayload = (object)[];
                                    if ($canonical) $transportPayload->canonical = $canonical;
                                    if ($pickup_place) $transportPayload->pickup_place = $pickup_place;
                                    if ($vehicle_key) $transportPayload->vehicle = $vehicle_key;
                                    if ($vehicle_label) $transportPayload->vehicle_label = $vehicle_label;
                                  @endphp
                                  @php
                                    $hours = null;
                                    // Primary: explicit trail fields
                                    if (!empty($trail->opening_time) || !empty($trail->closing_time)) {
                                      $hours = [
                                        'open' => $trail->opening_time ?? null,
                                        'close' => $trail->closing_time ?? null,
                                      ];
                                    }

                                    // Secondary: trail.hours JSON
                                    if (!$hours && !empty($trail->hours)) {
                                      $decodedHours = json_decode($trail->hours, true);
                                      if (is_array($decodedHours)) $hours = $decodedHours;
                                    }

                                    // Tertiary: try the related package (package table) if available
                                    if (!$hours && isset($trail->package) && $trail->package) {
                                      if (!empty($trail->package->opening_time) || !empty($trail->package->closing_time)) {
                                        $hours = [
                                          'open' => $trail->package->opening_time ?? null,
                                          'close' => $trail->package->closing_time ?? null,
                                        ];
                                      } elseif (!empty($trail->package->hours)) {
                                        $decodedPkgHours = json_decode($trail->package->hours, true);
                                        if (is_array($decodedPkgHours)) $hours = $decodedPkgHours;
                                      }
                                    }
                                  @endphp
                                  @php
                                    $isSelected = (old('trail') == $trail->trail_name) || (isset($preselectedTrail) && $preselectedTrail->id == $trail->id);
                                    // Get hiking start time from the trail's most recent event
                                    $hikingStartTime = $trail->events->first()?->hiking_start_time ?? null;
                                  @endphp
                                  <option value="{{ $trail->trail_name }}" 
                                    data-trail-id="{{ $trail->id }}" 
                                    data-region="{{ $trail->location ? $trail->location->region : '' }}"
                                    data-province="{{ $trail->location ? $trail->location->province : '' }}"
                                    data-mountain="{{ $trail->mountain_name ?? '' }}"
                                    data-full-address="{{ $trail->location ? $trail->location->province . ', ' . $trail->location->region : '' }}"
                                    data-difficulty="{{ ucfirst($trail->difficulty ?? 'Unknown') }}"
                                    {{ $isSelected ? 'selected' : '' }} 
                                    data-side-trips='@json($sideTripsArray)' 
                                    data-transport='@json($transportPayload)'
                                    @if($hours) data-hours='{{ htmlspecialchars(json_encode($hours), ENT_QUOTES, 'UTF-8') }}' @endif
                                    @if(!empty($trail->opening_time)) data-opening="{{ e($trail->opening_time) }}" @endif
                                    @if(!empty($trail->closing_time)) data-closing="{{ e($trail->closing_time) }}" @endif
                                    @if($hikingStartTime) data-hiking-start-time="{{ e($hikingStartTime) }}" @endif
                                    @if(!empty($trail->pickup_time)) data-pickup-time="{{ e($trail->pickup_time) }}" @elseif(!empty($trail->pickup_time)) data-pickup-time="{{ e($trail->pickup_time) }}" @endif
                                    @if(!empty($trail->departure_time)) data-departure-time="{{ e($trail->departure_time) }}" @elseif(!empty($trail->departure_time)) data-departure-time="{{ e($trail->departure_time) }}" @endif
                                    @if(!empty($trail->pickup_time)) data-pickup-time-short="{{ e(\Carbon\Carbon::parse($trail->pickup_time)->format('H:i')) }}" @elseif(!empty($trail->pickup_time)) data-pickup-time-short="{{ e(\Carbon\Carbon::parse($trail->pickup_time)->format('H:i')) }}" @endif
                                    @if(!empty($trail->departure_time)) data-departure-time-short="{{ e(\Carbon\Carbon::parse($trail->departure_time)->format('H:i')) }}" @elseif(!empty($trail->departure_time)) data-departure-time-short="{{ e(\Carbon\Carbon::parse($trail->departure_time)->format('H:i')) }}" @endif>
                                    {{ $trail->trail_name }} - {{ $trail->location ? $trail->location->province . ', ' . $trail->location->region : ($trail->mountain_name ?? 'Location N/A') }} ({{ ucfirst($trail->difficulty ?? 'Unknown') }})
                                  </option>
                                @endforeach
                              </optgroup>
                            @endif
                          @else
                            <option value="Custom Trail">Custom Trail</option>
                          @endif
                        </select>
                      </div>

                      <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                      </svg>
                      
                      @if($trails && $trails->count() == 0)
                        <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                          <div class="flex items-center gap-2 text-xs text-yellow-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <span>No trails available. You can still create a custom itinerary.</span>
                          </div>
                        </div>
                      @endif
                    </div>

                    <div class="flex items-center gap-2">
                      <span class="text-xs text-gray-500"></span>
                      <span class="text-sm font-medium text-emerald-700" id="selectedTrailCount">
                        {{ $trails && $trails->count() > 0 ? $trails->count() : 0 }} trails available
                      </span>
                    </div>
                  </div>
                  
                  @if($assessment && $trails && $trails->count() > 0)
                    <div class="mt-3 p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                      <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-xs text-emerald-800">
                          <p class="font-medium">Personalized Recommendation</p>
                          <p class="mt-1">Based on your {{ $assessment->overall_score }}/100 score, we recommend trails with 
                            <strong>{{ $assessment->overall_score >= 80 ? 'advanced' : ($assessment->overall_score >= 60 ? 'intermediate' : 'beginner') }}</strong> difficulty. 
                            You can still choose other trails if you prefer.</p>
                        </div>
                      </div>
                    </div>
                  @endif

                  <!-- Trail Overview Section -->
                  <div class="mt-4">
                    <div class="flex items-center justify-between mb-3">
                      <h4 class="text-sm font-semibold text-gray-800">Trail Overview</h4>
                      <button type="button" id="refresh-trail-info" class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-200 transition-colors">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.001 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                      </button>
                    </div>
                    
                    <!-- Trail Info Loading State -->
                    <div id="trail-info-loading" class="hidden text-center py-4">
                      <div class="inline-block animate-spin rounded-full h-5 w-5 border-2 border-blue-200 border-t-blue-600 mb-2"></div>
                      <p class="text-xs text-gray-500">Loading trail information...</p>
                    </div>
                    
                    <!-- Trail Info Container -->
                    <div id="trail-info-container" class="space-y-3">
                      <!-- Initial state - no trail selected -->
                      <div class="text-center py-6">
                        <div class="text-gray-400 text-2xl mb-2">🏔️</div>
                        <p class="text-sm text-gray-600 font-medium mb-1">No Trail Selected</p>
                        <p class="text-xs text-gray-500">Select a trail to view its overview</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!--RIGHT: Controls Card -->
              <div class="rounded-xl border border-white/70 bg-white/85 p-4 shadow-sm ring-1 ring-black/5 backdrop-blur">
                <div class="space-y-6">
                  <!-- Trail Information -->
                  <div>
                    <div class="mb-2 flex items-center gap-2">
                      <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 20h18l-9-15L3 20z"/>
                      </svg>
                      <p class="text-sm font-semibold text-gray-800">Trail Information</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3 border border-gray-200">
                      <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-600">Selected Trail:</span>
                        <span class="text-sm font-semibold text-gray-800" id="selectedTrailDisplay">Select trail first</span>
                        <input type="hidden" name="trail_name" id="trailNameInput" value="{{ old('trail_name') }}" required />
                      </div>
                      <div class="mt-2 text-xs text-gray-500">
                        <span id="trailDifficultyDisplay">Difficulty: Not selected</span>
                      </div>
                      <!-- Trail Opening / Closing Time Preview -->
                      <div id="trail-times-preview" class="mt-3 hidden">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 mb-1">Trail Hours</div>
                        <div class="flex items-center gap-6">
                          <div>
                            <div class="text-[11px] text-gray-500">Opens</div>
                            <div id="trail-opening" class="text-sm font-semibold text-gray-800">—</div>
                          </div>
                          <div>
                            <div class="text-[11px] text-gray-500">Closes</div>
                            <div id="trail-closing" class="text-sm font-semibold text-gray-800">—</div>
                          </div>
                          <div class="ml-auto text-xs text-gray-400" id="trail-hours-note"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Schedule -->
                  <div>
                    <div class="mb-2 flex items-center gap-2">
                      <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                      <p class="text-sm font-semibold text-gray-800">Schedule</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                      <label class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-600">Hiking Start Time</span>
                        <div id="hiking-start-time-display" class="rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-sm font-semibold text-gray-800 w-28">
                          —
                        </div>
                      </label>
                      <label class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-600">Date</span>
                        <input type="date" name="date" value="{{ old('date') }}" class="rounded-md border border-gray-300 px-2 py-1.5 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200 w-40 transition" />
                      </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Hiking start time is set by the trail. Select a date for your hike.</p>
                  </div>

                  <!-- Weather Forecast -->
                  <div class="mt-4">
                    <div class="mb-3 flex items-center gap-2">
                      <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                      </svg>
                      <p class="text-sm font-semibold text-gray-800">Trail Weather</p>
                    </div>
                    
                    <div class="rounded-xl border border-gray-200 bg-white/80 p-4 backdrop-blur">
                      <div class="mb-3 flex items-center justify-between">
                        <div>
                          <h4 class="text-sm font-semibold text-gray-800">5-Day Weather Forecast</h4>
                          <p class="text-xs text-gray-500" id="weather-location-info">Select a trail to see weather</p>
                        </div>
                        <button type="button" id="refresh-weather" class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-200 transition-colors">
                          <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.001 0 01-15.357-2m15.357 2H15"/>
                          </svg>
                          Refresh
                        </button>
                      </div>
                      
                      <!-- Weather Loading State -->
                      <div id="weather-loading" class="hidden text-center py-6">
                        <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-blue-200 border-t-blue-600 mb-2"></div>
                        <p class="text-xs text-gray-500">Loading weather forecast...</p>
                      </div>
                      
                      <!-- Weather Calendar Grid -->
                      <div id="weather-calendar" class="hidden">
                        <div class="grid grid-cols-5 gap-3">
                          <!-- Weather days will be populated here -->
                        </div>
                        
                        <!-- Weather Summary -->
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg text-center">
                          <div class="text-sm text-gray-700">
                            <span class="font-medium">Best day:</span> <span id="best-day-weather" class="text-emerald-700 font-semibold">-</span> • 
                            <span class="font-medium">Rain risk:</span> <span id="rain-risk-weather" class="text-blue-700 font-semibold">-</span>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Weather Error State -->
                      <div id="weather-error" class="hidden text-center py-4">
                        <div class="text-red-500 text-lg mb-2">⚠️</div>
                        <p class="text-xs text-red-600 mb-2">Weather data unavailable</p>
                        <button type="button" onclick="loadWeatherForSelectedTrail()" class="text-xs text-blue-600 hover:text-blue-700">
                          Try again
                        </button>
                      </div>
                      
                      <!-- No Trail Selected State -->
                      <div id="weather-no-trail" class="text-center py-6">
                        <div class="text-gray-400 text-2xl mb-2">🗺️</div>
                        <p class="text-sm text-gray-600 font-medium mb-2">No Trail Selected</p>
                        <p class="text-xs text-gray-500">Select a trail from the map or dropdown above to see weather conditions</p>
                      </div>
                    </div>
                  </div>

                  <!-- Locations & Lists -->
                  <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-3">
                    <div class="grid gap-3">
                      <div>
                        <div class="flex items-center justify-between">
                          <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Your Location</p>
                          <button type="button" id="getCurrentLocationBtn" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-200 transition-colors">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/>
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Current
                          </button>
                        </div>
                        <p class="text-sm text-gray-800" id="userLocationDisplay">{{ Auth::user()->location ?? 'Click "Current" to get your location' }}</p>
                        <p class="text-xs text-gray-500 mt-1" id="locationHelper">{{ Auth::user()->location ? '' : 'No location set yet. Use the Current button to get your location automatically.' }}</p>
                        <input type="hidden" name="user_location" id="userLocationInput" value="{{ Auth::user()->location ?? '' }}" />
                        <input type="hidden" name="user_lat" id="userLatInput" value="" />
                        <input type="hidden" name="user_lng" id="userLngInput" value="" />
                      </div>
                                              <div class="border-t border-dashed pt-2">
                          <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Destination</p>
                          <div class="min-h-[3rem] p-2 bg-gray-50 rounded-md border border-gray-200">
                            <p class="text-sm text-gray-800 leading-relaxed" id="destinationDisplay">{{ old('trail_name', 'Select trail first') }}</p>
                          </div>
                        </div>
                      </div>

                                            <!-- Stopovers Section -->
                      <div class="border-t border-dashed pt-2">
                        <div class="flex items-center justify-between mb-2">
                          <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Stopovers</p>
                          <button type="button" id="add-stopover-btn" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                            + Add Stopover
                          </button>
                        </div>
                        <div class="flex gap-2 mb-2">
                          <input type="text" id="add-stopover-input" placeholder="Search Philippine locations..." 
                                 class="flex-1 rounded-md border border-gray-300 px-2 py-1.5 text-xs ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200">
                        </div>
                        <div id="stopovers-container" class="space-y-1">
                          <!-- Stopovers will be added here dynamically -->
                        </div>
                      </div>

                      <!-- Side Trips Section -->
                      <div class="border-t border-dashed pt-2">
                        <div class="flex items-center justify-between mb-2">
                          <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Side Trips</p>
                          <div class="flex items-center gap-3">
                            <input type="hidden" name="include_side_trips" id="includeSideTripsHidden" value="0">
                            <label for="includeSideTrips" class="inline-flex items-center cursor-pointer select-none">
                              <span class="relative inline-block">
                                <input id="includeSideTrips" type="checkbox" class="sr-only" />
                                <!-- track (background) -->
                                <div class="track w-10 h-5 bg-gray-200 rounded-full shadow-inner transition-colors duration-200 z-0"></div>
                                <!-- knob -->
                                <div class="dot absolute left-0 top-0.5 w-4 h-4 bg-white rounded-full shadow transform transition-transform z-10"></div>
                              </span>
                              <span class="ml-3 text-xs text-gray-600">Include side trips in itinerary</span>
                            </label>
                          </div>
                        </div>
                        <p class="text-xs text-gray-500">Side trips are part of the selected trail package by default. Toggle this on to include them in the generated itinerary.</p>
                        <div id="sidetrips-preview" class="mt-3 hidden">
                          <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 mb-1">Included Side Trips Preview</p>
                          <div class="rounded-md border border-gray-200 bg-white p-3 text-sm text-gray-700" id="sidetrips-preview-list">
                            <!-- Populated by JS -->
                            <em class="text-xs text-gray-400">Enable "Include side trips" and select a trail to preview included side trips.</em>
                          </div>
                        </div>
                        
                        <!-- Minimal Read-only Transportation Preview -->
                        <div id="transportation-preview-block" class="mt-4 hidden">
                          <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 mb-1">Transportation Preview</p>
                          <div class="rounded-md border border-gray-200 bg-white p-3 text-sm text-gray-700">
                            <div class="grid grid-cols-1 gap-2">
                              <!-- Pickup block: shown when pickup/meeting info present -->
                              <div id="transport-pickup-block" class="hidden">
                                <p class="text-[11px] text-gray-500">Pickup / Meeting Point</p>
                                <div id="transportation-pickup" class="min-h-[2rem] p-2 bg-gray-50 rounded-md border border-gray-200">—</div>
                              </div>

                              <!-- Vehicle block: shown when vehicle label is available (paired with pickup or commute) -->
                              <div id="transport-vehicle-block" class="hidden">
                                <p class="text-[11px] text-gray-500">Vehicle</p>
                                <div id="transportation-vehicle" class="min-h-[2rem] p-2 bg-gray-50 rounded-md border border-gray-200">—</div>
                              </div>

                              <!-- Commute block: shown when commute legs/summary exist -->
                              <div id="transport-commute-block" class="hidden">
                                <p class="text-[11px] text-gray-500">Commute / Legs</p>
                                <div id="transportation-legs" class="min-h-[2rem] p-2 bg-white rounded-md border border-gray-200 text-gray-700">No transport details available.</div>
                              </div>

                              <!-- Pickup / Departure Time preview - shows only available times -->
                              <div id="transport-time-preview-block" class="hidden">
                                <p class="text-[11px] text-gray-500">Times</p>
                                <div class="flex flex-col gap-1">
                                  <div class="flex items-center justify-between text-sm">
                                    <div class="text-xs text-gray-500">Pickup Time</div>
                                    <div id="transportation-pickup-time" class="text-sm font-semibold text-gray-800">—</div>
                                  </div>
                                  <div class="flex items-center justify-between text-sm">
                                    <div class="text-xs text-gray-500">Departure Time</div>
                                    <div id="transportation-departure-time" class="text-sm font-semibold text-gray-800">—</div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                  



                  </div>

                </div>
              </div>
            </div>

        <!-- Submit Button -->
        <div class="mt-8 text-center">
          <button type="submit" 
                  class="group relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 px-12 py-4 text-white font-bold text-lg ring-1 ring-emerald-400/40 transition-all duration-300 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-emerald-200">
            <span class="absolute inset-0 translate-x-[-120%] bg-white/20 transition-all duration-500 group-hover:translate-x-[120%]"></span>
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"/>
            </svg>
            Generate Personalized Itinerary
          </button>
          <p class="mt-3 text-sm text-gray-600">Your itinerary will be personalized based on your assessment results</p>
        </div>

        <!--Footer-->
        <footer class="mt-6 text-center text-xs text-gray-600">
          <p class="inline-flex items-center gap-1 rounded-full bg-white/70 px-3 py-1 ring-1 ring-gray-200 backdrop-blur">
            &copy; {{ date('Y') }} HikeThere • All rights reserved
          </p>
        </footer>
      </div>
    </div>
  </form>

  <!--Scripts -->
  <style>
    /* Weather Calendar Enhancements */
    .weather-day {
      transition: all 0.2s ease-in-out;
      aspect-ratio: 1;
      padding: 0.5rem;
    }
    
    .weather-day:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Responsive adjustments for 5 columns */
    @media (max-width: 1024px) {
      #weather-calendar .grid {
        grid-template-columns: repeat(5, 1fr);
        gap: 0.5rem;
      }
    }
    
    @media (max-width: 768px) {
      #weather-calendar .grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
      }
    }
    
    @media (max-width: 480px) {
      #weather-calendar .grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
      }
    }
    
    /* Trail Overview Enhancements */
    .line-clamp-3 {
      display: -webkit-box;
      line-clamp: 3;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  </style>
  
  <script>
    //Used for both Stopovers & Side Trips
    function createListItem(text, inputName) {
      const wrapper = document.createElement('div');
      wrapper.className = 'relative mb-2';
      
      // Different colors for different container types
      const isSideTrip = inputName === 'sidetrips';
      const orderColor = isSideTrip ? 'bg-blue-500' : 'bg-emerald-500';
      
      wrapper.innerHTML = `
        <div class="flex items-center gap-3 rounded-md border border-gray-200 bg-white p-3 shadow-sm">
          <!-- Order indicator -->
          <div class="flex-shrink-0 w-6 h-6 rounded-full ${orderColor} text-white text-xs font-bold flex items-center justify-center order-indicator">1</div>
          
          <!-- Location text -->
          <div class="flex-1 min-w-0">
            <span class="editable text-sm text-gray-800 block truncate">${text.replace(/[×x]/g, '').trim()}</span>
          </div>
          
          <!-- Hidden input -->
          <input type="hidden" name="${inputName}[]" value="${text}">
          
          <!-- Reorder buttons -->
          <div class="flex items-center gap-1">
            <button type="button" class="move-up-btn flex-shrink-0 w-6 h-6 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-800 transition-colors border border-gray-300 flex items-center justify-center" title="Move Up">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
              </svg>
            </button>
            <button type="button" class="move-down-btn flex-shrink-0 w-6 h-6 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-800 transition-colors border border-gray-300 flex items-center justify-center" title="Move Down">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
          </div>
          
          <!-- Remove button -->
          <button type="button" class="remove-btn flex-shrink-0 w-6 h-6 rounded-full bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800 transition-colors border border-red-300 flex items-center justify-center" title="Remove">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      `;
      
      return wrapper;
    }

    function addItem(listSelector, text, inputName) {
      const container = document.querySelector(listSelector);
      if (!container) {
        console.error('Container not found:', listSelector);
        return;
      }
      const item = createListItem(text, inputName);
      container.appendChild(item);
      updateOrderNumbers(container);
    }

    function updateOrderNumbers(container) {
      const items = container.querySelectorAll('.relative');
      items.forEach((item, index) => {
        const orderIndicator = item.querySelector('.order-indicator');
        if (orderIndicator) {
          orderIndicator.textContent = index + 1;
        }
      });
    }



    // Add via inputs (NO prompt)
    document.getElementById('add-stopover-btn')?.addEventListener('click', () => {
      const input = document.getElementById('add-stopover-input');
      const value = input.value.trim();
      if (value) {
        addItem('#stopovers-container', value, 'stopovers');
        input.value = '';
        input.focus();
      }
    });

    // Side trips are included in the trail package by default. Provide a toggle
    // that controls whether side trips are included in the generated itinerary.
    const includeCheckbox = document.getElementById('includeSideTrips');
    const includeHidden = document.getElementById('includeSideTripsHidden');
    if (includeCheckbox && includeHidden) {
      // Initialize visual state based on hidden input
      includeCheckbox.checked = includeHidden.value === '1';
      // Visual toggle movement for the dot
      const label = includeCheckbox.closest('label');
      const dot = label?.querySelector('.dot');
      const track = label?.querySelector('.track');
      const updateToggleVisual = () => {
        if (!dot || !track) return;
        if (includeCheckbox.checked) {
          // move knob and set track color (inline to avoid Tailwind purge issues)
          dot.style.transform = 'translateX(1.25rem)';
          track.style.backgroundColor = '#10B981'; // emerald-500
        } else {
          dot.style.transform = 'translateX(0)';
          track.style.backgroundColor = ''; // revert to CSS default
        }
      };

      includeCheckbox.addEventListener('change', () => {
        includeHidden.value = includeCheckbox.checked ? '1' : '0';
        updateToggleVisual();
      });

      // Run once to set initial visual
      updateToggleVisual();
    }

    // Update side trips preview when trail selection changes or the toggle changes
    function renderSideTripsPreview() {
      const preview = document.getElementById('sidetrips-preview');
      const previewList = document.getElementById('sidetrips-preview-list');
      const trailSelect = document.getElementById('trailSelect');
      const includeOn = includeHidden && includeHidden.value === '1';

      if (!preview || !previewList || !trailSelect) return;

      if (!includeOn) {
        preview.classList.add('hidden');
        return;
      }

      const selectedOption = trailSelect.options[trailSelect.selectedIndex];
      if (!selectedOption) {
        previewList.innerHTML = '<em class="text-xs text-gray-400">Select a trail to preview included side trips.</em>';
        preview.classList.remove('hidden');
        return;
      }

      const sideTripsData = selectedOption.getAttribute('data-side-trips');
      let sideTrips = [];
      try {
        sideTrips = sideTripsData ? JSON.parse(sideTripsData) : [];
      } catch (err) {
        sideTrips = [];
      }

      if (!sideTrips || sideTrips.length === 0) {
        previewList.innerHTML = '<em class="text-xs text-gray-500">No side trips are defined for this trail.</em>';
      } else {
        previewList.innerHTML = '<ul class="list-disc pl-5 space-y-1">' + sideTrips.map(s => `<li>${s}</li>`).join('') + '</ul>';
      }

      preview.classList.remove('hidden');
    }

    // Helper: format integer minutes into a human-friendly string
    function formatMinutesHuman(m) {
      if (!m || isNaN(m)) return 'N/A';
      m = parseInt(m, 10);
      if (m <= 0) return 'N/A';
      if (m >= 60*24) {
        const days = Math.floor(m / (60*24));
        const hours = Math.floor((m % (60*24)) / 60);
        return days + ' day' + (days>1 ? 's' : '') + (hours ? ' ' + hours + ' h' : '');
      }
      if (m >= 60) {
        const hours = Math.floor(m / 60);
        const mins = m % 60;
        return hours + ' h' + (mins ? ' ' + mins + ' m' : '');
      }
      return m + ' m';
    }

    document.getElementById('trailSelect')?.addEventListener('change', renderSideTripsPreview);
    includeCheckbox?.addEventListener('change', renderSideTripsPreview);

    // Lightweight transport preview renderer (non-invasive)
    // Reads transport data from (in order): option[data-transport] (server or map-injected),
    // or window.itineraryMap.trails matching the selected option. The data may be:
    // - a plain pickup string (legacy)
    // - an object with { type: 'commute', legs: [...] }
    // - an object with pickup/vehicle fields
    // The renderer chooses only one representation: "commute" when legs are present / type==='commute',
    // otherwise pickup/vehicle.
    function renderTransportationPreview() {
      const previewBlock = document.getElementById('transportation-preview-block');
      const pickupEl = document.getElementById('transportation-pickup');
      const vehicleEl = document.getElementById('transportation-vehicle');
      const legsEl = document.getElementById('transportation-legs');
      const pickupBlock = document.getElementById('transport-pickup-block');
      const vehicleBlock = document.getElementById('transport-vehicle-block');
      const commuteBlock = document.getElementById('transport-commute-block');
  const timeBlock = document.getElementById('transport-time-preview-block');
      const pickupTimeEl = document.getElementById('transportation-pickup-time');
      const departureTimeEl = document.getElementById('transportation-departure-time');
      const trailSelect = document.getElementById('trailSelect');
      if (!previewBlock || !pickupEl || !vehicleEl || !legsEl || !trailSelect) return;

      const selectedOption = trailSelect.options[trailSelect.selectedIndex];
      if (!selectedOption || !selectedOption.value) {
        previewBlock.classList.add('hidden');
        return;
      }

  // Reset preview fields and blocks to safe defaults to avoid leaking values between different transport types
  pickupEl.textContent = '—';
  vehicleEl.textContent = '—';
  legsEl.textContent = '';
  pickupBlock.classList.add('hidden');
  vehicleBlock.classList.add('hidden');
  commuteBlock.classList.add('hidden');
  timeBlock.classList.add('hidden');
  pickupTimeEl.textContent = '—';
  departureTimeEl.textContent = '—';

      let transportRaw = null; // can be string or object
      let matchedTrail = null;

      // 1) Prefer structured data attached directly to the <option>
      // Server embeds a unified object into data-transport with optional keys:
      //  - canonical: original transportation_details payload (string or object)
      //  - pickup_place: explicit pickup place string
      //  - vehicle: explicit vehicle label
      const dataTransport = selectedOption.getAttribute('data-transport');
      if (dataTransport) {
        try {
          const parsed = JSON.parse(dataTransport);
          // If parsed is an object with our wrapper keys, prefer its canonical value
          if (parsed && typeof parsed === 'object' && (parsed.canonical !== undefined || parsed.pickup_place !== undefined || parsed.vehicle !== undefined)) {
            // Use canonical payload when present, otherwise construct a minimal object from pickup/vehicle
            if (parsed.canonical !== undefined && parsed.canonical !== null) {
              transportRaw = parsed.canonical;
              // canonical itself may be a JSON string
              if (typeof transportRaw === 'string' && (transportRaw.trim().startsWith('{') || transportRaw.trim().startsWith('['))) {
                try { transportRaw = JSON.parse(transportRaw); } catch (e) { /* leave as string */ }
              }
            } else {
              // build a small object so downstream code can treat as object
              // prefer vehicle_label if present; otherwise use vehicle key
              transportRaw = { pickup_place_name: parsed.pickup_place || '', vehicle_label: parsed.vehicle_label || parsed.vehicle || '' };
            }
            // Keep parsed wrapper as matchedTrail for fallback lookups
            matchedTrail = parsed;
          } else {
            // Not the wrapper, use parsed directly (may be object or string)
            transportRaw = parsed;
            if (typeof transportRaw === 'string' && (transportRaw.trim().startsWith('{') || transportRaw.trim().startsWith('['))) {
              try { transportRaw = JSON.parse(transportRaw); } catch (e) { /* ignore */ }
            }
          }
        } catch (e) {
          // Not JSON — treat as plain string
          transportRaw = dataTransport;
        }
      }

      // 2) Fall back to global itineraryMap trail object (if present)
      if (transportRaw === null) {
        try {
          if (window.itineraryMap && Array.isArray(window.itineraryMap.trails)) {
            matchedTrail = window.itineraryMap.trails.find(t => {
              if (!t) return false;
              // match by exact name or partial inclusion to tolerate formatting differences
              return (t.name && t.name === selectedOption.value) || (selectedOption.value && t.name && selectedOption.value.includes(t.name));
            });
            if (matchedTrail) {
              transportRaw = matchedTrail.transportation || matchedTrail.transport || matchedTrail.transport_details || matchedTrail.transportation_details || null;
            }
          }
        } catch (e) {
          transportRaw = null;
        }
      }

      // If still null, show fallback but keep preview visible (user requested preview area)
      if (transportRaw === null || transportRaw === '' || (typeof transportRaw === 'object' && Object.keys(transportRaw).length === 0)) {
        pickupEl.textContent = '—';
        vehicleEl.textContent = '—';
        legsEl.textContent = 'No transport details available.';
        // show only the minimal vehicle/commute fallback as previously
        vehicleBlock.classList.remove('hidden');
        commuteBlock.classList.remove('hidden');
        previewBlock.classList.remove('hidden');
        return;
      }

      // Normalize and decide which UI to render
      // Case A: plain string (legacy pickup summary) — but the string may be:
      //  - a JSON string (double-encoded), or
      //  - a human-friendly commute summary (contains arrows or semicolons), or
      //  - an actual pickup/meeting point value.
      if (typeof transportRaw === 'string') {
        const rawTrim = transportRaw.trim();

        // Try to parse if it looks like JSON
        if (rawTrim.startsWith('{') || rawTrim.startsWith('[') || (rawTrim.startsWith('"') && (rawTrim.includes('{') || rawTrim.includes('[')))) {
          try {
            const parsed = JSON.parse(rawTrim);
            // If parsing worked, use the parsed object as transportRaw and continue
            transportRaw = parsed;
          } catch (e) {
            // fallthrough
          }
        }
      }

      // If after attempting parse transportRaw is still a string, decide how to render it
      if (typeof transportRaw === 'string') {
        const s = transportRaw.trim();

        // Special-case pickup notices created by admin UI: "Pick-Up Point: <place>"
        if (/pick-?up point\s*:/i.test(s) || /^pick-?up point$/i.test(s)) {
          const after = s.split(':').slice(1).join(':').trim();
          pickupEl.textContent = after || '—';

          // Show vehicle: prefer matchedTrail or wrapper vehicle_label/vehicle
          let vehicleVal = '';
          if (matchedTrail && typeof matchedTrail === 'object') {
            vehicleVal = matchedTrail.vehicle_label || matchedTrail.vehicle || matchedTrail.transportation_vehicle_label || matchedTrail.transportation_vehicle || '';
          }
          if ((!vehicleVal || vehicleVal === '—') && selectedOption.getAttribute('data-transport')) {
            try {
              const rawOption = JSON.parse(selectedOption.getAttribute('data-transport'));
              if (rawOption && typeof rawOption === 'object') {
                vehicleVal = rawOption.vehicle_label || rawOption.vehicle || rawOption.transportation_vehicle_label || rawOption.transportation_vehicle || vehicleVal;
                // If the canonical payload exists inside the wrapper, it may also contain vehicle info
                if ((!vehicleVal || vehicleVal === '—') && rawOption.canonical) {
                  try {
                    const c = typeof rawOption.canonical === 'string' ? JSON.parse(rawOption.canonical) : rawOption.canonical;
                    if (c && typeof c === 'object') vehicleVal = c.vehicle_label || c.vehicle || c.transportation_vehicle || vehicleVal;
                  } catch (e) { /* ignore parse errors */ }
                }
              }
            } catch (e) { /* ignore */ }
          }

          // As a last resort, try to extract a trailing parenthetical vehicle from the original string
          if ((!vehicleVal || vehicleVal === '—') && s) {
            const pm = s.match(/\(([^)]+)\)\s*$/);
            if (pm && pm[1]) vehicleVal = pm[1].trim();
          }

          // Also, if the wrapper includes a pickup_place explicit field, prefer that as the pickup name
          try {
            const rawOption2 = selectedOption.getAttribute('data-transport');
            if (rawOption2) {
              const parsedOpt2 = JSON.parse(rawOption2);
              if (parsedOpt2 && parsedOpt2.pickup_place) {
                pickupEl.textContent = parsedOpt2.pickup_place;
              }
            }
          } catch (e) { /* ignore */ }

          vehicleEl.textContent = getVehicleLabel(vehicleVal) || '—';
          legsEl.textContent = 'No transport legs available.';
          // ensure pickup+vehicle blocks are visible for this representation
          pickupBlock.classList.remove('hidden');
          vehicleBlock.classList.remove('hidden');
          previewBlock.classList.remove('hidden');
          return;
        }

        // Heuristics: if it looks like a commute summary (contains arrows or semicolons)
        const looksLikeSummary = s.includes('→') || s.includes('->') || s.includes(';');
        if (looksLikeSummary) {
          pickupEl.textContent = '—';
          // If the summary contains semicolons, split into list items for readability
          const parts = s.split(';').map(p => p.trim()).filter(Boolean);
          if (parts.length > 1) {
            const vehicles = new Set();
            const itemsHtml = parts.map(p => {
              const m = p.match(/^(.*)\s*\(([^)]+)\)\s*$/);
              let text = p;
              let veh = '';
              if (m) {
                text = m[1].trim();
                veh = m[2].trim();
              }
              // Normalize vehicle labels so they display like commute vehicle labels
              if (veh) vehicles.add(getVehicleLabel(veh) || veh);
              const safeText = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
              return `<li class="text-sm text-gray-700">${safeText}</li>`;
            }).join('');
            legsEl.innerHTML = '<ul class="list-disc pl-5 space-y-1">' + itemsHtml + '</ul>';
            const vehArr = Array.from(vehicles).filter(Boolean);
            vehicleEl.textContent = vehArr.length ? vehArr.join(', ') : '—';
          } else {
            const m = s.match(/^(.*)\s*\(([^)]+)\)\s*$/);
            if (m) {
              const text = m[1].trim().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
              legsEl.textContent = text || 'Commute details not available.';
              vehicleEl.textContent = getVehicleLabel(m[2].trim()) || m[2].trim();
            } else {
              legsEl.textContent = s || 'Commute details not available.';
              vehicleEl.textContent = '—';
            }
          }
          // Show commute block (hide pickup block)
          commuteBlock.classList.remove('hidden');
          vehicleBlock.classList.remove('hidden');
          pickupBlock.classList.add('hidden');
          previewBlock.classList.remove('hidden');
          return;
        }

        // Otherwise treat as legacy pickup string
  pickupEl.textContent = s || '—';
        let fallbackVeh = '—';
        if (matchedTrail && typeof matchedTrail === 'object') fallbackVeh = matchedTrail.vehicle || matchedTrail.vehicle_label || matchedTrail.transportation_vehicle || matchedTrail.transportation_vehicle_label || fallbackVeh;
        if ((!fallbackVeh || fallbackVeh === '—') && selectedOption.getAttribute('data-transport')) {
          try {
            const rawOpt = JSON.parse(selectedOption.getAttribute('data-transport'));
            if (rawOpt && typeof rawOpt === 'object') fallbackVeh = rawOpt.vehicle || rawOpt.vehicle_label || rawOpt.transportation_vehicle || rawOpt.transportation_vehicle_label || fallbackVeh;
            if ((fallbackVeh === '—' || !fallbackVeh) && rawOpt && rawOpt.canonical) {
              try {
                const c = typeof rawOpt.canonical === 'string' ? JSON.parse(rawOpt.canonical) : rawOpt.canonical;
                if (c && typeof c === 'object') fallbackVeh = c.vehicle || c.vehicle_label || c.transportation_vehicle || fallbackVeh;
              } catch (e) { /* ignore */ }
            }
          } catch (e) { /* ignore */ }
        }
        vehicleEl.textContent = getVehicleLabel(fallbackVeh) || '—';
        legsEl.textContent = 'No transport legs available.';
        // Show pickup + vehicle blocks only
        pickupBlock.classList.remove('hidden');
        vehicleBlock.classList.remove('hidden');
        commuteBlock.classList.add('hidden');
        previewBlock.classList.remove('hidden');
        return;
      }

      // Case B: object
      // If transportRaw is actually a full trail object that contains a nested transport block,
      // unwrap it so callers that accidentally serialized the whole trail still work.
      let t = transportRaw;
      if (typeof t === 'object' && (t.transportation || t.transport || t.transport_details)) {
        t = t.transportation || t.transport || t.transport_details;
      }

      // Helper: normalize vehicle keys into human labels when possible
      function getVehicleLabel(v) {
        if (!v && v !== 0) return '';
        if (typeof v === 'object') return v.label || v.name || '';
        const vs = String(v || '').trim();
        if (!vs) return '';
        // If option wrapper provided a matchedTrail with vehicle_label, prefer that when keys match
        if (matchedTrail && typeof matchedTrail === 'object') {
          try {
            if (matchedTrail.vehicle && String(matchedTrail.vehicle) === vs && matchedTrail.vehicle_label) return matchedTrail.vehicle_label;
            if (matchedTrail.vehicle_label && !matchedTrail.vehicle) return matchedTrail.vehicle_label;
          } catch (e) {}
        }
        // Try to read data-transport for a vehicle_label
        try {
          const rawOpt = selectedOption.getAttribute('data-transport');
          if (rawOpt) {
            const parsedOpt = JSON.parse(rawOpt);
            if (parsedOpt && typeof parsedOpt === 'object') {
              if (parsedOpt.vehicle && String(parsedOpt.vehicle) === vs && parsedOpt.vehicle_label) return parsedOpt.vehicle_label;
              if (parsedOpt.vehicle_label && !parsedOpt.vehicle) return parsedOpt.vehicle_label;
            }
          }
        } catch (e) { /* ignore */ }
        // Small fallback map
        const MAP = { van: 'Van', jeep: 'Jeep', bus: 'Bus', car: 'Car', motorbike: 'Motorbike', bike: 'Bike' };
        if (MAP[vs.toLowerCase()]) return MAP[vs.toLowerCase()];
        return vs;
      }

      // If object explicitly indicates commute or contains legs array — treat as commute
      const legs = Array.isArray(t.legs) && t.legs.length > 0 ? t.legs : (Array.isArray(t.commute) && t.commute.length > 0 ? t.commute : null);
      if (t.type === 'commute' || legs) {
        // Render commute only
        pickupEl.textContent = '—';
        vehicleEl.textContent = '—';

        // helper to escape HTML
        function escapeHtml(s) { return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  if (legs && legs.length > 0) {
          const vehicles = new Set();
          const items = legs.map(l => {
            // Prefer place metadata names when available
            const from = (l.from_place && l.from_place.name) || l.from || l.start || '';
            const to = (l.to_place && l.to_place.name) || l.to || l.end || l.destination || '';

            // vehicle label: normalize via helper so keys map to readable labels
            let vehLabel = getVehicleLabel(l.vehicle || l.vehicle_type || '');
            if (vehLabel) vehicles.add(vehLabel);

            if (from && to) return `<li class="text-sm text-gray-700">${escapeHtml(from)} → ${escapeHtml(to)}</li>`;
            if (from) return `<li class="text-sm text-gray-700">${escapeHtml(from)} → (unknown)</li>`;
            if (to) return `<li class="text-sm text-gray-700">(unknown) → ${escapeHtml(to)}</li>`;
            return `<li class="text-sm text-gray-700">Unknown leg</li>`;
          });

          legsEl.innerHTML = '<ul class="list-disc pl-5 space-y-1">' + items.join('') + '</ul>';
          const vehArr = Array.from(vehicles);
          vehicleEl.textContent = vehArr.length ? vehArr.join(', ') : '—';
          // Show commute and vehicle blocks (pickup hidden)
          commuteBlock.classList.remove('hidden');
          vehicleBlock.classList.remove('hidden');
          pickupBlock.classList.add('hidden');
        } else {
          legsEl.textContent = t.summary || t.transport_summary || 'Commute details not available.';
        }

        previewBlock.classList.remove('hidden');
        return;
      }

      // Case C: object with pickup/vehicle information — render pickup
  let pickupName = t.pickup_place_name || t.pickup || t.meeting_point || (t.pickup_place && (typeof t.pickup_place === 'string' ? t.pickup_place : t.pickup_place.name)) || '';
      let vehicleLabel = t.vehicle_label || t.vehicle || t.vehicle_type || '';

      // Fallback: if the option wrapper has explicit pickup_place or vehicle keys, prefer them
      try {
        const rawOpt = selectedOption.getAttribute('data-transport');
        if (rawOpt) {
          const parsedOpt = JSON.parse(rawOpt);
          if (parsedOpt && typeof parsedOpt === 'object') {
            if (!pickupName && parsedOpt.pickup_place) pickupName = parsedOpt.pickup_place;
            if ((!vehicleLabel || vehicleLabel === '') && (parsedOpt.vehicle_label || parsedOpt.vehicle)) vehicleLabel = parsedOpt.vehicle_label || parsedOpt.vehicle;
          }
        }
      } catch (e) { /* ignore */ }

      // Fallback: if matchedTrail (from window.itineraryMap) has pickup fields, use them
      try {
        if (!pickupName && matchedTrail && typeof matchedTrail === 'object') {
          pickupName = matchedTrail.transportation_pickup_place || matchedTrail.pickup_place || matchedTrail.meeting_point || pickupName;
        }
        if ((!vehicleLabel || vehicleLabel === '') && matchedTrail && typeof matchedTrail === 'object') {
          vehicleLabel = matchedTrail.transportation_vehicle_label || matchedTrail.transportation_vehicle || matchedTrail.vehicle_label || matchedTrail.vehicle || vehicleLabel;
        }
      } catch (e) { /* ignore */ }

      // Fallback: inspect any human summary fields for a 'Pick-Up Point:' prefix or trailing parenthetical vehicle
      try {
        const textCandidates = [t.summary, t.transport_summary, t.transportation_details, t.transport_details];
        for (const txt of textCandidates) {
          if (!txt) continue;
          const sTxt = String(txt || '').trim();
          // If it contains Pick-Up Point: extract following text as pickup name
          const mPrefix = sTxt.match(/(?:pick-?up|pickup|meeting point)\s*:\s*(.+)$/i);
          if (!pickupName && mPrefix && mPrefix[1]) {
            pickupName = mPrefix[1].trim();
            break;
          }
          // If none, try trailing parenthetical as vehicle if we don't have a vehicle yet
          if ((!vehicleLabel || vehicleLabel === '') ) {
            const mPar = sTxt.match(/\(([^)]+)\)\s*$/);
            if (mPar && mPar[1]) {
              vehicleLabel = vehicleLabel || mPar[1].trim();
            }
          }
        }
      } catch (e) { /* ignore */ }

      pickupEl.textContent = pickupName || '—';
      vehicleEl.textContent = getVehicleLabel(vehicleLabel) || '—';

      // If there is summary text and no commute legs, use it for legs area
      if (t.summary || t.transport_summary || t.transportation_details || t.transport_details) {
        legsEl.textContent = t.summary || t.transport_summary || t.transportation_details || t.transport_details;
      } else {
        legsEl.textContent = 'No transport legs available.';
      }

      // By default show pickup+vehicle blocks for this object shape; hide commute block
      pickupBlock.classList.remove('hidden');
      vehicleBlock.classList.remove('hidden');
      commuteBlock.classList.add('hidden');

      // Also attempt to populate pickup/departure times if present on matchedTrail/package
      try {
        // selectedOption may contain a wrapper with pickup_time/departure_time
        const selectedOptionRaw = selectedOption ? selectedOption.getAttribute('data-transport') : null;
        let optParsed = null;
        if (selectedOptionRaw) {
          try { optParsed = JSON.parse(selectedOptionRaw); } catch (e) { optParsed = null; }
        }
  // Also read explicit pickup/departure attributes placed on the <option>
  const optPickupAttr = selectedOption ? selectedOption.getAttribute('data-pickup-time') : null;
  const optDepartureAttr = selectedOption ? selectedOption.getAttribute('data-departure-time') : null;
  // Short-format attributes (added as a fallback in case full time has seconds or different format)
  const optPickupShortAttr = selectedOption ? selectedOption.getAttribute('data-pickup-time-short') : null;
  const optDepartureShortAttr = selectedOption ? selectedOption.getAttribute('data-departure-time-short') : null;

        // matchedTrail may exist from earlier; check package-level times first
        let pickupTime = null;
        let departureTime = null;
        if (matchedTrail && typeof matchedTrail === 'object') {
          if (matchedTrail.package) {
            // Check multiple possible keys from package/map JSON
            pickupTime = matchedTrail.package.pickup_time || matchedTrail.package.pickup_time_short || matchedTrail.package.pickup_time_formatted || null;
            departureTime = matchedTrail.package.departure_time || matchedTrail.package.departure_time_short || matchedTrail.package.departure_time_formatted || null;
          }
          // also check top-level matchedTrail fields (some map payloads put times at root)
          if (!pickupTime) pickupTime = matchedTrail.pickup_time || matchedTrail.pickup_time_short || null;
          if (!departureTime) departureTime = matchedTrail.departure_time || matchedTrail.departure_time_short || null;
        }

        // Option wrapper overrides
  if (!pickupTime && optParsed && optParsed.pickup_time) pickupTime = optParsed.pickup_time;
  if (!departureTime && optParsed && optParsed.departure_time) departureTime = optParsed.departure_time;
  // Also accept short-format keys in parsed wrapper
  if (!pickupTime && optParsed && optParsed.pickup_time_short) pickupTime = optParsed.pickup_time_short;
  if (!departureTime && optParsed && optParsed.departure_time_short) departureTime = optParsed.departure_time_short;
  // fallback to explicit data-* attributes on the <option>
  if (!pickupTime && optPickupAttr) pickupTime = optPickupAttr;
  if (!departureTime && optDepartureAttr) departureTime = optDepartureAttr;
  // fallback to explicit short attrs
  if (!pickupTime && optPickupShortAttr) pickupTime = optPickupShortAttr;
  if (!departureTime && optDepartureShortAttr) departureTime = optDepartureShortAttr;

        // Also check top-level t (transport object) for times
        if (!pickupTime && t && t.pickup_time) pickupTime = t.pickup_time;
        if (!departureTime && t && t.departure_time) departureTime = t.departure_time;

        // Helper to normalize various time formats into HH:MM where possible
        function normalizeToShort(t) {
          if (!t && t !== 0) return null;
          try {
            const s = String(t).trim();
            // If already HH:MM or H:MM, return first 5 chars
            const hhmm = s.match(/^(\d{1,2}:\d{2})/);
            if (hhmm) return hhmm[1].padStart(5, '0');
            // If format includes seconds (HH:MM:SS), strip seconds
            const withSec = s.match(/^(\d{2}:\d{2}:\d{2})/);
            if (withSec) return withSec[1].substr(0,5);
            // If ISO datetime, extract time part
            const iso = s.match(/T(\d{2}:\d{2}):\d{2}/);
            if (iso) return iso[1];
            // Last resort: return original trimmed
            return s;
          } catch (e) { return String(t).trim(); }
        }

        // Debug: show selected option attributes and matchedTrail pickup/departure data
        try {
          const dbg = {
            optPickupAttr: optPickupAttr || null,
            optDepartureAttr: optDepartureAttr || null,
            optPickupShortAttr: optPickupShortAttr || null,
            optDepartureShortAttr: optDepartureShortAttr || null,
            optParsed: optParsed || null,
            matchedTrailPackagePickup: matchedTrail && matchedTrail.package ? (matchedTrail.package.pickup_time || matchedTrail.package.pickup_time_short) : null,
            matchedTrailPackageDeparture: matchedTrail && matchedTrail.package ? (matchedTrail.package.departure_time || matchedTrail.package.departure_time_short) : null,
            pickupTimeRaw: pickupTime || null,
            departureTimeRaw: departureTime || null,
            timeBlockHidden: timeBlock ? timeBlock.classList.contains('hidden') : null,
            previewBlockHidden: previewBlock ? previewBlock.classList.contains('hidden') : null,
          };
          console.debug('transportPreview-debug', dbg);
          const panel = document.getElementById('transport-debug-panel');
          if (panel) {
            panel.textContent = `pickup:${dbg.pickupTimeRaw || 'null'} departure:${dbg.departureTimeRaw || 'null'} timeHidden:${dbg.timeBlockHidden}`;
            panel.style.color = (dbg.pickupTimeRaw || dbg.departureTimeRaw) ? '#064e3b' : '#9f1239';
          }
        } catch (e) { /* ignore */ }

        // Normalize and show time block only if at least one present
        const pickupNorm = pickupTime ? normalizeToShort(pickupTime) : null;
        const departureNorm = departureTime ? normalizeToShort(departureTime) : null;
        if (pickupNorm || departureNorm) {
          if (pickupNorm) pickupTimeEl.textContent = pickupNorm;
          else pickupTimeEl.textContent = '—';
          if (departureNorm) departureTimeEl.textContent = departureNorm;
          else departureTimeEl.textContent = '—';
          timeBlock.classList.remove('hidden');
        } else {
          timeBlock.classList.add('hidden');
        }

      } catch (e) { /* ignore */ }

      previewBlock.classList.remove('hidden');
    }

    // Hook into the trailSelect change to update transport preview as well
    document.getElementById('trailSelect')?.addEventListener('change', () => {
      try { renderTransportationPreview(); } catch (e) { /* silent */ }
      try { updateHikingStartTime(); } catch (e) { /* silent */ }
    });

    // Update Hiking Start Time Display
    function updateHikingStartTime() {
      const display = document.getElementById('hiking-start-time-display');
      const trailSelect = document.getElementById('trailSelect');
      
      if (!display || !trailSelect) return;
      
      const selectedOption = trailSelect.options[trailSelect.selectedIndex];
      if (!selectedOption || !selectedOption.value) {
        display.textContent = '—';
        return;
      }
      
      // Try to get hiking start time from the trail's event data
      // First check if there's a data attribute for hiking start time
      const hikingStartTime = selectedOption.getAttribute('data-hiking-start-time');
      
      if (hikingStartTime) {
        // Format the time (assuming it's in HH:mm format)
        try {
          const [hours, minutes] = hikingStartTime.split(':');
          const hour = parseInt(hours, 10);
          const min = minutes || '00';
          const ampm = hour >= 12 ? 'PM' : 'AM';
          const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
          display.textContent = `${displayHour}:${min} ${ampm}`;
        } catch (e) {
          display.textContent = hikingStartTime;
        }
      } else {
        display.textContent = '—';
      }
    }
    
    // Initialize hiking start time on page load
    document.addEventListener('DOMContentLoaded', () => {
      updateHikingStartTime();
    });

    // Render Trail Opening / Closing times preview

    function renderTrailTimesPreview(passedTrail) {
      const preview = document.getElementById('trail-times-preview');
      const openingEl = document.getElementById('trail-opening');
      const closingEl = document.getElementById('trail-closing');
      const noteEl = document.getElementById('trail-hours-note');
      const trailSelect = document.getElementById('trailSelect');

      if (!preview || !openingEl || !closingEl || !trailSelect) return;

      // If a trail object is passed (from map), prefer it
      let opening = null;
      let closing = null;
      let source = 'option';

      try {
        if (passedTrail && typeof passedTrail === 'object') {
          // common keys: opening_time, closing_time, opening_time_short, closing_time_short, open_time, close_time, hours
          opening = passedTrail.opening_time_short || passedTrail.opening_time || passedTrail.open_time || (passedTrail.hours && (passedTrail.hours.open || passedTrail.hours.opening)) || (passedTrail.package && (passedTrail.package.opening_time_short || passedTrail.package.opening_time || (passedTrail.package.hours && (passedTrail.package.hours.open || passedTrail.package.hours.opening)))) || null;
          closing = passedTrail.closing_time_short || passedTrail.closing_time || passedTrail.close_time || (passedTrail.hours && (passedTrail.hours.close || passedTrail.hours.closing)) || (passedTrail.package && (passedTrail.package.closing_time_short || passedTrail.package.closing_time || (passedTrail.package.hours && (passedTrail.package.hours.close || passedTrail.package.hours.closing)))) || null;
          source = 'map';
        }
      } catch (e) { /* ignore */ }

      // If none from map or not provided, inspect the selected <option> data attributes
      if ((!opening || !closing) && trailSelect.options[trailSelect.selectedIndex]) {
        const selectedOption = trailSelect.options[trailSelect.selectedIndex];
        if (selectedOption) {
          try {
            // Allow for data-hours JSON wrapper or explicit data-opening/data-closing
            const dataHours = selectedOption.getAttribute('data-hours');
            if (dataHours) {
              try {
                const parsed = JSON.parse(dataHours);
                opening = opening || parsed.opening || parsed.open || parsed.opens || null;
                closing = closing || parsed.closing || parsed.close || parsed.closes || null;
              } catch (e) {
                // not JSON, try to parse simple 'HH:MM-HH:MM' format
                const m = String(dataHours).split('-').map(s => s.trim());
                if (m.length === 2) { opening = opening || m[0]; closing = closing || m[1]; }
              }
            }

            // explicit attributes
            opening = opening || selectedOption.getAttribute('data-opening') || selectedOption.getAttribute('data-open') || opening;
            closing = closing || selectedOption.getAttribute('data-closing') || selectedOption.getAttribute('data-close') || closing;
          } catch (e) { /* ignore */ }
        }
      }

      // Additional fallback: if still missing, try to locate the selected trail in window.itineraryMap.trails
      if ((!opening || !closing)) {
        try {
          const selVal = trailSelect.value;
          const selectedOption = trailSelect.options[trailSelect.selectedIndex];
          let matchedTrail = null;

          if (window.itineraryMap && Array.isArray(window.itineraryMap.trails)) {
            matchedTrail = window.itineraryMap.trails.find(t => {
              if (!t) return false;
              // match by name (some options contain the trail name as value)
              if (t.name && selVal && (t.name === selVal || (selVal.includes && selVal.includes(t.name)))) return true;
              // match by id if option holds a trail id
              if (selectedOption && selectedOption.dataset && selectedOption.dataset.trailId) {
                if (String(t.id) === String(selectedOption.dataset.trailId)) return true;
              }
              return false;
            });
          }

          if (matchedTrail) {
            // Try package first then direct fields; include short-form keys if present
            opening = opening || (matchedTrail.package && (matchedTrail.package.opening_time_short || matchedTrail.package.opening_time || (matchedTrail.package.hours && (matchedTrail.package.hours.open || matchedTrail.package.hours.opening)))) || matchedTrail.opening_time_short || matchedTrail.opening_time || matchedTrail.open_time || (matchedTrail.hours && (matchedTrail.hours.open || matchedTrail.hours.opening)) || null;
            closing = closing || (matchedTrail.package && (matchedTrail.package.closing_time_short || matchedTrail.package.closing_time || (matchedTrail.package.hours && (matchedTrail.package.hours.close || matchedTrail.package.hours.closing)))) || matchedTrail.closing_time_short || matchedTrail.closing_time || matchedTrail.close_time || (matchedTrail.hours && (matchedTrail.hours.close || matchedTrail.hours.closing)) || null;
            source = 'map';
          }
        } catch (e) { /* ignore */ }
      }

      // Normalize display values
      const displayOpening = opening ? String(opening).trim() : null;
      const displayClosing = closing ? String(closing).trim() : null;

      // Debug logging to help determine why preview may not show
      try { console.debug('renderTrailTimesPreview', { passedTrail: passedTrail || null, opening: displayOpening, closing: displayClosing, source }); } catch (e) { /* ignore */ }

      if (displayOpening || displayClosing) {
        openingEl.textContent = displayOpening || '—';
        closingEl.textContent = displayClosing || '—';
        noteEl.textContent = source === 'map' ? 'Hours from map data' : 'Hours from trail data';
        preview.classList.remove('hidden');
      } else {
        // Show a clear fallback when no hours are defined so users see the preview area
        openingEl.textContent = '—';
        closingEl.textContent = '—';
        noteEl.textContent = 'No hours defined for this trail';
        preview.classList.remove('hidden');
      }
    }







    // Delegated events: remove + move + inline edit
    document.addEventListener('click', (e) => {
      // Remove button
      const removeBtn = e.target.closest('.remove-btn');
      if (removeBtn) {
        e.preventDefault();
        e.stopPropagation();
        
        const row = removeBtn.closest('.relative');
        if (row) {
          const parent = row.parentElement;
          
          // Remove immediately
          row.remove();
          
          // Update order numbers if parent exists
          if (parent) {
            updateOrderNumbers(parent);
          }
        }
        return;
      }
      
      // Move up button
      const moveUpBtn = e.target.closest('.move-up-btn');
      if (moveUpBtn) {
        e.preventDefault();
        e.stopPropagation();
        
        const row = moveUpBtn.closest('.relative');
        if (row) {
          const parent = row.parentElement;
          const prevRow = row.previousElementSibling;
          
          if (prevRow && prevRow.classList.contains('relative')) {
            parent.insertBefore(row, prevRow);
            updateOrderNumbers(parent);
          }
        }
        return;
      }
      
      // Move down button
      const moveDownBtn = e.target.closest('.move-down-btn');
      if (moveDownBtn) {
        e.preventDefault();
        e.stopPropagation();
        
        const row = moveDownBtn.closest('.relative');
        if (row) {
          const parent = row.parentElement;
          const nextRow = row.nextElementSibling;
          
          if (nextRow && nextRow.classList.contains('relative')) {
            parent.insertBefore(nextRow, row);
            updateOrderNumbers(parent);
          }
        }
        return;
      }
    });

    // Inline edit (double click) with Enter/Escape support
    document.addEventListener('dblclick', (e) => {
      if (e.target.classList.contains('editable')) {
        const span = e.target;
        const currentText = span.textContent;
        const input = document.createElement('input');
        input.type = 'text';
        input.value = currentText;
        input.className = 'w-full rounded-md border border-gray-300 px-2 py-1 text-sm text-gray-800 ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200';
        span.replaceWith(input);
        input.focus();

        const commit = () => {
          const newSpan = document.createElement('span');
          newSpan.className = 'editable text-sm text-gray-800';
          newSpan.textContent = input.value;
          const hiddenInput = input.parentElement.querySelector('input[type="hidden"]');
          if (hiddenInput) hiddenInput.value = input.value;
          input.replaceWith(newSpan);
        };

        input.addEventListener('blur', commit);
        input.addEventListener('keydown', (ev) => {
          if (ev.key === 'Enter') commit();
          if (ev.key === 'Escape') { input.value = currentText; commit(); }
        });
      }
    });

  // Set default date to tomorrow
  document.addEventListener('DOMContentLoaded', function() {
      const dateInput = document.querySelector('input[name="date"]');
      if (dateInput) {
        // Set minimum selectable date to today to prevent selecting past dates
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const isoToday = `${yyyy}-${mm}-${dd}`;
        dateInput.min = isoToday;

        // If no value already set, default to tomorrow for convenience
        if (!dateInput.value) {
          const tomorrow = new Date();
          tomorrow.setDate(tomorrow.getDate() + 1);
          dateInput.value = tomorrow.toISOString().split('T')[0];
        }
      }
      
      // Region filter functionality
      const regionFilter = document.getElementById('regionFilter');
      const trailSelect = document.getElementById('trailSelect');
      
      if (regionFilter && trailSelect) {
        // Store all options when page loads
        const allOptions = Array.from(trailSelect.options);
        
        regionFilter.addEventListener('change', function() {
          const selectedRegion = this.value.toLowerCase().trim();
          
          // Remove all options except the first one (placeholder)
          while (trailSelect.options.length > 1) {
            trailSelect.remove(1);
          }
          
          // Get optgroups if they exist
          const optgroups = {};
          allOptions.forEach(option => {
            if (option.value === '') return; // Skip placeholder
            
            const region = option.getAttribute('data-region');
            const parentLabel = option.parentElement?.label || '';
            
            // Filter by region if selected
            if (selectedRegion === '' || (region && region.toLowerCase() === selectedRegion)) {
              // Create optgroups if they don't exist
              if (parentLabel && !optgroups[parentLabel]) {
                const optgroup = document.createElement('optgroup');
                optgroup.label = parentLabel;
                optgroups[parentLabel] = optgroup;
                trailSelect.appendChild(optgroup);
              }
              
              // Clone and append the option
              const clonedOption = option.cloneNode(true);
              if (parentLabel && optgroups[parentLabel]) {
                optgroups[parentLabel].appendChild(clonedOption);
              } else {
                trailSelect.appendChild(clonedOption);
              }
            }
          });
          
          // Update trail count display
          const visibleCount = trailSelect.options.length - 1; // Exclude placeholder
          const selectedTrailCount = document.getElementById('selectedTrailCount');
          if (selectedTrailCount) {
            selectedTrailCount.textContent = visibleCount;
          }
          
          // Show notification
          if (selectedRegion) {
            const regionName = regionFilter.options[regionFilter.selectedIndex].text;
            showNotification(`Showing ${visibleCount} trails in ${regionName}`, 'info');
          } else {
            showNotification(`Showing all ${visibleCount} trails`, 'info');
          }
        });
      }
      
      // Load saved location from localStorage if available
      loadSavedLocation();
      
      // Initialize existing items with reordering functionality
      initializeExistingItems();
      
      // Update trail count display
      updateTrailCountDisplay();
      
      // Simple initialization
      initializeExistingItems();
      
      // Initialize trail previews on page load
      try { renderSideTripsPreview(); } catch (e) { /* ignore if function not available */ }
      try { renderTrailTimesPreview(); } catch (e) { /* ignore if function not available */ }
      

    });

    // Weather Integration Functions
    let currentWeatherData = null;

    // Handle trail selection and update display (updated to include reviews)
    document.getElementById('trailSelect')?.addEventListener('change', function() {
      const selectedTrail = this.value;
      const selectedTrailDisplay = document.getElementById('selectedTrailDisplay');
      const trailNameInput = document.getElementById('trailNameInput');
      const destinationDisplay = document.getElementById('destinationDisplay');
      const selectedTrailCount = document.getElementById('selectedTrailCount');
      const trailDifficultyDisplay = document.getElementById('trailDifficultyDisplay');
      
      if (selectedTrail) {
        // Get the selected option text for display
        const trailOptions = Array.from(this.options);
        const selectedOption = trailOptions.find(option => option.value === selectedTrail);
        
        if (selectedOption) {
          const trailText = selectedOption.text;
          const difficulty = selectedOption.getAttribute('data-difficulty') || 'Unknown';
          const mountainName = selectedOption.getAttribute('data-mountain') || '';
          const fullAddress = selectedOption.getAttribute('data-full-address') || '';
          
          // Update trail name display
          if (selectedTrailDisplay) {
            selectedTrailDisplay.textContent = selectedTrail;
          }
          
          // Update trail name hidden input - CRITICAL FIX
          if (trailNameInput) {
            trailNameInput.value = selectedTrail;
          }
          
          // Update destination display with formatted output
          if (destinationDisplay) {
            // Format: Trail Name, Mountain Name
            // Under: Full Address
            let destinationHTML = `<div class="font-medium text-gray-900">${selectedTrail}`;
            if (mountainName) {
              destinationHTML += `, ${mountainName}`;
            }
            destinationHTML += `</div>`;
            if (fullAddress) {
              destinationHTML += `<div class="text-xs text-gray-600 mt-1">${fullAddress}</div>`;
            }
            destinationDisplay.innerHTML = destinationHTML;
          }
          
          // Update difficulty display
          if (trailDifficultyDisplay) {
            trailDifficultyDisplay.textContent = `Difficulty: ${difficulty}`;
            // Add color coding based on difficulty
            trailDifficultyDisplay.className = 'text-sm font-medium';
            if (difficulty.toLowerCase() === 'beginner' || difficulty.toLowerCase() === 'easy') {
              trailDifficultyDisplay.classList.add('text-green-700');
            } else if (difficulty.toLowerCase() === 'intermediate' || difficulty.toLowerCase() === 'moderate') {
              trailDifficultyDisplay.classList.add('text-yellow-700');
            } else if (difficulty.toLowerCase() === 'advanced' || difficulty.toLowerCase() === 'difficult' || difficulty.toLowerCase() === 'hard') {
              trailDifficultyDisplay.classList.add('text-red-700');
            } else {
              trailDifficultyDisplay.classList.add('text-gray-700');
            }
          }
          
          // Update trail count to show selected trail
          if (selectedTrailCount) {
            selectedTrailCount.textContent = `Selected: ${selectedTrail}`;
            selectedTrailCount.className = 'text-sm font-medium text-emerald-700';
          }
          
          // Load trail overview for the selected trail
          loadTrailOverview();
          
          // Load weather for the selected trail
          loadWeatherForSelectedTrail();

          // Update transportation preview (try immediately; loadTrailOverview will also call it when data is ready)
          try { setTimeout(() => { renderTransportationPreview(); }, 100); } catch (e) { /* ignore */ }
          // Render trail opening/closing times preview
          try { setTimeout(() => { renderTrailTimesPreview(); }, 50); } catch (e) { /* ignore */ }
          
        } else {
          // Fallback if option not found
          if (selectedTrailDisplay) selectedTrailDisplay.textContent = selectedTrail;
          if (trailNameInput) trailNameInput.value = selectedTrail;
          if (destinationDisplay) {
            destinationDisplay.innerHTML = `<div class="font-medium text-gray-900">${selectedTrail}</div>`;
          }
          if (selectedTrailCount) {
            selectedTrailCount.textContent = `Selected: ${selectedTrail}`;
            selectedTrailCount.className = 'text-sm font-medium text-emerald-700';
          }
        }
      } else {
        // Reset displays when no trail is selected
        if (selectedTrailDisplay) selectedTrailDisplay.textContent = 'Select trail first';
        if (trailNameInput) trailNameInput.value = '';
        if (destinationDisplay) {
          destinationDisplay.innerHTML = '<div class="text-gray-500">Select trail first</div>';
        }
        if (selectedTrailCount) {
          selectedTrailCount.textContent = `${trails.length} trails available`;
          selectedTrailCount.className = 'text-sm font-medium text-emerald-700';
        }
        if (trailDifficultyDisplay) {
          trailDifficultyDisplay.textContent = 'Difficulty: Not selected';
        }
        
        // Reset trail overview
        resetTrailOverview();
        
        // Reset weather display
        resetWeatherDisplay();
      }
    });

    async function loadWeatherForSelectedTrail() {
      const weatherLoading = document.getElementById('weather-loading');
      const weatherCalendar = document.getElementById('weather-calendar');
      const weatherError = document.getElementById('weather-error');
      const weatherNoTrail = document.getElementById('weather-no-trail');
      
      // Show loading state
      weatherLoading.classList.remove('hidden');
      weatherCalendar.classList.add('hidden');
      weatherError.classList.add('hidden');
      weatherNoTrail.classList.add('hidden');
      
      try {
        // Get coordinates from selected trail
        const coordinates = await getTrailCoordinates();
        if (!coordinates) {
          throw new Error('Unable to get trail coordinates');
        }
        
        // Fetch weather forecast for the trail location
        const forecastResponse = await fetch(`/api/weather/forecast?lat=${coordinates.lat}&lng=${coordinates.lng}`);
        
        if (!forecastResponse.ok) {
          throw new Error('Failed to fetch weather data');
        }
        
        const forecastData = await forecastResponse.json();
        currentWeatherData = forecastData;
        
        // Update location info to show it's the trail location
        updateWeatherLocationInfo(forecastData.location);
        
        // Render weather calendar
        renderWeatherCalendar(forecastData);
        
        // Hide loading, show calendar
        weatherLoading.classList.add('hidden');
        weatherCalendar.classList.remove('hidden');
        
      } catch (error) {
        console.error('Weather loading error:', error);
        weatherLoading.classList.add('hidden');
        weatherError.classList.remove('hidden');
      }
    }

    async function getTrailCoordinates() {
      // Get the selected trail from the dropdown
      const trailSelect = document.getElementById('trailSelect');
      if (!trailSelect || !trailSelect.value) {
        return null;
      }
      
      // If we have the itinerary map with trail data, use that
      if (window.itineraryMap && window.itineraryMap.trails) {
        const selectedTrail = window.itineraryMap.trails.find(t => 
          t.name === trailSelect.value || trailSelect.value.includes(t.name)
        );
        
        if (selectedTrail && selectedTrail.coordinates) {
          return {
            lat: parseFloat(selectedTrail.coordinates.lat),
            lng: parseFloat(selectedTrail.coordinates.lng)
          };
        }
      }
      
      // Fallback: try to get coordinates from the map markers
      if (window.itineraryMap && window.itineraryMap.markers) {
        // This would need to be implemented based on your map structure
        return null;
      }
      
      // If no trail coordinates found, try to get from the trail data in the dropdown
      const selectedOption = trailSelect.options[trailSelect.selectedIndex];
      if (selectedOption && selectedOption.dataset.coordinates) {
        const coords = selectedOption.dataset.coordinates.split(',');
        return {
          lat: parseFloat(coords[0]),
          lng: parseFloat(coords[1])
        };
      }
      
      return null;
    }

    function updateWeatherLocationInfo(location) {
      const locationInfoElement = document.getElementById('weather-location-info');
      if (locationInfoElement) {
        locationInfoElement.textContent = `${location.city}, ${location.country}`;
      }
    }

    function showNoTrailState() {
      const weatherLoading = document.getElementById('weather-loading');
      const weatherCalendar = document.getElementById('weather-calendar');
      const weatherError = document.getElementById('weather-error');
      const weatherNoTrail = document.getElementById('weather-no-trail');
      
      weatherLoading.classList.add('hidden');
      weatherCalendar.classList.add('hidden');
      weatherError.classList.add('hidden');
      weatherNoTrail.classList.remove('hidden');
      
      // Don't load weather for current location automatically - only when trail is selected
      // This prevents interference with the current location functionality
    }

    async function loadWeatherForCurrentLocation() {
      try {
        if (navigator.geolocation) {
          const position = await new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
              timeout: 10000,
              enableHighAccuracy: true
            });
          });
          
          const coordinates = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          
          // Fetch weather for current location
          const forecastResponse = await fetch(`/api/weather/forecast?lat=${coordinates.lat}&lng=${coordinates.lng}`);
          
          if (forecastResponse.ok) {
            const forecastData = await forecastResponse.json();
            currentWeatherData = forecastData;
            
            // Update location info to show it's current location
            const locationInfoElement = document.getElementById('weather-location-info');
            if (locationInfoElement) {
              locationInfoElement.textContent = `Current Location: ${forecastData.location.city}, ${forecastData.location.country}`;
            }
            
            // Render weather calendar
            renderWeatherCalendar(forecastData);
            
            // Hide no trail state, show calendar
            const weatherNoTrail = document.getElementById('weather-no-trail');
            weatherNoTrail.classList.add('hidden');
            weatherCalendar.classList.remove('hidden');
          }
        }
      } catch (error) {
        console.log('Could not load weather for current location:', error);
      }
    }

    function renderWeatherCalendar(weatherData) {
      const calendarContainer = document.querySelector('#weather-calendar .grid');
      if (!calendarContainer) return;
      
      calendarContainer.innerHTML = '';
      
      console.log('Rendering weather calendar with data:', weatherData.forecast); // Debug log
      
      weatherData.forecast.forEach((day, index) => {
        console.log(`Day ${index}:`, day); // Debug log for each day
        const dayElement = createWeatherDayElement(day, index);
        calendarContainer.appendChild(dayElement);
      });
      
      // Update weather summary
      updateWeatherSummary(weatherData.forecast);
    }

    function createWeatherDayElement(day, index) {
      const dayDiv = document.createElement('div');
      dayDiv.className = 'text-center p-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md';
      
      console.log(`Creating day element for ${day.day_label}:`, day); // Debug log
      
      let dayContent = '';
      
      // Use the backend-provided day label and styling
      if (day.day_label === 'TODAY') {
        dayDiv.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50');
        dayContent += '<div class="text-xs text-emerald-600 font-bold mb-1">TODAY</div>';
      } else if (day.day_label === 'TOMORROW') {
        dayDiv.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
        dayContent += '<div class="text-xs text-blue-600 font-bold mb-1">TOMORROW</div>';
      } else {
        dayContent += '<div class="text-xs font-semibold text-gray-800 mb-1">' + day.day_label + '</div>';
      }
      
      // Weather icon
      const iconUrl = `https://openweathermap.org/img/wn/${day.icon}.png`;
      dayContent += `
        <div class="mb-2">
          <img src="${iconUrl}" alt="${day.condition}" class="w-6 h-6 mx-auto" onerror="this.src='/img/default-trail.jpg'">
        </div>
        
        <!-- Date under weather icon -->
        <div class="text-xs text-gray-600 mb-1">${day.date_formatted}</div>
        
        <!-- Temperature -->
        <div class="text-sm font-bold text-gray-900 mb-1">${day.temp_midday}°</div>
      `;
      
      dayDiv.innerHTML = dayContent;
      
      // Add click event to show detailed forecast
      dayDiv.addEventListener('click', () => showDetailedForecast(day));
      
      return dayDiv;
    }

    function updateWeatherSummary(forecast) {
      // Find best day (lowest precipitation, moderate temperature)
      const bestDay = forecast.reduce((best, current) => {
        const currentScore = calculateHikingScore(current);
        const bestScore = calculateHikingScore(best);
        return currentScore > bestScore ? current : best;
      });
      
      // Calculate overall rain risk
      const totalPrecipitation = forecast.reduce((sum, day) => sum + day.precipitation, 0);
      const averagePrecipitation = totalPrecipitation / forecast.length;
      
      // Update summary elements
      const bestDayElement = document.getElementById('best-day-weather');
      const rainRiskElement = document.getElementById('rain-risk-weather');
      
      if (bestDayElement) bestDayElement.textContent = bestDay.day_label;
      if (rainRiskElement) {
        if (averagePrecipitation < 20) {
          rainRiskElement.textContent = 'Low';
          rainRiskElement.className = 'text-emerald-700';
        } else if (averagePrecipitation < 50) {
          rainRiskElement.textContent = 'Medium';
          rainRiskElement.className = 'text-yellow-700';
        } else {
          rainRiskElement.textContent = 'High';
          rainRiskElement.className = 'text-red-700';
        }
      }
    }

    function calculateHikingScore(day) {
      let score = 100;
      
      // Reduce score for high precipitation
      score -= day.precipitation * 0.5;
      
      // Reduce score for extreme temperatures
      if (day.temp_max > 30 || day.temp_min < 10) {
        score -= 20;
      }
      
      // Bonus for clear conditions
      if (day.condition.includes('Clear')) {
        score += 10;
      }
      
      // Penalty for stormy conditions
      if (day.condition.includes('Thunderstorm')) {
        score -= 30;
      }
      
      return Math.max(0, score);
    }

    function showDetailedForecast(day) {
      // Create a modal with detailed hourly forecast
      const modal = document.createElement('div');
      modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
      modal.innerHTML = `
        <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">${day.day_label}, ${day.date_formatted}</h3>
            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          
          <!-- Current day summary -->
          <div class="bg-gradient-to-r from-blue-50 to-emerald-50 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-4">
              <img src="https://openweathermap.org/img/wn/${day.icon}@2x.png" alt="${day.condition}" class="w-20 h-20">
              <div class="flex-1">
                <div class="text-3xl font-bold text-gray-900 mb-1">${day.temp_midday}°C</div>
                <div class="text-lg text-gray-700 mb-1">${day.condition}</div>
                <div class="text-sm text-gray-600">${day.description}</div>
              </div>
              <div class="text-right">
                <div class="text-sm text-gray-600 mb-1">Min: <span class="font-semibold text-blue-600">${day.temp_min}°C</span></div>
                <div class="text-sm text-gray-600">Max: <span class="font-semibold text-red-600">${day.temp_max}°C</span></div>
                <div class="text-sm text-gray-600 mt-2">Humidity: <span class="font-semibold">${day.humidity}%</span></div>
                <div class="text-sm text-gray-600">Wind: <span class="font-semibold">${day.wind_speed} km/h</span></div>
              </div>
            </div>
          </div>
          
          <!-- Hourly forecast -->
          <div class="mb-6">
            <h4 class="font-semibold text-gray-800 mb-3 text-lg">Hourly Forecast</h4>
            <div class="grid grid-cols-4 gap-3">
              ${day.hourly_forecasts.map(hour => `
                <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-200">
                  <div class="text-sm font-semibold text-gray-700 mb-2">${hour.time}</div>
                  <img src="https://openweathermap.org/img/wn/${hour.icon}.png" alt="${hour.condition}" class="w-8 h-8 mx-auto mb-2">
                  <div class="text-lg font-bold text-gray-900 mb-1">${hour.temp}°C</div>
                  <div class="text-xs text-gray-600 mb-1">${hour.condition}</div>
                  ${hour.precipitation > 20 ? `
                    <div class="text-xs text-blue-600 font-medium">💧 ${hour.precipitation}%</div>
                  ` : '<div class="text-xs text-gray-400">-</div>'}
                </div>
              `).join('')}
            </div>
          </div>
          
          <!-- Hiking recommendations -->
          <div class="p-4 bg-amber-50 rounded-lg border border-amber-200">
            <h4 class="font-semibold text-amber-800 mb-3 text-lg">Hiking Recommendations</h4>
            <div class="text-sm text-amber-700 space-y-2">
              ${getHikingRecommendations(day)}
            </div>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      
      // Close modal when clicking outside
      modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.remove();
      });
    }

    function getHikingRecommendations(day) {
      const recommendations = [];
      
      // Temperature recommendations
      if (day.temp_max > 30) {
        recommendations.push('🌡️ High temperatures - start early, bring extra water');
      } else if (day.temp_min < 15) {
        recommendations.push('❄️ Cool temperatures - bring warm layers');
      }
      
      // Precipitation recommendations
      if (day.precipitation > 60) {
        recommendations.push('🌧️ High chance of rain - consider postponing or bring rain gear');
      } else if (day.precipitation > 30) {
        recommendations.push('🌦️ Some rain expected - bring waterproof gear');
      }
      
      // General recommendations
      if (day.condition.includes('Clear') && day.temp_max < 25) {
        recommendations.push('☀️ Perfect hiking conditions - ideal for long trails');
      }
      
      if (recommendations.length === 0) {
        recommendations.push('✅ Good conditions for hiking');
      }
      
      return recommendations.map(rec => `<div>• ${rec}</div>`).join('');
    }

    // Refresh weather button
    document.getElementById('refresh-weather')?.addEventListener('click', loadWeatherForSelectedTrail);

    // Refresh trail info button
    document.getElementById('refresh-trail-info')?.addEventListener('click', loadTrailOverview);

    // Current Location Button
    document.getElementById('getCurrentLocationBtn')?.addEventListener('click', getCurrentLocation);

    // Trail Overview Functions
    let currentTrailId = null;

    async function loadTrailOverview() {
      const trailInfoLoading = document.getElementById('trail-info-loading');
      const trailInfoContainer = document.getElementById('trail-info-container');
      
      // Show loading state
      trailInfoLoading.classList.remove('hidden');
      
      try {
        // Get the selected trail data from the map (same as weather system)
        const trail = await getSelectedTrailData();
        console.log('Trail data for overview:', trail); // Debug log
        
        if (!trail) {
          throw new Error('No trail selected');
        }
        
        // Reset for new trail
        if (currentTrailId !== trail.id) {
          currentTrailId = trail.id;
          trailInfoContainer.innerHTML = ''; // Clear existing info
        }
        
        // Render trail overview using the trail data we already have
        const trailOverviewElement = createTrailOverviewElement(trail);
        trailInfoContainer.innerHTML = '';
        trailInfoContainer.appendChild(trailOverviewElement);
    // Update trail times preview now that trail data is available
    try { setTimeout(() => { renderTrailTimesPreview(trail); }, 30); } catch (e) { /* ignore */ }
  // Ensure transport preview is refreshed after overview renders (map/trail data likely ready now)
  try { setTimeout(() => { renderTransportationPreview(); }, 50); } catch (e) { /* ignore */ }
        
      } catch (error) {
        console.error('Error loading trail overview:', error);
        
        // Show error state
        trailInfoContainer.innerHTML = `
          <div class="text-center py-6">
            <div class="text-red-400 text-2xl mb-2">⚠️</div>
            <p class="text-sm text-red-600 font-medium mb-1">Failed to Load Trail Info</p>
            <p class="text-xs text-red-500">${error.message}</p>
            <button type="button" onclick="loadTrailOverview()" class="mt-2 px-3 py-1.5 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
              Try Again
            </button>
          </div>
        `;
      } finally {
        trailInfoLoading.classList.add('hidden');
      }
    }

    async function getSelectedTrailData() {
      // Get the selected trail from the dropdown
      const trailSelect = document.getElementById('trailSelect');
      if (!trailSelect || !trailSelect.value) {
        console.log('No trail select or value'); // Debug log
        return null;
      }
      
      console.log('Looking for trail:', trailSelect.value); // Debug log
      console.log('Map trails available:', window.itineraryMap?.trails?.length || 0); // Debug log
      
      // If we have the itinerary map with trail data, use that (same as weather system)
      if (window.itineraryMap && window.itineraryMap.trails) {
        const selectedTrail = window.itineraryMap.trails.find(t => 
          t.name === trailSelect.value || trailSelect.value.includes(t.name)
        );
        
        console.log('Found trail in map:', selectedTrail); // Debug log
        
        if (selectedTrail) {
          return selectedTrail;
        }
      }
      
      // Fallback: try to get from the trail data in the dropdown
      const selectedOption = trailSelect.options[trailSelect.selectedIndex];
      if (selectedOption && selectedOption.dataset.trailId) {
        // If we have a trail ID, we could fetch it, but for now return null
        console.warn('Trail ID found but no trail data available');
        return null;
      }
      
      console.warn('Could not find trail data for:', trailSelect.value);
      return null;
    }

        function createTrailOverviewElement(trail) {
      const overviewDiv = document.createElement('div');
      overviewDiv.className = 'bg-white rounded-lg border border-gray-200 p-3 shadow-sm';
      
      // Create star rating HTML - handle trails with no reviews
      // The map data has average_rating as a string, so we need to parse it
      const averageRating = parseFloat(trail.average_rating) || 0;
      const totalReviews = parseInt(trail.total_reviews) || 0;
      
      let ratingHtml = '';
      if (totalReviews > 0) {
        const starsHtml = Array.from({length: 5}, (_, i) => 
          `<svg class="w-4 h-4 ${i < averageRating ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>`
        ).join('');
        
        ratingHtml = `
          <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center gap-1">
              ${starsHtml}
              <span class="text-sm font-semibold text-gray-800 ml-1">${averageRating.toFixed(1)}</span>
            </div>
            <span class="text-xs text-gray-500">(${totalReviews} reviews)</span>
          </div>
        `;
      } else {
        ratingHtml = `
          <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center gap-1">
              <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
            </div>
            <span class="text-xs text-gray-500">(No reviews yet)</span>
          </div>
        `;
      }
      
      // Create trail stats HTML
      let statsHtml = '';
      const hasStats = trail.length || trail.elevation_gain || trail.estimated_time || trail.best_season;
      
      if (hasStats) {
        statsHtml = `
          <div class="grid grid-cols-2 gap-3 mb-3">
            ${trail.length ? `
              <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-xs text-gray-500 mb-1">Length</div>
                <div class="text-sm font-semibold text-gray-800">${trail.length} km</div>
              </div>
            ` : ''}
            
            ${trail.elevation_gain ? `
              <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-xs text-gray-500 mb-1">Elevation</div>
                <div class="text-sm font-semibold text-gray-800">+${trail.elevation_gain}m</div>
              </div>
            ` : ''}
            
            ${trail.estimated_time ? `
              <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-xs text-gray-500 mb-1">Estimated Hiking Time</div>
                <div class="text-sm font-semibold text-gray-800">${formatMinutesHuman(trail.estimated_time)}</div>
              </div>
            ` : ''}
            
            ${trail.best_season ? `
              <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-xs text-gray-500 mb-1">Best Season</div>
                <div class="text-sm font-semibold text-gray-800">${trail.best_season}</div>
              </div>
            ` : ''}
          </div>
        `;
      } else {
        // Show a message if no stats are available
        statsHtml = `
          <div class="text-center p-3 bg-gray-50 rounded-lg mb-3">
            <div class="text-xs text-gray-500">Trail statistics not available</div>
          </div>
        `;
      }
      
      // Create trail features HTML
      let featuresHtml = '';
      if (trail.features && trail.features.length > 0) {
        featuresHtml = `
          <div>
            <div class="text-xs font-medium text-gray-600 mb-2">Trail Features</div>
            <div class="flex flex-wrap gap-1">
              ${trail.features.slice(0, 6).map(feature => `
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                  ${feature.charAt(0).toUpperCase() + feature.slice(1)}
                </span>
              `).join('')}
              ${trail.features.length > 6 ? `
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                  +${trail.features.length - 6} more
                </span>
              ` : ''}
            </div>
          </div>
        `;
      }
      
      overviewDiv.innerHTML = `
        ${ratingHtml}
        
        ${statsHtml}
        
        ${trail.summary ? `
          <!-- Trail Summary -->
          <div class="mb-3">
            <div class="text-xs font-medium text-gray-600 mb-1">Trail Summary</div>
            <p class="text-sm text-gray-800">${trail.summary.length > 150 ? trail.summary.substring(0, 150) + '...' : trail.summary}</p>
          </div>
        ` : ''}
        
        ${featuresHtml}
      `;
      
      return overviewDiv;
    }



    // Location Functions
    function getCurrentLocation() {
      if (navigator.geolocation) {
        const button = document.getElementById('getCurrentLocationBtn');
        const originalText = button.innerHTML;
        
        // Show loading state
        button.innerHTML = `
          <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.001 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Getting...
        `;
        button.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
          (position) => {
            const { latitude, longitude } = position.coords;
            
            // Reverse geocode to get address
            reverseGeocode(latitude, longitude, (address) => {
              updateUserLocation(address, latitude, longitude);
              button.innerHTML = originalText;
              button.disabled = false;
              
              // Show success message
              showLocationNotification('Location updated successfully!', 'success');
            });
          },
          (error) => {
            console.error('Geolocation error:', error);
            button.innerHTML = originalText;
            button.disabled = false;
            
            let errorMessage = 'Unable to get your location';
            switch (error.code) {
              case error.PERMISSION_DENIED:
                errorMessage = 'Location access denied. Please enable location services.';
                break;
              case error.POSITION_UNAVAILABLE:
                errorMessage = 'Location information unavailable.';
                break;
              case error.TIMEOUT:
                errorMessage = 'Location request timed out.';
                break;
            }
            
            showLocationNotification(errorMessage, 'error');
          },
          {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 60000
          }
        );
      } else {
        showLocationNotification('Geolocation not supported by your browser.', 'error');
      }
    }

    function reverseGeocode(lat, lng, callback) {
      // Use Google Geocoding API if available
      if (window.google && google.maps && google.maps.Geocoder) {
        const geocoder = new google.maps.Geocoder();
        const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
        
        geocoder.geocode({ location: latlng }, (results, status) => {
          if (status === 'OK' && results[0]) {
            const address = results[0].formatted_address;
            callback(address);
          } else {
            // Fallback to coordinates
            callback(`${lat.toFixed(4)}, ${lng.toFixed(4)}`);
          }
        });
      } else {
        // Fallback to coordinates
        callback(`${lat.toFixed(4)}, ${lng.toFixed(4)}`);
      }
    }

    function updateUserLocation(address, lat, lng) {
      const locationDisplay = document.getElementById('userLocationDisplay');
      const locationInput = document.getElementById('userLocationInput');
      const locationHelper = document.getElementById('locationHelper');
      const userLatInput = document.getElementById('userLatInput');
      const userLngInput = document.getElementById('userLngInput');
      
      if (locationDisplay) {
        locationDisplay.textContent = address;
        locationDisplay.className = 'text-sm text-gray-800 font-medium';
      }
      
      if (locationInput) {
        locationInput.value = address;
      }

      // Update coordinate inputs
      if (userLatInput) {
        userLatInput.value = lat;
      }
      
      if (userLngInput) {
        userLngInput.value = lng;
      }
      
      if (locationHelper) {
        locationHelper.textContent = 'Location updated successfully!';
        locationHelper.className = 'text-xs text-emerald-600 mt-1';
      }
      
      // Save to localStorage
      localStorage.setItem('userLocation', JSON.stringify({
        address: address,
        lat: lat,
        lng: lng,
        timestamp: Date.now()
      }));
    }

    function loadSavedLocation() {
      const savedLocation = localStorage.getItem('userLocation');
      if (savedLocation) {
        try {
          const locationData = JSON.parse(savedLocation);
          const locationDisplay = document.getElementById('userLocationDisplay');
          const locationInput = document.getElementById('userLocationInput');
          
          if (locationDisplay && locationData.address) {
            locationDisplay.textContent = locationData.address;
            locationDisplay.className = 'text-sm text-gray-800 font-medium';
          }
          
          if (locationInput && locationData.address) {
            locationInput.value = locationData.address;
          }
          
          // Update coordinate inputs
          const userLatInput = document.getElementById('userLatInput');
          const userLngInput = document.getElementById('userLngInput');
          
          if (userLatInput && locationData.lat) {
            userLatInput.value = locationData.lat;
          }
          
          if (userLngInput && locationData.lng) {
            userLngInput.value = locationData.lng;
          }
          
          // Update helper text
          const locationHelper = document.getElementById('locationHelper');
          if (locationHelper) {
            locationHelper.textContent = 'Location loaded from previous session';
            locationHelper.className = 'text-xs text-emerald-600 mt-1';
          }
        } catch (error) {
          console.error('Error loading saved location:', error);
        }
      }
    }

    function showLocationNotification(message, type) {
      const notification = document.createElement('div');
      notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border-l-4 ${
        type === 'error' ? 'bg-red-100 border-red-500 text-red-700' :
        type === 'success' ? 'bg-green-100 border-green-500 text-green-700' :
        'bg-blue-100 border-blue-500 text-blue-700'
      }`;
      
      notification.innerHTML = `
        <div class="flex items-center">
          <span class="mr-2">${type === 'error' ? '❌' : type === 'success' ? '✅' : 'ℹ️'}</span>
          <span>${message}</span>
        </div>
      `;
      
      document.body.appendChild(notification);
      
      // Auto remove after 4 seconds
      setTimeout(() => {
        if (notification.parentElement) {
          notification.remove();
        }
      }, 4000);
    }

    // Helper function to reset trail overview
    function resetTrailOverview() {
      const trailInfoContainer = document.getElementById('trail-info-container');
      if (trailInfoContainer) {
        trailInfoContainer.innerHTML = `
          <div class="text-center py-6">
            <div class="text-gray-400 text-2xl mb-2">🏔️</div>
            <p class="text-sm text-gray-600 font-medium mb-1">No Trail Selected</p>
            <p class="text-xs text-gray-500">Select a trail to view its overview</p>
          </div>
        `;
      }
    }

    // Helper function to reset weather display
    function resetWeatherDisplay() {
      const weatherCalendar = document.getElementById('weather-calendar');
      const weatherNoTrail = document.getElementById('weather-no-trail');
      const weatherLoading = document.getElementById('weather-loading');
      const weatherError = document.getElementById('weather-error');
      
      if (weatherCalendar) weatherCalendar.classList.add('hidden');
      if (weatherNoTrail) weatherNoTrail.classList.remove('hidden');
      if (weatherLoading) weatherLoading.classList.add('hidden');
      if (weatherError) weatherError.classList.add('hidden');
    }

    // Helper function to show no trail state for weather
    function showNoTrailState() {
      const weatherCalendar = document.getElementById('weather-calendar');
      const weatherNoTrail = document.getElementById('weather-no-trail');
      
      if (weatherCalendar) weatherCalendar.classList.add('hidden');
      if (weatherNoTrail) weatherNoTrail.classList.remove('hidden');
    }
  </script>

  <!-- Map Integration Scripts -->
  @push('scripts')
  <!-- Load the Itinerary Map JavaScript first -->
  @vite(['resources/js/itinerary-map.js'])
  
  <!-- Google Maps API with enhanced libraries -->
  <script>
      // Wait for Vite to load the ItineraryMap class before initializing
      function waitForItineraryMap() {
          return new Promise((resolve) => {
              if (typeof ItineraryMap !== 'undefined') {
                  resolve();
              } else {
                  // Check every 100ms until ItineraryMap is available
                  const checkInterval = setInterval(() => {
                      if (typeof ItineraryMap !== 'undefined') {
                          clearInterval(checkInterval);
                          resolve();
                      }
                  }, 100);
                  
                  // Timeout after 10 seconds
                  setTimeout(() => {
                      clearInterval(checkInterval);
                      console.error('ItineraryMap class not loaded within timeout');
                      resolve(); // Resolve anyway to continue with error handling
                  }, 10000);
              }
          });
      }

    // Enhanced Google Maps API loading with comprehensive libraries
    async function loadGoogleMapsAPI() {
      return new Promise((resolve, reject) => {
  const apiKey = '{{ config("services.google.maps_api_key") }}';
        if (!apiKey) { reject(new Error('Google Maps API key not configured.')); return; }
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=geometry,places,visualization,drawing&callback=initItineraryMap`;
        script.onerror = () => reject(new Error('Failed to load Google Maps API'));
        const timeout = setTimeout(()=>reject(new Error('Google Maps API loading timeout')),15000);
        window.initItineraryMap = async function() {
          clearTimeout(timeout);
          try {
            await waitForItineraryMap();
            document.getElementById('map-loading-state').style.display = 'none';
            document.getElementById('map-fallback').style.display = 'none';
            if (typeof ItineraryMap !== 'undefined') {
              window.itineraryMap = new ItineraryMap({ mapElementId: 'itinerary-map' });
              console.log('Itinerary map initialized successfully');
            } else {
              showMapError('Map System Error', 'The itinerary map system failed to initialize.');
            }
            resolve();
          } catch(e) {
            console.error('Error during map initialization:', e);
            showMapError('Map Loading Error', 'Failed to load map components.');
            reject(e);
          }
        };
        document.head.appendChild(script);
      });
    }

      // Helper function to show map errors
      function showMapError(title, message) {
          const fallback = document.getElementById('map-fallback');
          if (fallback) {
              fallback.classList.remove('hidden');
              fallback.innerHTML = `
                  <div class="text-center p-8">
                      <div class="text-red-600 text-4xl mb-4">⚠️</div>
                      <p class="text-red-600 text-lg font-semibold mb-2">${title}</p>
                      <p class="text-gray-600 mb-4">${message}</p>
                      <div class="mt-4 space-y-2">
                          <button onclick="location.reload()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                              🔄 Refresh Page
                          </button>
                      </div>
                  </div>
              `;
          }
      }

      // Enhanced error handling and user feedback
      loadGoogleMapsAPI().catch(error => {
          console.error('Error loading Google Maps API:', error);
          document.getElementById('map-loading-state').style.display = 'none';
          showMapError('Failed to load Google Maps', error.message + ' - Try refreshing the page.');
      });
  </script>
  @endpush

  <script>
    // Serialize builder form into an `itinerary` payload expected by ItineraryController@store
    (function(){
  const form = document.querySelector(`form[action="{{ route('hiker.itinerary.generate') }}"]`);
      if (!form) return;

      form.addEventListener('submit', function(e){
        // Prevent default submission; we'll submit structured payload
        e.preventDefault();

        // Gather basic fields
        const formData = new FormData(form);
        const itinerary = {};
        itinerary.title = formData.get('title') || null;
        itinerary.trail_name = formData.get('trail_name') || formData.get('trail') || null;
        itinerary.start_date = formData.get('date') || null;
        itinerary.start_time = formData.get('time') || null;
        // duration_days may not be present in UI; infer 1 if not present
        itinerary.duration_days = parseInt(formData.get('duration_days') || '1', 10);
        itinerary.nights = Math.max(0, (itinerary.duration_days || 1) - 1);

        // Stopovers and sidetrips arrays
        itinerary.stopovers = formData.getAll('stopovers[]') || [];
        itinerary.sidetrips = formData.getAll('sidetrips[]') || [];

        // Transport preview fields (if set)
        itinerary.transport_details = {};
        const pickupEl = document.getElementById('transportation-pickup');
        const vehicleEl = document.getElementById('transportation-vehicle');
        if (pickupEl && pickupEl.textContent && pickupEl.textContent.trim() !== '—') itinerary.transport_details.pickup = pickupEl.textContent.trim();
        if (vehicleEl && vehicleEl.textContent && vehicleEl.textContent.trim() !== '—') itinerary.transport_details.vehicle = vehicleEl.textContent.trim();

        // Also include the structured transport payload if provided on the selected <option> (data-transport)
        try {
          const trailSelect = document.getElementById('trailSelect');
          const selectedOption = trailSelect && trailSelect.options[trailSelect.selectedIndex];
          if (selectedOption) {
            const dt = selectedOption.getAttribute('data-transport');
            if (dt) {
              try {
                const parsed = JSON.parse(dt);
                // Attach canonical payload or wrapper contents to transport_details so it persists fully
                if (parsed) {
                  // If wrapper has canonical, attach it; otherwise attach the parsed object
                  itinerary.transport_details.canonical = parsed.canonical ?? parsed;
                  if (parsed.pickup_place) itinerary.transport_details.pickup_place = parsed.pickup_place;
                  if (parsed.vehicle_label || parsed.vehicle) itinerary.transport_details.vehicle_label = parsed.vehicle_label ?? parsed.vehicle;
                }
              } catch (e) {
                // not JSON, store as human-summary
                itinerary.transport_details.summary = dt;
              }
            }

            // If the option carries dataset coordinates, include them
            if (selectedOption.dataset && selectedOption.dataset.coordinates) {
              // dataset.coordinates expected as 'lat,lng'
              itinerary.route_coordinates = selectedOption.dataset.coordinates;
            }
          }
        } catch (e) {
          // ignore transport metadata errors
        }

        // Weather data - try to include currentWeatherData if available
        if (window.currentWeatherData) itinerary.weather_data = window.currentWeatherData;

        // Build a simple daily_schedule placeholder so the controller can persist days if needed
        itinerary.daily_schedule = [];
        const days = itinerary.duration_days || 1;
        // If we have a map-backed trail object, copy useful metadata (length, elevation, estimated_time, package days)
        try {
          if (window.itineraryMap && Array.isArray(window.itineraryMap.trails)) {
            const matchedTrail = window.itineraryMap.trails.find(t => t && (t.name === (itinerary.trail_name) || (itinerary.trail_name && String(itinerary.trail_name).includes(t.name))));
            if (matchedTrail) {
              // copy common fields into itinerary so generated view can read persisted metadata
              if (matchedTrail.id) itinerary.trail_id = matchedTrail.id;
              if (matchedTrail.length) itinerary.distance_km = matchedTrail.length;
              if (matchedTrail.elevation_gain) itinerary.elevation_m = matchedTrail.elevation_gain;
              if (matchedTrail.estimated_time) itinerary.trail_estimated_time = matchedTrail.estimated_time;
              if (matchedTrail.best_season) itinerary.best_season = matchedTrail.best_season;
              if (matchedTrail.difficulty) itinerary.difficulty = matchedTrail.difficulty;
              if (matchedTrail.coordinates) itinerary.route_coordinates = matchedTrail.coordinates;
              // If the trail package includes a recommended package duration (days), respect it when user hasn't set duration_days explicitly
              if (!formData.get('duration_days') && (matchedTrail.package_days || matchedTrail.duration_days)) {
                itinerary.duration_days = matchedTrail.package_days ?? matchedTrail.duration_days;
                itinerary.nights = Math.max(0, (itinerary.duration_days || 1) - 1);
              }
              // attach side trips if present
              if (matchedTrail.side_trips && (!itinerary.sidetrips || itinerary.sidetrips.length === 0)) {
                // Normalized shape: may be string, array, or JSON metadata
                if (Array.isArray(matchedTrail.side_trips)) {
                  itinerary.sidetrips = matchedTrail.side_trips.filter(Boolean);
                } else if (typeof matchedTrail.side_trips === 'string' && matchedTrail.side_trips.trim() !== '') {
                  // server may provide a comma-separated string or newline-separated; prefer comma split
                  if (matchedTrail.side_trips.includes('\n')) {
                    itinerary.sidetrips = matchedTrail.side_trips.split('\n').map(s => s.trim()).filter(Boolean);
                  } else if (matchedTrail.side_trips.includes(',')) {
                    itinerary.sidetrips = matchedTrail.side_trips.split(',').map(s => s.trim()).filter(Boolean);
                  } else {
                    itinerary.sidetrips = [matchedTrail.side_trips.trim()];
                  }
                } else if (matchedTrail.package && matchedTrail.package.side_trips) {
                  // some map payloads include package.side_trips
                  const pkg = matchedTrail.package;
                  if (Array.isArray(pkg.side_trips)) itinerary.sidetrips = pkg.side_trips.filter(Boolean);
                  else if (typeof pkg.side_trips === 'string' && pkg.side_trips.trim() !== '') itinerary.sidetrips = (pkg.side_trips.includes(',') ? pkg.side_trips.split(',') : [pkg.side_trips]).map(s => s.trim()).filter(Boolean);
                }
              }
            }
          }
        } catch (e) {
          // ignore
        }
        for (let d = 0; d < days; d++) {
          itinerary.daily_schedule.push({
            date: itinerary.start_date || null,
            activities: [
              { minutes: 0, title: 'Start', description: 'Begin your hike', location: itinerary.trail_name }
            ],
            meta: {}
          });
        }

        // Serialize the itinerary object into nested `itinerary[...]` inputs
        // so Laravel receives a structured array in `$request->input('itinerary')`.
        function appendField(name, value) {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = name;
          input.value = value == null ? '' : String(value);
          form.appendChild(input);
        }

        function buildFields(prefix, obj) {
          if (obj === null || obj === undefined) {
            appendField(prefix, '');
            return;
          }

          if (Array.isArray(obj)) {
            obj.forEach((item, idx) => {
              buildFields(`${prefix}[${idx}]`, item);
            });
            return;
          }

          if (typeof obj === 'object') {
              Object.keys(obj).forEach(key => {
                const val = obj[key];
                buildFields(`${prefix}[${key}]`, val);
              });
            return;
          }

          // primitive value
          appendField(prefix, obj);
        }

        // Remove any previous itinerary.* hidden inputs we created earlier
        Array.from(form.querySelectorAll('input[name^="itinerary"]')).forEach(el => el.remove());

        buildFields('itinerary', itinerary);
        // Submit the form normally now that payload fields are present
        form.submit();
      });
    })();

    // Handle preselected trail after all event listeners are registered
    function tryPreselectedTrail(retryCount = 0) {
      const trailSelect = document.getElementById('trailSelect');
      if (!trailSelect || !trailSelect.value) return;
      
      console.log('Found preselected trail:', trailSelect.value);
      console.log('Map trails available:', window.itineraryMap?.trails?.length || 0);
      
      // Check if map and trail data is ready
      if (window.itineraryMap && window.itineraryMap.trails && window.itineraryMap.trails.length > 0) {
        console.log('Map data ready, triggering change event for preselected trail...');
        
        // Trigger the change event programmatically
        const changeEvent = new Event('change', { bubbles: true });
        trailSelect.dispatchEvent(changeEvent);
        
        console.log('Change event dispatched for preselected trail');
      } else if (retryCount < 10) {
        // Map data not ready yet, retry after a short delay
        console.log(`Map data not ready, retrying in 300ms... (attempt ${retryCount + 1}/10)`);
        setTimeout(() => tryPreselectedTrail(retryCount + 1), 300);
      } else {
        console.warn('Map data still not ready after 10 attempts, giving up on preselected trail');
      }
    }
    
    // Start trying after initial delay
    setTimeout(tryPreselectedTrail, 500);

    // Function to select a recommended trail from the ML suggestions
    function selectRecommendedTrail(trailId, trailName) {
      const trailSelect = document.getElementById('trailSelect');
      if (!trailSelect) return;
      
      // Find the option with matching trail name or data-trail-id
      const options = Array.from(trailSelect.options);
      const matchingOption = options.find(opt => {
        const optTrailId = opt.getAttribute('data-trail-id');
        const optValue = opt.value;
        return optTrailId == trailId || optValue === trailName;
      });
      
      if (matchingOption) {
        // Select the option
        trailSelect.value = matchingOption.value;
        
        // Trigger change event to update the UI
        const changeEvent = new Event('change', { bubbles: true });
        trailSelect.dispatchEvent(changeEvent);
        
        // Scroll to the trail select dropdown
        trailSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Add a visual highlight effect
        trailSelect.classList.add('ring-4', 'ring-emerald-400');
        setTimeout(() => {
          trailSelect.classList.remove('ring-4', 'ring-emerald-400');
        }, 1500);
        
        // Show success notification
        showNotification('Trail selected: ' + trailName, 'success');
      } else {
        console.warn('Could not find matching trail option for:', trailName, trailId);
        showNotification('Trail not found in dropdown', 'error');
      }
    }
    
    // Simple notification helper
    function showNotification(message, type = 'info') {
      const colors = {
        success: 'bg-emerald-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
      };
      
      const notification = document.createElement('div');
      notification.className = `fixed top-4 right-4 ${colors[type] || colors.info} text-white px-4 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
      notification.textContent = message;
      
      document.body.appendChild(notification);
      
      setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
      }, 3000);
    }
  </script>
</x-app-layout>