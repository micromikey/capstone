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
               class="inline-flex items-center rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Download PDF
            </a>
            <a href="{{ route('hiker.itinerary.build') }}" 
               class="inline-flex items-center rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200 border border-gray-200">
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              Create New
            </a>
          </div>
        </div>
      </div>

      <!-- Route Information -->
      @if($itinerary->route_data && isset($itinerary->route_data['primary_provider']))
      <div class="mt-8 rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Smart Routing Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
            <div class="flex items-center space-x-2">
              @if($itinerary->route_data['primary_provider'] === 'google')
                <span class="text-2xl">🗺️</span>
                <div>
                  <h3 class="font-semibold text-blue-900">Google Maps</h3>
                  <p class="text-sm text-blue-700">Primary routing provider</p>
                </div>
              @elseif($itinerary->route_data['primary_provider'] === 'openrouteservice')
                <span class="text-2xl">🥾</span>
                <div>
                  <h3 class="font-semibold text-green-900">OpenRouteService</h3>
                  <p class="text-sm text-green-700">Optimized for hiking/trails</p>
                </div>
              @else
                <span class="text-2xl">🔄</span>
                <div>
                  <h3 class="font-semibold text-gray-900">Hybrid Routing</h3>
                  <p class="text-sm text-gray-700">Multiple providers used</p>
                </div>
              @endif
            </div>
          </div>

          @if(isset($itinerary->route_data['routing_strategy']))
          <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-4 border border-emerald-200">
            <div class="flex items-center space-x-2">
              <span class="text-2xl">⚡</span>
              <div>
                <h3 class="font-semibold text-emerald-900">Strategy</h3>
                <p class="text-sm text-emerald-700 capitalize">{{ str_replace('_', ' ', $itinerary->route_data['routing_strategy']) }}</p>
              </div>
            </div>
          </div>
          @endif

          @if(isset($itinerary->route_data['elevation_profile']))
          <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg p-4 border border-orange-200">
            <div class="flex items-center space-x-2">
              <span class="text-2xl">⛰️</span>
              <div>
                <h3 class="font-semibold text-orange-900">Elevation Data</h3>
                <p class="text-sm text-orange-700">
                  +{{ $itinerary->route_data['elevation_profile']['total_ascent'] ?? 0 }}m ascent
                </p>
              </div>
            </div>
          </div>
          @endif
        </div>

        @if(isset($itinerary->route_data['trail_enhancements']))
        <div class="mt-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-200">
          <h3 class="font-semibold text-purple-900 mb-2">🎯 Trail Enhancements</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @if(isset($itinerary->route_data['trail_enhancements']['estimated_hiking_time']))
            <div>
              <span class="font-medium text-purple-800">Estimated Hiking Time:</span>
              <span class="text-purple-700">{{ $itinerary->route_data['trail_enhancements']['estimated_hiking_time'] }}</span>
            </div>
            @endif
            @if(isset($itinerary->route_data['trail_enhancements']['difficulty_assessment']))
            <div>
              <span class="font-medium text-purple-800">Difficulty:</span>
              <span class="text-purple-700">{{ $itinerary->route_data['trail_enhancements']['difficulty_assessment'] }}</span>
            </div>
            @endif
          </div>
        </div>
        @endif
      </div>
      @endif

      <!-- Trip Summary -->
      @if($itinerary->daily_schedule && count($itinerary->daily_schedule) > 0)
      <div class="mt-8 rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-gray-900">Trip Summary</h2>
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
              {{ ucfirst($itinerary->transportation) }}
            </span>
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              {{ $itinerary->difficulty_level }}
            </span>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="text-center p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg border border-emerald-200">
            <div class="text-2xl font-bold text-emerald-700">
              @php
                $totalDistance = $itinerary->daily_schedule[0]['total_distance'] ?? $itinerary->distance ?? 'N/A';
                
                // Convert raw API distance to user-friendly format
                if (is_numeric($totalDistance)) {
                    // If it's a large number (likely meters), convert to km
                    if ($totalDistance > 1000) {
                        $totalDistance = round($totalDistance / 1000, 1) . ' km';
                    } else {
                        $totalDistance = $totalDistance . ' m';
                    }
                } elseif (strpos($totalDistance, 'km') === false && strpos($totalDistance, 'm') === false && is_numeric(str_replace([' ', 'km', 'm'], '', $totalDistance))) {
                    // If it's just a number without units, assume km
                    $cleanDistance = (float) preg_replace('/[^0-9.]/', '', $totalDistance);
                    if ($cleanDistance > 100) {
                        $totalDistance = round($cleanDistance / 1000, 1) . ' km';
                    } else {
                        $totalDistance = $cleanDistance . ' km';
                    }
                }
                
                echo $totalDistance;
              @endphp
            </div>
            <div class="text-xs text-emerald-600 font-medium">Travel Distance</div>
          </div>
          
          <div class="text-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
            <div class="text-2xl font-bold text-blue-700">
              @php
                // Use the new travel_duration field or fallback to total_duration
                $travelDuration = $itinerary->daily_schedule[0]['travel_duration'] ?? 
                                  $itinerary->travel_duration ?? 
                                  $itinerary->daily_schedule[0]['total_duration'] ?? 'N/A';
                
                // If it's already formatted (has 'h' or 'min'), use as is
                if (strpos($travelDuration, 'h') !== false || strpos($travelDuration, 'min') !== false) {
                    echo $travelDuration;
                } else {
                    // Convert raw API duration to user-friendly format
                    if (is_numeric($travelDuration)) {
                        // If it's a large number (likely seconds), convert to hours/minutes
                        if ($travelDuration > 3600) {
                            $hours = floor($travelDuration / 3600);
                            $minutes = floor(($travelDuration % 3600) / 60);
                            $travelDuration = $hours . 'h ' . $minutes . 'm';
                        } else {
                            $minutes = floor($travelDuration / 60);
                            $travelDuration = $minutes . ' min';
                        }
                        echo $travelDuration;
                    } elseif (is_numeric(str_replace([' ', 'h', 'min', 'm'], '', $travelDuration))) {
                        // If it's just a number without time units, convert assuming seconds
                        $cleanDuration = (int) preg_replace('/[^0-9]/', '', $travelDuration);
                        if ($cleanDuration > 3600) {
                            $hours = floor($cleanDuration / 3600);
                            $minutes = floor(($cleanDuration % 3600) / 60);
                            echo $hours . 'h ' . $minutes . 'm';
                        } else {
                            $minutes = floor($cleanDuration / 60);
                            echo $minutes . ' min';
                        }
                    } else {
                        echo $travelDuration;
                    }
                }
              @endphp
            </div>
            <div class="text-xs text-blue-600 font-medium">Travel Time</div>
          </div>
          
          <div class="text-center p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg border border-orange-200">
            <div class="text-2xl font-bold text-orange-700">
              @php
                // Use the new hiking_duration field or fallback to estimated_duration
                $hikingDuration = $itinerary->daily_schedule[0]['hiking_duration'] ?? 
                                  $itinerary->estimated_duration ?? '4h';
                
                // If it's already formatted (has 'h' or 'min'), use as is
                if (strpos($hikingDuration, 'h') !== false || strpos($hikingDuration, 'min') !== false) {
                    echo $hikingDuration;
                } else {
                    // Format hiking duration consistently
                    if (is_numeric($hikingDuration)) {
                        if ($hikingDuration > 3600) {
                            $hours = floor($hikingDuration / 3600);
                            $minutes = floor(($hikingDuration % 3600) / 60);
                            echo $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
                        } else {
                            $minutes = floor($hikingDuration / 60);
                            echo $minutes . ' min';
                        }
                    } else {
                        // Try to extract numbers and format
                        $numbers = preg_replace('/[^0-9.]/', '', $hikingDuration);
                        if (is_numeric($numbers)) {
                            echo round($numbers, 1) . 'h';
                        } else {
                            echo $hikingDuration;
                        }
                    }
                }
              @endphp
            </div>
            <div class="text-xs text-orange-600 font-medium">Hiking Time</div>
          </div>
          
          <div class="text-center p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
            <div class="text-2xl font-bold text-purple-700">{{ count($itinerary->daily_schedule[0]['activities'] ?? []) }}</div>
            <div class="text-xs text-purple-600 font-medium">Activities</div>
          </div>
        </div>
        
        <!-- Quick Trip Overview -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <div class="flex items-center mb-3">
            <span class="text-lg mr-2">📍</span>
            <h3 class="font-semibold text-gray-800">Trip Overview</h3>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
              <span class="font-medium text-gray-700">From:</span> 
              <span class="text-gray-600">{{ $itinerary->user_location }}</span>
            </div>
            <div>
              <span class="font-medium text-gray-700">To:</span> 
              <span class="text-gray-600">{{ $itinerary->trail_name }}</span>
            </div>
            <div>
              <span class="font-medium text-gray-700">Transportation:</span> 
              <span class="text-gray-600">{{ $itinerary->transportation }}</span>
            </div>
            <div>
              <span class="font-medium text-gray-700">Weather:</span> 
              <span class="text-gray-600">{{ $itinerary->weather_conditions }}</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Detailed Daily Schedule -->
      <div class="mt-8 rounded-2xl border border-white/70 bg-white/80 p-6 shadow-xl ring-1 ring-black/5 backdrop-blur">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Detailed Itinerary Schedule</h2>
        
        @foreach($itinerary->daily_schedule as $day)
        <div class="mb-8 last:mb-0">
          <h3 class="text-lg font-semibold text-gray-800 mb-6">{{ $day['day_label'] ?? 'Day '.$day['day_number'] }} - {{ \Carbon\Carbon::parse($day['date'])->format('l, M d, Y') }}</h3>
          
          @php
            // Group activities by type for better organization
            $departureActivities = collect($day['activities'])->filter(function($activity) {
                return in_array($activity['activity_type'] ?? '', ['departure', 'transit_board', 'transit_arrive', 'walking', 'driving', 'station_to_trail']);
            });
            
            $trailActivities = collect($day['activities'])->filter(function($activity) {
                return in_array($activity['activity_type'] ?? '', ['arrival', 'hiking_phase']);
            });
            
            $returnActivities = collect($day['activities'])->filter(function($activity) {
                return in_array($activity['activity_type'] ?? '', ['return_start', 'return_transit_board', 'return_transit_arrive', 'return_driving', 'return_home']);
            });
          @endphp

          <!-- Departure & Journey to Trail -->
          @if($departureActivities->isNotEmpty())
          <div class="mb-8">
            <h4 class="text-md font-semibold text-emerald-700 mb-3 flex items-center">
              <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
              🚗 Journey to Trail
            </h4>
            <div class="overflow-x-auto">
              <table class="w-full border-collapse border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white">
                  <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-emerald-400">Time</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-emerald-400">Description</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-emerald-400">Weather</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-emerald-400">Transport</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Notes</th>
                  </tr>
                </thead>
                <tbody class="bg-white">
                @foreach($departureActivities as $activity)
                <tr class="hover:bg-gray-50 border-b border-gray-100">
                  <td class="px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-100">
                    {{ $activity['time'] }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-800 border-r border-gray-100">
                    {{ $activity['description'] ?? $activity['location'] ?? 'Unknown Location' }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-100">
                    <div class="flex items-center space-x-2">
                      <span class="text-lg">{{ $activity['condition'] }}</span>
                      <span class="text-xs text-gray-600">{{ $activity['temperature'] }}</span>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-100">
                    @php
                      $transportClass = match($activity['activity_type'] ?? '') {
                          'departure' => 'bg-emerald-100 text-emerald-800',
                          'transit_board', 'transit_arrive' => 'bg-purple-100 text-purple-800',
                          'walking' => 'bg-green-100 text-green-800',
                          'driving' => 'bg-gray-100 text-gray-800',
                          'station_to_trail' => 'bg-yellow-100 text-yellow-800',
                          default => 'bg-gray-100 text-gray-800'
                      };
                    @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transportClass }}">
                      {{ $activity['transport_mode'] }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-600">
                    <div class="space-y-2">
                      <div class="font-medium">{{ $activity['note'] }}</div>
                      
                      <!-- Guidelines Section -->
                      @if(isset($activity['guidelines']) && is_array($activity['guidelines']) && !empty($activity['guidelines']))
                      <div class="bg-emerald-50 p-2 rounded border-l-2 border-emerald-300">
                        <div class="text-xs font-semibold text-emerald-800 mb-1">📋 Guidelines:</div>
                        <ul class="text-xs text-emerald-700 space-y-0.5">
                          @foreach($activity['guidelines'] as $guideline)
                          <li class="flex items-start">
                            <span class="text-emerald-600 mr-1">•</span>
                            <span>{{ $guideline }}</span>
                          </li>
                          @endforeach
                        </ul>
                      </div>
                      @endif
                      
                      <!-- Transit Details -->
                      @if(isset($activity['transit_details']))
                      <div class="text-xs text-purple-600 bg-purple-50 p-2 rounded border-l-2 border-purple-300">
                        <div class="font-semibold mb-1">🚌 {{ $activity['transit_details']['vehicle_type'] ?? 'Transit' }} Details:</div>
                        <div class="space-y-1">
                          @if(isset($activity['transit_details']['line_name']))
                          <div><strong>Line:</strong> {{ $activity['transit_details']['line_name'] }}
                            @if(isset($activity['transit_details']['short_name'])) ({{ $activity['transit_details']['short_name'] }}) @endif
                          </div>
                          @endif
                          
                          @if(isset($activity['transit_details']['departure_stop']) && isset($activity['transit_details']['arrival_stop']))
                          <div><strong>Route:</strong> {{ $activity['transit_details']['departure_stop'] }} → {{ $activity['transit_details']['arrival_stop'] }}</div>
                          @endif
                          
                          @if(isset($activity['transit_details']['num_stops']))
                          <div><strong>Stops:</strong> {{ $activity['transit_details']['num_stops'] }} stops</div>
                          @endif
                          
                          @if(isset($activity['transit_details']['fare']))
                          <div><strong>Fare:</strong> {{ $activity['transit_details']['fare']['text'] ?? 'Check current rates' }}</div>
                          @endif
                          
                          <div class="flex gap-3">
                        @if(isset($activity['transit_details']['departure_time']))
                            <span><strong>Departs:</strong> {{ $activity['transit_details']['departure_time'] }}</span>
                        @endif
                        @if(isset($activity['transit_details']['arrival_time']))
                            <span><strong>Arrives:</strong> {{ $activity['transit_details']['arrival_time'] }}</span>
                            @endif
                          </div>
                        </div>
                      </div>
                      @endif
                      
                      <!-- Walking Details -->
                      @if(isset($activity['walking_details']))
                      <div class="text-xs text-green-600 bg-green-50 p-2 rounded border-l-2 border-green-300">
                        <div class="font-semibold mb-1">🚶 Walking Details:</div>
                        <div class="space-y-1">
                          @if(isset($activity['walking_details']['distance']))
                          <div><strong>Distance:</strong> {{ $activity['walking_details']['distance'] }}</div>
                          @endif
                          @if(isset($activity['walking_details']['duration']))
                          <div><strong>Duration:</strong> {{ $activity['walking_details']['duration'] }}</div>
                          @endif
                          @if(isset($activity['walking_details']['instructions']))
                          <div><strong>Instructions:</strong> {{ $activity['walking_details']['instructions'] }}</div>
                          @endif
                        </div>
                      </div>
                      @endif
                      
                      <!-- Driving Details -->
                      @if(isset($activity['driving_details']))
                      <div class="text-xs text-blue-600 bg-blue-50 p-2 rounded border-l-2 border-blue-300">
                        <div class="font-semibold mb-1">🚗 Driving Details:</div>
                        <div class="space-y-1">
                          @if(isset($activity['driving_details']['total_distance']) || isset($activity['driving_details']['distance']))
                          <div><strong>Distance:</strong> {{ $activity['driving_details']['total_distance'] ?? $activity['driving_details']['distance'] }}</div>
                          @endif
                          @if(isset($activity['driving_details']['estimated_duration']) || isset($activity['driving_details']['duration']))
                          <div><strong>Duration:</strong> {{ $activity['driving_details']['estimated_duration'] ?? $activity['driving_details']['duration'] }}</div>
                          @endif
                          @if(isset($activity['driving_details']['route_summary']))
                          <div><strong>Route:</strong> {{ $activity['driving_details']['route_summary'] }}</div>
                          @endif
                          @if(isset($activity['driving_details']['instructions']))
                          <div><strong>Instructions:</strong> {{ $activity['driving_details']['instructions'] }}</div>
                        @endif
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
          @endif

          <!-- Trail Activities -->
          @if($trailActivities->isNotEmpty())
          <div class="mb-8">
            <h4 class="text-md font-semibold text-orange-700 mb-3 flex items-center">
              <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
              🥾 Trail Activities
            </h4>
            <div class="overflow-x-auto">
              <table class="w-full border-collapse border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-r from-orange-500 to-amber-500 text-white">
                  <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-orange-400">Time</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-orange-400">Location</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-orange-400">Weather</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-orange-400">Activity</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Notes</th>
                  </tr>
                </thead>
                <tbody class="bg-white">
                @foreach($trailActivities as $activity)
                <tr class="hover:bg-gray-50 border-b border-gray-100">
                  <td class="px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-100">
                    {{ $activity['time'] }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-800 border-r border-gray-100">
                    {{ $activity['description'] ?? $activity['location'] ?? 'Unknown Location' }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-100">
                    <div class="flex items-center space-x-2">
                      <span class="text-lg">{{ $activity['condition'] }}</span>
                      <span class="text-xs text-gray-600">{{ $activity['temperature'] }}</span>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-100">
                    @php
                      $activityClass = match($activity['activity_type'] ?? '') {
                          'arrival' => 'bg-blue-100 text-blue-800',
                          'hiking_phase' => 'bg-orange-100 text-orange-800',
                          'hiking_start' => 'bg-green-100 text-green-800',
                          'preparation' => 'bg-purple-100 text-purple-800',
                          default => 'bg-gray-100 text-gray-800'
                      };
                    @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $activityClass }}">
                      {{ $activity['transport_mode'] }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-600">
                    <div class="space-y-2">
                      <div class="font-medium">{{ $activity['note'] }}</div>
                      
                      <!-- Guidelines Section -->
                      @if(isset($activity['guidelines']) && is_array($activity['guidelines']) && !empty($activity['guidelines']))
                      <div class="bg-orange-50 p-2 rounded border-l-2 border-orange-300">
                        <div class="text-xs font-semibold text-orange-800 mb-1">🥾 Trail Guidelines:</div>
                        <ul class="text-xs text-orange-700 space-y-0.5">
                          @foreach($activity['guidelines'] as $guideline)
                          <li class="flex items-start">
                            <span class="text-orange-600 mr-1">•</span>
                            <span>{{ $guideline }}</span>
                          </li>
                          @endforeach
                        </ul>
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
          @endif

          <!-- Return Journey -->
          @if($returnActivities->isNotEmpty())
          <div class="mb-8">
            <h4 class="text-md font-semibold text-red-700 mb-3 flex items-center">
              <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
              🏠 Return Journey
            </h4>
            <div class="overflow-x-auto">
              <table class="w-full border-collapse border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-r from-red-500 to-pink-500 text-white">
                  <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-red-400">Time</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-red-400">Description</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-red-400">Weather</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold border-r border-red-400">Transport</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Notes</th>
                  </tr>
                </thead>
                <tbody class="bg-white">
                @foreach($returnActivities as $activity)
                <tr class="hover:bg-gray-50 border-b border-gray-100">
                  <td class="px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-100">
                    {{ $activity['time'] }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-800 border-r border-gray-100">
                    {{ $activity['description'] ?? $activity['location'] ?? 'Unknown Location' }}
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-100">
                    <div class="flex items-center space-x-2">
                      <span class="text-lg">{{ $activity['condition'] }}</span>
                      <span class="text-xs text-gray-600">{{ $activity['temperature'] }}</span>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-100">
                    @php
                      $returnClass = match($activity['activity_type'] ?? '') {
                          'return_start', 'return_preparation' => 'bg-red-100 text-red-800',
                          'return_transit_board', 'return_transit_arrive', 'return_transit', 'return_transit_segment' => 'bg-purple-100 text-purple-800',
                          'return_driving', 'return_driving_start' => 'bg-gray-100 text-gray-800',
                          'return_home', 'journey_complete' => 'bg-green-100 text-green-800',
                          default => 'bg-gray-100 text-gray-800'
                      };
                    @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $returnClass }}">
                      {{ $activity['transport_mode'] }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-600">
                    <div class="space-y-2">
                      <div class="font-medium">{{ $activity['note'] }}</div>
                      
                      <!-- Guidelines Section -->
                      @if(isset($activity['guidelines']) && is_array($activity['guidelines']) && !empty($activity['guidelines']))
                      <div class="bg-red-50 p-2 rounded border-l-2 border-red-300">
                        <div class="text-xs font-semibold text-red-800 mb-1">🏠 Return Guidelines:</div>
                        <ul class="text-xs text-red-700 space-y-0.5">
                          @foreach($activity['guidelines'] as $guideline)
                          <li class="flex items-start">
                            <span class="text-red-600 mr-1">•</span>
                            <span>{{ $guideline }}</span>
                          </li>
                          @endforeach
                        </ul>
                      </div>
                      @endif
                      
                      <!-- Return Transit Details -->
                      @if(isset($activity['transit_details']))
                      <div class="text-xs text-purple-600 bg-purple-50 p-2 rounded border-l-2 border-purple-300">
                        <div class="font-semibold mb-1">🚌 Return {{ $activity['transit_details']['vehicle_type'] ?? 'Transit' }} Details:</div>
                        <div class="space-y-1">
                          @if(isset($activity['transit_details']['line_name']))
                          <div><strong>Line:</strong> {{ $activity['transit_details']['line_name'] }}
                            @if(isset($activity['transit_details']['short_name'])) ({{ $activity['transit_details']['short_name'] }}) @endif
                          </div>
                          @endif
                          
                          @if(isset($activity['transit_details']['departure_stop']) && isset($activity['transit_details']['arrival_stop']))
                          <div><strong>Route:</strong> {{ $activity['transit_details']['departure_stop'] }} → {{ $activity['transit_details']['arrival_stop'] }}</div>
                          @endif
                          
                          <div class="flex gap-3">
                            @if(isset($activity['transit_details']['departure_time']))
                            <span><strong>Departs:</strong> {{ $activity['transit_details']['departure_time'] }}</span>
                            @endif
                            @if(isset($activity['transit_details']['arrival_time']))
                            <span><strong>Arrives:</strong> {{ $activity['transit_details']['arrival_time'] }}</span>
                            @endif
                          </div>
                        </div>
                      </div>
                      @endif
                      
                      <!-- Return Driving Details -->
                      @if(isset($activity['driving_details']))
                      <div class="text-xs text-blue-600 bg-blue-50 p-2 rounded border-l-2 border-blue-300">
                        <div class="font-semibold mb-1">🚗 Return Driving Details:</div>
                        <div class="space-y-1">
                          @if(isset($activity['driving_details']['total_distance']) || isset($activity['driving_details']['distance']))
                          <div><strong>Distance:</strong> {{ $activity['driving_details']['total_distance'] ?? $activity['driving_details']['distance'] }}</div>
                          @endif
                          @if(isset($activity['driving_details']['estimated_duration']) || isset($activity['driving_details']['duration']))
                          <div><strong>Duration:</strong> {{ $activity['driving_details']['estimated_duration'] ?? $activity['driving_details']['duration'] }}</div>
                          @endif
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
          @endif

        </div>
        @endforeach
      </div>
      @endif

    </div>
  </div>

  <script>
    // Star animation
    document.getElementById('starBtn').addEventListener('click', function() {
      const star = document.getElementById('star');
      star.style.transform = 'rotate(360deg) scale(1.2)';
      star.style.transition = 'transform 0.6s ease-in-out';
      
      setTimeout(() => {
        star.style.transform = 'rotate(0deg) scale(1)';
      }, 600);
    });
  </script>

</x-app-layout>
