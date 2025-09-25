<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $trail->trail_name }}
            </h2>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('explore') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Explore
                </a>
                <!-- Favorite Button -->
                <button id="favorite-btn" data-trail-id="{{ $trail->id }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg id="favorite-icon" class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.343l-6.828-6.829a4 4 0 010-5.656z" />
                    </svg>
                    <span id="favorite-text">Save</span>
                    <span id="favorite-count" class="ml-2 text-sm text-white/80">({{ $trail->favoritedBy()->count() }})</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Trail Image Gallery -->
                <div class="relative h-96 bg-gray-200" x-data="trailGallery()" x-init="init()">
                    <!-- Main Image -->
                    <div class="w-full h-full overflow-hidden">
                        <img x-show="currentImage" 
                             :src="currentImage" 
                             :alt="'{{ $trail->trail_name }} - Image ' + (currentIndex + 1)"
                             class="w-full h-full object-cover transition-all duration-300">
                        
                        <!-- Fallback when no images -->
                        <div x-show="!currentImage" class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Navigation Controls -->
                    <div x-show="images.length > 1" class="absolute inset-y-0 left-0 flex items-center">
                        <button @click="previousImage()" 
                                class="ml-4 p-3 bg-black/50 hover:bg-black/70 text-white rounded-full backdrop-blur-sm transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div x-show="images.length > 1" class="absolute inset-y-0 right-0 flex items-center">
                        <button @click="nextImage()" 
                                class="mr-4 p-3 bg-black/50 hover:bg-black/70 text-white rounded-full backdrop-blur-sm transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Image Counter -->
                    <div x-show="images.length > 1" 
                         class="absolute top-4 right-4 bg-black/60 text-white px-3 py-1 rounded-full text-sm backdrop-blur-sm">
                        <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                    </div>

                    <!-- Image Dots -->
                    <div x-show="images.length > 1" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                        <template x-for="(image, index) in images" :key="index">
                            <button @click="setImage(index)" 
                                    :class="currentIndex === index ? 'bg-white' : 'bg-white/50'"
                                    class="w-3 h-3 rounded-full transition-all hover:bg-white/75"></button>
                        </template>
                    </div>
                    
                    <!-- Overlay Info -->
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-white">
                                <h1 class="text-3xl font-bold mb-2">{{ $trail->trail_name }}</h1>
                                <p class="text-xl text-gray-200">{{ $trail->mountain_name }}</p>
                                <p class="text-gray-300">{{ $trail->location->name }}, {{ $trail->location->province }}</p>
                            </div>
                            <div class="text-right text-white">
                                <div class="text-3xl font-bold">₱{{ number_format(optional($trail->package)->price ?? $trail->price ?? 0, 2) }}</div>
                                <div class="text-sm text-gray-200">Package Price</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trail Content -->
                <div class="p-6">
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $trail->length }} km</div>
                            <div class="text-sm text-gray-500">Length</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $trail->elevation_gain }} m</div>
                            <div class="text-sm text-gray-500">Elevation Gain</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ optional($trail->package)->duration ?? $trail->duration }}</div>
                            <div class="text-sm text-gray-500">Duration</div>
                        </div>
                        <div class="text-center">
                            @php
                                // Prefer the formatted value if model provides it. If not, and we have an integer
                                // `estimated_time` (stored as minutes), convert to a friendly string here.
                                function format_minutes_to_human($mins) {
                                    if (!$mins || !is_numeric($mins) || $mins <= 0) return 'N/A';
                                    $m = (int)$mins;
                                    if ($m >= 60*24) {
                                        $days = intdiv($m, 60*24);
                                        $hours = intdiv($m % (60*24), 60);
                                        return $days . ' day' . ($days>1 ? 's' : '') . ($hours ? ' ' . $hours . ' h' : '');
                                    }
                                    if ($m >= 60) {
                                        $hours = intdiv($m, 60);
                                        $minutes = $m % 60;
                                        return $hours . ' h' . ($minutes ? ' ' . $minutes . ' m' : '');
                                    }
                                    return $m . ' m';
                                }

                                $estDisplay = $trail->estimated_time_formatted ?? null;
                                if (!$estDisplay && isset($trail->estimated_time)) {
                                    $estDisplay = format_minutes_to_human($trail->estimated_time);
                                }
                            @endphp
                            <div class="text-2xl font-bold text-gray-900">{{ $estDisplay ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">Estimated Hiking Time</div>
                        </div>
                    </div>

                    <!-- Difficulty and Organization -->
                    <div class="flex flex-col md:flex-row gap-6 mb-8">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Trail Information</h3>
                            <div class="space-y-2 text-sm text-gray-700">
                                <div class="flex justify-between">
                                    <span class="font-medium">Difficulty:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white 
                                        {{ $trail->difficulty === 'beginner' ? 'bg-green-500' : ($trail->difficulty === 'intermediate' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                        {{ $trail->difficulty_label }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Best Season:</span>
                                    <span>{{ $trail->best_season }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Package Price:</span>
                                    <span class="font-semibold text-green-600">₱{{ number_format(optional($trail->package)->price ?? $trail->price ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Organization</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $trail->user->profile_photo_url }}" alt="{{ $trail->user->display_name }}" class="w-12 h-12 rounded-full object-cover">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $trail->user->display_name }}</p>
                                            <p class="text-sm text-gray-600">Trail Organizer</p>
                                            @if($trail->user->user_type === 'organization')
                                                <div class="flex items-center text-xs text-gray-500 mt-1">
                                                    <svg class="w-3 h-3 mr-1 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Verified Organization
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @auth
                                        @if(auth()->user()->user_type === 'hiker' && $trail->user->user_type === 'organization')
                                            <button id="follow-org-btn"
                                                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ auth()->user()->isFollowing($trail->user_id) ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                                                    data-organization-id="{{ $trail->user_id }}"
                                                    data-organization-name="{{ $trail->user->display_name }}">
                                                @if(auth()->user()->isFollowing($trail->user_id))
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Following
                                                    </span>
                                                @else
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Follow
                                                    </span>
                                                @endif
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($trail->summary)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $trail->summary }}</p>
                        </div>
                    @endif

                    <!-- Features -->
                    @if($trail->features && count($trail->features) > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Trail Features</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($trail->features as $feature)
                                    <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">{{ $feature }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Interactive Trail Map with Route -->
                    @if($trail->coordinates && count($trail->coordinates) > 0)
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900">Trail Route & Map</h3>
                                <div class="flex gap-2">
                                    <div class="flex flex-col gap-1">
                                            <a href="{{ route('trails.download-map-tcpdf', $trail) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Download PDF Map
                                            </a>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <button type="button" data-trail-id="{{ $trail->id }}" data-print-url="{{ route('trails.print-map', $trail) }}" onclick="printTrailMapFromButton(this)" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V3h12v6M6 21h12V9H6v12z" />
                                            </svg>
                                            Print Map
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Location Tracking Status -->
                            <div id="tracking-status" class="hidden mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="animate-pulse w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                        <span class="text-blue-800 font-medium">Tracking your location...</span>
                                    </div>
                                    <button id="stop-tracking" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Stop Tracking</button>
                                </div>
                                <div class="mt-2 text-sm text-blue-700">
                                    <span id="distance-from-trail">Distance from trail: Calculating...</span>
                                    <br>
                                    <span id="progress-percentage">Progress: 0%</span>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <div id="interactive-trail-map" style="height: 500px; width: 100%; border-radius: 8px;"></div>
                                <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <div class="w-4 h-1 bg-red-500 mr-2"></div>
                                        Trail Route
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                        Start Point
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                        End Point
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900">Trail Route & Map</h3>
                                @auth
                                    @if(auth()->user()->user_type === 'organization' && $trail->user_id === auth()->id())
                                        <a href="/admin/trails/coordinates" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
                                            </svg>
                                            Generate Coordinates
                                        </a>
                                    @endif
                                @endauth
                            </div>
                            
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                                <svg class="w-12 h-12 text-yellow-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                <h4 class="text-lg font-semibold text-yellow-800 mb-2">Trail Coordinates Not Available</h4>
                                <p class="text-yellow-700 mb-4">This trail doesn't have GPS coordinates yet. Coordinates are needed for the interactive map, tracking features, and downloadable PDF maps.</p>
                                @auth
                                    @if(auth()->user()->user_type === 'organization' && $trail->user_id === auth()->id())
                                        <p class="text-sm text-yellow-600">As the trail owner, you can generate coordinates using Google Maps data.</p>
                                    @else
                                        <p class="text-sm text-yellow-600">Please contact the trail organization to add GPS coordinates for this trail.</p>
                                    @endif
                                @else
                                    <p class="text-sm text-yellow-600">GPS coordinates will be available once the trail organization adds them.</p>
                                @endauth
                            </div>
                        </div>
                    @endif

                    <!-- Package Inclusions -->
                    @if(optional($trail->package)->package_inclusions ?? $trail->package_inclusions)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Package Inclusions</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700 whitespace-pre-line">{{ optional($trail->package)->package_inclusions ?? $trail->package_inclusions }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Reviews Section -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-semibold text-gray-900">Reviews & Ratings</h3>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= round($trail->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600" id="average-rating">{{ number_format($trail->average_rating, 1) }} out of 5</span>
                                </div>
                                <span class="text-sm text-gray-500" id="total-reviews">({{ $trail->total_reviews }} {{ $trail->total_reviews === 1 ? 'review' : 'reviews' }})</span>
                            </div>
                        </div>

                        <!-- Review Statistics -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                            <!-- Rating Distribution -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="font-medium text-gray-900 mb-4">Rating Distribution</h4>
                                <div class="space-y-2" id="rating-distribution">
                                    @php
                                        $ratingDistribution = [];
                                        for ($i = 5; $i >= 1; $i--) {
                                            $count = $trail->reviews()->where('rating', $i)->count();
                                            $percentage = $trail->total_reviews > 0 ? round(($count / $trail->total_reviews) * 100) : 0;
                                            $ratingDistribution[$i] = ['count' => $count, 'percentage' => $percentage];
                                        }
                                    @endphp
                                    @foreach($ratingDistribution as $stars => $data)
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-600 w-2">{{ $stars }}</span>
                                            <svg class="w-4 h-4 text-yellow-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <div class="flex-1 mx-2">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full rating-fill" data-percentage="{{ $data['percentage'] }}"></div>
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $data['count'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Submit Review Form (Only for followers) -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                @auth
                                    @if(auth()->user()->user_type === 'hiker')
                                        @if(auth()->user()->isFollowing($trail->user_id))
                                            @php
                                                $existingReview = $trail->reviews()->where('user_id', auth()->id())->first();
                                            @endphp
                                            @if(!$existingReview)
                                                <h4 class="font-medium text-gray-900 mb-4">Share Your Experience</h4>
                                                <form id="review-form" class="space-y-4" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="trail_id" value="{{ $trail->id }}">
                                                    
                                                    <!-- Rating -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                                        <div class="flex items-center space-x-1" id="rating-stars">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <button type="button" class="rating-star text-gray-300 hover:text-yellow-400 focus:outline-none" data-rating="{{ $i }}">
                                                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                    </svg>
                                                                </button>
                                                            @endfor
                                                        </div>
                                                        <input type="hidden" name="rating" id="rating-input" required>
                                                    </div>
                                                    
                                                    <!-- Hike Date -->
                                                    <div>
                                                        <label for="hike_date" class="block text-sm font-medium text-gray-700 mb-2">When did you hike this trail?</label>
                                                        <input type="date" id="hike_date" name="hike_date" max="{{ date('Y-m-d') }}" required
                                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                                    </div>
                                                    
                                                    <!-- Conditions -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Trail Conditions (optional)</label>
                                                        <div class="flex flex-wrap gap-2">
                                                            @foreach(['sunny', 'cloudy', 'rainy', 'windy', 'foggy', 'hot', 'cold', 'humid', 'dry'] as $condition)
                                                                <label class="inline-flex items-center">
                                                                    <input type="checkbox" name="conditions[]" value="{{ $condition }}" 
                                                                           class="text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                                                    <span class="ml-2 text-sm text-gray-600">{{ ucfirst($condition) }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Review Text -->
                                                    <div>
                                                        <label for="review" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                                                        <textarea id="review" name="review" rows="4" required minlength="10" maxlength="1000"
                                                                   placeholder="Share your experience on this trail..."
                                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                                                                   oninput="checkContent(this.value)"></textarea>
                                                        <div class="mt-1 flex justify-between text-xs text-gray-500">
                                                            <span>Minimum 10 characters</span>
                                                            <span id="review-char-count">0/1000</span>
                                                        </div>
                                                        
                                                        <!-- Content Filtering Preview -->
                                                        <div id="content-filter-preview" class="mt-2 hidden">
                                                            <div class="text-xs p-2 rounded-lg">
                                                                <div class="flex items-center space-x-2 mb-1">
                                                                    <span id="content-score-label" class="font-medium"></span>
                                                                    <span id="content-score" class="text-lg font-bold"></span>
                                                                </div>
                                                                <div id="content-feedback" class="text-gray-600"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Review Images -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photos (Optional)</label>
                                                        <div class="space-y-3">
                                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="image-upload-grid">
                                                                @for($i = 0; $i < 4; $i++)
                                                                    <div class="relative">
                                                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-emerald-400 transition-colors cursor-pointer image-upload-slot" data-slot="{{ $i }}">
                                                                            <input type="file" name="review_images[]" class="hidden image-input" accept="image/*" data-slot="{{ $i }}">
                                                                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                            </svg>
                                                                            <p class="text-xs text-gray-500">Add Photo</p>
                                                                        </div>
                                                                        <div class="image-preview hidden absolute inset-0 bg-white rounded-lg overflow-hidden">
                                                                            <img src="" alt="Preview" class="w-full h-full object-cover">
                                                                            <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 remove-image" data-slot="{{ $i }}">
                                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endfor
                                                            </div>
                                                            <p class="text-xs text-gray-500">Upload up to 4 photos. Supported formats: JPG, PNG, GIF. Max size: 2MB each.</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <button type="submit" id="submit-review-btn"
                                                            class="w-full bg-emerald-600 text-white py-2 px-4 rounded-lg hover:bg-emerald-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        Submit Review
                                                    </button>
                                                </form>
                                            @else
                                                <div class="text-center py-4">
                                                    <svg class="mx-auto h-8 w-8 text-green-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <h4 class="font-medium text-gray-900 mb-1">You've already reviewed this trail</h4>
                                                    <p class="text-sm text-gray-600">Thank you for sharing your experience!</p>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center py-6">
                                                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                <h4 class="font-medium text-gray-900 mb-2">Follow to Review</h4>
                                                <p class="text-sm text-gray-600 mb-4">You need to follow {{ $trail->user->display_name }} to submit a review for this trail.</p>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-6">
                                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <h4 class="font-medium text-gray-900 mb-2">Hiker Account Required</h4>
                                            <p class="text-sm text-gray-600">Only hikers can submit trail reviews.</p>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-6">
                                        <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                        <h4 class="font-medium text-gray-900 mb-2">Sign In to Review</h4>
                                        <p class="text-sm text-gray-600 mb-4">Please sign in to submit a review for this trail.</p>
                                        <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">Sign In</a>
                                    </div>
                                @endauth
                            </div>
                        </div>

                        <!-- Reviews List -->
                        <div class="bg-white">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Recent Reviews</h4>
                            
                            <div id="reviews-container" class="space-y-6">
                                @if($trail->reviews && $trail->reviews->count() > 0)
                                    @foreach($trail->reviews->take(5) as $review)
                                        <div class="border-b border-gray-200 pb-6 last:border-b-0">
                                            <div class="flex items-start space-x-4">
                                                <img src="{{ $review->user->profile_photo_url }}" alt="{{ $review->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-2">
                                                            <h5 class="font-medium text-gray-900">{{ $review->user->name }}</h5>
                                                            <div class="flex items-center">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292c.3.921-.755 1.688-1.54 1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                    </svg>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                        <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-gray-700 mt-2">{{ $review->review }}</p>
                                                    
                                                                                                         <!-- Review Images -->
                                                     @if($review->review_images && count($review->review_images) > 0)
                                                         <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2">
                                                             @foreach($review->review_images as $image)
                                                                 <div class="relative group">
                                                        <img src="{{ asset('storage/' . $image['path']) }}" 
                                                            alt="Review photo" 
                                                            class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity review-image"
                                                            data-image-src="{{ asset('storage/' . $image['path']) }}"
                                                            data-image-caption="{{ $review->user->name }}">
                                                                 </div>
                                                             @endforeach
                                                         </div>
                                                     @endif
                                                    
                                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                                        <span>Hiked on {{ $review->hike_date->format('M d, Y') }}</span>
                                                        @if($review->conditions && count($review->conditions) > 0)
                                                            <span>Conditions: {{ implode(', ', $review->conditions) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($trail->reviews->count() > 5)
                                        <div class="text-center pt-4">
                                            <p class="text-sm text-gray-500">Showing 5 of {{ $trail->reviews->count() }} reviews</p>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8s-9-3.582-9-8 4.03-8 9-8 9 3.582 9 8z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No reviews yet</h3>
                                        <p class="mt-1 text-sm text-gray-500">Be the first to share your experience on this trail.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                        <button class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                            Book This Trail
                        </button>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                            Download GPX
                        </button>
                        <a href="{{ route('explore') }}?location={{ $trail->location->slug }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-6 rounded-lg font-medium transition-colors text-center">
                            Explore More Trails
                        </a>
                    </div>
                </div>

                <!-- Additional Images Section -->
                @php
                    $imageService = app(App\Services\TrailImageService::class);
                    $allImages = $imageService->getTrailImages($trail, 10);
                @endphp
                
                @if(count($allImages) > 1)
                    <div class="border-t border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Trail Photos ({{ count($allImages) }})</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($allImages as $index => $image)
                                <button data-index="{{ $index }}"
                                        class="aspect-square rounded-lg overflow-hidden hover:opacity-75 transition-opacity group gallery-thumb">
                                    <img src="{{ $image['url'] }}" 
                                         alt="{{ $image['caption'] }}"
                                         class="w-full h-full object-cover">
                                    
                                    <!-- Image source badge -->
                                    @if($image['source'] !== 'organization')
                                        <div class="absolute bottom-1 right-1 bg-black/70 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                            {{ ucfirst($image['source']) }}
                                        </div>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

<script>
    // Server-provided constants (moved up so earlier scripts can use them)
    (function(){
        const _serverDataEl = document.getElementById('server-data');
        const _serverData = _serverDataEl ? _serverDataEl.dataset : {};
        // Expose as globals for older inline scripts that run before the later block
        window.API_REVIEWS_STORE = _serverData.apiReviewsStore || null;
        window.CSRF_TOKEN = _serverData.csrf || '';
        window.FAVORITE_COUNT = parseInt(_serverData.favoriteCount || 0, 10);
        window.SAVED_TRAILS_ROUTE = _serverData.savedTrailsRoute || '/profile/saved-trails';
        window.TRAIL_NAME = _serverData.trailName || '';
        window.COMMUNITY_UNFOLLOW_ROUTE = _serverData.communityUnfollow || null;
        window.COMMUNITY_FOLLOW_ROUTE = _serverData.communityFollow || null;
        window.ELEVATION_PROFILE_ROUTE = _serverData.elevationProfileRoute || null;
        window.BASE_URL = _serverData.baseUrl || '';
    })();

    document.addEventListener('DOMContentLoaded', function() {
        const favBtn = document.getElementById('favorite-btn');
        if (!favBtn) return;

        const trailId = favBtn.dataset.trailId;
        const favText = document.getElementById('favorite-text');
        const favIcon = document.getElementById('favorite-icon');
        const favCount = document.getElementById('favorite-count');

        // Determine initial state for authenticated user
        let isFavorited = false;

        // Helper to update UI
        function updateFavoriteUI(state, count){
            isFavorited = state;
            if(state){
                favBtn.classList.remove('bg-emerald-600');
                favBtn.classList.add('bg-gray-200');
                favBtn.classList.remove('text-white');
                favBtn.classList.add('text-gray-800');
                favText.textContent = 'Saved';
                favIcon.classList.add('text-rose-500');
            } else {
                favBtn.classList.remove('bg-gray-200');
                favBtn.classList.add('bg-emerald-600');
                favBtn.classList.remove('text-gray-800');
                favBtn.classList.add('text-white');
                favText.textContent = 'Save';
                favIcon.classList.remove('text-rose-500');
            }
            if(typeof count !== 'undefined') favCount.textContent = `(${count})`;
        }

        // Prefer session-based check first (works for users logged in via session)
        fetch(`/trails/${trailId}/is-favorited`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data && data.success) {
                    updateFavoriteUI(!!data.is_favorited, FAVORITE_COUNT);
                } else {
                    // Fallback to checking API favorites (may require token)
                    return fetch(`/api/trails/favorites`, { credentials: 'same-origin' })
                        .then(r => r.json())
                        .then(data => {
                            if(data.success && data.data){
                                const items = data.data.data || data.data;
                                const found = items.find(it => parseInt(it.id) === parseInt(trailId));
                                updateFavoriteUI(!!found, FAVORITE_COUNT);
                            }
                        })
                        .catch(() => {});
                }
            })
            .catch(() => {
                // Ignore errors; leave default UI
            });

        favBtn.addEventListener('click', function(){
            favBtn.disabled = true;
            const payload = { trail_id: trailId };

            const doRequest = (url, isApi = false) => {
                return fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: isApi ? {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    } : {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: isApi ? JSON.stringify(payload) : new URLSearchParams(payload)
                });
            };

            // Try API (sanctum token) first
            doRequest('/api/trails/favorite/toggle', true)
            .then(response => {
                if(response.status === 401){
                    // Fallback to web route using session auth
                    return doRequest('/trails/favorite/toggle', false);
                }
                return response;
            })
            .then(r => r.json())
            .then(data => {
                if(data && data.success){
                    updateFavoriteUI(data.is_favorited, data.count);
                    // Debug log before showing toast
                    console.debug('Favorite toggle response', data);
                    // Show rich success toast with trail name and link
                    showToast('success', data.message || (data.is_favorited ? ('Saved "' + TRAIL_NAME + '"') : 'Removed from saved trails'), {
                        details: data.is_favorited ? ('Saved "' + TRAIL_NAME + '"') : ('Removed "' + TRAIL_NAME + '"'),
                        viewLink: SAVED_TRAILS_ROUTE
                    });
                } else if(data && data.message){
                    console.debug('Favorite toggle error response', data);
                    showToast('error', data.message);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('error', 'Unable to update favorites.');
            })
            .finally(() => { favBtn.disabled = false; });
        });
    });
</script>

    <!-- Success Toast -->
    <div id="success-toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-all duration-300 z-50" style="display:none; opacity:0;">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-1">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <div id="success-message" class="font-medium"></div>
                <div id="success-details" class="text-sm opacity-90 mt-1"></div>
            </div>
            <div class="ml-4 flex items-center">
                <a id="success-view-link" href="{{ route('profile.saved-trails') }}" class="text-sm underline">View Saved Trails &raquo;</a>
            </div>
        </div>
    </div>

    <!-- Error Toast -->
    <div id="error-toast" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-all duration-300 z-50" style="display:none; opacity:0;">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 mt-1">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <div id="error-message" class="font-medium"></div>
            </div>
        </div>
    </div>
 
     <!-- Image Modal -->
     <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden">
         <div class="flex items-center justify-center min-h-screen p-4">
             <div class="relative max-w-4xl max-h-full">
                 <img id="modal-image" src="" alt="Review photo" class="max-w-full max-h-full object-contain rounded-lg">
                 <div class="absolute top-4 right-4">
                     <button onclick="closeImageModal()" class="bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75 transition-all">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                         </svg>
                     </button>
                 </div>
                 <div class="absolute bottom-4 left-4 bg-black bg-opacity-50 text-white px-3 py-2 rounded-lg">
                     <p id="modal-caption" class="text-sm font-medium"></p>
                 </div>
             </div>
         </div>
     </div>

    @php
        // Prepare gallery image arrays for JS
        $imageService = app(App\Services\TrailImageService::class);
        $allImages = $imageService->getTrailImages($trail, 10);
        $imageUrls = [];
        $imageCaptions = [];
        foreach ($allImages as $image) {
            $imageUrls[] = $image['url'];
            $imageCaptions[] = $image['caption'] ?? $trail->trail_name;
        }
    @endphp

    {{-- Server-provided data for JS (use data-* to avoid Blade-in-JS parsing) --}}
    <div id="server-data" style="display:none;"
        data-saved-trails-route="{{ route('profile.saved-trails') }}"
        data-trail-name="{{ $trail->trail_name }}"
        data-api-reviews-store="{{ route('api.trails.reviews.store') }}"
        data-community-unfollow="{{ route('api.community.unfollow') }}"
        data-community-follow="{{ route('api.community.follow') }}"
        data-elevation-profile-route="{{ route('trails.elevation-profile', $trail) }}"
        data-base-url="{{ url('/') }}"
        data-csrf="{{ csrf_token() }}"
        data-favorite-count="{{ $trail->favoritedBy()->count() }}"></div>

    <script id="trail-images" type="application/json">{!! json_encode($imageUrls) !!}</script>
    <script id="image-captions" type="application/json">{!! json_encode($imageCaptions) !!}</script>
    <script id="trail-coordinates" type="application/json">{!! json_encode($trail->coordinates ?? []) !!}</script>

    <script>
        // Trail Gallery Component
        function trailGallery() {
            return {
                images: [],
                currentIndex: 0,
                currentImage: null,
                
                init() {
                    // Load trail images from server
                    this.loadImages();
                },
                
                loadImages() {
                    // Read precomputed JSON blobs injected by the server
                    const imagesScript = document.getElementById('trail-images');
                    const captionsScript = document.getElementById('image-captions');
                    const trailImages = imagesScript ? JSON.parse(imagesScript.textContent || '[]') : [];
                    const imageCaptions = captionsScript ? JSON.parse(captionsScript.textContent || '[]') : [];
                    
                    this.images = trailImages.filter(img => img);
                    this.captions = imageCaptions;
                    
                    if (this.images.length > 0) {
                        this.currentImage = this.images[0];
                        this.currentIndex = 0;
                    }
                    
                    // Store globally for thumbnail access
                    window.trailGalleryComponent = this;
                },
                
                nextImage() {
                    if (this.images.length === 0) return;
                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                    this.currentImage = this.images[this.currentIndex];
                },
                
                previousImage() {
                    if (this.images.length === 0) return;
                    this.currentIndex = this.currentIndex === 0 ? this.images.length - 1 : this.currentIndex - 1;
                    this.currentImage = this.images[this.currentIndex];
                },
                
                setImage(index) {
                    if (index >= 0 && index < this.images.length) {
                        this.currentIndex = index;
                        this.currentImage = this.images[index];
                    }
                }
            }
        }

        // Community & Review Functionality
        document.addEventListener('DOMContentLoaded', function() {
            let selectedRating = 0;

            // Initialize components
            initializeReviewForm();
            initializeFollowButton();
            initializeImageUploads();

            // Review Form Initialization
            function initializeReviewForm() {
                const reviewForm = document.getElementById('review-form');
                const ratingStars = document.querySelectorAll('.rating-star');
                const ratingInput = document.getElementById('rating-input');
                const reviewTextarea = document.getElementById('review');
                const charCounter = document.getElementById('review-char-count');

                // Rating stars functionality
                ratingStars.forEach((star, index) => {
                    star.addEventListener('click', function() {
                        selectedRating = index + 1;
                        ratingInput.value = selectedRating;
                        updateStarDisplay(selectedRating);
                    });

                    star.addEventListener('mouseenter', function() {
                        updateStarDisplay(index + 1);
                    });
                });

                // Reset stars on mouse leave
                document.getElementById('rating-stars').addEventListener('mouseleave', function() {
                    updateStarDisplay(selectedRating);
                });

                // Character counter
                if (reviewTextarea && charCounter) {
                    reviewTextarea.addEventListener('input', function() {
                        const count = this.value.length;
                        charCounter.textContent = count + '/1000';
                    });
                }

                // Form submission
                if (reviewForm) {
                    reviewForm.addEventListener('submit', handleReviewSubmission);
                }
            }

            function updateStarDisplay(rating) {
                const stars = document.querySelectorAll('.rating-star');
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
            }

            // Follow Button Functionality
            function initializeFollowButton() {
                const followBtn = document.getElementById('follow-org-btn');
                if (followBtn) {
                    followBtn.addEventListener('click', handleFollowClick);
                }
            }

            function handleFollowClick(e) {
                const button = e.currentTarget;
                const organizationId = button.dataset.organizationId;
                const organizationName = button.dataset.organizationName;
                const isFollowing = button.textContent.trim().includes('Following');
                
                // Disable button during request
                button.disabled = true;
                
                const url = isFollowing ? COMMUNITY_UNFOLLOW_ROUTE : COMMUNITY_FOLLOW_ROUTE;
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        organization_id: organizationId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateFollowButton(button, data.is_following);
                        showToast('success', data.message);
                        
                        // Refresh page to update review form visibility
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('error', data.message || 'An error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred. Please try again.');
                })
                .finally(() => {
                    button.disabled = false;
                });
            }

            function updateFollowButton(button, isFollowing) {
                if (isFollowing) {
                    button.className = 'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300';
                    button.innerHTML = `
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Following
                        </span>
                    `;
                } else {
                    button.className = 'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-emerald-600 text-white hover:bg-emerald-700';
                    button.innerHTML = `
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            Follow
                        </span>
                    `;
                }
            }

            // Review Submission
            function handleReviewSubmission(e) {
                e.preventDefault();
                
                const form = e.target;
                const formData = new FormData(form);
                const submitBtn = document.getElementById('submit-review-btn');
                
                // Get selected conditions
                const conditions = Array.from(form.querySelectorAll('input[name="conditions[]"]:checked')).map(cb => cb.value);
                
                // Clear previous conditions and add new ones
                formData.delete('conditions[]');
                conditions.forEach(condition => {
                    formData.append('conditions[]', condition);
                });
                
                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
                
                fetch(API_REVIEWS_STORE, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: formData // Use FormData for file uploads
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message);
                        
                        // Show moderation feedback if content was flagged
                        if (data.moderation && !data.moderation.approved) {
                            showToast('error', 'Content flagged for moderation: ' + data.moderation.feedback.join(', '));
                        }
                        
                        // Refresh page to show updated reviews and "already reviewed" message
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('error', data.message || 'An error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred while submitting your review');
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Review';
                });
            }

            window.showToast = function showToast(type, message, opts = {}) {
                try {
                    const toast = document.getElementById(type + '-toast');
                    if (!toast) {
                        console.debug('showToast: no toast element for type', type);
                        return;
                    }

                    // elements
                    const messageEl = toast.querySelector('#' + type + '-message');
                    const detailsEl = toast.querySelector('#' + (type === 'success' ? 'success-details' : 'error-details'));
                    const viewLink = document.getElementById('success-view-link');

                    // set contents (allow plain text or HTML)
                    if (messageEl) messageEl.textContent = message || '';
                    if (opts.detailsHtml && detailsEl) {
                        detailsEl.innerHTML = opts.detailsHtml;
                    } else if (opts.details && detailsEl) {
                        detailsEl.textContent = opts.details;
                    }
                    if (opts.viewLink && viewLink) {
                        viewLink.href = opts.viewLink;
                        // ensure link is visible
                        viewLink.style.display = '';
                    }

                    // force styles to ensure visibility
                    toast.style.display = 'block';
                    toast.style.zIndex = opts.zIndex || 99999;
                    toast.style.pointerEvents = 'auto';

                    // Remove translate and set transform directly to avoid class timing issues
                    requestAnimationFrame(() => {
                        toast.style.opacity = '1';
                        toast.style.transform = 'translateX(0)';
                        toast.classList.remove('translate-x-full');
                    });

                    // hide after duration with proper cleanup so no sliver remains
                    const hide = () => {
                        // fade & slide out
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateX(100%)';
                        toast.classList.add('translate-x-full');
                        // remove display after transition (duration ~300ms)
                        setTimeout(() => {
                            try { toast.style.display = 'none'; } catch(e){}
                        }, (opts.transitionMs || 300) + 50);
                    };

                    // clear any existing timer
                    if (toast._hideTimer) clearTimeout(toast._hideTimer);
                    toast._hideTimer = setTimeout(hide, opts.duration || 3000);
                    console.debug('showToast shown', type, message, opts);
                } catch (err) {
                    console.error('showToast error', err);
                }
            }

            // Image Modal Functionality
            function openImageModal(imageUrl, caption) {
                const modalImage = document.getElementById('modal-image');
                const modalCaption = document.getElementById('modal-caption');
                modalImage.src = imageUrl;
                modalCaption.textContent = caption;
                document.getElementById('image-modal').classList.remove('hidden');
            }

            function closeImageModal() {
                document.getElementById('image-modal').classList.add('hidden');
            }

            // Image Upload Functionality
            function initializeImageUploads() {
                const imageUploadSlots = document.querySelectorAll('.image-upload-slot');
                const imageInputs = document.querySelectorAll('.image-input');
                const imagePreviews = document.querySelectorAll('.image-preview');
                const removeImageButtons = document.querySelectorAll('.remove-image');

                imageUploadSlots.forEach((slot, index) => {
                    slot.addEventListener('click', function() {
                        const input = this.querySelector('input[type="file"]');
                        input.click();
                    });
                });

                imageInputs.forEach((input, index) => {
                    input.addEventListener('change', function(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const preview = imagePreviews[index];
                                preview.querySelector('img').src = e.target.result;
                                preview.classList.remove('hidden');
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                });

                removeImageButtons.forEach((button, index) => {
                    button.addEventListener('click', function() {
                        const preview = imagePreviews[index];
                        preview.querySelector('img').src = ''; // Clear image source
                        preview.classList.add('hidden');
                        const input = imageInputs[index];
                        input.value = ''; // Clear file input value
                    });
                });
            }

            // Content Filtering
            function checkContent(text) {
                if (text.length < 10) {
                    document.getElementById('content-filter-preview').classList.add('hidden');
                    return;
                }

                // Simple client-side content checking (basic version)
                const explicitWords = ['fuck', 'shit', 'bitch', 'asshole', 'dick', 'pussy', 'cock', 'cunt', 'bastard', 'whore', 'slut'];
                const trailKeywords = ['hiking', 'trail', 'mountain', 'nature', 'outdoor', 'adventure', 'camping', 'forest', 'peak', 'summit', 'climb', 'trek', 'scenery', 'view', 'landscape', 'wildlife', 'plants', 'trees', 'waterfall', 'river', 'lake', 'stream', 'path', 'route', 'difficulty', 'elevation', 'distance', 'duration', 'weather', 'equipment', 'gear', 'boots', 'backpack', 'tent', 'map', 'safety', 'first aid', 'emergency', 'rescue', 'guide', 'experience', 'challenge', 'achievement', 'memories', 'friends', 'family', 'group', 'solo', 'guided', 'tour', 'package', 'booking', 'reservation', 'cost', 'price', 'fee', 'payment', 'transportation', 'accommodation', 'food', 'water', 'supplies', 'photography', 'camera', 'pictures', 'videos', 'documentation', 'conservation', 'environment', 'sustainability', 'leave no trace', 'wilderness', 'backcountry', 'remote', 'isolated', 'peaceful', 'quiet', 'serene', 'beautiful', 'amazing', 'incredible', 'wonderful', 'exhilarating', 'challenging', 'rewarding', 'fulfilling', 'satisfying', 'memorable', 'unforgettable', 'life-changing', 'transformative', 'spiritual', 'meditative', 'reflective', 'contemplative', 'mindful', 'physical', 'exercise', 'fitness', 'health', 'wellness', 'mental health', 'stress relief', 'relaxation', 'recreation', 'leisure', 'hobby', 'passion', 'interest', 'enthusiasm', 'excitement', 'anticipation', 'preparation', 'planning', 'research', 'information', 'knowledge', 'skills', 'experience', 'expertise', 'proficiency', 'competence', 'confidence', 'courage', 'determination', 'perseverance', 'resilience', 'teamwork', 'cooperation', 'support', 'encouragement', 'motivation', 'inspiration', 'role model', 'mentor', 'leader', 'guide', 'teacher', 'learning', 'education', 'training', 'workshop', 'seminar', 'class', 'certification', 'qualification', 'accreditation', 'recognition', 'achievement', 'accomplishment', 'success', 'victory', 'triumph', 'celebration', 'commemoration', 'remembrance', 'honor', 'respect', 'gratitude', 'appreciation', 'thankfulness', 'blessing', 'gift', 'opportunity', 'privilege', 'advantage', 'benefit', 'value', 'meaning', 'purpose', 'significance', 'importance', 'relevance', 'connection', 'relationship', 'bond', 'friendship', 'camaraderie', 'community', 'society', 'culture', 'heritage', 'tradition', 'custom', 'history', 'past', 'present', 'future', 'legacy', 'impact', 'influence', 'contribution', 'participation', 'involvement', 'engagement', 'commitment', 'dedication', 'devotion', 'loyalty', 'faithfulness', 'reliability', 'dependability', 'trustworthiness', 'honesty', 'integrity', 'ethics', 'morals', 'values', 'principles', 'standards', 'guidelines', 'rules', 'regulations', 'policies', 'procedures', 'protocols', 'safety', 'security', 'protection', 'prevention', 'avoidance', 'risk', 'danger', 'hazard', 'threat', 'challenge', 'obstacle', 'difficulty', 'problem', 'issue', 'concern', 'worry', 'anxiety', 'fear', 'nervousness', 'tension', 'pressure', 'stress', 'strain', 'fatigue', 'exhaustion', 'tiredness', 'weakness', 'soreness', 'pain', 'injury', 'illness', 'sickness', 'disease', 'condition', 'symptom', 'treatment', 'medicine', 'medication', 'therapy', 'rehabilitation', 'recovery', 'healing', 'improvement', 'progress', 'development', 'growth', 'advancement', 'enhancement', 'upgrade', 'improvement', 'betterment', 'refinement', 'polish', 'perfection', 'excellence', 'quality', 'standard', 'level', 'grade', 'rank', 'position', 'status', 'reputation', 'image', 'appearance', 'look', 'style', 'fashion', 'trend', 'popular', 'famous', 'well-known', 'recognized', 'acknowledged', 'accepted', 'approved', 'endorsed', 'recommended', 'suggested', 'proposed', 'offered', 'provided', 'supplied', 'given', 'shared', 'distributed', 'spread', 'circulated', 'disseminated', 'communicated', 'conveyed', 'expressed', 'stated', 'declared', 'announced', 'proclaimed', 'publicized', 'advertised', 'promoted', 'marketed', 'sold', 'bought', 'purchased', 'acquired', 'obtained', 'gained', 'earned', 'won', 'achieved', 'accomplished', 'completed', 'finished', 'ended', 'concluded', 'terminated', 'stopped', 'halted', 'paused', 'suspended', 'interrupted', 'disrupted', 'disturbed', 'bothered', 'annoyed', 'irritated', 'frustrated', 'angry', 'mad', 'upset', 'disappointed', 'sad', 'depressed', 'miserable', 'unhappy', 'dissatisfied', 'displeased', 'discontent', 'uncomfortable', 'uneasy', 'nervous', 'anxious', 'worried', 'concerned', 'troubled', 'distressed', 'agitated', 'excited', 'thrilled', 'delighted', 'pleased', 'happy', 'joyful', 'cheerful', 'glad', 'satisfied', 'content', 'pleased', 'grateful', 'thankful', 'appreciative', 'blessed', 'fortunate', 'lucky', 'privileged', 'advantaged', 'benefited', 'valued', 'cherished', 'treasured', 'prized', 'valued', 'esteemed', 'respected', 'admired', 'appreciated', 'loved', 'cared for', 'supported', 'encouraged', 'motivated', 'inspired', 'influenced', 'affected', 'changed', 'transformed', 'modified', 'altered', 'adjusted', 'adapted', 'accommodated', 'fitted', 'suited', 'matched', 'compatible', 'suitable', 'appropriate', 'proper', 'correct', 'right', 'good', 'excellent', 'outstanding', 'superior', 'exceptional', 'extraordinary', 'remarkable', 'notable', 'significant', 'important', 'essential', 'necessary', 'required', 'needed', 'wanted', 'desired', 'preferred', 'chosen', 'selected', 'picked', 'elected', 'voted', 'decided', 'determined', 'resolved', 'settled', 'agreed', 'consented', 'approved', 'authorized', 'permitted', 'allowed', 'enabled', 'empowered', 'capable', 'able', 'competent', 'qualified', 'skilled', 'talented', 'gifted', 'intelligent', 'smart', 'clever', 'wise', 'knowledgeable', 'educated', 'informed', 'aware', 'conscious', 'mindful', 'attentive', 'careful', 'cautious', 'prudent', 'sensible', 'reasonable', 'rational', 'logical', 'sensible', 'practical', 'realistic', 'achievable', 'attainable', 'reachable', 'accessible', 'available', 'obtainable', 'acquirable', 'gettable', 'procurable', 'securable', 'attainable', 'achievable', 'accomplishable', 'doable', 'feasible', 'possible', 'practical', 'realistic', 'reasonable', 'sensible', 'logical', 'rational', 'intelligent', 'smart', 'clever', 'wise', 'knowledgeable', 'educated', 'informed', 'aware', 'conscious', 'mindful', 'attentive', 'careful', 'cautious', 'prudent', 'sensible', 'reasonable', 'rational', 'logical', 'sensible', 'practical', 'realistic', 'achievable', 'attainable', 'reachable', 'accessible', 'available', 'obtainable', 'acquirable', 'gettable', 'procurable', 'securable', 'attainable', 'achievable', 'accomplishable', 'doable', 'feasible', 'possible', 'practical', 'realistic', 'reasonable', 'sensible', 'logical', 'rational'];

                let score = 100;
                let feedback = [];

                // Check for explicit content
                const lowerText = text.toLowerCase();
                explicitWords.forEach(word => {
                    if (lowerText.includes(word)) {
                        score -= 50;
                        feedback.push('Contains inappropriate language');
                        return;
                    }
                });

                // Check for trail relevance
                let relevantCount = 0;
                trailKeywords.forEach(keyword => {
                    if (lowerText.includes(keyword)) {
                        relevantCount++;
                    }
                });

                const relevancePercentage = (relevantCount / trailKeywords.length) * 100;
                if (relevancePercentage < 20) {
                    score -= 30;
                    feedback.push('Content may not be relevant to hiking');
                }

                // Check for excessive length
                if (text.length > 1000) {
                    score -= 20;
                    feedback.push('Content is very long');
                }

                // Check for repetitive content
                const words = text.toLowerCase().split(/\s+/);
                const wordCounts = {};
                words.forEach(word => {
                    if (word.length > 3) {
                        wordCounts[word] = (wordCounts[word] || 0) + 1;
                    }
                });

                Object.values(wordCounts).forEach(count => {
                    if (count > 5) {
                        score -= 25;
                        feedback.push('Content appears repetitive');
                        return;
                    }
                });

                score = Math.max(0, score);

                // Update UI
                const preview = document.getElementById('content-filter-preview');
                const scoreLabel = document.getElementById('content-score-label');
                const scoreElement = document.getElementById('content-score');
                const feedbackElement = document.getElementById('content-feedback');

                preview.classList.remove('hidden');

                if (score >= 80) {
                    preview.className = 'mt-2 text-xs p-2 rounded-lg bg-green-100 border border-green-200';
                    scoreLabel.textContent = 'Content Score:';
                    scoreElement.textContent = score + '/100';
                    scoreElement.className = 'text-lg font-bold text-green-600';
                } else if (score >= 60) {
                    preview.className = 'mt-2 text-xs p-2 rounded-lg bg-yellow-100 border border-yellow-200';
                    scoreLabel.textContent = 'Content Score:';
                    scoreElement.textContent = score + '/100';
                    scoreElement.className = 'text-lg font-bold text-yellow-600';
                } else {
                    preview.className = 'mt-2 text-xs p-2 rounded-lg bg-red-100 border border-red-200';
                    scoreLabel.textContent = 'Content Score:';
                    scoreElement.textContent = score + '/100';
                    scoreElement.className = 'text-lg font-bold text-red-600';
                }

                feedbackElement.textContent = feedback.length > 0 ? feedback.join(', ') : 'Content looks good!';
            }
        });

    // Interactive Trail Map and Tracking Features
        let map, trailPath, userMarker, userLocation;
        let isTracking = false;
        let watchId = null;
    const trailCoordinates = JSON.parse(document.getElementById('trail-coordinates').textContent || '[]');

    // server data
    const _serverDataEl = document.getElementById('server-data');
    const _serverData = _serverDataEl ? _serverDataEl.dataset : {};
    const API_REVIEWS_STORE = _serverData.apiReviewsStore || null;
    const CSRF_TOKEN = _serverData.csrf || '';
    const FAVORITE_COUNT = parseInt(_serverData.favoriteCount || 0, 10);
    const SAVED_TRAILS_ROUTE = _serverData.savedTrailsRoute || '/profile/saved-trails';
    const TRAIL_NAME = _serverData.trailName || '';
    const COMMUNITY_UNFOLLOW_ROUTE = _serverData.communityUnfollow || null;
    const COMMUNITY_FOLLOW_ROUTE = _serverData.communityFollow || null;
    const ELEVATION_PROFILE_ROUTE = _serverData.elevationProfileRoute || null;
    const BASE_URL = _serverData.baseUrl || '';

        // Initialize Google Maps
        function initTrailMap() {
            if (!trailCoordinates || trailCoordinates.length === 0) return;

            // Calculate bounds for the trail
            const bounds = new google.maps.LatLngBounds();
            trailCoordinates.forEach(coord => {
                bounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
            });

            // Initialize map
            map = new google.maps.Map(document.getElementById('interactive-trail-map'), {
                zoom: 13,
                center: bounds.getCenter(),
                mapTypeId: 'terrain',
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: true
            });

            // Fit map to trail bounds
            map.fitBounds(bounds);

            // Create trail path
            trailPath = new google.maps.Polyline({
                path: trailCoordinates,
                geodesic: true,
                strokeColor: '#ff0000',
                strokeOpacity: 1.0,
                strokeWeight: 4
            });
            trailPath.setMap(map);

            // Add start marker
            new google.maps.Marker({
                position: trailCoordinates[0],
                map: map,
                title: 'Trail Start',
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                    scaledSize: new google.maps.Size(32, 32)
                }
            });

            // Add end marker
            new google.maps.Marker({
                position: trailCoordinates[trailCoordinates.length - 1],
                map: map,
                title: 'Trail End',
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(32, 32)
                }
            });

            // Add waypoint markers for every 10th coordinate
            for (let i = 10; i < trailCoordinates.length - 1; i += 10) {
                new google.maps.Marker({
                    position: trailCoordinates[i],
                    map: map,
                    title: `Waypoint ${Math.floor(i / 10)}`,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                        scaledSize: new google.maps.Size(20, 20)
                    }
                });
            }
        }

        // Start location tracking
        function startTracking() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by this browser.');
                return;
            }

            isTracking = true;
            document.getElementById('tracking-status').classList.remove('hidden');
            document.getElementById('start-tracking').disabled = true;
            document.getElementById('start-tracking').innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" class="opacity-75"></path></svg>Tracking...';

            watchId = navigator.geolocation.watchPosition(
                updateUserLocation,
                handleLocationError,
                {
                    enableHighAccuracy: true,
                    maximumAge: 30000,
                    timeout: 27000
                }
            );
        }

        // Update user location
        function updateUserLocation(position) {
            userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            // Update or create user marker
            if (userMarker) {
                userMarker.setPosition(userLocation);
            } else {
                userMarker = new google.maps.Marker({
                    position: userLocation,
                    map: map,
                    title: 'Your Location',
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                        scaledSize: new google.maps.Size(24, 24)
                    }
                });
            }

            // Calculate distance from trail and progress
            calculateTrailProgress();
        }

        // Calculate trail progress and distance
        function calculateTrailProgress() {
            if (!userLocation || !trailCoordinates.length) return;

            let minDistance = Infinity;
            let closestPointIndex = 0;

            // Find closest point on trail
            trailCoordinates.forEach((coord, index) => {
                const distance = calculateDistance(userLocation, coord);
                if (distance < minDistance) {
                    minDistance = distance;
                    closestPointIndex = index;
                }
            });

            // Update UI
            const distanceText = minDistance < 1 ? 
                Math.round(minDistance * 1000) + 'm' : 
                minDistance.toFixed(1) + 'km';
            
            const progress = Math.round((closestPointIndex / (trailCoordinates.length - 1)) * 100);

            document.getElementById('distance-from-trail').textContent = `Distance from trail: ${distanceText}`;
            document.getElementById('progress-percentage').textContent = `Progress: ${progress}%`;
        }

        // Calculate distance between two coordinates (Haversine formula)
        function calculateDistance(pos1, pos2) {
            const R = 6371; // Earth's radius in kilometers
            const dLat = toRad(pos2.lat - pos1.lat);
            const dLon = toRad(pos2.lng - pos1.lng);
            const lat1 = toRad(pos1.lat);
            const lat2 = toRad(pos2.lat);

            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            
            return R * c;
        }

        function toRad(degrees) {
            return degrees * (Math.PI/180);
        }

        // Handle location errors
        function handleLocationError(error) {
            let message = 'Unknown error occurred.';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message = 'Location access denied by user.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = 'Location information is unavailable.';
                    break;
                case error.TIMEOUT:
                    message = 'Location request timed out.';
                    break;
            }
            alert('Geolocation error: ' + message);
            stopTracking();
        }

        // Stop location tracking
        function stopTracking() {
            isTracking = false;
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            
            document.getElementById('tracking-status').classList.add('hidden');
            document.getElementById('start-tracking').disabled = false;
            document.getElementById('start-tracking').innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Start Tracking';
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map when page loads
            if (document.getElementById('interactive-trail-map')) {
                initTrailMap();
            }

            // Load elevation profile
            loadElevationProfile();

            // Tracking buttons
            document.getElementById('start-tracking').addEventListener('click', startTracking);
            document.getElementById('stop-tracking').addEventListener('click', stopTracking);
        });

        // gallery thumbnail click handling (avoid inline onclick with Blade)
        document.querySelectorAll('.gallery-thumb').forEach(btn => {
            btn.addEventListener('click', function(e){
                const idx = this.getAttribute('data-index');
                if (window.trailGalleryComponent && typeof window.trailGalleryComponent.setImage === 'function') {
                    window.trailGalleryComponent.setImage(parseInt(idx, 10));
                }
            });
        });

        // review image modal handling
        document.querySelectorAll('.review-image').forEach(img => {
            img.addEventListener('click', function(){
                const src = this.dataset.imageSrc;
                const caption = this.dataset.imageCaption || '';
                openImageModal(src, caption);
            });
        });

        // Apply rating-fill widths from data attributes to avoid inline CSS parsing in Blade
        document.querySelectorAll('.rating-fill').forEach(el => {
            const pct = el.getAttribute('data-percentage') || '0';
            el.style.width = pct + '%';
        });

        // Load and display elevation profile using Chart.js
        function loadElevationProfile() {
            fetch(ELEVATION_PROFILE_ROUTE)
                .then(response => response.json())
                .then(data => {
                    if (data.elevations && data.elevations.length > 0) {
                        createElevationChart(data.elevations, data.trail_length);
                    }
                })
                .catch(error => {
                    console.error('Error loading elevation data:', error);
                });
        }

        // Create elevation chart using Chart.js
        function createElevationChart(elevations, trailLength) {
            const ctx = document.getElementById('elevation-chart');
            if (!ctx) return;

            // Prepare data
            const distances = elevations.map((_, index) => {
                return (index / (elevations.length - 1)) * (trailLength || 10);
            });

            const elevationData = elevations.map(point => Math.round(point.elevation));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: distances.map(d => d.toFixed(1) + 'km'),
                    datasets: [{
                        label: 'Elevation (m)',
                        data: elevationData,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Trail Elevation Profile'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Distance'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Elevation (m)'
                            }
                        }
                    },
                    elements: {
                        point: {
                            radius: 2
                        }
                    }
                }
            });
        }
    </script>

    <!-- Load Google Maps API -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initTrailMap&libraries=geometry"></script>

    <!-- Load Chart.js for elevation profile -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-app-layout>

