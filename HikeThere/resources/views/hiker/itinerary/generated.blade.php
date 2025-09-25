@php
/**
* Generated Itinerary Blade
*
* Assumptions about variables passed to this view (if missing, sensible defaults are used):
* - $itinerary: object/array containing keys: 'duration_days' (int), 'nights' (int), 'start_time' (string 'HH:MM'), 'start_date' (Y-m-d or Carbon), 'activities' (array keyed by day index starting at 1), 'side_trips' (array), 'stop_overs' (array)
* - $trail: object/array with: 'name', 'region', 'distance_km', 'elevation_m', 'difficulty', 'overnight_allowed' (bool)
* - $build: object/array with transportation details: 'transport_mode' ('Pickup'|'Commute'), 'vehicle' (string), 'legs' (array of legs with 'from','to','vehicle')
* - $weatherData: optional array like $weatherData[dayIndex]["HH:MM"] => 'Sunny / 24°C'
*
* Notes:
* - This view attempts to compute per-row times using Carbon and a base schedule per-day.
* - If detailed activities are present in $itinerary['activities'], they are used; otherwise a default template is generated.
* - Side trips and stop overs are inserted between the nearest activities based on a simple index heuristic.
*/
use Carbon\Carbon;
use Illuminate\Support\Arr;

// Normalize inputs: accept either an Eloquent Itinerary model or a plain array payload
$itModel = $itinerary ?? null;
$weatherData = $weatherData ?? [];
if (is_object($itModel) && method_exists($itModel, 'toArray')) {
// keep model reference handy for any model access, but operate on an array for compatibility
$itArr = $itModel->toArray();
} else {
$itArr = (array) ($itinerary ?? []);
$itModel = null;
}
$itinerary = $itArr;

// If the user just submitted the builder form or we redirected back with input,
// prefer the old `itinerary` input for a live preview so the generated view
// matches exactly what the user built. This supports both nested arrays (normal
// Laravel inputs) or a JSON-serialized itinerary string.
try {
	$oldIt = session()->getOldInput('itinerary');
	if (!empty($oldIt)) {
		// If oldIt is a JSON string, decode it
		if (is_string($oldIt)) {
			$decoded = json_decode($oldIt, true);
			if (is_array($decoded)) $oldIt = $decoded;
		}

		if (is_array($oldIt)) {
			// Normalize any JSON-encoded nested fields (some builders stringify nested objects)
			foreach ($oldIt as $k => $v) {
				if (is_string($v) && strlen($v) > 0 && ($v[0] === '{' || $v[0] === '[')) {
					$d = json_decode($v, true);
					if ($d !== null) $oldIt[$k] = $d;
				}
			}

			// Merge old input on top of the provided itinerary so preview data wins
			$itinerary = array_merge($itinerary, $oldIt);
		}
	}
} catch (\Throwable $e) {
	// ignore parsing errors — fallback to provided itinerary
}

// Precompute route_data (make it available early for basic info display)
$routeData = $itinerary['route_data'] ?? $itinerary['route'] ?? [];
if (is_string($routeData)) {
$routeData = json_decode($routeData, true) ?: [];
}

// Normalize routeData: compute total_distance_km and legs array if possible
$routeData = is_array($routeData) ? $routeData : [];
$routeData['legs'] = $routeData['legs'] ?? $routeData['route']['legs'] ?? $routeData['routes'][0]['legs'] ?? ($routeData['legs'] ?? null);
// If legs is a nested object, coerce to array
if (!empty($routeData['legs']) && !is_array($routeData['legs'])) {
	$routeData['legs'] = (array) $routeData['legs'];
}
// compute total_distance_km if present in meters
if (empty($routeData['total_distance_km'])) {
	$totalMeters = 0;
	if (!empty($routeData['total_distance'])) $totalMeters = floatval($routeData['total_distance']);
	elseif (!empty($routeData['total_distance_m'])) $totalMeters = floatval($routeData['total_distance_m']);
	elseif (!empty($routeData['legs']) && is_array($routeData['legs'])) {
		foreach ($routeData['legs'] as $leg) {
			$m = $leg['distance_m'] ?? ($leg['distance']['value'] ?? ($leg['distance_meters'] ?? 0));
			if (is_numeric($m)) $totalMeters += floatval($m);
		}
	}
	if ($totalMeters > 0) $routeData['total_distance_km'] = round($totalMeters / 1000.0, 3);
}
// legs_count flag
$routeData['legs_count'] = is_array($routeData['legs']) ? count($routeData['legs']) : 0;

// Resolve trail if passed as ID
// Resolve trail: accept numeric id, model, array, or fall back to itinerary fields
$trail = $trail ?? null;
if (is_numeric($trail)) {
try {
$trailModel = app()->make(\App\Models\Trail::class)::find($trail);
$trail = $trailModel ? $trailModel->toArray() : [];
} catch (\Throwable $e) {
$trail = [];
}
} elseif (is_object($trail)) {
$trail = (array) $trail;
} else {
$trail = (array) ($trail ?? []);
}

// If trail not provided or missing useful keys, try to resolve from itinerary payload
if (empty($trail) || (empty($trail['name']) && empty($trail['trail_name']))) {
// Try several itinerary keys that may contain trail id or name
$trailCandidate = null;
if (!empty($itinerary['trail_id'])) {
$trailCandidate = $itinerary['trail_id'];
} elseif (!empty($itinerary['trail']) && is_numeric($itinerary['trail'])) {
$trailCandidate = $itinerary['trail'];
}

if ($trailCandidate) {
try {
$trailModel = app()->make(\App\Models\Trail::class)::find($trailCandidate);
if ($trailModel) {
$trail = $trailModel->toArray();
}
} catch (\Throwable $e) {
// ignore
}
}

// If still no model, try by name fields
if ((empty($trail) || (empty($trail['name']) && empty($trail['trail_name']))) && !empty($itinerary['trail_name'])) {
try {
$trailModel = app()->make(\App\Models\Trail::class)::where('trail_name', $itinerary['trail_name'])->orWhere('trail_name', 'like', '%'.$itinerary['trail_name'].'%')->first();
if ($trailModel) $trail = $trailModel->toArray();
} catch (\Throwable $e) {
// ignore
}
}
}

