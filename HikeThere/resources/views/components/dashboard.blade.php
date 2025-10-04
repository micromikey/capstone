@props(['weather', 'forecast', 'user', 'latestAssessment', 'latestItinerary', 'followedTrails' => collect(), 'followingCount' => 0, 'upcomingEvents' => collect()])

@push('floating-navigation')
@php
$sections = [
['id' => 'welcome-section', 'title' => 'Welcome', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>'],
['id' => 'trail-recommendations', 'title' => 'Trail Recommendations', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>']
];

if(isset($weather) && $weather) {
$sections[] = ['id' => 'weather-section', 'title' => 'Weather', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>'];
}

if(isset($user) && $user && $user->user_type === 'hiker') {
$sections[] = ['id' => 'hiking-tools', 'title' => 'Hiking Tools', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>'];

// Add Events section if there are upcoming events
if(isset($upcomingEvents) && $upcomingEvents->count() > 0) {
$sections[] = ['id' => 'events-section', 'title' => 'Events', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'];
}

if((isset($followedTrails) && $followedTrails->count() > 0)) {
$sections[] = ['id' => 'community-section', 'title' => 'Community', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>'];
} else {
$sections[] = ['id' => 'community-invitation', 'title' => 'Join Community', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>'];
}
}
@endphp

<x-floating-navigation :sections="$sections" />
@endpush

@push('floating-weather')
@if(isset($weather) && $weather)
<x-floating-weather :weather="$weather" />
@endif
@endpush

@php
// Initialize the TrailImageService for dynamic images
$imageService = app('App\Services\TrailImageService');
@endphp

<style>
    .custom-scrollbar::-webkit-scrollbar {
        height: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.4);
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

{{-- Header --}}
<div id="welcome-section" class="relative p-6 lg:p-10 bg-gradient-to-r from-green-100 via-white to-white border-b border-gray-200 rounded-b-xl shadow-sm overflow-hidden min-h-[300px]">

    {{-- Vague Mountain SVG (Right Side) --}}
    <svg class="absolute bottom-0 right-0 w-full md:w-1/2 h-full opacity-75 pointer-events-none select-none"
        viewBox="0 0 800 200" preserveAspectRatio="xMinYMid slice" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="fade-mask" x1="1" y1="0" x2="0" y2="0">
                <stop offset="0.7" stop-color="white" />
                <stop offset="1" stop-color="white" stop-opacity="0" />
            </linearGradient>
            <mask id="fade">
                <rect width="100%" height="100%" fill="url(#fade-mask)" />
            </mask>
        </defs>

        <g mask="url(#fade)">
            <path d="M0 150 Q 50 120, 100 140 Q 150 160, 200 130 Q 250 100, 300 120 Q 350 140, 400 110 Q 450 80, 500 100 Q 550 120, 600 90 Q 650 60, 700 100 Q 750 130, 800 90 L 800 200 L 0 200 Z"
                class="fill-mountain-100" />
            <path d="M0 160 Q 100 130, 200 150 Q 300 170, 400 140 Q 500 110, 600 140 Q 700 170, 800 130 L 800 200 L 0 200 Z"
                class="fill-mountain-200 opacity-80" />
            <path d="M0 180 Q 100 160, 200 170 Q 300 180, 400 160 Q 500 150, 600 160 Q 700 170, 800 150 L 800 200 L 0 200 Z"
                class="fill-mountain-300 opacity-70" />
        </g>
    </svg>

    {{-- Main Content --}}
    <div class="relative z-10">
        <div class="flex items-center space-x-3 md:space-x-4">
            <x-application-logo class="h-10 w-10 md:h-14 md:w-14 flex-shrink-0 object-contain" />
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl md:text-3xl font-bold text-green-800 tracking-tight">
                    HikeThere
                </h1>
                <p class="text-sm text-gray-600 font-medium leading-tight">
                    Your smart companion for safe and informed hiking.
                </p>
            </div>
        </div>

        <div class="mt-8 max-w-3xl">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800">
                Start Ready. End with Safety.
            </h2>
            <p class="mt-3 text-gray-600 text-sm md:text-base leading-relaxed">
                Plan your hike with real-time weather forecasts, personalized trail suggestions, and essential safety recommendations — all in one place.
            </p>

            {{-- Quick Access to Hiking Tools for Hikers --}}
            @if(isset($user) && $user && $user->user_type === 'hiker')
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('hiking-tools') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    Access Hiking Tools
                </a>
                <a href="{{ route('assessment.instruction') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Start Assessment
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        // Only run if recommendations container exists
        const grid = document.getElementById('recommendations-grid');
        const container = document.getElementById('recommendations-container');
        if (!grid || !container) return;

        // Add controls: loader and refresh button (idempotent)
        let controlsBar = document.getElementById('recommendations-controls');
        if (!controlsBar) {
            controlsBar = document.createElement('div');
            controlsBar.id = 'recommendations-controls';
            controlsBar.className = 'flex items-center justify-between mb-4';
            controlsBar.innerHTML = `
                <div class="text-sm text-gray-600">Personalized for you</div>
                <div class="flex items-center space-x-2">
                    <button id="recommend-refresh" class="inline-flex items-center px-3 py-1 bg-white border border-gray-200 rounded-md text-sm hover:bg-gray-50" aria-label="Refresh recommendations">Refresh</button>
                    <div id="recommend-loader" role="status" aria-live="polite" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-gray-500" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 01-8 8z"></path></svg>
                    </div>
                </div>
            `;
            container.insertBefore(controlsBar, container.firstChild);
        }

        const loader = document.getElementById('recommend-loader');
        const refreshBtn = document.getElementById('recommend-refresh');

        // Use logged-in user id if available
        const userId = @json(optional($user)->id ?? null);
        if (!userId) {
            grid.innerHTML = '<div class="col-span-1">Please sign in to see personalized recommendations.</div>';
            return;
        }

        // Concurrency control for fetches
        let currentAbort = null;

        async function showLoader(on) {
            if (on) loader.classList.remove('hidden');
            else loader.classList.add('hidden');
            refreshBtn.disabled = on;
        }

        // Auto-refresh configuration
        const AUTO_REFRESH_INTERVAL = 1000 * 60 * 5; // 5 minutes
        const AUTO_REFRESH_JITTER = 1000 * 30; // up to 30s jitter
        let autoRefreshTimer = null;

        // Visibility-aware auto-refresh: pause when tab is hidden
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                if (autoRefreshTimer) {
                    clearTimeout(autoRefreshTimer);
                    autoRefreshTimer = null;
                }
            } else {
                scheduleAutoRefresh();
            }
        });

        function scheduleAutoRefresh() {
            if (autoRefreshTimer) clearTimeout(autoRefreshTimer);
            const jitter = Math.floor(Math.random() * AUTO_REFRESH_JITTER);
            autoRefreshTimer = setTimeout(() => {
                fetchRecommendations(6);
            }, AUTO_REFRESH_INTERVAL + jitter);
        }

        // Skeleton generator while loading
        function renderSkeletons(count = 6) {
            const skeleton = `<div class="animate-pulse bg-white rounded-2xl p-6 h-56"></div>`;
            grid.innerHTML = Array.from({
                length: count
            }).map(() => skeleton).join('');
        }

        async function fetchRecommendations(k = 6) {
            // Abort previous request if any
            if (currentAbort) {
                try {
                    currentAbort.abort();
                } catch (e) {}
                currentAbort = null;
            }

            currentAbort = new AbortController();
            const signal = currentAbort.signal;

            // Use skeletons for richer UX + loader indicator
            renderSkeletons(6);
            await showLoader(true);

            try {
                const resp = await fetch(`/api/recommender/user/${userId}?k=${k}`, {
                    signal
                });
                if (!resp.ok) throw new Error(`Recommender responded with ${resp.status}`);
                const data = await resp.json();
                console.debug('Recommender raw response:', data);
                // Debug left in console only

                // Support two shapes from the API:
                // 1) ML-style: { results: [{ trail_id, score, explanation }, ...] }
                // 2) DB-fallback: { recommendations: [{ id, name, slug, ... }, ...] }

                // If backend returned DB fallback with full trail objects, render directly
                if ((!data.results || !data.results.length) && Array.isArray(data.recommendations) && data.recommendations.length) {
                    const fallback = data.recommendations;
                    grid.innerHTML = fallback.map((t) => {
                        // Render with any available score (may be undefined)
                        const score = typeof t.score !== 'undefined' ? t.score : 0;
                        return renderTrailCard(t, score, t.id);
                    }).join('');
                    scheduleAutoRefresh();
                    return;
                }

                if (!data.results || !data.results.length) {
                    grid.innerHTML = '<div class="col-span-1">No recommendations right now.</div>';
                    scheduleAutoRefresh();
                    return;
                }

                // Fetch trail metadata concurrently but with basic caching and logging
                const trailCache = {};
                const trailFetchErrors = {};
                const fetchTrail = async (id) => {
                    if (trailCache[id]) return trailCache[id];
                    try {
                        const r = await fetch(`/api/trails/${id}`, {
                            signal
                        });
                        if (!r.ok) {
                            const text = await r.text().catch(() => '');
                            console.warn('Failed to fetch trail metadata', id, r.status, text);
                            trailFetchErrors[id] = `HTTP ${r.status}`;
                            return null;
                        }
                        const j = await r.json();
                        trailCache[id] = j;
                        return j;
                    } catch (e) {
                        console.warn('Failed to fetch trail', id, e && e.message ? e.message : e);
                        trailFetchErrors[id] = e && e.message ? e.message : String(e);
                        return null;
                    }
                };

                const recommended = data.results;
                const trailPromises = recommended.map(r => fetchTrail(r.trail_id));
                const trailDetails = await Promise.all(trailPromises);

                // Render cards with explainability toggles
                grid.innerHTML = trailDetails.map((t, i) => {
                    const trail = t;
                    const metaScore = recommended[i]?.score ?? 0;
                    const explain = recommended[i]?.explanation ?? null; // optional field from recommender
                    const cardHtml = renderTrailCard(trail, metaScore, recommended[i]?.trail_id);
                    // If explanation is present, attach a small explain UI under the card via data attribute
                    return cardHtml.replace('</div>\n            ', `\n                <div class="p-4 border-t border-gray-100 bg-gray-50 text-sm text-gray-600 explain-container" data-explain='${escapeHtml(JSON.stringify(explain || null))}'>\n                    <button class="explain-toggle text-blue-600 underline">Why this trail?</button>\n                    <div class="explain-text mt-2 hidden"></div>\n                </div>\n            `);
                }).join('');

                // If any trail fetch failed, show a small diagnostic message to help debugging
                const failedIds = Object.keys(trailFetchErrors || {});
                if (failedIds.length) {
                    const diag = document.createElement('div');
                    diag.className = 'col-span-1 text-sm text-red-600 mt-4';
                    diag.innerHTML = `Failed to load details for trails: ${failedIds.join(', ')}. See console for details.`;
                    container.appendChild(diag);
                    console.warn('Trail metadata fetch errors:', trailFetchErrors);
                }

                // Wire explain toggles (after DOM insert)
                Array.from(grid.querySelectorAll('.explain-toggle')).forEach(btn => {
                    btn.addEventListener('click', async function(e) {
                        const c = btn.closest('.explain-container');
                        const textEl = c.querySelector('.explain-text');
                        if (!textEl) return;
                        if (!textEl.classList.contains('hidden')) {
                            textEl.classList.add('hidden');
                            return;
                        }
                        // Reveal explanation: prefer structured explanation from recommender
                        let payload = null;
                        try {
                            payload = JSON.parse(c.getAttribute('data-explain'));
                        } catch (e) {
                            payload = null;
                        }
                        if (payload) {
                            textEl.textContent = payload.explanation || JSON.stringify(payload);
                        } else {
                            textEl.textContent = 'Recommended based on your recent activity and preferences (difficulty, distance, features, and location).';
                        }
                        textEl.classList.remove('hidden');
                    });
                });
                scheduleAutoRefresh();
            } catch (err) {
                if (err.name === 'AbortError') {
                    // aborted - silently ignore
                    console.debug('Recommendation fetch aborted');
                    return;
                }
                console.error('Failed to load recommendations', err);
                // Try to show a friendly message and a retry button
                grid.innerHTML = `<div class="col-span-1 text-center text-sm text-gray-600">Failed to load recommendations. <button id="recommend-retry" class="ml-2 underline">Try again</button></div>`;
                const retryBtn = document.getElementById('recommend-retry');
                if (retryBtn) retryBtn.addEventListener('click', () => fetchRecommendations(k));
            } finally {
                await showLoader(false);
            }
        }

        function renderTrailCard(t, score, fallbackId) {
            if (!t) {
                // Minimal failed card with id so user/developer can see which failed
                return `\n                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 text-sm text-gray-600">\n                        <div class=\"font-semibold text-gray-800\">Trail ${escapeHtml(String(fallbackId))}</div>\n                        <div class=\"text-xs text-red-600 mt-2\">Failed to load details</div>\n                    </div>\n                `;
            }
            // Normalize fields from different API shapes
            const image = t.primary_image ?? (t.images && t.images.length ? (t.images[0].url || t.images[0].full_url) : '/img/default-trail.jpg');
            const name = t.name ?? t.trail_name ?? 'Trail';
            const slug = t.slug ?? t.id;
            const location = t.location?.full_name ?? (t.location || '');
            const rating = Number(t.average_rating ?? t.averageRating ?? 0) || 0;

            // Minimal card: image, trail name + rating, and a second line with mountain name and location
            const mountain = t.mountain_name ?? '';
            const locationLabel = t.location_label ?? '';

            const compact = `
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200 group">
                    <div class="relative h-40">
                        <img src="${escapeHtml(image)}" alt="${escapeHtml(name)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="mr-4 w-full">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-semibold text-gray-900 text-sm">${escapeHtml(name)}</h4>
                                    <div class="text-sm font-bold text-gray-900">⭐ ${escapeHtml(Number(rating).toFixed(1))}</div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">${escapeHtml(mountain)} ${mountain && locationLabel ? '📍' : ''} ${escapeHtml(locationLabel)}</div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="/trails/${encodeURIComponent(slug)}" class="inline-block w-full text-center bg-green-600 text-white rounded-xl py-2 text-sm">View</a>
                        </div>
                    </div>
                </div>
            `;

            return compact;
        }

        // Helper: sanitize small strings for insertion
        function escapeHtml(s) {
            if (!s && s !== 0) return '';
            return String(s).replace(/[&<>\"'`]/g, function(ch) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;',
                    '`': '&#96;'
                })[ch];
            });
        }

        function renderStars(rating) {
            const full = Math.round(Math.min(5, Math.max(0, Number(rating) || 0)));
            let out = '';
            for (let i = 0; i < full; i++) out += '<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>';
            for (let i = full; i < 5; i++) out += '<svg class="w-4 h-4 text-gray-200" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>';
            return out;
        }

        // Wire refresh button
        refreshBtn.addEventListener('click', () => fetchRecommendations(6));

        // Initial load
        fetchRecommendations(6);
    })();
