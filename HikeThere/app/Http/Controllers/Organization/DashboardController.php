<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Trail;
use App\Models\Event;
use App\Models\Booking;
use App\Models\EmergencyReadiness;
use App\Models\SafetyIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the organization dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get total trails
        $totalTrails = Trail::where('user_id', $user->id)->count();
        
        // Get active events
        $activeEvents = Event::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('end_at', '>=', now())
                    ->orWhere('always_available', true);
            })
            ->count();
        
        // Get organization's trail IDs
        $trailIds = Trail::where('user_id', $user->id)->pluck('id');
        
        // Emergency Info Statistics
        $allTrails = Trail::where('user_id', $user->id)->get();
        $trailsWithEmergencyInfo = $allTrails->filter(function($trail) {
            return !empty($trail->emergency_info) && is_array($trail->emergency_info);
        })->count();
        $trailsNeedingEmergencyInfo = $totalTrails - $trailsWithEmergencyInfo;
        
        // Get recent activity (last 30 days)
        $recentActivity = collect();
        
        // Recent trails created
        $recentTrails = Trail::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->map(function($trail) {
                return [
                    'type' => 'trail_created',
                    'icon' => 'trail',
                    'title' => 'New trail created',
                    'description' => "Created trail: {$trail->trail_name}",
                    'timestamp' => $trail->created_at,
                    'color' => 'green',
                ];
            });
        
        // Recent events created
        $recentEvents = Event::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->with('trail')
            ->get()
            ->map(function($event) {
                return [
                    'type' => 'event_created',
                    'icon' => 'calendar',
                    'title' => 'New event created',
                    'description' => "Created event for {$event->trail->trail_name}",
                    'timestamp' => $event->created_at,
                    'color' => 'blue',
                ];
            });
        
        // Recent bookings
        $recentBookings = Booking::whereHas('event', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('created_at', '>=', now()->subDays(30))
            ->with('event.trail')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($booking) {
                return [
                    'type' => 'booking_received',
                    'icon' => 'booking',
                    'title' => 'New booking received',
                    'description' => "Booking for {$booking->event->trail->trail_name}",
                    'timestamp' => $booking->created_at,
                    'color' => 'indigo',
                ];
            });
        
        // Recent emergency readiness feedback
        $recentFeedback = EmergencyReadiness::whereIn('trail_id', $trailIds)
            ->whereNotNull('submitted_by')
            ->where('created_at', '>=', now()->subDays(30))
            ->with('trail')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($assessment) {
                return [
                    'type' => 'emergency_feedback',
                    'icon' => 'shield',
                    'title' => 'Emergency readiness feedback received',
                    'description' => "Feedback for {$assessment->trail->trail_name} - {$assessment->readiness_level}",
                    'timestamp' => $assessment->created_at,
                    'color' => 'red',
                ];
            });
        
        // Recent safety incidents
        $recentIncidents = SafetyIncident::whereIn('trail_id', $trailIds)
            ->whereNotNull('reported_by')
            ->where('created_at', '>=', now()->subDays(30))
            ->with('trail')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($incident) {
                return [
                    'type' => 'safety_incident',
                    'icon' => 'alert',
                    'title' => 'Safety incident reported',
                    'description' => "Incident on {$incident->trail->trail_name} - {$incident->severity}",
                    'timestamp' => $incident->created_at,
                    'color' => 'orange',
                ];
            });
        
        // Account approval activity
        if ($user->approved_at && $user->approved_at->gte(now()->subDays(30))) {
            $approvalActivity = collect([[
                'type' => 'account_approved',
                'icon' => 'check',
                'title' => 'Account approved',
                'description' => 'Your organization account was approved and activated',
                'timestamp' => $user->approved_at,
                'color' => 'green',
            ]]);
            $recentActivity = $recentActivity->merge($approvalActivity);
        }
        
        // Merge all activities and sort by timestamp
        $allActivities = $recentActivity
            ->merge($recentTrails)
            ->merge($recentEvents)
            ->merge($recentBookings)
            ->merge($recentFeedback)
            ->merge($recentIncidents)
            ->sortByDesc('timestamp')
            ->values();
        
        // Manually paginate the collection
        $currentPage = request()->get('activity_page', 1);
        $perPage = 5;
        $offset = ($currentPage - 1) * $perPage;
        
        $recentActivity = new \Illuminate\Pagination\LengthAwarePaginator(
            $allActivities->slice($offset, $perPage)->values(),
            $allActivities->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
                'pageName' => 'activity_page'
            ]
        );
        
        return view('org.dashboard', compact(
            'totalTrails',
            'activeEvents',
            'recentActivity',
            'trailsWithEmergencyInfo',
            'trailsNeedingEmergencyInfo'
        ));
    }
}
