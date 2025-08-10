<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApprovalStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user is an organization
            if ($user->user_type === 'organization') {
                if ($user->approval_status === 'pending') {
                    Auth::logout();
                    return redirect()->route('auth.pending-approval')
                        ->with('error', 'Your organization registration is pending approval. Please wait for admin review.');
                }

                if ($user->approval_status === 'rejected') {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Your organization registration was rejected. Please contact support for more information.');
                }
                
                // If organization is approved, they can proceed
                if ($user->approval_status === 'approved') {
                    return $next($request);
                }
            }
            
            // For hikers, they need email verification (handled by 'verified' middleware)
            if ($user->user_type === 'hiker') {
                return $next($request);
            }
        }

        return $next($request);
    }
}