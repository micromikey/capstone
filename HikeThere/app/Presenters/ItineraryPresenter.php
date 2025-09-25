<?php

// ItineraryPresenter removed per user request.
// If other parts of the application still reference this file, please remove those references
// or create a new lightweight presenter stub. This file intentionally contains no class definitions.

    protected function normalizeActivities(array $activities): array
    {
        $normalized = [];
        foreach ($activities as $a) {
            if (! is_array($a)) {
                $a = (array) $a;
            }
            // Normalize time key
            $time = $a['time'] ?? $a['start_time'] ?? null;
            $a['time'] = $time;
            $a['minutes'] = $this->timeToMinutes($time);
            $normalized[] = $a;
        }

        usort($normalized, function ($x, $y) {
            $mx = $x['minutes'] ?? PHP_INT_MAX;
            $my = $y['minutes'] ?? PHP_INT_MAX;
            return $mx <=> $my;
        });

        return $normalized;
    }

    protected function timeToMinutes($t)
    {
        if (! $t) return null;
        try {
            $c = Carbon::parse($t);
            return $c->hour * 60 + $c->minute;
        } catch (\Exception $e) {
            // Try H:i format
            if (preg_match('/^(\d{1,2}):(\d{2})/', $t, $m)) {
                return intval($m[1]) * 60 + intval($m[2]);
            }
        }
        return null;
    }

    protected function mergeAuxItemsIntoActivities(array $activities, array $auxItems): array
    {
        if (empty($auxItems)) return $activities;

        // Try time-based insertion first
        foreach ($auxItems as $aux) {
            $inserted = false;
            $auxTime = $this->timeToMinutes($aux['time'] ?? $aux['start_time'] ?? null);

            if ($auxTime !== null) {
                // Find the first activity with a later time
                for ($i = 0; $i < count($activities); $i++) {
                    $actMinutes = $activities[$i]['minutes'] ?? null;
                    if ($actMinutes === null || $auxTime <= $actMinutes) {
                        array_splice($activities, $i, 0, [$this->augmentAux($aux)]);
                        $inserted = true;
                        break;
                    }
                }
                if (! $inserted) {
                    $activities[] = $this->augmentAux($aux);
                    $inserted = true;
                }
            }

            if (! $inserted) {
                // Fallback: prefer geolocation-based nearest neighbor when coordinates exist
                $auxCoords = $this->getCoords($aux);
                if ($auxCoords !== null) {
                    $closestIdx = null;
                    $closestDist = PHP_INT_MAX;
                    foreach ($activities as $i => $a) {
                        $aCoords = $this->getCoords($a);
                        if ($aCoords === null) continue;
                        $dist = $this->haversineDistance($auxCoords, $aCoords);
                        if ($dist < $closestDist) {
                            $closestDist = $dist;
                            $closestIdx = $i;
                        }
                    }
                    if ($closestIdx !== null) {
                        // Insert BEFORE the nearest activity so sidetrips appear ahead of
                        // the nearest destination/activity (user expectation: Baguio before Benguet)
                        $minutesHint = isset($activities[$closestIdx]['minutes']) && $activities[$closestIdx]['minutes'] !== null
                            ? $activities[$closestIdx]['minutes'] - 1
                            : null;
                        $a = $this->augmentAux($aux);
                        if ($minutesHint !== null) $a['minutes_hint'] = $minutesHint;
                        array_splice($activities, $closestIdx, 0, [$a]);
                        $inserted = true;
                    }
                }

                // If not inserted by distance, fall back to nearest by time
                if (! $inserted) {
                    $nearestIndex = $this->findNearestByTime($activities, $aux);
                    if ($nearestIndex !== null) {
                        // Insert BEFORE the nearest-by-time activity (more natural)
                        $minutesHint = isset($activities[$nearestIndex]['minutes']) && $activities[$nearestIndex]['minutes'] !== null
                            ? $activities[$nearestIndex]['minutes'] - 1
                            : null;
                        $a = $this->augmentAux($aux);
                        if ($minutesHint !== null) $a['minutes_hint'] = $minutesHint;
                        array_splice($activities, $nearestIndex, 0, [$a]);
                        $inserted = true;
                    }
                }
            }

            if (! $inserted) {
                // Last resort: append
                $activities[] = $this->augmentAux($aux);
            }
        }

        // Re-normalize minutes and sort
        foreach ($activities as &$a) {
            // Prefer explicit time if present, else respect a minutes_hint injected during merge;
            // otherwise push to the end by using PHP_INT_MAX.
            if (! empty($a['time'])) {
                $a['minutes'] = $this->timeToMinutes($a['time']);
            } elseif (isset($a['minutes_hint'])) {
                $a['minutes'] = $a['minutes_hint'];
            } else {
                $a['minutes'] = PHP_INT_MAX;
            }
        }

        usort($activities, function ($x, $y) {
            $mx = $x['minutes'] ?? PHP_INT_MAX;
            $my = $y['minutes'] ?? PHP_INT_MAX;
            return $mx <=> $my;
        });

        return $activities;
    }

    protected function augmentAux(array $aux): array
    {
        // Preserve place markers
        if (! empty($aux['place']) && ($aux['activity_type'] ?? null) === 'place_marker') {
            // don't overwrite any minutes_hint; compute explicit minutes only when time exists
            if (! empty($aux['time'])) {
                $aux['minutes'] = $this->timeToMinutes($aux['time'] ?? null);
            }
            return $aux;
        }

        $aux['time'] = $aux['time'] ?? null;
        // Do not force minutes here — merge may set minutes_hint to control ordering
        if (! empty($aux['time'])) {
            $aux['minutes'] = $this->timeToMinutes($aux['time']);
        }
        $aux['description'] = $aux['description'] ?? $aux['title'] ?? '';
        return $aux;
    }

    protected function findNearestByTime(array $activities, array $aux)
    {
        $auxTime = $this->timeToMinutes($aux['time'] ?? null);
        if ($auxTime === null) return null;

        $bestIdx = null;
        $bestDiff = PHP_INT_MAX;
        foreach ($activities as $i => $a) {
            $am = $a['minutes'] ?? null;
            if ($am === null) continue;
            $diff = abs($am - $auxTime);
            if ($diff < $bestDiff) {
                $bestDiff = $diff;
                $bestIdx = $i;
            }
        }

        return $bestIdx;
    }

    /**
     * Extract coordinates from an activity or aux item.
     * Supports keys: lat/lng, latitude/longitude, coords [lat,lng], coordinates ['lat','lng'] or ['latitude','longitude']
     * Returns array [lat, lng] or null
     */
    protected function getCoords($item)
    {
        if (empty($item) || !is_array($item)) {
            if (is_object($item)) $item = (array) $item;
            else return null;
        }

        if (!empty($item['lat']) && !empty($item['lng'])) {
            return [floatval($item['lat']), floatval($item['lng'])];
        }
        if (!empty($item['latitude']) && !empty($item['longitude'])) {
            return [floatval($item['latitude']), floatval($item['longitude'])];
        }
        if (!empty($item['coords']) && is_array($item['coords']) && count($item['coords']) >= 2) {
            return [floatval($item['coords'][0]), floatval($item['coords'][1])];
        }
        if (!empty($item['coordinates']) && is_array($item['coordinates'])) {
            // Common shapes: ['lat'=>..., 'lng'=>...] or [lat, lng]
            if (isset($item['coordinates']['lat']) && isset($item['coordinates']['lng'])) {
                return [floatval($item['coordinates']['lat']), floatval($item['coordinates']['lng'])];
            }
            if (isset($item['coordinates'][0]) && isset($item['coordinates'][1])) {
                return [floatval($item['coordinates'][0]), floatval($item['coordinates'][1])];
            }
        }

        return null;
    }

    /**
     * Haversine distance in meters between two [lat, lng] pairs
     */
    protected function haversineDistance(array $a, array $b): float
    {
        if (empty($a) || empty($b) || count($a) < 2 || count($b) < 2) return PHP_INT_MAX;
        $lat1 = deg2rad($a[0]); $lon1 = deg2rad($a[1]);
        $lat2 = deg2rad($b[0]); $lon2 = deg2rad($b[1]);
        $dlat = $lat2 - $lat1; $dlon = $lon2 - $lon1;
        $h = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($h), sqrt(max(0, 1 - $h)));
        $r = 6371000; // Earth radius meters
        return $r * $c;
    }

    public function getTransportLabel(array $activity): ?string
    {
        // Prefer canonical itinerary-level transport payload
        $payload = data_get($this->itinerary, 'transportPayload') ?? data_get($this->itinerary, 'transportation_details') ?? data_get($this->itinerary, 'transport_details');

        // Activity-level payload may contain legs/commute or pickup info
        $aPayload = $activity['transportation'] ?? $activity['transport_details'] ?? null;

        // If activity is a commute-style object (has legs/commute array), summarize vehicles
        $legs = null;
        if (is_array($aPayload) && (isset($aPayload['legs']) || isset($aPayload['commute']))) {
            $legs = $aPayload['legs'] ?? $aPayload['commute'];
        }
        if (! $legs && is_array($activity) && (isset($activity['legs']) || isset($activity['commute']))) {
            $legs = $activity['legs'] ?? $activity['commute'];
        }

        if (is_array($legs) && count($legs) > 0) {
            $vehicles = [];
            foreach ($legs as $l) {
                $v = $l['vehicle_label'] ?? $l['vehicle'] ?? $l['vehicle_type'] ?? null;
                if ($v) $vehicles[] = $this->humanizeVehicleLabel($v);
                // also check nested transport summaries
                if (! isset($v) && ! empty($l['transport_summary'])) $vehicles[] = substr($l['transport_summary'], 0, 40);
            }
            $vehicles = array_values(array_unique(array_filter($vehicles)));
            return count($vehicles) ? implode(', ', $vehicles) : null;
        }

        // Otherwise treat as pickup-style payload
        $pickup = data_get($payload, 'pickup_place') ?: data_get($aPayload, 'pickup_place') ?: data_get($this->itinerary, 'transportation_pickup_place') ?: null;
        $vehicle = data_get($payload, 'vehicle') ?: data_get($aPayload, 'vehicle') ?: data_get($payload, 'transportation_vehicle') ?: null;

        $vehicleLabel = null;
        if (is_array($vehicle) && isset($vehicle['label'])) {
            $vehicleLabel = $vehicle['label'];
        } elseif ($vehicle) {
            $vehicleLabel = $this->humanizeVehicleLabel($vehicle);
        }

        if ($vehicleLabel && $pickup) {
            return trim($vehicleLabel) . ' · ' . trim($pickup);
        }
        if ($vehicleLabel) return trim($vehicleLabel);
        if ($pickup) return trim($pickup);

        // Fallback to transport_mode or transport label in activity
        $tm = $activity['transport_mode'] ?? $activity['transport_mode_label'] ?? null;
        return $tm ? $tm : null;
    }

    protected function humanizeVehicleLabel($v): string
    {
        if (is_array($v)) {
            return $v['label'] ?? (string) data_get($v, 'type', 'Vehicle');
        }
        $s = (string) $v;
        // Normalize common short codes
        $map = [
            'bus' => 'Bus', 'shuttle' => 'Shuttle', 'car' => 'Car', 'taxi' => 'Taxi', 'walk' => 'Walk', 'hike' => 'Walk'
        ];
        $low = strtolower(trim($s));
        return $map[$low] ?? ucwords(str_replace(['_', '-'], ' ', $s));
    }

    public function activityNotes(array $activity): string
    {
        $notes = [];
        if (! empty($activity['note'])) $notes[] = $activity['note'];
        if (! empty($activity['guidelines']) && is_array($activity['guidelines'])) {
            $notes[] = implode('; ', array_slice($activity['guidelines'], 0, 3));
        }

        // Transit/walking/driving short summaries
        if (! empty($activity['transit_details'])) {
            $td = $activity['transit_details'];
            $notes[] = ($td['departure_time'] ?? '') . ' to ' . ($td['arrival_time'] ?? '');
        }

        return trim(implode(' — ', array_filter($notes)));
    }
}
<?php

