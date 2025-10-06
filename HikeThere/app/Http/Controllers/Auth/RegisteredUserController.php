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
        // Log the incoming request data for debugging
        Log::info('Organization registration attempt', [
            'request_data' => $request->except(['password', 'password_confirmation']),
            'files' => $request->allFiles(),
            'has_business_permit' => $request->hasFile('business_permit'),
            'has_government_id' => $request->hasFile('government_id'),
            'terms_accepted' => $request->has('terms'),
            'documentation_confirmed' => $request->has('documentation_confirm'),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'organization_name' => 'required|string|max:255',
            'organization_description' => 'nullable|string|max:1000',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'business_permit' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|mimetypes:application/pdf,image/jpeg,image/jpg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
            'government_id' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|mimetypes:application/pdf,image/jpeg,image/jpg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
            'additional_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|mimetypes:application/pdf,image/jpeg,image/jpg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
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

            // Handle file uploads
            $businessPermitPath = $request->file('business_permit')->store('organization_documents', $disk);
            $governmentIdPath = $request->file('government_id')->store('organization_documents', $disk);
            $additionalDocs = [];

            if ($request->hasFile('additional_documents')) {
                foreach ($request->file('additional_documents') as $file) {
                    $additionalDocs[] = $file->store('organization_documents', $disk);
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
            
            // Log the email attempt for debugging
            Log::info('Attempting to send approval email', [
                'admin_email' => $adminEmail,
                'user_id' => $user->id,
                'organization_name' => $user->organization_name,
                'mail_config' => [
                    'default' => config('mail.default'),
                    'from_address' => config('mail.from.address'),
                    'from_name' => config('mail.from.name'),
                ],
            ]);
            
            try {
                // Create the mail instance
                $mailInstance = new \App\Mail\OrganizationApprovalNotification($user);
                Log::info('Mail instance created successfully', [
                    'mail_class' => get_class($mailInstance),
                    'user_data' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'organization_name' => $user->organization_name,
                    ],
                ]);
                
                // Send the email
                Mail::to($adminEmail)->send($mailInstance);
                Log::info('Approval email sent successfully', [
                    'admin_email' => $adminEmail,
                    'user_id' => $user->id,
                ]);
            } catch (\Exception $emailException) {
                Log::error('Failed to send approval email', [
                    'admin_email' => $adminEmail,
                    'user_id' => $user->id,
                    'error' => $emailException->getMessage(),
                    'error_trace' => $emailException->getTraceAsString(),
                ]);
                // Don't fail the registration if email fails, just log it
            }

            // Log the redirect attempt
            Log::info('Redirecting to pending approval page', [
                'route' => 'auth.pending-approval',
                'user_id' => $user->id,
            ]);
            
            return redirect()->route('auth.pending-approval')
                ->with('success', 'Organization registration submitted successfully! An approval email has been sent. Please check your email and click the approval link to activate your account.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Organization registration failed: ' . $e->getMessage(), [
                'request_data' => $request->except(['password', 'password_confirmation']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Provide more specific error messages
            $errorMessage = 'Registration failed. Please try again.';
            if (str_contains($e->getMessage(), 'SQLSTATE')) {
                $errorMessage = 'Database error occurred. Please check your information and try again.';
            } elseif (str_contains($e->getMessage(), 'file')) {
                $errorMessage = 'File upload error. Please ensure your documents are valid and try again.';
            }
            
            // Log the specific error for debugging
            Log::error('Specific error details', [
                'error_type' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
            ]);
            
            return back()->withErrors(['error' => $errorMessage])->withInput();
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

        // Redirect to guest email verification notice
        return redirect()->route('verification.notice.guest')
            ->with('success', 'Account created successfully! Please check your email to verify your account before logging in.');
    }
}