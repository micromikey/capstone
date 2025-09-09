<?php

namespace App\Jobs;

use App\Models\Trail;
use App\Services\WikilocService;
use App\Services\OpenRouteService;
use App\Services\GoogleDirectionsService;
use App\Services\OSMTrailBuilder;
use App\Services\TrailMetricsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnrichTrailData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120; // seconds

    public function __construct(private int $trailId) {}

    public function handle(): void
    {
        $trail = Trail::find($this->trailId);
        if (!$trail) { return; }

        // Skip if already has coordinates or length
        if (is_array($trail->coordinates) && count($trail->coordinates) > 1 && $trail->length !== null) {
            return; }

        $stepsTried = [];
        $coords = null;
        $source = null;

        try {
            // 1. Wikiloc
            $wikiloc = app(WikilocService::class);
            $wikilocResult = $wikiloc->getTrailCoordinates($trail->location->name ?? '', $trail->trail_name, $trail->mountain_name);
            if ($wikilocResult && isset($wikilocResult['coordinates'])) {
                $coords = $wikilocResult['coordinates'];
                $source = 'wikiloc';
            }
            $stepsTried[] = 'wikiloc:'.($coords?'hit':'miss');

            // 2. OpenRouteService fallback (approx route via start/end heuristics) - requires origin/destination strings
            if (!$coords) {
                $ors = app(OpenRouteService::class);
                if ($ors->isAvailable()) {
                    $origin = $trail->departure_point; // heuristic
                    $destination = $trail->mountain_name.' summit';
                    $orsResult = $ors->getHikingDirections($origin, $destination);
                    if (!empty($orsResult['overview_polyline']['points'])) {
                        $decoded = $this->decodePolyline($orsResult['overview_polyline']['points']);
                        if ($decoded) {
                            $coords = $decoded;
                            $source = 'openrouteservice';
                        }
                    }
                    $stepsTried[] = 'ors:' . ($source==='openrouteservice'?'hit':'miss');
                }
            }

            // 3. Google Directions fallback
            if (!$coords) {
                $google = app(GoogleDirectionsService::class);
                // Primary attempt: detailed coordinate extraction method
                $gCoords = $google->getTrailCoordinates($trail->departure_point, $trail->mountain_name.' summit');
                if ($gCoords) { $coords = $gCoords; $source = 'google_directions'; }
                $stepsTried[] = 'google_extracted:'.($coords?'hit':'miss');
                // Secondary attempt: generic directions polyline decode
                if (!$coords) {
                    $gDir = $google->getDirections($trail->departure_point, $trail->mountain_name.' summit', [], 'walking');
                    if ($gDir && !empty($gDir['polyline'])) {
                        $decoded = $this->decodePolyline($gDir['polyline']);
                        if ($decoded) { $coords = $decoded; $source = 'google_polyline'; }
                    }
                    $stepsTried[] = 'google_polyline:'.($source==='google_polyline'?'hit':'miss');
                }
            }

            // 4. OSM Segment enhancement if we somehow have minimal coords
            if ($coords && count($coords) > 1) {
                $builder = app(OSMTrailBuilder::class);
                try {
                    // Attempt to enhance (will adjust metrics & method)
                    $tempTrail = clone $trail;
                    $tempTrail->coordinates = $coords;
                    $builder->enhanceTrailWithSegments($tempTrail); // uses DB, but we cloned so ignore result here
                } catch (\Throwable $e) {
                    Log::info('OSM enhance skipped: '.$e->getMessage());
                }
            }

            if ($coords) {
                    // Store coordinates directly
                    $trail->coordinates = $coords;
                    $trail->coordinate_generation_method = $source;
                    (new TrailMetricsService())->fillMissingMetrics($trail);
                // Confidence heuristic
                $trail->metrics_confidence = match($source) {
                    'wikiloc' => 'high',
                    'openrouteservice','google_directions','google_polyline' => 'medium',
                    default => 'low'
                };
                $trail->save();
            }

            Log::info('Trail enrichment completed', [
                'trail_id' => $trail->id,
                'source' => $source,
                'steps' => $stepsTried
            ]);
        } catch (\Throwable $e) {
            Log::error('Trail enrichment failed', [
                'trail_id' => $trail->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Decode an encoded polyline string to array of ['lat'=>x,'lng'=>y].
     */
    private function decodePolyline(string $encoded): array
    {
        $len = strlen($encoded);
        $index = 0; $lat = 0; $lng = 0; $points = [];
        while ($index < $len) {
            $result = 0; $shift = 0; $b = 0;
            do { $b = ord($encoded[$index++]) - 63; $result |= ($b & 0x1f) << $shift; $shift += 5; } while ($b >= 0x20);
            $dlat = ($result & 1) ? ~($result >> 1) : ($result >> 1); $lat += $dlat;
            $result = 0; $shift = 0;
            do { $b = ord($encoded[$index++]) - 63; $result |= ($b & 0x1f) << $shift; $shift += 5; } while ($b >= 0x20);
            $dlng = ($result & 1) ? ~($result >> 1) : ($result >> 1); $lng += $dlng;
            $points[] = ['lat' => $lat / 1e5, 'lng' => $lng / 1e5];
        }
        return $points;
    }
}
