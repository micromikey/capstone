<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Animation Test - Dashboard Integration</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-purple-50 to-blue-50 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Weather Animation Integration Test</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Sunny Day -->
            <div class="weather-card-container animated bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-5 text-white shadow-md h-64 flex flex-col justify-between relative overflow-hidden">
                <x-weather-animation weather-condition="sunny" :is-day="true" />
                <div class="weather-card-content relative z-10">
                    <h2 class="text-xs font-medium opacity-90">{{ now()->format('F j, Y') }}</h2>
                    <div class="flex items-center justify-between mt-3 flex-1">
                        <div class="text-right ml-auto">
                            <h1 class="text-4xl font-bold leading-none">28°</h1>
                            <p class="text-xs mt-1 capitalize leading-tight">sunny</p>
                        </div>
                    </div>
                    <p class="text-xs font-medium tracking-wide text-white/90 truncate mt-3">
                        📍 Test Location
                    </p>
                </div>
            </div>

            <!-- Cloudy Day -->
            <div class="weather-card-container animated bg-gradient-to-r from-gray-400 to-gray-600 rounded-xl p-5 text-white shadow-md h-64 flex flex-col justify-between relative overflow-hidden">
                <x-weather-animation weather-condition="clouds" :is-day="true" />
                <div class="weather-card-content relative z-10">
                    <h2 class="text-xs font-medium opacity-90">{{ now()->format('F j, Y') }}</h2>
                    <div class="flex items-center justify-between mt-3 flex-1">
                        <div class="text-right ml-auto">
                            <h1 class="text-4xl font-bold leading-none">22°</h1>
                            <p class="text-xs mt-1 capitalize leading-tight">cloudy</p>
                        </div>
                    </div>
                    <p class="text-xs font-medium tracking-wide text-white/90 truncate mt-3">
                        📍 Test Location
                    </p>
                </div>
            </div>

            <!-- Cloudy Night -->
            <div class="weather-card-container animated bg-gradient-to-r from-indigo-600 to-purple-800 rounded-xl p-5 text-white shadow-md h-64 flex flex-col justify-between relative overflow-hidden">
                <x-weather-animation weather-condition="clouds" :is-day="false" />
                <div class="weather-card-content relative z-10">
                    <h2 class="text-xs font-medium opacity-90">{{ now()->format('F j, Y') }}</h2>
                    <div class="flex items-center justify-between mt-3 flex-1">
                        <div class="text-right ml-auto">
                            <h1 class="text-4xl font-bold leading-none">18°</h1>
                            <p class="text-xs mt-1 capitalize leading-tight">cloudy night</p>
                        </div>
                    </div>
                    <p class="text-xs font-medium tracking-wide text-white/90 truncate mt-3">
                        📍 Test Location
                    </p>
                </div>
            </div>

            <!-- Rainy Day -->
            <div class="weather-card-container animated bg-gradient-to-r from-blue-400 to-blue-700 rounded-xl p-5 text-white shadow-md h-64 flex flex-col justify-between relative overflow-hidden">
                <x-weather-animation weather-condition="rain" :is-day="true" />
                <div class="weather-card-content relative z-10">
                    <h2 class="text-xs font-medium opacity-90">{{ now()->format('F j, Y') }}</h2>
                    <div class="flex items-center justify-between mt-3 flex-1">
                        <div class="text-right ml-auto">
                            <h1 class="text-4xl font-bold leading-none">19°</h1>
                            <p class="text-xs mt-1 capitalize leading-tight">light rain</p>
                        </div>
                    </div>
                    <p class="text-xs font-medium tracking-wide text-white/90 truncate mt-3">
                        📍 Test Location
                    </p>
                </div>
            </div>

            <!-- Thunderstorm Day -->
            <div class="weather-card-container animated bg-gradient-to-r from-indigo-700 to-gray-900 rounded-xl p-5 text-white shadow-md h-64 flex flex-col justify-between relative overflow-hidden">
                <x-weather-animation weather-condition="thunderstorm" :is-day="true" />
                <div class="weather-card-content relative z-10">
                    <h2 class="text-xs font-medium opacity-90">{{ now()->format('F j, Y') }}</h2>
                    <div class="flex items-center justify-between mt-3 flex-1">
                        <div class="text-right ml-auto">
                            <h1 class="text-4xl font-bold leading-none">16°</h1>
                            <p class="text-xs mt-1 capitalize leading-tight">thunderstorm</p>
                        </div>
                    </div>
                    <p class="text-xs font-medium tracking-wide text-white/90 truncate mt-3">
                        📍 Test Location
                    </p>
                </div>
            </div>

            <!-- Overcast Night -->
            <div class="weather-card-container animated bg-gradient-to-r from-gray-500 to-gray-700 rounded-xl p-5 text-white shadow-md h-64 flex flex-col justify-between relative overflow-hidden">
                <x-weather-animation weather-condition="overcast clouds" :is-day="false" />
                <div class="weather-card-content relative z-10">
                    <h2 class="text-xs font-medium opacity-90">{{ now()->format('F j, Y') }}</h2>
                    <div class="flex items-center justify-between mt-3 flex-1">
                        <div class="text-right ml-auto">
                            <h1 class="text-4xl font-bold leading-none">15°</h1>
                            <p class="text-xs mt-1 capitalize leading-tight">overcast night</p>
                        </div>
                    </div>
                    <p class="text-xs font-medium tracking-wide text-white/90 truncate mt-3">
                        📍 Test Location
                    </p>
                </div>
            </div>

        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                ← Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>