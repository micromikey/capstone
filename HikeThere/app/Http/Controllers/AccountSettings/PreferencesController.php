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
        $preferences = $user->preferences ?? UserPreference::getDefaults();
        
        return view('account.preferences', compact('preferences'));
    }

    /**
     * Update user preferences
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'trail_updates' => 'boolean',
            'security_alerts' => 'boolean',
            'newsletter' => 'boolean',
            'profile_visibility' => 'in:public,friends,private',
            'show_email' => 'boolean',
            'show_phone' => 'boolean',
            'show_location' => 'boolean',
            'show_birth_date' => 'boolean',
            'show_hiking_preferences' => 'boolean',
            'two_factor_required' => 'boolean',
            'timezone' => 'string|max:50',
            'language' => 'string|max:10',
        ]);

        // Update or create preferences
        UserPreference::updatePreferences($user->id, $validated);

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
