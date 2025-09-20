<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Dashboard summary (counts, charts, etc.)
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        return view('admin.dashboard', compact('totalBookings', 'pendingBookings'));
    }

    public function bookings()
    {
        $bookings = Booking::latest()->paginate(10);
        return view('booking.admin-booking', compact('bookings'));
    }

    

    public function showBooking($id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        return redirect()->route('admin.bookings')->with('success', 'Booking status updated.');
    }

    public function reports()
    {
        // Example: get bookings grouped by month
        $reports = Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->get();

        return view('admin.reports.index', compact('reports'));
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }
}
