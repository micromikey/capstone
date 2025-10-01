@props(['weather' => null])

@if(isset($weather) && $weather)
@php
    // Determine day or night from weather icon (OpenWeatherMap: icon ends with 'd' for day, 'n' for night)
    $icon = $weather['icon'] ?? '01d';
    $isNight = str_ends_with($icon, 'n');
    $weatherCondition = strtolower($weather['main'] ?? 'clear');
    // Day and night gradients for each condition
    $gradients = [
        'clear' => [
            'day' => 'bg-gradient-to-br from-yellow-100/95 via-orange-50/95 to-yellow-200/95 border-yellow-200/50',
            'night' => 'bg-gradient-to-br from-blue-900/90 via-indigo-900/90 to-slate-800/90 border-blue-900/50',
        ],
        'clouds' => [
            'day' => 'bg-gradient-to-br from-gray-100/95 via-slate-50/95 to-gray-200/95 border-gray-200/50',
            'night' => 'bg-gradient-to-br from-gray-800/90 via-slate-900/90 to-gray-700/90 border-gray-800/50',
        ],
        'rain' => [
            'day' => 'bg-gradient-to-br from-blue-100/95 via-indigo-50/95 to-blue-200/95 border-blue-200/50',
            'night' => 'bg-gradient-to-br from-blue-900/90 via-indigo-900/90 to-blue-800/90 border-blue-900/50',
        ],
        'drizzle' => [
            'day' => 'bg-gradient-to-br from-blue-50/95 via-cyan-50/95 to-blue-100/95 border-blue-100/50',
            'night' => 'bg-gradient-to-br from-cyan-900/90 via-blue-900/90 to-cyan-800/90 border-cyan-900/50',
        ],
        'thunderstorm' => [
            'day' => 'bg-gradient-to-br from-purple-100/95 via-indigo-100/95 to-purple-200/95 border-purple-200/50',
            'night' => 'bg-gradient-to-br from-indigo-900/90 via-purple-900/90 to-indigo-800/90 border-indigo-900/50',
        ],
        'snow' => [
            'day' => 'bg-gradient-to-br from-blue-50/95 via-white/95 to-slate-100/95 border-slate-200/50',
            'night' => 'bg-gradient-to-br from-slate-900/90 via-blue-900/90 to-slate-800/90 border-slate-900/50',
        ],
        'mist' => [
            'day' => 'bg-gradient-to-br from-gray-50/95 via-slate-100/95 to-gray-100/95 border-gray-300/50',
            'night' => 'bg-gradient-to-br from-gray-900/90 via-slate-900/90 to-gray-800/90 border-gray-900/50',
        ],
        'fog' => [
            'day' => 'bg-gradient-to-br from-gray-50/95 via-slate-100/95 to-gray-100/95 border-gray-300/50',
            'night' => 'bg-gradient-to-br from-gray-900/90 via-slate-900/90 to-gray-800/90 border-gray-900/50',
        ],
        'haze' => [
            'day' => 'bg-gradient-to-br from-gray-50/95 via-slate-100/95 to-gray-100/95 border-gray-300/50',
            'night' => 'bg-gradient-to-br from-gray-900/90 via-slate-900/90 to-gray-800/90 border-gray-900/50',
        ],
        'smoke' => [
            'day' => 'bg-gradient-to-br from-orange-50/95 via-amber-50/95 to-orange-100/95 border-orange-200/50',
            'night' => 'bg-gradient-to-br from-amber-900/90 via-orange-900/90 to-amber-800/90 border-amber-900/50',
        ],
        'dust' => [
            'day' => 'bg-gradient-to-br from-amber-100/95 via-yellow-100/95 to-amber-200/95 border-amber-200/50',
            'night' => 'bg-gradient-to-br from-yellow-900/90 via-amber-900/90 to-yellow-800/90 border-yellow-900/50',
        ],
        'sand' => [
            'day' => 'bg-gradient-to-br from-amber-100/95 via-yellow-100/95 to-amber-200/95 border-amber-200/50',
            'night' => 'bg-gradient-to-br from-yellow-900/90 via-amber-900/90 to-yellow-800/90 border-yellow-900/50',
        ],
        'squall' => [
            'day' => 'bg-gradient-to-br from-red-100/95 via-orange-100/95 to-red-200/95 border-red-200/50',
            'night' => 'bg-gradient-to-br from-red-900/90 via-orange-900/90 to-red-800/90 border-red-900/50',
        ],
        'tornado' => [
            'day' => 'bg-gradient-to-br from-red-100/95 via-orange-100/95 to-red-200/95 border-red-200/50',
            'night' => 'bg-gradient-to-br from-red-900/90 via-orange-900/90 to-red-800/90 border-red-900/50',
        ],
        'default' => [
            'day' => 'bg-gradient-to-br from-blue-50/95 via-sky-50/95 to-blue-100/95 border-blue-200/50',
            'night' => 'bg-gradient-to-br from-blue-900/90 via-sky-900/90 to-blue-800/90 border-blue-900/50',
        ],
    ];
    $dayNight = $isNight ? 'night' : 'day';
    $weatherGradient = $gradients[$weatherCondition][$dayNight] ?? $gradients['default'][$dayNight];