namespace App\Presenters;

use App\Models\Itinerary;
use Carbon\Carbon;

class ItineraryPresenter
{
    protected $itinerary;

    public function __construct(Itinerary $itinerary)
    {
        $this->itinerary = $itinerary;
    }

    public function prepare(): array
    {
        $days = $this->prepareDays();
        $pacing = $this->computePacing();

        return [
            'days' => $days,
            'pacing' => $pacing,
        ];
    }

    public function computePacing(): float
    {
        // Default pacing multiplier (1.0 = normal). Adjust based on assessment if available.
        $assessment = data_get($this->itinerary, 'route_data.trail_enhancements.difficulty_assessment');

        if (is_array($assessment) && isset($assessment['pace'])) {
            // Expect pace as 'slow'|'moderate'|'fast' or numeric multiplier
            $pace = $assessment['pace'];
            if (is_numeric($pace)) {
                return floatval($pace);
            }
            return match (strtolower($pace)) {
                'slow' => 1.25,
                'moderate' => 1.0,
                'fast' => 0.9,
                default => 1.0,
            };
        }

        // Fallback: derive from difficulty
        $difficulty = strtolower(data_get($this->itinerary, 'difficulty_level', 'moderate'));
        return match ($difficulty) {
            'easy' => 0.9,
            'beginner' => 1.0,
            'moderate' => 1.0,
            'hard', 'difficult' => 1.15,
            'advanced', 'expert' => 1.3,
            default => 1.0,
        };
    }

