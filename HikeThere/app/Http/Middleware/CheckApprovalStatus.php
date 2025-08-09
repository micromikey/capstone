<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApprovalStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->user_type === 'organization') {
            if (Auth::user()->approval_status === 'pending') {
                Auth::logout();
                return redirect()->route('auth.pending-approval');
            }

            if (Auth::user()->approval_status === 'rejected') {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your organization registration was rejected. Please contact support for more information.');
            }
        }

        return $next($request);
    }
}