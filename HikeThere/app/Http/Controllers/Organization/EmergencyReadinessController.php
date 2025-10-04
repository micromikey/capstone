<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\EmergencyReadiness;
use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyReadinessController extends Controller
{
    /**
     * Display a listing of emergency readiness feedback from hikers
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get organization's trails
        $trails = Trail::where('user_id', $user->id)->get();
        $trailIds = $trails->pluck('id');
        
        // Get all hiker feedback for organization's trails (only hiker-submitted feedback)
        $assessments = EmergencyReadiness::whereIn('trail_id', $trailIds)
            ->whereNotNull('submitted_by') // Only hiker feedback
            ->with(['trail', 'submitter'])
            ->orderBy('assessment_date', 'desc')
            ->paginate(15);
        
        // Calculate statistics from hiker feedback only
        $hikerFeedback = EmergencyReadiness::whereIn('trail_id', $trailIds)
            ->whereNotNull('submitted_by')
            ->get();
            
        $stats = [
            'total_assessments' => $hikerFeedback->count(),
            'avg_score' => $hikerFeedback->count() > 0 ? round($hikerFeedback->avg('overall_score'), 2) : 0,
            'excellent_count' => $hikerFeedback->where('readiness_level', 'Excellent')->count(),
            'needs_improvement' => $hikerFeedback->where('readiness_level', 'Needs Improvement')->count() + $hikerFeedback->where('readiness_level', 'Critical')->count(),
        ];
        
        return view('organization.emergency-readiness.index', compact('assessments', 'trails', 'stats'));
    }

    /**
     * Display the specified assessment feedback
     */
    public function show(EmergencyReadiness $emergencyReadiness)
    {
        $user = Auth::user();
        
        // Verify the assessment belongs to one of the organization's trails
        $trail = Trail::where('id', $emergencyReadiness->trail_id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        // Only show hiker-submitted feedback
        if (!$emergencyReadiness->submitted_by) {
            abort(404, 'Feedback not found.');
        }
        
        $emergencyReadiness->load(['trail', 'submitter']);
        
        return view('organization.emergency-readiness.show', compact('emergencyReadiness'));
    }
}