    public function prepareDays(): array
    {
        $dailySchedule = data_get($this->itinerary, 'daily_schedule', []);
        $sideTrips = data_get($this->itinerary, 'sidetrips', []);
        $stopovers = data_get($this->itinerary, 'stopovers', []);

        // Build aux items once and assign them to days intelligently. This prevents
        // inserting the same set of aux items into every day.
        $auxItems = [];
        foreach ($stopovers as $s) {
            $auxItems[] = $this->normalizeAuxItem($s, 'stopover');
        }
        foreach ($sideTrips as $s) {
            $auxItems[] = $this->normalizeAuxItem($s, 'sidetrip');
        }

        // Assign aux items to specific days using coords/time/explicit day metadata
        $assignments = $this->assignAuxItemsToDays($dailySchedule, $auxItems);

        $prepared = [];
        foreach ($dailySchedule as $dayIndex => $day) {
            $activities = $this->normalizeActivities(data_get($day, 'activities', []));

            $dayNumber = $day['day_number'] ?? ($dayIndex + 1);
            $assignedAux = $assignments[$dayNumber] ?? [];
            if (! empty($assignedAux)) {
                $activities = $this->mergeAuxItemsIntoActivities($activities, $assignedAux);
            }

            $prepared[] = [
                'day_label' => $day['day_label'] ?? ('Day '.($dayNumber)),
                'day_number' => $dayNumber,
                'date' => $day['date'] ?? null,
                'activities' => $activities,
            ];
        }

        return $prepared;
    }

