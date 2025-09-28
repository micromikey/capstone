<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrganizationBookingController extends Controller
{
    /**
     * List bookings for trails owned by the authenticated organization.
     */
    public function index(Request $request)
    {
        $orgId = Auth::id();

        $bookings = Booking::whereHas('trail', function($q) use ($orgId) {
            $q->where('user_id', $orgId);
        })->with(['trail', 'user'])
          ->orderBy('created_at', 'desc')
          ->paginate(15);

        return view('org.bookings.index', compact('bookings'));
    }

    /**
     * Show a specific booking; ensure it belongs to this organization.
     */
    public function show(Booking $booking)
    {
        $orgId = Auth::id();

        if (!$booking->trail || $booking->trail->user_id !== $orgId) {
            abort(403);
        }

        $booking->load(['trail', 'user']);
        return view('org.bookings.show', compact('booking'));
    }

    /**
     * Update booking status (e.g., confirm, cancel).
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $orgId = Auth::id();

        if (!$booking->trail || $booking->trail->user_id !== $orgId) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $booking->status = $request->input('status');
        $booking->save();

        // Optionally: Log or dispatch notifications here
        Log::info('Organization updated booking status', ['organization_id' => $orgId, 'booking_id' => $booking->id, 'status' => $booking->status]);

        return redirect()->route('org.bookings.show', $booking)->with('success', 'Booking status updated.');
    }
}
