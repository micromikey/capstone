<?php

namespace App\Http\Controllers\AccountSettings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPreference;

class PreferencesController extends Controller
{
    /**
     * Display the user's preferences
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user preferences or use defaults if not set
        if ($user->preferences) {
            $preferences = $user->preferences->toArray();
        } else {
            $preferences = UserPreference::getDefaults();
        }
        
        return view('account.preferences', compact('preferences'));
    }

    /**
     * Update user preferences
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'email_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'trail_updates' => 'nullable|boolean',
            'security_alerts' => 'nullable|boolean',
            'newsletter' => 'nullable|boolean',
            'profile_visibility' => 'required|in:public,private',
            'show_email' => 'nullable|boolean',
            'show_phone' => 'nullable|boolean',
            'show_location' => 'nullable|boolean',
            'show_birth_date' => 'nullable|boolean',
            'show_hiking_preferences' => 'nullable|boolean',
            'two_factor_required' => 'nullable|boolean',
        ]);

        // Handle checkboxes - unchecked boxes don't send values
        $preferences = [
            'email_notifications' => $request->has('email_notifications'),
            'push_notifications' => $request->has('push_notifications'),
            'trail_updates' => $request->has('trail_updates'),
            'security_alerts' => $request->has('security_alerts'),
            'newsletter' => $request->has('newsletter'),
            'profile_visibility' => $validated['profile_visibility'],
            'show_email' => $request->has('show_email'),
            'show_phone' => $request->has('show_phone'),
            'show_location' => $request->has('show_location'),
            'show_birth_date' => $request->has('show_birth_date'),
            'show_hiking_preferences' => $request->has('show_hiking_preferences'),
            'two_factor_required' => $request->has('two_factor_required'),
        ];

        // Update or create preferences
        UserPreference::updatePreferences($user->id, $preferences);

        return back()->with('success', 'Preferences updated successfully.');
    }

    /**
     * Reset preferences to defaults
     */
    public function reset()
    {
        $user = Auth::user();
        
        // Delete existing preferences to use defaults
        $user->preferences()->delete();

        return back()->with('success', 'Preferences reset to defaults.');
    }

    /**
     * Export user data (GDPR compliance)
     */
    public function export()
    {
        $user = Auth::user();
        
        // Prepare user data for export
        $exportData = [
            'user_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'last_login' => $user->last_login_at ?? 'Never',
            ],
            'profile' => [
                'phone' => $user->phone,
                'bio' => $user->bio,
                'location' => $user->location,
                'birth_date' => $user->birth_date?->format('Y-m-d') ?? null,
                'gender' => $user->gender,
                'hiking_preferences' => $user->hiking_preferences,
            ],
            'preferences' => $user->preferences ? $user->preferences->toArray() : UserPreference::getDefaults(),
        ];

        // Return JSON response for download
        return response()->json($exportData)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="user-data-' . $user->id . '.json"');
    }
}
