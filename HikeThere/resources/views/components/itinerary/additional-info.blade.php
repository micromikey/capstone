@props(['trail', 'build'])

<!-- Additional Trail Information -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Left Column: Basic Info & Package -->
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">Basic Information</h3>
            <div class="bg-gray-50 rounded-lg p-4 space-y-3 mt-3">
                <div class="flex items-start">
                    <span class="text-gray-600 w-36 flex-shrink-0">Best Season:</span>
                    <span class="font-medium ml-4 flex-1 break-words">
                        {{ $trail['best_season'] ?? 'N/A' }}
                    </span>
                </div>
                <div class="flex items-start">
                    <span class="text-gray-600 w-36 flex-shrink-0">Departure Point:</span>
                    @php
                        $departurePoint = null;
                        if (!empty($build) && is_array($build)) {
                            $departurePoint = $build['meeting_point'] ?? $build['pickup_point'] ?? $build['pickup'] ?? $build['meeting'] ?? null;
                            if (empty($departurePoint) && !empty($build['legs']) && is_array($build['legs'])) {
                                $firstLeg = $build['legs'][0] ?? null;
                                if (is_array($firstLeg)) {
                                    $departurePoint = $firstLeg['from'] ?? $firstLeg['to'] ?? null;
                                }
                            }
                        }
                        if (empty($departurePoint) && !empty($build['vehicle'])) {
                            $departurePoint = $build['vehicle'];
                        }
                        if (empty($departurePoint)) {
                            $departurePoint = $trail['departure_point'] ?? null;
                        }
                    @endphp
                    <span class="font-medium ml-4 flex-1 break-words">{{ $departurePoint ?? 'N/A' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-gray-600 w-36 flex-shrink-0">Transport Options:</span>
                    @php
                        $transportDisplay = null;
                        if (!empty($build) && is_array($build)) {
                            $mode = strtolower($build['transport_mode'] ?? '');
                            if (in_array($mode, ['pickup', 'pick up', 'meet', 'meeting'])) {
                                $meeting = $build['meeting_point'] ?? $build['pickup_point'] ?? null;
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
                        if (empty($transportDisplay)) $transportDisplay = $trail['transport_options'] ?? 'Varies';
                    @endphp
                    <span class="font-medium ml-4 flex-1 break-words">{{ $transportDisplay }}</span>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700">Package Inclusions</h3>
            <div class="bg-gray-50 rounded-lg p-4 mt-3">
                <p class="text-gray-700">{{ $trail['package_inclusions'] ?? 'Not specified.' }}</p>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700">Terrain & Requirements</h3>
            <div class="bg-gray-50 rounded-lg p-4 space-y-3 mt-3">
                <div>
                    <span class="text-gray-600 font-medium">Terrain Notes:</span>
                    <p class="text-gray-700 mt-1">{{ $trail['terrain_notes'] ?? 'No specific terrain notes.' }}</p>
                </div>
                @if(!empty($trail['other_trail_notes']))
                    <div>
                        <span class="text-gray-600 font-medium">Additional Notes:</span>
                        <p class="text-gray-700 mt-1">{{ $trail['other_trail_notes'] }}</p>
                    </div>
                @endif
                @if(!empty($trail['requirements']))
                    <div>
                        <span class="text-gray-600 font-medium">Requirements:</span>
                        <p class="text-gray-700 mt-1">{{ $trail['requirements'] }}</p>
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
                    <span class="font-medium ml-4 flex-1 break-words">
                        {{ (!empty($trail['permit_required']) ? 'Yes' : 'No') }}
                    </span>
                </div>
                @if(!empty($trail['permit_process']))
                    <div>
                        <span class="text-gray-600 font-medium">Permit Process:</span>
                        <p class="text-gray-700 mt-1">{{ $trail['permit_process'] }}</p>
                    </div>
                @endif
                <div>
                    <span class="text-gray-600 font-medium">Emergency Contacts:</span>
                    <p class="text-gray-700 mt-1">{{ $trail['emergency_contacts'] ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700">Health & Fitness</h3>
            <div class="bg-gray-50 rounded-lg p-4 space-y-3 mt-3">
                <div>
                    <span class="text-gray-600 font-medium">Health & Fitness Requirements:</span>
                    <p class="text-gray-700 mt-1">{{ $trail['health_fitness'] ?? 'General fitness recommended.' }}</p>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Packing List:</span>
                    <p class="text-gray-700 mt-1">{{ $trail['packing_list'] ?? 'Bring standard hiking gear.' }}</p>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700">Additional Information</h3>
            <div class="bg-gray-50 rounded-lg p-4 space-y-3 mt-3">
                @if(!empty($trail['side_trips']))
                    <div>
                        <span class="text-gray-600 font-medium">Side Trips:</span>
                        <p class="text-gray-700 mt-1">{{ $trail['side_trips'] }}</p>
                    </div>
                @endif
                @if(!empty($trail['campsite_info']))
                    <div>
                        <span class="text-gray-600 font-medium">Campsite Information:</span>
                        <p class="text-gray-700 mt-1">{{ $trail['campsite_info'] }}</p>
                    </div>
                @endif
                @if(!empty($trail['guide_info']))
                    <div>
                        <span class="text-gray-600 font-medium">Guide Information:</span>
                        <p class="text-gray-700 mt-1">{{ $trail['guide_info'] }}</p>
                    </div>
                @endif
                @if(!empty($trail['environmental_practices']))
                    <div>
                        <span class="text-gray-600 font-medium">Environmental Practices:</span>
                        <p class="text-gray-700 mt-1">{{ $trail['environmental_practices'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>