@endphp
<!-- Floating Weather Card -->
<div id="floating-weather" class="fixed top-48 right-10 z-40 transition-all duration-300 transform {{ $isNight ? 'night' : '' }}" data-weather="{{ $weatherCondition }}">
    <div class="{{ $weatherGradient }} backdrop-blur-md rounded-2xl shadow-xl p-4 min-w-[250px] max-w-[300px]">
        <!-- Header -->
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold weather-text-main flex items-center">
                <svg class="w-4 h-4 mr-2 text-current opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                </svg>
                Current Weather
            </h3>
            <button id="toggle-weather-card" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Current Weather -->
        <div class="mb-4">
            <div class="grid grid-cols-2 gap-2 items-center">
                <!-- Column 1: Basic Weather Data -->
                <div class="flex flex-col items-center">
                <img id="floating-weather-icon" src="https://openweathermap.org/img/wn/{{ $weather['icon'] ?? '01d' }}@2x.png" 
                    alt="{{ $weather['description'] ?? 'Clear' }}" 
                    class="w-10 h-10 mb-1">
                <div id="floating-weather-temp" class="text-xl font-bold weather-text-main">{{ $weather['temp'] ?? 25 }}°</div>
                <div id="floating-weather-desc" class="text-xs weather-text-secondary capitalize leading-tight">{{ $weather['description'] ?? 'Clear sky' }}</div>
                </div>
                <!-- Column 2: Weather Details, 4 rows -->
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-1 weather-text-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12a6 6 0 0011.41 2.822l1.74 1.74a1 1 0 001.415-1.414l-1.74-1.74A6 6 0 006 12z"></path>
                        </svg>
                        <span id="floating-weather-feels" class="text-xs font-semibold">Feels {{ $weather['feels_like'] ?? 27 }}°</span>
                    </div>
                    <div class="flex items-center gap-1 weather-text-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <span id="floating-weather-humid" class="text-xs font-semibold">Humid {{ $weather['humidity'] ?? 65 }}%</span>
                    </div>
                    <div class="flex items-center gap-1 weather-text-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span id="floating-weather-uv" class="text-xs font-semibold">UV {{ $weather['uv_index'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center gap-1 weather-text-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 3v12a2 2 0 002 2h6a2 2 0 002-2V7H7z"></path>
                        </svg>
                        <span id="floating-weather-wind" class="text-xs font-semibold">Wind {{ $weather['wind_speed'] ?? 5 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Updated and actions -->
        <div class="mt-3 pt-2 border-t border-gray-200">
            <div class="flex items-center justify-between gap-3 text-xs weather-text-secondary">
                <div class="flex items-center gap-3">
                    <span class="weather-text-secondary">Last updated</span>
                    <span id="weather-last-updated" class="weather-text-secondary">{{ \Carbon\Carbon::now()->format('H:i') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Inline 'Use my location' button (non-absolute to avoid overlap) -->
                    <button id="floating-use-location-inline" class="use-location-btn text-xs px-2 py-1 rounded bg-white/10 hover:bg-white/20 text-white/80">Use my location</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Collapsed Weather Card -->
<div id="floating-weather-minimized" class="fixed top-48 right-10 z-40 hidden">
    <button id="expand-weather-card" class="{{ $weatherGradient }} backdrop-blur-md rounded-full shadow-xl p-3 hover:brightness-110 transition-all duration-300 transform hover:scale-105">
        <svg class="w-5 h-5 text-current opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
        </svg>
    </button>
</div>

<style>
    /* Weather card specific styles with dynamic gradients */
    #floating-weather {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #floating-weather:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        filter: brightness(1.05);
    }

    /* Weather-specific animations */
    #floating-weather[data-weather="rain"]::before,
    #floating-weather[data-weather="drizzle"]::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(59, 130, 246, 0.1) 50%, transparent 70%);
        animation: rainShimmer 3s infinite;
        border-radius: 1rem;
        pointer-events: none;
    }

    #floating-weather[data-weather="thunderstorm"]::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(147, 51, 234, 0.2) 50%, transparent 70%);
        animation: thunderFlash 4s infinite;
        border-radius: 1rem;
        pointer-events: none;
    }

    #floating-weather[data-weather="clear"]::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(251, 191, 36, 0.1) 50%, transparent 70%);
        animation: sunGlow 4s infinite;
        border-radius: 1rem;
        pointer-events: none;
    }

    @keyframes rainShimmer {
        0%, 100% { opacity: 0; transform: translateX(-100%); }
        50% { opacity: 1; transform: translateX(100%); }
    }

    @keyframes thunderFlash {
        0%, 95%, 100% { opacity: 0; }
        2%, 8% { opacity: 1; }
    }

    @keyframes sunGlow {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 0.7; }
    }

    /* Hide floating weather on mobile devices for better responsiveness */
    @media (max-width: 1024px) {
        #floating-weather,
        #floating-weather-minimized {
            display: none !important;
        }
    }


    /* Weather card text color adaptation */
    .weather-text-main {
        color: #1e293b;
    }
    .weather-text-secondary {
        color: #334155;
    }
    [data-weather].night .weather-text-main,
    [data-weather].night .weather-text-secondary {
        color: #f1f5f9 !important;
    }

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const weatherCard = document.getElementById('floating-weather');
    const minimizedWeather = document.getElementById('floating-weather-minimized');
    const toggleBtn = document.getElementById('toggle-weather-card');
    const expandBtn = document.getElementById('expand-weather-card');

    // Replace server-rendered last-updated with client-localized time immediately
    try {
        const lastUpdatedEl = document.getElementById('weather-last-updated');
        if (lastUpdatedEl) {
            const now = new Date();
            lastUpdatedEl.textContent = now.toLocaleTimeString(undefined, { hour12: false, hour: '2-digit', minute: '2-digit' });
        }
    } catch (e) {}

    // Toggle weather card
    toggleBtn?.addEventListener('click', function() {
        weatherCard.classList.add('hidden');
        minimizedWeather.classList.remove('hidden');
    });

    expandBtn?.addEventListener('click', function() {
        minimizedWeather.classList.add('hidden');
        weatherCard.classList.remove('hidden');
    });

    // Weather update system with caching
    let weatherUpdateInterval;
    let activeWeatherRequest = null;
    let lastWeatherFetchAt = 0;
    const WEATHER_FETCH_COOLDOWN_MS = 3000; // 3s cooldown
    const WEATHER_CACHE_DURATION_MS = 2 * 60 * 1000; // 2 minutes cache

    // Helper: Get cached weather data if still valid
    function getCachedWeather() {
        try {
            const cached = localStorage.getItem('floatingWeatherCache');
            if (!cached) return null;
            
            const data = JSON.parse(cached);
            const age = Date.now() - (data.timestamp || 0);
            
            if (age < WEATHER_CACHE_DURATION_MS) {
                console.debug('Using cached floating weather (age: ' + Math.round(age / 1000) + 's)');
                return data.weather;
            }
        } catch (e) {
            console.warn('Failed to read floating weather cache:', e);
        }
        return null;
    }

    // Helper: Save weather data to cache
    function cacheWeather(weatherData) {
        try {
            localStorage.setItem('floatingWeatherCache', JSON.stringify({
                weather: weatherData,
                timestamp: Date.now()
            }));
        } catch (e) {
            console.warn('Failed to cache floating weather:', e);
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
            console.debug('Skipping floating weather fetch due to cooldown');
            return;
        }
        lastWeatherFetchAt = now;

        // Show cached data immediately if available (unless explicitly skipping)
        if (!skipCache) {
            const cached = getCachedWeather();
            if (cached) {
                updateWeatherDisplay(cached.weather, cached.forecast);
            }
        }

        // coords: { lat, lon } optional
        let url = '/api/weather/current';
        if (coords && coords.lat && coords.lon) {
            url += `?lat=${encodeURIComponent(coords.lat)}&lon=${encodeURIComponent(coords.lon)}`;
        }

        // Use AbortController for cancellable fetch
        activeWeatherRequest = new AbortController();
        const signal = activeWeatherRequest.signal;
        // Use AbortController for cancellable fetch
        activeWeatherRequest = new AbortController();
        const signal = activeWeatherRequest.signal;

        const handleSuccess = function(data) {
            activeWeatherRequest = null;
            
            if (data && (data.weather || data.temp || data.city || data.location)) {
                // Ensure we have a weather object
                const weatherObj = data.weather || {
                    temp: (typeof data.temp !== 'undefined') ? data.temp : (data.main && data.main.temp ? data.main.temp : null),
                    feels_like: data.feels_like || (data.main && data.main.feels_like) || null,
                    humidity: data.humidity || (data.main && data.main.humidity) || null,
                    wind_speed: data.wind_speed || (data.wind && data.wind.speed) || null,
                    uv_index: data.uv_index || null,
                    icon: (data.weather && data.weather.icon) || (data.icon) || '01d',
                    description: (data.weather && data.weather.description) || (data.description) || '',
                    main: (data.weather && data.weather.main) || (data.main && data.main.weather && data.main.weather[0] && data.main.weather[0].main) || (data.description && data.description.split(' ')[0]) || 'Unknown'
                };
                
                // Cache the response
                cacheWeather({
                    weather: weatherObj,
                    forecast: data.forecast
                });
                
                updateWeatherDisplay(weatherObj, data.forecast);
            } else {
                console.warn('No usable weather data returned');
            }
        };

        const handleError = function(error) {
            activeWeatherRequest = null;
            
            // Don't show error if request was cancelled
            if (error.name === 'AbortError') {
                console.debug('Floating weather fetch cancelled');
                return;
            }
            
            console.error('Error updating floating weather:', error);
            
            // Only show error if we don't have cached data
            if (!getCachedWeather()) {
                const tempEl = document.querySelector('#floating-weather .weather-text-main');
                if (tempEl) tempEl.textContent = 'Unknown';
            }
        };

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
        // Update weather condition and gradient
        const weatherCard = document.getElementById('floating-weather');
        const minimizedWeather = document.getElementById('floating-weather-minimized');
        const expandBtn = document.getElementById('expand-weather-card');
        
        if (weather && weather.main) {
            const condition = weather.main.toLowerCase();
            // Detect day or night from icon (OpenWeatherMap: icon ends with 'd' for day, 'n' for night)
            const icon = weather.icon || '01d';
            const isNight = icon.endsWith('n');
            weatherCard.setAttribute('data-weather', condition);
            // Day and night gradients for each condition
            const gradients = {
                'clear': {
                    day: 'bg-gradient-to-br from-yellow-100/95 via-orange-50/95 to-yellow-200/95 border-yellow-200/50',
                    night: 'bg-gradient-to-br from-blue-900/90 via-indigo-900/90 to-slate-800/90 border-blue-900/50',
                },
                'clouds': {
                    day: 'bg-gradient-to-br from-gray-100/95 via-slate-50/95 to-gray-200/95 border-gray-200/50',
                    night: 'bg-gradient-to-br from-gray-800/90 via-slate-900/90 to-gray-700/90 border-gray-800/50',
                },
                'rain': {
                    day: 'bg-gradient-to-br from-blue-100/95 via-indigo-50/95 to-blue-200/95 border-blue-200/50',
                    night: 'bg-gradient-to-br from-blue-900/90 via-indigo-900/90 to-blue-800/90 border-blue-900/50',
                },
                'drizzle': {
                    day: 'bg-gradient-to-br from-blue-50/95 via-cyan-50/95 to-blue-100/95 border-blue-100/50',
                    night: 'bg-gradient-to-br from-cyan-900/90 via-blue-900/90 to-cyan-800/90 border-cyan-900/50',
                },
                'thunderstorm': {
                    day: 'bg-gradient-to-br from-purple-100/95 via-indigo-100/95 to-purple-200/95 border-purple-200/50',
                    night: 'bg-gradient-to-br from-indigo-900/90 via-purple-900/90 to-indigo-800/90 border-indigo-900/50',
                },
                'snow': {
                    day: 'bg-gradient-to-br from-blue-50/95 via-white/95 to-slate-100/95 border-slate-200/50',
                    night: 'bg-gradient-to-br from-slate-900/90 via-blue-900/90 to-slate-800/90 border-slate-900/50',
                },
                'mist': {
                    day: 'bg-gradient-to-br from-gray-50/95 via-slate-100/95 to-gray-100/95 border-gray-300/50',
                    night: 'bg-gradient-to-br from-gray-900/90 via-slate-900/90 to-gray-800/90 border-gray-900/50',
                },
                'fog': {
                    day: 'bg-gradient-to-br from-gray-50/95 via-slate-100/95 to-gray-100/95 border-gray-300/50',
                    night: 'bg-gradient-to-br from-gray-900/90 via-slate-900/90 to-gray-800/90 border-gray-900/50',
                },
                'haze': {
                    day: 'bg-gradient-to-br from-gray-50/95 via-slate-100/95 to-gray-100/95 border-gray-300/50',
                    night: 'bg-gradient-to-br from-gray-900/90 via-slate-900/90 to-gray-800/90 border-gray-900/50',
                },
                'smoke': {
                    day: 'bg-gradient-to-br from-orange-50/95 via-amber-50/95 to-orange-100/95 border-orange-200/50',
                    night: 'bg-gradient-to-br from-amber-900/90 via-orange-900/90 to-amber-800/90 border-amber-900/50',
                },
                'dust': {
                    day: 'bg-gradient-to-br from-amber-100/95 via-yellow-100/95 to-amber-200/95 border-amber-200/50',
                    night: 'bg-gradient-to-br from-yellow-900/90 via-amber-900/90 to-yellow-800/90 border-yellow-900/50',
                },
                'sand': {
                    day: 'bg-gradient-to-br from-amber-100/95 via-yellow-100/95 to-amber-200/95 border-amber-200/50',
                    night: 'bg-gradient-to-br from-yellow-900/90 via-amber-900/90 to-yellow-800/90 border-yellow-900/50',
                },
                'squall': {
                    day: 'bg-gradient-to-br from-red-100/95 via-orange-100/95 to-red-200/95 border-red-200/50',
                    night: 'bg-gradient-to-br from-red-900/90 via-orange-900/90 to-red-800/90 border-red-900/50',
                },
                'tornado': {
                    day: 'bg-gradient-to-br from-red-100/95 via-orange-100/95 to-red-200/95 border-red-200/50',
                    night: 'bg-gradient-to-br from-red-900/90 via-orange-900/90 to-red-800/90 border-red-900/50',
                },
                'default': {
                    day: 'bg-gradient-to-br from-blue-50/95 via-sky-50/95 to-blue-100/95 border-blue-200/50',
                    night: 'bg-gradient-to-br from-blue-900/90 via-sky-900/90 to-blue-800/90 border-blue-900/50',
                },
            };
            const dayNight = isNight ? 'night' : 'day';
            const newGradient = (gradients[condition] && gradients[condition][dayNight]) ? gradients[condition][dayNight] : gradients['default'][dayNight];
            // Remove old gradient classes and add new ones
            const cardDiv = weatherCard.querySelector('div');
            const expandBtnElement = expandBtn;
            // Reset classes
            cardDiv.className = cardDiv.className.replace(/bg-gradient-to-br.*?border-\w+-\d+\/\d+/g, '').trim();
            expandBtnElement.className = expandBtnElement.className.replace(/bg-gradient-to-br.*?border-\w+-\d+\/\d+/g, '').trim();
            // Add new gradient
            cardDiv.className += ' ' + newGradient;
            expandBtnElement.className += ' ' + newGradient;
            // Toggle night class on root containers so CSS selectors adapt text colors
            if (isNight) {
                weatherCard.classList.add('night');
                minimizedWeather.classList.add('night');
            } else {
                weatherCard.classList.remove('night');
                minimizedWeather.classList.remove('night');
            }
        }
        
        // Update last refresh time
        const now = new Date();
        // Use client's locale/timezone for the last-updated stamp
        const timeString = now.toLocaleTimeString(undefined, {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const lastUpdatedElement = document.getElementById('weather-last-updated');
        if (lastUpdatedElement) {
            lastUpdatedElement.textContent = timeString;
        }

        // Update visible weather fields (temp, description, icon, and details)
        try {
            const tempNode = document.getElementById('floating-weather-temp');
            const descNode = document.getElementById('floating-weather-desc');
            const iconNode = document.getElementById('floating-weather-icon');
            const feelsNode = document.getElementById('floating-weather-feels');
            const humidNode = document.getElementById('floating-weather-humid');
            const uvNode = document.getElementById('floating-weather-uv');
            const windNode = document.getElementById('floating-weather-wind');

            if (tempNode && typeof weather.temp !== 'undefined' && weather.temp !== null) tempNode.textContent = `${Math.round(weather.temp * 100) / 100}°`;
            if (descNode && weather.description) descNode.textContent = weather.description;
            if (iconNode && weather.icon) iconNode.src = `https://openweathermap.org/img/wn/${weather.icon}@2x.png`;
            if (feelsNode && typeof weather.feels_like !== 'undefined') feelsNode.textContent = `Feels ${weather.feels_like}°`;
            if (humidNode && typeof weather.humidity !== 'undefined') humidNode.textContent = `Humid ${weather.humidity}%`;
            if (uvNode) uvNode.textContent = `UV ${weather.uv_index ?? 'N/A'}`;
            if (windNode && typeof weather.wind_speed !== 'undefined') windNode.textContent = `Wind ${weather.wind_speed}`;
        } catch (e) {}
    }

    // Start auto-refresh with optimized settings
    let floatingWeatherStarted = false;
    const GEO_WAIT_MS = 4000; // Reduced to 4s for faster load

    function startFloatingUpdates(coords) {
        if (floatingWeatherStarted) return;
        floatingWeatherStarted = true;
        // Initial fetch (will use cache if available)
        updateWeatherData(coords);
        // Periodic updates every 3 minutes (with cache, fresher data)
        weatherUpdateInterval = setInterval(() => updateWeatherData(coords, true), 180000);
    }

    // Show cached data immediately for better UX
    const cached = getCachedWeather();
    if (cached && cached.weather) {
        updateWeatherDisplay(cached.weather, cached.forecast);
    } else {
        // Only show 'Updating...' if no cache available
        try {
            const tempEl = document.getElementById('floating-weather-temp');
            if (tempEl) tempEl.textContent = 'Updating...';
        } catch (e) {}
    }

    // Check for saved coords
    let storedCoords = null;
    try { storedCoords = JSON.parse(localStorage.getItem('lastCoords')) || null; } catch (e) { storedCoords = null; }

    const geoWait = setTimeout(() => {
        if (floatingWeatherStarted) return;
        console.warn('Floating weather geolocation wait timed out');
        if (storedCoords && storedCoords.lat && storedCoords.lon) {
            startFloatingUpdates(storedCoords);
        } else if (!cached) {
            // No saved coords and no cache: show Unknown
            try {
                const tempEl = document.getElementById('floating-weather-temp');
                if (tempEl) tempEl.textContent = 'Unknown';
            } catch (e) {}
        }
    }, GEO_WAIT_MS);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            clearTimeout(geoWait);
            const coords = { lat: position.coords.latitude, lon: position.coords.longitude };
            try { localStorage.setItem('lastCoords', JSON.stringify(coords)); } catch (e) {}
            if (!floatingWeatherStarted) startFloatingUpdates(coords);
            else updateWeatherData(coords, true); // Skip cache for fresh geolocation data
        }, function(err) {
            clearTimeout(geoWait);
            console.warn('Floating weather geolocation failed or denied:', err && err.message);
            if (!floatingWeatherStarted) {
                if (storedCoords && storedCoords.lat && storedCoords.lon) startFloatingUpdates(storedCoords);
                else if (!cached) {
                    try {
                    const tempEl = document.getElementById('floating-weather-temp');
                    if (tempEl) tempEl.textContent = 'Unknown';
                    } catch (e) {}
                }
            }
        }, { 
            enableHighAccuracy: false, 
            timeout: GEO_WAIT_MS, 
            maximumAge: 300000 // Use 5-minute old position to speed up
        });
    } else {
        clearTimeout(geoWait);
        if (!floatingWeatherStarted) {
            if (storedCoords && storedCoords.lat && storedCoords.lon) startFloatingUpdates(storedCoords);
            else if (!cached) {
                try {
                    const tempEl = document.getElementById('floating-weather-temp');
                    if (tempEl) tempEl.textContent = 'Unknown';
                } catch (e) {}
            }
        }
    }

    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (weatherUpdateInterval) {
            clearInterval(weatherUpdateInterval);
        }
    });

    // Hide weather card on scroll (optional - can be removed if you want it always visible)
    let lastScrollTop = 0;
    window.addEventListener('scroll', function() {
        const scrollTop = window.scrollY;
        
        if (scrollTop > lastScrollTop && scrollTop > 300) {
            // Scrolling down - fade out slightly
            weatherCard.style.opacity = '0.7';
        } else {
            // Scrolling up - full opacity
            weatherCard.style.opacity = '1';
        }
        
        lastScrollTop = scrollTop;
    }, { passive: true });

    // Wire the inline 'Use my location' button (rendered in the Last updated row)
    const inlineUseBtn = document.getElementById('floating-use-location-inline');
    if (inlineUseBtn) {
        inlineUseBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Geolocation not supported by your browser.');
                return;
            }

            const t = setTimeout(() => {
                alert('Unable to get location. Please try again.');
            }, 10000);

            navigator.geolocation.getCurrentPosition(function(position) {
                clearTimeout(t);
                const coords = { lat: position.coords.latitude, lon: position.coords.longitude };
                try { localStorage.setItem('lastCoords', JSON.stringify(coords)); } catch (e) {}
                updateWeatherData(coords, true); // Skip cache for manual location request
            }, function(err) {
                clearTimeout(t);
                console.warn('Floating weather geolocation denied/failed', err);
                alert('Unable to retrieve your location. Please check browser permissions.');
            }, { timeout: 10000 });
        });
    }
});
</script>
@endif