</script>
@endpush

{{-- Trail Recommendations --}}
<div id="trail-recommendations" class="px-6 lg:px-8 py-10 bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="text-center mb-8">
        <h3 class="text-3xl font-bold text-gray-800 mb-3">Trail Recommendations</h3>
        <p class="text-gray-600 max-w-2xl mx-auto">Discover amazing hiking trails tailored to your preferences and current conditions</p>
    </div>

    <div id="recommendations-container" class="max-w-7xl mx-auto">
        <div id="recommendations-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 custom-scrollbar overflow-x-auto">
            {{-- Filled dynamically via JS --}}
            <div class="col-span-1 text-center text-sm text-gray-600">Loading recommendations...</div>
        </div>
    </div>

    <!-- View All Trails Button -->
    <div class="text-center mt-8">
        <a href="{{ route('explore') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Explore More Trails
        </a>
    </div>
</div>

{{-- Weather and Forecast Section --}}
@if(isset($weather) && $weather)
<div id="weather-section" class="px-6 lg:px-8 py-8">
    <div class="text-center mb-8">
        <h3 class="text-3xl font-bold text-gray-800 mb-3">Weather</h3>
        <p class="text-gray-600 max-w-2xl mx-auto">Stay updated with the latest weather information for your hiking adventures.</p>
    </div>
    <div class="bg-gradient-to-br {{ $weather['gradient'] ?? 'from-indigo-500 to-yellow-300' }} rounded-3xl p-8 text-white shadow-xl relative overflow-hidden min-h-[400px] weather-container">

        {{-- Animated Weather Background --}}
        <x-weather-animation
            :weather-condition="$weather['condition'] ?? 'clear'"
            :is-day="$weather['is_day'] ?? true" />

        {{-- Weather Content Container --}}
        <div class="relative z-10 h-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 h-full">

                {{-- Column 1: Current Weather Basic Details --}}
                <div class="flex flex-col justify-center">
                    {{-- Location and Date --}}
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-white/90 mb-1 weather-city">{{ $weather['city'] ?? 'Unknown' }}</h2>
                        <p class="text-sm text-white/80">{{ now()->format('l, F j, Y') }}</p>
                    </div>

                    {{-- Temperature and Description --}}
                    <div class="flex items-center space-x-6">
                        <div class="text-6xl lg:text-7xl font-bold weather-temp">{{ $weather['temp'] ?? 'N/A' }}°</div>
                        <div class="flex flex-col">
                            <img src="https://openweathermap.org/img/wn/{{ $weather['icon'] ?? '01d' }}@4x.png"
                                alt="{{ $weather['description'] ?? 'Clear sky' }}"
                                class="h-20 w-20 drop-shadow-lg weather-icon">
                            <p class="text-sm capitalize font-medium text-center mt-2 weather-description">{{ $weather['description'] ?? 'Clear sky' }}</p>
                        </div>
                    </div>

                    {{-- Use my location button placeholder (inserted here under the basic weather column) --}}
                    <div id="weather-use-location-placeholder" class="mt-4"></div>
                </div>

                {{-- Column 2: Three Containers --}}
                <div class="flex flex-col space-y-4">

                    {{-- Container 1: 5-Day Forecast in Rows --}}
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 flex-1">
                        <h3 class="text-sm font-semibold mb-3 text-white/90">5-Day Forecast</h3>
                        <div class="space-y-2">
                            @if(isset($forecast) && $forecast && $forecast->count() > 0)
                            @foreach($forecast as $index => $day)
                            @php
                            $isToday = $index === 0; // First day is today
                            @endphp
                            <div class="grid grid-cols-3 items-center py-2 hover:bg-white/10 rounded-lg px-2 transition-all {{ $isToday ? 'bg-white/15 border border-yellow-400/50' : '' }} forecast-day-{{ $index }}">
                                {{-- Day & Date --}}
                                <div class="text-xs">
                                    <div class="font-medium text-white flex items-center">
                                        {{ $isToday ? 'Today' : explode(', ', $day['date'])[0] }}
                                        @if($isToday)
                                        <span class="ml-1 w-2 h-2 bg-yellow-400 rounded-full"></span>
                                        @endif
                                    </div>
                                    <div class="text-white/70">{{ explode(', ', $day['date'])[1] }}</div>
                                </div>
                                {{-- Weather Icon --}}
                                <div class="flex justify-center">
                                    <img src="https://openweathermap.org/img/wn/{{ $day['icon'] ?? '01d' }}@2x.png"
                                        alt="{{ $day['condition'] ?? 'Clear' }}"
                                        class="h-8 w-8 forecast-icon">
                                </div>
                                {{-- Temp & Weather --}}
                                <div class="text-right text-xs">
                                    <div class="font-bold text-white forecast-temp">{{ $day['temp'] ?? 'N/A' }}°</div>
                                    <div class="text-white/70 capitalize truncate forecast-condition">{{ $day['condition'] ?? 'Clear' }}</div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="text-center py-4 text-white/70 text-xs">
                                <p>Forecast not available</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Container 2: 24-Hour Forecast --}}
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 flex-1">
                        <h3 class="text-sm font-semibold mb-3 text-white/90">24-Hour Forecast</h3>
                        <div class="relative overflow-hidden">
                            <div class="overflow-x-auto pb-2 custom-scrollbar relative" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.3) transparent;">
                                @php
                                // Use server timezone; client will display localized times when possible
                                $currentTime = now();

                                // Generate hourly temperatures for dynamic line
                                $hourlyTemps = [];
                                $minTemp = 100;
                                $maxTemp = -100;

                                for($i = 0; $i < 8; $i++) {
                                    $temp=rand(20, 30); // Replace with actual hourly data
                                    $hourlyTemps[]=$temp;
                                    $minTemp=min($minTemp, $temp);
                                    $maxTemp=max($maxTemp, $temp);
                                    }

                                    // Calculate line points
                                    $points=[];
                                    $cardWidth=60; // min-width of each card
                                    $cardSpacing=12; // space-x-3
                                    $startX=30; // Center of first card

                                    for($i=0; $i < count($hourlyTemps); $i++) {
                                    $x=$startX + ($i * ($cardWidth + $cardSpacing));
                                    // Normalize temperature to y-coordinate (inverted for SVG)
                                    $normalizedTemp=($hourlyTemps[$i] - $minTemp) / max(1, ($maxTemp - $minTemp));
                                    $y=15 - ($normalizedTemp * 10); // Adjusted for line positioning
                                    $points[]=['x'=> $x, 'y' => $y];
                                    }

                                    // Generate smooth curve path
                                    $path = "M{$points[0]['x']} {$points[0]['y']}";
                                    for($i = 1; $i < count($points); $i++) {
                                        if($i==1) {
                                        $path .=" Q{$points[$i]['x']} {$points[$i]['y']}, {$points[$i]['x']} {$points[$i]['y']}" ;
                                        } else {
                                        $prev=$points[$i-1];
                                        $curr=$points[$i];
                                        $controlX=($prev['x'] + $curr['x']) / 2;
                                        $path .=" Q{$controlX} {$prev['y']}, {$curr['x']} {$curr['y']}" ;
                                        }
                                        }

                                        // Calculate total width for SVG
                                        $totalWidth=$startX + ((count($hourlyTemps) - 1) * ($cardWidth + $cardSpacing)) + $startX;
                                        @endphp

                                        <div class="flex space-x-3" style="min-width: {{ $totalWidth }}px;">
                                        @for($i = 0; $i < 8; $i++)
                                            @php
                                            $hour=$currentTime->copy()->addHours($i * 3);
                                            $temp = $hourlyTemps[$i]; // Use the same temperature from the line calculation
                                            $isCurrentHour = $i === 0; // First hour is current hour
                                            @endphp
                                            <div class="flex-shrink-0 text-center min-w-[60px] relative {{ $isCurrentHour ? 'bg-white/15 rounded-lg border border-yellow-400/50' : '' }} hourly-forecast-card">
                                                {{-- Space for temperature above line (positioned via SVG) --}}
                                                <div class="h-4 mb-2"></div>

                                                {{-- Space for the line (24px height) --}}
                                                <div class="h-6 mb-2 relative">
                                                    {{-- Line and temperatures positioned here via SVG --}}
                                                </div>

                                                {{-- Weather icon below line --}}
                                                <img src="https://openweathermap.org/img/wn/{{ $weather['icon'] ?? '01d' }}.png"
                                                    alt="Weather"
                                                    class="h-6 w-6 mx-auto mb-2 hourly-icon">

                                                {{-- Time at bottom --}}
                                                <div class="text-xs {{ $isCurrentHour ? 'text-yellow-300 font-semibold' : 'text-white/80' }} flex items-center justify-center">
                                                    {{ $hour->format('H:i') }}
                                                    @if($isCurrentHour)
                                                    <span class="ml-1 w-1.5 h-1.5 bg-yellow-400 rounded-full"></span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endfor
                            </div>

                            {{-- Dynamic Temperature trend line with temperature labels --}}
                            <svg class="absolute top-4 left-0 pointer-events-none z-10 temperature-line-svg"
                                width="{{ $totalWidth }}"
                                height="40"
                                viewBox="0 0 {{ $totalWidth }} 40"
                                style="height: 40px;">
                                <defs>
                                    <linearGradient id="tempGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" style="stop-color:#fbbf24;stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:#f59e0b;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#d97706;stop-opacity:1" />
                                    </linearGradient>
                                </defs>

                                {{-- Temperature labels positioned along the line --}}
                                @foreach($points as $index => $point)
                                <text x="{{ $point['x'] }}"
                                    y="{{ $point['y'] + 15 - 8 }}"
                                    text-anchor="middle"
                                    class="fill-white text-xs font-medium"
                                    style="font-size: 11px; font-family: system-ui;">{{ $hourlyTemps[$index] }}°</text>
                                @endforeach

                                {{-- Temperature trend line --}}
                                <path d="{{ $path }}"
                                    stroke="url(#tempGradient)"
                                    stroke-width="3"
                                    fill="none"
                                    stroke-linecap="round"
                                    transform="translate(0, 15)" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Container 3: UV, Humidity, Real Feel --}}
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        {{-- UV --}}
                        <div>
                            <div class="text-xs text-white/80 mb-1">UV</div>
                            <div class="text-sm font-bold text-white weather-uv">{{ $weather['uv_index'] ?? 'N/A' }}</div>
                            <div class="w-8 h-8 mx-auto mt-2 bg-white/20 rounded-full flex items-center justify-center">
                                <div class="text-lg">☀️</div>
                            </div>
                        </div>
                        {{-- Humidity --}}
                        <div>
                            <div class="text-xs text-white/80 mb-1">Humidity</div>
                            <div class="text-sm font-bold text-white weather-humidity">{{ $weather['humidity'] ?? 'N/A' }}%</div>
                            <div class="w-8 h-8 mx-auto mt-2 bg-white/20 rounded-full flex items-center justify-center">
                                <div class="text-lg">💧</div>
                            </div>
                        </div>
                        {{-- Real Feel --}}
                        <div>
                            <div class="text-xs text-white/80 mb-1">Real feel</div>
                            <div class="text-sm font-bold text-white weather-feels-like">{{ $weather['feels_like'] ?? 'N/A' }}°</div>
                            <div class="w-8 h-8 mx-auto mt-2 bg-white/20 rounded-full flex items-center justify-center">
                                <div class="text-lg">🌡️</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Copyright Notice --}}
    <div class="absolute bottom-4 right-6 text-xs text-white/50">
        Data provided by OpenWeather
    </div>