    protected function collectAuxForDay($day, $sideTrips, $stopovers): array
    {
        // Legacy: this method is kept for compatibility but presenters now assign
        // aux items to days globally via assignAuxItemsToDays().
        $auxItems = [];
        foreach ($stopovers as $s) {
            $auxItems[] = $this->normalizeAuxItem($s, 'stopover');
        }
        foreach ($sideTrips as $s) {
            $auxItems[] = $this->normalizeAuxItem($s, 'sidetrip');
        }

        return $auxItems;
    }

    /**
     * Assign aux items (stopovers / sidetrips) to days.
     * Strategy:
     *  - If aux has explicit day_number or date, respect it
     *  - Else if aux has coords, assign to day with closest activity by distance
     *  - Else if aux has time, assign to day with closest activity by time
     *  - Else assign to first day
     *
     * Returns an array keyed by day_number => [auxItems...]
     */
    protected function assignAuxItemsToDays(array $dailySchedule, array $auxItems): array
    {
        $assignments = [];
        // Normalize activities across days for matching
        $daysActivities = [];
        foreach ($dailySchedule as $idx => $d) {
            $dayNumber = $d['day_number'] ?? ($idx + 1);
            $acts = $this->normalizeActivities(data_get($d, 'activities', []));
            $daysActivities[$dayNumber] = $acts;
            $assignments[$dayNumber] = [];
        }

        foreach ($auxItems as $aux) {
            $assignedDay = null;

            if (! empty($aux['day_number'])) {
                $assignedDay = $aux['day_number'];
            } elseif (! empty($aux['date'])) {
                // Try to match date to day
                foreach ($dailySchedule as $idx => $d) {
                    $dayNumber = $d['day_number'] ?? ($idx + 1);
                    if (! empty($d['date']) && $d['date'] == $aux['date']) {
                        $assignedDay = $dayNumber;
                        break;
                    }
                }
            }

            if ($assignedDay === null) {
                // Try coordinate-based matching
                $auxCoords = $this->getCoords($aux);
                if ($auxCoords !== null) {
                    $bestDay = null; $bestDist = PHP_INT_MAX;
                    foreach ($daysActivities as $dayNum => $acts) {
                        foreach ($acts as $a) {
                            $aCoords = $this->getCoords($a);
                            if ($aCoords === null) continue;
                            $dist = $this->haversineDistance($auxCoords, $aCoords);
                            if ($dist < $bestDist) {
                                $bestDist = $dist;
                                $bestDay = $dayNum;
                            }
                        }
                    }
                    if ($bestDay !== null) {
                        $assignedDay = $bestDay;
                    }
                }
            }

            if ($assignedDay === null) {
                // Try time-based matching
                $auxTime = $this->timeToMinutes($aux['time'] ?? $aux['start_time'] ?? null);
                if ($auxTime !== null) {
                    $bestDay = null; $bestDiff = PHP_INT_MAX;
                    foreach ($daysActivities as $dayNum => $acts) {
                        foreach ($acts as $a) {
                            $am = $a['minutes'] ?? null;
                            if ($am === null) continue;
                            $diff = abs($am - $auxTime);
                            if ($diff < $bestDiff) {
                                $bestDiff = $diff;
                                $bestDay = $dayNum;
                            }
                        }
                    }
                    if ($bestDay !== null) {
                        $assignedDay = $bestDay;
                    }
                }
            }

            if ($assignedDay === null) {
                // Default to first day
                $assignedDay = array_key_first($assignments) ?: 1;
            }

            $assignments[$assignedDay][] = $aux;
        }

        return $assignments;
    }

