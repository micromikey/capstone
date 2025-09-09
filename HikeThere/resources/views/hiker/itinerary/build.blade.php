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
                  Hiking Itinerary Planner
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
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 0 1111.314 0z"/>
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
                    <p class="text-sm font-semibold text-gray-800">Suggested Trail</p>
                    @if($assessment)
                    <div class="mt-2 space-y-2">
                      <p class="bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700 bg-clip-text text-lg font-extrabold tracking-tight text-transparent">
                        Based on your assessment score: {{ $assessment->overall_score }}/100
                      </p>
                      <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500">Difficulty level:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                          {{ $assessment->overall_score >= 80 ? 'bg-red-100 text-red-800' : 
                             ($assessment->overall_score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                          {{ $assessment->overall_score >= 80 ? 'Hard' : ($assessment->overall_score >= 60 ? 'Moderate' : 'Easy') }}
                        </span>
                      </div>
                      <p class="text-xs text-gray-600">We've filtered trails that match your fitness level and experience.</p>
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
                                  <option value="{{ $trail->trail_name }}" {{ old('trail') == $trail->trail_name ? 'selected' : '' }}>
                                    {{ $trail->trail_name }} - {{ $trail->location ? $trail->location->province . ', ' . $trail->location->region : ($trail->mountain_name ?? 'Location N/A') }} ({{ ucfirst($trail->difficulty ?? 'Unknown') }})
                                  </option>
                                @endforeach
                              </optgroup>
                            @endif
                            
                            @if($filteredTrails->count() < $trails->count())
                              <optgroup label="Other trails ({{ $trails->count() - $filteredTrails->count() }} trails)">
                                @foreach($trails->whereNotIn('id', $filteredTrails->pluck('id')) as $trail)
                                  <option value="{{ $trail->trail_name }}" {{ old('trail') == $trail->trail_name ? 'selected' : '' }}>
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
                        <span class="text-xs font-medium text-gray-600">Time</span>
                        <input type="time" name="time" value="{{ old('time') }}" class="rounded-md border border-gray-300 px-2 py-1.5 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200 w-28 transition" />
                      </label>
                      <label class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-600">Date</span>
                        <input type="date" name="date" value="{{ old('date') }}" class="rounded-md border border-gray-300 px-2 py-1.5 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200 w-40 transition" />
                      </label>
                    </div>
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
                              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 0 1111.314 0z"/>
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Current
                          </button>
                        </div>
                        <p class="text-sm text-gray-800" id="userLocationDisplay">{{ Auth::user()->location ?? 'Click "Current" to get your location' }}</p>
                        <p class="text-xs text-gray-500 mt-1" id="locationHelper">{{ Auth::user()->location ? '' : 'No location set yet. Use the Current button to get your location automatically.' }}</p>
                        <input type="hidden" name="user_location" id="userLocationInput" value="{{ Auth::user()->location ?? '' }}" />
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
                          <button type="button" id="add-sidetrip-btn" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                            + Add Side Trip
                          </button>
                        </div>
                        <div class="flex gap-2 mb-2">
                          <input type="text" id="add-sidetrip-input" placeholder="Search Philippine locations..." 
                                 class="flex-1 rounded-md border border-gray-300 px-2 py-1.5 text-xs ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200">
                        </div>
                        <div id="sidetrips-container" class="space-y-1">
                          <!-- Side trips will be added here dynamically -->
                        </div>
                      </div>



                  </div>

                  <!-- Transportation -->
                  <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600">Transportation</label>
                    <div class="relative">
                      <div class="rounded-md bg-gradient-to-r from-emerald-300/40 to-teal-300/40 p-[1px]">
                        <select name="transportation" class="appearance-none bg-none shadow-none pr-8 w-full rounded-md bg-white px-3 py-2 text-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-emerald-200">
                          <option value="" {{ old('transportation') == '' ? 'selected' : '' }} disabled>Select option</option>
                          <option value="Commute" {{ old('transportation') == 'Commute' ? 'selected' : '' }}>Commute</option>
                          <option value="Private Vehicle" {{ old('transportation') == 'Private Vehicle' ? 'selected' : '' }}>Private Vehicle</option>
                        </select>
                      </div>
                      <!-- custom arrow (no shadow) -->
                      <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                      </svg>
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
            &copy; {{ date('Y') }} Hiking Planner • All rights reserved
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

    document.getElementById('add-sidetrip-btn')?.addEventListener('click', () => {
      const input = document.getElementById('add-sidetrip-input');
      const value = input.value.trim();
      if (value) {
        addItem('#sidetrips-container', value, 'sidetrips');
        input.value = '';
        input.focus();
      }
    });







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
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.value = tomorrow.toISOString().split('T')[0];
      }
      
      // Load saved location from localStorage if available
      loadSavedLocation();
      
      // Initialize existing items with reordering functionality
      initializeExistingItems();
      
      // Update trail count display
      updateTrailCountDisplay();
      
      // Simple initialization
      initializeExistingItems();
      
      // Don't load weather automatically - only when trail is selected
      // This prevents interference with current location functionality
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
          
          // Update trail name display
          if (selectedTrailDisplay) {
            selectedTrailDisplay.textContent = selectedTrail;
          }
          
          // Update trail name hidden input - CRITICAL FIX
          if (trailNameInput) {
            trailNameInput.value = selectedTrail;
          }
          
          // Update destination display
          if (destinationDisplay) {
            destinationDisplay.textContent = selectedTrail;
          }
          
          // Update trail count to show selected trail
          if (selectedTrailCount) {
            selectedTrailCount.textContent = `Selected: ${selectedTrail}`;
            selectedTrailCount.className = 'text-sm font-medium text-emerald-700';
          }
          
          // Load trail overview for the selected trail
          loadTrailOverview(selectedTrail);
          
          // Load weather for the selected trail
          loadWeatherForSelectedTrail();
          
        } else {
          // Fallback if option not found
          if (selectedTrailDisplay) selectedTrailDisplay.textContent = selectedTrail;
          if (trailNameInput) trailNameInput.value = selectedTrail;
          if (destinationDisplay) destinationDisplay.textContent = selectedTrail;
          if (selectedTrailCount) {
            selectedTrailCount.textContent = `Selected: ${selectedTrail}`;
            selectedTrailCount.className = 'text-sm font-medium text-emerald-700';
          }
        }
      } else {
        // Reset displays when no trail is selected
        if (selectedTrailDisplay) selectedTrailDisplay.textContent = 'Select trail first';
        if (trailNameInput) trailNameInput.value = '';
        if (destinationDisplay) destinationDisplay.textContent = 'Select trail first';
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
                <div class="text-xs text-gray-500 mb-1">Duration</div>
                <div class="text-sm font-semibold text-gray-800">${trail.estimated_time} min</div>
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
      
      if (locationDisplay) {
        locationDisplay.textContent = address;
        locationDisplay.className = 'text-sm text-gray-800 font-medium';
      }
      
      if (locationInput) {
        locationInput.value = address;
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
        const apiKey = '{{ config('services.google.maps_api_key') }}';
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
</x-app-layout>