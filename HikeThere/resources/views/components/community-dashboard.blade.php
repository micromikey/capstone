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
<!-- Hero Section -->
<div id="community-hero" class="relative bg-gradient-to-r from-purple-500 via-purple-300 to-pink-500 text-white overflow-hidden hero-container">
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
    
    <!-- Enhanced Community Elements Background -->
    <div class="absolute inset-0 opacity-15">
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
    
    <div class="relative max-w-7xl mx-auto px-6 py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">Trail Connections</h1>
            <p class="text-xl md:text-2xl text-purple-100 mb-8">Discover hiking organizations that match your interests</p>

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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
                        <img src="{{ $organization->profile_picture_url }}" alt="{{ $organization->display_name }}"
                            class="w-full h-48 object-cover rounded-t-xl">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        function showToast(type, message) {
            const toast = document.getElementById(type + '-toast');
            const messageSpan = document.getElementById(type + '-message');

            messageSpan.textContent = message;
            toast.classList.remove('translate-x-full');

            setTimeout(() => {
                toast.classList.add('translate-x-full');
            }, 3000);
        }
    
        // Add lightweight styles for tabs and event cards when Tailwind utilities aren't sufficient
        const extraStyles = document.createElement('style');
        extraStyles.textContent = `
            .tab-button { transition: all .18s ease; }
            .tab-button[aria-selected="true"] { box-shadow: 0 8px 20px rgba(16,185,129,0.12); }
            .tab-button:focus { outline: 3px solid rgba(56,189,248,0.18); outline-offset: 2px; }
            .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        `;
        document.head.appendChild(extraStyles);
    });
</script>
@endpush