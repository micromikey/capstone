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

        $query = Booking::whereHas('trail', function($q) use ($orgId) {
            $q->where('user_id', $orgId);
        })->with(['trail', 'user', 'payment']);

        // Filters
        if ($request->filled('mountain')) {
            $query->whereHas('trail', function($q) use ($request) {
                $q->where('mountain_name', 'like', '%' . $request->mountain . '%');
            });
        }

        if ($request->filled('price_min') || $request->filled('price_max')) {
            if ($request->filled('price_min')) {
                $query->where('price_cents', '>=', $request->price_min * 100);
            }
            if ($request->filled('price_max')) {
                $query->where('price_cents', '<=', $request->price_max * 100);
            }
        }

        if ($request->filled('party_min') || $request->filled('party_max')) {
            if ($request->filled('party_min')) {
                $query->where('party_size', '>=', $request->party_min);
            }
            if ($request->filled('party_max')) {
                $query->where('party_size', '<=', $request->party_max);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'date_booked':
                $query->orderBy('created_at', $sortOrder);
                break;
            case 'popularity':
                $query->join('trails', 'bookings.trail_id', '=', 'trails.id')
                    ->leftJoin('bookings as b2', 'trails.id', '=', 'b2.trail_id')
                    ->groupBy('bookings.id')
                    ->orderByRaw('COUNT(b2.id) ' . $sortOrder)
                    ->select('bookings.*');
                break;
            case 'paid':
                $query->leftJoin('payments', 'bookings.id', '=', 'payments.booking_id')
                    ->orderByRaw("CASE WHEN payments.payment_status = 'paid' THEN 0 ELSE 1 END " . ($sortOrder === 'asc' ? 'ASC' : 'DESC'))
                    ->select('bookings.*');
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $bookings = $query->paginate(15)->appends($request->query());

        // Calculate statistics
        $totalRevenue = Booking::whereHas('trail', function($q) use ($orgId) {
            $q->where('user_id', $orgId);
        })->whereHas('payment', function($q) {
            $q->where('payment_status', 'paid');
        })->sum('price_cents');

        $paidBookings = Booking::whereHas('trail', function($q) use ($orgId) {
            $q->where('user_id', $orgId);
        })->whereHas('payment', function($q) {
            $q->where('payment_status', 'paid');
        })->count();

        // Get unique mountains for filter dropdown
        $mountains = Booking::whereHas('trail', function($q) use ($orgId) {
            $q->where('user_id', $orgId);
        })->join('trails', 'bookings.trail_id', '=', 'trails.id')
            ->select('trails.mountain_name')
            ->distinct()
            ->whereNotNull('trails.mountain_name')
            ->pluck('trails.mountain_name');

        return view('org.bookings.index', compact('bookings', 'totalRevenue', 'paidBookings', 'mountains'));
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

        $booking->load(['trail', 'user', 'payment']);
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
