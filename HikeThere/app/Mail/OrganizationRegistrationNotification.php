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

        // Generate direct approval/rejection URLs
        $this->approveUrl = route('organizations.approve.email', $user->id);
        $this->rejectUrl = route('organizations.reject.email', $user->id);
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