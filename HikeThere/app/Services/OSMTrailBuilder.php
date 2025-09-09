<?php

namespace App\Services;

use App\Models\Trail;
use App\Models\TrailSegment;
use App\Models\TrailIntersection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * Enhanced Trail Builder using AllTrails OSM Segment methodology
 * 
 * This service creates accurate trail routes by connecting OSM trail segments
 * instead of relying on road-based routing APIs.
 */
class OSMTrailBuilder
{
    /**
     * Build a trail route using OSM segments
     */
    public function buildTrailFromSegments(
        array $startPoint,
        array $endPoint,
        string $trailName,
        array $options = []
    ): array {
        // 1. Find nearby trail segments
        $candidateSegments = $this->findCandidateSegments($startPoint, $endPoint, $options);
        
        // 2. Build path using A* or similar pathfinding
        $pathSegments = $this->findOptimalPath($candidateSegments, $startPoint, $endPoint);
        
        // 3. Generate detailed trail data
        return $this->generateTrailData($pathSegments, $trailName, $options);
    }
    
    /**
     * Enhance existing trail with OSM segment data
     */
    public function enhanceTrailWithSegments(Trail $trail): Trail
    {
        if (!$trail->coordinates || count($trail->coordinates) < 2) {
            return $trail;
        }
        
        // Find segments that match the trail coordinates
        $matchingSegments = $this->findMatchingSegments($trail->coordinates);
        
        if ($matchingSegments->count() > 0) {
            // Link trail to segments
            $this->linkTrailToSegments($trail, $matchingSegments);
            
            // Update trail metrics from segments
            $this->updateTrailMetrics($trail, $matchingSegments);
        }
        
        return $trail->fresh();
    }
    
    /**
     * Find trail segments near start and end points
     */
    private function findCandidateSegments(array $startPoint, array $endPoint, array $options): Collection
    {
        // Create bounding box around both points with buffer
        $buffer = $options['search_radius_km'] ?? 5.0;
        $bufferDegrees = $buffer / 111; // Rough conversion
        
        $minLat = min($startPoint['lat'], $endPoint['lat']) - $bufferDegrees;
        $maxLat = max($startPoint['lat'], $endPoint['lat']) + $bufferDegrees;
        $minLng = min($startPoint['lng'], $endPoint['lng']) - $bufferDegrees;
        $maxLng = max($startPoint['lng'], $endPoint['lng']) + $bufferDegrees;
        
        $query = TrailSegment::withinBounds($minLat, $maxLat, $minLng, $maxLng)
                            ->hikingTrails()
                            ->publicAccess()
                            ->with(['startIntersection', 'endIntersection']);
        
        // Apply difficulty filter
        if (isset($options['difficulty'])) {
            $sacScales = $this->getSacScalesForDifficulty($options['difficulty']);
            if ($sacScales) {
                $query->whereIn('sac_scale', $sacScales);
            }
        }
        
        // Apply surface preference
        if (isset($options['surface_preference'])) {
            $query->where('surface', $options['surface_preference']);
        }
        
        return $query->get();
    }
    
    /**
     * Find optimal path through segments using modified A* algorithm
     */
    private function findOptimalPath(Collection $segments, array $start, array $end): Collection
    {
        if ($segments->isEmpty()) {
            return collect();
        }
        
        // Simple implementation: find segments closest to start and end
        // In a full implementation, this would use A* pathfinding
        
        $startSegment = $this->findNearestSegment($segments, $start);
        $endSegment = $this->findNearestSegment($segments, $end);
        
        if (!$startSegment || !$endSegment) {
            return collect();
        }
        
        // If same segment contains both points
        if ($startSegment->id === $endSegment->id) {
            return collect([$startSegment]);
        }
        
        // Find connecting path (simplified - would use graph search in full implementation)
        $path = $this->findConnectingPath($startSegment, $endSegment, $segments);
        
        return $path;
    }
    
    /**
     * Find segment nearest to a point
     */
    private function findNearestSegment(Collection $segments, array $point): ?TrailSegment
    {
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;
        
        foreach ($segments as $segment) {
            $distance = $this->distanceToSegment($point, $segment);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $segment;
            }
        }
        