    protected function normalizeAuxItem($it, $type = 'aux')
    {
        // Accept string or array/object forms
        if (is_string($it)) {
            return [
                'title' => $it,
                'description' => $it,
                'activity_type' => $type,
            ];
        }

        if (is_array($it)) {
            $it['activity_type'] = $it['activity_type'] ?? $type;
            return $it;
        }

        if (is_object($it)) {
            $arr = (array) $it;
            $arr['activity_type'] = $arr['activity_type'] ?? $type;
            return $arr;
        }

        return ['title' => 'Aux', 'description' => '', 'activity_type' => $type];
    }

    protected function normalizeActivities(array $activities): array
    {
        $normalized = [];
        foreach ($activities as $a) {
            if (! is_array($a)) {
                $a = (array) $a;
            }
            // Normalize time key
            $time = $a['time'] ?? $a['start_time'] ?? null;
            $a['time'] = $time;
            $a['minutes'] = $this->timeToMinutes($time);
            $normalized[] = $a;
        }

        usort($normalized, function ($x, $y) {
            $mx = $x['minutes'] ?? PHP_INT_MAX;
            $my = $y['minutes'] ?? PHP_INT_MAX;
            return $mx <=> $my;
        });

        return $normalized;
    }

    protected function timeToMinutes($t)
    {
        if (! $t) return null;
        try {
            $c = Carbon::parse($t);
            return $c->hour * 60 + $c->minute;
        } catch (\Exception $e) {
            // Try H:i format
            if (preg_match('/^(\d{1,2}):(\d{2})/', $t, $m)) {
                return intval($m[1]) * 60 + intval($m[2]);
            }
        }
        return null;
    }

