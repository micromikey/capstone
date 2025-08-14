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
                <!-- Search -->
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Find a place</label>
                <div class="relative">
                  <div class="rounded-full p-[1px] bg-gradient-to-r from-emerald-300/50 via-teal-300/50 to-cyan-300/50">
                    <input
                      type="text"
                      placeholder="Search mountains, parks, provinces…"
                      class="peer w-full rounded-full bg-white/90 px-4 py-2.5 text-sm placeholder-gray-500 ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200" />
                  </div>
                  <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 transition peer-focus:text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7"/><path d="M21 21l-3.5-3.5"/>
                  </svg>
                </div>

                <!-- Map card -->
                <div class="relative rounded-xl border border-white/70 bg-white/70 shadow-sm ring-1 ring-black/5">
                  <img src="{{ asset('img/map.png') }}" alt="Map" class="h-52 w-full rounded-xl object-cover" />
                  <div class="pointer-events-none absolute inset-0 rounded-xl bg-gradient-to-t from-black/10 via-transparent"></div>
                  <div class="absolute bottom-2 left-2 inline-flex items-center gap-1 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-medium text-gray-700 ring-1 ring-gray-200 backdrop-blur">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l3 7 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z"/>
                    </svg>
                    Interactive preview
                  </div>
                </div>
              </div>

              <!--RIGHT: Controls Card-->
              <div class="rounded-xl border border-white/70 bg-white/85 p-4 shadow-sm ring-1 ring-black/5 backdrop-blur">
                <div class="space-y-6">
                  <!-- Trail Preferences -->
                  <div>
                    <div class="mb-2 flex items-center gap-2">
                      <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 20h18l-9-15L3 20z"/>
                      </svg>
                      <p class="text-sm font-semibold text-gray-800">Trail Preferences</p>
                    </div>
                    <div class="space-y-3">
                      <label class="block">
                        <span class="text-xs font-medium text-gray-600">Trail Name</span>
                        <input type="text" name="trail_name" placeholder="e.g., Mount Pulag, Tinipak River" 
                               value="{{ old('trail_name') }}"
                               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200 transition" />
                      </label>
                      <div class="grid grid-cols-2 gap-3">
                        <label class="block">
                          <span class="text-xs font-medium text-gray-600">Duration</span>
                          <select name="duration" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200 transition">
                            <option value="2-4 hours" {{ old('duration') == '2-4 hours' ? 'selected' : '' }}>2-4 hours</option>
                            <option value="4-6 hours" {{ old('duration', '4-6 hours') == '4-6 hours' ? 'selected' : '' }}>4-6 hours</option>
                            <option value="6-8 hours" {{ old('duration') == '6-8 hours' ? 'selected' : '' }}>6-8 hours</option>
                            <option value="8+ hours" {{ old('duration') == '8+ hours' ? 'selected' : '' }}>8+ hours</option>
                          </select>
                        </label>
                        <label class="block">
                          <span class="text-xs font-medium text-gray-600">Distance</span>
                          <select name="distance" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200 transition">
                            <option value="1-3 km" {{ old('distance') == '1-3 km' ? 'selected' : '' }}>1-3 km</option>
                            <option value="3-5 km" {{ old('distance', '3-5 km') == '3-5 km' ? 'selected' : '' }}>3-5 km</option>
                            <option value="5-8 km" {{ old('distance') == '5-8 km' ? 'selected' : '' }}>5-8 km</option>
                            <option value="8+ km" {{ old('distance') == '8+ km' ? 'selected' : '' }}>8+ km</option>
                          </select>
                        </label>
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
                          <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Your Location</p>
                          <p class="text-sm text-gray-800">{{ Auth::user()->address ?? '123 Mabini Street, San Isidro, Quezon City' }}</p>
                        </div>
                      <div class="border-t border-dashed pt-2">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Destination</p>
                        <p class="text-sm text-gray-800">{{ old('trail_name', 'Mt. Pulag National Park, Kabayan, Benguet') }}</p>
                      </div>
                    </div>

                    <!-- Stopovers -->
                    <div class="mt-3 border-t border-dashed pt-3">
                      <div class="mb-2 flex items-center justify-between">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Stopovers</p>
                        <div class="flex gap-2">
                          <input type="text" id="stopoverInput" placeholder="Add a stopover…" class="w-44 rounded-md border border-gray-300 bg-white px-2 py-1.5 text-xs ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200" />
                          <button type="button" aria-label="Add stopover" class="stopover-btn group relative inline-flex h-8 items-center justify-center gap-2 overflow-hidden rounded-md bg-gradient-to-r from-emerald-600 to-teal-600 px-3 text-[12px] font-medium text-white ring-1 ring-emerald-400/40 transition active:scale-95">
                            <span class="absolute inset-0 translate-x-[-120%] bg-white/20 transition-all duration-500 group-hover:translate-x-[120%]"></span>
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                            Add
                          </button>
                        </div>
                      </div>

                      <!-- Timeline container -->
                      <div class="stopover-list relative pl-6 mt-2 max-h-28 space-y-2 overflow-y-auto pr-1">
                        <!-- vertical line -->
                        <div class="pointer-events-none absolute left-2 top-0 bottom-0 w-px bg-emerald-200"></div>

                        <!-- Initial item -->
                        <div class="relative">
                          <!-- node dot -->
                          <div class="absolute -left-1 top-3 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></div>

                          <div class="flex items-center gap-2 rounded-md border border-gray-200 bg-white pl-3 pr-0 py-2">
                            <span class="editable text-sm text-gray-800">Baguio City, Benguet</span>
                            <input type="hidden" name="stopovers[]" value="Baguio City, Benguet">
                            <div class="ml-auto flex items-center gap-1 justify-end">
                              <button type="button" class="move-up rounded-full p-1 text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-700" title="Move up">▲</button>
                              <button type="button" class="move-down rounded-full p-1 text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-700" title="Move down">▼</button>
                              <button type="button" class="remove-btn rounded-full px-1 text-red-500 hover:bg-red-500 hover:text-white" aria-label="Remove">&times;</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Side Trips -->
                    <div class="mt-3 border-t border-dashed pt-3">
                      <div class="mb-2 flex items-center justify-between">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Side Trips</p>
                        <div class="flex gap-2">
                          <input type="text" id="sidetripInput" placeholder="Add a side trip…" class="w-44 rounded-md border border-gray-300 bg-white px-2 py-1.5 text-xs ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-200" />
                          <button type="button" aria-label="Add side trip" class="sidetrip-btn group relative inline-flex h-8 items-center justify-center gap-2 overflow-hidden rounded-md bg-gradient-to-r from-emerald-600 to-teal-600 px-3 text-[12px] font-medium text-white ring-1 ring-emerald-400/40 transition active:scale-95">
                            <span class="absolute inset-0 translate-x-[-120%] bg-white/20 transition-all duration-500 group-hover:translate-x-[120%]"></span>
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                            Add
                          </button>
                        </div>
                      </div>

                      <!-- Timeline container -->
                      <div class="sidetrip-list relative pl-6 mt-2 max-h-28 space-y-2 overflow-y-auto pr-1">
                        <!-- vertical line -->
                        <div class="pointer-events-none absolute left-2 top-0 bottom-0 w-px bg-emerald-200"></div>

                        <!-- Initial item -->
                        <div class="relative">
                          <!-- node dot -->
                          <div class="absolute -left-1 top-3 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></div>

                          <div class="flex items-center gap-2 rounded-md border border-gray-200 bg-white pl-3 pr-0 py-2">
                            <span class="editable text-sm text-gray-800">Lake Tabeo (Kabayan, Benguet)</span>
                            <input type="hidden" name="sidetrips[]" value="Lake Tabeo (Kabayan, Benguet)">
                            <div class="ml-auto flex items-center gap-1 justify-end">
                              <button type="button" class="move-up rounded-full p-1 text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-700" title="Move up">▲</button>
                              <button type="button" class="move-down rounded-full p-1 text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-700" title="Move down">▼</button>
                              <button type="button" class="remove-btn rounded-full px-1 text-red-500 hover:bg-red-500 hover:text-white" aria-label="Remove">&times;</button>
                            </div>
                          </div>
                        </div>
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
                    <select name="trail" class="appearance-none bg-none shadow-none pr-8 w-full rounded-md bg-white px-3 py-2 text-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-emerald-200">
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
    });
  </script>
</x-app-layout>
