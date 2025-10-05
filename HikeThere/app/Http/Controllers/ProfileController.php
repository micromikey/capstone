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
        \Illuminate\Support\Facades\Log::debug('[ProfileController@updateHikerProfile] invoked', [
            'route' => $request->route()?->getName(),
            'method' => $request->method(),
            'expectsJson' => $request->expectsJson(),
            'isAjax' => $request->ajax(),
            'hasFile_profile_picture' => $request->hasFile('profile_picture'),
            'has_name' => $request->has('name'),
            'has_email' => $request->has('email'),
            'content_type' => $request->header('Content-Type')
        ]);
        // If only a profile picture is being uploaded (no name/email provided),
        // validate and handle that as a quick partial update so the JS in-place
        // upload flow can work without providing all required profile fields.
        $onlyPicture = $request->hasFile('profile_picture') && !$request->has('name') && !$request->has('email');

        if ($onlyPicture) {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Determine which disk to use with safety check
            $disk = config('filesystems.default', 'public');
            if ($disk === 'gcs') {
                try {
                    if (!config('filesystems.disks.gcs.bucket')) {
                        $disk = 'public';
                        \Log::warning('GCS configured but bucket not set, using public disk');
                    }
                } catch (\Exception $e) {
                    $disk = 'public';
                    \Log::error('GCS configuration error: ' . $e->getMessage());
                }
            }

            if ($user->profile_picture) {
                Storage::disk($disk)->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile-pictures', $disk);
            $user->update(['profile_picture' => $path]);

            if ($disk === 'gcs') {
                $bucket = config('filesystems.disks.gcs.bucket');
                $url = "https://storage.googleapis.com/{$bucket}/{$path}";
            } else {
                $url = Storage::disk('public')->url($path);
            }
            
            // If it's an AJAX request, return JSON for the client to update in-place
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'profile_picture_url' => $url,
                    'cache_bust' => time(),
                ]);
            }

            return redirect()->route('custom.profile.show')->with('success', 'Profile picture updated successfully!');
        }

        // Full profile update (name/email required)
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
            // Determine which disk to use with safety check
            $disk = config('filesystems.default', 'public');
            if ($disk === 'gcs') {
                try {
                    if (!config('filesystems.disks.gcs.bucket')) {
                        $disk = 'public';
                        \Log::warning('GCS configured but bucket not set, using public disk');
                    }
                } catch (\Exception $e) {
                    $disk = 'public';
                    \Log::error('GCS configuration error: ' . $e->getMessage());
                }
            }
            
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk($disk)->delete($user->profile_picture);
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', $disk);
            $data['profile_picture'] = $path;
        }

        $user->update($data);
        return redirect()->route('custom.profile.show')->with('success', 'Profile updated successfully!');
    }

    private function updateOrganizationProfile(Request $request, User $user)
    {
        \Illuminate\Support\Facades\Log::debug('[ProfileController@updateOrganizationProfile] invoked', [
            'route' => $request->route()?->getName(),
            'method' => $request->method(),
            'expectsJson' => $request->expectsJson(),
            'isAjax' => $request->ajax(),
            'hasFile_profile_picture' => $request->hasFile('profile_picture'),
            'has_organization_name' => $request->has('organization_name'),
            'has_email' => $request->has('email'),
            'content_type' => $request->header('Content-Type')
        ]);

        // If only a profile picture is being uploaded (no organization_name/email provided),
        // validate and handle that as a quick partial update so the JS in-place
        // upload flow can work without providing all required profile fields.
        $onlyPicture = $request->hasFile('profile_picture') && !$request->has('organization_name') && !$request->has('email');

        if ($onlyPicture) {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Determine which disk to use with safety check
            $disk = config('filesystems.default', 'public');
            if ($disk === 'gcs') {
                try {
                    if (!config('filesystems.disks.gcs.bucket')) {
                        $disk = 'public';
                        \Log::warning('GCS configured but bucket not set, using public disk');
                    }
                } catch (\Exception $e) {
                    $disk = 'public';
                    \Log::error('GCS configuration error: ' . $e->getMessage());
                }
            }
            
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk($disk)->delete($user->profile_picture);
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', $disk);
            $user->update(['profile_picture' => $path]);

            // Get the public URL
            if ($disk === 'gcs') {
                $bucket = config('filesystems.disks.gcs.bucket');
                $url = "https://storage.googleapis.com/{$bucket}/{$path}";
            } else {
                $url = Storage::disk('public')->url($path);
            }
                
            // If it's an AJAX request, return JSON for the client to update in-place
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'profile_picture_url' => $url,
                    'cache_bust' => time(),
                ]);
            }

            return redirect()->route('custom.profile.show')->with('success', 'Profile picture updated successfully!');
        }

        // Full profile update (organization_name/email required)
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
            // Determine which disk to use with safety check
            $disk = config('filesystems.default', 'public');
            if ($disk === 'gcs') {
                try {
                    if (!config('filesystems.disks.gcs.bucket')) {
                        $disk = 'public';
                        \Log::warning('GCS configured but bucket not set, using public disk');
                    }
                } catch (\Exception $e) {
                    $disk = 'public';
                    \Log::error('GCS configuration error: ' . $e->getMessage());
                }
            }
            
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk($disk)->delete($user->profile_picture);
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', $disk);
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
            // Determine which disk to use with safety check
            $disk = config('filesystems.default', 'public');
            if ($disk === 'gcs') {
                try {
                    if (!config('filesystems.disks.gcs.bucket')) {
                        $disk = 'public';
                        \Log::warning('GCS configured but bucket not set, using public disk');
                    }
                } catch (\Exception $e) {
                    $disk = 'public';
                    \Log::error('GCS configuration error: ' . $e->getMessage());
                }
            }
            
            Storage::disk($disk)->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }

        return back()->with('success', 'Profile picture removed successfully!');
    }

    /**
     * AJAX endpoint specifically for profile picture uploads.
     * Returns JSON with the public URL when successful so the client can update in-place.
     */
    public function uploadProfilePicture(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Determine which disk to use with safety check
        $disk = config('filesystems.default', 'public');
        if ($disk === 'gcs') {
            try {
                if (!config('filesystems.disks.gcs.bucket')) {
                    $disk = 'public';
                    \Log::warning('GCS configured but bucket not set, using public disk');
                }
            } catch (\Exception $e) {
                $disk = 'public';
                \Log::error('GCS configuration error: ' . $e->getMessage());
            }
        }
        
        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk($disk)->delete($user->profile_picture);
        }

        // Store new profile picture
        $path = $request->file('profile_picture')->store('profile-pictures', $disk);
        $user->update(['profile_picture' => $path]);

        // Get the public URL
        if ($disk === 'gcs') {
            $bucket = config('filesystems.disks.gcs.bucket');
            $url = "https://storage.googleapis.com/{$bucket}/{$path}";
        } else {
            $url = Storage::disk('public')->url($path);
        }

        return response()->json([ 'profile_picture_url' => $url, 'cache_bust' => time() ]);
    }
}
