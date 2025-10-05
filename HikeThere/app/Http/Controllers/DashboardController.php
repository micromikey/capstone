<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is an organization and redirect them to org dashboard
        if (Auth::check() && Auth::user()->user_type === 'organization') {
            return redirect()->route('org.dashboard');
        }

        $lat = $request->session()->get('lat', null);
        $lon = $request->session()->get('lon', null);

        // If no coordinates found, fallback to Manila
        $queryParams = $lat && $lon
            ? ['lat' => $lat, 'lon' => $lon]
            : ['q' => 'Manila,PH'];

        $queryParams['appid'] = env('OPENWEATHER_API_KEY');
        $queryParams['units'] = 'metric';

        // Current Weather
        $current = Http::withOptions(['verify' => false])
            ->get('https://api.openweathermap.org/data/2.5/weather', $queryParams);

        $currentData = $current->json();

        $condition = strtolower($currentData['weather'][0]['main'] ?? '');
        $icon = $currentData['weather'][0]['icon'] ?? '01d';
        $isDay = $this->isDayTime($icon);

        // Different gradients for day and night
        $gradientMap = $isDay ? [
            'clear' => 'from-yellow-400 to-orange-500',
            'clouds' => 'from-gray-400 to-gray-600',
            'rain' => 'from-blue-400 to-blue-700',
            'thunderstorm' => 'from-indigo-700 to-gray-900',
            'snow' => 'from-blue-100 to-blue-300',
            'drizzle' => 'from-teal-300 to-teal-500',
            'mist' => 'from-gray-300 to-gray-500',
            'haze' => 'from-yellow-200 to-yellow-400',
            'fog' => 'from-gray-200 to-gray-400',
        ] : [
            // Night gradients - darker, cooler tones
            'clear' => 'from-indigo-900 to-blue-900',
            'clouds' => 'from-slate-700 to-slate-900',
            'rain' => 'from-slate-800 to-blue-900',
            'thunderstorm' => 'from-indigo-950 to-slate-950',
            'snow' => 'from-slate-600 to-slate-800',
            'drizzle' => 'from-slate-700 to-blue-900',
            'mist' => 'from-slate-600 to-slate-800',
            'haze' => 'from-slate-700 to-slate-900',
            'fog' => 'from-slate-600 to-slate-800',
        ];

        $gradient = $gradientMap[$condition] ?? ($isDay ? 'from-indigo-500 to-yellow-300' : 'from-indigo-900 to-purple-900'); // default

        $weather = [
            'temp' => $currentData['main']['temp'] ?? 'N/A',
            'feels_like' => $currentData['main']['feels_like'] ?? 'N/A',
            'description' => $currentData['weather'][0]['description'] ?? '',
            'icon' => $icon,
            'city' => $currentData['name'] ?? 'Unknown',
            'gradient' => $gradient,
            'condition' => $currentData['weather'][0]['main'] ?? 'Clear',
            'is_day' => $isDay,
            'humidity' => $currentData['main']['humidity'] ?? 'N/A',
            'uv_index' => 'N/A', // UV requires separate API call to onecall
            'wind_speed' => $currentData['wind']['speed'] ?? 'N/A',
        ];


        // Forecast
        $forecast = Http::withOptions(['verify' => false])
            ->get('https://api.openweathermap.org/data/2.5/forecast', $queryParams)
            ->json();

        $forecast = collect($forecast['list'] ?? [])->groupBy(function ($item) {
            return Carbon::parse($item['dt_txt'])->format('Y-m-d');
        })->map(function ($dayItems) {
            $midday = $dayItems->firstWhere('dt_txt', fn($dt) => str_contains($dt, '12:00:00')) ?? $dayItems->first();

            return [
                'date' => Carbon::parse($midday['dt_txt'])->format('l, M j'),
                'temp' => $midday['main']['temp'],
                'condition' => $midday['weather'][0]['main'],
                'icon' => $midday['weather'][0]['icon'],
            ];
        })->take(5);

        // Get user data for hikers
        $user = null;
        $latestAssessment = null;
        $latestItinerary = null;
        $followedTrails = collect();
        $followingCount = 0;
        $upcomingEvents = collect();

        if (Auth::check() && Auth::user()->user_type === 'hiker') {
            $user = Auth::user();
            $latestAssessment = $user->latestAssessmentResult;
            $latestItinerary = $user->latestItinerary;
            
            // Get trails from followed organizations
            $followedTrails = $user->followedOrganizationsTrails()
                ->with(['user', 'location', 'primaryImage'])
                ->limit(6)
                ->get();
            
            // Get count of organizations being followed
            $followingCount = $user->following->count();
            
            // Get upcoming events from followed organizations
            $followingIds = $user->following->pluck('id')->toArray();
            if (!empty($followingIds)) {
                $upcomingEvents = \App\Models\Event::whereIn('user_id', $followingIds)
                    ->where(function($q) {
                        $q->where('always_available', true)
                          ->orWhere('start_at', '>=', now())
                          ->orWhere(function($q2) {
                              $q2->whereNotNull('end_at')->where('end_at', '>', now());
                          });
                    })
                    ->with(['user'])
                    ->orderBy('start_at', 'asc')
                    ->limit(3)
                    ->get();
            }
        }

        return view('dashboard', [
            'weather' => $weather,
            'forecast' => $forecast,
            'user' => $user,
            'latestAssessment' => $latestAssessment,
            'latestItinerary' => $latestItinerary,
            'followedTrails' => $followedTrails,
            'followingCount' => $followingCount,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    /**
     * Determine if it's day time based on weather icon
     * OpenWeatherMap icons ending with 'd' are day, 'n' are night
     */
    private function isDayTime(string $icon): bool
    {
        return str_ends_with($icon, 'd');
    }
}
