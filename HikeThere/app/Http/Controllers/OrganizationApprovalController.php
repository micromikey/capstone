<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class OrganizationApprovalController extends Controller
{
    public function approve(User $user)
    {
        $user->update(['approved_at' => now()]);
        return back()->with('success', 'Organization approved successfully.');
    }

    public function reject(User $user)
    {
        $user->delete();
        return back()->with('success', 'Organization rejected successfully.');
    }
}