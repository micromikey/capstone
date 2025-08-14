<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\OrganizationProfile;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        if ($user->user_type === 'organization') {
            $organizationProfile = $user->organizationProfile;
            return view('profile.organization-show', compact('user', 'organizationProfile'));
        }
        
        // Load assessment results and itineraries for hikers
        $user->load(['latestAssessmentResult', 'latestItinerary']);
        
        return view('profile.hiker-show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        
        if ($user->user_type === 'organization') {
            $organizationProfile = $user->organizationProfile;
            return view('profile.organization-edit', compact('user', 'organizationProfile'));
        }
        
        return view('profile.hiker-edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        if ($user->user_type === 'organization') {
            return $this->updateOrganizationProfile($request, $user);
        }
        
        return $this->updateHikerProfile($request, $user);
    }

    private function updateHikerProfile(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'hiking_preferences' => 'nullable|array',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'bio', 'location', 'birth_date', 'gender',
            'hiking_preferences', 'emergency_contact_name', 'emergency_contact_phone',
            'emergency_contact_relationship'
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $data['profile_picture'] = $path;
        }

        $user->update($data);
        return redirect()->route('custom.profile.show')->with('success', 'Profile updated successfully!');
    }

    private function updateOrganizationProfile(Request $request, User $user)
    {
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'organization_description' => 'required|string|max:1000',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'mission_statement' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $userData = [
            'organization_name' => $request->organization_name,
            'email' => $request->email,
        ];

        $organizationData = $request->only([
            'organization_description', 'phone', 'website', 'mission_statement'
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $userData['profile_picture'] = $path;
        }

        $user->update($userData);
        
        if ($user->organizationProfile) {
            $user->organizationProfile->update($organizationData);
        } else {
            $organizationData['user_id'] = $user->id;
            OrganizationProfile::create($organizationData);
        }

        return redirect()->route('custom.profile.show')->with('success', 'Organization profile updated successfully!');
    }

    public function deleteProfilePicture()
    {
        $user = Auth::user();
        
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }

        return back()->with('success', 'Profile picture removed successfully!');
    }
}
