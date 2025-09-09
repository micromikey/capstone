<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OSMTrailSegmentService;
use App\Models\TrailSegment;
use App\Models\TrailIntersection;
use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TrailSegmentController extends Controller
{
    private OSMTrailSegmentService $osmService;

    public function __construct(OSMTrailSegmentService $osmService)
    {
        $this->osmService = $osmService;
    }

    /**
     * Generate trail segments for a region using AllTrails methodology
     */
    public function generateSegments(Request $request): JsonResponse
    {
        $request->validate([
            'min_lat' => 'required|numeric|between:-90,90',
            'max_lat' => 'required|numeric|between:-90,90', 
            'min_lng' => 'required|numeric|between:-180,180',
            'max_lng' => 'required|numeric|between:-180,180'
        ]);

        try {
            $result = $this->osmService->generateTrailSegments(
                $request->min_lat,
                $request->min_lng,
                $request->max_lat,
                $request->max_lng
            );

            // Store segments in database
            $this->storeSegments($result['segments'], $result['intersections']);

            return response()->json([
                'success' => true,
                'message' => 'Trail segments generated successfully',
                'data' => [
                    'statistics' => $result['statistics'],
                    'bounds' => [
                        'min_lat' => $request->min_lat,
                        'max_lat' => $request->max_lat,
                        'min_lng' => $request->min_lng,
                        'max_lng' => $request->max_lng
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Trail segment generation failed', [
                'error' => $e->getMessage(),
                'bounds' => $request->only(['min_lat', 'max_lat', 'min_lng', 'max_lng'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate trail segments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find trail segments for a specific trail
     */
    public function findTrailSegments(Request $request): JsonResponse
    {
        $request->validate([
            'trail_name' => 'required|string',
            'min_lat' => 'required|numeric',
            'max_lat' => 'required|numeric',
            'min_lng' => 'required|numeric',
            'max_lng' => 'required|numeric'
        ]);

        try {
            $result = $this->osmService->findTrailByName(
                $request->trail_name,
                [
                    'minLat' => $request->min_lat,
                    'minLng' => $request->min_lng,
                    'maxLat' => $request->max_lat,
                    'maxLng' => $request->max_lng
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Trail segments found',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to find trail segments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stored trail segments within bounds
     */
    public function getStoredSegments(Request $request): JsonResponse
    {
        $request->validate([
            'min_lat' => 'required|numeric',
            'max_lat' => 'required|numeric',
            'min_lng' => 'required|numeric',
            'max_lng' => 'required|numeric',
            'highway_type' => 'sometimes|string',
            'max_distance' => 'sometimes|numeric|min:0',
            'public_only' => 'sometimes|boolean'
        ]);

        try {
            $query = TrailSegment::withinBounds(
                $request->min_lat,
                $request->max_lat,
                $request->min_lng,
                $request->max_lng
            );

            // Apply filters
            if ($request->highway_type) {
                $query->byHighwayType($request->highway_type);
            }

            if ($request->max_distance) {
                $query->where('distance_total', '<=', $request->max_distance);
            }

            if ($request->boolean('public_only', true)) {
                $query->publicAccess();
            }

            // Default to hiking trails only
            if (!$request->highway_type) {
                $query->hikingTrails();
            }

            $segments = $query->with(['startIntersection', 'endIntersection'])
                            ->orderBy('distance_total', 'desc')
                            ->limit(1000) // Prevent massive responses
                            ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'segments' => $segments,
                    'count' => $segments->count(),
                    'bounds' => $request->only(['min_lat', 'max_lat', 'min_lng', 'max_lng'])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve trail segments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build an optimized trail route using segments
     */
    public function buildOptimizedRoute(Request $request): JsonResponse
    {
        $request->validate([
            'waypoints' => 'required|array|min:2',
            'waypoints.*.lat' => 'required|numeric',
            'waypoints.*.lng' => 'required|numeric',
            'difficulty_preference' => 'sometimes|in:beginner,intermediate,advanced',
            'surface_preference' => 'sometimes|string'
        ]);

        try {
            $waypoints = $request->waypoints;
            $route = $this->buildRoute($waypoints, $request->only(['difficulty_preference', 'surface_preference']));

            return response()->json([
                'success' => true,
                'data' => $route
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to build optimized route',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trail intersections near a point
     */
    public function getNearbyIntersections(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius_km' => 'sometimes|numeric|min:0.01|max:10'
        ]);

        try {
            $intersections = TrailIntersection::near(
                $request->lat,
                $request->lng,
                $request->radius_km ?? 1.0
            )->with(['startingSegments', 'endingSegments'])
             ->orderBy('connection_count', 'desc')
             ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'intersections' => $intersections,
                    'count' => $intersections->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to find nearby intersections',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store segments and intersections in database
     */
    private function storeSegments(array $segments, array $intersections): void
    {
        DB::transaction(function () use ($segments, $intersections) {
            // Store intersections first
            foreach ($intersections as $wayId => $wayIntersections) {
                foreach ($wayIntersections as $intersection) {
                    TrailIntersection::updateOrCreate(
                        [
                            'latitude' => $intersection['lat'],
                            'longitude' => $intersection['lng']
                        ],
                        [
                            'connected_ways' => [$intersection['way_a'], $intersection['way_b']],
                            'connected_segments' => [],
                            'connection_count' => 2,
                            'intersection_type' => 'waypoint'
                        ]
                    );
                }
            }

            // Store segments
            foreach ($segments as $segment) {
                $bounds = $segment['bounding_box'];
                
                TrailSegment::updateOrCreate(
                    ['segment_id' => $segment['id']],
                    [
                        'original_way_id' => $segment['original_way_id'],
                        'points_data' => $segment['points_data'],
                        'intersection_start_id' => $segment['intersection_start_id'],
                        'intersection_end_id' => $segment['intersection_end_id'],
                        'distance_total' => $segment['distance_total'],
                        'bounding_box' => $segment['bounding_box'],
                        'private_access' => $segment['private_access'],
                        'bicycle_accessible' => $segment['bicycle_accessible'],
                        'highway_type' => $segment['highway_type'],
                        'name' => $segment['name'],
                        'sac_scale' => $segment['sac_scale'],
                        'trail_visibility' => $segment['trail_visibility'],
                        'surface' => $segment['surface'],
                        'width' => $segment['width'],
                        'incline' => $segment['incline'],
                        'min_lat' => $bounds['min_lat'],
                        'max_lat' => $bounds['max_lat'],
                        'min_lng' => $bounds['min_lng'],
                        'max_lng' => $bounds['max_lng'],
                        'point_count' => count($segment['points_data'])
                    ]
                );
            }
        });
    }

    /**
     * Build route using stored trail segments
     */
    private function buildRoute(array $waypoints, array $preferences): array
    {
        $routeSegments = [];
        $totalDistance = 0;
        $totalElevation = 0;

        // Simple implementation - connect nearest segments between waypoints
        for ($i = 0; $i < count($waypoints) - 1; $i++) {
            $start = $waypoints[$i];
            $end = $waypoints[$i + 1];

            // Find segments connecting these points
            $segments = $this->findConnectingSegments($start, $end, $preferences);
            
            foreach ($segments as $segment) {
                $routeSegments[] = $segment;
                $totalDistance += $segment->distance_total;
                $totalElevation += $segment->estimated_elevation_gain ?? 0;
            }
        }

        return [
            'segments' => $routeSegments,
            'total_distance_km' => round($totalDistance, 2),
            'estimated_elevation_gain_m' => $totalElevation,
            'segment_count' => count($routeSegments),
            'waypoints' => $waypoints
        ];
    }

    /**
     * Find segments connecting two points
     */
    private function findConnectingSegments(array $start, array $end, array $preferences): \Illuminate\Database\Eloquent\Collection
    {
        // Create bounding box around both points
        $buffer = 0.01; // ~1km
        $minLat = min($start['lat'], $end['lat']) - $buffer;
        $maxLat = max($start['lat'], $end['lat']) + $buffer;
        $minLng = min($start['lng'], $end['lng']) - $buffer;
        $maxLng = max($start['lng'], $end['lng']) + $buffer;

        $query = TrailSegment::withinBounds($minLat, $maxLat, $minLng, $maxLng)
                            ->hikingTrails()
                            ->publicAccess();

        // Apply preferences
        if (isset($preferences['difficulty_preference'])) {
            // Filter by SAC scale or other difficulty indicators
            $sacScales = match($preferences['difficulty_preference']) {
                'beginner' => ['hiking', 'T1'],
                'intermediate' => ['mountain_hiking', 'T2', 'T3'],
                'advanced' => ['alpine_hiking', 'T4', 'T5', 'T6'],
                default => null
            };
            
            if ($sacScales) {
                $query->whereIn('sac_scale', $sacScales);
            }
        }

        return $query->orderBy('distance_total')
                    ->limit(50)
                    ->get();
    }
}
