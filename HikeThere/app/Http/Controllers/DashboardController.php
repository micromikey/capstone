<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is an organization and redirect them to org dashboard
        if (auth()->check() && auth()->user()->user_type === 'organization') {
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

        $gradientMap = [
            'clear' => 'from-yellow-400 to-orange-500',
            'clouds' => 'from-gray-400 to-gray-600',
            'rain' => 'from-blue-400 to-blue-700',
            'thunderstorm' => 'from-indigo-700 to-gray-900',
            'snow' => 'from-blue-100 to-blue-300',
            'drizzle' => 'from-teal-300 to-teal-500',
            'mist' => 'from-gray-300 to-gray-500',
            'haze' => 'from-yellow-200 to-yellow-400',
            'fog' => 'from-gray-200 to-gray-400',
        ];

        $gradient = $gradientMap[$condition] ?? 'from-indigo-500 to-yellow-300'; // default

        $weather = [
            'temp' => $currentData['main']['temp'] ?? 'N/A',
            'description' => $currentData['weather'][0]['description'] ?? '',
            'icon' => $currentData['weather'][0]['icon'] ?? null,
            'city' => $currentData['name'] ?? 'Unknown',
            'gradient' => $gradient,
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
        
        if (auth()->check() && auth()->user()->user_type === 'hiker') {
            $user = auth()->user();
            $latestAssessment = $user->latestAssessmentResult;
            $latestItinerary = $user->latestItinerary;
            
            // Get trails from followed organizations
            $followedTrails = $user->followedOrganizationsTrails()
                ->with(['user', 'location', 'primaryImage'])
                ->limit(6)
                ->get();
            
            // Get count of organizations being followed
            $followingCount = $user->following()->count();
        }

        return view('dashboard', [
            'weather' => $weather,
            'forecast' => $forecast,
            'user' => $user,
            'latestAssessment' => $latestAssessment,
            'latestItinerary' => $latestItinerary,
            'followedTrails' => $followedTrails,
            'followingCount' => $followingCount,
        ]);
    }
}
