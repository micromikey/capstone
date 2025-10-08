<x-app-layout>
    @php
        // Fetch trail images ONCE at the top to avoid duplicate calls and ensure consistency
        $imageService = app(App\Services\TrailImageService::class);
        $allImages = $imageService->getTrailImages($trail, 10);
    @endphp

    <!-- Floating Navigation -->
    <x-floating-navigation :sections="[
        ['id' => 'trail-gallery', 'title' => 'Trail', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/>'],
        ['id' => 'trail-stats', 'title' => 'Trail Stats', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z\'/>'],
        ['id' => 'trail-info', 'title' => 'Trail Information', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/>'],
        ['id' => 'community-events', 'title' => 'Community Events', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'/>'],
        ['id' => 'trail-map', 'title' => 'Trail Map & Route', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7\'/>'],
        ['id' => 'weather-info', 'title' => 'Weather Information', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z\'/>'],
        ['id' => 'reviews-section', 'title' => 'Reviews & Ratings', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z\'/>'],
        ['id' => 'trail-actions', 'title' => 'Book Trail', 'icon' => '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'/>']
    ]" />

    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $trail->trail_name }}
            </h2>
            
            <div class="flex items-center gap-3">
                <!-- Favorite Button -->
                <button id="favorite-btn" data-trail-id="{{ $trail->id }}" data-trail-slug="{{ $trail->slug }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg id="favorite-icon" class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.343l-6.828-6.829a4 4 0 010-5.656z" />
                    </svg>
                    <span id="favorite-text">Save</span>
                    <span id="favorite-count" class="ml-2 text-sm text-white/80" style="display: none;">({{ $trail->favoritedBy()->count() }})</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Trail Image Gallery -->
                <div id="trail-gallery" class="relative h-96 bg-gray-200" x-data="trailGallery()" x-init="init()">
                    <!-- Main Image -->
                    <div class="w-full h-full overflow-hidden cursor-pointer group" @click="openMainImage()">
                        <img x-show="currentImage" 
                             :src="currentImage" 
                             :alt="'{{ $trail->trail_name }} - Image ' + (currentIndex + 1)"
                             loading="eager"
                             fetchpriority="high"
                             class="w-full h-full object-cover transition-all duration-300 group-hover:scale-105">
                        
                        <!-- Click to expand hint -->
                        <div x-show="currentImage" class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-300 flex items-center justify-center pointer-events-none">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white/90 rounded-full p-3">
                                <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </div>
                        </div>
                        
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
                    <div id="trail-stats" class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
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
                    <div id="trail-info" class="flex flex-col md:flex-row gap-6 mb-8">
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
                                    <a href="{{ route('community.organization.show', $trail->user_id) }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity flex-1">
                                        @if($trail->user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $trail->user->profile_photo_path) }}" alt="{{ $trail->user->display_name }}" class="w-12 h-12 rounded-full object-cover">
                                        @else
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($trail->user->display_name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $trail->user->display_name)[1] ?? '', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900 hover:text-green-600 transition-colors">{{ $trail->user->display_name }}</p>
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
                                    </a>
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

                    <!-- Events Section -->
                    <div id="community-events" class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Community Events</h3>
                            <a href="{{ route('community.index', ['tab' => 'events']) }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">View All Events</a>
                        </div>
                        
                        @php
                            $now = \Carbon\Carbon::now();
                        @endphp
                        
                        @if($relatedEvents->count() > 0)
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                @foreach($relatedEvents as $event)
                                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-250 p-6 flex flex-col h-full" style="min-height:16rem;">
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
                                            @if($event->trail && $event->trail->location)
                                                <p class="text-sm text-gray-600 mt-1">📍 {{ $event->trail->location->name }}, {{ $event->trail->location->province }}</p>
                                            @endif
                                            @if($event->description)
                                            <p class="mt-3 text-sm text-gray-600 line-clamp-3">{{ Str::limit($event->description, 140) }}</p>
                                            @endif

                                            @if(isset($event->end_at) && $event->end_at->greaterThan($now))
                                                @php
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

                                        @if($event->capacity)
                                            <span class="text-sm font-semibold text-blue-700 px-3 py-1 bg-blue-50 rounded-full">{{ $event->capacity }} max</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-xl p-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No Events Found</h4>
                                <p class="text-gray-600 mb-4">There are currently no events related to this trail location.</p>
                                <a href="{{ route('community.index', ['tab' => 'events']) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Browse All Events
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Interactive Trail Map with Route -->
                    @if($trail->coordinates && count($trail->coordinates) > 0)
                        <div id="trail-map" class="mb-8">
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
                        <div id="trail-map" class="mb-8">
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

                    <!-- Weather Information -->
                    @if($trail->coordinates && count($trail->coordinates) > 0)
                        <div id="weather-info" class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Weather Information</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Current Weather -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-800 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                        </svg>
                                        Current Weather
                                    </h4>
                                    <div id="current-weather" class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100 shadow-sm">
                                        <div class="flex items-center justify-center h-24">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 5-Day Forecast -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-800 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        5-Day Forecast
                                    </h4>
                                    <div id="forecast-weather" class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-6 border border-green-100 shadow-sm">
                                        <div class="flex items-center justify-center h-24">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Reviews Section -->
                    <div id="reviews-section" class="mb-8">
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
                                                @if($review->user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $review->user->profile_photo_path) }}" alt="{{ $review->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center text-white font-semibold text-sm">
                                                        {{ strtoupper(substr($review->user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $review->user->name)[1] ?? '', 0, 1)) }}
                                                    </div>
                                                @endif
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
                                                    @php
                                                        $reviewImages = $review->review_images;
                                                        if (is_string($reviewImages)) {
                                                            $reviewImages = json_decode($reviewImages, true) ?? [];
                                                        }
                                                        $reviewImages = is_array($reviewImages) ? $reviewImages : [];
                                                    @endphp
                                                    @if(!empty($reviewImages))
                                                        <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2">
                                                            @foreach($reviewImages as $image)
                                                                <div class="relative group">
                                                        <img src="{{ asset('storage/' . $image['path']) }}" 
                                                            alt="Review photo" 
                                                            loading="lazy"
                                                            decoding="async"
                                                            class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity review-image"
                                                            data-image-src="{{ asset('storage/' . $image['path']) }}"
                                                            data-image-caption="{{ $review->user->name }}">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    
                                                    @php
                                                        $conditions = $review->conditions;
                                                        if (is_string($conditions)) {
                                                            $conditions = json_decode($conditions, true) ?? [];
                                                        }
                                                        $conditions = is_array($conditions) ? $conditions : [];
                                                    @endphp
                                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                                        <span>Hiked on {{ $review->hike_date->format('M d, Y') }}</span>
                                                        @if(!empty($conditions))
                                                            <span>Conditions: {{ implode(', ', $conditions) }}</span>
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
                    <div id="trail-actions" class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                        <button id="book-trail-btn" data-trail-id="{{ $trail->id }}" data-trail-slug="{{ $trail->slug }}" data-organization-id="{{ $trail->user_id }}" data-organization-name="{{ $trail->user->display_name }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Book This Trail
                        </button>
                        <button id="build-itinerary-btn" data-trail-id="{{ $trail->id }}" data-trail-slug="{{ $trail->slug }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Build Itinerary
                        </button>
                        @auth
                        @if(auth()->user()->user_type === 'hiker')
                        <button onclick="openReportIncidentModal()" class="bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center" title="Report Safety Issue">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.732-1.333-2.464 0L4.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </button>
                        @endif
                        @endauth
                        <button id="original-favorite-btn" data-trail-id="{{ $trail->id }}" data-trail-slug="{{ $trail->slug }}" class="bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                            <svg id="original-favorite-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.343l-6.828-6.829a4 4 0 010-5.656z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Additional Images Section -->
                @php
                    // Fetch trail images once and reuse (avoid duplicate calls)
                    if (!isset($allImages)) {
                        $imageService = app(App\Services\TrailImageService::class);
                        $allImages = $imageService->getTrailImages($trail, 10);
                    }
                @endphp
                
                @if(count($allImages) > 1)
                    <div class="border-t border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Trail Photos ({{ count($allImages) }})</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($allImages as $index => $image)
                                <button data-index="{{ $index }}"
                                        class="relative aspect-square rounded-lg overflow-hidden hover:opacity-75 transition-all transform hover:scale-105 group gallery-thumb cursor-pointer">
                                    <img src="{{ $image['url'] }}" 
                                         alt="{{ $image['caption'] }}"
                                         loading="lazy"
                                         decoding="async"
                                         class="w-full h-full object-cover">
                                    
                                    <!-- Expand icon overlay -->
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-200 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                        </svg>
                                    </div>
                                    
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

    <!-- Floating Actions Bar -->
    <div id="floating-actions" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg transform translate-y-full transition-transform duration-300 z-40">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row gap-4 p-4">
                <button id="floating-book-trail-btn" data-trail-id="{{ $trail->id }}" data-trail-slug="{{ $trail->slug }}" data-organization-id="{{ $trail->user_id }}" data-organization-name="{{ $trail->user->display_name }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Book This Trail
                </button>
                <button id="floating-build-itinerary-btn" data-trail-id="{{ $trail->id }}" data-trail-slug="{{ $trail->slug }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Build Itinerary
                </button>
                <button id="floating-favorite-btn" data-trail-id="{{ $trail->id }}" data-trail-slug="{{ $trail->slug }}" class="bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                    <svg id="floating-favorite-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.343l-6.828-6.829a4 4 0 010-5.656z" />
                    </svg>
                </button>
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
        // Initialize favorite functionality for multiple buttons
        initializeFavoriteButtons();
        
        function initializeFavoriteButtons() {
            // Get all favorite buttons (header, original actions, floating actions)
            const favoriteButtons = [
                {
                    btn: document.getElementById('favorite-btn'),
                    text: document.getElementById('favorite-text'),
                    icon: document.getElementById('favorite-icon'),
                    count: document.getElementById('favorite-count')
                },
                {
                    btn: document.getElementById('original-favorite-btn'),
                    text: null,
                    icon: document.getElementById('original-favorite-icon'),
                    count: null
                },
                {
                    btn: document.getElementById('floating-favorite-btn'),
                    text: null,
                    icon: document.getElementById('floating-favorite-icon'),
                    count: null
                }
            ].filter(item => item.btn); // Only keep buttons that exist

            if (favoriteButtons.length === 0) return;

            // Get trail data from the first available button
            const firstBtn = favoriteButtons[0].btn;
            const trailId = firstBtn.dataset.trailId;
            const trailSlug = firstBtn.dataset.trailSlug;

            // Determine initial state for authenticated user
            let isFavorited = false;

            // Helper to update UI for all buttons
            function updateFavoriteUI(state, count) {
                isFavorited = state;
                
                favoriteButtons.forEach(({ btn, text, icon, count: countEl }) => {
                    if (state) {
                        // Saved state: Pink background
                        btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                        btn.classList.add('bg-pink-500', 'hover:bg-pink-600');
                        btn.classList.remove('text-gray-800');
                        btn.classList.add('text-white');
                        if (text) text.textContent = 'Saved';
                        if (icon) {
                            icon.classList.add('text-white');
                            icon.classList.remove('text-rose-500');
                        }
                    } else {
                        // Unsaved state: Green background
                        btn.classList.remove('bg-pink-500', 'hover:bg-pink-600');
                        btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                        btn.classList.remove('text-gray-800');
                        btn.classList.add('text-white');
                        if (text) text.textContent = 'Save';
                        if (icon) {
                            icon.classList.remove('text-rose-500', 'text-white');
                        }
                    }
                    // Hide the count display if it exists
                    if (countEl) countEl.style.display = 'none';
                });
            }

        // Prefer session-based check first (works for users logged in via session)
        fetch(`/trails/${trailSlug}/is-favorited`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
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

            // Add click event listeners to all favorite buttons
            favoriteButtons.forEach(({ btn }) => {
                btn.addEventListener('click', function(){
                    // Disable all buttons during request
                    favoriteButtons.forEach(({ btn: b }) => b.disabled = true);
                    
                    const payload = { trail_id: trailId };

                    const doRequest = async (url, isApi = false) => {
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

                    // Use web route with session auth directly (since user is authenticated to view this page)
                    const performToggle = async () => {
                        try {
                            // Use web route with session authentication
                            let response = await doRequest('/trails/favorite/toggle', false);
                            
                            if (!response.ok) {
                                console.error('Response not OK:', response.status, response.statusText);
                                throw new Error(`Network response was not ok: ${response.status} ${response.statusText}`);
                            }

                            const data = await response.json();
                            
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
                            
                        } catch (error) {
                            console.error('Error toggling favorite:', error);
                            console.error('Error details:', error.message);
                            showToast('error', 'Unable to update favorites: ' + error.message);
                        } finally {
                            // Re-enable all buttons
                            favoriteButtons.forEach(({ btn: b }) => b.disabled = false);
                        }
                    };

                    performToggle();
                });
            });
        }

        // Initialize build itinerary functionality
        initializeBuildItineraryButtons();
        
        // Initialize book trail functionality
        initializeBookTrailButtons();
        
        function initializeBookTrailButtons() {
            const bookBtns = [
                document.getElementById('book-trail-btn'),
                document.getElementById('floating-book-trail-btn')
            ].filter(btn => btn);

            if (bookBtns.length === 0) return;

            bookBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const trailId = btn.dataset.trailId;
                    const trailSlug = btn.dataset.trailSlug;
                    const organizationId = btn.dataset.organizationId;
                    const organizationName = btn.dataset.organizationName;
                    
                    if (!trailId || !organizationId) {
                        showToast('error', 'Trail or organization information not found');
                        return;
                    }

                    // Build the booking URL with pre-populated data
                    const bookingUrl = new URL('{{ route("booking.details") }}', window.location.origin);
                    bookingUrl.searchParams.set('trail_id', trailId);
                    bookingUrl.searchParams.set('organization_id', organizationId);
                    
                    // Redirect to booking page with populated data
                    window.location.href = bookingUrl.toString();
                });
            });
        }
        
        function initializeBuildItineraryButtons() {
            const buildBtns = [
                document.getElementById('build-itinerary-btn'),
                document.getElementById('floating-build-itinerary-btn')
            ].filter(btn => btn);

            if (buildBtns.length === 0) return;

            buildBtns.forEach(btn => {
                btn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    const trailId = btn.dataset.trailId;
                    const trailSlug = btn.dataset.trailSlug;
                    
                    if (!trailId || !trailSlug) {
                        showToast('error', 'Trail information not found');
                        return;
                    }

                    // Disable button during check
                    btn.disabled = true;
                    btn.textContent = 'Checking...';

                    try {
                        // Check if user has completed assessment
                        const response = await fetch('/api/user/assessment-status', {
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to check assessment status');
                        }

                        const data = await response.json();

                        if (data.success && data.has_assessment) {
                            // User has assessment, proceed to build itinerary
                            window.location.href = `/itinerary/build/${trailSlug}`;
                        } else {
                            // User needs to complete assessment first
                            showAssessmentModal();
                        }

                    } catch (error) {
                        console.error('Error checking assessment:', error);
                        showToast('error', 'Unable to check assessment status. Please try again.');
                    } finally {
                        // Re-enable button
                        btn.disabled = false;
                        btn.textContent = 'Build Itinerary';
                    }
                });
            });
        }

        function showAssessmentModal() {
            const modal = document.getElementById('assessment-modal');
            if (modal) {
                modal.style.display = 'block';
            }
        }

        function hideAssessmentModal() {
            const modal = document.getElementById('assessment-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Assessment modal event listeners
        const cancelBtn = document.getElementById('cancel-assessment');
        const startBtn = document.getElementById('start-assessment');

        if (cancelBtn) {
            cancelBtn.addEventListener('click', hideAssessmentModal);
        }

        if (startBtn) {
            startBtn.addEventListener('click', function() {
                window.location.href = '{{ route("assessment.instruction") }}';
            });
        }

        // Close modal when clicking outside
        const modal = document.getElementById('assessment-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    hideAssessmentModal();
                }
            });
        }
    });