// Resolve build if passed as ID or object
$build = $build ?? null;
if (is_numeric($build)) {
if (class_exists(\App\Models\Build::class)) {
try {
$buildModel = app()->make(\App\Models\Build::class)::find($build);
$build = $buildModel ? $buildModel->toArray() : [];
} catch (\Throwable $e) {
$build = [];
}
} else {
$build = [];
}
} elseif (is_object($build)) {
$build = (array) $build;
} else {
$build = (array) ($build ?? []);
}

// If build info missing, try to read from itinerary payload (common keys)
if (empty($build) && !empty($itinerary)) {
$build = $itinerary['transport_details'] ?? $itinerary['build'] ?? $itinerary['transport'] ?? $build;
if (is_object($build)) $build = (array) $build;
if (!is_array($build)) $build = [];
}

// PERSONALIZATION: If the user just submitted the builder form and we redirected back
// or want to preview the generated itinerary immediately, Laravel may have old input
// that contains `itinerary` or `build` JSON/array data. Prefer that for personalization
// so generated view reflects the user's choices without needing to persist first.
if (empty($build)) {
	try {
		// Old input may contain nested array 'itinerary' with a 'build' or 'transport_details' key
		$oldIt = session()->getOldInput('itinerary');
		if (!empty($oldIt) && is_array($oldIt)) {
			$candidate = $oldIt['build'] ?? $oldIt['transport_details'] ?? $oldIt['build_data'] ?? null;
			if (!$candidate) {
				// Sometimes the form serializes the entire itinerary as JSON in a single field
				$candidate = $oldIt;
			}
			if ($candidate) {
				$build = is_string($candidate) ? json_decode($candidate, true) ?? [] : (array) $candidate;
			}
		}
	} catch (\Throwable $e) {
		// ignore old input parse errors
	}
}

// Final ensure build is array
$build = is_array($build) ? $build : (empty($build) ? [] : (array) $build);

// Normalize trail fields for the view so code can reference consistent keys
$trail = array_merge([
// prefer explicit itinerary fields saved with the Itinerary model, then fallback to
// the resolved $trail model array or empty defaults
'name' => $itinerary['trail_name'] ?? $itinerary['trail'] ?? ($trail['name'] ?? null),
'region' => $itinerary['region'] ?? $trail['region'] ?? ($trail['location'] ?? null),
'distance_km' => $itinerary['distance_km'] ?? $itinerary['distance'] ?? $trail['distance_km'] ?? $trail['length'] ?? $trail['distance'] ?? null,
'elevation_m' => $itinerary['elevation_m'] ?? $itinerary['elevation_gain'] ?? $trail['elevation_m'] ?? $trail['elevation_gain'] ?? null,
'difficulty' => $itinerary['difficulty'] ?? $itinerary['difficulty_level'] ?? $trail['difficulty'] ?? null,
'overnight_allowed' => $itinerary['overnight_allowed'] ?? $trail['overnight_allowed'] ?? $trail['overnight'] ?? null,
'route_description' => $itinerary['route_description'] ?? $trail['route_description'] ?? $trail['summary'] ?? $trail['description'] ?? null,
], $trail);

$durationDays = isset($itinerary['duration_days']) ? intval($itinerary['duration_days']) : (isset($itinerary['days']) ? intval($itinerary['days']) : null);

// If the itinerary did not explicitly provide duration_days, try to derive it from trail data
if (empty($durationDays)) {
$durationDays = 1;
// Prefer explicit trail.duration string when possible (e.g. "2 days") and extract numeric days
$trailDurationLabel = $trail['duration'] ?? $trail['duration'] ?? ($routeData['duration'] ?? null);
if (!empty($trailDurationLabel) && preg_match('/(\d+)\s*day/i', $trailDurationLabel, $m)) {
$durationDays = max(1, intval($m[1]));
} elseif (!empty($trail['estimated_time']) || !empty($routeData['estimated_duration_hours'])) {
// estimated_time stored in minutes on Trail model; routeData may have estimated_duration_hours
if (!empty($trail['estimated_time'])) {
$mins = intval($trail['estimated_time']);
$durationDays = max(1, (int) ceil($mins / (60 * 8))); // assume 8h hiking per day
} elseif (!empty($routeData['estimated_duration_hours'])) {
$hours = floatval($routeData['estimated_duration_hours']);
$durationDays = max(1, (int) ceil($hours / 8));
}
}
}

$nights = intval($itinerary['nights'] ?? max(0, $durationDays - 1));
$startTime = $itinerary['start_time'] ?? '06:00';
$startDate = isset($itinerary['start_date']) ? Carbon::parse($itinerary['start_date']) : Carbon::today();

// Activities: prefer explicit 'activities' key, otherwise derive from stored 'daily_schedule'
$activitiesByDay = [];
if (!empty($itinerary['activities']) && is_array($itinerary['activities'])) {
$activitiesByDay = $itinerary['activities'];
} elseif (!empty($itinerary['daily_schedule']) && is_array($itinerary['daily_schedule'])) {
// daily_schedule is typically an indexed array (0-based) with each day containing ['activities' => [...]]
foreach ($itinerary['daily_schedule'] as $idx => $day) {
$activitiesByDay[$idx + 1] = is_array($day) && isset($day['activities']) && is_array($day['activities']) ? $day['activities'] : [];
}
}

// Side trips / stopovers: accept different naming conventions used by builder/controller/model
$sideTrips = $itinerary['side_trips'] ?? $itinerary['sidetrips'] ?? $itinerary['sideTrips'] ?? [];
$stopOvers = $itinerary['stop_overs'] ?? $itinerary['stopovers'] ?? $itinerary['stopOvers'] ?? [];

