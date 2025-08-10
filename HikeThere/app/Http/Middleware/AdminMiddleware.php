<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user is admin (you can modify this logic based on your admin identification)
        // For now, we'll check if the user is the first user or has a specific admin flag
        if ($user->id === 1 || $user->email === config('mail.admin_email')) {
            return $next($request);
        }

        // If not admin, redirect with error
        return redirect()->back()->with('error', 'Access denied. Admin privileges required.');
    }
}