        return $nearest;
    }
    
    /**
     * Calculate distance from point to segment
     */
    private function distanceToSegment(array $point, TrailSegment $segment): float
    {
        $points = $segment->points_data;
        $minDistance = PHP_FLOAT_MAX;
        
        foreach ($points as $segmentPoint) {
            $distance = $this->haversineDistance(
                $point['lat'], $point['lng'],
                $segmentPoint['lat'], $segmentPoint['lng']
            );
            $minDistance = min($minDistance, $distance);
        }
        
        return $minDistance;
    }
    
    /**
     * Find connecting path between segments (simplified)
     */
    private function findConnectingPath(TrailSegment $start, TrailSegment $end, Collection $allSegments): Collection
    {
        $path = collect([$start]);
        $current = $start;
        $visited = [$start->id];
        $maxDepth = 20; // Prevent infinite loops
        $depth = 0;
        
        while ($current->id !== $end->id && $depth < $maxDepth) {
            $nextSegment = $this->findNextConnectedSegment($current, $end, $allSegments, $visited);
            
            if (!$nextSegment) {
                break;
            }
            
            $path->push($nextSegment);
            $visited[] = $nextSegment->id;
            $current = $nextSegment;
            $depth++;
        }
        
        return $path;
    }
    
    /**
     * Find next segment connected to current that gets closer to destination
     */
    private function findNextConnectedSegment(
        TrailSegment $current, 
        TrailSegment $destination, 
        Collection $allSegments, 
        array $visited
    ): ?TrailSegment {
        $candidates = $allSegments->filter(function ($segment) use ($current, $visited) {
            return !in_array($segment->id, $visited) && $current->connectsTo($segment);
        });
        
        if ($candidates->isEmpty()) {
            return null;
        }
        
        // Choose segment that gets closest to destination
        $destCenter = $destination->center_point;
        $best = null;
        $minDistance = PHP_FLOAT_MAX;
        
        foreach ($candidates as $candidate) {
            $candidateCenter = $candidate->center_point;
            $distance = $this->haversineDistance(
                $candidateCenter['lat'], $candidateCenter['lng'],
                $destCenter['lat'], $destCenter['lng']
            );
            
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $best = $candidate;
            }
        }
        
        return $best;
    }
    
    /**
     * Generate comprehensive trail data from segments
     */
    private function generateTrailData(Collection $segments, string $trailName, array $options): array
    {
        if ($segments->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No trail segments found'
            ];
        }
        
        // Combine all segment coordinates
        $allCoordinates = [];
        $totalDistance = 0;
        $totalElevationGain = 0;
        $surfaceTypes = [];
        $difficulties = [];
        
        foreach ($segments as $segment) {
            foreach ($segment->points_data as $point) {
                $allCoordinates[] = $point;
            }
            
            $totalDistance += $segment->distance_total;
            $totalElevationGain += $segment->estimated_elevation_gain ?? 0;
            
            if ($segment->surface) {
                $surfaceTypes[] = $segment->surface;
            }
            
            $difficulties[] = $segment->difficulty;
        }
        
        // Remove duplicate consecutive points
        $optimizedCoordinates = $this->optimizeCoordinates($allCoordinates);
        
        // Determine overall difficulty
        $overallDifficulty = $this->calculateOverallDifficulty($difficulties);
        
        return [
            'success' => true,
            'data' => [
                'trail_name' => $trailName,
                'coordinates' => $optimizedCoordinates,
                'distance_km' => round($totalDistance, 2),
                'elevation_gain_m' => $totalElevationGain,
                'segment_count' => $segments->count(),
                'difficulty' => $overallDifficulty,
                'surface_types' => array_unique($surfaceTypes),
                'data_source' => 'osm_segments',
                'accuracy' => 'high',
                'segments_used' => $segments->pluck('segment_id')->toArray()
            ]
        ];
    }

    /**
     * Attempt to match an arbitrary coordinate sequence to known OSM segments
     * and return a snapped/optimized coordinate array without writing to DB.
     *
     * This is a non-destructive, read-only map-matching helper used for
     * previewing and snapping candidate polylines to OSM trail segments.
     */
    public function matchCoordinatesToSegments(array $coordinates): ?array
    {
        if (count($coordinates) < 2) {
            return null;
        }

        // Find candidate segments within a small bounding box around the path
        $matching = $this->findMatchingSegments($coordinates);
        if ($matching->isEmpty()) {
            return null;
        }

        // Combine matching segments' point data into a single coordinate list
        $allCoordinates = [];
        foreach ($matching as $segment) {
            foreach ($segment->points_data as $pt) {
                $allCoordinates[] = $pt;
            }
        }

        if (empty($allCoordinates)) {
            return null;
        }

        // Optimize and return
        $optimized = $this->optimizeCoordinates($allCoordinates);
        return $optimized;
    }
    
    /**
     * Find segments matching existing trail coordinates
     */
    private function findMatchingSegments(array $coordinates): Collection
    {
        if (count($coordinates) < 2) {
            return collect();
        }
        
        // Create bounding box around trail
        $lats = array_column($coordinates, 'lat');
        $lngs = array_column($coordinates, 'lng');
        $buffer = 0.001; // Small buffer for matching
        
        return TrailSegment::withinBounds(
            min($lats) - $buffer,
            max($lats) + $buffer,
            min($lngs) - $buffer,
            max($lngs) + $buffer
        )->get()->filter(function ($segment) use ($coordinates) {
            return $this->segmentMatchesCoordinates($segment, $coordinates);
        });
    }
    
    /**
     * Check if segment matches coordinate path
     */
    private function segmentMatchesCoordinates(TrailSegment $segment, array $coordinates): bool
    {
        $segmentPoints = $segment->points_data;
        $tolerance = 0.0005; // ~50m tolerance
        $matchCount = 0;
        
        foreach ($segmentPoints as $segmentPoint) {
            foreach ($coordinates as $trailPoint) {
                $distance = $this->haversineDistance(
                    $segmentPoint['lat'], $segmentPoint['lng'],
                    $trailPoint['lat'], $trailPoint['lng']
                );
                
                if ($distance < $tolerance) {
                    $matchCount++;
                    break;
                }
            }
        }
        
        // Require at least 30% of segment points to match
        return ($matchCount / count($segmentPoints)) > 0.3;
    }
    
    /**
     * Link trail to segments in database
     */
    private function linkTrailToSegments(Trail $trail, Collection $segments): void
    {
        DB::transaction(function () use ($trail, $segments) {
            // Clear existing links
            DB::table('trail_segment_usage')->where('trail_id', $trail->id)->delete();
            
            // Create new links
            $order = 1;
            foreach ($segments as $segment) {
                DB::table('trail_segment_usage')->insert([
                    'trail_id' => $trail->id,
                    'trail_segment_id' => $segment->id,
                    'segment_order' => $order++,
                    'direction' => 'forward',
                    'segment_distance' => $segment->distance_total,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }
    
    /**
     * Update trail metrics from segments
     */
    private function updateTrailMetrics(Trail $trail, Collection $segments): void
    {
        $totalDistance = $segments->sum('distance_total');
        $totalElevation = $segments->sum('estimated_elevation_gain');
        $difficulties = $segments->pluck('difficulty')->filter()->unique();
        
        $trail->update([
            'length' => $totalDistance,
            'elevation_gain' => $totalElevation > 0 ? $totalElevation : null,
            'coordinate_generation_method' => 'osm_segments'
        ]);
    }
    
    /**
     * Helper methods
     */
    private function getSacScalesForDifficulty(string $difficulty): ?array
    {
        return match($difficulty) {
            'beginner' => ['hiking', 'T1'],
            'intermediate' => ['mountain_hiking', 'T2', 'T3'],
            'advanced' => ['alpine_hiking', 'T4', 'T5', 'T6'],
            default => null
        };
    }
    
    private function optimizeCoordinates(array $coordinates): array
    {
        if (count($coordinates) <= 2) {
            return $coordinates;
        }
        
        $optimized = [$coordinates[0]];
        $lastPoint = $coordinates[0];
        
        for ($i = 1; $i < count($coordinates) - 1; $i++) {
            $currentPoint = $coordinates[$i];
            $distance = $this->haversineDistance(
                $lastPoint['lat'], $lastPoint['lng'],
                $currentPoint['lat'], $currentPoint['lng']
            );
            
            // Keep points that are at least 10m apart
            if ($distance > 0.01) {
                $optimized[] = $currentPoint;
                $lastPoint = $currentPoint;
            }
        }
        
        $optimized[] = end($coordinates);
        return $optimized;
    }
    
    private function calculateOverallDifficulty(array $difficulties): string
    {
        $counts = array_count_values($difficulties);
        arsort($counts);
        return array_key_first($counts) ?: 'intermediate';
    }
    
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);
        
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($deltaLng / 2) * sin($deltaLng / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}
