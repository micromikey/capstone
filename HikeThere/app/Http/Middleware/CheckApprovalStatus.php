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
            
            // This middleware is specifically for organizations
            // It should NOT allow hikers to pass through
            if ($user->user_type !== 'organization') {
                abort(403, 'Unauthorized access.');
            }
            
            // Check organization approval status
            if ($user->approval_status === 'pending') {
                Auth::logout();
                return redirect()->route('auth.pending-approval')
                    ->with('error', 'Your organization registration is pending approval. Please check your email for the approval link.');
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
            
            // If somehow approval_status is null or other value, deny access
            abort(403, 'Your organization account status is invalid.');
        }

        // If not authenticated, redirect to login
        return redirect()->route('login');
    }
}