</div>
</div>

{{-- Weather Testing Script (Temporary) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const testBtn = document.getElementById('weatherTestBtn');
        const testMenu = document.getElementById('weatherTestMenu');
        const testOptions = document.querySelectorAll('.weather-test-option');

        if (testBtn && testMenu) {
            // Toggle menu
            testBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                testMenu.classList.toggle('hidden');
            });

            // Close menu when clicking outside
            document.addEventListener('click', function() {
                testMenu.classList.add('hidden');
            });

            // Handle weather testing
            testOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const condition = this.dataset.condition;
                    const isDay = this.dataset.isDay === 'true';

                    // Update the weather animation component
                    const weatherAnim = document.querySelector('.weather-anim');
                    if (weatherAnim) {
                        // Remove all existing classes
                        weatherAnim.className = 'weather-anim';

                        // Map conditions to classes
                        const conditionMap = {
                            'clear': 'weather-sunny',
                            'clouds': 'weather-cloudy',
                            'overcast clouds': 'weather-overcast',
                            'rain': 'weather-rain',
                            'thunderstorm': 'weather-thunderstorm'
                        };

                        const weatherClass = conditionMap[condition] || 'weather-cloudy';
                        const timeClass = (weatherClass === 'weather-sunny') ? 'day-time' : (isDay ? 'day-time' : 'night-time');

                        weatherAnim.classList.add(weatherClass, timeClass);

                        // Update the HTML content dynamically
                        updateWeatherAnimationContent(weatherClass, isDay);
                    }

                    testMenu.classList.add('hidden');
                });
            });
        }
    });

    function updateWeatherAnimationContent(weatherClass, isDay) {
        const weatherAnim = document.querySelector('.weather-anim');
        if (!weatherAnim) return;

        // Clear existing content except hills
        const hills = weatherAnim.querySelectorAll('[class*="hill-"]');
        weatherAnim.innerHTML = '';

        // Add celestial bodies
        if (weatherClass === 'weather-sunny') {
            weatherAnim.innerHTML += '<div class="sun"></div>';
        } else {
            if (isDay) {
                weatherAnim.innerHTML += '<div class="day-sun"></div>';
            } else {
                weatherAnim.innerHTML += '<div class="night-moon"><div class="moon-crater1"></div><div class="moon-crater2"></div></div>';
            }
        }

        // Add clouds for appropriate weather
        if (['weather-cloudy', 'weather-overcast', 'weather-thunderstorm', 'weather-rain'].includes(weatherClass)) {
            weatherAnim.innerHTML += '<div class="cloud cloud-1"></div>';
            weatherAnim.innerHTML += '<div class="cloud cloud-2"></div>';
            weatherAnim.innerHTML += '<div class="cloud cloud-3"></div>';
            // Add smaller clouds under main clouds
            weatherAnim.innerHTML += '<div class="cloud-small cloud-small-1"></div>';
            weatherAnim.innerHTML += '<div class="cloud-small cloud-small-2"></div>';
            weatherAnim.innerHTML += '<div class="cloud-small cloud-small-3"></div>';
        }

        // Add lightning for thunderstorms
        if (weatherClass === 'weather-thunderstorm') {
            weatherAnim.innerHTML += `
            <div class="lightning-container">
                <div class="lightning-bolt lightning-bolt-1">
                    <svg viewBox="0 0 20 120" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 0 L12 0 L7 35 L13 35 L6 65 L11 65 L3 120 L7 120 L15 75 L10 75 L16 45 L11 45 L18 15 L13 15 L8 0 Z"/>
                    </svg>
                </div>
                <div class="lightning-bolt lightning-bolt-2">
                    <svg viewBox="0 0 20 120" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 0 L10 0 L5 30 L11 30 L4 55 L9 55 L2 100 L6 100 L13 65 L8 65 L14 35 L9 35 L16 10 L11 10 L6 0 Z"/>
                    </svg>
                </div>
                <div class="lightning-bolt lightning-bolt-3">
                    <svg viewBox="0 0 20 120" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 0 L13 0 L8 40 L14 40 L7 70 L12 70 L4 120 L8 120 L16 80 L11 80 L17 50 L12 50 L19 20 L14 20 L9 0 Z"/>
                    </svg>
                </div>
            </div>
        `;
        }

        // Add rain for rain and thunderstorm
        if (['weather-rain', 'weather-thunderstorm'].includes(weatherClass)) {
            let rainHTML = '<div class="rain-container">';
            for (let i = 0; i < 20; i++) {
                const sizes = ['large', 'medium', 'small'];
                const size = sizes[i % 3];
                rainHTML += `<div class="rain-drop ${size}"></div>`;
            }
            rainHTML += '</div>';
            weatherAnim.innerHTML += rainHTML;
        }

        // Re-add hills
        hills.forEach(hill => {
            weatherAnim.appendChild(hill);
        });
    }
