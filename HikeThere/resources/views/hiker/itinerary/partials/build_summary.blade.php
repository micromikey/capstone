@php
// Partial: build_summary
// Renders a small transport summary based on the builder payload.
// Expected $build shape (examples):
// ['transport_mode' => 'Pickup'|'Commute', 'vehicle' => 'Van', 'meeting_point' => 'Park', 'legs' => [['from'=>'A','to'=>'B','vehicle'=>'Bus'], ...]]
// The generated view prefers user-submitted builder payloads via the itinerary payload or old() input.
// Expects $build (array) and $itinerary (array) in scope. Falls back to sensible defaults.
$build = $build ?? [];
$itinerary = $itinerary ?? [];

// Determine transport mode: Pickup if meeting/pickup point provided, otherwise Commute if legs exist
$transportModeDisplay = 'Commute';
$vehicleDisplay = null;
if (!empty($build) && is_array($build)) {
    $hasPickup = !empty($build['meeting_point']) || !empty($build['pickup_point']) || !empty($build['pickup']) || !empty($build['meeting']);
    $hasLegs = !empty($build['legs']) && is_array($build['legs']) && count($build['legs']) > 0;
    if ($hasPickup) {
        $transportModeDisplay = 'Pickup';
        $vehicleDisplay = $build['vehicle'] ?? null;
    } elseif ($hasLegs) {
        $transportModeDisplay = 'Commute';
        // Collect distinct vehicles mentioned in legs
        $vehicles = [];
        foreach ($build['legs'] as $leg) {
            if (!empty($leg['vehicle'])) $vehicles[] = $leg['vehicle'];
        }
        $vehicles = array_values(array_unique(array_filter($vehicles)));
        if (!empty($vehicles)) {
            $vehicleDisplay = implode(', ', $vehicles);
        } else {
            $vehicleDisplay = $build['vehicle'] ?? null;
        }
    } else {
        $transportModeDisplay = $build['transport_mode'] ?? 'Commute';
        $vehicleDisplay = $build['vehicle'] ?? null;
    }
}
if (empty($vehicleDisplay)) $vehicleDisplay = 'TBD';
@endphp

<div class="p-4 border rounded">
    <h3 class="font-medium text-gray-700">Transport (Build)</h3>
    <p class="text-sm text-gray-600 mt-2">Mode: {{ $transportModeDisplay }}</p>
    <p class="text-sm text-gray-600">Vehicle: {{ $vehicleDisplay }}</p>
</div>
