@props(['user', 'followedTrails', 'followingCount'])

@push('floating-navigation')
    @php
    $sections = [
        ['id' => 'hero-section', 'title' => 'Search Trails', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>'],
        ['id' => 'filter-section', 'title' => 'Filters', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>'],
        ['id' => 'trails-grid', 'title' => 'Trail Results', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>']
    ];
    @endphp
    
    <x-floating-navigation :sections="$sections" />
@endpush

@php
    // Initialize the TrailImageService for dynamic images
    $imageService = app('App\Services\TrailImageService');
@endphp

<div class="relative bg-gradient-to-br from-green-50 via-white to-blue-50 min-h-screen">
    <div x-data="trailExplorer()" class="flex flex-col min-h-screen">
        
        <!-- Hero Section -->
        <div id="hero-section" class="relative bg-gradient-to-r from-yellow-400 via-yellow-200 to-teal-400 text-white overflow-hidden hero-container">
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            
            <!-- Enhanced Trail Elements Background (Hidden on mobile) -->
            <div class="absolute inset-0 opacity-30 hidden md:block">
                <!-- Elegant curved trail lines with visible animation -->
                <svg class="absolute inset-0 w-full h-full animate-pulse-slow" viewBox="0 0 1200 400" fill="none" preserveAspectRatio="none">
                    <path d="M0 200 Q300 100 600 200 T1200 200" stroke="white" stroke-width="3" fill="none" class="connection-line"/>
                    <path d="M0 240 Q400 140 800 240 T1200 240" stroke="white" stroke-width="2" fill="none" class="connection-line-2"/>
                    <path d="M0 160 Q200 80 400 160 T800 160" stroke="white" stroke-width="1.5" fill="none" class="connection-line-3"/>
                </svg>
                
                <!-- More visible floating trail waypoints -->
                <div class="absolute top-16 left-1/4 w-4 h-4 bg-white rounded-full floating-node glow-effect" data-speed="2"></div>
                <div class="absolute top-24 right-1/3 w-3 h-3 bg-white rounded-full floating-node glow-effect" data-speed="3"></div>
                <div class="absolute bottom-20 left-1/3 w-5 h-5 bg-white rounded-full floating-node glow-effect" data-speed="1.5"></div>
                <div class="absolute bottom-16 right-1/4 w-3 h-3 bg-white rounded-full floating-node glow-effect" data-speed="2.5"></div>
                <div class="absolute top-1/2 left-1/5 w-3 h-3 bg-white rounded-full floating-node glow-effect" data-speed="1.8"></div>
                <div class="absolute top-1/3 right-1/5 w-4 h-4 bg-white rounded-full floating-node glow-effect" data-speed="2.2"></div>
                
                <!-- Larger interactive geometric shapes -->
                <div class="absolute top-12 right-16 w-12 h-12 border-2 border-white rounded-full opacity-70 hover-circle pulse-ring" data-hover="scale"></div>
                <div class="absolute bottom-12 left-12 w-10 h-10 border-2 border-white rounded-full opacity-60 hover-circle pulse-ring" data-hover="pulse"></div>
                <div class="absolute top-1/2 right-1/2 w-8 h-8 border border-white rounded-full opacity-50 hover-circle" data-hover="rotate"></div>
                
                <!-- Visible floating particles -->
                <div class="floating-particle absolute top-1/4 left-1/2 w-2 h-2 bg-white rounded-full opacity-60" data-delay="0"></div>
                <div class="floating-particle absolute top-3/4 right-1/3 w-1.5 h-1.5 bg-white rounded-full opacity-70" data-delay="2"></div>
                <div class="floating-particle absolute bottom-1/3 left-1/5 w-1.5 h-1.5 bg-white rounded-full opacity-50" data-delay="4"></div>
                <div class="floating-particle absolute top-1/6 left-2/3 w-1 h-1 bg-white rounded-full opacity-60" data-delay="1"></div>
                <div class="floating-particle absolute bottom-1/6 right-1/5 w-1 h-1 bg-white rounded-full opacity-50" data-delay="3"></div>
                
                <!-- Connection network lines -->
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 1200 400" fill="none" preserveAspectRatio="none">
                    <line x1="300" y1="80" x2="500" y2="320" stroke="white" stroke-width="1" opacity="0.3" class="connect-line"/>
                    <line x1="700" y1="100" x2="900" y2="280" stroke="white" stroke-width="1" opacity="0.3" class="connect-line"/>
                    <line x1="200" y1="200" x2="400" y2="120" stroke="white" stroke-width="1" opacity="0.3" class="connect-line"/>
                </svg>
                
                <!-- Decorative trail icons -->
                <div class="absolute top-20 left-16 opacity-40">
                    <svg class="w-6 h-6" fill="white" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-10-5z"/>
                    </svg>
                </div>
                <div class="absolute bottom-20 right-16 opacity-40">
                    <svg class="w-8 h-8" fill="white" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
                <div class="text-center">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-3 sm:mb-4 px-2">Your Followed Trails</h1>
                    <p class="text-lg sm:text-xl md:text-2xl text-green-100 mb-6 sm:mb-8 px-4">Explore trails from organizations you follow</p>
                    
                    <!-- Search Bar -->
                    <div class="max-w-2xl mx-auto px-4">
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                <input id="explore-search-input" type="text" 
                    placeholder="Search trails, mountains, or locations..."
                    class="w-full pl-11 sm:pl-12 pr-4 py-3 sm:py-4 text-base sm:text-lg border-0 rounded-2xl focus:ring-4 focus:ring-white focus:ring-opacity-50 transition-all duration-200 text-gray-900 placeholder-gray-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div id="filter-section" class="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col gap-4">
                    <!-- Mobile: Title, Results Count, and Filter Toggle Button -->
                    <div class="flex items-center justify-between">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Filter Trails</h2>
                        <div class="flex items-center gap-3">
                            <div class="text-gray-700 text-sm sm:text-base">
                                @if(isset($followedTrails))
                                    <span class="font-semibold">{{ $followedTrails->count() }}</span> trails
                                @else
                                    <span class="font-semibold">0</span> trails
                                @endif
                            </div>
                            <!-- Toggle Button (Mobile Only) -->
                            <button 
                                onclick="toggleFilters()" 
                                id="filter-toggle-btn"
                                class="lg:hidden inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-xl text-sm font-medium hover:bg-green-700 transition-colors duration-200">
                                <svg id="filter-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                                </svg>
                                <span id="filter-btn-text">Show Filters</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filters Grid - Responsive (Hidden on mobile by default) -->
                    <div id="filters-container" class="hidden lg:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- Mountain/Location Filter -->
                        <div class="relative">
                            <select id="explore-location-select" class="appearance-none w-full px-3 sm:px-4 py-2.5 sm:py-3 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white text-sm sm:text-base">
                                <option value="">All Mountains</option>
                                @if(isset($followedTrails) && $followedTrails->count() > 0)
                                    @php
                                        // Get unique locations from both location.name and mountain_name
                                        $locations = $followedTrails->pluck('location.name')->filter();
                                        $mountains = $followedTrails->pluck('mountain_name')->filter();
                                        $uniqueLocations = $locations->merge($mountains)->unique()->sort();
                                    @endphp
                                    @foreach($uniqueLocations as $locationName)
                                        <option value="{{ $locationName }}">{{ $locationName }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Difficulty Filter -->
                        <div class="relative">
                            <select id="explore-difficulty-select" class="appearance-none w-full px-3 sm:px-4 py-2.5 sm:py-3 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white text-sm sm:text-base">
                                <option value="">All Difficulties</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Season Filter -->
                        <div class="relative">
                            <select id="explore-season-select" class="appearance-none w-full px-3 sm:px-4 py-2.5 sm:py-3 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white text-sm sm:text-base">
                                <option value="">All Seasons</option>
                                <!-- Philippine seasons mapping: Dry (Amihan), Wet (Habagat), Transition (both/typhoon) -->
                                <option value="amihan">Amihan (Dry Season)</option>
                                <option value="habagat">Habagat (Wet Season)</option>
                                <option value="year-round">Year Round / All Seasons</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="relative">
                            <select id="explore-sort-select" class="appearance-none w-full px-3 sm:px-4 py-2.5 sm:py-3 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white text-sm sm:text-base">
                                <option value="popularity">Sort by Popularity</option>
                                <option value="rating">Sort by Rating</option>
                                <option value="length">Sort by Length</option>
                                <option value="price_low_high">Price: Low to High</option>
                                <option value="price_high_low">Price: High to Low</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleFilters() {
                const container = document.getElementById('filters-container');
                const btnText = document.getElementById('filter-btn-text');
                const filterIcon = document.getElementById('filter-icon');
                
                if (container.classList.contains('hidden')) {
                    // Show filters
                    container.classList.remove('hidden');
                    container.classList.add('grid');
                    btnText.textContent = 'Hide Filters';
                    // Change icon to close/up arrow
                    filterIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>';
                } else {
                    // Hide filters
                    container.classList.add('hidden');
                    container.classList.remove('grid');
                    btnText.textContent = 'Show Filters';
                    // Change icon back to filter
                    filterIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>';
                }
            }
        </script>

        <!-- Main Content -->
        <main id="trails-grid" class="flex-1 px-4 sm:px-6 py-6 sm:py-8">
            <div class="max-w-7xl mx-auto">
                
                <!-- Trail Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="trails-list">
                    @if(isset($followedTrails) && $followedTrails->count() > 0)
                        @foreach($followedTrails as $trail)
                            @php
                                // Normalize season values if available on the model
                                $seasonKey = strtolower(str_replace(' ', '-', optional($trail)->best_season ?? ''));
                                // map some common season labels to our keys
                                if(in_array($seasonKey, ['dry','dry season','amihan'])) $seasonKey = 'amihan';
                                if(in_array($seasonKey, ['wet','wet season','habagat','rainy'])) $seasonKey = 'habagat';
                                if(empty($seasonKey)) $seasonKey = 'year-round';
                            @endphp
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 cursor-pointer group" 
                                 x-data
                                 data-trail-name="{{ strtolower($trail->trail_name) }}"
                                 data-mountain-name="{{ strtolower($trail->mountain_name) }}"
                                 data-organization="{{ strtolower($trail->user->display_name) }}"
                                 data-location="{{ strtolower(optional($trail->location)->name ?? $trail->mountain_name) }}"
                                 data-difficulty="{{ strtolower($trail->difficulty) }}"
                                 data-season="{{ $seasonKey }}"
                                 data-rating="{{ $trail->average_rating ?? 0 }}"
                                 data-length="{{ $trail->length ?? 0 }}"
                                 data-price="{{ optional($trail->package)->price ?? $trail->price ?? 0 }}">
                                
                                <!-- Trail Image -->
                                <div class="relative h-64 overflow-hidden">
                                    @php
                                        // Get dynamic image from enhanced TrailImageService
                                        $primaryImage = $imageService->getPrimaryTrailImage($trail);
                                        $trailImage = $primaryImage['url'];
                                    @endphp
                                    <img src="{{ $trailImage }}" 
                                         alt="{{ $trail->trail_name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    
                                    <!-- Image source badge for API images -->
                                    @if($primaryImage['source'] !== 'organization')
                                        <div class="absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded">
                                            {{ ucfirst($primaryImage['source']) }}
                                        </div>
                                    @endif
                                    
                                    <!-- Difficulty Badge -->
                                    <div class="absolute top-4 left-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full text-white shadow-lg {{ $trail->difficulty === 'beginner' ? 'bg-green-600' : ($trail->difficulty === 'intermediate' ? 'bg-yellow-600' : 'bg-red-600') }}">
                                            {{ $trail->difficulty_label }}
                                        </span>
                                    </div>

                                    <!-- Price Badge -->
                                    <div class="absolute top-4 right-4">
                                        <span class="bg-green-500 text-white px-3 py-1 text-sm font-bold rounded-full shadow-lg">
                                            ₱{{ number_format(optional($trail->package)->price ?? $trail->price ?? 0, 0) }}
                                        </span>
                                    </div>

                                    <!-- Rating -->
                                    <div class="absolute bottom-4 left-4 bg-black bg-opacity-60 text-white px-3 py-2 rounded-xl text-sm backdrop-blur-sm">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            <span>{{ number_format($trail->average_rating, 1) }}</span>
                                        </div>
                                    </div>

                                    <!-- Quick Stats Overlay -->
                                    <div class="absolute bottom-4 right-4 bg-black bg-opacity-60 text-white px-3 py-2 rounded-xl text-sm backdrop-blur-sm">
                                        <div class="text-center">
                                            <div class="font-bold">{{ $trail->length }} km</div>
                                            <div class="text-xs text-gray-300">Length</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Trail Info -->
                                <div class="p-6 space-y-4">
                                    <div>
                                        <h3 class="font-bold text-xl text-gray-900 group-hover:text-green-600 transition-colors duration-200 mb-2">{{ $trail->trail_name }}</h3>
                                        <p class="text-gray-600 text-sm flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span>{{ $trail->mountain_name }}</span>
                                        </p>
                                    </div>

                                    <!-- Trail Stats -->
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div class="text-center">
                                            <div class="font-bold text-gray-900">{{ $trail->elevation_gain }} m</div>
                                            <div class="text-gray-500 text-xs">Elevation</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-gray-900">{{ $trail->estimated_time }}</div>
                                            <div class="text-gray-500 text-xs">Duration</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-gray-900">{{ $trail->best_season ?? 'Year-round' }}</div>
                                            <div class="text-gray-500 text-xs">Best Season</div>
                                        </div>
                                    </div>

                                    <!-- Organization -->
                                    <div class="flex items-center gap-3 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Organized by</p>
                                            <p class="font-medium text-gray-900">{{ $trail->user->display_name }}</p>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex gap-3 pt-2">
                                        <a href="{{ route('trails.show', $trail->slug) }}" 
                                           class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-xl text-sm font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View Details
                                        </a>
                                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white py-3 px-4 rounded-xl text-sm font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Empty State for No Followed Organizations -->
                        <div class="col-span-full text-center py-20">
                            <div class="max-w-md mx-auto">
                                <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">No trails available</h3>
                                <p class="text-gray-600 mb-6">Follow hiking organizations to discover their trails and start your adventure!</p>
                                
                                <!-- Show community link for hikers who aren't following organizations -->
                                <div class="mb-6">
                                    <a href="{{ route('community.index') }}" 
                                       class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Discover Organizations
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <script>
        // Trail Explorer functionality using vanilla JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[TrailExplorer] Initializing vanilla JS version...');
            
            const searchInput = document.getElementById('explore-search-input');
            const locationSelect = document.getElementById('explore-location-select');
            const difficultySelect = document.getElementById('explore-difficulty-select');
            const seasonSelect = document.getElementById('explore-season-select');
            const sortSelect = document.getElementById('explore-sort-select');
            const trailsList = document.getElementById('trails-list');
            const countElement = document.querySelector('.text-gray-700 .font-semibold');
            
            let searchTimer = null;
            
            function applyFilters() {
                console.log('[TrailExplorer] Applying filters...');
                
                if (!trailsList) {
                    console.error('[TrailExplorer] trails-list not found');
                    return;
                }
                
                const cards = trailsList.querySelectorAll('[data-trail-name]');
                console.log('[TrailExplorer] Found', cards.length, 'trail cards');
                
                if (cards.length === 0) {
                    console.warn('[TrailExplorer] No trail cards found');
                    return;
                }
                
                // Get filter values
                const search = (searchInput?.value || '').toLowerCase().trim();
                const location = (locationSelect?.value || '').toLowerCase().trim();
                const difficulty = (difficultySelect?.value || '').toLowerCase().trim();
                const season = (seasonSelect?.value || '').toLowerCase().trim();
                const sort = sortSelect?.value || 'popularity';
                
                console.log('[TrailExplorer] Filter values:', { search, location, difficulty, season, sort });
                
                let visibleCount = 0;
                const visibleCards = [];
                
                // Filter cards
                cards.forEach((card, index) => {
                    let visible = true;
                    
                    // Search filter
                    if (search) {
                        const trailName = (card.getAttribute('data-trail-name') || '').toLowerCase();
                        const mountainName = (card.getAttribute('data-mountain-name') || '').toLowerCase();
                        const organization = (card.getAttribute('data-organization') || '').toLowerCase();
                        const cardLocation = (card.getAttribute('data-location') || '').toLowerCase();
                        
                        const searchText = `${trailName} ${mountainName} ${organization} ${cardLocation}`;
                        if (!searchText.includes(search)) {
                            visible = false;
                        }
                    }
                    
                    // Location filter
                    if (visible && location) {
                        const cardLocation = (card.getAttribute('data-location') || '').toLowerCase();
                        const mountainName = (card.getAttribute('data-mountain-name') || '').toLowerCase();
                        
                        if (!cardLocation.includes(location) && !mountainName.includes(location)) {
                            visible = false;
                        }
                    }
                    
                    // Difficulty filter
                    if (visible && difficulty) {
                        const cardDifficulty = (card.getAttribute('data-difficulty') || '').toLowerCase();
                        if (cardDifficulty !== difficulty) {
                            visible = false;
                        }
                    }
                    
                    // Season filter
                    if (visible && season && season !== 'year-round') {
                        const cardSeason = (card.getAttribute('data-season') || '').toLowerCase();
                        if (cardSeason !== season && cardSeason !== 'year-round') {
                            visible = false;
                        }
                    }
                    
                    // Show/hide card
                    if (visible) {
                        card.style.display = '';
                        card.classList.remove('hidden');
                        visibleCards.push(card);
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                        card.classList.add('hidden');
                    }
                    
                    // Debug first few cards
                    if (index < 2) {
                        console.log('[TrailExplorer] Card', index, ':', {
                            trail: card.getAttribute('data-trail-name'),
                            visible,
                            attributes: {
                                location: card.getAttribute('data-location'),
                                difficulty: card.getAttribute('data-difficulty'),
                                season: card.getAttribute('data-season')
                            }
                        });
                    }
                });
                
                // Sort visible cards
                sortCards(visibleCards, sort);
                
                // Update count
                if (countElement) {
                    countElement.textContent = visibleCount;
                }
                
                console.log('[TrailExplorer] Filtering complete. Visible:', visibleCount);
            }
            
            function sortCards(cards, sortBy) {
                if (cards.length === 0) return;
                
                const sortedCards = [...cards].sort((a, b) => {
                    const getValue = (card, attr, defaultVal = 0) => {
                        return parseFloat(card.getAttribute(attr) || defaultVal) || defaultVal;
                    };
                    
                    switch (sortBy) {
                        case 'rating':
                            return getValue(b, 'data-rating') - getValue(a, 'data-rating');
                        case 'length':
                            return getValue(a, 'data-length') - getValue(b, 'data-length');
                        case 'price_low_high':
                            return getValue(a, 'data-price') - getValue(b, 'data-price');
                        case 'price_high_low':
                            return getValue(b, 'data-price') - getValue(a, 'data-price');
                        case 'popularity':
                        default:
                            return getValue(b, 'data-rating') - getValue(a, 'data-rating');
                    }
                });
                
                // Reorder DOM elements
                sortedCards.forEach(card => {
                    trailsList.appendChild(card);
                });
                
                console.log('[TrailExplorer] Sorted by:', sortBy);
            }
            
            // Debounced search
            function debounceFilter() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(applyFilters, 300);
            }
            
            // Event listeners
            if (searchInput) {
                searchInput.addEventListener('input', debounceFilter);
                console.log('[TrailExplorer] Search input listener attached');
            }
            
            if (locationSelect) {
                locationSelect.addEventListener('change', applyFilters);
                console.log('[TrailExplorer] Location select listener attached');
            }
            
            if (difficultySelect) {
                difficultySelect.addEventListener('change', applyFilters);
                console.log('[TrailExplorer] Difficulty select listener attached');
            }
            
            if (seasonSelect) {
                seasonSelect.addEventListener('change', applyFilters);
                console.log('[TrailExplorer] Season select listener attached');
            }
            
            if (sortSelect) {
                sortSelect.addEventListener('change', applyFilters);
                console.log('[TrailExplorer] Sort select listener attached');
            }
            
            // Initial filter application
            setTimeout(applyFilters, 100);
        });
        
        // Alpine.js component for compatibility
        function trailExplorer() {
            return {
                searchQuery: '',
                selectedLocation: '',
                sortBy: 'popularity',
                filters: {
                    difficulty: '',
                    season: ''
                }
            }
        }
    </script>

</div>

