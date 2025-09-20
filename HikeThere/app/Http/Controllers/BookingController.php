<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    // Show the booking form
    public function create()
    {
        return view('booking.booking-details');
    }

    // Store booking info
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:255',
            'payment_method' => 'required|string',
            'terms_accepted' => 'required|accepted',
        ]);

        // TODO: Save booking to database here

        // Redirect to confirmation page
        return redirect()->route('bookings.confirmation');
    }

    // Show confirmation page
    public function confirmation()
    {
        return view('booking.confirmation'); // create this Blade file
    }
}