// Helper: format time given day index and minutes offset
function computeTimeForRow($baseDate, $baseTime, $dayIndex, $minutesOffset)
{
$baseDateTime = Carbon::parse($baseDate->copy()->addDays($dayIndex - 1)->toDateString().' '.$baseTime);
return $baseDateTime->copy()->addMinutes($minutesOffset)->format('H:i');
}

// Helper: fetch weather
function getWeatherFor($weatherData, $dayIndex, $time)
{
if (!is_array($weatherData)) return null;

// Prefer an exact match for the provided dayIndex (handles keys like 1, '1')
if (array_key_exists($dayIndex, $weatherData)) {
$day = $weatherData[$dayIndex];
if (is_array($day)) {
return isset($day[$time]) ? $day[$time] : null;
}
if (is_string($day)) {
return $day;
}
}

// Fallback: some sources index days starting at 0 — try dayIndex - 1
$zeroIndex = intval($dayIndex) - 1;
if ($zeroIndex >= 0 && array_key_exists($zeroIndex, $weatherData)) {
$day = $weatherData[$zeroIndex];
if (is_array($day)) {
return isset($day[$time]) ? $day[$time] : null;
}
if (is_string($day)) {
return $day;
}
}

return null;
}

// Intelligent notes heuristic
function intelligentNote($activity, $weather)
{
$notes = [];
$type = strtolower($activity['type'] ?? 'activity');
$title = $activity['title'] ?? '';
if (str_contains($title, 'Summit') || str_contains($title, 'Ascent')) {
$notes[] = 'Steep sections expected. Use trekking poles if available.';
}
if ($type === 'camp' || $type === 'overnight') {
$notes[] = 'Prepare sleeping gear and warm clothing.';
}
if ($weather) {
$w = strtolower($weather);
if (str_contains($w, 'rain') || str_contains($w, 'shower')) {
$notes[] = 'Carry rain gear; trails may be slippery.';
}
if (str_contains($w, 'wind')) {
$notes[] = 'Windy conditions expected; secure loose items.';
}
}
return implode(' ', $notes) ?: null;
}

// Insert side trips and stopovers into activities by simple heuristic: place alternately between activities
function mergeExtrasIntoActivities($activities, $extras)
{
if (empty($extras)) return $activities;
$result = [];
$i = 0;
$lenA = count($activities);
foreach ($activities as $act) {
$result[] = $act;
$i++;
// Insert an extra every other activity if available
if (!empty($extras)) {
$extra = array_shift($extras);
if ($extra) $result[] = $extra;
}
}
// if extras still remain, append
foreach ($extras as $e) $result[] = $e;
return $result;
}

// Helpers to compute per-day hike plan based on route/routeData
function computeHikingSpeedKph($trail)
{
	// Tuned heuristic for hiking speed (km/h)
	// Base speed by difficulty - slightly higher for easy, slightly more conservative for hard
	$diff = strtolower($trail['difficulty'] ?? 'moderate');
	if (str_contains($diff, 'easy') || str_contains($diff, 'beginner')) {
		$base = 4.8; // km/h (brisk)
	} elseif (str_contains($diff, 'hard') || str_contains($diff, 'advanced')) {
		$base = 2.6; // slower on technical terrain
	} else {
		$base = 3.6; // moderate
	}

	// Elevation penalty: meters of ascent per km slows pace.
	$elev = floatval($trail['elevation_m'] ?? 0);
	$dist = max(0.1, floatval($trail['distance_km'] ?? 0));
	$elevPerKm = $elev / $dist; // meters per km

	// Apply a penalty of ~0.06 km/h for every 100m/km of climb (heuristic tuned)
	$penalty = ($elevPerKm / 100) * 0.06;
	$speed = max(0.9, $base - $penalty);
	// If overnight or multi-day, hikers often go slightly slower per day due to packs; apply small multiplier
	if (!empty($trail['overnight_allowed'])) {
		$speed *= 0.95;
	}
	return round($speed, 2);
}