    protected function mergeAuxItemsIntoActivities(array $activities, array $auxItems): array
    {
        if (empty($auxItems)) return $activities;

        // Try time-based insertion first
        foreach ($auxItems as $aux) {
            $inserted = false;
            $auxTime = $this->timeToMinutes($aux['time'] ?? $aux['start_time'] ?? null);

            if ($auxTime !== null) {
                // Find the first activity with a later time
                for ($i = 0; $i < count($activities); $i++) {
                    $actMinutes = $activities[$i]['minutes'] ?? null;
                    if ($actMinutes === null || $auxTime <= $actMinutes) {
                        array_splice($activities, $i, 0, [$this->augmentAux($aux)]);
                        $inserted = true;
                        break;
                    }
                }
                if (! $inserted) {
                    $activities[] = $this->augmentAux($aux);
                    $inserted = true;
                }
            }

            if (! $inserted) {
                // Fallback: prefer geolocation-based nearest neighbor when coordinates exist
                $auxCoords = $this->getCoords($aux);
                if ($auxCoords !== null) {
                    $closestIdx = null;
                    $closestDist = PHP_INT_MAX;
                    foreach ($activities as $i => $a) {
                        $aCoords = $this->getCoords($a);
                        if ($aCoords === null) continue;
                        $dist = $this->haversineDistance($auxCoords, $aCoords);
                        if ($dist < $closestDist) {
                            $closestDist = $dist;
                            $closestIdx = $i;
                        }
                    }
                    if ($closestIdx !== null) {
                        array_splice($activities, $closestIdx + 1, 0, [$this->augmentAux($aux)]);
                        $inserted = true;
                    }
                }

                // If not inserted by distance, fall back to nearest by time
                if (! $inserted) {
                    $nearestIndex = $this->findNearestByTime($activities, $aux);
                    if ($nearestIndex !== null) {
                        array_splice($activities, $nearestIndex + 1, 0, [$this->augmentAux($aux)]);
                        $inserted = true;
                    }
                }
            }

            if (! $inserted) {
                // Last resort: append
                $activities[] = $this->augmentAux($aux);
            }
        }

        // Re-normalize minutes and sort
        foreach ($activities as &$a) {
            $a['minutes'] = $this->timeToMinutes($a['time'] ?? null);
        }

        usort($activities, function ($x, $y) {
            $mx = $x['minutes'] ?? PHP_INT_MAX;
            $my = $y['minutes'] ?? PHP_INT_MAX;
            return $mx <=> $my;
        });

        return $activities;
    }

    protected function augmentAux(array $aux): array
    {
        $aux['time'] = $aux['time'] ?? null;
        $aux['minutes'] = $this->timeToMinutes($aux['time']);
        $aux['description'] = $aux['description'] ?? $aux['title'] ?? '';
        return $aux;
    }

    protected function findNearestByTime(array $activities, array $aux)
    {
        $auxTime = $this->timeToMinutes($aux['time'] ?? null);
        if ($auxTime === null) return null;

        $bestIdx = null;
        $bestDiff = PHP_INT_MAX;
        foreach ($activities as $i => $a) {
            $am = $a['minutes'] ?? null;
            if ($am === null) continue;
            $diff = abs($am - $auxTime);
            if ($diff < $bestDiff) {
                $bestDiff = $diff;
                $bestIdx = $i;
            }
        }

        return $bestIdx;
    }

