<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\SafetyIncident;
use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SafetyIncidentController extends Controller
{
    /**
     * Display a listing of hiker-reported safety incidents
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get organization's trails
        $trails = Trail::where('user_id', $user->id)->get();
        $trailIds = $trails->pluck('id');
        
        // Build query - ONLY show hiker-reported incidents
        $query = SafetyIncident::whereIn('trail_id', $trailIds)
            ->whereNotNull('reported_by') // Only hiker-reported incidents
            ->with(['trail', 'reporter']);
        
        // Apply filters
        if ($request->filled('status')) {
            // Handle both old and new status values
            $status = $request->status;
            $query->where(function($q) use ($status) {
                $q->where('status', $status)
                  ->orWhere('status', ucfirst($status))
                  ->orWhere('status', ucwords($status));
            });
        }
        
        if ($request->filled('severity')) {
            // Handle both old and new severity values
            $severity = $request->severity;
            $query->where(function($q) use ($severity) {
                $q->where('severity', $severity)
                  ->orWhere('severity', ucfirst($severity));
            });
        }
        
        if ($request->filled('trail_id')) {
            $query->where('trail_id', $request->trail_id);
        }
        
        if ($request->filled('incident_type')) {
            $query->where('incident_type', $request->incident_type);
        }
        
        $incidents = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Calculate statistics from hiker-reported incidents only
        $hikerIncidents = SafetyIncident::whereIn('trail_id', $trailIds)
            ->whereNotNull('reported_by');
        
        $stats = [
            'total_incidents' => (clone $hikerIncidents)->count(),
            'open_incidents' => (clone $hikerIncidents)
                ->where(function($q) {
                    $q->where('status', 'Open')
                      ->orWhere('status', 'open')
                      ->orWhere('status', 'reported');
                })->count(),
            'critical_incidents' => (clone $hikerIncidents)
                ->where(function($q) {
                    $q->where('severity', 'Critical')
                      ->orWhere('severity', 'critical')
                      ->orWhere('severity', 'high');
                })->count(),
            'resolved_this_month' => (clone $hikerIncidents)
                ->where(function($q) {
                    $q->where('status', 'Resolved')
                      ->orWhere('status', 'resolved');
                })
                ->whereMonth('created_at', Carbon::now()->month)
                ->count(),
        ];
        
        return view('organization.safety-incidents.index', compact('incidents', 'trails', 'stats'));
    }

    /**
     * Display the specified hiker-reported incident
     */
    public function show(SafetyIncident $safetyIncident)
    {
        $user = Auth::user();
        
        // Verify the incident belongs to one of the organization's trails
        $trail = Trail::where('id', $safetyIncident->trail_id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        // Only show hiker-reported incidents
        if (!$safetyIncident->reported_by) {
            abort(404, 'Incident not found.');
        }
        
        $safetyIncident->load(['trail', 'reporter']);
        
        return view('organization.safety-incidents.show', compact('safetyIncident'));
    }
}