function generateDayPlan($dayIndex, $trail, $durationDays, $startTime, $routeData = [])
{
	// Normalize inputs
	$totalKm = floatval($trail['distance_km'] ?? ($trail['distance'] ?? 0));
	if ($totalKm <= 0) $totalKm = max(8, 10); // sensible default if unknown

	$durationDays = max(1, intval($durationDays ?? 1));

	// If route/segment data (legs) is available, prefer it to split distance by legs
	$distPerDay = $totalKm / $durationDays; // fallback equal split
	try {
		if (!empty($routeData) && is_array($routeData)) {
			// Some providers store legs as ['legs'=>[...]] with distances in meters
			$legs = $routeData['legs'] ?? $routeData['route']['legs'] ?? $routeData['routes'][0]['legs'] ?? null;
			if (is_array($legs) && count($legs) > 0) {
				// build per-leg km array
				$legKms = [];
				foreach ($legs as $leg) {
					// support different distance key names
					$meters = $leg['distance_m'] ?? ($leg['distance']['value'] ?? ($leg['distance_meters'] ?? null));
					$meters = is_numeric($meters) ? floatval($meters) : 0;
					$legKms[] = $meters / 1000.0;
				}

				$legsCount = count($legKms);
				// Map legs into day buckets by simple slicing so earlier legs go to earlier days
				$startIdx = (int) floor(($dayIndex - 1) * $legsCount / $durationDays);
				$endIdx = (int) ceil($dayIndex * $legsCount / $durationDays) - 1;
				$endIdx = max($startIdx, min($endIdx, $legsCount - 1));

				$sum = 0.0;
				for ($i = $startIdx; $i <= $endIdx; $i++) {
					$sum += ($legKms[$i] ?? 0);
				}

				// If sum is zero (odd data), fallback to equal split
				if ($sum > 0) {
					$distPerDay = $sum;
				}
			}
		}
	} catch (\Throwable $e) {
		// ignore and use fallback equal-split
	}

	$speed = computeHikingSpeedKph($trail);
	// time in hours for hiking that day's distance
	$hikingHours = $distPerDay / $speed;

	// Add a buffer for breaks and terrain (20% of hike time)
	$bufferHours = max(0.5, $hikingHours * 0.2);
	$totalHikeHours = $hikingHours + $bufferHours;

	// Estimated minutes spent hiking on this day
	$hikeMinutes = intval(round($totalHikeHours * 60));

	// Build activities with realistic offsets and cumulative distance/time
	$activities = [];
	$cursor = 0;
	// Wake & breakfast
	$activities[] = ['minutes' => $cursor, 'cum_minutes' => $cursor, 'cum_distance_km' => 0.0, 'title' => 'Wake up & Breakfast', 'type' => 'meal', 'location' => 'Camp'];
	$cursor += 45; // 45 minutes
	// Travel to trailhead / briefing
	$activities[] = ['minutes' => $cursor, 'cum_minutes' => $cursor, 'cum_distance_km' => 0.0, 'title' => 'Travel to Trailhead & Briefing', 'type' => 'prep', 'location' => 'Trailhead'];
	$cursor += 30;
	// Hike start
	$activities[] = ['minutes' => $cursor, 'cum_minutes' => $cursor, 'cum_distance_km' => 0.0, 'title' => 'Hike Start', 'type' => 'hike', 'location' => 'Trail'];

	// Midday lunch roughly halfway through hiking minutes
	$lunchOffset = $cursor + intval(round($hikeMinutes / 2));
	$activities[] = ['minutes' => $lunchOffset, 'cum_minutes' => $lunchOffset, 'cum_distance_km' => round($distPerDay / 2, 2), 'title' => 'Lunch Break', 'type' => 'meal', 'location' => 'On-trail'];

	// Continue hiking - indicate mid/late day activity (3/4 point)
	$threeQuarter = $cursor + intval(round($hikeMinutes * 0.75));
	$activities[] = ['minutes' => $threeQuarter, 'cum_minutes' => $threeQuarter, 'cum_distance_km' => round($distPerDay * 0.75, 2), 'title' => 'Afternoon Break', 'type' => 'hike', 'location' => 'Trail'];

	// Approach / arrival
	$arriveMinutes = $cursor + $hikeMinutes + 15; // small buffer after hike
	$activities[] = ['minutes' => $arriveMinutes, 'cum_minutes' => $arriveMinutes, 'cum_distance_km' => round($distPerDay, 2), 'title' => ($dayIndex < $durationDays ? 'Set up Camp' : 'Finish Trail / Transfer'), 'type' => ($dayIndex < $durationDays ? 'camp' : 'finish'), 'location' => ($dayIndex < $durationDays ? 'Campsite' : 'Trailhead')];

	return $activities;
}

// Format elapsed minutes into human readable Hh Mm
function formatElapsed($minutes)
{
	$m = intval($minutes);
	if ($m < 60) return $m . 'm';
	$h = intdiv($m, 60);
	$rem = $m % 60;
	return $h . 'h' . ($rem ? ' ' . $rem . 'm' : '');
}

function formatDistanceKm($km)
{
	if ($km === null) return '-';
	return number_format(floatval($km), 2) . ' km';
}

function generateNightPlan($nightIndex, $arrivalMinutes)
{
	// Night activities anchored to arrival time
	$acts = [];
	$cursor = max(0, intval($arrivalMinutes));
	// Immediately set up camp
	$acts[] = ['minutes' => $cursor, 'title' => 'Set up Camp / Check-in', 'type' => 'camp', 'location' => 'Campsite'];
	$cursor += 45; // setup time
	$acts[] = ['minutes' => $cursor, 'title' => 'Dinner & Rest', 'type' => 'meal', 'location' => 'Campsite'];
	$cursor += 60;
	$acts[] = ['minutes' => $cursor, 'title' => 'Stargazing / Campfire', 'type' => 'relax', 'location' => 'Campsite'];
	$cursor += 90;
	$acts[] = ['minutes' => $cursor, 'title' => 'Sleep', 'type' => 'overnight', 'location' => 'Tents/Campsite'];
	return $acts;
}

// Backwards-compatible wrappers (if other code calls these)
function defaultDayTemplate($dayIndex, $trail = [], $durationDays = 1, $startTime = '06:00', $routeData = [])
{
	return generateDayPlan($dayIndex, $trail, $durationDays, $startTime, $routeData);
}

function defaultNightTemplate($nightIndex, $arrivalMinutes = 1080)
{
	return generateNightPlan($nightIndex, $arrivalMinutes);
}

// Expand a sparse day's activities into finer-grained steps when only a single or few
// activities are present. Uses the generated plan as a scaffold and merges any provided
// explicit activities (keeps provided times where available).
function expandDayActivities($activities, $trail, $dayIndex, $durationDays, $startTime, $routeData = [])
{
	// If activities already look detailed (more than 3 rows) keep as-is
	if (!empty($activities) && is_array($activities) && count($activities) > 3) return $activities;

	// Generate a scaffold plan
	$scaffold = generateDayPlan($dayIndex, $trail, $durationDays, $startTime, $routeData);

	// Merge explicit activities into scaffold by title matching or time proximity
	$result = [];
	foreach ($scaffold as $sc) {
		$matched = null;
		if (!empty($activities)) {
			foreach ($activities as $act) {
				// Match by title or location substring
				if (!empty($act['title']) && str_contains(strtolower($sc['title']), strtolower($act['title']))) {
					$matched = $act;
					break;
				}
				if (!empty($act['location']) && !empty($sc['location']) && str_contains(strtolower($act['location']), strtolower($sc['location']))) {
					$matched = $act;
					break;
				}
			}
		}

		if ($matched) {
			// keep matched activity but ensure it has minutes
			if (!isset($matched['minutes'])) $matched['minutes'] = $sc['minutes'];
			$result[] = $matched;
		} else {
			$result[] = $sc;
		}
	}

	// If there were extra user activities not matched, append them preserving their minutes
	if (!empty($activities)) {
		foreach ($activities as $act) {
			// already matched?
			$found = false;
			foreach ($result as $r) {
				if (($r['title'] ?? '') === ($act['title'] ?? '') && ($r['minutes'] ?? null) === ($act['minutes'] ?? null)) { $found = true; break; }
			}
			if (!$found) $result[] = $act;
		}
	}

	// Sort by minutes to ensure chronological order
	usort($result, function($a, $b){ return intval($a['minutes'] ?? 0) <=> intval($b['minutes'] ?? 0); });
	return $result;
}
@endphp

