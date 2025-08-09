<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrganizationProfile;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use App\Mail\OrganizationRegistrationNotification;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function createOrganization()
    {
        return view('auth.register-organization');
    }

    public function storeOrganization(Request $request)
    {
        $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'organization_description' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'business_permit' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'government_id' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'additional_docs.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'terms' => ['required', 'accepted'],
            'documentation_confirm' => ['required', 'accepted'],
        ]);

        // Create user - organizations don't need email verification
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'organization',
            'approval_status' => 'pending',
            'email_verified_at' => now(), // Skip email verification for organizations
        ]);

        // Handle file uploads
        $businessPermitPath = $request->file('business_permit')->store('organization-documents', 'public');
        $governmentIdPath = $request->file('government_id')->store('organization-documents', 'public');

        $additionalDocs = [];
        if ($request->hasFile('additional_docs')) {
            foreach ($request->file('additional_docs') as $file) {
                $additionalDocs[] = $file->store('organization-documents', 'public');
            }
        }

        // Create organization profile
        $organizationProfile = OrganizationProfile::create([
            'user_id' => $user->id,
            'organization_name' => $request->organization_name,
            'organization_description' => $request->organization_description,
            'email' => $request->email,
            'phone' => $request->phone,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'business_permit_path' => $businessPermitPath,
            'government_id_path' => $governmentIdPath,
            'additional_docs' => json_encode($additionalDocs),
        ]);

        // Don't fire Registered event for organizations (prevents email verification)
        // event(new Registered($user));

        // Send notification to admin email with error handling
        try {
            Mail::to(config('mail.admin_email', 'johnmichaeltorres.stud@gmail.com'))
                ->send(new OrganizationRegistrationNotification($user, $organizationProfile));
        } catch (\Exception $e) {
            // Log the error but don't block the registration process
            Log::error('Failed to send organization registration email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'organization' => $request->organization_name
            ]);
            
            // Still redirect successfully - the registration worked, just email failed
            return redirect()->route('auth.pending-approval')
                ->with('warning', 'Registration submitted successfully! However, there was an issue sending the notification email. Please contact support if you don\'t hear back within 24 hours.');
        }

        // Redirect to pending approval page
        return redirect()->route('auth.pending-approval')
            ->with('success', 'Registration submitted successfully! Please check your email for updates.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'hiker',
            'approval_status' => 'approved', // Hikers are auto-approved
            'email_verified_at' => now(), // Auto-verify hikers
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('home');
    }
}