<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HikerProfileController extends Controller
{
    /**
     * Display the hiker's profile for the organization
     * Only accessible for hikers with confirmed bookings
     */
    public function show($hikerId)
    {
        $organization = Auth::user();
        
        // Get the hiker
        $hiker = User::where('id', $hikerId)
            ->where('user_type', 'hiker')
            ->firstOrFail();
        
        // Verify that this hiker has a confirmed booking with the organization
        $booking = Booking::where('user_id', $hiker->id)
            ->whereHas('trail', function($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->where('payment_status', 'paid')
            ->whereIn('status', ['confirmed', 'completed'])
            ->latest()
            ->first();
        
        // If no confirmed/paid booking exists, deny access
        if (!$booking) {
            abort(403, 'Unauthorized access. This hiker has not booked any of your trails or payment is not confirmed.');
        }
        
        // Get the hiker's latest assessment result
        $latestAssessment = $hiker->latestAssessmentResult;
        
        // Get the hiker's latest itinerary
        $latestItinerary = $hiker->latestItinerary;
        
        return view('org.community.hiker-profile', compact(
            'hiker',
            'booking',
            'latestAssessment',
            'latestItinerary'
        ));
    }
}
