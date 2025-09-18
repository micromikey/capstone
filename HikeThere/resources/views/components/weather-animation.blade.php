@props(['weatherCondition' => 'clear', 'isDay' => true])

@php
    // Map weather conditions to animation classes
    $conditionMap = [
        'clear' => 'weather-sunny',
        'clear sky' => 'weather-sunny',
        'sunny' => 'weather-sunny', 
        'clouds' => 'weather-cloudy',
        'few clouds' => 'weather-cloudy',
        'scattered clouds' => 'weather-cloudy',
        'broken clouds' => 'weather-overcast',
        'overcast clouds' => 'weather-overcast',
        'overcast' => 'weather-overcast',
        'rain' => 'weather-rain',
        'light rain' => 'weather-rain',
        'moderate rain' => 'weather-rain',
        'heavy rain' => 'weather-rain',
        'drizzle' => 'weather-rain',
        'light intensity drizzle' => 'weather-rain',
        'thunderstorm' => 'weather-thunderstorm',
        'thunderstorm with light rain' => 'weather-thunderstorm',
        'thunderstorm with rain' => 'weather-thunderstorm',
        'thunderstorm with heavy rain' => 'weather-thunderstorm',
        'snow' => 'weather-overcast', // Use overcast for snow
        'light snow' => 'weather-overcast',
        'heavy snow' => 'weather-overcast',
        'mist' => 'weather-overcast',
        'fog' => 'weather-overcast',
        'haze' => 'weather-cloudy',
        'smoke' => 'weather-overcast',
        'dust' => 'weather-overcast',
        'sand' => 'weather-overcast',
        'ash' => 'weather-overcast',
        'squall' => 'weather-thunderstorm',
        'tornado' => 'weather-thunderstorm',
    ];

    $condition = strtolower(trim($weatherCondition));
    $weatherClass = $conditionMap[$condition] ?? 'weather-cloudy';
    $timeClass = $isDay ? 'day-time' : 'night-time';
    
    // Sunny is always day
    if ($weatherClass === 'weather-sunny') {
        $timeClass = 'day-time';
    }
@endphp

<div class="weather-anim {{ $weatherClass }} {{ $timeClass }}">
    @if($weatherClass === 'weather-sunny')
        {{-- Sunny weather --}}
        <div class="sun"></div>
    @else
        {{-- Day/Night celestial bodies for other weather --}}
        @if($isDay)
            <div class="day-sun"></div>
        @else
            <div class="night-moon">
                <div class="moon-crater1"></div>
                <div class="moon-crater2"></div>
            </div>
        @endif
    @endif

    {{-- Clouds for cloudy, overcast, and thunderstorm weather --}}
    @if(in_array($weatherClass, ['weather-cloudy', 'weather-overcast', 'weather-thunderstorm']))
        <div class="cloud cloud-1"></div>
        <div class="cloud cloud-2"></div>
        <div class="cloud cloud-3"></div>
        {{-- Smaller clouds under main clouds --}}
        <div class="cloud-small cloud-small-1"></div>
        <div class="cloud-small cloud-small-2"></div>
        <div class="cloud-small cloud-small-3"></div>
    @endif

    {{-- Lightning for thunderstorms --}}
    @if($weatherClass === 'weather-thunderstorm')
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
    @endif

    {{-- Rain drops for rain and thunderstorm weather --}}
    @if(in_array($weatherClass, ['weather-rain', 'weather-thunderstorm']))
        <div class="rain-container">
            <div class="rain-drop large"></div>
            <div class="rain-drop medium"></div>
            <div class="rain-drop small"></div>
            <div class="rain-drop large"></div>
            <div class="rain-drop medium"></div>
            <div class="rain-drop small"></div>
            <div class="rain-drop large"></div>
            <div class="rain-drop medium"></div>
            <div class="rain-drop small"></div>
            <div class="rain-drop large"></div>
            <div class="rain-drop medium"></div>
            <div class="rain-drop small"></div>
            <div class="rain-drop large"></div>
            <div class="rain-drop medium"></div>
            <div class="rain-drop small"></div>
            <div class="rain-drop large"></div>
            <div class="rain-drop medium"></div>
            <div class="rain-drop small"></div>
            <div class="rain-drop medium"></div>
            <div class="rain-drop small"></div>
        </div>
    @endif

    {{-- Hills background - Multiple layers for complete coverage --}}
    <div class="hill-bg-1"></div>
    <div class="hill-bg-2"></div>
    <div class="hill-bg-3"></div>
    <div class="hill-bg-4"></div>
    <div class="hill-fg-1"></div>
    <div class="hill-fg-2"></div>
    <div class="hill-fg-3"></div>
    <div class="hill-fg-4"></div>
    <div class="hill-fg-5"></div>
</div>