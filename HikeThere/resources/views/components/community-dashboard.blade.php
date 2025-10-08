@push('floating-navigation')
    @php
    $sections = [
        ['id' => 'community-hero', 'title' => 'Community Hub', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"></path>'],
        ['id' => 'featured-organizations', 'title' => 'Organizations', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>'],
        ['id' => 'trail-reviews', 'title' => 'Trail Reviews', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>'],
        ['id' => 'community-stats', 'title' => 'Community Stats', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>']
    ];
    @endphp
    
    <x-floating-navigation :sections="$sections" />
@endpush

@php
$imageService = app('App\\Services\\TrailImageService');
@endphp

<!-- Main Tabs Section (Above Hero) -->
<div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
    <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex space-x-8" aria-label="Main Tabs">
            <button id="main-tab-community" 
                    class="main-tab-button py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                    role="tab" 
                    aria-controls="main-content-community" 
                    aria-selected="true"
                    data-tab="community">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"></path>
                    </svg>
                    Community
                </span>
            </button>
            <button id="main-tab-posts" 
                    class="main-tab-button py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                    role="tab" 
                    aria-controls="main-content-posts" 
                    aria-selected="false"
                    data-tab="posts">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                    Posts
                </span>
            </button>
        </nav>
    </div>
</div>

<!-- Community Tab Content -->
<div id="main-content-community" class="main-tab-content" role="tabpanel" aria-labelledby="main-tab-community">
<!-- Hero Section -->
<div id="community-hero" class="relative bg-gradient-to-r from-purple-500 via-purple-300 to-pink-500 text-white overflow-hidden hero-container">
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
    
    <!-- Enhanced Community Elements Background (Hidden on mobile) -->
    <div class="absolute inset-0 opacity-15 hidden md:block">
        <!-- Elegant curved connection lines with visible animation -->
        <svg class="absolute inset-0 w-full h-full animate-pulse-slow" viewBox="0 0 1200 400" fill="none" preserveAspectRatio="none">
            <path d="M0 200 Q300 100 600 200 T1200 200" stroke="white" stroke-width="3" fill="none" class="connection-line"/>
            <path d="M0 240 Q400 140 800 240 T1200 240" stroke="white" stroke-width="2" fill="none" class="connection-line-2"/>
            <path d="M0 160 Q200 80 400 160 T800 160" stroke="white" stroke-width="1.5" fill="none" class="connection-line-3"/>
        </svg>
        
        <!-- More visible floating community nodes -->
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
        
        <!-- Decorative community icons -->
        <div class="absolute top-20 left-16 opacity-40">
            <svg class="w-6 h-6" fill="white" viewBox="0 0 24 24">
                <path d="M16 4c4.42 0 8 3.58 8 8s-3.58 8-8 8H8c-4.42 0-8-3.58-8-8s3.58-8 8-8h8m0-2H8C3.58 2 0 5.58 0 10s3.58 8 8 8h8c4.42 0 8-3.58 8-8s-3.58-8-8-8z"/>
            </svg>
        </div>
        <div class="absolute bottom-20 right-16 opacity-40">
            <svg class="w-8 h-8" fill="white" viewBox="0 0 24 24">
                <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-10-5z"/>
            </svg>
        </div>
    </div>
    
    <div class="relative max-w-[90rem] mx-auto px-4 sm:px-6 py-12 sm:py-16">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-3 sm:mb-4 px-2">Trail Connections</h1>
            <p class="text-lg sm:text-xl md:text-2xl text-purple-100 mb-6 sm:mb-8 px-4">Discover hiking organizations that match your interests</p>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto px-4">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input id="community-search-input" type="text"
                        placeholder="Search organizations, trails, or locations..."
                        class="w-full pl-11 sm:pl-12 pr-4 py-3 sm:py-4 text-base sm:text-lg border-0 rounded-2xl focus:ring-4 focus:ring-white focus:ring-opacity-50 transition-all duration-200 text-gray-900 placeholder-gray-500">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Tab Buttons -->
    <div class="mb-6">
        <div class="flex items-center space-x-3" role="tablist" aria-label="Community Tabs">
            <button id="tab-featured-organizations" class="tab-button inline-flex items-center px-4 py-2 rounded-full text-sm font-medium focus:outline-none" role="tab" aria-controls="content-featured-organizations" aria-selected="true" tabindex="0">Discover Organizations</button>
            <button id="tab-events" class="tab-button inline-flex items-center px-4 py-2 rounded-full text-sm font-medium focus:outline-none" role="tab" aria-controls="content-events" aria-selected="false" tabindex="-1">Events</button>
            <button id="tab-trail-reviews" class="tab-button inline-flex items-center px-4 py-2 rounded-full text-sm font-medium focus:outline-none" role="tab" aria-controls="content-trail-reviews" aria-selected="false" tabindex="-1">Latest Trails</button>
        </div>
    </div>

    <!-- Discover Organizations Tab -->
    <div id="featured-organizations" class="tab-content" data-tab="featured-organizations">
        <div class="mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Follow organizations to see their trails and submit reviews for trails from organizations you follow.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if($organizations->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($organizations as $organization)
            <div class="organization-card bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <a href="{{ route('community.organization.show', $organization->id) }}" class="block">
                    <div class="relative">
                        @if($organization->profile_picture)
                            <img src="{{ $organization->profile_picture_url }}" alt="{{ $organization->display_name }}"
                                class="w-full h-48 object-cover rounded-t-xl">
                        @else
                            <!-- Initials Avatar -->
                            <div class="w-full h-48 bg-gradient-to-br from-emerald-400 via-teal-500 to-cyan-600 rounded-t-xl flex items-center justify-center">
                                <div class="text-white text-6xl font-bold">
                                    {{ strtoupper(substr($organization->display_name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $organization->display_name)[1] ?? '', 0, 1)) }}
                                </div>
                            </div>
                        @endif
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Verified
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xl font-semibold text-gray-900 truncate">{{ $organization->display_name }}</h3>
                        </div>

                        @if($organization->bio)
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($organization->bio, 100) }}</p>
                        @endif

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $organization->location ?? 'Location not set' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-yellow-600 font-medium">{{ $organization->average_rating ?? 0 }}</span>
                                <span class="text-gray-400 ml-1">({{ $organization->total_ratings ?? 0 }})</span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-6">
                            <span>{{ $organization->followers_count ?? 0 }} follower{{ ($organization->followers_count ?? 0) !== 1 ? 's' : '' }}</span>
                            <span>{{ $organization->organizationTrails ? $organization->organizationTrails->where('is_active', true)->count() : 0 }} trails</span>
                        </div>
                    </div>
                </a>

                <!-- Follow button outside the link to prevent nested clicking -->
                <div class="px-6 pb-6">
                    <button class="follow-btn w-full py-2 px-4 rounded-lg font-medium transition-all duration-200 
                                    {{ in_array($organization->id, $followingIds) ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                        data-organization-id="{{ $organization->id }}"
                        data-organization-name="{{ $organization->display_name }}"
                        onclick="event.stopPropagation();">
                        @if(in_array($organization->id, $followingIds))
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
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4a9.971 9.971 0 01.712-3.714m0 0A9.971 9.971 0 0118 32a9.971 9.971 0 013.288 4.286M30 20a6 6 0 11-12 0 6 6 0 0112 0zm12 0a6 6 0 11-12 0 6 6 0 0112 0zm-12 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No organizations found</h3>
            <p class="mt-1 text-sm text-gray-500">There are currently no approved organizations available.</p>
        </div>
        @endif
    </div>

    <!-- Events Tab -->
    <div id="events" class="tab-content hidden" data-tab="events">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Events by Organizations</h2>
            <p class="text-sm text-gray-600">Discover upcoming events hosted by organizations you follow or across the community.</p>
        </div>

        @php
            // Helpful debug / guidance when no events are showing
            $debugFollowingIds = isset($followingIds) ? $followingIds : [];
        @endphp

        @if(isset($events) && $events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                @php
                    $now = \Carbon\Carbon::now();
                @endphp
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-250 p-4 flex flex-col h-full" style="min-height:14rem;">
                <div class="flex items-start gap-4">
                    <!-- Date badge -->
                    <div class="flex-shrink-0">
                        @php
                            if (!empty($event->always_available)) {
                                $dateLabel = 'Always';
                                $dayLabel = 'Open';
                            } else {
                                $dateLabel = $event->start_at ? $event->start_at->format('M') : 'TBA';
                                $dayLabel = $event->start_at ? $event->start_at->format('d') : '';
                            }
                        @endphp
                        <div class="bg-emerald-600 text-white text-center rounded-lg px-3 py-2 w-16">
                            <div class="text-xs font-semibold">{{ $dateLabel }}</div>
                            <div class="text-lg font-bold">{{ $dayLabel }}</div>
                        </div>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 leading-tight">{{ $event->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ optional($event->user)->display_name ?? 'Organization' }} • @if(!empty($event->always_available)) Always Open @else {{ $event->start_at ? $event->start_at->format('M d, Y g:ia') : 'TBA' }} @endif</p>
                        @if($event->hiking_start_time)
                            <p class="text-sm text-emerald-700 font-medium mt-1">🥾 Hike starts at {{ \Carbon\Carbon::parse($event->hiking_start_time)->format('g:i A') }}</p>
                        @endif
                        @if($event->location_name ?? false)
                            <p class="text-sm text-gray-600 mt-1">📍 {{ $event->location_name }}</p>
                        @endif
                        @if($event->description)
                        <p class="mt-3 text-sm text-gray-600 line-clamp-3">{{ Str::limit($event->description, 140) }}</p>
                        @endif

                                @if(isset($event->end_at) && $event->end_at->greaterThan($now))
                                        @php
                                            // Short relative formatter: prefer days, then hours, then minutes
                                            // Use absolute diffs (second arg = true) to avoid negative signed values
                                            // and cast to integers to avoid long floats.
                                            $diffInDays = (int) max(0, round($event->end_at->diffInDays($now, true)));
                                            $diffInHours = (int) max(0, round($event->end_at->diffInHours($now, true)));
                                            $diffInMinutes = (int) max(0, round($event->end_at->diffInMinutes($now, true)));

                                            if ($diffInDays >= 1) {
                                                $short = $diffInDays . 'd';
                                            } elseif ($diffInHours >= 1) {
                                                $short = $diffInHours . 'h';
                                            } else {
                                                $short = max(0, $diffInMinutes) . 'm';
                                            }
                                        @endphp
                                    <p class="mt-2 text-sm text-red-600 font-semibold">Ends {{ $short }} left • {{ $event->end_at->format('M d, Y g:ia') }}</p>
                                @endif
                    </div>
                </div>

                <div class="mt-auto flex items-center justify-between">
                    <a href="{{ route('hiker.events.show', $event->slug) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-600 text-white text-sm hover:bg-emerald-700 shadow">
                        <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        View Event
                    </a>

                    @if($event->is_free ?? false)
                        <span class="text-sm font-semibold text-emerald-700 px-3 py-1 bg-emerald-50 rounded-full">Free</span>
                    @elseif(isset($event->price) && $event->price > 0)
                        <span class="text-sm font-semibold text-gray-800">₱{{ number_format($event->price, 0) }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h24M12 28h24M12 36h24" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No events found</h3>
            <p class="mt-1 text-sm text-gray-500">
                There are currently no upcoming events from organizations you follow.
            </p>

            @if(empty($debugFollowingIds))
                <p class="mt-2 text-sm text-amber-600">Tip: You are not following any organizations yet. Follow organizations to see their events here.</p>
            @else
                <p class="mt-2 text-sm text-gray-600">Tip: The organizations you follow may not have upcoming events, or existing events may have start dates in the past.</p>
            @endif

            {{-- Show debug info when app debug is enabled --}}
            @if(config('app.debug'))
                <div class="mt-4 text-left bg-gray-50 border border-gray-200 rounded-lg p-4 text-xs text-gray-700 overflow-auto">
                    <strong>Debug:</strong>
                    <div class="mt-2">
                        <strong>Following IDs:</strong>
                        <pre class="text-xs">{{ json_encode($debugFollowingIds, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    <div class="mt-2">
                        <strong>Events (count):</strong> {{ isset($events) ? $events->count() : 0 }}
                    </div>
                    @if(isset($events) && $events->count() > 0)
                        <div class="mt-2">
                            <strong>Sample Event:</strong>
                            <pre class="text-xs">{{ json_encode($events->first()->toArray(), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Following Tab -->
    <div id="community-stats" class="tab-content hidden">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Organizations You Follow</h2>
        </div>

        <div id="following-organizations">
            <!-- Content loaded via AJAX -->
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mx-auto"></div>
                <p class="mt-2 text-gray-600">Loading...</p>
            </div>
        </div>
    </div>

    <!-- Latest Trails Tab -->
    <div id="trail-reviews" class="tab-content hidden">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Latest Trails from Organizations You Follow</h2>
        </div>

        @if($followedTrails && $followedTrails->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="followed-trails">
            @foreach($followedTrails as $trail)
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative">
                    @php
                    // Get dynamic image from TrailImageService
                    $trailImage = $imageService->getTrailImage($trail, 'primary', 'medium');
                    @endphp
                    <img src="{{ $trailImage }}"
                        alt="{{ $trail->trail_name }}"
                        class="w-full h-48 object-cover rounded-t-xl">
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $trail->difficulty === 'easy' ? 'bg-green-100 text-green-800' : ($trail->difficulty === 'moderate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $trail->difficulty_label }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $trail->trail_name }}</h3>
                    </div>

                    <p class="text-sm text-gray-600 mb-2">by {{ $trail->user->display_name }}</p>

                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $trail->location->name ?? 'Location not set' }}
                    </div>

                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            {{ number_format($trail->average_rating, 1) }} ({{ $trail->total_reviews }})
                        </div>
                        <span>₱{{ number_format(optional($trail->package)->price ?? $trail->price ?? 0, 0) }}</span>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('trails.show', $trail->slug) }}"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 transition-colors duration-200">
                            View Trail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5l7-7 7 7M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No trails available</h3>
            <p class="mt-1 text-sm text-gray-500">Follow some organizations to see their latest trails here.</p>
        </div>
        @endif
    </div>
</div>
</div>
<!-- End Community Tab Content -->

<!-- Posts Tab Content -->
<div id="main-content-posts" class="main-tab-content hidden" role="tabpanel" aria-labelledby="main-tab-posts">
    <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Posts Header with Create Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Community Posts</h1>
                <p class="mt-2 text-gray-600">Share your hiking experiences and discover content from organizations</p>
            </div>
            <button id="create-post-btn" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Post
            </button>
        </div>

        <!-- Posts Feed -->
        <div id="posts-feed" class="space-y-6">
            <!-- Loading State -->
            <div id="posts-loading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
            </div>
            
            <!-- Posts will be loaded here dynamically -->
        </div>

        <!-- Load More Button -->
        <div id="load-more-container" class="text-center mt-8 hidden">
            <button id="load-more-btn" class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                Load More Posts
            </button>
        </div>
    </div>
</div>
<!-- End Posts Tab Content -->

<!-- Create Post Modal -->
<div id="create-post-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form id="create-post-form" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Create a Post</h3>
                        <button type="button" id="close-modal-btn" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-5">
                        <!-- Trail/Event Selection -->
                        <div id="content-selection-section">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span id="selection-label">Select Trail</span> <span class="text-red-500">*</span>
                            </label>
                            <select id="content-select" name="trail_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">Loading...</option>
                            </select>
                            <select id="event-select" name="event_id" class="hidden w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">Select an event</option>
                            </select>
                        </div>

                        <!-- For hikers: Rating -->
                        <div id="rating-section" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating (Optional)</label>
                            <div class="flex gap-2">
                                <div class="star-rating flex gap-1">
                                    <button type="button" class="star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="1">★</button>
                                    <button type="button" class="star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="2">★</button>
                                    <button type="button" class="star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="3">★</button>
                                    <button type="button" class="star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="4">★</button>
                                    <button type="button" class="star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="5">★</button>
                                </div>
                                <input type="hidden" name="rating" id="rating-input">
                            </div>
                        </div>

                        <!-- For hikers: Hike Date -->
                        <div id="hike-date-section" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hike Date (Optional)</label>
                            <input type="date" name="hike_date" id="hike-date-input" max="{{ date('Y-m-d') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>

                        <!-- For hikers: Trail Conditions -->
                        <div id="conditions-section" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Trail Conditions (Optional)</label>
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

                        <!-- Content/Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Share your experience <span class="text-red-500">*</span>
                            </label>
                            <textarea name="content" id="post-content" rows="5" required maxlength="5000" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none"
                                placeholder="Tell us about your experience..."></textarea>
                            <p class="mt-1 text-xs text-gray-500"><span id="char-count">0</span>/5000 characters</p>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Add Photos (Optional, max 10)</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB each</p>
                                    </div>
                                    <input type="file" name="images[]" id="images-input" multiple accept="image/*" class="hidden" max="10">
                                </label>
                            </div>
                            
                            <!-- Image Previews -->
                            <div id="image-previews" class="grid grid-cols-3 gap-3 mt-4 hidden"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" id="cancel-post-btn" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="submit-post-btn" class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-colors">
                        Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Post Modal -->
<div id="edit-post-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="edit-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form id="edit-post-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-post-id" name="post_id">
                
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900" id="edit-modal-title">Edit Post</h3>
                        <button type="button" id="close-edit-modal-btn" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-5">
                        <!-- Content/Trail Info (Read-only) -->
                        <div id="edit-content-info" class="p-3 bg-gray-50 rounded-lg text-sm text-gray-700"></div>

                        <!-- Rating (for trail posts) -->
                        <div id="edit-rating-section" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                            <div class="flex gap-2">
                                <div class="edit-star-rating flex gap-1">
                                    <button type="button" class="edit-star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="1">★</button>
                                    <button type="button" class="edit-star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="2">★</button>
                                    <button type="button" class="edit-star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="3">★</button>
                                    <button type="button" class="edit-star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="4">★</button>
                                    <button type="button" class="edit-star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="5">★</button>
                                </div>
                                <input type="hidden" name="rating" id="edit-rating-input">
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="content" id="edit-post-content" rows="5" required maxlength="5000" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none"
                                placeholder="Share your experience..."></textarea>
                            <p class="mt-1 text-xs text-gray-500"><span id="edit-char-count">0</span>/5000 characters</p>
                        </div>

                        <!-- Existing Images -->
                        <div id="edit-existing-images" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                            <div id="edit-existing-images-grid" class="grid grid-cols-3 gap-3"></div>
                        </div>

                        <!-- Add New Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Add New Photos (Optional, max 10 total)</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB each</p>
                                    </div>
                                    <input type="file" name="images[]" id="edit-images-input" multiple accept="image/*" class="hidden" max="10">
                                </label>
                            </div>
                            
                            <!-- New Image Previews -->
                            <div id="edit-new-image-previews" class="grid grid-cols-3 gap-3 mt-4 hidden"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" id="cancel-edit-btn" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="submit-edit-btn" class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-colors">
                        Update Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-post-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="delete-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-6 pt-6 pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="delete-modal-title">
                            Delete Post
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete this post? This action cannot be undone. All comments and likes will also be deleted.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" id="confirm-delete-btn" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Delete Post
                </button>
                <button type="button" id="cancel-delete-btn" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                    Cancel
                </button>
            </div>
        </div>
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
        // Search functionality
        const searchInput = document.getElementById('community-search-input');
        let searchTimer = null;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    performSearch(this.value.toLowerCase().trim());
                }, 300);
            });
        }

        function performSearch(query) {
            // Get the current active tab
            const activeTab = document.querySelector('.tab-button[aria-selected="true"]');
            const activeTabId = activeTab ? activeTab.id.replace('tab-', '') : 'featured-organizations';

            if (activeTabId === 'featured-organizations' || activeTabId === 'community-stats') {
                // Search organizations
                searchOrganizations(query);
            } else if (activeTabId === 'trail-reviews') {
                // Search trails
                searchTrails(query);
            } else if (activeTabId === 'events') {
                // Search events
                searchEvents(query);
            }
        }

        function searchOrganizations(query) {
            const cards = document.querySelectorAll('#featured-organizations .organization-card, #following-organizations .organization-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const bio = card.querySelector('.text-gray-600')?.textContent.toLowerCase() || '';
                const location = card.querySelector('.flex.items-center span')?.textContent.toLowerCase() || '';
                
                const searchText = `${name} ${bio} ${location}`;
                const isVisible = !query || searchText.includes(query);

                if (isVisible) {
                    card.style.display = '';
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                    card.classList.add('hidden');
                }
            });

            // Show "no results" message if needed
            updateNoResultsMessage('featured-organizations', visibleCount, 'organizations');
            if (document.getElementById('following-organizations').dataset.loaded) {
                updateNoResultsMessage('following-organizations', visibleCount, 'organizations');
            }
        }

        function searchTrails(query) {
            const cards = document.querySelectorAll('#followed-trails > div');
            let visibleCount = 0;

            cards.forEach(card => {
                const trailName = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const orgName = card.querySelector('.text-sm.text-gray-600')?.textContent.toLowerCase() || '';
                const location = card.querySelector('.flex.items-center.text-sm')?.textContent.toLowerCase() || '';
                
                const searchText = `${trailName} ${orgName} ${location}`;
                const isVisible = !query || searchText.includes(query);

                if (isVisible) {
                    card.style.display = '';
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                    card.classList.add('hidden');
                }
            });

            updateNoResultsMessage('trail-reviews', visibleCount, 'trails');
        }

        function searchEvents(query) {
            const cards = document.querySelectorAll('#events .bg-white.rounded-xl');
            let visibleCount = 0;

            cards.forEach(card => {
                const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const orgName = card.querySelector('.text-gray-500')?.textContent.toLowerCase() || '';
                const location = card.querySelector('.text-gray-600')?.textContent.toLowerCase() || '';
                const description = card.querySelector('.line-clamp-3')?.textContent.toLowerCase() || '';
                
                const searchText = `${title} ${orgName} ${location} ${description}`;
                const isVisible = !query || searchText.includes(query);

                if (isVisible) {
                    card.style.display = '';
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                    card.classList.add('hidden');
                }
            });

            updateNoResultsMessage('events', visibleCount, 'events');
        }

        function updateNoResultsMessage(containerId, count, itemType) {
            const container = document.getElementById(containerId);
            if (!container) return;

            let noResultsDiv = container.querySelector('.no-search-results');

            if (count === 0 && searchInput.value.trim()) {
                // Show no results message
                if (!noResultsDiv) {
                    noResultsDiv = document.createElement('div');
                    noResultsDiv.className = 'no-search-results text-center py-12 col-span-full';
                    noResultsDiv.innerHTML = `
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No ${itemType} found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms.</p>
                    `;
                    const grid = container.querySelector('.grid');
                    if (grid) {
                        grid.appendChild(noResultsDiv);
                    } else {
                        container.appendChild(noResultsDiv);
                    }
                }
            } else if (noResultsDiv) {
                // Remove no results message
                noResultsDiv.remove();
            }
        }

        // Hero section interactive effects
        const heroContainer = document.querySelector('.hero-container');
        const floatingNodes = document.querySelectorAll('.floating-node');
        const hoverCircles = document.querySelectorAll('.hover-circle');
        
        // Mouse movement parallax effect
        if (heroContainer) {
            heroContainer.addEventListener('mousemove', function(e) {
                const rect = heroContainer.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const moveX = (x - centerX) / centerX;
                const moveY = (y - centerY) / centerY;
                
                // Apply subtle parallax to floating nodes
                floatingNodes.forEach((node, index) => {
                    const speed = parseFloat(node.dataset.speed) || 1;
                    const translateX = moveX * speed * 2;
                    const translateY = moveY * speed * 2;
                    node.style.transform = `translate(${translateX}px, ${translateY}px)`;
                });
                
                // Apply gentle movement to hover circles
                hoverCircles.forEach((circle, index) => {
                    const translateX = moveX * 1.5;
                    const translateY = moveY * 1.5;
                    circle.style.transform = `translate(${translateX}px, ${translateY}px)`;
                });
            });
            
            // Reset positions when mouse leaves
            heroContainer.addEventListener('mouseleave', function() {
                floatingNodes.forEach(node => {
                    node.style.transform = '';
                });
                hoverCircles.forEach(circle => {
                    circle.style.transform = '';
                });
            });
        }
        
        // Add click ripple effect to floating nodes
        floatingNodes.forEach(node => {
            node.addEventListener('click', function(e) {
                // Create ripple effect
                const ripple = document.createElement('div');
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.4);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                    left: -10px;
                    top: -10px;
                    width: 20px;
                    height: 20px;
                `;
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        function activateTab(tabKey) {
            // Update button states (visual + accessibility)
            tabButtons.forEach((btn, idx) => {
                const isActive = btn.id === 'tab-' + tabKey;
                btn.classList.toggle('active', isActive);
                if (isActive) {
                    btn.classList.add('bg-emerald-600', 'text-white', 'shadow-lg');
                    btn.classList.remove('bg-white', 'text-gray-600');
                    btn.setAttribute('aria-selected', 'true');
                    btn.setAttribute('tabindex', '0');
                } else {
                    btn.classList.remove('bg-emerald-600', 'text-white', 'shadow-lg');
                    btn.classList.add('bg-white', 'text-gray-600');
                    btn.setAttribute('aria-selected', 'false');
                    btn.setAttribute('tabindex', '-1');
                }
            });

            // Update content visibility
            tabContents.forEach(content => {
                const key = content.dataset.tab || content.id;
                if (key === tabKey || content.id === tabKey || content.id === 'content-' + tabKey) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });

            // Special-case: when showing the following/organization-stats area, load following organizations
            if (tabKey === 'community-stats') {
                const followingContainer = document.getElementById('following-organizations');
                if (followingContainer && !followingContainer.dataset.loaded) {
                    loadFollowingOrganizations();
                    followingContainer.dataset.loaded = 'true';
                }
            }
        }

        // Wire buttons
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.id.replace('tab-', '');
                activateTab(tabId);
            });
        });

        // Keyboard navigation for tabs (Left/Right/Home/End)
        tabButtons.forEach((btn, index) => {
            btn.addEventListener('keydown', (e) => {
                const key = e.key;
                let newIndex = null;
                if (key === 'ArrowRight') newIndex = (index + 1) % tabButtons.length;
                if (key === 'ArrowLeft') newIndex = (index - 1 + tabButtons.length) % tabButtons.length;
                if (key === 'Home') newIndex = 0;
                if (key === 'End') newIndex = tabButtons.length - 1;

                if (newIndex !== null) {
                    e.preventDefault();
                    tabButtons[newIndex].focus();
                    const tabId = tabButtons[newIndex].id.replace('tab-', '');
                    activateTab(tabId);
                }
            });
        });

        // Activate initial tab (featured-organizations)
        activateTab('featured-organizations');

        // If a tab is requested via hash (#events) or query (?tab=events), activate it
        try {
            const hash = window.location.hash ? window.location.hash.replace('#', '') : null;
            const params = new URLSearchParams(window.location.search);
            const tabParam = params.get('tab');
            const requested = tabParam || hash;
            if (requested) {
                // Normalize ids that may include 'tab-' prefix
                const normalized = requested.replace(/^tab-/, '');
                // Delay slightly to ensure DOM ready
                setTimeout(() => activateTab(normalized), 50);
            }
        } catch (e) {
            // ignore URL parsing errors
            console.warn('Could not parse requested tab from URL', e);
        }

        // Follow/Unfollow functionality
        const followButtons = document.querySelectorAll('.follow-btn');
        followButtons.forEach(button => {
            button.addEventListener('click', handleFollowClick);
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

                        // Update counters
                        updateFollowingCount();

                        // Update follower count in the card (if available)
                        const card = button.closest('.organization-card');
                        const followerSpan = card.querySelector('.text-gray-500');
                        if (followerSpan && followerSpan.textContent.includes('follower')) {
                            followerSpan.textContent = `${data.follower_count} follower${data.follower_count !== 1 ? 's' : ''}`;
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
                button.className = 'follow-btn w-full py-2 px-4 rounded-lg font-medium transition-all duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300';
                button.innerHTML = `
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Following
                    </span>
                `;
            } else {
                button.className = 'follow-btn w-full py-2 px-4 rounded-lg font-medium transition-all duration-200 bg-emerald-600 text-white hover:bg-emerald-700';
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

        function updateFollowingCount() {
            // Count current following buttons
            const followingButtons = document.querySelectorAll('.follow-btn:not([disabled])');
            let followingCount = 0;

            followingButtons.forEach(button => {
                if (button.textContent.includes('Following')) {
                    followingCount++;
                }
            });

            // Update count displays
            const countElement = document.getElementById('following-count');
            if (countElement) {
                countElement.textContent = followingCount;
            }

            // Update tab text
            const followingTab = document.getElementById('tab-following');
            if (followingTab) {
                followingTab.textContent = `Following (${followingCount})`;
            }
        }

        function loadFollowingOrganizations() {
            // Clear loading state
            const followingContainer = document.getElementById('following-organizations');
            followingContainer.innerHTML = '';

            // Get organizations that are being followed
            const followingCards = Array.from(document.querySelectorAll('.organization-card')).filter(card => {
                const followBtn = card.querySelector('.follow-btn');
                return followBtn && followBtn.textContent.includes('Following');
            });

            if (followingCards.length > 0) {
                const gridContainer = document.createElement('div');
                gridContainer.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';

                followingCards.forEach(card => {
                    // Clone the card and ensure the follow button works
                    const clonedCard = card.cloneNode(true);
                    const clonedFollowBtn = clonedCard.querySelector('.follow-btn');
                    if (clonedFollowBtn) {
                        clonedFollowBtn.addEventListener('click', handleFollowClick);
                    }
                    gridContainer.appendChild(clonedCard);
                });

                followingContainer.appendChild(gridContainer);
            } else {
                // Show empty state
                followingContainer.innerHTML = `
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4a9.971 9.971 0 01.712-3.714m0 0A9.971 9.971 0 0118 32a9.971 9.971 0 013.288 4.286M30 20a6 6 0 11-12 0 6 6 0 0112 0zm12 0a6 6 0 11-12 0 6 6 0 0112 0zm-12 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Not following any organizations yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Follow some organizations to see them here.</p>
                    </div>
                `;
            }
        }

        // Enhanced Toast System
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
    
        // Add lightweight styles for tabs and event cards when Tailwind utilities aren't sufficient
        const extraStyles = document.createElement('style');
        extraStyles.textContent = `
            .tab-button { transition: all .18s ease; }
            .tab-button[aria-selected="true"] { box-shadow: 0 8px 20px rgba(16,185,129,0.12); }
            .tab-button:focus { outline: 3px solid rgba(56,189,248,0.18); outline-offset: 2px; }
            .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
            .main-tab-button { border-color: transparent; color: #6b7280; }
            .main-tab-button[aria-selected="true"] { border-color: #10b981; color: #10b981; }
            .main-tab-content { display: block; }
            .main-tab-content.hidden { display: none; }
        `;
        document.head.appendChild(extraStyles);

        // ========================================
        // MAIN TABS FUNCTIONALITY (Community vs Posts)
        // ========================================
        const mainTabButtons = document.querySelectorAll('.main-tab-button');
        const mainTabContents = document.querySelectorAll('.main-tab-content');

        function activateMainTab(tabName) {
            mainTabButtons.forEach(btn => {
                const isActive = btn.dataset.tab === tabName;
                btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            mainTabContents.forEach(content => {
                const contentId = content.id.replace('main-content-', '');
                if (contentId === tabName) {
                    content.classList.remove('hidden');
                    
                    // Load posts when Posts tab is activated
                    if (tabName === 'posts' && !content.dataset.loaded) {
                        loadPosts();
                        content.dataset.loaded = 'true';
                    }
                } else {
                    content.classList.add('hidden');
                }
            });
        }

        mainTabButtons.forEach(button => {
            button.addEventListener('click', () => {
                activateMainTab(button.dataset.tab);
            });
        });

        // ========================================
        // POSTS TAB FUNCTIONALITY
        // ========================================
        let currentPage = 1;
        let isLoadingPosts = false;
        const userType = '{{ auth()->check() ? auth()->user()->user_type : "" }}';

        // Create Post Modal
        const createPostBtn = document.getElementById('create-post-btn');
        const createPostModal = document.getElementById('create-post-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const cancelPostBtn = document.getElementById('cancel-post-btn');
        const createPostForm = document.getElementById('create-post-form');

        if (createPostBtn) {
            createPostBtn.addEventListener('click', openCreatePostModal);
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeCreatePostModal);
        }

        if (cancelPostBtn) {
            cancelPostBtn.addEventListener('click', closeCreatePostModal);
        }

        // Close modal on backdrop click
        if (createPostModal) {
            createPostModal.addEventListener('click', (e) => {
                if (e.target === createPostModal) {
                    closeCreatePostModal();
                }
            });
        }
        
        // ========================================
        // EDIT POST MODAL EVENT LISTENERS
        // ========================================
        const editPostModal = document.getElementById('edit-post-modal');
        const closeEditModalBtn = document.getElementById('close-edit-modal-btn');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');
        const editPostForm = document.getElementById('edit-post-form');
        const editContentTextarea = document.getElementById('edit-post-content');
        const editImagesInput = document.getElementById('edit-images-input');
        
        if (closeEditModalBtn) {
            closeEditModalBtn.addEventListener('click', closeEditModal);
        }
        
        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', closeEditModal);
        }
        
        // Close edit modal on backdrop click
        if (editPostModal) {
            editPostModal.addEventListener('click', (e) => {
                if (e.target === editPostModal) {
                    closeEditModal();
                }
            });
        }
        
        // Edit form submit
        if (editPostForm) {
            editPostForm.addEventListener('submit', handleEditSubmit);
        }
        
        // Edit rating stars
        document.querySelectorAll('.edit-star-btn').forEach(star => {
            star.addEventListener('click', () => {
                editRatingValue = parseInt(star.dataset.rating);
                updateEditStars();
            });
        });
        
        // Edit character count
        if (editContentTextarea) {
            editContentTextarea.addEventListener('input', updateEditCharCount);
        }
        
        // Edit image preview
        if (editImagesInput) {
            editImagesInput.addEventListener('change', function() {
                const previewsContainer = document.getElementById('edit-new-image-previews');
                previewsContainer.innerHTML = '';
                
                if (this.files.length > 0) {
                    previewsContainer.classList.remove('hidden');
                    
                    Array.from(this.files).forEach((file, idx) => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                const div = document.createElement('div');
                                div.className = 'relative';
                                div.innerHTML = `
                                    <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg">
                                    <div class="absolute top-1 right-1 bg-emerald-500 text-white text-xs px-2 py-1 rounded">New</div>
                                `;
                                previewsContainer.appendChild(div);
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                } else {
                    previewsContainer.classList.add('hidden');
                }
            });
        }
        
        // ========================================
        // DELETE POST MODAL EVENT LISTENERS
        // ========================================
        const deletePostModal = document.getElementById('delete-post-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        
        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', closeDeleteModal);
        }
        
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', confirmDeletePost);
        }
        
        // Close delete modal on backdrop click
        if (deletePostModal) {
            deletePostModal.addEventListener('click', (e) => {
                if (e.target === deletePostModal) {
                    closeDeleteModal();
                }
            });
        }

        function openCreatePostModal() {
            if (!createPostModal) return;
            
            createPostModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Load content based on user type
            if (userType === 'organization') {
                loadOrganizationContent();
            } else {
                loadUserTrails();
            }
        }

        function closeCreatePostModal() {
            if (!createPostModal) return;
            
            createPostModal.classList.add('hidden');
            document.body.style.overflow = '';
            createPostForm.reset();
            document.getElementById('image-previews').innerHTML = '';
            document.getElementById('image-previews').classList.add('hidden');
            resetStarRating();
        }

        // Load trails for hikers
        function loadUserTrails() {
            const contentSelect = document.getElementById('content-select');
            const ratingSection = document.getElementById('rating-section');
            const hikeDateSection = document.getElementById('hike-date-section');
            const conditionsSection = document.getElementById('conditions-section');
            const selectionLabel = document.getElementById('selection-label');
            
            selectionLabel.textContent = 'Select Trail';
            ratingSection.classList.remove('hidden');
            hikeDateSection.classList.remove('hidden');
            conditionsSection.classList.remove('hidden');
            
            contentSelect.innerHTML = '<option value="">Loading trails...</option>';
            
            console.log('🔍 Loading user trails from:', '{{ route("community.posts.user-trails") }}');
            
            fetch('{{ route("community.posts.user-trails") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('📡 Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📦 Received data:', data);
                    console.log('✅ Success:', data.success);
                    console.log('🗺️ Trails count:', data.trails ? data.trails.length : 0);
                    
                    if (data.success) {
                        if (data.trails && data.trails.length > 0) {
                            console.log('✨ Populating dropdown with', data.trails.length, 'trails');
                            contentSelect.innerHTML = '<option value="">Select a trail you\'ve visited</option>';
                            contentSelect.disabled = false;
                            data.trails.forEach(trail => {
                                const displayName = trail.user?.organization_name || trail.user?.name || 'Unknown';
                                console.log('  - Trail:', trail.trail_name, 'by', displayName);
                                const option = document.createElement('option');
                                option.value = trail.id;
                                option.textContent = trail.trail_name + ' (by ' + displayName + ')';
                                contentSelect.appendChild(option);
                            });
                            showToast('success', `Found ${data.trails.length} trail(s) from organizations you follow`);
                        } else {
                            console.warn('⚠️ No trails returned');
                            contentSelect.innerHTML = '<option value="">No trails available - Follow organizations with trails first</option>';
                            contentSelect.disabled = true;
                            showToast('info', 'Follow some organizations to see their trails here');
                        }
                    } else {
                        console.error('❌ API returned success: false');
                        throw new Error(data.message || 'Failed to load trails');
                    }
                })
                .catch(error => {
                    console.error('💥 Error loading trails:', error);
                    console.error('Error stack:', error.stack);
                    contentSelect.innerHTML = '<option value="">Error loading trails - Please try again</option>';
                    contentSelect.disabled = true;
                    showToast('error', 'Error loading trails: ' + error.message);
                });
        }

        // Load content for organizations
        function loadOrganizationContent() {
            const contentSelect = document.getElementById('content-select');
            const eventSelect = document.getElementById('event-select');
            const ratingSection = document.getElementById('rating-section');
            const hikeDateSection = document.getElementById('hike-date-section');
            const conditionsSection = document.getElementById('conditions-section');
            const selectionLabel = document.getElementById('selection-label');
            
            selectionLabel.textContent = 'Select Trail or Event';
            ratingSection.classList.add('hidden');
            hikeDateSection.classList.add('hidden');
            conditionsSection.classList.add('hidden');
            
            fetch('{{ route("community.posts.organization-content") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Populate trails
                        contentSelect.innerHTML = '<option value="">Select your trail</option>';
                        data.trails.forEach(trail => {
                            const option = document.createElement('option');
                            option.value = trail.id;
                            option.textContent = trail.trail_name;
                            contentSelect.appendChild(option);
                        });

                        // Populate events
                        if (data.events.length > 0) {
                            eventSelect.classList.remove('hidden');
                            eventSelect.innerHTML = '<option value="">Or select your event</option>';
                            data.events.forEach(event => {
                                const option = document.createElement('option');
                                option.value = event.id;
                                option.textContent = event.title;
                                eventSelect.appendChild(option);
                            });
                        }
                    } else {
                        throw new Error(data.message || 'Failed to load content');
                    }
                })
                .catch(error => {
                    console.error('Error loading content:', error);
                    showToast('error', 'Error loading content: ' + error.message);
                });
        }

        // Star rating functionality
        const starButtons = document.querySelectorAll('.star-btn');
        const ratingInput = document.getElementById('rating-input');
        let currentRating = 0;

        starButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const rating = parseInt(this.dataset.rating);
                setStarRating(rating);
            });

            button.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                highlightStars(rating);
            });
        });

        document.querySelector('.star-rating')?.addEventListener('mouseleave', function() {
            highlightStars(currentRating);
        });

        function setStarRating(rating) {
            currentRating = rating;
            ratingInput.value = rating;
            highlightStars(rating);
        }

        function highlightStars(rating) {
            starButtons.forEach(button => {
                const starRating = parseInt(button.dataset.rating);
                if (starRating <= rating) {
                    button.classList.remove('text-gray-300');
                    button.classList.add('text-yellow-400');
                } else {
                    button.classList.remove('text-yellow-400');
                    button.classList.add('text-gray-300');
                }
            });
        }

        function resetStarRating() {
            currentRating = 0;
            ratingInput.value = '';
            highlightStars(0);
        }

        // Character count for post content
        const postContent = document.getElementById('post-content');
        const charCount = document.getElementById('char-count');
        
        if (postContent) {
            postContent.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }

        // Image upload and preview
        const imagesInput = document.getElementById('images-input');
        const imagePreviews = document.getElementById('image-previews');
        let selectedImages = [];

        if (imagesInput) {
            imagesInput.addEventListener('change', handleImageSelection);
        }

        function handleImageSelection(e) {
            const files = Array.from(e.target.files);
            
            if (selectedImages.length + files.length > 10) {
                showToast('warning', 'You can only upload up to 10 images');
                return;
            }

            files.forEach(file => {
                if (file.size > 5 * 1024 * 1024) {
                    showToast('warning', `${file.name} is too large. Max size is 5MB`);
                    return;
                }

                selectedImages.push(file);
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative group';
                    previewDiv.innerHTML = `
                        <img src="${event.target.result}" class="w-full h-24 object-cover rounded-lg">
                        <button type="button" class="remove-image absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity" data-index="${selectedImages.length - 1}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    
                    imagePreviews.appendChild(previewDiv);
                    imagePreviews.classList.remove('hidden');
                    
                    previewDiv.querySelector('.remove-image').addEventListener('click', function() {
                        const index = parseInt(this.dataset.index);
                        removeImage(index, previewDiv);
                    });
                };
                
                reader.readAsDataURL(file);
            });
        }

        function removeImage(index, previewDiv) {
            selectedImages.splice(index, 1);
            previewDiv.remove();
            
            if (selectedImages.length === 0) {
                imagePreviews.classList.add('hidden');
            }
            
            // Update indices
            document.querySelectorAll('.remove-image').forEach((btn, idx) => {
                btn.dataset.index = idx;
            });
        }

        // Submit post form
        if (createPostForm) {
            createPostForm.addEventListener('submit', handlePostSubmit);
        }

        function handlePostSubmit(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-post-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Posting...';
            
            const formData = new FormData(createPostForm);
            
            // Add selected images to form data
            selectedImages.forEach((image, index) => {
                formData.append(`images[${index}]`, image);
            });
            
            // Add conditions as array
            const conditions = Array.from(document.querySelectorAll('input[name="conditions[]"]:checked')).map(cb => cb.value);
            formData.delete('conditions[]');
            conditions.forEach(condition => {
                formData.append('conditions[]', condition);
            });
            
            fetch('{{ route("community.posts.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message || 'Post created successfully!');
                    closeCreatePostModal();
                    // Reload the page to show the new post
                    window.location.reload();
                } else {
                    showToast('error', data.message || 'Failed to create post');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while creating the post');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Post';
            });
        }

        // Load posts function
        function loadPosts(refresh = false) {
            if (isLoadingPosts) return;
            
            isLoadingPosts = true;
            const postsFeed = document.getElementById('posts-feed');
            const postsLoading = document.getElementById('posts-loading');
            
            if (refresh) {
                currentPage = 1;
                postsFeed.innerHTML = '';
                postsLoading.classList.remove('hidden');
            }
            
            fetch(`{{ route("community.posts.index") }}?page=${currentPage}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        postsLoading.classList.add('hidden');
                        
                        if (data.posts.data.length === 0 && currentPage === 1) {
                            postsFeed.innerHTML = `
                                <div class="text-center py-12 bg-white rounded-xl shadow-sm">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-gray-900">No posts yet</h3>
                                    <p class="mt-2 text-gray-500">Be the first to share your hiking experience!</p>
                                    <button onclick="document.getElementById('create-post-btn').click()" class="mt-4 px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                                        Create Your First Post
                                    </button>
                                </div>
                            `;
                        } else {
                            data.posts.data.forEach(post => {
                                postsFeed.appendChild(createPostCard(post));
                            });
                            
                            // Show/hide load more button
                            const loadMoreContainer = document.getElementById('load-more-container');
                            if (data.posts.next_page_url) {
                                loadMoreContainer.classList.remove('hidden');
                            } else {
                                loadMoreContainer.classList.add('hidden');
                            }
                        }
                    } else {
                        throw new Error(data.message || 'Failed to load posts');
                    }
                })
                .catch(error => {
                    console.error('Error loading posts:', error);
                    postsLoading.classList.add('hidden');
                    showToast('error', 'Error loading posts: ' + error.message);
                })
                .finally(() => {
                    isLoadingPosts = false;
                });
        }

        // Load more posts
        const loadMoreBtn = document.getElementById('load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => {
                currentPage++;
                loadPosts();
            });
        }

        // TO BE CONTINUED IN NEXT PART - createPostCard function and interactions
        
        // Create post card HTML
        // Helper function to get initials from name
        function getInitials(name) {
            if (!name) return '?';
            const words = name.trim().split(' ');
            if (words.length === 1) {
                return words[0].substring(0, 2).toUpperCase();
            }
            return (words[0][0] + words[words.length - 1][0]).toUpperCase();
        }
        
        // Helper function to create avatar HTML (with initials fallback)
        function createAvatarHtml(user, size = 'w-10 h-10', textSize = 'text-sm') {
            const name = user?.display_name || user?.organization_name || user?.name || 'Unknown';
            const avatarUrl = user?.profile_picture_url;
            
            // Check if avatar URL is valid and not a default/placeholder
            const hasValidAvatar = avatarUrl && 
                                   avatarUrl.trim() !== '' && 
                                   avatarUrl !== '/images/default-avatar.png' &&
                                   avatarUrl !== 'default-avatar.png' &&
                                   !avatarUrl.includes('default');
            
            if (hasValidAvatar) {
                return `<img src="${avatarUrl}" alt="${name}" class="${size} rounded-full object-cover">`;
            } else {
                const initials = getInitials(name);
                const colors = [
                    'bg-gradient-to-br from-blue-400 to-blue-600',
                    'bg-gradient-to-br from-green-400 to-green-600',
                    'bg-gradient-to-br from-purple-400 to-purple-600',
                    'bg-gradient-to-br from-pink-400 to-pink-600',
                    'bg-gradient-to-br from-indigo-400 to-indigo-600',
                    'bg-gradient-to-br from-emerald-400 to-emerald-600',
                ];
                const colorIndex = name.charCodeAt(0) % colors.length;
                const colorClass = colors[colorIndex];
                
                return `<div class="${size} ${colorClass} rounded-full flex items-center justify-center text-white font-bold ${textSize}">${initials}</div>`;
            }
        }
        
        function createPostCard(post) {
            const card = document.createElement('div');
            card.className = 'bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden';
            card.dataset.postId = post.id;
            
            // Get user display name and avatar
            const userName = post.user?.display_name || post.user?.organization_name || post.user?.name || 'Unknown User';
            const avatarHtml = createAvatarHtml(post.user, 'w-12 h-12', 'text-lg');
            const isOrg = post.type === 'organization';
            const formattedDate = new Date(post.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            
            let contentLink = '';
            if (post.trail) {
                contentLink = `<a href="/trails/${post.trail.slug}" class="text-emerald-600 hover:text-emerald-700 font-medium">📍 ${post.trail.trail_name}</a>`;
            } else if (post.event) {
                contentLink = `<a href="/events/${post.event.slug}" class="text-emerald-600 hover:text-emerald-700 font-medium">🎉 ${post.event.title}</a>`;
            }
            
            let ratingHtml = '';
            if (post.rating) {
                const stars = '⭐'.repeat(post.rating) + '☆'.repeat(5 - post.rating);
                ratingHtml = `<div class="mt-2 text-yellow-500 text-lg">${stars}</div>`;
            }
            
            let conditionsHtml = '';
            if (post.conditions && post.conditions.length > 0) {
                conditionsHtml = `
                    <div class="mt-3 flex flex-wrap gap-2">
                        ${post.conditions.map(condition => `
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium capitalize">${condition}</span>
                        `).join('')}
                    </div>
                `;
            }
            
            let imagesHtml = '';
            if (post.image_urls && post.image_urls.length > 0) {
                const imageGrid = post.image_urls.length === 1 ? 'grid-cols-1' : 
                                 post.image_urls.length === 2 ? 'grid-cols-2' : 'grid-cols-3';
                imagesHtml = `
                    <div class="mt-4 grid ${imageGrid} gap-2">
                        ${post.image_urls.map((url, idx) => `
                            <img src="${url}" alt="Post image ${idx + 1}" 
                                 class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                 onclick="openImageModal('${url}')">
                        `).join('')}
                    </div>
                `;
            }
            
            const likeIcon = post.is_liked_by_auth_user ? '❤️' : '🤍';
            const likeClass = post.is_liked_by_auth_user ? 'text-red-500' : 'text-gray-500';
            
            // Check if current user owns this post
            const currentUserId = {{ auth()->id() }};
            const isOwner = post.user_id === currentUserId;
            
            card.innerHTML = `
                <div class="p-6">
                    <!-- Post Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            ${avatarHtml}
                            <div>
                                <h3 class="font-semibold text-gray-900">${userName}</h3>
                                <p class="text-sm text-gray-500">${formattedDate}${post.hike_date ? ' • Hiked on ' + new Date(post.hike_date).toLocaleDateString() : ''}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            ${isOrg ? '<span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">Organization</span>' : ''}
                            ${isOwner ? `
                            <div class="relative post-menu">
                                <button class="post-menu-btn p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                    </svg>
                                </button>
                                <div class="post-menu-dropdown hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                    <button class="edit-post-btn w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-colors" data-post-id="${post.id}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit Post
                                    </button>
                                    <button class="delete-post-btn w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 rounded-b-lg transition-colors" data-post-id="${post.id}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete Post
                                    </button>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    
                    <!-- Trail/Event Link -->
                    ${contentLink ? `<div class="mb-3">${contentLink}</div>` : ''}
                    
                    <!-- Rating -->
                    ${ratingHtml}
                    
                    <!-- Post Content -->
                    <p class="mt-3 text-gray-700 whitespace-pre-wrap">${escapeHtml(post.content)}</p>
                    
                    <!-- Conditions -->
                    ${conditionsHtml}
                    
                    <!-- Images -->
                    ${imagesHtml}
                    
                    <!-- Actions -->
                    <div class="mt-6 pt-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <button class="like-btn flex items-center gap-2 ${likeClass} hover:text-red-500 transition-colors" data-post-id="${post.id}">
                                <span class="text-xl">${likeIcon}</span>
                                <span class="like-count font-medium">${post.likes_count || 0}</span>
                            </button>
                            <button class="comment-toggle-btn flex items-center gap-2 text-gray-500 hover:text-emerald-600 transition-colors" data-post-id="${post.id}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="comment-count font-medium">${post.comments_count || 0}</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Comments Section -->
                    <div class="comments-section hidden mt-4 pt-4 border-t border-gray-200" data-post-id="${post.id}">
                        <!-- Add Comment Form -->
                        <div class="mb-4">
                            <textarea class="comment-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none" 
                                      placeholder="Write a comment..." rows="2" data-post-id="${post.id}"></textarea>
                            <button class="submit-comment-btn mt-2 px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-colors" data-post-id="${post.id}">
                                Post Comment
                            </button>
                        </div>
                        
                        <!-- Comments List -->
                        <div class="comments-list space-y-4" data-post-id="${post.id}">
                            <div class="text-center py-4">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-emerald-600 mx-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Attach event listeners
            attachPostEventListeners(card, post.id);
            
            return card;
        }
        
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
        
        function attachPostEventListeners(card, postId) {
            // Like button
            const likeBtn = card.querySelector('.like-btn');
            if (likeBtn) {
                likeBtn.addEventListener('click', () => handleLike(postId, card));
            }
            
            // Comment toggle
            const commentToggleBtn = card.querySelector('.comment-toggle-btn');
            if (commentToggleBtn) {
                commentToggleBtn.addEventListener('click', () => toggleComments(postId, card));
            }
            
            // Submit comment
            const submitCommentBtn = card.querySelector('.submit-comment-btn');
            if (submitCommentBtn) {
                submitCommentBtn.addEventListener('click', () => submitComment(postId, card));
            }
            
            // Post menu (for owners)
            const postMenuBtn = card.querySelector('.post-menu-btn');
            const postMenuDropdown = card.querySelector('.post-menu-dropdown');
            
            if (postMenuBtn && postMenuDropdown) {
                postMenuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    postMenuDropdown.classList.toggle('hidden');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', () => {
                    postMenuDropdown.classList.add('hidden');
                });
                
                // Edit button
                const editBtn = card.querySelector('.edit-post-btn');
                if (editBtn) {
                    editBtn.addEventListener('click', () => {
                        postMenuDropdown.classList.add('hidden');
                        openEditModal(postId);
                    });
                }
                
                // Delete button
                const deleteBtn = card.querySelector('.delete-post-btn');
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', () => {
                        postMenuDropdown.classList.add('hidden');
                        openDeleteModal(postId);
                    });
                }
            }
        }
        
        // ========================================
        // EDIT POST MODAL FUNCTIONS
        // ========================================
        let currentEditPostId = null;
        let editRatingValue = 0;
        let editImagesToDelete = [];
        
        function openEditModal(postId) {
            currentEditPostId = postId;
            editImagesToDelete = [];
            
            // Fetch post data
            fetch(`{{ url('community/posts') }}/${postId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const post = data.post;
                        const modal = document.getElementById('edit-post-modal');
                        
                        // Set post ID
                        document.getElementById('edit-post-id').value = postId;
                        
                        // Set content info (trail or event - read-only)
                        const contentInfo = document.getElementById('edit-content-info');
                        if (post.trail) {
                            contentInfo.textContent = `Trail: ${post.trail.trail_name}`;
                        } else if (post.event) {
                            contentInfo.textContent = `Event: ${post.event.title}`;
                        } else {
                            contentInfo.textContent = 'General Post';
                        }
                        
                        // Set rating (if trail post)
                        const ratingSection = document.getElementById('edit-rating-section');
                        if (post.trail && post.rating) {
                            ratingSection.classList.remove('hidden');
                            editRatingValue = post.rating;
                            updateEditStars();
                        } else {
                            ratingSection.classList.add('hidden');
                            editRatingValue = 0;
                        }
                        
                        // Set description
                        const contentTextarea = document.getElementById('edit-post-content');
                        contentTextarea.value = post.content;
                        updateEditCharCount();
                        
                        // Show existing images
                        const existingImagesDiv = document.getElementById('edit-existing-images');
                        const existingImagesGrid = document.getElementById('edit-existing-images-grid');
                        
                        if (post.image_urls && post.image_urls.length > 0) {
                            existingImagesDiv.classList.remove('hidden');
                            existingImagesGrid.innerHTML = post.image_urls.map((url, idx) => `
                                <div class="relative group" data-image-url="${url}">
                                    <img src="${url}" class="w-full h-24 object-cover rounded-lg" alt="Post image">
                                    <button type="button" class="remove-existing-image absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity" data-url="${url}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            `).join('');
                            
                            // Add remove image event listeners
                            existingImagesGrid.querySelectorAll('.remove-existing-image').forEach(btn => {
                                btn.addEventListener('click', () => {
                                    const url = btn.dataset.url;
                                    editImagesToDelete.push(url);
                                    btn.closest('[data-image-url]').remove();
                                    
                                    // Hide section if no more images
                                    if (existingImagesGrid.children.length === 0) {
                                        existingImagesDiv.classList.add('hidden');
                                    }
                                });
                            });
                        } else {
                            existingImagesDiv.classList.add('hidden');
                        }
                        
                        // Clear new image previews
                        document.getElementById('edit-new-image-previews').innerHTML = '';
                        document.getElementById('edit-new-image-previews').classList.add('hidden');
                        document.getElementById('edit-images-input').value = '';
                        
                        // Show modal
                        modal.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error fetching post:', error);
                    showToast('error', 'Failed to load post data');
                });
        }
        
        function closeEditModal() {
            const modal = document.getElementById('edit-post-modal');
            modal.classList.add('hidden');
            currentEditPostId = null;
            editRatingValue = 0;
            editImagesToDelete = [];
        }
        
        function updateEditStars() {
            const stars = document.querySelectorAll('.edit-star-btn');
            stars.forEach((star, idx) => {
                if (idx < editRatingValue) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
            document.getElementById('edit-rating-input').value = editRatingValue;
        }
        
        function updateEditCharCount() {
            const textarea = document.getElementById('edit-post-content');
            const charCount = document.getElementById('edit-char-count');
            charCount.textContent = textarea.value.length;
        }
        
        function handleEditSubmit(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-edit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
            
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('content', document.getElementById('edit-post-content').value);
            
            if (editRatingValue > 0) {
                formData.append('rating', editRatingValue);
            }
            
            // Add images to delete
            if (editImagesToDelete.length > 0) {
                editImagesToDelete.forEach(url => {
                    formData.append('images_to_delete[]', url);
                });
            }
            
            // Add new images
            const newImages = document.getElementById('edit-images-input').files;
            for (let i = 0; i < newImages.length; i++) {
                formData.append('images[]', newImages[i]);
            }
            
            fetch(`{{ url('community/posts') }}/${currentEditPostId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message || 'Post updated successfully!');
                    closeEditModal();
                    window.location.reload();
                } else {
                    showToast('error', data.message || 'Failed to update post');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while updating the post');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Update Post';
            });
        }
        
        // ========================================
        // DELETE POST MODAL FUNCTIONS
        // ========================================
        let currentDeletePostId = null;
        
        function openDeleteModal(postId) {
            currentDeletePostId = postId;
            document.getElementById('delete-post-modal').classList.remove('hidden');
        }
        
        function closeDeleteModal() {
            document.getElementById('delete-post-modal').classList.add('hidden');
            currentDeletePostId = null;
        }
        
        function confirmDeletePost() {
            if (!currentDeletePostId) return;
            
            const confirmBtn = document.getElementById('confirm-delete-btn');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Deleting...';
            
            fetch(`{{ url('community/posts') }}/${currentDeletePostId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message || 'Post deleted successfully!');
                    closeDeleteModal();
                    
                    // Remove post card from DOM
                    const postCard = document.querySelector(`[data-post-id="${currentDeletePostId}"]`);
                    if (postCard) {
                        postCard.remove();
                    }
                } else {
                    showToast('error', data.message || 'Failed to delete post');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while deleting the post');
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Delete Post';
            });
        }
        
        // Handle like/unlike
        function handleLike(postId, card) {
            fetch(`{{ url('community/posts') }}/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const likeBtn = card.querySelector('.like-btn');
                    const likeCount = card.querySelector('.like-count');
                    const likeIcon = likeBtn.querySelector('span');
                    
                    if (data.is_liked) {
                        likeIcon.textContent = '❤️';
                        likeBtn.classList.remove('text-gray-500');
                        likeBtn.classList.add('text-red-500');
                    } else {
                        likeIcon.textContent = '🤍';
                        likeBtn.classList.remove('text-red-500');
                        likeBtn.classList.add('text-gray-500');
                    }
                    
                    likeCount.textContent = data.likes_count;
                }
            })
            .catch(error => {
                console.error('Error liking post:', error);
                showToast('error', 'Failed to like post');
            });
        }
        
        // Toggle comments section
        function toggleComments(postId, card) {
            const commentsSection = card.querySelector('.comments-section');
            const commentsList = commentsSection.querySelector('.comments-list');
            
            if (commentsSection.classList.contains('hidden')) {
                commentsSection.classList.remove('hidden');
                
                // Load comments if not already loaded
                if (!commentsList.dataset.loaded) {
                    loadComments(postId, card);
                    commentsList.dataset.loaded = 'true';
                }
            } else {
                commentsSection.classList.add('hidden');
            }
        }
        
        // Load comments for a post
        function loadComments(postId, card) {
            const commentsList = card.querySelector('.comments-list');
            
            fetch(`{{ url('community/posts') }}/${postId}/comments`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        commentsList.innerHTML = '';
                        
                        if (data.comments.data.length === 0) {
                            commentsList.innerHTML = '<p class="text-center text-gray-500 text-sm py-4">No comments yet. Be the first to comment!</p>';
                        } else {
                            data.comments.data.forEach(comment => {
                                commentsList.appendChild(createCommentElement(comment, postId));
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading comments:', error);
                    commentsList.innerHTML = '<p class="text-center text-red-500 text-sm py-4">Failed to load comments</p>';
                });
        }
        
        // Submit a comment
        function submitComment(postId, card, parentId = null) {
            const commentInput = card.querySelector(`.comment-input[data-post-id="${postId}"]`);
            const comment = commentInput.value.trim();
            
            if (!comment) {
                showToast('warning', 'Please enter a comment');
                return;
            }
            
            fetch(`{{ url('community/posts') }}/${postId}/comments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    comment: comment,
                    parent_id: parentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Comment added!');
                    commentInput.value = '';
                    
                    // Update comment count
                    const commentCount = card.querySelector('.comment-count');
                    commentCount.textContent = parseInt(commentCount.textContent) + 1;
                    
                    // Reload comments
                    const commentsList = card.querySelector('.comments-list');
                    commentsList.dataset.loaded = 'false';
                    loadComments(postId, card);
                } else {
                    showToast('error', data.message || 'Failed to add comment');
                }
            })
            .catch(error => {
                console.error('Error submitting comment:', error);
                showToast('error', 'Failed to add comment');
            });
        }
        
        // Create comment element
        function createCommentElement(comment, postId) {
            const div = document.createElement('div');
            div.className = 'comment-item bg-gray-50 rounded-lg p-4';
            
            const userName = comment.user?.display_name || comment.user?.organization_name || comment.user?.name || 'Unknown User';
            const avatarHtml = createAvatarHtml(comment.user, 'w-8 h-8', 'text-xs');
            const formattedDate = new Date(comment.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            
            div.innerHTML = `
                <div class="flex gap-3">
                    ${avatarHtml}
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-sm text-gray-900">${userName}</span>
                            <span class="text-xs text-gray-500">${formattedDate}</span>
                        </div>
                        <p class="text-sm text-gray-700">${escapeHtml(comment.comment)}</p>
                        
                        ${comment.replies && comment.replies.length > 0 ? `
                            <div class="mt-3 ml-4 space-y-3 border-l-2 border-gray-200 pl-4">
                                ${comment.replies.map(reply => {
                                    const replyUserName = reply.user?.display_name || reply.user?.organization_name || reply.user?.name || 'Unknown User';
                                    const replyAvatarHtml = createAvatarHtml(reply.user, 'w-6 h-6', 'text-[10px]');
                                    return `
                                    <div class="flex gap-2">
                                        ${replyAvatarHtml}
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-xs text-gray-900">${replyUserName}</span>
                                                <span class="text-xs text-gray-500">${new Date(reply.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</span>
                                            </div>
                                            <p class="text-xs text-gray-700 mt-1">${escapeHtml(reply.comment)}</p>
                                        </div>
                                    </div>
                                `;}).join('')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            return div;
        }
        
        // Image modal (simple version)
        function openImageModal(url) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-[9999] bg-black bg-opacity-90 flex items-center justify-center p-4';
            modal.innerHTML = `
                <img src="${url}" class="max-w-full max-h-full object-contain">
                <button class="absolute top-4 right-4 text-white text-4xl hover:text-gray-300">&times;</button>
            `;
            
            modal.addEventListener('click', () => {
                document.body.removeChild(modal);
            });
            
            document.body.appendChild(modal);
        }
        
        window.openImageModal = openImageModal;
    });
</script>
@endpush