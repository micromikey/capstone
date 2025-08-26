<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDirectionsService
{
    protected $apiKey;

    protected $baseUrl = 'https://maps.googleapis.com/maps/api/directions/json';

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
    }

    /**
     * Get directions between two points with waypoints
     */
    public function getDirections($origin, $destination, $waypoints = [], $mode = 'driving')
    {
        try {
            $params = [
                'origin' => $origin,
                'destination' => $destination,
                'mode' => $mode,
                'key' => $this->apiKey,
                'units' => 'metric',
                'language' => 'en',
                'alternatives' => 'false',
            ];

            if (! empty($waypoints)) {
                $params['waypoints'] = implode('|', $waypoints);
            }

            $response = Http::get($this->baseUrl, $params);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'OK') {
                    return $this->parseDirectionsResponse($data);
                } else {
                    Log::error('Google Directions API error', [
                        'status' => $data['status'],
                        'error_message' => $data['error_message'] ?? 'Unknown error',
                    ]);

                    return null;
                }
            }

            Log::error('Google Directions API request failed', [
                'status_code' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Google Directions API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Parse the Google Directions API response
     */
    protected function parseDirectionsResponse($data)
    {
        $route = $data['routes'][0] ?? null;
        if (! $route) {
            return null;
        }

        $legs = $route['legs'] ?? [];
        $overviewPolyline = $route['overview_polyline'] ?? null;

        $parsedRoute = [
            'summary' => $route['summary'] ?? '',
            'total_distance' => 0,
            'total_duration' => 0,
            'legs' => [],
            'route_coordinates' => [],
            'transport_details' => [],
        ];

        // Parse each leg of the journey
        foreach ($legs as $leg) {
            $legData = [
                'start_address' => $leg['start_address'] ?? '',
                'end_address' => $leg['end_address'] ?? '',
                'distance' => $leg['distance']['text'] ?? '',
                'distance_meters' => $leg['distance']['value'] ?? 0,
                'duration' => $leg['duration']['text'] ?? '',
                'duration_seconds' => $leg['duration']['value'] ?? 0,
                'steps' => [],
            ];

            // Parse steps within each leg
            foreach ($leg['steps'] ?? [] as $step) {
                $stepData = [
                    'instruction' => $step['html_instructions'] ?? '',
                    'distance' => $step['distance']['text'] ?? '',
                    'duration' => $step['duration']['text'] ?? '',
                    'travel_mode' => $step['travel_mode'] ?? '',
                    'start_location' => [
                        'lat' => $step['start_location']['lat'] ?? 0,
                        'lng' => $step['start_location']['lng'] ?? 0,
                    ],
                    'end_location' => [
                        'lat' => $step['end_location']['lat'] ?? 0,
                        'lng' => $step['end_location']['lng'] ?? 0,
                    ],
                    'polyline' => $step['polyline']['points'] ?? '',
                    'transit_details' => $step['transit_details'] ?? null,
                ];

                $legData['steps'][] = $stepData;
            }

            $parsedRoute['legs'][] = $legData;
            $parsedRoute['total_distance'] += $legData['distance_meters'];
            $parsedRoute['total_duration'] += $legData['duration_seconds'];
        }

        // Decode overview polyline for route visualization
        if ($overviewPolyline) {
            $parsedRoute['route_coordinates'] = $this->decodePolyline($overviewPolyline['points']);
        }

        // Convert total values to human-readable format
        $parsedRoute['total_distance_km'] = round($parsedRoute['total_distance'] / 1000, 1);
        $parsedRoute['total_duration_hours'] = round($parsedRoute['total_duration'] / 3600, 1);

        return $parsedRoute;
    }

    /**
     * Decode Google's polyline format to coordinates
     */
    protected function decodePolyline($encoded)
    {
        $poly = [];
        $index = 0;
        $len = strlen($encoded);
        $lat = 0;
        $lng = 0;

        while ($index < $len) {

            $shift = 0;
            $result = 0;
            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1F) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lat += $dlat;

            $shift = 0;
            $result = 0;
            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1F) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;

            $poly[] = [
                'lat' => $lat / 1E5,
                'lng' => $lng / 1E5,
            ];
        }

        return $poly;
    }

    /**
     * Generate detailed transport information for commute mode
     */
    public function generateCommuteDetails($directions)
    {
        if (! $directions) {
            return [];
        }

        $commuteDetails = [];

        foreach ($directions['legs'] as $legIndex => $leg) {
            foreach ($leg['steps'] as $stepIndex => $step) {
                if ($step['travel_mode'] === 'TRANSIT') {
                    $transit = $step['transit_details'] ?? [];

                    $commuteDetails[] = [
                        'leg' => $legIndex + 1,
                        'step' => $stepIndex + 1,
                        'mode' => 'Public Transport',
                        'line' => $transit['line']['name'] ?? 'Unknown',
                        'vehicle' => $transit['line']['vehicle']['name'] ?? 'Unknown',
                        'departure_stop' => $transit['departure_stop']['name'] ?? 'Unknown',
                        'arrival_stop' => $transit['line']['vehicle']['name'] ?? 'Unknown',
                        'departure_time' => $transit['departure_time']['text'] ?? '',
                        'arrival_time' => $transit['arrival_time']['text'] ?? '',
                        'duration' => $step['duration'],
                        'distance' => $step['distance'],
                        'instruction' => $step['instruction'],
                    ];
                } elseif ($step['travel_mode'] === 'WALKING') {
                    $commuteDetails[] = [
                        'leg' => $legIndex + 1,
                        'step' => $stepIndex + 1,
                        'mode' => 'Walking',
                        'line' => null,
                        'vehicle' => null,
                        'departure_stop' => null,
                        'arrival_stop' => null,
                        'departure_time' => null,
                        'arrival_time' => null,
                        'duration' => $step['duration'],
                        'distance' => $step['distance'],
                        'instruction' => $step['instruction'],
                    ];
                }
            }
        }

        return $commuteDetails;
    }
}