</script>

{{-- Real-time Weather Update Script --}}
<script>
    let weatherUpdateInterval;
    let lastRequestedCoords = null;
    let lastWeatherFetchAt = 0;
    let weatherStarted = false;
    let activeWeatherRequest = null; // Track active fetch to cancel duplicates
    const WEATHER_FETCH_COOLDOWN_MS = 3000; // Reduced to 3s for better responsiveness
    const WEATHER_CACHE_DURATION_MS = 2 * 60 * 1000; // 2 minutes cache

    // Helper: Get cached weather data if still valid
    function getCachedWeather() {
        try {
            const cached = localStorage.getItem('weatherCache');
            if (!cached) return null;
            
            const data = JSON.parse(cached);
            const age = Date.now() - (data.timestamp || 0);
            
            if (age < WEATHER_CACHE_DURATION_MS) {
                console.debug('Using cached weather data (age: ' + Math.round(age / 1000) + 's)');
                return data.weather;
            }
        } catch (e) {
            console.warn('Failed to read weather cache:', e);
        }
        return null;
    }

    // Helper: Save weather data to cache
    function cacheWeather(weatherData) {
        try {
            localStorage.setItem('weatherCache', JSON.stringify({
                weather: weatherData,
                timestamp: Date.now()
            }));
        } catch (e) {
            console.warn('Failed to cache weather:', e);
        }
    }

    function updateWeatherData(coords, skipCache = false) {
        // Cancel any active request to avoid duplicates
        if (activeWeatherRequest) {
            try {
                activeWeatherRequest.abort();
            } catch (e) {}
            activeWeatherRequest = null;
        }

        // Cooldown check
        const now = Date.now();
        if (lastWeatherFetchAt && (now - lastWeatherFetchAt) < WEATHER_FETCH_COOLDOWN_MS) {
            console.debug('Skipping weather fetch due to cooldown');
            return;
        }
        lastWeatherFetchAt = now;

        // Show cached data immediately if available (unless explicitly skipping)
        if (!skipCache) {
            const cached = getCachedWeather();
            if (cached) {
                updateWeatherDisplay(cached.weather, cached.forecast, cached.hourly || []);
            }
        }

        // Build URL
        let url = '/api/weather/current';
        if (coords && coords.lat && coords.lon) {
            url += `?lat=${encodeURIComponent(coords.lat)}&lon=${encodeURIComponent(coords.lon)}`;
            lastRequestedCoords = coords;
        }

        // Use AbortController for cancellable fetch
        activeWeatherRequest = new AbortController();
        const signal = activeWeatherRequest.signal;

        const handleSuccess = function(data) {
            activeWeatherRequest = null;
            
            if (data.success && data.weather) {
                // Cache the response
                cacheWeather({
                    weather: data.weather,
                    forecast: data.forecast,
                    hourly: data.hourly || []
                });
                
                updateWeatherDisplay(data.weather, data.forecast, data.hourly || []);
                
                // Hide loading indicator if shown
                const alertEl = document.getElementById('weather-location-alert');
                if (alertEl && alertEl.textContent.includes('Updating')) {
                    alertEl.classList.add('hidden');
                }
            }
        };

        const handleError = function(error) {
            activeWeatherRequest = null;
            
            // Don't show error if request was cancelled
            if (error.name === 'AbortError') {
                console.debug('Weather fetch cancelled');
                return;
            }
            
            console.error('Error updating weather:', error);
            
            const cityElement = document.querySelector('.weather-city');
            const alertEl = document.getElementById('weather-location-alert');
            
            // Only show error if we don't have cached data
            if (!getCachedWeather()) {
                if (cityElement) cityElement.textContent = 'Unknown';
                if (alertEl) {
                    alertEl.className = 'mt-3 p-2 rounded text-sm font-medium bg-yellow-100 text-yellow-800';
                    alertEl.textContent = 'Unable to fetch weather data. Try "Use my location" or wait for retry.';
                    alertEl.classList.remove('hidden');
                }
            }
        };

        // Modern fetch with AbortController
        fetch(url, {
            method: 'GET',
            signal: signal,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(handleSuccess)
        .catch(handleError);
    }

    function updateWeatherDisplay(weather, forecast) {
        // Update city label if available from AJAX response, otherwise show coords if known
        const cityElement = document.querySelector('.weather-city');
        if (cityElement) {
            if (weather.city) {
                cityElement.textContent = weather.city;
            } else if (lastRequestedCoords) {
                cityElement.textContent = `${lastRequestedCoords.lat.toFixed(4)}, ${lastRequestedCoords.lon.toFixed(4)}`;
            }
        }
        // Update current temperature
        const tempElement = document.querySelector('.weather-temp');
        if (tempElement) {
            tempElement.textContent = weather.temp + '°';
        }

        // Update weather description
        const descElement = document.querySelector('.weather-description');
        if (descElement) {
            descElement.textContent = weather.description;
        }

        // Update weather icon
        const iconElement = document.querySelector('.weather-icon');
        if (iconElement) {
            iconElement.src = `https://openweathermap.org/img/wn/${weather.icon}@4x.png`;
            iconElement.alt = weather.description;
        }

        // Update UV index
        const uvElement = document.querySelector('.weather-uv');
        if (uvElement) {
            uvElement.textContent = weather.uv_index || 'N/A';
        }

        // Update humidity
        const humidityElement = document.querySelector('.weather-humidity');
        if (humidityElement) {
            humidityElement.textContent = weather.humidity + '%';
        }

        // Update feels like
        const feelsLikeElement = document.querySelector('.weather-feels-like');
        if (feelsLikeElement) {
            feelsLikeElement.textContent = weather.feels_like + '°';
        }

        // Update 5-day forecast
        if (forecast) {
            updateForecastDisplay(forecast);
        }

        // Update 24-hour forecast
        updateHourlyForecast(weather);

        // Update last refresh time
        const now = new Date();
        // Use the client's locale and timezone for the displayed timestamp
        const timeString = now.toLocaleTimeString(undefined, {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit'
        });

        // Add/update last refresh indicator
        let refreshIndicator = document.querySelector('.weather-refresh-indicator');
        if (!refreshIndicator) {
            refreshIndicator = document.createElement('div');
            refreshIndicator.className = 'weather-refresh-indicator absolute top-4 left-6 text-xs text-white/60';
            document.querySelector('.weather-container').appendChild(refreshIndicator);
        }
        refreshIndicator.textContent = `Last updated: ${timeString}`;
    }

    function updateForecastDisplay(forecast) {
        forecast.forEach((day, index) => {
            const dayElement = document.querySelector(`.forecast-day-${index}`);
            if (dayElement) {
                const tempElement = dayElement.querySelector('.forecast-temp');
                const iconElement = dayElement.querySelector('.forecast-icon');
                const conditionElement = dayElement.querySelector('.forecast-condition');

                if (tempElement) tempElement.textContent = day.temp + '°';
                if (iconElement) {
                    iconElement.src = `https://openweathermap.org/img/wn/${day.icon}@2x.png`;
                    iconElement.alt = day.condition;
                }
                if (conditionElement) conditionElement.textContent = day.condition;
            }
        });
    }

    function updateHourlyForecast(weather) {
        // Regenerate hourly temperatures using provided hourly array (next ~24 hours)
        const hourlyCards = document.querySelectorAll('.hourly-forecast-card');
        const temps = [];
        const hourlyData = Array.isArray(weather.hourly_data) ? weather.hourly_data : (window.__lastHourlyData || []);

        // If hourlyData is empty and a separate hourly param was passed, use that
        if ((!hourlyData || !hourlyData.length) && window.__lastHourlyFromServer) {
            window.__lastHourlyData = window.__lastHourlyFromServer;
        }

        hourlyCards.forEach((card, index) => {
            const hourObj = (window.__lastHourlyData && window.__lastHourlyData[index]) ? window.__lastHourlyData[index] : null;
            const temp = hourObj ? Math.round(hourObj.temp) : Math.floor(Math.random() * 11) + 20;
            temps.push(temp);

            const iconElement = card.querySelector('.hourly-icon');
            if (iconElement) {
                const iconCode = hourObj && hourObj.icon ? hourObj.icon : (weather.icon || '01d');
                iconElement.src = `https://openweathermap.org/img/wn/${iconCode}.png`;
            }

            // Update the time label if provided
            const timeEl = card.querySelector('.text-xs');
            if (timeEl && hourObj && hourObj.time) {
                timeEl.childNodes[0].nodeValue = hourObj.time;
            }
        });

        // Store last hourly for reuse
        window.__lastHourlyData = window.__lastHourlyData || window.__lastHourlyFromServer || [];

        // Update the temperature line (this would require regenerating the SVG)
        updateTemperatureLine(temps);
    }

    function updateTemperatureLine(temps) {
        const svgElement = document.querySelector('.temperature-line-svg');
        if (!svgElement) return;

        const minTemp = Math.min(...temps);
        const maxTemp = Math.max(...temps);
        const cardWidth = 60;
        const cardSpacing = 12;
        const startX = 30;

        // Recalculate points
        const points = temps.map((temp, i) => {
            const x = startX + (i * (cardWidth + cardSpacing));
            const normalizedTemp = (temp - minTemp) / Math.max(1, (maxTemp - minTemp));
            const y = 15 - (normalizedTemp * 10);
            return {
                x,
                y
            };
        });

        // Update temperature labels
        const textElements = svgElement.querySelectorAll('text');
        textElements.forEach((text, index) => {
            if (points[index]) {
                text.textContent = temps[index] + '°';
                text.setAttribute('x', points[index].x);
                text.setAttribute('y', points[index].y + 15 - 8);
            }
        });

        // Update path
        let path = `M${points[0].x} ${points[0].y}`;
        for (let i = 1; i < points.length; i++) {
            if (i === 1) {
                path += ` Q${points[i].x} ${points[i].y}, ${points[i].x} ${points[i].y}`;
            } else {
                const prev = points[i - 1];
                const curr = points[i];
                const controlX = (prev.x + curr.x) / 2;
                path += ` Q${controlX} ${prev.y}, ${curr.x} ${curr.y}`;
            }
        }

        const pathElement = svgElement.querySelector('path');
        if (pathElement) {
            pathElement.setAttribute('d', path);
        }
    }

    // Initialize real-time updates when the page loads
    document.addEventListener('DOMContentLoaded', function() {
    // Attempt to get browser geolocation. If it fails or is denied, do not default to a specific city.
    const defaultCoords = null; // explicit: no geographic fallback

        function startUpdates(coords) {
            // Prevent double-starts
            if (weatherStarted) return;
            weatherStarted = true;

            // Initial fetch (will use cache if available)
            updateWeatherData(coords);
            
            // Clear any existing interval to avoid duplicates
            if (weatherUpdateInterval) {
                try { clearInterval(weatherUpdateInterval); } catch (e) {}
                weatherUpdateInterval = null;
            }
            
            // Start periodic updates every 3 minutes (reduced from 5 for fresher data)
            weatherUpdateInterval = setInterval(() => updateWeatherData(coords, true), 180000);

            // Add manual refresh button
            const refreshButton = document.createElement('button');
            refreshButton.innerHTML = `
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        `;
            refreshButton.className = 'weather-refresh-btn absolute top-4 right-20 text-white/60 hover:text-white/90 transition-colors p-1 rounded';
            refreshButton.title = 'Refresh Weather Data';
            refreshButton.onclick = () => {
                // Force fresh fetch by skipping cache
                updateWeatherData(coords, true);
                // Add spinning animation
                refreshButton.style.animation = 'spin 0.5s ease-in-out';
                setTimeout(() => refreshButton.style.animation = '', 500);
            };

            const weatherContainer = document.querySelector('.weather-container');
            if (weatherContainer && !document.querySelector('.weather-refresh-btn')) {
                weatherContainer.appendChild(refreshButton);
            }
            // Add 'Use my location' button for manual geolocation re-request
            if (!document.querySelector('.weather-use-location-btn')) {
                const useLocBtn = document.createElement('button');
                useLocBtn.className = 'weather-use-location-btn text-white/80 bg-white/10 hover:bg-white/20 px-3 py-1 rounded transition text-sm';
                useLocBtn.title = 'Use my location';
                useLocBtn.textContent = 'Use my location';
                useLocBtn.onclick = function() {
                    // Re-request geolocation and store coordinates
                    if (!navigator.geolocation) {
                        alert('Geolocation is not supported by your browser.');
                        return;
                    }

                    const t = setTimeout(() => {
                        console.warn('Geolocation request timed out');
                        alert('Unable to get location in time. Please try again.');
                    }, 10000);

                    navigator.geolocation.getCurrentPosition(function(position) {
                        clearTimeout(t);
                        const coords = { lat: position.coords.latitude, lon: position.coords.longitude };
                        try { localStorage.setItem('lastCoords', JSON.stringify(coords)); } catch (e) {}
                        updateWeatherData(coords);
                    }, function(err) {
                        clearTimeout(t);
                        console.warn('Geolocation denied or failed', err);
                        alert('Unable to retrieve your location. Please check browser permissions.');
                    }, { timeout: 10000 });
                };

                // Prefer to place the button inside the placeholder under Column 1
                const placeholder = document.getElementById('weather-use-location-placeholder');
                if (placeholder) {
                    placeholder.appendChild(useLocBtn);
                } else if (weatherContainer) {
                    // Fallback: append to top-right area
                    useLocBtn.className += ' absolute top-4 right-4';
                    weatherContainer.appendChild(useLocBtn);
                }
            }

            // Wire manual coords testing button
            const manualBtn = document.getElementById('weather-manual-btn');
            if (manualBtn) {
                manualBtn.addEventListener('click', function() {
                    const lat = parseFloat(document.getElementById('weather-manual-lat').value);
                    const lon = parseFloat(document.getElementById('weather-manual-lon').value);
                    if (!isFinite(lat) || !isFinite(lon)) {
                        alert('Please enter valid numeric latitude and longitude');
                        return;
                    }
                    const coords = { lat, lon };
                    try { localStorage.setItem('lastCoords', JSON.stringify(coords)); } catch (e) {}
                    updateWeatherData(coords);
                });
            }
        }

        // Try LocalStorage first for faster startup but prefer geolocation-first (wait up to N ms)
        let stored = null;
        try {
            stored = JSON.parse(localStorage.getItem('lastCoords')) || null;
        } catch (e) {
            stored = null;
        }

        const GEO_WAIT_MS = 4000; // Reduced to 4s for faster page load
        const alertEl = document.getElementById('weather-location-alert');
        const cityElement = document.querySelector('.weather-city');

        // Show cached data immediately for better UX
        const cached = getCachedWeather();
        if (cached && cached.weather) {
            updateWeatherDisplay(cached.weather, cached.forecast || [], cached.hourly || []);
            if (cityElement) cityElement.textContent = cached.weather.city || 'Loading...';
        } else {
            // Only show 'Updating...' if no cache available
            if (cityElement) {
                cityElement.textContent = 'Updating...';
            }
            if (alertEl) {
                alertEl.className = 'mt-3 p-2 rounded text-sm font-medium bg-white/10 text-white';
                alertEl.textContent = 'Loading weather...';
                alertEl.classList.remove('hidden');
            }
        }

        // Wait up to GEO_WAIT_MS for geolocation. If it doesn't arrive, use stored/default coords.
        const geoWait = setTimeout(() => {
            if (weatherStarted) return;
            console.warn('Geolocation wait timed out');
            if (stored && stored.lat && stored.lon) {
                // Use saved location if available
                startUpdates(stored);
                if (alertEl) {
                    alertEl.className = 'mt-3 p-2 rounded text-sm font-medium bg-white/10 text-white/60';
                    alertEl.textContent = 'Using saved location (updating if geolocation becomes available)...';
                }
            } else {
                // No saved location and geolocation didn't arrive: show Unknown and prompt user
                if (cityElement) {
                    cityElement.textContent = 'Unknown';
                }
                if (alertEl) {
                    alertEl.className = 'mt-3 p-2 rounded text-sm font-medium bg-yellow-100 text-yellow-800';
                    alertEl.textContent = 'Unable to determine your location. Click "Use my location" or enter coordinates manually.';
                    alertEl.classList.remove('hidden');
                }
                console.warn('No stored coords and geolocation timed out — showing Unknown');
            }
        }, GEO_WAIT_MS);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                clearTimeout(geoWait);
                const coords = { lat: position.coords.latitude, lon: position.coords.longitude };
                try { localStorage.setItem('lastCoords', JSON.stringify(coords)); } catch (e) {}
                if (alertEl) {
                    alertEl.classList.add('hidden');
                }
                // If we haven't started weather updates yet, start with geolocation.
                if (!weatherStarted) {
                    startUpdates(coords);
                } else {
                    // Refresh to new coords
                    updateWeatherData(coords, true); // Skip cache for fresh geolocation data
                }
            }, function(err) {
                clearTimeout(geoWait);
                console.warn('Geolocation failed or denied:', err && err.message);
                if (!weatherStarted) {
                    if (stored && stored.lat && stored.lon) {
                        startUpdates(stored);
                    } else {
                        // No stored coords: set Unknown and show helpful alert
                        if (cityElement && !cached) {
                            cityElement.textContent = 'Unknown';
                        }
                        if (alertEl && !cached) {
                            alertEl.className = 'mt-3 p-2 rounded text-sm font-medium bg-yellow-100 text-yellow-800';
                            if (err && err.code === 1) {
                                alertEl.textContent = 'Location permission denied.';
                            } else if (err && err.code === 3) {
                                alertEl.textContent = 'Location request timed out.';
                            } else {
                                alertEl.textContent = 'Unable to retrieve location.';
                            }
                            alertEl.classList.remove('hidden');
                        }
                    }
                }
            }, { 
                enableHighAccuracy: false, 
                timeout: GEO_WAIT_MS, 
                maximumAge: 300000 // Use 5-minute old position to speed up
            });
        } else {
            // Geolocation not supported - if stored coords available use them, otherwise show Unknown
            console.warn('Browser does not support geolocation');
            clearTimeout(geoWait);
            if (!weatherStarted) {
                if (stored && stored.lat && stored.lon) {
                    startUpdates(stored);
                } else {
                    if (cityElement) cityElement.textContent = 'Unknown';
                    if (alertEl) {
                        alertEl.className = 'mt-3 p-2 rounded text-sm font-medium bg-yellow-100 text-yellow-800';
                        alertEl.textContent = 'Geolocation is not supported by your browser. Enter coordinates or use "Use my location".';
                        alertEl.classList.remove('hidden');
                    }
                }
            }
        }
    });

    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (weatherUpdateInterval) {
            clearInterval(weatherUpdateInterval);
        }
    });
