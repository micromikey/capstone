<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OrganizationProfile;
use App\Mail\OrganizationApprovalNotification;
use Illuminate\Support\Facades\Mail;

class OrganizationApprovalController extends Controller
{
    public function approve(Request $request, User $user)
    {
        // Verify the signed URL
        if (!$request->hasValidSignature()) {
            abort(401, 'Invalid or expired approval link');
        }

        // Update user approval status
        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now()
        ]);

        // Send approval notification to the organization
        Mail::to($user->email)
            ->send(new OrganizationApprovalNotification($user, true));

        return view('admin.approval-success', [
            'action' => 'approved',
            'organization' => $user->organizationProfile->organization_name ?? $user->name
        ]);
    }

    public function reject(Request $request, User $user)
    {
        // Verify the signed URL
        if (!$request->hasValidSignature()) {
            abort(401, 'Invalid or expired approval link');
        }

        // Send rejection notification to the organization before deleting
        Mail::to($user->email)
            ->send(new OrganizationApprovalNotification($user, false));

        // Update status to rejected (or delete if preferred)
        $user->update(['approval_status' => 'rejected']);
        
        // Optional: Delete the user and related data
        // $user->organizationProfile()->delete();
        // $user->delete();

        return view('admin.approval-success', [
            'action' => 'rejected',
            'organization' => $user->organizationProfile->organization_name ?? $user->name
        ]);
    }
}