<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrganizationApprovalNotification;
use App\Mail\OrganizationStatusUpdate;

class OrganizationApprovalController extends Controller
{
    /**
     * Approve an organization
     */
    public function approve(User $user)
    {
        // Verify this is an organization user
        if ($user->user_type !== 'organization') {
            return back()->with('error', 'Only organization users can be approved.');
        }

        // Update approval status
        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
        ]);

        // Send approval notification email to the organization
        Mail::to($user->email)->send(new OrganizationStatusUpdate($user, true));

        return back()->with('success', 'Organization approved successfully! An approval notification has been sent to ' . $user->email);
    }

    /**
     * Reject an organization
     */
    public function reject(User $user)
    {
        // Verify this is an organization user
        if ($user->user_type !== 'organization') {
            return back()->with('error', 'Only organization users can be rejected.');
        }

        // Update approval status
        $user->update([
            'approval_status' => 'rejected',
            'approved_at' => null,
        ]);

        // Send rejection notification email to the organization
        Mail::to($user->email)->send(new OrganizationStatusUpdate($user, false));

        return back()->with('success', 'Organization rejected successfully! A rejection notification has been sent to ' . $user->email);
    }
}