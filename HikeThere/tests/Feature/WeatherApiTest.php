<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_weather_api_requires_lat_lng_parameters(): void
    {
        $response = $this->getJson('/api/weather');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lat', 'lng']);
    }

    public function test_weather_api_validates_lat_lng_range(): void
    {
        $response = $this->getJson('/api/weather?lat=100&lng=200');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lat', 'lng']);
    }

    public function test_weather_api_returns_error_without_api_key(): void
    {
        // Clear the OpenWeather API key
        config(['services.openweather.api_key' => null]);

        $response = $this->getJson('/api/weather?lat=14.5995&lng=120.9842');

        $response->assertStatus(500)
            ->assertJson(['error' => 'Weather data unavailable']);
    }

    public function test_weather_api_accepts_valid_coordinates(): void
    {
        // Mock the OpenWeather API response
        $this->mock(\Illuminate\Support\Facades\Http::class, function ($mock) {
            $mock->shouldReceive('get')
                ->once()
                ->andReturn(new \Illuminate\Http\Client\Response(
                    response: json_encode([
                        'weather' => [
                            ['main' => 'Clear', 'description' => 'clear sky'],
                        ],
                        'main' => [
                            'temp' => 25,
                            'humidity' => 65,
                            'pressure' => 1013,
                        ],
                        'wind' => ['speed' => 3.5],
                        'visibility' => 10000,
                    ]),
                    status: 200
                ));
        });

        $response = $this->getJson('/api/weather?lat=14.5995&lng=120.9842');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'condition',
                'temperature',
                'humidity',
                'windSpeed',
                'uvIndex',
                'timestamp',
                'location',
                'description',
                'pressure',
                'visibility',
            ]);
    }
}
