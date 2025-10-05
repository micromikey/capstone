<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class LocationWeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $lat = $request->lat;
        $lon = $request->lon;

        $weatherApiKey = config('services.openweather.api_key');

        $response = Http::withOptions(['verify' => false])->get("https://api.openweathermap.org/data/2.5/weather", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $weatherApiKey,
            'units' => 'metric',
        ]);

        if ($response->successful()) {
            $data = $response->json();

            // Save to session so DashboardController can use it
            session([
                'weather_dynamic' => [
                    'temp' => $data['main']['temp'],
                    'description' => $data['weather'][0]['description'],
                    'icon' => $data['weather'][0]['icon'],
                    'condition' => $data['weather'][0]['main'],
                    'city' => $data['name'],
                ]
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