</script>

@endif


{{-- Hiker's Unique Features Section --}}
@if(isset($user) && $user && $user->user_type === 'hiker')
<div id="hiking-tools" class="px-6 lg:px-8 py-12 bg-gradient-to-br from-green-50 via-blue-50 to-emerald-50 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 left-10 w-32 h-32 bg-green-400 rounded-full"></div>
        <div class="absolute top-32 right-20 w-24 h-24 bg-blue-400 rounded-full"></div>
        <div class="absolute bottom-20 left-1/4 w-20 h-20 bg-emerald-400 rounded-full"></div>
    </div>

    <div class="relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-500 to-blue-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                </svg>
            </div>
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Your Essential Hiking Tools</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Two powerful tools designed to ensure your hiking adventures are safe, enjoyable, and perfectly planned.
            </p>
        </div>

        <!-- Tools Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Pre-Hike Self-Assessment Card -->
            <div class="group bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 overflow-hidden relative">
                <!-- Status Badge -->
                <div class="absolute top-6 right-6 z-20">
                    @if($latestAssessment)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Completed
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Required
                    </span>
                    @endif
                </div>

                <div class="relative h-56">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500 via-emerald-600 to-teal-700 group-hover:from-green-600 group-hover:via-emerald-700 group-hover:to-teal-800 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-7xl group-hover:scale-110 transition-transform duration-500">🥾</span>
                    </div>
                    <div class="absolute bottom-6 left-6 right-6">
                        <h3 class="text-2xl font-bold text-white mb-2">Pre-Hike Self-Assessment</h3>
                        <p class="text-white/90 text-sm leading-relaxed">Comprehensive readiness evaluation for safe hiking</p>
                    </div>
                </div>

                <div class="p-8">
                    @if($latestAssessment)
                    <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-200">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-semibold text-green-800 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Latest Assessment
                            </span>
                            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">{{ $latestAssessment->completed_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600">{{ $latestAssessment->overall_score }}%</div>
                                <div class="text-xs text-green-700">Overall Score</div>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-green-800 mb-1">{{ $latestAssessment->readiness_level }}</div>
                                <div class="w-full bg-green-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $latestAssessment->overall_score }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('assessment.saved-results') }}" class="block w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white text-center py-4 px-6 rounded-2xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            View Detailed Results
                        </a>
                        <a href="{{ route('assessment.instruction') }}" class="block w-full bg-gray-100 text-gray-700 text-center py-3 px-6 rounded-2xl font-medium hover:bg-gray-200 transition-all duration-300">
                            Retake Assessment
                        </a>
                    </div>
                    @else
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Assessment Required</h4>
                                <p class="text-sm text-gray-600">Complete this before your first hike</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Evaluate your fitness, gear, health, weather awareness, emergency preparedness, and environmental factors to ensure you're ready for safe hiking.
                        </p>
                    </div>
                    <a href="{{ route('assessment.instruction') }}" class="block w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white text-center py-4 px-6 rounded-2xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Start Your Assessment
                    </a>
                    @endif
                </div>
            </div>

            <!-- Itinerary Builder Card -->
            <div class="group bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 overflow-hidden relative">
                <!-- Status Badge -->
                <div class="absolute top-6 right-6 z-20">
                    @if($latestItinerary)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Active
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Ready to Build
                    </span>
                    @endif
                </div>

                <div class="relative h-56">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-700 group-hover:from-blue-600 group-hover:via-indigo-700 group-hover:to-purple-800 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-7xl group-hover:scale-110 transition-transform duration-500">🗺️</span>
                    </div>
                    <div class="absolute bottom-6 left-6 right-6">
                        <h3 class="text-2xl font-bold text-white mb-2">Itinerary Builder</h3>
                        <p class="text-white/90 text-sm leading-relaxed">Create personalized hiking plans and routes</p>
                    </div>
                </div>

                <div class="p-8">
                    @if($latestItinerary)
                    <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-semibold text-blue-800 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Latest Itinerary
                            </span>
                            <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">{{ $latestItinerary->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-semibold text-blue-800 text-lg">{{ $latestItinerary->title }}</h4>
                            <div class="flex items-center gap-4 text-sm">
                                <span class="text-blue-700 bg-blue-100 px-2 py-1 rounded-full">{{ $latestItinerary->trail_name }}</span>
                                <span class="text-blue-700 bg-blue-100 px-2 py-1 rounded-full">{{ $latestItinerary->difficulty_level }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('itinerary.build') }}" class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center py-4 px-6 rounded-2xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create New Itinerary
                        </a>
                        <a href="{{ route('itinerary.build') }}" class="block w-full bg-gray-100 text-gray-700 text-center py-3 px-6 rounded-2xl font-medium hover:bg-gray-200 transition-all duration-300">
                            View All Itineraries
                        </a>
                    </div>
                    @else
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Plan Your Adventure</h4>
                                <p class="text-sm text-gray-600">Build your perfect hiking route</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Create personalized hiking itineraries with optimized routes, safety protocols, emergency contacts, and offline access for your adventures.
                        </p>
                    </div>
                    <a href="{{ route('itinerary.build') }}" class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center py-4 px-6 rounded-2xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Start Building Your Itinerary
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Access Bar -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-center md:text-left">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Quick Access</h3>
                    <p class="text-sm text-gray-600">Get to your tools faster</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('hiking-tools') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        All Tools
                    </a>
                    <a href="{{ route('explore') }}" class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-xl font-medium hover:bg-green-200 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Explore Trails
                    </a>
                    <a href="{{ route('community.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl font-medium hover:bg-emerald-200 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Community
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Events Section for Hikers --}}
@if(isset($user) && $user && $user->user_type === 'hiker')
<div id="events-section" class="px-6 lg:px-8 py-12 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 left-10 w-32 h-32 bg-orange-400 rounded-full"></div>
        <div class="absolute top-32 right-20 w-24 h-24 bg-amber-400 rounded-full"></div>
        <div class="absolute bottom-20 left-1/4 w-20 h-20 bg-yellow-400 rounded-full"></div>
    </div>

    <div class="relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-orange-500 to-amber-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Upcoming Hiking Events</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Join organized hikes, workshops, and community gatherings from organizations you follow
            </p>
        </div>

        @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($upcomingEvents as $event)
            @php
                $now = \Carbon\Carbon::now();
                if (!empty($event->always_available)) {
                    $dateLabel = 'Always';
                    $dayLabel = 'Open';
                } else {
                    $dateLabel = $event->start_at ? $event->start_at->format('M') : 'TBA';
                    $dayLabel = $event->start_at ? $event->start_at->format('d') : '';
                }
            @endphp
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden group flex flex-col">
                <div class="relative h-48 bg-gradient-to-br from-orange-400 to-amber-500 group-hover:from-orange-500 group-hover:to-amber-600 transition-all duration-300">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-2 rounded-lg text-center min-w-[4rem]">
                        <div class="text-xs font-semibold text-orange-600">{{ $dateLabel }}</div>
                        <div class="text-lg font-bold text-orange-800">{{ $dayLabel }}</div>
                    </div>
                    @if($event->is_free ?? false)
                    <div class="absolute top-4 left-4 bg-emerald-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                        Free
                    </div>
                    @elseif(isset($event->price) && $event->price > 0)
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">
                        ₱{{ number_format($event->price, 0) }}
                    </div>
                    @endif
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex-grow">
                        <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2 min-h-[3.5rem]">{{ $event->title }}</h3>
                        <p class="text-sm text-gray-500 mb-3">
                            <span class="font-medium">{{ optional($event->user)->display_name ?? 'Organization' }}</span>
                            @if(!empty($event->always_available))
                            <span class="text-emerald-600"> • Always Open</span>
                            @else
                            <span> • {{ $event->start_at ? $event->start_at->format('M d, Y g:ia') : 'TBA' }}</span>
                            @endif
                        </p>
                        
                        @if($event->hiking_start_time)
                        <div class="flex items-center text-sm text-emerald-700 font-medium mb-3">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Hike starts at {{ \Carbon\Carbon::parse($event->hiking_start_time)->format('g:i A') }}</span>
                        </div>
                        @endif
                        
                        <div class="min-h-[1.25rem] mb-3">
                            @if($event->location_name ?? false)
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                <span class="line-clamp-1">{{ $event->location_name }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="min-h-[2.5rem] mb-4">
                            @if($event->description)
                            <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($event->description, 100) }}</p>
                            @endif
                        </div>

                        <div class="min-h-[1.5rem] mb-4">
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
                            <p class="text-xs text-red-600 font-semibold">⏰ Ends in {{ $short }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <a href="{{ route('hiker.events.show', $event->slug) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-600 text-white rounded-xl font-semibold hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow mt-auto">
                        View Event
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- No Events State -->
        <div class="text-center py-12 bg-white rounded-2xl shadow-lg border border-gray-100 max-w-2xl mx-auto mb-8">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Upcoming Events</h3>
            <p class="text-gray-600 mb-6">
                @if(isset($followingCount) && $followingCount > 0)
                    The organizations you follow don't have upcoming events at the moment.
                @else
                    Follow some organizations to see their events here.
                @endif
            </p>
            <a href="{{ route('community.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-600 text-white rounded-xl font-semibold hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"></path>
                </svg>
                Discover Organizations
            </a>
        </div>
        @endif

        <!-- View All Events Button -->
        <div class="text-center">
            <a href="{{ route('community.index') }}#events" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-600 text-white rounded-xl font-semibold hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                View All Events
            </a>
        </div>
    </div>
</div>
@endif

{{-- Community Section for Hikers --}}
@if(isset($user) && $user && $user->user_type === 'hiker' && (isset($followedTrails) && $followedTrails->count() > 0))
<div id="community-section" class="px-6 lg:px-8 py-12 bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 right-10 w-32 h-32 bg-purple-400 rounded-full"></div>
        <div class="absolute top-32 left-20 w-24 h-24 bg-pink-400 rounded-full"></div>
        <div class="absolute bottom-20 right-1/4 w-20 h-20 bg-indigo-400 rounded-full"></div>
    </div>

    <div class="relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Community Trails</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Latest trails from organizations you follow
                @if(isset($followingCount) && $followingCount > 0)
                <span class="text-purple-600 font-semibold">({{ $followingCount }} {{ $followingCount === 1 ? 'organization' : 'organizations' }})</span>
                @endif
            </p>
        </div>

        <!-- Community Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 border border-purple-100">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $followingCount ?? 0 }}</div>
                <div class="text-sm text-gray-600">Organizations Following</div>
            </div>
            <div class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 border border-pink-100">
                <div class="text-3xl font-bold text-pink-600 mb-2">{{ $followedTrails->count() ?? 0 }}</div>
                <div class="text-sm text-gray-600">New Trails Available</div>
            </div>
            <div class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 border border-indigo-100">
                <div class="text-3xl font-bold text-indigo-600 mb-2">
                    {{ $followedTrails->sum(function($trail) { return $trail->reviews()->where('user_id', auth()->id())->count(); }) }}
                </div>
                <div class="text-sm text-gray-600">Your Reviews</div>
            </div>
        </div>

        <!-- Followed Trails Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($followedTrails as $trail)
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden group">
                <div class="relative h-48">
                    @php
                    // Get dynamic image from enhanced TrailImageService
                    $primaryImage = $imageService->getPrimaryTrailImage($trail);
                    $trailImage = $primaryImage['url'];
                    @endphp
                    <img src="{{ $trailImage }}"
                        alt="{{ $trail->trail_name }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                    <!-- Image source badge for API images -->
                    @if($primaryImage['source'] !== 'organization')
                    <div class="absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded">
                        {{ ucfirst($primaryImage['source']) }}
                    </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-white px-2 py-1 rounded-full {{ $trail->difficulty === 'easy' ? 'bg-green-600' : ($trail->difficulty === 'moderate' ? 'bg-yellow-600' : 'bg-red-600') }}">
                                {{ $trail->difficulty_label }}
                            </span>
                            <div class="flex items-center text-white text-xs">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Following
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-purple-600 transition-colors duration-300 truncate">
                            {{ $trail->trail_name }}
                        </h3>
                    </div>
                    <p class="text-sm text-gray-500 mb-2">by {{ $trail->user->display_name }}</p>
                    <p class="text-xs text-gray-400 mb-4">{{ $trail->location->name ?? 'Location not set' }}</p>

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= round($trail->average_rating) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                    </svg>
                                    @endfor
                            </div>
                            <span class="text-sm text-gray-600 ml-2">{{ number_format($trail->average_rating, 1) }} ({{ $trail->total_reviews }})</span>
                        </div>
                        <span class="text-lg font-bold text-purple-600">₱{{ number_format(optional($trail->package)->price ?? $trail->price ?? 0, 0) }}</span>
                    </div>

                    <a href="{{ route('trails.show', $trail->slug) }}"
                        class="block w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:scale-105">
                        View & Review Trail
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Community Actions -->
        <div class="text-center">
            <div class="inline-flex flex-col sm:flex-row gap-4">
                <a href="{{ route('community.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Explore Community
                </a>
                <a href="{{ route('explore') }}"
                    class="inline-flex items-center px-6 py-3 bg-white text-purple-600 border-2 border-purple-600 rounded-xl font-semibold hover:bg-purple-600 hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Discover Organizations
                </a>
            </div>
        </div>
    </div>
</div>
@elseif(isset($user) && $user && $user->user_type === 'hiker')
{{-- Show community invitation for hikers not following anyone yet --}}
<div id="community-invitation" class="px-6 lg:px-8 py-12 bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 relative overflow-hidden">
    <div class="relative z-10 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Join the Community</h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
            Connect with hiking organizations, discover exclusive trails, and share your adventures through reviews and experiences.
        </p>
        <div class="bg-white rounded-2xl p-8 shadow-xl border border-purple-100 max-w-md mx-auto mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Community Features</h3>
            <div class="space-y-4 text-left">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-gray-700">Follow trusted hiking organizations</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <span class="text-gray-700">Review trails you've experienced</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                        </svg>
                    </div>
                    <span class="text-gray-700">Access exclusive trails & content</span>
                </div>
            </div>
        </div>
        <a href="{{ route('community.index') }}"
            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 shadow-lg text-lg">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Start Building Your Network
        </a>
    </div>
</div>
@endif



<!-- Floating Action Button for Hiking Tools -->
@if(isset($user) && $user && $user->user_type === 'hiker')
<div class="fixed bottom-4 right-4 md:bottom-6 md:right-6 z-50">
    <div class="relative group">
        <!-- Main FAB -->
        <button id="hiking-tools-fab" class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-110 flex items-center justify-center">
            <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
        </button>

        <!-- Tooltip (Hidden on mobile) -->
        <div class="absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap hidden md:block">
            Hiking Tools
            <div class="absolute top-full right-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
        </div>

        <!-- Quick Actions Menu -->
        <div id="quick-actions-menu" class="absolute bottom-full right-0 mb-4 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 md:block">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-3 md:p-4 min-w-[180px] md:min-w-[200px]">
                <div class="text-center mb-3">
                    <h4 class="font-semibold text-gray-800 text-sm md:text-base">Quick Access</h4>
                    <p class="text-xs text-gray-500">Essential hiking tools</p>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('assessment.instruction') }}" class="flex items-center p-2 md:p-3 rounded-xl hover:bg-green-50 transition-colors duration-200 group/item">
                        <div class="w-7 h-7 md:w-8 md:h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2 md:mr-3 group-hover/item:bg-green-200 transition-colors duration-200">
                            <svg class="w-3 h-3 md:w-4 md:h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-800 text-xs md:text-sm">Assessment</div>
                            <div class="text-xs text-gray-500">Check readiness</div>
                        </div>
                    </a>
                    <a href="{{ route('itinerary.build') }}" class="flex items-center p-2 md:p-3 rounded-xl hover:bg-blue-50 transition-colors duration-200 group/item">
                        <div class="w-7 h-7 md:w-8 md:h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2 md:mr-3 group-hover/item:bg-blue-200 transition-colors duration-200">
                            <svg class="w-3 h-3 md:w-4 md:h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-800 text-xs md:text-sm">Itinerary</div>
                            <div class="text-xs text-gray-500">Plan your hike</div>
                        </div>
                    </a>
                    <a href="{{ route('hiking-tools') }}" class="flex items-center p-2 md:p-3 rounded-xl hover:bg-gray-50 transition-colors duration-200 group/item">
                        <div class="w-7 h-7 md:w-8 md:h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-2 md:mr-3 group-hover/item:bg-gray-200 transition-colors duration-200">
                            <svg class="w-3 h-3 md:w-4 md:h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-800 text-xs md:text-sm">All Tools</div>
                            <div class="text-xs text-gray-500">Complete toolkit</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                fetch(`/location-weather?lat=${position.coords.latitude}&lon=${position.coords.longitude}`);
            });
        }

        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add intersection observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all hiking tool cards and trail cards
        document.querySelectorAll('.hiking-tool-card, .trail-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(card);
        });
    });
</script>

{{-- Real-time Event Polling for Hikers --}}
@if(isset($user) && $user && $user->user_type === 'hiker')
@vite(['resources/js/event-poller.js'])
@endif
