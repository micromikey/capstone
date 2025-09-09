<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    /**
     * Search locations by name
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        $locations = Location::where('name', 'like', "%{$query}%")
            ->orWhere('province', 'like', "%{$query}%")
            ->orWhere('region', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'province', 'region']);

        return response()->json(['data' => $locations]);
    }

    /**
     * Get all locations for dropdown
     */
    public function index(): JsonResponse
    {
        $locations = Location::orderBy('name')->get(['id', 'name', 'slug', 'province', 'region']);

        return response()->json($locations);
    }

    /**
     * Get location details by ID
     */
    public function show($id): JsonResponse
    {
        $location = Location::find($id);

        if (! $location) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        return response()->json(['data' => $location]);
    }

    /**
     * Handle Google Places location creation or find existing
     */
    public function handleGooglePlacesLocation(Request $request): JsonResponse
    {
        $request->validate([
            'place_id' => 'required|string',
            'formatted_address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'name' => 'nullable|string',
        ]);

        try {
            \Log::info('Processing Google Places location', [
                'place_id' => $request->place_id,
                'formatted_address' => $request->formatted_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // First, try to find an existing location by coordinates (within a small radius)
            $existingLocation = Location::whereRaw('
                (latitude BETWEEN ? AND ?) AND 
                (longitude BETWEEN ? AND ?)
            ', [
                $request->latitude - 0.01, // ~1km radius
                $request->latitude + 0.01,
                $request->longitude - 0.01,
                $request->longitude + 0.01,
            ])->first();

            if ($existingLocation) {
                \Log::info('Found existing location', ['location_id' => $existingLocation->id]);

                return response()->json([
                    'success' => true,
                    'location' => $existingLocation,
                    'action' => 'found_existing',
                ]);
            }

            // Parse the formatted address to extract location details
            $addressParts = $this->parseGoogleAddress($request->formatted_address);

            // Create a new location with unique slug
            $baseSlug = Str::slug($request->name ?: $addressParts['name']);
            $slug = $baseSlug;
            $counter = 1;

            // Ensure slug is unique
            while (Location::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$counter;
                $counter++;
            }

            $location = Location::create([
                'name' => $request->name ?: $addressParts['name'],
                'slug' => $slug,
                'province' => $addressParts['province'],
                'region' => $addressParts['region'],
                'country' => 'Philippines',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'description' => 'Location added via Google Places',
            ]);

            \Log::info('Created new location', [
                'location_id' => $location->id,
                'name' => $location->name,
                'slug' => $location->slug,
            ]);

            return response()->json([
                'success' => true,
                'location' => $location,
                'action' => 'created_new',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to process location: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Parse Google Places formatted address to extract location details
     */
    private function parseGoogleAddress(string $formattedAddress): array
    {
        // Default values
        $result = [
            'name' => 'Unknown Location',
            'province' => 'Unknown Province',
            'region' => 'Unknown Region',
        ];

        // Split the address by commas
        $parts = array_map('trim', explode(',', $formattedAddress));

        if (count($parts) >= 1) {
            $result['name'] = $parts[0];
        }

        // Look for province and region in the address parts
        foreach ($parts as $part) {
            $part = trim($part);

            // Common Philippine provinces
            $provinces = [
                'Benguet', 'Pampanga', 'Zambales', 'Batangas', 'Rizal', 'Cavite',
                'Laguna', 'Quezon', 'Bulacan', 'Nueva Ecija', 'Tarlac', 'Pangasinan',
                'La Union', 'Ilocos Sur', 'Ilocos Norte', 'Abra', 'Kalinga', 'Apayao',
                'Mountain Province', 'Ifugao', 'Baguio', 'Metro Manila', 'Manila',
            ];

            if (in_array($part, $provinces)) {
                $result['province'] = $part;
                break;
            }
        }

        // Look for regions
        $regions = [
            'Cordillera Administrative Region', 'Central Luzon', 'Calabarzon',
            'Ilocos Region', 'Cagayan Valley', 'Bicol Region', 'Western Visayas',
            'Central Visayas', 'Eastern Visayas', 'Zamboanga Peninsula',
            'Northern Mindanao', 'Davao Region', 'Soccsksargen', 'Caraga',
            'Bangsamoro Autonomous Region in Muslim Mindanao', 'National Capital Region',
        ];

        foreach ($parts as $part) {
            $part = trim($part);
            if (in_array($part, $regions)) {
                $result['region'] = $part;
                break;
            }
        }

        return $result;
    }
}