<x-app-layout>
	<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
		<div class="bg-white shadow rounded-lg p-6">
			<!-- Header: Trail Route & Details -->
			<div class="flex items-start justify-between mb-6">
				<div>
					<h1 class="text-2xl font-semibold text-gray-900">Itinerary: {{ $trail['name'] ?? ($itinerary['title'] ?? 'Untitled Trail') }}</h1>
					<p class="text-sm text-gray-500">{{ $trail['region'] ?? '' }}</p>
				</div>
				<div class="text-right">
					@php
					// Ensure we always have a Carbon instance for the start date
					$startDateLabel = isset($itinerary['start_date']) ? Carbon::parse($itinerary['start_date']) : $startDate;

					// Coerce duration into a usable integer number of days.
					$durationInt = intval($durationDays ?? 0);

					// Look for numeric days in common labels even if payload provided a value.
					$lbl = $trail['duration'] ?? ($routeData['duration'] ?? ($itinerary['duration'] ?? ''));
					$parsedLabelDays = null;
					if (is_string($lbl)) {
					if (preg_match('/(\d+)\s*day/i', $lbl, $m)) {
					$parsedLabelDays = max(1, intval($m[1]));
					} elseif (preg_match('/(\d+)/', $lbl, $m)) {
					$parsedLabelDays = max(1, intval($m[1]));
					}
					}

					// Consider nights if the itinerary provides it (nights + 1 = days)
					$nightsPayload = isset($itinerary['nights']) ? intval($itinerary['nights']) : null;

					// If we have no duration from payload, default to 0 (we'll enforce >=1 below)
					if ($durationInt < 1) $durationInt=0;

						// Take the maximum available indication of days: payload, parsed label, or nights+1
						$candidates=[$durationInt];
						if (!is_null($parsedLabelDays)) $candidates[]=$parsedLabelDays;
						if (!is_null($nightsPayload)) $candidates[]=max(1, $nightsPayload + 1);
						$durationInt=max(1, intval(max($candidates)));

						// End date: start date plus (duration - 1) days
						$endDate=$startDateLabel->copy()->addDays(max(0, $durationInt - 1));

						// Prepare display values for header (days/nights)
						$displayDurationDays = $durationInt;
						$displayNights = intval($itinerary['nights'] ?? max(0, $displayDurationDays - 1));

						// Ensure variables used later by the day/night loops reflect the parsed duration
						$durationDays = $durationInt;
						$nights = intval($itinerary['nights'] ?? max(0, $durationDays - 1));
						@endphp
						<p class="text-sm text-gray-600">Start: {{ $startDateLabel->toFormattedDateString() }}</p>
						<p class="text-sm text-gray-600">End: {{ $endDate->toFormattedDateString() }}</p>

						@php
						// Prefer a human-friendly trail.duration string where present for header display
						$headerDurationLabel = $trail['duration'] ?? $routeData['duration'] ?? null;
						if (empty($headerDurationLabel)) {
						$headerDurationLabel = $displayDurationDays . ' day(s) • ' . $displayNights . ' night(s)';
						}
						@endphp
						<p class="text-sm text-gray-600">Duration: {{ $headerDurationLabel }}</p>
				</div>
			</div>


			<!-- Trail Summary Boxes -->
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
				<div class="p-4 border rounded">
					<h3 class="font-medium text-gray-700">Trail Route</h3>
					<p class="text-sm text-gray-600 mt-2">{{ $trail['route_description'] ?? 'Route details not provided.' }}</p>
				</div>
				<div class="p-4 border rounded">
					<h3 class="font-medium text-gray-700">Trail Details</h3>
					@php
					$displayDistance = $trail['distance_km'] ?? ($routeData['total_distance_km'] ?? null) ?? $itinerary['distance_km'] ?? $itinerary['distance'] ?? 'N/A';
					$displayElevation = $trail['elevation_m'] ?? ($routeData['elevation_gain_m'] ?? null) ?? $itinerary['elevation_m'] ?? $itinerary['elevation_gain'] ?? 'N/A';
					$displayDifficulty = $trail['difficulty'] ?? ($routeData['difficulty'] ?? null) ?? $itinerary['difficulty'] ?? $itinerary['difficulty_level'] ?? 'Unknown';
					@endphp
					<ul class="text-sm text-gray-600 mt-2 space-y-1">
						<li><strong>Distance:</strong> {{ $displayDistance }} km</li>
						<li><strong>Elevation Gain:</strong> {{ $displayElevation }} m</li>
						<li><strong>Difficulty:</strong> {{ $displayDifficulty }}</li>
						<li><strong>Overnight Allowed:</strong> {{ (!empty($trail['overnight_allowed']) ? 'Yes' : 'No') }}</li>
						@if(!empty($routeData['legs_count']) && $routeData['legs_count'] > 0)
						<li><strong>Route Segments:</strong> {{ $routeData['legs_count'] }} (using route legs for distance split)</li>
						@endif
					</ul>
				</div>
				@include('hiker.itinerary.partials.build_summary')
			</div>

			<!-- Itinerary Tables per day -->
			<div class="space-y-8">
				@for ($day = 1; $day <= max(1, $durationDays); $day++)
					@php
					$dayActivities=$activitiesByDay[$day] ?? [];
					if (empty($dayActivities)) {
						// generate a realistic per-day plan based on the trail metrics and route segments
						$dayActivities = defaultDayTemplate($day, $trail, $durationDays, $startTime, $routeData);
					} else {
						// Expand sparse user-provided activities into a scaffolded plan (route-aware)
						$dayActivities = expandDayActivities($dayActivities, $trail, $day, $durationDays, $startTime, $routeData);
					}

					// Merge side trips and stopovers into this day's activities if any (simple heuristic).
					$extras=[];
					if (!empty($sideTrips) && is_array($sideTrips)) {
					// pick up to 2 side trips for the day
					$extras=array_slice($sideTrips, 0, 2);
					}
					if (!empty($stopOvers) && is_array($stopOvers)) {
					$extras=array_merge($extras, array_slice($stopOvers, 0, 2));
					}
					$merged=mergeExtrasIntoActivities($dayActivities, $extras);

					// Starting minute offset for this day (start_time + minutes)
					$baseDateForDay=$startDate->copy()->addDays($day - 1);
					$minutesCursor = 0;
					@endphp

					<div class="bg-gray-50 p-4 rounded">
						<div class="flex items-center justify-between mb-4">
							<h2 class="text-lg font-semibold">Day {{ $day }}</h2>
							<p class="text-sm text-gray-600">{{ $baseDateForDay->toFormattedDateString() }}</p>
						</div>

						<div class="overflow-x-auto">
							<table class="min-w-full table-fixed divide-y divide-gray-200">
								<colgroup>
									<col style="width:90px">
									<col>
									<col style="width:110px">
									<col style="width:110px">
									<col style="width:120px">
									<col style="width:120px">
									<col style="width:150px">
								</colgroup>
								<thead class="bg-gray-100">
									<tr>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Time</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Activity</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Elapsed</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Distance</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Weather</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Transport</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Notes</th>
									</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
									@foreach ($merged as $idx => $act)
									@php
									// activity normalization
									$act = (array) $act;
									// minutes offset: if provided use, otherwise increment relative to plan times
									$minutes = isset($act['minutes']) ? intval($act['minutes']) : ($minutesCursor + ($idx === 0 ? 0 : 90));
									$minutesCursor = $minutes;
									$timeLabel = computeTimeForRow($baseDateForDay, $startTime, $day, $minutes);
									$weatherLabel = getWeatherFor($weatherData, $day, $timeLabel) ?? 'N/A';

									// Transport calculation
									$transportLabel = 'N/A';
									if (strtolower(($build['transport_mode'] ?? 'commute')) === 'pickup') {
									$transportLabel = $build['vehicle'] ?? 'Pickup vehicle';
									} else {
									// Commute: attempt to find leg vehicle that matches activity location
									$legs = $build['legs'] ?? [];
									$found = null;
									foreach ($legs as $leg) {
									if (isset($leg['from']) && isset($act['location']) && str_contains(strtolower($act['location']), strtolower($leg['from']))) {
									$found = $leg['vehicle'] ?? $found;
									break;
									}
									if (isset($leg['to']) && isset($act['location']) && str_contains(strtolower($act['location']), strtolower($leg['to']))) {
									$found = $leg['vehicle'] ?? $found;
									break;
									}
									}
									$transportLabel = $found ?? ($build['vehicle'] ?? 'Varies');
									}

									$notes = intelligentNote($act, $weatherLabel);
									@endphp
									<tr>
										<td class="px-4 py-3 text-sm text-gray-700">{{ $timeLabel }}</td>
										<td class="px-4 py-3 text-sm text-gray-800">
											<div class="font-medium">{{ $act['title'] ?? 'Activity' }}</div>
											@if(!empty($act['location']))
											<div class="text-xs text-gray-500">{{ $act['location'] }}</div>
											@endif
											@if(!empty($act['description']))
											<div class="text-xs text-gray-500 mt-1">{{ $act['description'] }}</div>
											@endif
										</td>
										<td class="px-4 py-3 text-sm text-gray-700">{{ isset($act['cum_minutes']) ? formatElapsed($act['cum_minutes']) : '-' }}</td>
										<td class="px-4 py-3 text-sm text-gray-700">{{ isset($act['cum_distance_km']) ? formatDistanceKm($act['cum_distance_km']) : '-' }}</td>
										<td class="px-4 py-3 text-sm text-gray-700">{{ $weatherLabel }}</td>
										<td class="px-4 py-3 text-sm text-gray-700">{{ $transportLabel }}</td>
										<td class="px-4 py-3 text-sm text-gray-700">{{ $notes ?? '-' }}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					@php
						// Capture the day's final arrival minute for night planning
						$lastDayArrival[$day] = null;
						if (isset($merged) && is_array($merged) && count($merged)) {
							$mins = array_map(function($a){ return intval($a['minutes'] ?? 0); }, $merged);
							$lastDayArrival[$day] = intval(max($mins));
						}
					@endphp
					@endfor

					{{-- Insert Night tables between days when itinerary includes nights --}}
					@php
					// Many payloads won't provide explicit per-night activities. We accept keys like 'nights_activities',
					// 'night_activities' or a keyed array in itinerary['nights'] where each index maps to activities for that night.
					$nightActivitiesByIndex = [];
					if (!empty($itinerary['night_activities']) && is_array($itinerary['night_activities'])) {
						$nightActivitiesByIndex = $itinerary['night_activities'];
					} elseif (!empty($itinerary['nights']) && is_array($itinerary['nights'])) {
						$nightActivitiesByIndex = $itinerary['nights'];
					} elseif (!empty($itinerary['nights_activities']) && is_array($itinerary['nights_activities'])) {
						$nightActivitiesByIndex = $itinerary['nights_activities'];
					}
					@endphp

					@for ($night = 1; $night <= $nights; $night++)
					@php
					$nightActs = $nightActivitiesByIndex[$night] ?? [];
					// Determine arrival minute from the corresponding day if available
					$arrivalMin = $lastDayArrival[$night] ?? null;
					if (empty($nightActs)) {
						$nightActs = defaultNightTemplate($night, $arrivalMin ?? 1080);
					}
					// For night table timing, use the same baseDate for the day that precedes it
					$baseDateForNight = $startDate->copy()->addDays($night - 1);
					$minutesCursor = 0;
					@endphp

					<div class="bg-gray-50 p-4 rounded">
						<div class="flex items-center justify-between mb-4">
							<h2 class="text-lg font-semibold">Night {{ $night }}</h2>
							<p class="text-sm text-gray-600">{{ $baseDateForNight->toFormattedDateString() }}</p>
						</div>

						<div class="overflow-x-auto">
							<table class="min-w-full table-fixed divide-y divide-gray-200">
								<colgroup>
									<col style="width:90px">
									<col>
									<col style="width:110px">
									<col style="width:110px">
									<col style="width:120px">
									<col style="width:120px">
									<col style="width:150px">
								</colgroup>
								<thead class="bg-gray-100">
									<tr>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Time</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Activity</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Elapsed</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Distance</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Weather</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Transport</th>
										<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Notes</th>
									</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
									@foreach ($nightActs as $idx => $act)
									@php
									$act = (array) $act;
									$minutes = isset($act['minutes']) ? intval($act['minutes']) : ($minutesCursor + ($idx === 0 ? 0 : 90));
									$minutesCursor = $minutes;
									$timeLabel = computeTimeForRow($baseDateForNight, $startTime, $night, $minutes);
									$weatherLabel = getWeatherFor($weatherData, $night, $timeLabel) ?? 'N/A';
									// Night transport: usually same as day's pickup/vehicle
									$transportLabel = $build['vehicle'] ?? ($build['transport_mode'] ?? 'TBD');
									$notes = intelligentNote($act, $weatherLabel);
									@endphp
									<tr>
										<td class="px-4 py-3 text-sm text-gray-700">{{ $timeLabel }}</td>
										<td class="px-4 py-3 text-sm text-gray-800">
											<div class="font-medium">{{ $act['title'] ?? 'Night Activity' }}</div>
											@if(!empty($act['location']))
											<div class="text-xs text-gray-500">{{ $act['location'] }}</div>
											@endif
											@if(!empty($act['description']))
											<div class="text-xs text-gray-500 mt-1">{{ $act['description'] }}</div>
											@endif
										</td>
										@php
											$elapsedForRow = isset($act['cum_minutes']) ? $act['cum_minutes'] : $minutes;
											$distanceForRow = $act['cum_distance_km'] ?? null;
										@endphp
										<td class="px-4 py-3 text-sm text-gray-700">{{ isset($elapsedForRow) ? formatElapsed($elapsedForRow) : '-' }}</td>
										<td class="px-4 py-3 text-sm text-gray-700">{{ isset($distanceForRow) ? formatDistanceKm($distanceForRow) : '-' }}</td>
										<td class="px-4 py-3 text-sm text-gray-700">{{ $weatherLabel }}</td>
										<td class="px-4 py-3 text-sm text-gray-700">{{ $transportLabel }}</td>
										<td class="px-4 py-3 text-sm text-gray-700">{{ $notes ?? '-' }}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					@endfor
			</div>
		</div>
	</div>

	<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
		<div class="bg-white shadow rounded-lg p-6">
			<!-- Additional Trail Information (mirror org/trails/show.blade.php where available) -->
			<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
				<!-- Left Column: Basic Info & Package -->
				<div class="space-y-6">
					<div>
						<h3 class="text-lg font-semibold text-gray-700">Basic Information</h3>
						<div class="bg-gray-50 rounded-lg p-4 space-y-3 mt-3">
							<div class="flex items-start">
								<span class="text-gray-600 w-36 flex-shrink-0">Best Season:</span>
								<span class="font-medium ml-4 flex-1 break-words">{{ $trail['best_season'] ?? $itinerary['best_season'] ?? ($routeData['best_season'] ?? 'N/A') }}</span>
							</div>
							<div class="flex items-start">
								<span class="text-gray-600 w-36 flex-shrink-0">Departure Point:</span>
								@php
								// Determine a sensible departure point: prefer build pickup/meeting, then first leg 'from',
								// then itinerary-provided departure_point, then trail field, otherwise N/A.
								$departurePoint = null;
								if (!empty($build) && is_array($build)) {
								// common keys used by builder
								$departurePoint = $build['meeting_point'] ?? $build['pickup_point'] ?? $build['pickup'] ?? $build['meeting'] ?? null;
								// If not present, try to use the first leg's 'from' or 'to'
								if (empty($departurePoint) && !empty($build['legs']) && is_array($build['legs'])) {
								$firstLeg = $build['legs'][0] ?? null;
								if (is_array($firstLeg)) {
								$departurePoint = $firstLeg['from'] ?? $firstLeg['to'] ?? null;
								}
								}
								}
								// As another option, if build has a vehicle but no meeting point, show vehicle as hint
								if (empty($departurePoint) && !empty($build['vehicle'])) {
								$departurePoint = $build['vehicle'];
								}
								// Fall back to itinerary or trail fields
								if (empty($departurePoint)) {
								$departurePoint = $itinerary['departure_point'] ?? $itinerary['meeting_point'] ?? $trail['departure_point'] ?? null;
								}
								@endphp
								<span class="font-medium ml-4 flex-1 break-words">{{ $departurePoint ?? 'N/A' }}</span>
							</div>
							<div class="flex items-start">
								<span class="text-gray-600 w-36 flex-shrink-0">Transport Options:</span>
								@php
								// Prefer explicit itinerary transport options when provided
								$transportDisplay = $itinerary['transport_options'] ?? null;
								// Otherwise synthesize from the build (transport details)
								if (empty($transportDisplay) && is_array($build) && !empty($build)) {
								$mode = strtolower($build['transport_mode'] ?? '');
								if (in_array($mode, ['pickup', 'pick up', 'meet', 'meeting'])) {
								$meeting = $build['meeting_point'] ?? $build['pickup_point'] ?? $itinerary['meeting_point'] ?? null;
								$vehicle = $build['vehicle'] ?? null;
								$transportDisplay = 'Pickup';
								if (!empty($meeting)) $transportDisplay .= ' — Meeting point: ' . $meeting;
								if (!empty($vehicle)) $transportDisplay .= ' • Vehicle: ' . $vehicle;
								} elseif (!empty($build['legs']) && is_array($build['legs'])) {
								$legsArr = [];
								foreach ($build['legs'] as $leg) {
								$from = $leg['from'] ?? null;
								$to = $leg['to'] ?? null;
								$veh = $leg['vehicle'] ?? null;
								$part = '';
								if ($from || $to) {
								$part = trim(($from ? $from : '') . ($from && $to ? ' → ' : '') . ($to ? $to : ''));
								}
								if ($veh) $part .= ($part ? ' (' . $veh . ')' : $veh);
								if ($part) $legsArr[] = $part;
								}
								$transportDisplay = implode('; ', $legsArr) ?: ($build['vehicle'] ?? null);
								} else {
								$transportDisplay = $build['vehicle'] ?? $build['transport_mode'] ?? null;
								}
								}
								// Fallbacks: trail-level field, or Varies
								if (empty($transportDisplay)) $transportDisplay = $trail['transport_options'] ?? 'Varies';
								@endphp
								<span class="font-medium ml-4 flex-1 break-words">{{ $transportDisplay }}</span>
							</div>
						</div>
					</div>

					<div>
						<h3 class="text-lg font-semibold text-gray-700">Package Inclusions</h3>
						<div class="bg-gray-50 rounded-lg p-4 mt-3">
							<p class="text-gray-700">{{ $trail['package_inclusions'] ?? $itinerary['package_inclusions'] ?? 'Not specified.' }}</p>
						</div>
					</div>

					<div>
						<h3 class="text-lg font-semibold text-gray-700">Terrain & Requirements</h3>
						<div class="bg-gray-50 rounded-lg p-4 space-y-3 mt-3">
							<div>
								<span class="text-gray-600 font-medium">Terrain Notes:</span>
								<p class="text-gray-700 mt-1">{{ $trail['terrain_notes'] ?? $itinerary['terrain_notes'] ?? 'No specific terrain notes.' }}</p>
							</div>
							@if(!empty($trail['other_trail_notes']) || !empty($itinerary['other_trail_notes']))
							<div>
								<span class="text-gray-600 font-medium">Additional Notes:</span>
								<p class="text-gray-700 mt-1">{{ $trail['other_trail_notes'] ?? $itinerary['other_trail_notes'] }}</p>
							</div>
							@endif
							@if(!empty($trail['requirements']) || !empty($itinerary['requirements']))
							<div>
								<span class="text-gray-600 font-medium">Requirements:</span>
								<p class="text-gray-700 mt-1">{{ $trail['requirements'] ?? $itinerary['requirements'] }}</p>
							</div>
							@endif
						</div>
					</div>
				</div>

				<!-- Right Column: Permits, Health, Additional Info -->
				<div class="space-y-6">
					<div>
						<h3 class="text-lg font-semibold text-gray-700">Permits & Safety</h3>
						<div class="bg-gray-50 rounded-lg p-4 space-y-3 mt-3">
							<div class="flex items-start">
								<span class="text-gray-600 w-36 flex-shrink-0">Permit Required:</span>
								<span class="font-medium ml-4 flex-1 break-words">{{ (!empty($trail['permit_required']) ? 'Yes' : (!empty($itinerary['permit_required']) ? ($itinerary['permit_required'] ? 'Yes' : 'No') : 'No')) }}</span>
							</div>
							@if(!empty($trail['permit_process']) || !empty($itinerary['permit_process']))
							<div>
								<span class="text-gray-600 font-medium">Permit Process:</span>
								<p class="text-gray-700 mt-1">{{ $trail['permit_process'] ?? $itinerary['permit_process'] }}</p>
							</div>
							@endif
							<div>
								<span class="text-gray-600 font-medium">Emergency Contacts:</span>
								<p class="text-gray-700 mt-1">{{ $trail['emergency_contacts'] ?? $itinerary['emergency_contacts'] ?? 'Not provided' }}</p>
							</div>
						</div>
					</div>

					<div>
						<h3 class="text-lg font-semibold text-gray-700">Health & Fitness</h3>
						<div class="bg-gray-50 rounded-lg p-4 space-y-3 mt-3">
							<div>
								<span class="text-gray-600 font-medium">Health & Fitness Requirements:</span>
								<p class="text-gray-700 mt-1">{{ $trail['health_fitness'] ?? $itinerary['health_fitness'] ?? 'General fitness recommended.' }}</p>
							</div>
							<div>
								<span class="text-gray-600 font-medium">Packing List:</span>
								<p class="text-gray-700 mt-1">{{ $trail['packing_list'] ?? $itinerary['packing_list'] ?? 'Bring standard hiking gear.' }}</p>
							</div>
						</div>
					</div>

					<div>
						<h3 class="text-lg font-semibold text-gray-700">Additional Information</h3>
						<div class="bg-gray-50 rounded-lg p-4 space-y-3 mt-3">
							@if(!empty($trail['side_trips']) || !empty($itinerary['side_trips']))
							<div>
								<span class="text-gray-600 font-medium">Side Trips:</span>
								<p class="text-gray-700 mt-1">{{ $trail['side_trips'] ?? $itinerary['side_trips'] }}</p>
							</div>
							@endif
							@if(!empty($trail['campsite_info']) || !empty($itinerary['campsite_info']))
							<div>
								<span class="text-gray-600 font-medium">Campsite Information:</span>
								<p class="text-gray-700 mt-1">{{ $trail['campsite_info'] ?? $itinerary['campsite_info'] }}</p>
							</div>
							@endif
							@if(!empty($trail['guide_info']) || !empty($itinerary['guide_info']))
							<div>
								<span class="text-gray-600 font-medium">Guide Information:</span>
								<p class="text-gray-700 mt-1">{{ $trail['guide_info'] ?? $itinerary['guide_info'] }}</p>
							</div>
							@endif
							@if(!empty($trail['environmental_practices']) || !empty($itinerary['environmental_practices']))
							<div>
								<span class="text-gray-600 font-medium">Environmental Practices:</span>
								<p class="text-gray-700 mt-1">{{ $trail['environmental_practices'] ?? $itinerary['environmental_practices'] }}</p>
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>