<script>
    // Print helper: fetch the print map HTML and print it inside a hidden iframe
    function printTrailMapFromButton(btn){
        const id = btn.getAttribute('data-trail-id');
        if(!id) return;
        printTrailMap(id);
    }

    async function printTrailMap(trailId){
        try{
            // Prefer server-generated URL from button's data-print-url attribute
            let url = null;
            const btn = document.querySelector(`[data-trail-id="${trailId}"]`);
            if(btn && btn.dataset && btn.dataset.printUrl){
                url = btn.dataset.printUrl;
            }

            // Fallback to constructed URL if no data attribute provided
            if(!url){
                url = `${BASE_URL}/trails/` + trailId + '/print-map';
            }

            // Fetch the rendered print view (server should render same as route('trails.print-map', $trail))
            const res = await fetch(url, { credentials: 'same-origin' });
            if(!res.ok) throw new Error('Failed to load print view');
            const html = await res.text();

            // Create or reuse iframe
            let iframe = document.getElementById('print-iframe');
            if(!iframe){
                iframe = document.createElement('iframe');
                iframe.id = 'print-iframe';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                iframe.style.visibility = 'hidden';
                document.body.appendChild(iframe);
            }

            const iframeDoc = iframe.contentWindow || iframe.contentDocument;
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open();
            doc.write(html);
            doc.close();

            // Wait a tick for styles and images to load, but only print once.
            let printed = false;
            const doPrint = () => {
                if(printed) return;
                printed = true;
                try{
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                }catch(e){
                    console.error('Print failed', e);
                }
            };

            iframe.onload = function(){
                doPrint();
            };

            // Fallback: try print after short delay if onload didn't fire. Guarded by printed flag.
            setTimeout(()=>{
                doPrint();
            }, 700);

        }catch(err){
            console.error(err);
            alert('Unable to load print view.');
        }
    }
</script>
