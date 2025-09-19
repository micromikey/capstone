<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    /**
     * Show the hiking preferences onboarding form.
     */
    public function showPreferences(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Render a simple preferences form. The view will handle empty preferences.
        return view('onboard.preferences', ['user' => $user]);
    }

    /**
     * Save the hiking preferences submitted by the user and mark onboarding complete.
     */
    public function savePreferences(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'hiking_preferences' => 'array',
            'hiking_preferences.*' => 'string|max:255',
        ]);

        try {
            $user->hiking_preferences = $data['hiking_preferences'] ?? [];
            $user->preferences_onboarded_at = now();
            $user->save();
            Log::info('[OnboardingController] saved hiking preferences', ['user_id' => $user->id]);
        } catch (\Throwable $e) {
            Log::error('[OnboardingController] failed to save hiking preferences', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['hiking_preferences' => 'Failed to save preferences. Please try again.']);
        }

        // After saving, redirect to intended / dashboard or home
        return redirect()->intended(route('dashboard'));
    }
}
