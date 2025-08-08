<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class OrganizationApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $approved;

    public function __construct(User $user, bool $approved)
    {
        $this->user = $user;
        $this->approved = $approved;
    }

    public function build()
    {
        $subject = $this->approved
            ? 'Your Organization Registration has been Approved!'
            : 'Your Organization Registration Status';

        return $this->subject($subject)
            ->view('emails.organization-approval')
            ->with([
                'user' => $this->user,
                'approved' => $this->approved,
                'loginUrl' => route('login')
            ]);
    }
}
