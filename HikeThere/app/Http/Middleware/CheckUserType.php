<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $userType
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $userType)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user type matches the required type
        if ($user->user_type !== $userType) {
            // Redirect based on user type
            if ($user->user_type === 'organization') {
                if ($user->approval_status === 'approved') {
                    return redirect()->route('org.dashboard')
                        ->with('error', 'This area is for hikers only. Please use your organization dashboard.');
                } else {
                    return redirect()->route('auth.pending-approval')
                        ->with('error', 'Your organization is not yet approved.');
                }
            } else {
                return redirect()->route('dashboard')
                    ->with('error', 'This area is for organizations only. Please use your hiker dashboard.');
            }
        }

        return $next($request);
    }
}
