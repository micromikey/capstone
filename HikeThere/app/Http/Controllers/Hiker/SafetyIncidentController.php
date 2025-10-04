<?php

namespace App\Http\Controllers\Hiker;

use App\Http\Controllers\Controller;
use App\Models\SafetyIncident;
use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SafetyIncidentController extends Controller
{
    /**
     * Store a newly created incident report from a hiker.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trail_id' => 'required|exists:trails,id',
            'incident_type' => 'required|in:injury,accident,hazard,wildlife,weather,equipment,other',
            'severity' => 'required|in:low,medium,high,critical',
            'location' => 'required|string|max:500',
            'description' => 'required|string|max:2000',
            'incident_date' => 'required|date|before_or_equal:today',
            'incident_time' => 'nullable|date_format:H:i',
        ]);

        // Get the trail and its organization
        $trail = Trail::findOrFail($validated['trail_id']);

        // Create the incident report
        $incident = SafetyIncident::create([
            'trail_id' => $trail->id,
            'organization_id' => $trail->user_id, // The trail's organization
            'reported_by' => Auth::id(), // The hiker reporting it
            'incident_type' => $validated['incident_type'],
            'severity' => $validated['severity'],
            'location' => $validated['location'],
            'description' => $validated['description'],
            'incident_date' => $validated['incident_date'],
            'incident_time' => $validated['incident_time'],
            'status' => 'reported', // New reports start as 'reported'
        ]);

        // TODO: Send notification to organization about the new incident report
        // TODO: If severity is 'critical', send urgent notification

        return response()->json([
            'success' => true,
            'message' => 'Safety incident reported successfully. The trail organization has been notified.',
            'incident' => $incident
        ], 201);
    }

    /**
     * Display the hiker's reported incidents.
     */
    public function index()
    {
        $incidents = SafetyIncident::with(['trail', 'organization'])
            ->where('reported_by', Auth::id())
            ->orderBy('incident_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('hiker.incidents.index', compact('incidents'));
    }

    /**
     * Display the specified incident.
     */
    public function show(SafetyIncident $incident)
    {
        // Ensure the hiker can only view their own reports
        if ($incident->reported_by !== Auth::id()) {
            abort(403, 'You can only view incidents you reported.');
        }

        $incident->load(['trail', 'organization']);

        return view('hiker.incidents.show', compact('incident'));
    }
}
