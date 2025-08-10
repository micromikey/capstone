<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class OrganizationStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $approved;
    public $organizationProfile;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, bool $approved)
    {
        $this->user = $user;
        $this->approved = $approved;
        $this->organizationProfile = $user->organizationProfile;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->approved
            ? 'Your Organization Registration has been Approved! 🎉'
            : 'Organization Registration Update';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = $this->approved ? 'emails.organization-approval' : 'emails.organization-rejection';
        
        return new Content(
            view: $view,
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

