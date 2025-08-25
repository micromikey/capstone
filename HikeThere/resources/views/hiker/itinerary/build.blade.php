<x-app-layout>

  <form action="{{ route('itinerary.generate') }}" method="POST" class="relative min-h-screen overflow-hidden">
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                      </svg>
                    </button>
                  </div>
                  
                  <!-- Map Status Bar -->
                  <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-3">
                    <div class="flex items-center justify-between text-white">
                      <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
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
              </div>

              <!--RIGHT: Controls Card-->
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
                        <input type="hidden" name="trail_name" id="trailNameInput" value="{{ old('trail_name') }}" />
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

                  <!-- Locations & Lists -->
                  <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-3">
                    <div class="grid gap-3">
                      <div>
                        <div class="flex items-center justify-between">
                          <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Your Location</p>
                          <button type="button" id="getCurrentLocationBtn" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-200 transition-colors">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
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
                          <p class="text-sm text-gray-800" id="destinationDisplay">{{ old('trail_name', 'Select trail first') }}</p>
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
                        <div id="stopovers-container" class="space-y-2">
                          <!-- Stopovers will be added here dynamically -->
                        </div>
                      </div>

                      <!-- Side Trips Section -->
                      <div class="border-t border-dashed pt-2">
                        <div class="flex items-center justify-between mb-2">
                          <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Side Trips</p>
                          <button type="button" id="add-sidetrip-btn" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                            + Add Side Trip
                          </button>
                        </div>
                        <div class="flex gap-2 mb-2">
                          <input type="text" id="add-sidetrip-input" placeholder="Search Philippine locations..." 
                                 class="flex-1 rounded-md border border-gray-300 px-2 py-1.5 text-xs ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200">
                        </div>
                        <div id="sidetrips-container" class="space-y-2">
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

            <!--Suggested Trail-->
            <div class="mt-5 rounded-xl border border-white/70 bg-white/85 p-4 ring-1 ring-black/5 backdrop-blur">
              <div class="mb-3">
                <p class="text-sm font-semibold text-gray-800">Suggested Trail</p>
                @if($assessment)
                <p class="bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700 bg-clip-text text-lg font-extrabold tracking-tight text-transparent">
                  Based on your assessment score: {{ $assessment->overall_score }}/100
                </p>
                <p class="mt-0.5 text-xs text-gray-500">Difficulty level: {{ $assessment->overall_score >= 80 ? 'Hard' : ($assessment->overall_score >= 60 ? 'Moderate' : 'Easy') }}</p>
                @else
                <p class="bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700 bg-clip-text text-lg font-extrabold tracking-tight text-transparent">
                  Complete your assessment for personalized recommendations
                </p>
                <p class="mt-0.5 text-xs text-gray-500">We'll suggest trails based on your fitness level and experience.</p>
                @endif
              </div>

              <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative w-full sm:w-72">
                  <div class="rounded-md bg-gradient-to-r from-emerald-300/40 to-cyan-300/40 p-[1px]">
                    <select name="trail" id="trailSelect" class="appearance-none bg-none shadow-none pr-8 w-full rounded-md bg-white px-3 py-2 text-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-emerald-200">
                      <option value="" {{ old('trail') == '' ? 'selected' : '' }} disabled>Select option</option>
                      @if($trails && $trails->count() > 0)
                        @foreach($trails as $trail)
                          <option value="{{ $trail->trail_name }}" {{ old('trail') == $trail->trail_name ? 'selected' : '' }}>
                            {{ $trail->trail_name }} ({{ $trail->difficulty }})
                          </option>
                        @endforeach
                      @else
                        <option value="Custom Trail">Custom Trail</option>
                      @endif
                    </select>
                  </div>

                  <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </div>

                <button type="submit" class="group relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-lg bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 px-5 py-2 text-sm font-semibold text-white ring-1 ring-emerald-400/40 transition active:scale-[.98]">
                  <span class="absolute inset-0 translate-x-[-120%] bg-white/20 transition-all duration-500 group-hover:translate-x-[120%]"></span>
                  <span>GENERATE</span>
                  <svg class="h-4 w-4 transition group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-8 text-center">
          <button type="submit" 
                  class="inline-flex items-center px-12 py-4 text-white font-bold text-lg rounded-2xl shadow-2xl transition-all duration-300 hover:shadow-3xl hover:scale-105 focus:outline-none focus:ring-4 focus:ring-emerald-200"
                  style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
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
  <script>
    //Used for both Stopovers & Side Trips
    function createListItem(text, inputName) {
      const wrapper = document.createElement('div');
      wrapper.className = 'relative';
      wrapper.innerHTML = `
        <div class="absolute -left-1 top-3 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></div>
        <div class="flex items-center gap-2 rounded-md border border-gray-200 bg-white pl-3 pr-0 py-2">
          <span class="editable text-sm text-gray-800">${text}</span>
          <input type="hidden" name="${inputName}[]" value="${text}">
          <div class="ml-auto flex items-center gap-1 justify-end">
            <button type="button" class="move-up rounded-full p-1 text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-700" title="Move up">▲</button>
            <button type="button" class="move-down rounded-full p-1 text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-700" title="Move down">▼</button>
            <button type="button" class="remove-btn rounded-full px-1 text-red-500 hover:bg-red-500 hover:text-white" aria-label="Remove">&times;</button>
          </div>
        </div>
      `;
      return wrapper;
    }

    function addItem(listSelector, text, inputName) {
      const container = document.querySelector(listSelector);
      if (!container) return;
      const item = createListItem(text, inputName);
      container.appendChild(item);
    }

    // Add via inputs (NO prompt)
    document.querySelector('.stopover-btn')?.addEventListener('click', () => {
      const input = document.getElementById('stopoverInput');
      const value = input.value.trim();
      if (value) {
        addItem('.stopover-list', value, 'stopovers');
        input.value = '';
        input.focus();
      }
    });

    document.querySelector('.sidetrip-btn')?.addEventListener('click', () => {
      const input = document.getElementById('sidetripInput');
      const value = input.value.trim();
      if (value) {
        addItem('.sidetrip-list', value, 'sidetrips');
        input.value = '';
        input.focus();
      }
    });

    // Delegated events: remove + move + inline edit
    document.addEventListener('click', (e) => {
      // Remove
      if (e.target.classList.contains('remove-btn')) {
        e.target.closest('.relative')?.remove();
      }
      // Move up/down
      if (e.target.classList.contains('move-up') || e.target.classList.contains('move-down')) {
        const row = e.target.closest('.relative');
        const parent = row?.parentElement;
        if (!row || !parent) return;
        if (e.target.classList.contains('move-up') && row.previousElementSibling) {
          parent.insertBefore(row, row.previousElementSibling);
        }
        if (e.target.classList.contains('move-down') && row.nextElementSibling) {
          parent.insertBefore(row.nextElementSibling, row);
        }
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
    });

    // Handle trail selection and update display
    document.getElementById('trailSelect')?.addEventListener('change', function() {
      const selectedTrail = this.value;
      const displayElement = document.getElementById('selectedTrailDisplay');
      const hiddenInput = document.getElementById('trailNameInput');
      const destinationDisplay = document.getElementById('destinationDisplay');
      
      if (selectedTrail) {
        displayElement.textContent = selectedTrail;
        hiddenInput.value = selectedTrail;
        destinationDisplay.textContent = selectedTrail;
      } else {
        displayElement.textContent = 'Select trail first';
        hiddenInput.value = '';
        destinationDisplay.textContent = 'Select trail first';
      }
    });

    // Handle current location functionality
    document.getElementById('getCurrentLocationBtn')?.addEventListener('click', function() {
      const button = this;
      const originalText = button.innerHTML;
      
      // Show loading state
      button.innerHTML = `
        <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Getting...
      `;
      button.disabled = true;
      
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(position) {
            // Success - get address from coordinates
            getAddressFromCoordinates(position.coords.latitude, position.coords.longitude, button, originalText);
          },
          function(error) {
            // Error handling
            console.error('Geolocation error:', error);
            showLocationError(button, originalText);
          },
          {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 60000
          }
        );
      } else {
        showLocationError(button, originalText, 'Geolocation not supported');
      }
    });

    // Function to get address from coordinates using reverse geocoding
    function getAddressFromCoordinates(lat, lng, button, originalText) {
      // Use OpenStreetMap Nominatim API for reverse geocoding (free and no API key required)
      const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
      
      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data.display_name) {
            // Format the address nicely
            const address = formatAddress(data);
            updateLocationDisplay(address);
            button.innerHTML = `
              <svg class="h-3 w-3 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
              </svg>
              Updated
            `;
            button.classList.remove('bg-emerald-100', 'text-emerald-700', 'hover:bg-emerald-200');
            button.classList.add('bg-green-100', 'text-green-700', 'hover:bg-green-200');
            
            // Reset button after 3 seconds
            setTimeout(() => {
              button.innerHTML = originalText;
              button.classList.remove('bg-green-100', 'text-green-700', 'hover:bg-green-200');
              button.classList.add('bg-emerald-100', 'text-emerald-700', 'hover:bg-emerald-200');
              button.disabled = false;
            }, 3000);
          } else {
            throw new Error('No address data received');
          }
        })
        .catch(error => {
          console.error('Reverse geocoding error:', error);
          showLocationError(button, originalText, 'Failed to get address');
        });
    }

    // Function to format address from Nominatim data
    function formatAddress(data) {
      const parts = [];
      
      if (data.address.house_number && data.address.road) {
        parts.push(`${data.address.house_number} ${data.address.road}`);
      } else if (data.address.road) {
        parts.push(data.address.road);
      }
      
      if (data.address.city) {
        parts.push(data.address.city);
      } else if (data.address.town) {
        parts.push(data.address.town);
      } else if (data.address.village) {
        parts.push(data.address.village);
      }
      
      if (data.address.state) {
        parts.push(data.address.state);
      }
      
      if (data.address.country) {
        parts.push(data.address.country);
      }
      
      return parts.join(', ') || data.display_name;
    }

    // Function to update location display
    function updateLocationDisplay(address) {
      const displayElement = document.getElementById('userLocationDisplay');
      const inputElement = document.getElementById('userLocationInput');
      const helperElement = document.getElementById('locationHelper');
      
      if (displayElement && inputElement) {
        displayElement.textContent = address;
        inputElement.value = address;
        
        // Hide helper text when location is set
        if (helperElement) {
          helperElement.style.display = 'none';
        }
        
        // Save to localStorage for persistence
        localStorage.setItem('hikeThere_userLocation', address);
      }
    }

    // Function to load saved location from localStorage
    function loadSavedLocation() {
      const savedLocation = localStorage.getItem('hikeThere_userLocation');
      const userProfileLocation = '{{ Auth::user()->location }}';
      const helperElement = document.getElementById('locationHelper');
      
      if (savedLocation && savedLocation.trim() !== '') {
        // Use saved location from localStorage
        updateLocationDisplay(savedLocation);
      } else if (userProfileLocation && userProfileLocation.trim() !== '') {
        // Use user's profile location
        updateLocationDisplay(userProfileLocation);
      } else {
        // No saved location, show placeholder
        const displayElement = document.getElementById('userLocationDisplay');
        const inputElement = document.getElementById('userLocationInput');
        
        if (displayElement && inputElement) {
          displayElement.textContent = 'Click "Current" to get your location';
          inputElement.value = '';
          
          // Show helper text when no location is set
          if (helperElement) {
            helperElement.style.display = 'block';
          }
        }
      }
    }

    // Function to show location error
    function showLocationError(button, originalText, message = 'Location access denied') {
      button.innerHTML = `
        <svg class="h-3 w-3 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        Error
      `;
      button.classList.remove('bg-emerald-100', 'text-emerald-700', 'hover:bg-emerald-200');
      button.classList.add('bg-red-100', 'text-red-700', 'hover:bg-red-200');
      
      // Show error message
      const errorDiv = document.createElement('div');
      errorDiv.className = 'mt-2 text-xs text-red-600';
      errorDiv.textContent = message;
      button.parentNode.appendChild(errorDiv);
      
      // Reset button after 5 seconds
      setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('bg-red-100', 'text-red-700', 'hover:bg-red-200');
        button.classList.add('bg-emerald-100', 'text-emerald-700', 'hover:bg-emerald-200');
        button.disabled = false;
        if (errorDiv.parentNode) {
          errorDiv.parentNode.removeChild(errorDiv);
        }
      }, 5000);
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
                // Check if API key is available
                const apiKey = '{{ config('services.google.maps_api_key') }}';
                if (!apiKey || apiKey === '') {
                    reject(new Error('Google Maps API key not configured. Please check your .env file.'));
                    return;
                }
                
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=geometry,places,visualization,drawing&callback=initItineraryMap`;
                script.onerror = () => reject(new Error('Failed to load Google Maps API'));
                script.onload = () => resolve();
                
                // Set timeout for API loading
                const timeout = setTimeout(() => {
                    reject(new Error('Google Maps API loading timeout'));
                }, 15000);
                
                // Override the callback to clear timeout and initialize
                window.initItineraryMap = async function() {
                    clearTimeout(timeout);
                    
                    try {
                        // Wait for ItineraryMap class to be available
                        await waitForItineraryMap();
                        
                        document.getElementById('map-loading-state').style.display = 'none';
                        document.getElementById('map-fallback').style.display = 'none';
                        resolve();
                        
                        // Initialize the itinerary map
                        if (typeof ItineraryMap !== 'undefined') {
                            try {
                                window.itineraryMap = new ItineraryMap({
                                    mapElementId: 'itinerary-map'
                                });
                                
                                console.log('Itinerary map initialized successfully');
                            } catch (error) {
                                console.error('Error initializing itinerary map:', error);
                                showMapError('Map Initialization Error', error.message);
                            }
                        } else {
                            console.error('ItineraryMap class not available after waiting');
                            showMapError('Map System Error', 'The itinerary map system failed to initialize.');
                        }
                    } catch (error) {
                        console.error('Error during map initialization:', error);
                        showMapError('Map Loading Error', 'Failed to load map components.');
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
