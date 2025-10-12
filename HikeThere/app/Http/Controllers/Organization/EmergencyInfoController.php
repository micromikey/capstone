<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyInfoController extends Controller
{
    /**
     * Show the emergency info management page for a trail
     */
    public function edit(Trail $trail)
    {
        // Ensure organization owns this trail
        if ($trail->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to trail emergency information');
        }

        return view('org.trails.emergency-info', compact('trail'));
    }

    /**
     * Update emergency information for a trail
     */
    public function update(Request $request, Trail $trail)
    {
        // Ensure organization owns this trail
        if ($trail->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to trail emergency information');
        }

        $validated = $request->validate([
            'emergency_numbers' => 'nullable|array',
            'emergency_numbers.*.service' => 'required|string|max:255',
            'emergency_numbers.*.number' => 'required|string|max:50',
            
            'hospitals' => 'nullable|array',
            'hospitals.*.name' => 'required|string|max:255',
            'hospitals.*.address' => 'required|string|max:500',
            'hospitals.*.phone' => 'nullable|string|max:50',
            'hospitals.*.distance' => 'nullable|string|max:50',
            
            'ranger_stations' => 'nullable|array',
            'ranger_stations.*.name' => 'required|string|max:255',
            'ranger_stations.*.address' => 'required|string|max:500',
            'ranger_stations.*.phone' => 'nullable|string|max:50',
            'ranger_stations.*.contact_person' => 'nullable|string|max:255',
            
            'evacuation_points' => 'nullable|array',
            'evacuation_points.*.name' => 'required|string|max:255',
            'evacuation_points.*.coordinates' => 'nullable|string|max:100',
            'evacuation_points.*.description' => 'required|string|max:1000',
            
            'off_limits_areas' => 'nullable|array',
            'off_limits_areas.*.name' => 'required|string|max:255',
            'off_limits_areas.*.coordinates' => 'nullable|string|max:100',
            'off_limits_areas.*.reason' => 'required|string|max:1000',
        ]);

        // Build the emergency_info JSON structure
        $emergencyInfo = [
            'emergency_numbers' => $validated['emergency_numbers'] ?? [],
            'hospitals' => $validated['hospitals'] ?? [],
            'ranger_stations' => $validated['ranger_stations'] ?? [],
            'evacuation_points' => $validated['evacuation_points'] ?? [],
            'off_limits_areas' => $validated['off_limits_areas'] ?? [],
            'last_updated' => now()->toDateTimeString(),
            'updated_by' => Auth::user()->name,
        ];

        // Update the trail
        $trail->update([
            'emergency_info' => $emergencyInfo,
        ]);

        return redirect()
            ->route('org.trails.emergency-info.edit', $trail)
            ->with('success', 'Emergency information updated successfully!');
    }

    /**
     * Clear emergency information for a trail (revert to auto-generated)
     */
    public function destroy(Trail $trail)
    {
        // Ensure organization owns this trail
        if ($trail->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to trail emergency information');
        }

        $trail->update([
            'emergency_info' => null,
        ]);

        return redirect()
            ->route('org.trails.emergency-info.edit', $trail)
            ->with('success', 'Emergency information cleared. System will use auto-generated data.');
    }
}
