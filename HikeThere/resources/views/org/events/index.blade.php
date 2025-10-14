<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb />
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manage Events') }}</h2>
                <a href="{{ route('org.events.create') }}" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Event
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <!-- Circular FAB Toggle Buttons -->
            <div class="fixed bottom-6 left-6 z-50 flex flex-col gap-3">
                <!-- Filters FAB -->
                <button onclick="toggleFilters()" id="filters-fab" class="group relative w-14 h-14 bg-[#336d66] hover:bg-[#2a5a54] text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span class="absolute left-full ml-3 px-3 py-1.5 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                        Filters
                    </span>
                </button>

                <!-- Statistics FAB -->
                <button onclick="toggleStats()" id="stats-fab" class="group relative w-14 h-14 bg-[#336d66] hover:bg-[#2a5a54] text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="absolute left-full ml-3 px-3 py-1.5 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                        Statistics
                    </span>
                </button>
            </div>

            <!-- Filters Panel (Hidden by default) -->
            <div id="floating-filters" class="fixed top-20 left-6 z-40 transition-all duration-300 transform translate-x-[-120%] opacity-0">
                <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg border border-gray-200/50 p-4 w-64 max-h-[calc(100vh-14rem)] overflow-y-auto">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filters
                        </h3>
                        @if(request()->hasAny(['mountain', 'sort_by', 'sort_order']))
                            <a href="{{ route('org.events.index') }}" class="text-xs text-red-600 hover:text-red-800 font-medium">Clear</a>
                        @endif
                    </div>

                    <form method="GET" action="{{ route('org.events.index') }}" id="eventFilterForm" class="space-y-3">
                        <!-- Mountain Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Mountain</label>
                            <select name="mountain" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                                <option value="">All Mountains</option>
                                @foreach($mountains as $mountain)
                                    <option value="{{ $mountain }}" {{ request('mountain') == $mountain ? 'selected' : '' }}>{{ $mountain }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="my-3 border-gray-200">

                        <!-- Sort By -->
                        <div>
                            <label class="flex items-center text-xs font-medium text-gray-700 mb-1">
                                <svg class="w-3 h-3 mr-1 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                </svg>
                                Sort By
                            </label>
                            <select name="sort_by" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                                <option value="date" {{ request('sort_by') == 'date' ? 'selected' : '' }}>Date</option>
                                <option value="popularity" {{ request('sort_by') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                                <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>Date Modified</option>
                            </select>
                        </div>

                        <!-- Sort Direction -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Order</label>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button type="submit" name="sort_order" value="asc" class="flex items-center justify-center px-2 py-1.5 text-xs font-medium rounded-md transition-colors {{ request('sort_order') == 'asc' ? 'bg-[#336d66] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    Asc
                                </button>
                                <button type="submit" name="sort_order" value="desc" class="flex items-center justify-center px-2 py-1.5 text-xs font-medium rounded-md transition-colors {{ request('sort_order', 'desc') == 'desc' ? 'bg-[#336d66] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    Desc
                                </button>
                            </div>
                        </div>

                        <!-- Hidden inputs to preserve other sort parameters -->
                        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'date') }}">
                    </form>

                    <!-- Pagination Section -->
                    @if($events->hasPages())
                        <hr class="my-3 border-gray-200">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Page {{ $events->currentPage() }} of {{ $events->lastPage() }}</span>
                                <span class="font-medium">{{ $events->total() }} total</span>
                            </div>
                            <div class="flex gap-2">
                                @if($events->onFirstPage())
                                    <button disabled class="flex-1 px-3 py-2 text-xs font-medium bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Previous
                                    </button>
                                @else
                                    <a href="{{ $events->appends(request()->query())->previousPageUrl() }}" class="flex-1 px-3 py-2 text-xs font-medium bg-[#336d66] hover:bg-[#2a5a54] text-white rounded-md transition-colors text-center">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Previous
                                    </a>
                                @endif

                                @if($events->hasMorePages())
                                    <a href="{{ $events->appends(request()->query())->nextPageUrl() }}" class="flex-1 px-3 py-2 text-xs font-medium bg-[#336d66] hover:bg-[#2a5a54] text-white rounded-md transition-colors text-center">
                                        Next
                                        <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @else
                                    <button disabled class="flex-1 px-3 py-2 text-xs font-medium bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                        Next
                                        <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Panel (Hidden by default) -->
            <div id="floating-stats" class="fixed top-20 right-6 z-40 transition-all duration-300 transform translate-x-[120%] opacity-0">
                <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg border border-gray-200/50 p-4 w-72 max-h-[calc(100vh-14rem)] overflow-y-auto">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 mr-2 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-800">Event Statistics</h3>
                    </div>

                    <div class="space-y-3">
                        <!-- Total Events -->
                        <div class="bg-gradient-to-br from-white to-emerald-50 rounded-lg border border-emerald-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-[#336d66]/10 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Total Events</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $events->total() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="bg-gradient-to-br from-white to-blue-50 rounded-lg border border-blue-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Upcoming</p>
                                    <p class="text-xl font-bold text-blue-600">{{ $events->where('start_at','>=',now())->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Current Page Count -->
                        <div class="bg-gradient-to-br from-white to-yellow-50 rounded-lg border border-yellow-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Always Open</p>
                                    <p class="text-xl font-bold text-yellow-600">{{ $events->where('always_available', true)->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Created by User -->
                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg border border-gray-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-gray-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Ended</p>
                                    <p class="text-xl font-bold text-gray-600">{{ $events->where('start_at','<',now())->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events Table -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if($events->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Details</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trail</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($events as $event)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                                @if($event->description)
                                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($event->description, 60) }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $event->trail ? $event->trail->trail_name : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($event->always_available)
                                                    <div class="text-sm font-semibold text-yellow-600">
                                                        Always Open
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        Flexible booking
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-900">
                                                        {{ optional($event->start_at)->format('M d, Y') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ optional($event->start_at)->format('h:i A') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $isUpcoming = $event->start_at && $event->start_at >= now();
                                                    $statusClass = $isUpcoming ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                                                    $statusText = $isUpcoming ? 'Upcoming' : 'Past';
                                                @endphp
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-3">
                                                    <a href="{{ route('events.show', $event->slug) }}" class="text-[#336d66] hover:text-[#2a5a54]">View</a>
                                                    <a href="{{ route('org.events.edit', $event) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">{{ $events->appends(request()->query())->links() }}</div>
                    @else
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No events yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first hiking event.</p>
                            <div class="mt-6">
                                <a href="{{ route('org.events.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#336d66] hover:bg-[#2a5a54] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#336d66]">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Event
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Circular FAB Toggle Script -->
    <script>
        let filtersOpen = false;
        let statsOpen = false;

        function toggleFilters() {
            const panel = document.getElementById('floating-filters');
            const fab = document.getElementById('filters-fab');
            
            filtersOpen = !filtersOpen;
            
            if (filtersOpen) {
                panel.classList.remove('translate-x-[-120%]', 'opacity-0');
                panel.classList.add('translate-x-0', 'opacity-100');
                fab.classList.add('ring-4', 'ring-[#336d66]/30');
            } else {
                panel.classList.add('translate-x-[-120%]', 'opacity-0');
                panel.classList.remove('translate-x-0', 'opacity-100');
                fab.classList.remove('ring-4', 'ring-[#336d66]/30');
            }
        }

        function toggleStats() {
            const panel = document.getElementById('floating-stats');
            const fab = document.getElementById('stats-fab');
            
            statsOpen = !statsOpen;
            
            if (statsOpen) {
                panel.classList.remove('translate-x-[120%]', 'opacity-0');
                panel.classList.add('translate-x-0', 'opacity-100');
                fab.classList.add('ring-4', 'ring-[#336d66]/30');
            } else {
                panel.classList.add('translate-x-[120%]', 'opacity-0');
                panel.classList.remove('translate-x-0', 'opacity-100');
                fab.classList.remove('ring-4', 'ring-[#336d66]/30');
            }
        }

        // Close panels on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (filtersOpen) toggleFilters();
                if (statsOpen) toggleStats();
            }
        });

        // Close panels when clicking outside
        document.addEventListener('click', function(e) {
            const filtersPanel = document.getElementById('floating-filters');
            const statsPanel = document.getElementById('floating-stats');
            const filtersFab = document.getElementById('filters-fab');
            const statsFab = document.getElementById('stats-fab');
            
            if (filtersOpen && !filtersPanel.contains(e.target) && !filtersFab.contains(e.target)) {
                toggleFilters();
            }
            
            if (statsOpen && !statsPanel.contains(e.target) && !statsFab.contains(e.target)) {
                toggleStats();
            }
        });
    </script>
</x-app-layout>