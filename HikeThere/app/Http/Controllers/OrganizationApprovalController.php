<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrganizationApprovalNotification;
use App\Mail\OrganizationStatusUpdate;
use Illuminate\Support\Facades\URL;

class OrganizationApprovalController extends Controller
{
    /**
     * Approve an organization directly from email link
     */
    public function approveFromEmail(Request $request, User $user)
    {
        // Verify the signed URL
        if (!URL::hasValidSignature($request)) {
            abort(403, 'Invalid approval link.');
        }

        // Verify this is an organization user
        if ($user->user_type !== 'organization') {
            return redirect()->route('login')
                ->with('error', 'Only organization users can be approved.');
        }

        // Check if already approved
        if ($user->approval_status === 'approved') {
            return redirect()->route('login')
                ->with('info', 'This organization is already approved. You can now log in.');
        }

        // Check if already rejected
        if ($user->approval_status === 'rejected') {
            return redirect()->route('login')
                ->with('error', 'This organization was rejected and cannot be approved.');
        }

        // Update approval status
        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
        ]);

        // Send approval notification email to the organization
        Mail::to($user->email)->send(new OrganizationStatusUpdate($user, true));

        return redirect()->route('login')
            ->with('success', 'Organization approved successfully! You can now log in to your account.');
    }

    /**
     * Reject an organization directly from email link
     */
    public function rejectFromEmail(Request $request, User $user)
    {
        // Verify the signed URL
        if (!URL::hasValidSignature($request)) {
            abort(403, 'Invalid rejection link.');
        }

        // Verify this is an organization user
        if ($user->user_type !== 'organization') {
            return redirect()->route('login')
                ->with('error', 'Only organization users can be rejected.');
        }

        // Check if already rejected
        if ($user->approval_status === 'rejected') {
            return redirect()->route('login')
                ->with('info', 'This organization was already rejected.');
        }

        // Check if already approved
        if ($user->approval_status === 'approved') {
            return redirect()->route('login')
                ->with('error', 'This organization is already approved and cannot be rejected.');
        }

        // Update approval status
        $user->update([
            'approval_status' => 'rejected',
            'approved_at' => null,
        ]);

        // Send rejection notification email to the organization
        Mail::to($user->email)->send(new OrganizationStatusUpdate($user, false));

        return redirect()->route('login')
            ->with('info', 'Organization rejected. A notification has been sent to the organization.');
    }
}