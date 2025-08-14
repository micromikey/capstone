@props(['user', 'followedTrails', 'followingCount'])

@php
    // Initialize the TrailImageService for dynamic images
    $imageService = app('App\Services\TrailImageService');
@endphp

<div class="relative bg-gradient-to-br from-green-50 via-white to-blue-50 min-h-screen">
    <div x-data="trailExplorer()" x-init="init()" x-cloak class="flex flex-col min-h-screen">
        
        <!-- Hero Section -->
        <div class="relative bg-gradient-to-r from-green-600 via-green-500 to-blue-600 text-white">
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            <div class="relative max-w-7xl mx-auto px-6 py-16">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">Your Followed Trails</h1>
                    <p class="text-xl md:text-2xl text-green-100 mb-8">Explore trails from organizations you follow</p>
                    
                    <!-- Search Bar -->
                    <div class="max-w-2xl mx-auto">
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" 
                                   placeholder="Search trails, mountains, or locations..."
                                   class="w-full pl-12 pr-4 py-4 text-lg border-0 rounded-2xl focus:ring-4 focus:ring-white focus:ring-opacity-50 transition-all duration-200 text-gray-900 placeholder-gray-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                    <div class="flex items-center gap-4">
                        <h2 class="text-lg font-semibold text-gray-900">Filter Trails:</h2>
                        
                        <!-- Mountain/Location Filter -->
                        <div class="relative">
                            <select class="appearance-none px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white min-w-[180px]">
                                <option value="">All Mountains</option>
                                @if(isset($followedTrails) && $followedTrails->count() > 0)
                                    @php
                                        $uniqueLocations = $followedTrails->pluck('location.name')->unique()->filter();
                                    @endphp
                                    @foreach($uniqueLocations as $locationName)
                                        <option value="{{ $locationName }}">{{ $locationName }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Difficulty Filter -->
                        <div class="relative">
                            <select class="appearance-none px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white min-w-[160px]">
                                <option value="">All Difficulties</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Season Filter -->
                        <div class="relative">
                            <select class="appearance-none px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white min-w-[140px]">
                                <option value="">All Seasons</option>
                                <option value="dry">Dry Season</option>
                                <option value="wet">Wet Season</option>
                                <option value="year-round">Year Round</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Results Count & Sort -->
                    <div class="flex items-center gap-4">
                        <!-- Sort Options -->
                        <div class="relative">
                            <select class="appearance-none px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white text-sm">
                                <option value="name">Sort by Name</option>
                                <option value="difficulty">Sort by Difficulty</option>
                                <option value="length">Sort by Length</option>
                                <option value="rating">Sort by Rating</option>
                                <option value="price">Sort by Price</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-1 px-6 py-8">
            <div class="max-w-7xl mx-auto">
                
                <!-- Trail Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @if(isset($followedTrails) && $followedTrails->count() > 0)
                        @foreach($followedTrails as $trail)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 cursor-pointer group">
                                
                                <!-- Trail Image -->
                                <div class="relative h-64 overflow-hidden">
                                    @php
                                        // Get dynamic image from TrailImageService
                                        $trailImage = $imageService->getTrailImage($trail, 'primary', 'medium');
                                    @endphp
                                    <img src="{{ $trailImage }}" 
                                         alt="{{ $trail->trail_name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    
                                    <!-- Difficulty Badge -->
                                    <div class="absolute top-4 left-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full text-white shadow-lg {{ $trail->difficulty === 'beginner' ? 'bg-green-600' : ($trail->difficulty === 'intermediate' ? 'bg-yellow-600' : 'bg-red-600') }}">
                                            {{ ucfirst($trail->difficulty) }}
                                        </span>
                                    </div>

                                    <!-- Price Badge -->
                                    <div class="absolute top-4 right-4">
                                        <span class="bg-green-500 text-white px-3 py-1 text-sm font-bold rounded-full shadow-lg">
                                            ₱{{ number_format($trail->price, 0) }}
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
        function trailExplorer() {
            return {
                searchQuery: '',
                selectedLocation: '',
                filters: {
                    difficulty: '',
                    season: ''
                },
                
                init() {
                    // Basic initialization
                    console.log('Trail explorer initialized');
                },
                
                filterTrails() {
                    // For now, just log the filter changes
                    // This can be enhanced later with actual filtering
                    console.log('Filters changed:', {
                        location: this.selectedLocation,
                        difficulty: this.filters.difficulty,
                        season: this.filters.season
                    });
                },
                
                sortTrails() {
                    // For now, just log the sort change
                    console.log('Sort changed');
                },
                
                debounceSearch() {
                    // For now, just log the search
                    console.log('Search query:', this.searchQuery);
                }
            }
        }
    </script>
</div>

