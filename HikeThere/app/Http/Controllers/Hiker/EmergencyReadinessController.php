<?php

namespace App\Http\Controllers\Hiker;

use App\Http\Controllers\Controller;
use App\Models\EmergencyReadiness;
use App\Models\Booking;
use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmergencyReadinessController extends Controller
{
    /**
     * Show the feedback form for a completed booking
     */
    public function create(Booking $booking)
    {
        $user = Auth::user();
        
        // Verify the booking belongs to the authenticated hiker
        if ($booking->user_id !== $user->id) {
            abort(403, 'You can only provide feedback for your own bookings.');
        }
        
        // Check if feedback already submitted
        if ($booking->emergency_readiness_id) {
            return redirect()->route('hiker.readiness.show', $booking->emergency_readiness_id)
                ->with('info', 'You have already submitted feedback for this hike.');
        }
        
        // Load relationships
        $booking->load(['trail', 'batch']);
        
        return view('hiker.emergency-readiness.create', compact('booking'));
    }

    /**
     * Store the emergency readiness feedback
     */
    public function store(Request $request, Booking $booking)
    {
        $user = Auth::user();
        
        // Verify the booking belongs to the authenticated hiker
        if ($booking->user_id !== $user->id) {
            abort(403, 'You can only provide feedback for your own bookings.');
        }
        
        // Check if feedback already submitted
        if ($booking->emergency_readiness_id) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted feedback for this hike.'
            ], 422);
        }
        
        $validated = $request->validate([
            'first_aid_score' => 'required|integer|min:0|max:100',
            'communication_score' => 'required|integer|min:0|max:100',
            'equipment_score' => 'required|integer|min:0|max:100',
            'staff_training_score' => 'required|integer|min:0|max:100',
            'emergency_access_score' => 'required|integer|min:0|max:100',
            'comments' => 'nullable|string|max:2000',
        ]);
        
        // Calculate overall score (average of all scores)
        $overallScore = round(
            ($validated['first_aid_score'] + 
             $validated['communication_score'] + 
             $validated['equipment_score'] + 
             $validated['staff_training_score'] + 
             $validated['emergency_access_score']) / 5
        );
        
        // Determine readiness level
        $readinessLevel = $this->determineReadinessLevel($overallScore);
        
        // Create the emergency readiness feedback
        $readiness = EmergencyReadiness::create([
            'trail_id' => $booking->trail_id,
            'organization_id' => $booking->trail->user_id,
            'submitted_by' => $user->id,
            'first_aid_score' => $validated['first_aid_score'],
            'communication_score' => $validated['communication_score'],
            'equipment_score' => $validated['equipment_score'],
            'staff_training_score' => $validated['staff_training_score'],
            'emergency_access_score' => $validated['emergency_access_score'],
            'overall_score' => $overallScore,
            'readiness_level' => $readinessLevel,
            'comments' => $validated['comments'],
            'assessment_date' => now(),
        ]);
        
        // Link the feedback to the booking
        $booking->update([
            'emergency_readiness_id' => $readiness->id,
            'feedback_submitted_at' => now(),
        ]);
        
        // TODO: Send notification to organization about new feedback
        
        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback! Your input helps improve trail safety.',
            'readiness' => $readiness,
            'redirect_url' => route('hiker.readiness.show', $readiness)
        ], 201);
    }

    /**
     * Display the feedback
     */
    public function show(EmergencyReadiness $readiness)
    {
        $user = Auth::user();
        
        // Verify the hiker submitted this feedback
        if ($readiness->submitted_by !== $user->id) {
            abort(403, 'You can only view your own feedback.');
        }
        
        $readiness->load(['trail', 'organization', 'submitter']);
        
        return view('hiker.emergency-readiness.show', compact('readiness'));
    }

    /**
     * Display list of hiker's submitted feedback
     */
    public function index()
    {
        $user = Auth::user();
        
        $feedbacks = EmergencyReadiness::with(['trail', 'organization'])
            ->where('submitted_by', $user->id)
            ->orderBy('assessment_date', 'desc')
            ->paginate(15);
        
        return view('hiker.emergency-readiness.index', compact('feedbacks'));
    }

    /**
     * Get bookings eligible for feedback (for notification system)
     */
    public static function getEligibleBookingsForFeedback()
    {
        $cutoffTime = Carbon::now()->subHours(48);
        
        return Booking::with(['trail', 'batch', 'user'])
            ->whereNull('emergency_readiness_id') // No feedback submitted yet
            ->whereNull('feedback_requested_at') // Notification not sent yet
            ->where('status', 'confirmed') // Only confirmed bookings
            ->whereHas('batch', function($query) use ($cutoffTime) {
                $query->where('ends_at', '<=', $cutoffTime); // Hike ended at least 48 hours ago
            })
            ->get();
    }

    /**
     * Determine readiness level based on overall score
     */
    private function determineReadinessLevel(int $score): string
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 75) return 'Good';
        if ($score >= 60) return 'Adequate';
        if ($score >= 40) return 'Needs Improvement';
        return 'Critical';
    }
}
