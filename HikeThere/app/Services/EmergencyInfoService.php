<?php

namespace App\Services;

use App\Models\Trail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmergencyInfoService
{
    protected $googleMaps;

    public function __construct(GoogleMapsService $googleMaps)
    {
        $this->googleMaps = $googleMaps;
    }

    /**
     * Get emergency information for a trail
     * Returns hospitals, ranger stations, evacuation points
     */
    public function getEmergencyInfo($trail)
    {
        // If trail already has emergency info saved, return it
        if ($trail && is_array($trail) && !empty($trail['emergency_info'])) {
            return $trail['emergency_info'];
        }
        
        if ($trail && is_object($trail) && !empty($trail->emergency_info)) {
            return $trail->emergency_info;
        }

        // Generate emergency info based on trail location
        return $this->generateEmergencyInfo($trail);
    }

    /**
     * Generate emergency information using Google Places API
     */
    protected function generateEmergencyInfo($trail)
    {
        $trailLat = null;
        $trailLng = null;

        // Extract coordinates
        if (is_array($trail)) {
            $trailLat = $trail['latitude'] ?? null;
            $trailLng = $trail['longitude'] ?? null;
        } elseif (is_object($trail)) {
            $trailLat = $trail->latitude ?? null;
            $trailLng = $trail->longitude ?? null;
        }

        if (!$trailLat || !$trailLng) {
            return $this->getDefaultEmergencyInfo();
        }

        $emergencyInfo = [
            'hospitals' => $this->findNearbyHospitals($trailLat, $trailLng),
            'ranger_stations' => $this->findRangerStations($trailLat, $trailLng),
            'police_stations' => $this->findPoliceStations($trailLat, $trailLng),
            'evacuation_points' => $this->generateEvacuationPoints($trail),
            'emergency_numbers' => $this->getPhilippinesEmergencyNumbers(),
        ];

        return $emergencyInfo;
    }

    /**
     * Find nearby hospitals using Google Places API
     */
    protected function findNearbyHospitals($lat, $lng, $radius = 50000) // 50km
    {
        try {
            $apiKey = config('services.google_maps.key');
            $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
                'location' => "{$lat},{$lng}",
                'radius' => $radius,
                'type' => 'hospital',
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $hospitals = [];

                foreach (array_slice($data['results'] ?? [], 0, 3) as $place) {
                    $hospitals[] = [
                        'name' => $place['name'],
                        'address' => $place['vicinity'] ?? 'Address not available',
                        'lat' => $place['geometry']['location']['lat'],
                        'lng' => $place['geometry']['location']['lng'],
                        'distance' => $this->calculateDistance($lat, $lng, 
                            $place['geometry']['location']['lat'], 
                            $place['geometry']['location']['lng']),
                    ];
                }

                return $hospitals;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch hospitals: ' . $e->getMessage());
        }

        return $this->getDefaultHospitals();
    }

    /**
     * Find ranger stations or park offices
     */
    protected function findRangerStations($lat, $lng, $radius = 30000) // 30km
    {
        // Try to find park offices, tourist information centers
        try {
            $apiKey = config('services.google_maps.key');
            $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
                'location' => "{$lat},{$lng}",
                'radius' => $radius,
                'keyword' => 'ranger station park office tourism',
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $stations = [];

                foreach (array_slice($data['results'] ?? [], 0, 2) as $place) {
                    $stations[] = [
                        'name' => $place['name'],
                        'address' => $place['vicinity'] ?? 'Address not available',
                        'lat' => $place['geometry']['location']['lat'],
                        'lng' => $place['geometry']['location']['lng'],
                    ];
                }

                if (!empty($stations)) {
                    return $stations;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch ranger stations: ' . $e->getMessage());
        }

        return [
            [
                'name' => 'Trail Management Office',
                'address' => 'Contact your tour organizer for location',
                'lat' => $lat,
                'lng' => $lng,
            ]
        ];
    }

    /**
     * Find nearby police stations
     */
    protected function findPoliceStations($lat, $lng, $radius = 30000) // 30km
    {
        try {
            $apiKey = config('services.google_maps.key');
            $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
                'location' => "{$lat},{$lng}",
                'radius' => $radius,
                'type' => 'police',
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['results'])) {
                    $place = $data['results'][0];
                    return [[
                        'name' => $place['name'],
                        'address' => $place['vicinity'] ?? 'Address not available',
                        'lat' => $place['geometry']['location']['lat'],
                        'lng' => $place['geometry']['location']['lng'],
                    ]];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch police stations: ' . $e->getMessage());
        }

        return [[
            'name' => 'Local Police Station',
            'address' => 'Contact local authorities',
        ]];
    }

    /**
     * Generate evacuation points based on trail data
     */
    protected function generateEvacuationPoints($trail)
    {
        $points = [
            ['name' => 'Trailhead / Base Camp', 'description' => 'Primary evacuation point'],
        ];

        // Add camp points if multi-day
        if (is_array($trail) && !empty($trail['package']['duration'])) {
            $duration = $trail['package']['duration'];
            if (str_contains(strtolower($duration), '2') || str_contains(strtolower($duration), 'two')) {
                $points[] = ['name' => 'Camp 1', 'description' => 'Mid-trail evacuation point'];
            }
            if (str_contains(strtolower($duration), '3') || str_contains(strtolower($duration), 'three')) {
                $points[] = ['name' => 'Camp 1', 'description' => 'First evacuation point'];
                $points[] = ['name' => 'Camp 2', 'description' => 'Second evacuation point'];
            }
        }

        $points[] = ['name' => 'Summit Area', 'description' => 'Emergency shelter if descent not possible'];

        return $points;
    }

    /**
     * Get Philippines emergency numbers
     */
    protected function getPhilippinesEmergencyNumbers()
    {
        return [
            ['service' => 'National Emergency Hotline', 'number' => '911'],
            ['service' => 'Philippine Red Cross', 'number' => '143'],
            ['service' => 'NDRRMC Hotline', 'number' => '(02) 8911-1406'],
            ['service' => 'Coast Guard', 'number' => '(02) 8527-8481'],
        ];
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 1) . ' km';
    }

    /**
     * Default emergency info when API fails
     */
    protected function getDefaultEmergencyInfo()
    {
        return [
            'hospitals' => $this->getDefaultHospitals(),
            'ranger_stations' => [[
                'name' => 'Trail Management Office',
                'address' => 'Contact your tour organizer',
            ]],
            'police_stations' => [[
                'name' => 'Local Police Station',
                'address' => 'Dial 911 for emergencies',
            ]],
            'evacuation_points' => [
                ['name' => 'Trailhead / Base Camp', 'description' => 'Primary evacuation point'],
                ['name' => 'Summit Area', 'description' => 'Emergency shelter'],
            ],
            'emergency_numbers' => $this->getPhilippinesEmergencyNumbers(),
        ];
    }

    protected function getDefaultHospitals()
    {
        return [[
            'name' => 'Nearest Medical Facility',
            'address' => 'Contact your tour organizer for location',
            'distance' => 'Unknown',
        ]];
    }
}