</script>

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
                            <a class="toast-link text-xs font-medium mt-2 inline-flex items-center gap-1 hover:gap-2 transition-all hidden">
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

    <!-- Assessment Required Modal -->
    <div id="assessment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Assessment Required</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        To build a personalized itinerary for this trail, please complete the Pre-Hike Self-Assessment first. This will help us create recommendations tailored to your fitness level and experience.
                    </p>
                </div>
                <div class="flex justify-center gap-4 px-4 py-3">
                    <button id="cancel-assessment" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button id="start-assessment" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Start Assessment
                    </button>
                </div>
            </div>
        </div>
    </div>
 
     <!-- Image Modal -->
     <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden" onclick="closeImageModal()">
         <div class="flex items-center justify-center min-h-screen p-4">
             <div class="relative max-w-4xl max-h-full" onclick="event.stopPropagation()">
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
        // Prepare gallery image arrays for JS (using images fetched at top of file)
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

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.5);
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.7);
        }

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
                },
                
                openMainImage() {
                    if (this.currentImage) {
                        const caption = this.captions[this.currentIndex] || '{{ $trail->trail_name }}';
                        openImageModal(this.currentImage, caption);
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
                const ratingStarsContainer = document.getElementById('rating-stars');

                // Check if review form exists before initializing
                if (!reviewForm) {
                    console.debug('Review form not found on page');
                    return;
                }

                // Rating stars functionality
                ratingStars.forEach((star, index) => {
                    star.addEventListener('click', function() {
                        selectedRating = index + 1;
                        if (ratingInput) ratingInput.value = selectedRating;
                        updateStarDisplay(selectedRating);
                    });

                    star.addEventListener('mouseenter', function() {
                        updateStarDisplay(index + 1);
                    });
                });

                // Reset stars on mouse leave
                if (ratingStarsContainer) {
                    ratingStarsContainer.addEventListener('mouseleave', function() {
                        updateStarDisplay(selectedRating);
                    });
                }

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
                    
                    // Optional link (backwards compatibility)
                    const linkHref = opts.link || opts.viewLink;
                    const linkText = opts.linkText || (opts.viewLink ? 'View Saved Trails »' : null);
                    if (linkHref && linkText) {
                        const linkEl = toast.querySelector('.toast-link');
                        linkEl.href = linkHref;
                        linkEl.querySelector('.link-text').textContent = linkText;
                        linkEl.classList.remove('hidden');
                        linkEl.style.display = 'inline-flex';
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

            // Initialize weather data if coordinates are available
            initializeWeatherData();
            
            // Initialize floating actions
            initializeFloatingActions();
        });

        // Floating Actions functionality
        function initializeFloatingActions() {
            const originalActions = document.getElementById('trail-actions');
            const floatingActions = document.getElementById('floating-actions');
            
            if (!originalActions || !floatingActions) return;

            let originalPosition = null;
            let isFloating = false;

            function updateFloatingActions() {
                // Get the position of the original actions element
                const rect = originalActions.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                
                // Calculate the original position relative to the document
                if (originalPosition === null) {
                    originalPosition = rect.top + window.pageYOffset;
                }

                // Check if the original actions are below the viewport
                const shouldFloat = rect.top > windowHeight - 100; // 100px buffer
                
                if (shouldFloat && !isFloating) {
                    // Show floating actions
                    isFloating = true;
                    floatingActions.classList.remove('translate-y-full');
                    floatingActions.classList.add('translate-y-0');
                } else if (!shouldFloat && isFloating) {
                    // Hide floating actions
                    isFloating = false;
                    floatingActions.classList.remove('translate-y-0');
                    floatingActions.classList.add('translate-y-full');
                }
            }

            // Listen for scroll events
            let ticking = false;
            function onScroll() {
                if (!ticking) {
                    requestAnimationFrame(function() {
                        updateFloatingActions();
                        ticking = false;
                    });
                    ticking = true;
                }
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', updateFloatingActions);
            
            // Initial check
            setTimeout(updateFloatingActions, 100);
        }

        // Weather functionality with caching
        const WEATHER_CACHE_DURATION_MS = 2 * 60 * 1000; // 2 minutes cache
        let weatherFetchInProgress = false;

        // Helper: Get cached weather data if still valid
        function getCachedWeather(cacheKey) {
            try {
                const cached = localStorage.getItem(cacheKey);
                if (!cached) return null;
                
                const data = JSON.parse(cached);
                const age = Date.now() - (data.timestamp || 0);
                
                if (age < WEATHER_CACHE_DURATION_MS) {
                    console.debug('Using cached weather data (age: ' + Math.round(age / 1000) + 's)');
                    return data;
                }
            } catch (e) {
                console.warn('Failed to read weather cache:', e);
            }
            return null;
        }

        // Helper: Save weather data to cache
        function cacheWeather(cacheKey, weatherData) {
            try {
                localStorage.setItem(cacheKey, JSON.stringify({
                    ...weatherData,
                    timestamp: Date.now()
                }));
            } catch (e) {
                console.warn('Failed to cache weather:', e);
            }
        }

        function initializeWeatherData() {
            const trailCoords = JSON.parse(document.getElementById('trail-coordinates').textContent || '[]');
            
            if (!trailCoords || trailCoords.length === 0) {
                console.warn('No trail coordinates available for weather');
                displayWeatherError('current-weather', 'Location data unavailable');
                displayWeatherError('forecast-weather', 'Location data unavailable');
                return; // No coordinates available for weather
            }

            // Use the first coordinate (trail start) for weather data
            const startCoord = trailCoords[0];
            const lat = startCoord.lat;
            const lng = startCoord.lng;

            // Validate coordinates
            if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
                console.error('Invalid coordinates:', { lat, lng });
                displayWeatherError('current-weather', 'Invalid location data');
                displayWeatherError('forecast-weather', 'Invalid location data');
                return;
            }

            // Create cache keys based on coordinates
            const currentWeatherCacheKey = `trail_weather_current_${lat.toFixed(4)}_${lng.toFixed(4)}`;
            const forecastCacheKey = `trail_weather_forecast_${lat.toFixed(4)}_${lng.toFixed(4)}`;

            // Try to load from cache first
            const cachedCurrent = getCachedWeather(currentWeatherCacheKey);
            const cachedForecast = getCachedWeather(forecastCacheKey);

            if (cachedCurrent) {
                console.log('Loading current weather from cache');
                displayCurrentWeather(cachedCurrent.data);
            }

            if (cachedForecast) {
                console.log('Loading forecast from cache');
                displayForecast(cachedForecast.data);
                displayHourlyForecast(cachedForecast.data.hourly || []);
            }

            // Fetch fresh data (will update the UI and cache)
            if (!weatherFetchInProgress) {
                fetchCurrentWeather(lat, lng, currentWeatherCacheKey);
                fetchForecast(lat, lng, forecastCacheKey);
            }
        }

        function fetchCurrentWeather(lat, lng, cacheKey) {
            if (weatherFetchInProgress) return;
            weatherFetchInProgress = true;

            fetch(`/api/weather?lat=${lat}&lng=${lng}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        displayWeatherError('current-weather', 'Unable to load current weather');
                        return;
                    }
                    
                    // Cache the data
                    cacheWeather(cacheKey, { data: data });
                    
                    // Display the data
                    displayCurrentWeather(data);
                    
                    weatherFetchInProgress = false;
                })
                .catch(error => {
                    console.error('Error fetching current weather:', error);
                    displayWeatherError('current-weather', 'Failed to load weather data');
                    weatherFetchInProgress = false;
                });
        }

        function fetchForecast(lat, lng, cacheKey) {
            fetch(`/api/weather/forecast?lat=${lat}&lng=${lng}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        displayWeatherError('forecast-weather', 'Unable to load forecast');
                        displayHourlyError();
                        return;
                    }
                    
                    // Cache the data
                    cacheWeather(cacheKey, { data: data });
                    
                    // Display the data
                    displayForecast(data);
                    displayHourlyForecast(data.hourly || []);
                })
                .catch(error => {
                    console.error('Error fetching forecast:', error);
                    displayWeatherError('forecast-weather', 'Failed to load forecast data');
                    displayHourlyError();
                });
        }

        function displayCurrentWeather(data) {
            const container = document.getElementById('current-weather');
            const weatherIcon = getWeatherIcon(data.condition_code, data.icon);
            
            container.innerHTML = `
                <div>
                    <!-- Main Weather Info -->
                    <div class="text-center mb-4">
                        <div class="flex items-center justify-center mb-3">
                            <div class="p-3 bg-white rounded-full shadow-sm mr-4">
                                ${weatherIcon}
                            </div>
                            <div class="text-left">
                                <div class="text-3xl font-bold text-gray-900">${data.temperature}°C</div>
                                <div class="text-lg text-gray-600 capitalize">${data.condition}</div>
                                <div class="text-sm text-gray-500">Feels like ${data.feels_like}°C</div>
                            </div>
                        </div>
                    </div>

                    <!-- Weather Details Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b border-blue-100">
                        <div class="text-center">
                            <div class="text-sm text-gray-500">Humidity</div>
                            <div class="text-lg font-semibold text-gray-800">${data.humidity}%</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-500">Wind Speed</div>
                            <div class="text-lg font-semibold text-gray-800">${data.wind_speed} km/h</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-500">UV Index</div>
                            <div class="text-lg font-semibold text-gray-800">${data.uvIndex}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-500">Pressure</div>
                            <div class="text-lg font-semibold text-gray-800">${data.pressure} hPa</div>
                        </div>
                    </div>

                    <!-- Hourly Forecast -->
                    <div id="hourly-forecast-container">
                        <div class="flex items-center justify-center">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                            <span class="ml-2 text-sm text-gray-500">Loading hourly forecast...</span>
                        </div>
                    </div>
                </div>
            `;

            // Store current weather data for hourly forecast
            window.currentWeatherData = data;
        }

        function displayForecast(data) {
            const container = document.getElementById('forecast-weather');
            
            if (!data.forecast || data.forecast.length === 0) {
                displayWeatherError('forecast-weather', 'No forecast data available');
                return;
            }

            const forecastHTML = data.forecast.map((day, index) => {
                const weatherIcon = getWeatherIcon(day.condition_code, day.icon);
                const isToday = day.day_label === 'TODAY';
                const isTomorrow = day.day_label === 'TOMORROW';
                
                return `
                    <div class="flex items-center justify-between py-3 px-2 rounded-lg hover:bg-green-100 transition-colors ${isToday ? 'bg-green-100 border border-green-600' : ''} ${index < data.forecast.length - 1 ? 'border-b border-green-100' : ''}">
                        <div class="flex items-center flex-1">
                            <div class="p-2 bg-white rounded-full shadow-sm mr-3">
                                ${weatherIcon}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900 ${isToday ? 'text-green-600' : ''}">${day.day_label}</div>
                                <div class="text-xs text-gray-600">${day.date_formatted}</div>
                                <div class="text-xs text-gray-500 capitalize">${day.condition}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">${day.temp_max}°<span class="text-gray-500">/${day.temp_min}°</span></div>
                            <div class="text-xs text-gray-600">${day.precipitation}% rain</div>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = `<div class="space-y-1">${forecastHTML}</div>`;
        }

        function displayHourlyForecast(hourlyData) {
            const container = document.getElementById('hourly-forecast-container');
            
            if (!hourlyData || hourlyData.length === 0) {
                displayHourlyError();
                return;
            }

            // Take up to 8 hours (24 hours worth from 3-hourly data)
            const hours = hourlyData.slice(0, 8);
            
            const hourlyHTML = hours.map((hour, index) => {
                const weatherIcon = getWeatherIcon(hour.condition, hour.icon);
                const isCurrentHour = index === 0;
                
                return `
                    <div class="flex-shrink-0 text-center min-w-[60px] ${isCurrentHour ? 'bg-blue-100 rounded-lg p-2 border border-blue-300' : 'p-2'}">
                        <div class="text-xs font-medium text-gray-700 mb-1">${hour.time}</div>
                        <div class="mb-2">${weatherIcon}</div>
                        <div class="text-sm font-semibold text-gray-900">${hour.temp}°</div>
                        <div class="text-xs text-blue-600 mt-1">${hour.precipitation}%</div>
                    </div>
                `;
            }).join('');

            container.innerHTML = `
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-3">24-Hour Forecast</h5>
                    <div class="flex space-x-1 overflow-x-auto pb-2 custom-scrollbar">
                        ${hourlyHTML}
                    </div>
                </div>
            `;
        }

        function displayHourlyError() {
            const container = document.getElementById('hourly-forecast-container');
            if (container) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500">Hourly forecast unavailable</p>
                    </div>
                `;
            }
        }

        function displayWeatherError(containerId, message) {
            const container = document.getElementById(containerId);
            container.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <div class="p-3 bg-white rounded-full inline-block shadow-sm mb-3">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium">${message}</p>
                    <p class="text-xs text-gray-400 mt-1">Please try again later</p>
                </div>
            `;
        }

        function getWeatherIcon(conditionCode, iconCode) {
            // Map weather conditions to icons
            const iconMap = {
                'Clear': '<svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/></svg>',
                'Clouds': '<svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M4.5 9.75a6 6 0 0111.573-2.226 3.75 3.75 0 014.133 4.303A4.5 4.5 0 0118 20.25H6.75a5.25 5.25 0 01-2.23-10.004 6.072 6.072 0 01-.02-.496z" clip-rule="evenodd" /></svg>',
                'Rain': '<svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6.083 9c.38-2.708 2.687-4.958 5.529-4.958 2.844 0 5.152 2.25 5.531 4.958.29-.085.598-.125.92-.125 1.657 0 3 1.343 3 3s-1.343 3-3 3H6.937c-1.657 0-3-1.343-3-3s1.343-3 3-3c.322 0 .63.04.92.125z"/><path d="M8 16l1 3m3-3l1.5 4m3-4l1 3"/></svg>',
                'Drizzle': '<svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M6.083 9c.38-2.708 2.687-4.958 5.529-4.958 2.844 0 5.152 2.25 5.531 4.958.29-.085.598-.125.92-.125 1.657 0 3 1.343 3 3s-1.343 3-3 3H6.937c-1.657 0-3-1.343-3-3s1.343-3 3-3c.322 0 .63.04.92.125z"/><path d="M9 17l.5 2M12.5 17l.5 2M16 17l.5 2"/></svg>',
                'Thunderstorm': '<svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24"><path d="M6.083 9c.38-2.708 2.687-4.958 5.529-4.958 2.844 0 5.152 2.25 5.531 4.958.29-.085.598-.125.92-.125 1.657 0 3 1.343 3 3s-1.343 3-3 3H6.937c-1.657 0-3-1.343-3-3s1.343-3 3-3c.322 0 .63.04.92.125z"/><path d="M13 14l-4 6 1.5-2.5-1.5-1.5h2l2-3.5h-2l1.5-1.5z" fill="#7c3aed"/></svg>',
                'Snow': '<svg class="w-6 h-6 text-blue-300" fill="currentColor" viewBox="0 0 24 24"><path d="M6.083 9c.38-2.708 2.687-4.958 5.529-4.958 2.844 0 5.152 2.25 5.531 4.958.29-.085.598-.125.92-.125 1.657 0 3 1.343 3 3s-1.343 3-3 3H6.937c-1.657 0-3-1.343-3-3s1.343-3 3-3c.322 0 .63.04.92.125z"/><path d="M8 16l1 1m-1 1l1-1m3-1l1 1m-1 1l1-1m3-1l1 1m-1 1l1-1" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>',
                'Mist': '<svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M3 8h18c.6 0 1 .4 1 1s-.4 1-1 1H3c-.6 0-1-.4-1-1s.4-1 1-1zM3 12h18c.6 0 1 .4 1 1s-.4 1-1 1H3c-.6 0-1-.4-1-1s.4-1 1-1zM3 16h18c.6 0 1 .4 1 1s-.4 1-1 1H3c-.6 0-1-.4-1-1s.4-1 1-1z"/></svg>',
                'Fog': '<svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M3 8h18c.6 0 1 .4 1 1s-.4 1-1 1H3c-.6 0-1-.4-1-1s.4-1 1-1zM5 12h14c.6 0 1 .4 1 1s-.4 1-1 1H5c-.6 0-1-.4-1-1s.4-1 1-1zM7 16h10c.6 0 1 .4 1 1s-.4 1-1 1H7c-.6 0-1-.4-1-1s.4-1 1-1z"/></svg>',
                'Haze': '<svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M3 8h18c.6 0 1 .4 1 1s-.4 1-1 1H3c-.6 0-1-.4-1-1s.4-1 1-1zM5 12h14c.6 0 1 .4 1 1s-.4 1-1 1H5c-.6 0-1-.4-1-1s.4-1 1-1zM7 16h10c.6 0 1 .4 1 1s-.4 1-1 1H7c-.6 0-1-.4-1-1s.4-1 1-1z"/></svg>'
            };

            return iconMap[conditionCode] || iconMap['Clear'];
        }

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

        // Image Modal Functions (Global scope so they can be called from anywhere)
        function openImageModal(imageUrl, caption) {
            const modal = document.getElementById('image-modal');
            const modalImage = document.getElementById('modal-image');
            const modalCaption = document.getElementById('modal-caption');
            
            if (modal && modalImage) {
                modalImage.src = imageUrl;
                if (modalCaption) {
                    modalCaption.textContent = caption || '';
                }
                modal.classList.remove('hidden');
            }
        }

        function closeImageModal() {
            const modal = document.getElementById('image-modal');
            if (modal) {
                modal.classList.add('hidden');
                // Clear image source to prevent it showing briefly on next open
                const modalImage = document.getElementById('modal-image');
                if (modalImage) {
                    setTimeout(() => { modalImage.src = ''; }, 300);
                }
            }
        }

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

            const trackingStatus = document.getElementById('tracking-status');
            const startTrackingBtn = document.getElementById('start-tracking');
            
            if (!startTrackingBtn) {
                console.error('Start tracking button not found');
                return;
            }

            isTracking = true;
            
            if (trackingStatus) {
                trackingStatus.classList.remove('hidden');
            }
            
            startTrackingBtn.disabled = true;
            startTrackingBtn.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" class="opacity-75"></path></svg>Tracking...';

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

            const distanceElement = document.getElementById('distance-from-trail');
            const progressElement = document.getElementById('progress-percentage');
            
            if (distanceElement) {
                distanceElement.textContent = `Distance from trail: ${distanceText}`;
            }
            
            if (progressElement) {
                progressElement.textContent = `Progress: ${progress}%`;
            }
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
            
            const trackingStatus = document.getElementById('tracking-status');
            const startTrackingBtn = document.getElementById('start-tracking');
            
            if (trackingStatus) {
                trackingStatus.classList.add('hidden');
            }
            
            if (startTrackingBtn) {
                startTrackingBtn.disabled = false;
                startTrackingBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Start Tracking';
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map when page loads
            if (document.getElementById('interactive-trail-map')) {
                initTrailMap();
            }

            // Load elevation profile
            loadElevationProfile();

            // Tracking buttons - check if they exist
            const startTrackingBtn = document.getElementById('start-tracking');
            const stopTrackingBtn = document.getElementById('stop-tracking');
            
            if (startTrackingBtn) {
                startTrackingBtn.addEventListener('click', startTracking);
            }
            
            if (stopTrackingBtn) {
                stopTrackingBtn.addEventListener('click', stopTracking);
            }

            // Gallery thumbnail click handling - open modal with full image
            document.querySelectorAll('.gallery-thumb').forEach(btn => {
                btn.addEventListener('click', function(e){
                    const idx = parseInt(this.getAttribute('data-index'), 10);
                    const img = this.querySelector('img');
                    if (img) {
                        const imageUrl = img.src;
                        const imageCaption = img.alt || '';
                        openImageModal(imageUrl, imageCaption);
                    }
                });
            });

            // Review image modal handling
            document.querySelectorAll('.review-image').forEach(img => {
                img.addEventListener('click', function(){
                    const src = this.dataset.imageSrc;
                    const caption = this.dataset.imageCaption || '';
                    openImageModal(src, caption);
                });
            });

            // ESC key to close modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('image-modal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeImageModal();
                    }
                }
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
    
    <style>
        .line-clamp-3 { 
            display: -webkit-box; 
            -webkit-line-clamp: 3; 
            line-clamp: 3;
            -webkit-box-orient: vertical; 
            overflow: hidden; 
        }
    </style>
</x-app-layout>

<script>
    // Print helper: fetch the print map HTML and print it inside a hidden iframe
    function printTrailMapFromButton(btn){
        // Get the print URL directly from the button's data attribute
        const printUrl = btn.getAttribute('data-print-url');
        if(!printUrl){
            console.error('Print URL not found on button');
            return;
        }
        printTrailMap(printUrl);
    }

    async function printTrailMap(url){
        try{
            // Fetch the rendered print view
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

    // Report Incident Modal Functions
    function openReportIncidentModal() {
        const modal = document.getElementById('reportIncidentModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeReportIncidentModal() {
        const modal = document.getElementById('reportIncidentModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('reportIncidentForm').reset();
        // Clear any error messages
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    // Submit Incident Report
    async function submitIncidentReport(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch('{{ route("hiker.incidents.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Show success message
                alert('Safety incident reported successfully. Thank you for helping keep our trails safe!');
                closeReportIncidentModal();
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorEl = document.getElementById(key + '_error');
                        if (errorEl) {
                            errorEl.textContent = data.errors[key][0];
                        }
                    });
                } else {
                    alert(data.message || 'An error occurred while submitting your report.');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while submitting your report. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    }
</script>

<!-- Report Incident Modal -->
<div id="reportIncidentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="bg-red-600 text-white p-6 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.732-1.333-2.464 0L4.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold">Report Safety Issue</h3>
                </div>
                <button onclick="closeReportIncidentModal()" class="text-white hover:text-red-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="mt-2 text-red-100 text-sm">Help keep our trails safe by reporting any safety issues you've encountered.</p>
        </div>

        <!-- Modal Body -->
        <form id="reportIncidentForm" onsubmit="submitIncidentReport(event)" class="p-6 space-y-6">
            <input type="hidden" name="trail_id" value="{{ $trail->id }}">
            
            <!-- Incident Type -->
            <div>
                <label for="incident_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Incident Type <span class="text-red-500">*</span>
                </label>
                <select name="incident_type" id="incident_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="">Select type...</option>
                    <option value="injury">Injury</option>
                    <option value="accident">Accident</option>
                    <option value="hazard">Trail Hazard</option>
                    <option value="wildlife">Wildlife Encounter</option>
                    <option value="weather">Weather Issue</option>
                    <option value="equipment">Equipment Problem</option>
                    <option value="other">Other</option>
                </select>
                <span id="incident_type_error" class="error-message text-red-500 text-sm mt-1"></span>
            </div>

            <!-- Severity -->
            <div>
                <label for="severity" class="block text-sm font-medium text-gray-700 mb-2">
                    Severity <span class="text-red-500">*</span>
                </label>
                <select name="severity" id="severity" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="">Select severity...</option>
                    <option value="low">Low - Minor issue, no immediate danger</option>
                    <option value="medium">Medium - Moderate concern, caution advised</option>
                    <option value="high">High - Serious issue, significant risk</option>
                    <option value="critical">Critical - Emergency situation</option>
                </select>
                <span id="severity_error" class="error-message text-red-500 text-sm mt-1"></span>
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Specific Location <span class="text-red-500">*</span>
                </label>
                <input type="text" name="location" id="location" required placeholder="e.g., Near kilometer marker 3, at the river crossing..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <span id="location_error" class="error-message text-red-500 text-sm mt-1"></span>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="4" required placeholder="Please provide detailed information about the incident..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                <span id="description_error" class="error-message text-red-500 text-sm mt-1"></span>
            </div>

            <!-- Date & Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="incident_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="incident_date" id="incident_date" required max="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <span id="incident_date_error" class="error-message text-red-500 text-sm mt-1"></span>
                </div>
                <div>
                    <label for="incident_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Approximate Time
                    </label>
                    <input type="time" name="incident_time" id="incident_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <span id="incident_time_error" class="error-message text-red-500 text-sm mt-1"></span>
                </div>
            </div>

            <!-- Contact Info Note -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm text-blue-700">Your contact information from your profile will be included so we can follow up if needed.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t">
                <button type="button" onclick="closeReportIncidentModal()" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                    Submit Report
                </button>
            </div>
        </form>
    </div>
</div>