    /**
     * Extract coordinates from an activity or aux item.
     * Supports keys: lat/lng, latitude/longitude, coords [lat,lng], coordinates ['lat','lng'] or ['latitude','longitude']
     * Returns array [lat, lng] or null
     */
    protected function getCoords($item)
    {
        if (empty($item) || !is_array($item)) {
            if (is_object($item)) $item = (array) $item;
            else return null;
        }

        if (!empty($item['lat']) && !empty($item['lng'])) {
            return [floatval($item['lat']), floatval($item['lng'])];
        }
        if (!empty($item['latitude']) && !empty($item['longitude'])) {
            return [floatval($item['latitude']), floatval($item['longitude'])];
        }
        if (!empty($item['coords']) && is_array($item['coords']) && count($item['coords']) >= 2) {
            return [floatval($item['coords'][0]), floatval($item['coords'][1])];
        }
        if (!empty($item['coordinates']) && is_array($item['coordinates'])) {
            // Common shapes: ['lat'=>..., 'lng'=>...] or [lat, lng]
            if (isset($item['coordinates']['lat']) && isset($item['coordinates']['lng'])) {
                return [floatval($item['coordinates']['lat']), floatval($item['coordinates']['lng'])];
            }
            if (isset($item['coordinates'][0]) && isset($item['coordinates'][1])) {
                return [floatval($item['coordinates'][0]), floatval($item['coordinates'][1])];
            }
        }

        return null;
    }

    /**
     * Haversine distance in meters between two [lat, lng] pairs
     */
    protected function haversineDistance(array $a, array $b): float
    {
        if (empty($a) || empty($b) || count($a) < 2 || count($b) < 2) return PHP_INT_MAX;
        $lat1 = deg2rad($a[0]); $lon1 = deg2rad($a[1]);
        $lat2 = deg2rad($b[0]); $lon2 = deg2rad($b[1]);
        $dlat = $lat2 - $lat1; $dlon = $lon2 - $lon1;
        $h = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($h), sqrt(max(0, 1 - $h)));
        $r = 6371000; // Earth radius meters
        return $r * $c;
    }

    public function getTransportLabel(array $activity): ?string
    {
        // Prefer canonical itinerary-level transport payload
        $payload = data_get($this->itinerary, 'transportPayload') ?? data_get($this->itinerary, 'transportation_details');

        // Activity-level payload
        $aPayload = $activity['transportation'] ?? $activity['transport_details'] ?? null;

        $vehicle = data_get($payload, 'vehicle') ?: data_get($aPayload, 'vehicle') ?: data_get($payload, 'transportation_vehicle') ?: null;
        $pickup = data_get($payload, 'pickup_place') ?: data_get($aPayload, 'pickup_place') ?: data_get($this->itinerary, 'transportation_pickup_place') ?: null;

        $vehicleLabel = $vehicle;
        if (is_array($vehicle) && isset($vehicle['label'])) {
            $vehicleLabel = $vehicle['label'];
        }

        if ($vehicleLabel && $pickup) {
            return trim($vehicleLabel).' · '.trim($pickup);
        }
        if ($vehicleLabel) return trim($vehicleLabel);
        if ($pickup) return trim($pickup);

        // Fallback to transport_mode or transport label in activity
        $tm = $activity['transport_mode'] ?? $activity['transport_mode_label'] ?? null;
        return $tm ? $tm : null;
    }

    public function activityNotes(array $activity): string
    {
        $notes = [];
        if (! empty($activity['note'])) $notes[] = $activity['note'];
        if (! empty($activity['guidelines']) && is_array($activity['guidelines'])) {
            $notes[] = implode('; ', array_slice($activity['guidelines'], 0, 3));
        }

        // Transit/walking/driving short summaries
        if (! empty($activity['transit_details'])) {
            $td = $activity['transit_details'];
            $notes[] = ($td['departure_time'] ?? '') . ' to ' . ($td['arrival_time'] ?? '');
        }

        return trim(implode(' — ', array_filter($notes)));
    }
}
