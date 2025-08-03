{{-- Header --}}
<div class="relative p-6 lg:p-10 bg-gradient-to-r from-green-100 via-white to-white border-b border-gray-200 rounded-b-xl shadow-sm overflow-hidden min-h-[300px]">

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
        <div class="flex items-center space-x-4">
            <x-application-logo class="h-14 w-auto" />
            <div>
                <h1 class="text-3xl font-bold text-green-800 tracking-tight">
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
        </div>
    </div>
</div>

{{-- Weather and Forecast Section --}}
<div class="bg-white bg-opacity-90 px-6 lg:px-8 py-6 grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">

    {{-- Weather Overview Card (1/3 width on md+) --}}
    <div class="col-span-1 bg-gradient-to-r {{ $weather['gradient'] }} rounded-xl p-5 text-white shadow-md h-full flex flex-col justify-between">

        {{-- Date --}}
        <h2 class="text-xs font-medium opacity-90">{{ now()->format('F j, Y') }}</h2>

        {{-- Icon + Temp --}}
        <div class="flex items-center justify-between mt-3 flex-1">
            <img src="https://openweathermap.org/img/wn/{{ $weather['icon'] }}@2x.png"
                alt="{{ $weather['description'] }}"
                class="h-16 w-16 drop-shadow-sm">

            <div class="text-right">
                <h1 class="text-4xl font-bold leading-none">{{ $weather['temp'] }}°</h1>
                <p class="text-xs mt-1 capitalize leading-tight">{{ $weather['description'] }}</p>
            </div>
        </div>

        {{-- Location --}}
        <p class="text-xs font-medium tracking-wide text-white/90 truncate mt-3">
            📍 {{ $weather['city'] }}
        </p>
    </div>

    {{-- Forecast Calendar Card (2/3 width on md+) --}}
    <div class="col-span-1 md:col-span-2 bg-white rounded-xl shadow-md p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($forecast as $day)
            <div class="bg-blue-100 p-4 rounded-lg text-center shadow-sm hover:shadow-md transition">
                <div class="text-sm font-medium text-gray-800 mb-2">
                    <div>{{ explode(', ', $day['date'])[0] }}</div>
                    <div class="text-xs text-gray-600">{{ explode(', ', $day['date'])[1] }}</div>
                </div>
                <img src="https://openweathermap.org/img/wn/{{ $day['icon'] }}@2x.png"
                    alt="{{ $day['condition'] }}"
                    class="mx-auto h-12 w-12 mt-2">
                <div class="text-lg font-bold text-gray-900 mt-2">{{ $day['temp'] }}°C</div>
                <div class="capitalize text-gray-600 text-sm mt-1 truncate">{{ $day['condition'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

</div>


{{-- Trail Recommendations --}}
<div class="px-6 lg:px-8 py-10 bg-gray-100">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Trail Recommendations</h2>

    <div class="space-y-6">

        {{-- Trail Card --}}
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Mount_Pulag_Summit_2014.jpg/640px-Mount_Pulag_Summit_2014.jpg"
                alt="Ambangeg Trail"
                class="w-28 h-20 rounded object-cover">

            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800">Ambangeg Trail</h3>
                <p class="text-sm text-gray-500">Beginner-Friendly • Kabayan, Benguet, Philippines</p>
                <p class="text-sm text-gray-600">⭐ 4.7 (80 Reviews)</p>
            </div>

            <div class="text-right">
                <p class="text-sm text-gray-700 font-medium">19° 🌤️</p>
            </div>
        </div>

        {{-- Trail Card --}}
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
            <img src="https://www.choosephilippines.com/uploads/2020/02/18/Tinipak-River.jpg"
                alt="Tinipak Trail"
                class="w-28 h-20 rounded object-cover">

            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800">Tinipak Trail</h3>
                <p class="text-sm text-gray-500">Moderate • Tanay, Rizal, Philippines</p>
                <p class="text-sm text-gray-600">⭐ 4.6 (180 Reviews)</p>
            </div>

            <div class="text-right">
                <p class="text-sm text-gray-700 font-medium">21° ☀️</p>
            </div>
        </div>

        {{-- Trail Card --}}
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
            <img src="https://upload.wikimedia.org/wikipedia/commons/3/30/Kidapawan_Lake_Agco.jpg"
                alt="Kidapawan Trail"
                class="w-28 h-20 rounded object-cover">

            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800">Kidapawan Trail</h3>
                <p class="text-sm text-gray-500">Moderate to Hard • North Cotabato, Philippines</p>
                <p class="text-sm text-gray-600">⭐ 4.3 (310 Reviews)</p>
            </div>

            <div class="text-right">
                <p class="text-sm text-gray-700 font-medium">25° ☀️</p>
            </div>
        </div>

    </div>
</div>