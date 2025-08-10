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
use Illuminate\Support\Facades\DB;
use App\Mail\OrganizationApprovalNotification;

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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'organization_name' => 'required|string|max:255',
            'organization_description' => 'nullable|string|max:1000',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'business_permit' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'government_id' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'additional_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'terms' => 'required|accepted',
            'documentation_confirm' => 'required|accepted',
        ]);

        DB::beginTransaction();

        try {
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'organization',
                'organization_name' => $request->organization_name,
                'organization_description' => $request->organization_description,
                'approval_status' => 'pending',
                // Don't set email_verified_at - organizations need approval first
            ]);

            // Handle file uploads
            $businessPermitPath = $request->file('business_permit')->store('organization_documents', 'public');
            $governmentIdPath = $request->file('government_id')->store('organization_documents', 'public');
            $additionalDocs = [];

            if ($request->hasFile('additional_documents')) {
                foreach ($request->file('additional_documents') as $file) {
                    $additionalDocs[] = $file->store('organization_documents', 'public');
                }
            }

            // Create organization profile
            $user->organizationProfile()->create([
                'organization_name' => $request->organization_name,
                'organization_description' => $request->organization_description,
                'email' => $request->email,
                'phone' => $request->phone,
                'name' => $request->name,
                'address' => $request->address,
                'password' => Hash::make($request->password), // Store password in profile as per migration
                'business_permit_path' => $businessPermitPath,
                'government_id_path' => $governmentIdPath,
                'additional_docs' => $additionalDocs,
            ]);

            DB::commit();

            // Send approval notification email to admin
            $adminEmail = config('mail.admin_email', 'admin@hikethere.com');
            Mail::to($adminEmail)->send(new OrganizationApprovalNotification($user));

            return redirect()->route('auth.pending-approval')
                ->with('success', 'Organization registration submitted successfully! Your application is now pending admin approval. You will receive an email notification once your account has been reviewed.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Organization registration failed: ' . $e->getMessage(), [
                'request_data' => $request->except(['password', 'password_confirmation']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
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
            // Don't set email_verified_at - let email verification handle this
        ]);

        // Fire Registered event for hikers to trigger email verification
        event(new Registered($user));

        // Don't auto-login hikers - they need to verify email first
        // Auth::login($user);

        // Redirect to email verification notice
        return redirect()->route('verification.notice')
            ->with('success', 'Account created successfully! Please check your email to verify your account before logging in.');
    }
}