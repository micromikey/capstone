<x-app-layout>

  <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-teal-50">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
      <div class="absolute -top-24 -left-24 h-80 w-80 rounded-full bg-gradient-to-br from-emerald-300/50 via-teal-300/40 to-cyan-300/40 blur-3xl"></div>
      <div class="absolute top-32 -right-16 h-72 w-72 rounded-full bg-gradient-to-br from-indigo-300/40 via-purple-300/40 to-fuchsia-300/40 blur-3xl"></div>
      <div class="absolute bottom-0 left-1/3 h-64 w-64 rounded-full bg-gradient-to-br from-amber-300/40 via-rose-300/40 to-emerald-300/40 blur-3xl"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 py-10">

      <!-- Success Message -->
      @if(session('success'))
      <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800">
              {{ session('success') }}
            </p>
          </div>
        </div>
      </div>
      @endif

      <div class="rounded-2xl p-[1px] bg-gradient-to-r from-emerald-300/60 via-cyan-300/60 to-indigo-300/60 shadow-xl">
        <div class="rounded-2xl bg-white/80 px-8 py-10 text-center ring-1 ring-black/5 backdrop-blur-xl">
          <!-- Animated star + halo -->
          <div class="relative mx-auto mb-6 h-[160px] w-[160px]">
            <button id="starBtn" type="button" aria-label="Celebrate" class="absolute inset-0 grid place-items-center group">
              <span id="star" class="text-[140px] leading-none select-none" style="filter: drop-shadow(0 10px 24px rgba(234,179,8,.35));">⭐</span>
              <span class="pointer-events-none absolute inset-0 rounded-full bg-yellow-300/30 blur-2xl group-hover:bg-yellow-300/40 transition"></span>
            </button>
          </div>

          <h1 class="mb-2 text-2xl font-extrabold tracking-tight">
            <span class="bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700 bg-clip-text text-transparent">
              Itinerary Generated Successfully!
            </span>
          </h1>
          <p class="mx-auto max-w-2xl text-base text-slate-600">
            Your {{ $itinerary->trail_name }} trip plan is ready. Review the details and prepare for your hike!
          </p>

          <!-- Actions-->
          <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('itinerary.pdf', $itinerary) }}"
               class="group relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-full bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg ring-1 ring-emerald-400/40 transition active:scale-[.98]">
              <span class="absolute inset-0 translate-x-[-120%] bg-white/20 transition-all duration-500 group-hover:translate-x-[120%]"></span>
              <svg class="h-4 w-4 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
              <span>View PDF</span>
            </a>

            <button id="shareBtn"
              class="inline-flex items-center gap-2 rounded-full bg-blue-500 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600 active:scale-[.98]">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12v0m8-8v0m8 8v0m-8 8v0m0-16v16"/></svg>
              Share
            </button>

            <button id="favBtn"
              class="inline-flex items-center gap-2 rounded-full bg-amber-400 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:bg-amber-500 active:scale-[.98]">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364z"/></svg>
              Favorite
            </button>

            <a href="{{ route('itinerary.build') }}"
               class="inline-flex items-center gap-2 rounded-full bg-white/80 px-6 py-2.5 text-sm font-semibold text-emerald-700 shadow ring-1 ring-emerald-300/50 backdrop-blur transition hover:bg-white">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
              Back to Planner
            </a>
          </div>
        </div>
      </div>

      <!-- Route Summary -->
      @if($itinerary->route_summary)
      <div class="mt-8 rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Route Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-3">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
              <div>
                <p class="text-sm font-medium text-gray-600">Departure</p>
                <p class="text-base font-semibold text-gray-900">{{ $itinerary->route_summary['departure'] }}</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
              <div>
                <p class="text-sm font-medium text-gray-600">Destination</p>
                <p class="text-base font-semibold text-gray-900">{{ $itinerary->route_summary['destination'] }}</p>
              </div>
            </div>
          </div>
          <div class="space-y-3">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
              <div>
                <p class="text-sm font-medium text-gray-600">Transportation</p>
                <p class="text-base font-semibold text-gray-900">{{ $itinerary->route_summary['transportation'] }}</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
              <div>
                <p class="text-sm font-medium text-gray-600">Total Distance</p>
                <p class="text-base font-semibold text-gray-900">{{ $itinerary->route_summary['total_distance'] }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

      <!-- Route Map -->
      @if($itinerary->static_map_url || ($itinerary->route_coordinates && count($itinerary->route_coordinates) > 0))
      <div class="mt-8 rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Route Map</h2>
        
        @if($itinerary->static_map_url)
        <!-- Static Map for PDF compatibility -->
        <div class="text-center">
          <img src="{{ $itinerary->static_map_url }}" 
               alt="Route Map from {{ $itinerary->route_summary['departure'] ?? 'departure' }} to {{ $itinerary->route_summary['destination'] ?? 'destination' }}"
               class="w-full h-96 object-cover rounded-xl border border-gray-200 shadow-lg"
               onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
          
          <!-- Fallback if static map fails -->
          <div class="hidden h-96 w-full rounded-xl bg-gray-100 flex items-center justify-center">
            <div class="text-center p-4">
              <div class="text-gray-400 text-4xl mb-2">🗺️</div>
              <p class="text-sm text-gray-600 font-medium mb-1">Route Map</p>
              <p class="text-xs text-gray-500">Map visualization not available</p>
            </div>
          </div>
        </div>
        @else
        <!-- Interactive Map Fallback -->
        <div class="relative">
          <div id="route-map" class="h-96 w-full rounded-xl bg-gray-100 flex items-center justify-center">
            <div id="map-loading" class="text-center p-4">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 mb-3"></div>
              <p class="text-sm text-gray-600 font-medium">Loading route map...</p>
            </div>
          </div>
        </div>
        @endif
      </div>
      @endif

      <!-- Daily Schedule -->
      @if($itinerary->daily_schedule && count($itinerary->daily_schedule) > 0)
      <div class="mt-8 rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Detailed Itinerary Schedule</h2>
        
        @foreach($itinerary->daily_schedule as $day)
        <div class="mb-8 last:mb-0">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $day['day_label'] ?? 'Day '.$day['day_number'] }} - {{ \Carbon\Carbon::parse($day['date'])->format('l, M d, Y') }}</h3>
          
          <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-200 rounded-lg overflow-hidden">
              <thead class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white">
                <tr>
                  <th class="px-4 py-3 text-left text-sm font-semibold border-r border-emerald-400">Time</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold border-r border-emerald-400">Location</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold border-r border-emerald-400">Condition</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold border-r border-emerald-400">Temperature</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold border-r border-emerald-400">Transport Mode</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold border-r border-emerald-400">Duration</th>
                  <th class="px-4 py-3 text-left text-sm font-semibold">Notes</th>
                </tr>
              </thead>
              <tbody class="bg-white">
                @foreach($day['activities'] as $activity)
                <tr class="hover:bg-gray-50 border-b border-gray-100">
                  <td class="px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-100">
                    {{ $activity['time'] }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-800 border-r border-gray-100">
                    {{ $activity['location'] }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-100">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                      {{ str_contains(strtolower($activity['condition']), 'hot') ? 'bg-red-100 text-red-800' : 
                         (str_contains(strtolower($activity['condition']), 'warm') ? 'bg-orange-100 text-orange-800' : 
                         (str_contains(strtolower($activity['condition']), 'mild') ? 'bg-yellow-100 text-yellow-800' : 
                         (str_contains(strtolower($activity['condition']), 'cool') ? 'bg-blue-100 text-blue-800' : 
                         (str_contains(strtolower($activity['condition']), 'cold') ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800')))) }}">
                      {{ $activity['condition'] }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-100">
                    {{ $activity['temperature'] }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-100">
                    @php
                      $transportClass = match($activity['activity_type'] ?? '') {
                          'departure' => 'bg-emerald-100 text-emerald-800',
                          'arrival' => 'bg-blue-100 text-blue-800',
                          'transit' => 'bg-purple-100 text-purple-800',
                          'walking' => 'bg-green-100 text-green-800',
                          'driving' => 'bg-orange-100 text-orange-800',
                          'stopover' => 'bg-cyan-100 text-cyan-800',
                          'sidetrip' => 'bg-pink-100 text-pink-800',
                          default => 'bg-gray-100 text-gray-800'
                      };
                    @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transportClass }}">
                      {{ $activity['transport_mode'] }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-100">
                    {{ $activity['duration'] }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-600">
                    <div class="space-y-1">
                      <div>{{ $activity['note'] }}</div>
                      
                      @if(isset($activity['transit_details']))
                      <div class="text-xs text-purple-600 bg-purple-50 p-2 rounded border-l-2 border-purple-300">
                        <div class="font-medium">🚌 Transit Details:</div>
                        <div>{{ $activity['transit_details']['line_name'] }} ({{ $activity['transit_details']['vehicle_type'] }})</div>
                        <div class="text-xs text-purple-500">
                          From: {{ $activity['transit_details']['departure_stop'] }} → 
                          To: {{ $activity['transit_details']['arrival_stop'] }}
                        </div>
                        <div class="text-xs text-purple-500">
                          {{ $activity['transit_details']['departure_time'] }} - {{ $activity['transit_details']['arrival_time'] }}
                          ({{ $activity['transit_details']['num_stops'] }} stops)
                        </div>
                      </div>
                      @endif
                      
                      @if(isset($activity['walking_details']))
                      <div class="text-xs text-green-600 bg-green-50 p-2 rounded border-l-2 border-green-300">
                        <div class="font-medium">🚶 Walking:</div>
                        <div>{{ $activity['walking_details']['instruction'] }}</div>
                        <div class="text-xs text-green-500">
                          {{ $activity['walking_details']['distance_km'] }} km, {{ $activity['walking_details']['duration_minutes'] }} min
                        </div>
                      </div>
                      @endif
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        @endforeach
      </div>
      @endif

      <!-- Transport Details for Commute Mode -->
      @if($itinerary->transportation === 'Commute' && $itinerary->transport_details && count($itinerary->transport_details) > 0)
      <div class="mt-8 rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Public Transportation Summary</h2>
        
        <!-- Transit Overview -->
        @if(isset($itinerary->route_data['transit_summary']))
        <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600">{{ $itinerary->route_data['transit_summary']['total_transit_legs'] ?? 0 }}</div>
              <div class="text-sm text-purple-700">Transit Segments</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ $itinerary->route_data['transit_summary']['total_walking_legs'] ?? 0 }}</div>
              <div class="text-sm text-green-700">Walking Segments</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">{{ $itinerary->route_data['transit_summary']['transit_modes'] ? count($itinerary->route_data['transit_summary']['transit_modes']) : 0 }}</div>
              <div class="text-sm text-blue-700">Transport Modes</div>
            </div>
          </div>
          
          @if(isset($itinerary->route_data['transit_summary']['transit_modes']))
          <div class="mt-4 text-center">
            <div class="text-sm text-gray-600 mb-2">Transport Modes Used:</div>
            <div class="flex flex-wrap justify-center gap-2">
              @foreach($itinerary->route_data['transit_summary']['transit_modes'] as $mode)
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                @switch($mode)
                  @case('Bus')
                    🚌
                    @break
                  @case('Jeepney')
                    🚐
                    @break
                  @case('Train')
                    🚆
                    @break
                  @case('Subway')
                    🚇
                    @break
                  @default
                    🚗
                @endswitch
                {{ $mode }}
              </span>
              @endforeach
            </div>
          </div>
          @endif
          
          @if(isset($itinerary->route_data['transit_summary']['total_cost_estimate']))
          <div class="mt-4 text-center">
            <div class="text-sm text-gray-600 mb-2">Estimated Total Cost:</div>
            <div class="text-2xl font-bold text-emerald-600">
              ₱{{ $itinerary->route_data['transit_summary']['total_cost_estimate']['total'] }}
            </div>
            <div class="text-xs text-gray-500">{{ $itinerary->route_data['transit_summary']['total_cost_estimate']['currency'] }}</div>
          </div>
          @endif
        </div>
        @endif
        
        <div class="space-y-4">
          @foreach($itinerary->transport_details as $transport)
          <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-gray-600">Step {{ $transport['step'] }}</span>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                {{ $transport['mode'] === 'Public Transport' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                {{ $transport['mode'] }}
              </span>
            </div>
            
            @if($transport['mode'] === 'Public Transport')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600">Line/Vehicle</p>
                <p class="text-base font-semibold text-gray-900">{{ $transport['line'] }} ({{ $transport['vehicle'] }})</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Duration</p>
                <p class="text-base font-semibold text-gray-900">{{ $transport['duration'] }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">From</p>
                <p class="text-base font-semibold text-gray-900">{{ $transport['departure_stop'] }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">To</p>
                <p class="text-base font-semibold text-gray-900">{{ $transport['arrival_stop'] }}</p>
              </div>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600">Distance</p>
                <p class="text-base font-semibold text-gray-900">{{ $transport['distance'] }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Duration</p>
                <p class="text-base font-semibold text-gray-900">{{ $transport['duration'] }}</p>
              </div>
            </div>
            @endif
            
            <div class="mt-3 pt-3 border-t border-gray-200">
              <p class="text-sm text-gray-600">Instructions</p>
              <p class="text-sm text-gray-800">{{ $transport['instruction'] }}</p>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      <!-- Itinerary Details -->
      <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left Column: Basic Info -->
        <div class="space-y-6">
          
          <!-- Trail Information -->
          <div class="rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Trail Information</h2>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Trail Name:</span>
                <span class="font-semibold">{{ $itinerary->trail_name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Difficulty:</span>
                <span class="font-semibold text-{{ $itinerary->difficulty_color }}-600">{{ $itinerary->difficulty_level }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Duration:</span>
                <span class="font-semibold">{{ $itinerary->estimated_duration }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Distance:</span>
                <span class="font-semibold">{{ $itinerary->distance }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Elevation Gain:</span>
                <span class="font-semibold">{{ $itinerary->elevation_gain }}</span>
              </div>
            </div>
          </div>

          <!-- Schedule -->
          @if($itinerary->schedule)
          <div class="rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Schedule</h2>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Date:</span>
                <span class="font-semibold">{{ \Carbon\Carbon::parse($itinerary->schedule['date'])->format('M d, Y') }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Start Time:</span>
                <span class="font-semibold">{{ $itinerary->schedule['start_time'] }}</span>
              </div>
              @if(isset($itinerary->schedule['estimated_arrival']))
              <div class="flex justify-between">
                <span class="text-gray-600">Estimated Arrival:</span>
                <span class="font-semibold">{{ $itinerary->schedule['estimated_arrival'] }}</span>
              </div>
              @endif
              <div class="flex justify-between">
                <span class="text-gray-600">Transportation:</span>
                <span class="font-semibold">{{ $itinerary->transportation }}</span>
              </div>
            </div>
          </div>
          @endif

          <!-- Weather & Safety -->
          <div class="rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Weather & Safety</h2>
            <div class="space-y-3">
              <div>
                <span class="text-gray-600 font-medium">Weather Conditions:</span>
                <p class="text-sm text-gray-700 mt-1">{{ $itinerary->weather_conditions }}</p>
              </div>
              <div>
                <span class="text-gray-600 font-medium">Safety Tips:</span>
                <ul class="text-sm text-gray-700 mt-1 space-y-1">
                  @foreach($itinerary->safety_tips as $tip)
                    <li>• {{ $tip }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>

        </div>

        <!-- Right Column: Route & Gear -->
        <div class="space-y-6">
          
          <!-- Route Description -->
          <div class="rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Route Description</h2>
            <p class="text-gray-700">{{ $itinerary->route_description }}</p>
          </div>

          <!-- Waypoints -->
          @if($itinerary->waypoints)
          <div class="rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Route Waypoints</h2>
            <div class="space-y-3">
              @foreach($itinerary->waypoints as $waypoint)
              <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                <div class="w-2 h-2 bg-emerald-500 rounded-full mt-2 flex-shrink-0"></div>
                <div class="flex-1">
                  <h4 class="font-semibold text-gray-900">{{ $waypoint['name'] }}</h4>
                  <p class="text-sm text-gray-600">{{ $waypoint['description'] }}</p>
                  <div class="flex gap-4 mt-1 text-xs text-gray-500">
                    <span>{{ $waypoint['distance'] }}</span>
                    <span>{{ $waypoint['elevation'] }}</span>
                    @if(isset($waypoint['time']))
                      <span>{{ $waypoint['time'] }}</span>
                    @endif
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          @endif

          <!-- Gear Recommendations -->
          @if($itinerary->gear_recommendations)
          <div class="rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Gear Recommendations</h2>
            <ul class="space-y-2">
              @foreach($itinerary->gear_recommendations as $gear)
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span class="text-gray-700">{{ $gear }}</span>
                </li>
              @endforeach
            </ul>
          </div>
          @endif

          <!-- Emergency Contacts -->
          @if($itinerary->emergency_contacts)
          <div class="rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Emergency Contacts</h2>
            <div class="space-y-2">
              @foreach($itinerary->emergency_contacts as $type => $contact)
                <div class="flex justify-between">
                  <span class="text-gray-600 capitalize">{{ str_replace('_', ' ', $type) }}:</span>
                  <span class="font-semibold">{{ $contact }}</span>
                </div>
              @endforeach
            </div>
          </div>
          @endif

        </div>

      </div>

      <!-- Stopovers and Side Trips -->
      @if(($itinerary->stopovers && count($itinerary->stopovers) > 0) || ($itinerary->sidetrips && count($itinerary->sidetrips) > 0))
      <div class="mt-8 rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Additional Stops</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          
          @if($itinerary->stopovers && count($itinerary->stopovers) > 0)
          <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Stopovers</h3>
            <ul class="space-y-2">
              @foreach($itinerary->stopovers as $stopover)
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="text-gray-700">{{ $stopover }}</span>
                </li>
              @endforeach
            </ul>
          </div>
          @endif

          @if($itinerary->sidetrips && count($itinerary->sidetrips) > 0)
          <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Side Trips</h3>
            <ul class="space-y-2">
              @foreach($itinerary->sidetrips as $sidetrip)
                <li class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                  <span class="text-gray-700">{{ $sidetrip }}</span>
                </li>
              @endforeach
            </ul>
          </div>
          @endif

        </div>
      </div>
      @endif

      <!-- Footer -->
      <footer class="mt-8 text-center text-xs text-gray-600">
        <p class="inline-flex items-center gap-1 rounded-full bg-white/70 px-3 py-1 ring-1 ring-gray-200 backdrop-blur">
          Itinerary created on {{ $itinerary->created_at->format('M d, Y \a\t g:i A') }}
        </p>
      </footer>

    </div>
  </div>

  <!--Scripts -->
  <script>
    // Star animation
    document.getElementById('starBtn')?.addEventListener('click', function() {
      const star = document.getElementById('star');
      star.style.transform = 'scale(1.2) rotate(360deg)';
      star.style.transition = 'all 0.5s ease';
      
      setTimeout(() => {
        star.style.transform = 'scale(1) rotate(0deg)';
      }, 500);
    });

    // Share functionality
    document.getElementById('shareBtn')?.addEventListener('click', function() {
      if (navigator.share) {
        navigator.share({
          title: '{{ $itinerary->title }}',
          text: 'Check out my hiking itinerary for {{ $itinerary->trail_name }}!',
          url: window.location.href
        });
      } else {
        // Fallback for browsers that don't support Web Share API
        navigator.clipboard.writeText(window.location.href).then(() => {
          alert('Link copied to clipboard!');
        });
      }
    });

    // Favorite functionality
    document.getElementById('favBtn')?.addEventListener('click', function() {
      this.classList.toggle('bg-red-500');
      this.classList.toggle('hover:bg-red-600');
      
      if (this.classList.contains('bg-red-500')) {
        this.innerHTML = `
          <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
          </svg>
          Favorited
        `;
      } else {
        this.innerHTML = `
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364z"/>
          </svg>
          Favorite
        `;
      }
    });
  </script>

  <!-- Route Map Integration -->
  @if($itinerary->route_coordinates && count($itinerary->route_coordinates) > 0)
  <script>
    // Initialize route map when page loads
    document.addEventListener('DOMContentLoaded', function() {
      initializeRouteMap();
    });

    function initializeRouteMap() {
      const mapElement = document.getElementById('route-map');
      if (!mapElement) return;

      // Check if Google Maps is available
      if (typeof google === 'undefined' || !google.maps) {
        // Load Google Maps API
        loadGoogleMapsAPI();
        return;
      }

      createRouteMap();
    }

    function loadGoogleMapsAPI() {
      const script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=geometry&callback=createRouteMap`;
      document.head.appendChild(script);
    }

    function createRouteMap() {
      const mapElement = document.getElementById('route-map');
      if (!mapElement) return;

      // Hide loading state
      document.getElementById('map-loading').style.display = 'none';

      // Get route coordinates from the itinerary
      const routeCoordinates = @json($itinerary->route_coordinates);
      const departureInfo = @json($itinerary->departure_info);
      const arrivalInfo = @json($itinerary->arrival_info);

      if (!routeCoordinates || routeCoordinates.length === 0) {
        mapElement.innerHTML = '<div class="text-center p-4"><p class="text-gray-500">Route map not available</p></div>';
        return;
      }

      // Create map
      const map = new google.maps.Map(mapElement, {
        zoom: 10,
        center: routeCoordinates[Math.floor(routeCoordinates.length / 2)],
        mapTypeId: google.maps.MapTypeId.TERRAIN,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
      });

      // Draw route polyline
      const routePath = new google.maps.Polyline({
        path: routeCoordinates,
        geodesic: true,
        strokeColor: '#10B981',
        strokeOpacity: 1.0,
        strokeWeight: 4,
        map: map
      });

      // Add departure marker
      if (departureInfo && departureInfo.coordinates) {
        new google.maps.Marker({
          position: departureInfo.coordinates,
          map: map,
          title: 'Departure: ' + departureInfo.location,
          icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: '#10B981',
            fillOpacity: 0.8,
            strokeColor: '#FFFFFF',
            strokeWeight: 2
          }
        });
      }

      // Add arrival marker
      if (arrivalInfo && arrivalInfo.coordinates) {
        new google.maps.Marker({
          position: arrivalInfo.coordinates,
          map: map,
          title: 'Destination: ' + arrivalInfo.trail_name,
          icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: '#3B82F6',
            fillOpacity: 0.8,
            strokeColor: '#FFFFFF',
            strokeWeight: 2
          }
        });
      }

      // Fit map to show entire route
      const bounds = new google.maps.LatLngBounds();
      routeCoordinates.forEach(coord => bounds.extend(coord));
      map.fitBounds(bounds);

      // Add map controls
      setupMapControls(map);
    }

    function setupMapControls(map) {
      // Zoom in
      document.getElementById('map-zoom-in')?.addEventListener('click', () => {
        map.setZoom(map.getZoom() + 1);
      });

      // Zoom out
      document.getElementById('map-zoom-out')?.addEventListener('click', () => {
        map.setZoom(map.getZoom() - 1);
      });

      // Reset view
      document.getElementById('map-reset')?.addEventListener('click', () => {
        const routeCoordinates = @json($itinerary->route_coordinates);
        if (routeCoordinates && routeCoordinates.length > 0) {
          const bounds = new google.maps.LatLngBounds();
          routeCoordinates.forEach(coord => bounds.extend(coord));
          map.fitBounds(bounds);
        }
      });
    }
  </script>
  @endif
</x-app-layout>
