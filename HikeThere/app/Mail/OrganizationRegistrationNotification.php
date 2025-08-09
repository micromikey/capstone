<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class OrganizationRegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $organizationProfile;
    public $approveUrl;
    public $rejectUrl;

    public function __construct(User $user, $organizationProfile)
    {
        $this->user = $user;
        $this->organizationProfile = $organizationProfile;

        // Generate signed URLs for approve/reject actions
        $this->approveUrl = URL::temporarySignedRoute(
            'admin.organization.approve',
            now()->addDays(30), // Link expires in 30 days
            ['user' => $user->id]
        );

        $this->rejectUrl = URL::temporarySignedRoute(
            'admin.organization.reject',
            now()->addDays(30),
            ['user' => $user->id]
        );
    }

    public function build()
    {
        return $this->subject('New Organization Registration - ' . $this->organizationProfile->organization_name)
            ->view('emails.organization-registration-admin')
            ->with([
                'user' => $this->user,
                'profile' => $this->organizationProfile,
                'approveUrl' => $this->approveUrl,
                'rejectUrl' => $this->rejectUrl,
            ]);
    }
}