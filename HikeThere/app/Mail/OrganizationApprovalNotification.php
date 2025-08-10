<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class OrganizationApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $organizationProfile;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->organizationProfile = $user->organizationProfile;
        
        // Log the mail construction for debugging
        \Log::info('OrganizationApprovalNotification mail constructed', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'organization_name' => $user->organization_name,
            'has_organization_profile' => $this->organizationProfile ? 'yes' : 'no',
            'organization_profile_data' => $this->organizationProfile ? [
                'additional_docs' => $this->organizationProfile->additional_docs,
                'additional_docs_type' => gettype($this->organizationProfile->additional_docs),
                'business_permit_path' => $this->organizationProfile->business_permit_path,
                'government_id_path' => $this->organizationProfile->government_id_path,
            ] : 'no profile',
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.from.address', 'noreply@hikethere.com'),
                config('mail.from.name', 'HikeThere System')
            ),
            subject: 'New Organization Registration Requires Approval',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.organization-approval-admin',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}