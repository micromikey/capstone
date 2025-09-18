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
                    <img src="https://openweathermap.org/img/wn/{{ $weather['icon'] ?? '01d' }}@2x.png" 
                         alt="{{ $weather['description'] ?? 'Clear' }}" 
                         class="w-10 h-10 mb-1">
                    <div class="text-xl font-bold weather-text-main">{{ $weather['temp'] ?? 25 }}°</div>
                    <div class="text-xs weather-text-secondary capitalize leading-tight">{{ $weather['description'] ?? 'Clear sky' }}</div>
                </div>
                <!-- Column 2: Weather Details, 4 rows -->
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-1 weather-text-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12a6 6 0 0011.41 2.822l1.74 1.74a1 1 0 001.415-1.414l-1.74-1.74A6 6 0 006 12z"></path>
                        </svg>
                        <span class="text-xs font-semibold">Feels {{ $weather['feels_like'] ?? 27 }}°</span>
                    </div>
                    <div class="flex items-center gap-1 weather-text-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <span class="text-xs font-semibold">Humid {{ $weather['humidity'] ?? 65 }}%</span>
                    </div>
                    <div class="flex items-center gap-1 weather-text-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="text-xs font-semibold">UV {{ $weather['uv_index'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center gap-1 weather-text-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 3v12a2 2 0 002 2h6a2 2 0 002-2V7H7z"></path>
                        </svg>
                        <span class="text-xs font-semibold">Wind {{ $weather['wind_speed'] ?? 5 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Updated -->
        <div class="mt-3 pt-2 border-t border-gray-200">
            <div class="flex items-center justify-between text-xs weather-text-secondary">
                <span class="weather-text-secondary">Last updated</span>
                <span id="weather-last-updated" class="weather-text-secondary">{{ \Carbon\Carbon::now()->setTimezone('Asia/Manila')->format('H:i') }}</span>
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

    /* Responsive design for weather card */
    @media (max-width: 1024px) {
        #floating-weather,
        #floating-weather-minimized {
            right: 6px;
            transform: scale(0.95);
        }
    }

    @media (max-width: 768px) {
        #floating-weather,
        #floating-weather-minimized {
            right: 4px;
            top: 40px;
            transform: scale(0.9);
        }
    }

    @media (max-width: 640px) {
        #floating-weather {
            min-width: 200px;
            max-width: 220px;
        }
        #floating-weather .grid-cols-2 {
            gap: 2px;
        }
        #floating-weather .text-2xl {
            font-size: 1.5rem;
        }
        #floating-weather .w-12 {
            width: 2.5rem;
            height: 2.5rem;
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

    // Toggle weather card
    toggleBtn?.addEventListener('click', function() {
        weatherCard.classList.add('hidden');
        minimizedWeather.classList.remove('hidden');
    });

    expandBtn?.addEventListener('click', function() {
        minimizedWeather.classList.add('hidden');
        weatherCard.classList.remove('hidden');
    });

    // Auto-refresh weather data every 10 minutes
    let weatherUpdateInterval;
    
    function updateWeatherData() {
        fetch('/api/weather/current', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateWeatherDisplay(data.weather, data.forecast);
            }
        })
        .catch(error => {
            console.error('Error updating weather:', error);
        });
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
        const timeString = now.toLocaleTimeString('en-PH', {
            timeZone: 'Asia/Manila',
            hour12: false,
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const lastUpdatedElement = document.getElementById('weather-last-updated');
        if (lastUpdatedElement) {
            lastUpdatedElement.textContent = timeString;
        }
    }

    // Start auto-refresh
    weatherUpdateInterval = setInterval(updateWeatherData, 600000); // 10 minutes

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
});
</script>
@endif