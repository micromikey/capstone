<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->user_type === 'organization') {
            return view('account.organization-settings', compact('user'));
        }
        
        return view('account.hiker-settings', compact('user'));
    }

    /**
     * Update the user's fitness level
     */
    public function updateFitnessLevel(Request $request)
    {
        $request->validate([
            'fitness_level' => 'required|in:beginner,intermediate,advanced',
        ]);

        $user = Auth::user();
        $user->fitness_level = $request->fitness_level;
        $user->save();

        return redirect()->back()->with('fitness_updated', 'Your fitness level has been updated successfully! This will affect your future itinerary recommendations.');
    }
}
