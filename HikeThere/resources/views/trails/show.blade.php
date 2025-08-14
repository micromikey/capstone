<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $trail->trail_name }}
            </h2>
            
            <a href="{{ route('explore') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Explore
            </a>
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
                                <div class="text-3xl font-bold">₱{{ number_format($trail->price, 2) }}</div>
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
                            <div class="text-2xl font-bold text-gray-900">{{ $trail->duration }}</div>
                            <div class="text-sm text-gray-500">Duration</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $trail->estimated_time_formatted }}</div>
                            <div class="text-sm text-gray-500">Estimated Time</div>
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
                                        {{ ucfirst($trail->difficulty) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Best Season:</span>
                                    <span>{{ $trail->best_season }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Package Price:</span>
                                    <span class="font-semibold text-green-600">₱{{ number_format($trail->price, 2) }}</span>
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

                    <!-- Package Inclusions -->
                    @if($trail->package_inclusions)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Package Inclusions</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700 whitespace-pre-line">{{ $trail->package_inclusions }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Trail Location Map -->
                    @if($trail->location && $trail->location->latitude && $trail->location->longitude)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Trail Location</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700 mb-4">This trail is located at <strong>{{ $trail->location->name }}, {{ $trail->location->province }}</strong>. Use the map below to see the exact location and get directions.</p>
                                
                                <x-trail-map 
                                    height="400px"
                                    :showControls="true"
                                    :showSearch="false"
                                    :showFilters="false"
                                    :showActions="true"
                                    :centerLat="$trail->location->latitude"
                                    :centerLng="$trail->location->longitude"
                                    :zoom="12"
                                    id="trail-location-map" />
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
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
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
                                                                          class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                                                          onclick="openImageModal('{{ asset('storage/' . $image['path']) }}', '{{ $review->user->name }}')">
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
                @if($trail->images && $trail->images->count() > 1)
                    <div class="border-t border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Trail Photos ({{ $trail->images->count() }})</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($trail->images as $index => $image)
                                <button onclick="window.trailGalleryComponent.setImage({{ $index }})"
                                        class="aspect-square rounded-lg overflow-hidden hover:opacity-75 transition-opacity">
                                    <img src="{{ $image->url }}" 
                                         alt="{{ $image->caption }}"
                                         class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="success-toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span id="success-message"></span>
        </div>
    </div>

    <!-- Error Toast -->
    <div id="error-toast" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span id="error-message"></span>
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
                    const trailImages = [
                        @if($trail->images && $trail->images->count() > 0)
                            @foreach($trail->images as $image)
                                '{{ $image->url }}',
                            @endforeach
                        @else
                            @php
                                // Use the TrailImageService to get API images
                                $imageService = app(App\Services\TrailImageService::class);
                                $primaryImage = $imageService->getTrailImage($trail, 'primary', 'large');
                            @endphp
                            '{{ $primaryImage }}',
                        @endif
                    ];
                    
                    this.images = trailImages.filter(img => img);
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
                
                const url = isFollowing ? '{{ route("api.community.unfollow") }}' : '{{ route("api.community.follow") }}';
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                
                fetch('{{ route("api.trails.reviews.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
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

            function showToast(type, message) {
                const toast = document.getElementById(type + '-toast');
                const messageSpan = document.getElementById(type + '-message');
                
                messageSpan.textContent = message;
                toast.classList.remove('translate-x-full');
                
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                }, 3000);
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
    </script>
</x-app-layout>
