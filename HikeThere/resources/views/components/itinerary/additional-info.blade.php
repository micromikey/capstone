@props(['trail', 'build'])

<!-- Enhanced Additional Trail Information -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Left Column: Basic Info & Package -->
    <div class="space-y-8">
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200/60 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-6">
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-3 rounded-xl shadow-lg mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold bg-gradient-to-r from-emerald-700 to-teal-700 bg-clip-text text-transparent">Basic Information</h3>
            </div>
            <div class="rounded-xl p-5 space-y-4">
                <div class="flex items-center justify-between bg-white p-3 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-slate-700 font-semibold mr-3">Best Season:</span>
                    </div>
                    <span class="bg-emerald-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                        {{ $trail['best_season'] ?? 'N/A' }}
                    </span>
                </div>
                <div class="flex items-center justify-between bg-white p-3 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-slate-700 font-semibold mr-3">Departure Point:</span>
                    </div>
                    <span class="bg-emerald-500 text-white px-3 py-1 rounded-full text-sm font-bold">
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
                        {{ $departurePoint ?? 'N/A' }}
                    </span>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <span class="text-slate-700 font-semibold">Transport Options:</span>
                    </div>
                    <div class="bg-white p-3 rounded-lg">
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
                        <span class="bg-emerald-500 text-white px-3 py-1 rounded-full text-sm font-bold">{{ $transportDisplay }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200/60 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-4">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-xl shadow-lg mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold bg-gradient-to-r from-blue-700 to-indigo-700 bg-clip-text text-transparent">Package Inclusions</h3>
            </div>
            <div class="bg-white rounded-xl p-4">
                <p class="text-slate-700 leading-relaxed">{{ $trail['package_inclusions'] ?? 'Not specified.' }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-amber-50 border-2 border-orange-200/60 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-4">
                <div class="bg-gradient-to-br from-orange-500 to-amber-600 p-3 rounded-xl shadow-lg mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold bg-gradient-to-r from-orange-700 to-amber-700 bg-clip-text text-transparent">Terrain & Requirements</h3>
            </div>
            <div class="rounded-xl p-5 space-y-4">
                <div class="bg-white p-3 rounded-lg">
                    <span class="text-slate-700 font-semibold flex items-center mb-2">
                        Terrain Notes:
                    </span>
                    <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['terrain_notes'] ?? 'No specific terrain notes.' }}</p>
                </div>
                @if(!empty($trail['other_trail_notes']))
                    <div class="bg-white p-3 rounded-lg">
                        <span class="text-slate-700 font-semibold flex items-center mb-2">
                            Additional Notes:
                        </span>
                        <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['other_trail_notes'] }}</p>
                    </div>
                @endif
                @if(!empty($trail['requirements']))
                    <div class="bg-white p-3 rounded-lg">
                        <span class="text-slate-700 font-semibold flex items-center mb-2">
                            Requirements:
                        </span>
                        <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['requirements'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Permits, Health, Additional Info -->
    <div class="space-y-8">
        <div class="bg-gradient-to-br from-red-50 to-orange-50 border-2 border-red-200/60 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-6">
                <div class="bg-gradient-to-br from-red-500 to-orange-600 p-3 rounded-xl shadow-lg mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold bg-gradient-to-r from-red-700 to-orange-700 bg-clip-text text-transparent">Permits & Safety</h3>
            </div>
            <div class="rounded-xl p-5 space-y-4">
                <div class="flex items-center justify-between bg-white p-3 rounded-lg">
                    <div class="flex items-center">
                        <span class="text-slate-700 font-semibold mr-3">Permit Required:</span>
                    </div>
                    <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                        {{ (!empty($trail['permit_required']) ? 'Yes' : 'No') }}
                    </span>
                </div>
                @if(!empty($trail['permit_process']))
                    <div class="bg-white p-3 rounded-lg">
                        <span class="text-slate-700 font-semibold flex items-center mb-2">Permit Process:</span>
                        <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['permit_process'] }}</p>
                    </div>
                @endif
                <div class="bg-white p-3 rounded-lg">
                    <span class="text-slate-700 font-semibold flex items-center mb-2">Emergency Contacts:</span>
                    <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['emergency_contacts'] ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-teal-50 to-cyan-50 border-2 border-teal-200/60 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-6">
                <div class="bg-gradient-to-br from-teal-500 to-cyan-600 p-3 rounded-xl shadow-lg mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold bg-gradient-to-r from-teal-700 to-cyan-700 bg-clip-text text-transparent">Health & Fitness</h3>
            </div>
            <div class="rounded-xl p-5 space-y-4">
                <div class="bg-white p-3 rounded-lg">
                    <span class="text-slate-700 font-semibold flex items-center mb-2">Fitness Requirements:</span>
                    <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['health_fitness'] ?? 'General fitness recommended.' }}</p>
                </div>
                <div class="bg-white p-3 rounded-lg">
                    <span class="text-slate-700 font-semibold flex items-center mb-2">Packing List:</span>
                    <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['packing_list'] ?? 'Bring standard hiking gear.' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border-2 border-purple-200/60 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-6">
                <div class="bg-gradient-to-br from-purple-500 to-indigo-600 p-3 rounded-xl shadow-lg mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold bg-gradient-to-r from-purple-700 to-indigo-700 bg-clip-text text-transparent">Additional Information</h3>
            </div>
            <div class="rounded-xl p-5 space-y-4">
                @if(!empty($trail['side_trips']))
                    <div class="bg-white p-3 rounded-lg">
                        <span class="text-slate-700 font-semibold flex items-center mb-2">Side Trips:</span>
                        <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['side_trips'] }}</p>
                    </div>
                @endif
                @if(!empty($trail['campsite_info']))
                    <div class="bg-white p-3 rounded-lg">
                        <span class="text-slate-700 font-semibold flex items-center mb-2">Campsite Information:</span>
                        <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['campsite_info'] }}</p>
                    </div>
                @endif
                @if(!empty($trail['guide_info']))
                    <div class="bg-white p-3 rounded-lg">
                        <span class="text-slate-700 font-semibold flex items-center mb-2">Guide Information:</span>
                        <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['guide_info'] }}</p>
                    </div>
                @endif
                @if(!empty($trail['environmental_practices']))
                    <div class="bg-white p-3 rounded-lg">
                        <span class="text-slate-700 font-semibold flex items-center mb-2">Environmental Practices:</span>
                        <p class="text-slate-700 bg-white p-3 rounded-lg">{{ $trail['environmental_practices'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>