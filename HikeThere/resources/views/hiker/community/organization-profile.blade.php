<x-app-layout>
    @php
    // Initialize the TrailImageService for dynamic images
    $imageService = app('App\Services\TrailImageService');
    @endphp
    <div>
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Organization Profile') }}
                </h2>

                {{-- Search Bar --}}
                <form class="flex items-center max-w-2xl w-full relative" action="{{ route('trails.search') }}" method="GET">
                    <label for="trail-search" class="sr-only">Search Trails</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 21 21">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.15 5.6h.01m3.337 1.913h.01m-6.979 0h.01M5.541 11h.01M15 15h2.706a1.957 1.957 0 0 0 1.883-1.325A9 9 0 1 0 2.043 11.89 9.1 9.1 0 0 0 7.2 19.1a8.62 8.62 0 0 0 3.769.9A2.013 2.013 0 0 0 13 18v-.857A2.034 2.034 0 0 1 15 15Z" />
                            </svg>
                        </div>
                        <input type="text" id="header-search-input" name="q" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full ps-10 p-3" placeholder="Search trails, locations..." value="{{ request('q') }}" autocomplete="off" />
                        @include('partials.header-search-dropdown')
                    </div>
                    <button type="submit" class="inline-flex items-center py-2.5 px-3 ms-2 text-sm font-medium text-white bg-green-700 rounded-lg border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300">
                        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>Search
                    </button>
                </form>
            </div>
        </x-slot>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Organization Profile Section -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-10">
                <!-- Gradient Header -->
                <div class="h-40 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 relative">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-4 left-8 w-2 h-2 bg-white rounded-full animate-pulse"></div>
                        <div class="absolute top-8 right-16 w-1.5 h-1.5 bg-white rounded-full animate-pulse" style="animation-delay: 0.3s;"></div>
                        <div class="absolute bottom-6 left-1/4 w-2 h-2 bg-white rounded-full animate-pulse" style="animation-delay: 0.6s;"></div>
                    </div>
                </div>
                
                <div class="px-6 sm:px-10 pb-10">
                    <div class="-mt-20 mb-8 relative z-10">
                        <div class="flex flex-col sm:flex-row items-start gap-6">
                            <!-- Avatar -->
                            @if($organization->profile_picture)
                                <img src="{{ $organization->profile_picture_url }}"
                                    alt="{{ $organization->display_name }}"
                                    class="w-32 h-32 rounded-xl object-cover border-4 border-white shadow-xl flex-shrink-0">
                            @else
                                <div class="w-32 h-32 bg-gradient-to-br from-emerald-400 via-teal-500 to-cyan-600 rounded-xl flex items-center justify-center relative overflow-hidden border-4 border-white shadow-xl flex-shrink-0">
                                    <svg class="absolute inset-0 w-full h-full opacity-15" viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice">
                                        <path d="M0,200 L0,100 L100,40 L200,120 L300,20 L400,80 L400,200 Z" fill="white"/>
                                    </svg>
                                    <svg class="relative z-10 w-14 h-14 text-white drop-shadow-lg" viewBox="0 0 120 120" fill="none">
                                        <path d="M20 85 L45 45 L60 65 L75 35 L100 70 L100 85 Z" fill="white" opacity="0.3"/>
                                        <circle cx="60" cy="55" r="4" fill="white" opacity="0.95"/>
                                        <circle cx="42" cy="63" r="3.5" fill="white" opacity="0.9"/>
                                        <circle cx="78" cy="63" r="3.5" fill="white" opacity="0.9"/>
                                        <circle cx="60" cy="62" r="18" stroke="white" stroke-width="1.2" fill="none" opacity="0.5"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Content Section -->
                            <div class="flex-1 min-w-0">
                                <!-- Name & Badge -->
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <h1 class="text-3xl font-bold text-gray-900 sm:text-white">{{ $organization->display_name }}</h1>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-lime-500 text-white">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.238.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Verified
                                    </span>
                                </div>
                                
                                <!-- Bio -->
                                @if($organization->bio)
                                <p class="text-sm text-gray-900 sm:text-white mb-3 max-w-2xl">{{ $organization->bio }}</p>
                                @endif
                                
                                <!-- Stats & Location -->
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
                                    @php
                                        $totalRatings = 0;
                                        $sumRatings = 0;
                                        foreach ($trails as $trail) {
                                            if ($trail->reviews_count > 0) {
                                                $totalRatings += $trail->reviews_count;
                                                $sumRatings += $trail->reviews_avg_rating * $trail->reviews_count;
                                            }
                                        }
                                        $averageRating = $totalRatings > 0 ? round($sumRatings / $totalRatings, 1) : 0;
                                    @endphp
                                    <span class="font-semibold text-gray-900 sm:text-white inline-flex items-center">
                                        <svg class="w-5 h-5 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-yellow-500 sm:text-yellow-300">{{ $averageRating }}</span>
                                        <span class="text-gray-500 sm:text-white ml-1">({{ $totalRatings }})</span>
                                    </span>
                                    <span class="text-gray-400 sm:text-white">•</span>
                                    <span class="font-semibold text-gray-900 sm:text-white">
                                        <span class="text-emerald-600 sm:text-emerald-100" id="followers-count">{{ $organization->followers_count ?? 0 }}</span> Followers
                                    </span>
                                    <span class="text-gray-400 sm:text-white">•</span>
                                    <span class="font-semibold text-gray-900 sm:text-white">
                                        <span class="text-teal-600 sm:text-teal-100">{{ $trails->total() }}</span> Trails
                                    </span>
                                    @if($organization->location)
                                    <span class="text-gray-400 sm:text-white">•</span>
                                    <span class="inline-flex items-center text-gray-900 sm:text-gray-600">
                                        <svg class="w-4 h-4 mr-1 text-emerald-600 sm:text-emerald-100" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $organization->location }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Follow Button -->
                            <div class="flex-shrink-0 w-full sm:w-auto">
                                <button class="follow-btn w-full sm:w-auto px-8 py-2.5 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg
                                    {{ $isFollowing ? 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                                    data-organization-id="{{ $organization->id }}"
                                    data-organization-name="{{ $organization->display_name }}">
                                    @if($isFollowing)
                                    <span class="flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Following
                                    </span>
                                    @else
                                    <span class="flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Follow
                                    </span>
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- About Section -->
                    @if($organization->organizationProfile)
                    <div class="border-t border-gray-100 pt-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            About {{ $organization->display_name }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($organization->organizationProfile->description)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    Description
                                </h3>
                                <p class="text-sm text-gray-600">{{ $organization->organizationProfile->description }}</p>
                            </div>
                            @endif
                            @if($organization->organizationProfile->website)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                    </svg>
                                    Website
                                </h3>
                                <a href="{{ $organization->organizationProfile->website }}" target="_blank"
                                    class="text-sm text-emerald-600 hover:text-emerald-700 inline-flex items-center group">
                                    <span class="truncate">{{ $organization->organizationProfile->website }}</span>
                                    <svg class="w-3 h-3 ml-1 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                            @endif
                            @if($organization->organizationProfile->contact_person)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    Contact Person
                                </h3>
                                <p class="text-sm text-gray-600">{{ $organization->organizationProfile->contact_person }}</p>
                            </div>
                            @endif
                            @if($organization->organizationProfile->phone_number)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                    </svg>
                                    Phone
                                </h3>
                                <a href="tel:{{ $organization->organizationProfile->phone_number }}"
                                    class="text-sm text-emerald-600 hover:text-emerald-700">
                                    {{ $organization->organizationProfile->phone_number }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Reviews Section -->
            @if($reviews->count() > 0)
            <div class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        Reviews & Ratings
                    </h2>
                    <div class="bg-amber-100 text-amber-800 px-4 py-1.5 rounded-lg text-sm font-semibold">
                        {{ $reviews->total() }} Review{{ $reviews->total() !== 1 ? 's' : '' }}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($reviews as $review)
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                        <!-- Trail Info Header -->
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-4 py-3 border-b border-gray-100">
                            <a href="{{ route('trails.show', $review->trail->slug) }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 transition-colors flex items-center group">
                                <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="truncate group-hover:underline">{{ $review->trail->trail_name }}</span>
                            </a>
                        </div>

                        <div class="p-4">
                            <!-- Reviewer Info -->
                            <div class="flex items-center gap-3 mb-3">
                                @if($review->user->profile_picture)
                                    <img src="{{ $review->user->profile_picture_url }}" 
                                        alt="{{ $review->user->display_name }}"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-emerald-200">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-lg border-2 border-emerald-200">
                                        {{ strtoupper(substr($review->user->display_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 truncate">{{ $review->user->display_name }}</h4>
                                    <div class="flex items-center gap-2">
                                        <!-- Star Rating -->
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Review Content: Comment and Images Side by Side -->
                            <div class="flex gap-3">
                                <!-- Left: Review Comment -->
                                <div class="flex-1 min-w-0">
                                    @if($review->review)
                                        @php
                                            $reviewText = $review->review;
                                            $reviewLength = strlen($reviewText);
                                            $isLongReview = $reviewLength > 150;
                                            $shortReview = $isLongReview ? substr($reviewText, 0, 150) . '...' : $reviewText;
                                        @endphp
                                        <div class="review-comment-container">
                                            <p class="text-sm text-gray-700 leading-relaxed review-comment-short">{{ $shortReview }}</p>
                                            @if($isLongReview)
                                                <p class="text-sm text-gray-700 leading-relaxed review-comment-full hidden">{{ $reviewText }}</p>
                                                <button class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 mt-2 flex items-center gap-1 toggle-comment">
                                                    <span class="show-more-text">Show More</span>
                                                    <svg class="w-3 h-3 transition-transform show-more-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-400 italic">No comment provided</p>
                                    @endif
                                </div>

                                <!-- Right: Review Images -->
                                @php
                                    // Ensure review_images is an array
                                    $reviewImages = $review->review_images;
                                    if (is_string($reviewImages)) {
                                        $reviewImages = json_decode($reviewImages, true) ?? [];
                                    }
                                    $reviewImages = is_array($reviewImages) ? $reviewImages : [];
                                @endphp
                                
                                @if(!empty($reviewImages))
                                    @php
                                        $imageCount = count($reviewImages);
                                        $displayCount = min($imageCount, 4);
                                        // Dynamic grid classes based on image count - smaller sizes
                                        if ($imageCount == 1) {
                                            $gridClass = 'grid-cols-1 w-20'; // Single image - 80px
                                        } elseif ($imageCount == 2) {
                                            $gridClass = 'grid-cols-2 w-32'; // 2 images - 128px (64px each)
                                        } elseif ($imageCount == 3) {
                                            $gridClass = 'grid-cols-2 w-32'; // 3 images - 128px
                                        } else {
                                            $gridClass = 'grid-cols-2 w-32'; // 4+ images - 128px (64px each)
                                        }
                                    @endphp
                                    <div class="flex-shrink-0">
                                        <div class="grid {{ $gridClass }} gap-1">
                                            @foreach($reviewImages as $index => $image)
                                                @if($index < 4)
                                                    <div class="relative {{ $imageCount == 1 ? 'aspect-square' : ($imageCount == 2 ? 'aspect-square' : ($imageCount == 3 && $index == 2 ? 'col-span-2 aspect-video' : 'aspect-square')) }} rounded-md overflow-hidden group cursor-pointer bg-gray-100">
                                                        <img src="{{ asset('storage/' . $image['path']) }}" 
                                                            alt="Review image {{ $index + 1 }}"
                                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                            onerror="this.src='{{ asset('images/placeholder-trail.jpg') }}'">
                                                        @if($index === 3 && $imageCount > 4)
                                                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                                                <span class="text-white text-xs font-bold">+{{ $imageCount - 4 }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($reviews->total() >= 4)
                <div class="flex justify-center">
                    {{ $reviews->appends(['reviews_page' => request('reviews_page')])->links('pagination::tailwind') }}
                </div>
                @endif
            </div>
            @endif

            <!-- Trails Section -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                        </svg>
                        Trails by {{ $organization->display_name }}
                    </h2>
                    @if($trails->count() > 0)
                    <div class="bg-emerald-100 text-emerald-800 px-4 py-1.5 rounded-lg text-sm font-semibold">
                        {{ $trails->total() }} Trail{{ $trails->total() !== 1 ? 's' : '' }}
                    </div>
                    @endif
                </div>
            </div>

            @if($trails->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                @foreach($trails as $trail)
                <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-gray-100">
                    <div class="relative overflow-hidden">
                        @php
                        // Get dynamic image from TrailImageService
                        $trailImage = $imageService->getTrailImage($trail, 'primary', 'medium');
                        @endphp
                        <img src="{{ $trailImage }}"
                            alt="{{ $trail->trail_name }}"
                            class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110">
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold shadow-lg backdrop-blur-sm border border-white/20 {{ $trail->difficulty === 'easy' ? 'bg-green-500/90 text-white' : ($trail->difficulty === 'moderate' ? 'bg-yellow-500/90 text-white' : 'bg-red-500/90 text-white') }}">
                                {{ $trail->difficulty_label }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-3 truncate group-hover:text-emerald-600 transition-colors">
                            {{ $trail->trail_name }}
                        </h3>

                        <div class="flex items-center text-sm text-gray-600 mb-4 bg-gray-50 px-3 py-2 rounded-lg">
                            <svg class="w-4 h-4 mr-2 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="truncate">{{ $trail->location->name ?? 'Location not set' }}</span>
                        </div>

                        <div class="flex items-center justify-between text-sm mb-6 pb-6 border-b border-gray-100">
                            <div class="flex items-center bg-amber-50 px-3 py-1.5 rounded-lg">
                                <svg class="w-4 h-4 mr-1.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="font-bold text-amber-700">{{ number_format($trail->average_rating, 1) }}</span>
                                <span class="text-gray-600 ml-1">({{ $trail->total_reviews }})</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-2xl font-bold text-emerald-600">₱{{ number_format(optional($trail->package)->price ?? $trail->price ?? 0, 0) }}</span>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('trails.show', $trail->slug) }}"
                                class="group/btn w-full inline-flex justify-center items-center px-6 py-3 border-2 border-emerald-600 text-sm font-bold rounded-xl text-emerald-600 bg-white hover:bg-emerald-600 hover:text-white transition-all duration-300 shadow-sm hover:shadow-lg">
                                <span>View Trail Details</span>
                                <svg class="w-5 h-5 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $trails->links() }}
            </div>
            @else
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border-2 border-dashed border-gray-300 text-center py-16 px-8">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5l7-7 7 7M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No Trails Available Yet</h3>
                    <p class="text-gray-600 text-lg mb-6">This organization hasn't published any trails yet. Check back soon for exciting adventures!</p>
                    <div class="flex justify-center space-x-2">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                        <div class="w-2 h-2 bg-teal-400 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                        <div class="w-2 h-2 bg-cyan-400 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none">
        <!-- Toasts will be dynamically inserted here -->
    </div>

    <!-- Toast Templates (Hidden) -->
    <template id="toast-template">
        <div class="toast-item bg-white rounded-xl shadow-2xl overflow-hidden min-w-[320px] max-w-[420px] pointer-events-auto transform translate-x-[500px] opacity-0 transition-all duration-300 ease-out border-l-4">
            <div class="relative">
                <!-- Progress Bar -->
                <div class="toast-progress absolute top-0 left-0 h-1 bg-current opacity-30 transition-all ease-linear" style="width: 100%;"></div>
                
                <div class="p-4 pr-12">
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="toast-icon flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path class="toast-icon-path" fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0 pt-0.5">
                            <div class="toast-title font-semibold text-gray-900 mb-1"></div>
                            <div class="toast-message text-sm text-gray-600"></div>
                            <div class="toast-details text-xs text-gray-500 mt-1 hidden"></div>
                            <a class="toast-link text-xs font-medium mt-2 gap-1 hover:gap-2 transition-all" style="display: none;">
                                <span class="link-text"></span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Close Button -->
                <button class="toast-close absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </template>

    @push('styles')
    <style>
        /* Toast Animations */
        @keyframes slideInRight {
            from {
                transform: translateX(500px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(500px);
                opacity: 0;
            }
        }

        .toast-item {
            animation: slideInRight 0.3s ease-out forwards;
            backdrop-filter: blur(10px);
        }

        .toast-item.hiding {
            animation: slideOutRight 0.3s ease-in forwards;
        }

        /* Toast hover effects */
        .toast-item:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .toast-close:hover {
            transform: scale(1.1);
        }

        /* Mobile responsiveness */
        @media (max-width: 640px) {
            #toast-container {
                left: 1rem;
                right: 1rem;
            }

            .toast-item {
                min-width: auto;
                max-width: 100%;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Follow/Unfollow functionality
            const followButton = document.querySelector('.follow-btn');
            if (followButton) {
                followButton.addEventListener('click', handleFollowClick);
            }

            // Show More/See Less functionality for review comments
            const toggleButtons = document.querySelectorAll('.toggle-comment');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const container = this.closest('.review-comment-container');
                    const shortText = container.querySelector('.review-comment-short');
                    const fullText = container.querySelector('.review-comment-full');
                    const buttonText = this.querySelector('.show-more-text');
                    const icon = this.querySelector('.show-more-icon');
                    
                    if (shortText.classList.contains('hidden')) {
                        // Currently showing full, switch to short
                        shortText.classList.remove('hidden');
                        fullText.classList.add('hidden');
                        buttonText.textContent = 'Show More';
                        icon.style.transform = 'rotate(0deg)';
                    } else {
                        // Currently showing short, switch to full
                        shortText.classList.add('hidden');
                        fullText.classList.remove('hidden');
                        buttonText.textContent = 'See Less';
                        icon.style.transform = 'rotate(180deg)';
                    }
                });
            });

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
                            // Update button appearance
                            updateFollowButton(button, data.is_following);

                            // Show success message
                            showToast('success', data.message);

                            // Update follower count
                            const followerCount = document.getElementById('followers-count');
                            if (followerCount && data.follower_count !== undefined) {
                                followerCount.textContent = data.follower_count;
                            }

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
                    button.className = 'follow-btn w-full sm:w-auto px-8 py-2.5 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300';
                    button.innerHTML = `
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Following
                    </span>
                `;
                } else {
                    button.className = 'follow-btn w-full sm:w-auto px-8 py-2.5 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg bg-emerald-600 text-white hover:bg-emerald-700';
                    button.innerHTML = `
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Follow
                    </span>
                `;
                }
            }

            function showToast(type, message, opts = {}) {
                try {
                    const container = document.getElementById('toast-container');
                    const template = document.getElementById('toast-template');
                    
                    if (!container || !template) {
                        console.error('Toast container or template not found');
                        return;
                    }

                    // Clone template
                    const toast = template.content.cloneNode(true).querySelector('.toast-item');
                    
                    // Toast configuration based on type
                    const config = {
                        success: {
                            title: opts.title || 'Success!',
                            borderColor: 'border-emerald-500',
                            iconBg: 'bg-emerald-100 text-emerald-600',
                            iconPath: 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z',
                            progressColor: 'text-emerald-500'
                        },
                        error: {
                            title: opts.title || 'Error',
                            borderColor: 'border-red-500',
                            iconBg: 'bg-red-100 text-red-600',
                            iconPath: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z',
                            progressColor: 'text-red-500'
                        },
                        warning: {
                            title: opts.title || 'Warning',
                            borderColor: 'border-amber-500',
                            iconBg: 'bg-amber-100 text-amber-600',
                            iconPath: 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z',
                            progressColor: 'text-amber-500'
                        },
                        info: {
                            title: opts.title || 'Info',
                            borderColor: 'border-blue-500',
                            iconBg: 'bg-blue-100 text-blue-600',
                            iconPath: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
                            progressColor: 'text-blue-500'
                        }
                    };

                    const typeConfig = config[type] || config.info;
                    
                    // Apply styling
                    toast.classList.add(typeConfig.borderColor);
                    const iconContainer = toast.querySelector('.toast-icon');
                    iconContainer.className += ' ' + typeConfig.iconBg;
                    const iconPath = toast.querySelector('.toast-icon-path');
                    iconPath.setAttribute('d', typeConfig.iconPath);
                    
                    // Set content
                    toast.querySelector('.toast-title').textContent = typeConfig.title;
                    toast.querySelector('.toast-message').textContent = message;
                    
                    // Optional details
                    if (opts.details) {
                        const detailsEl = toast.querySelector('.toast-details');
                        detailsEl.textContent = opts.details;
                        detailsEl.classList.remove('hidden');
                    }
                    
                    // Optional link
                    const linkHref = opts.link || opts.viewLink;
                    const linkText = opts.linkText || (opts.viewLink ? 'View More' : null);
                    if (linkHref && linkText) {
                        const linkEl = toast.querySelector('.toast-link');
                        linkEl.href = linkHref;
                        linkEl.querySelector('.link-text').textContent = linkText;
                        linkEl.style.display = 'inline-flex';
                        linkEl.classList.add('items-center');
                        linkEl.classList.add(typeConfig.progressColor);
                    }
                    
                    // Progress bar
                    const progressBar = toast.querySelector('.toast-progress');
                    progressBar.classList.add(typeConfig.progressColor);
                    
                    // Close button
                    const closeBtn = toast.querySelector('.toast-close');
                    closeBtn.addEventListener('click', () => hideToast(toast));
                    
                    // Add to container
                    container.appendChild(toast);
                    
                    // Animate in
                    requestAnimationFrame(() => {
                        toast.style.transform = 'translateX(0)';
                        toast.style.opacity = '1';
                    });
                    
                    // Auto-hide with progress bar animation
                    const duration = opts.duration || 5000;
                    progressBar.style.transition = `width ${duration}ms linear`;
                    
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            progressBar.style.width = '0%';
                        });
                    });
                    
                    // Auto-hide timer
                    const hideTimer = setTimeout(() => {
                        hideToast(toast);
                    }, duration);
                    
                    // Store timer for manual close
                    toast._hideTimer = hideTimer;
                    
                    // Pause on hover
                    toast.addEventListener('mouseenter', () => {
                        clearTimeout(toast._hideTimer);
                        progressBar.style.transition = 'none';
                        const currentWidth = progressBar.offsetWidth;
                        progressBar.style.width = currentWidth + 'px';
                    });
                    
                    toast.addEventListener('mouseleave', () => {
                        const remainingWidth = parseFloat(progressBar.style.width);
                        const remainingTime = (remainingWidth / toast.offsetWidth) * duration;
                        
                        progressBar.style.transition = `width ${remainingTime}ms linear`;
                        progressBar.style.width = '0%';
                        
                        toast._hideTimer = setTimeout(() => {
                            hideToast(toast);
                        }, remainingTime);
                    });
                    
                } catch (err) {
                    console.error('showToast error', err);
                }
            }

            function hideToast(toast) {
                if (toast._hideTimer) {
                    clearTimeout(toast._hideTimer);
                }
                
                toast.style.transform = 'translateX(500px)';
                toast.style.opacity = '0';
                
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        });
    </script>
    @endpush
</x-app-layout>