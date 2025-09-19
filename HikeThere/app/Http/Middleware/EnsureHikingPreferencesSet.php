<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureHikingPreferencesSet
{
    /**
     * Handle an incoming request.
     * If the authenticated user is a hiker and has no hiking_preferences saved,
     * redirect them to the profile edit page so they can set preferences.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        Log::debug('[EnsureHikingPreferencesSet] middleware invoked', [
            'route' => $request->route()?->getName(),
            'method' => $request->method(),
            'is_ajax' => $request->expectsJson() || $request->isXmlHttpRequest(),
            'user_id' => $user?->id ?? null,
            'user_type' => $user?->user_type ?? null,
        ]);

        // Only run for authenticated users on web routes
        if (!$user) {
            return $next($request);
        }

        // Only enforce for hikers
        if ($user->user_type !== 'hiker') {
            Log::debug('[EnsureHikingPreferencesSet] skipping - not a hiker', ['user_id' => $user->id ?? null, 'user_type' => $user->user_type ?? null]);
            return $next($request);
        }

        // Only trigger after the hiker has verified their email
        if (is_null($user->email_verified_at)) {
            Log::debug('[EnsureHikingPreferencesSet] skipping - email not verified yet', ['user_id' => $user->id]);
            return $next($request);
        }

        // Skip for AJAX/api requests
        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return $next($request);
        }

        // Allow the explicit preferences management routes and custom profile show so we don't redirect in a loop.
        // Note: we intentionally do NOT allow 'profile.edit' or 'profile.update' here so that users
        // who land on the profile edit page immediately after login will be redirected to the
        // dedicated onboarding flow (onboard.preferences).
        $allowedRoutes = [
            'preferences.index',
            'preferences.update',
            'preferences.reset',
        ];

        $currentRoute = $request->route()?->getName();

        if (in_array($currentRoute, $allowedRoutes, true)) {
            Log::debug('[EnsureHikingPreferencesSet] allowed route - skipping redirect', ['route' => $currentRoute]);
            return $next($request);
        }

        // If user has hiking preferences array and it's non-empty, continue
        $prefs = $user->hiking_preferences ?? [];
        if (is_array($prefs) && count($prefs) > 0) {
            Log::debug('[EnsureHikingPreferencesSet] user has hiking preferences, skipping', ['count' => count($prefs)]);
            return $next($request);
        }

        // If the user has already been onboarded for preferences, skip prompting
        if (!is_null($user->preferences_onboarded_at)) {
            Log::debug('[EnsureHikingPreferencesSet] skipping - user already onboarded for preferences', ['user_id' => $user->id, 'preferences_onboarded_at' => $user->preferences_onboarded_at]);
            return $next($request);
        }

        // If the user has a UserPreference record and they have turned off showing hiking preferences, skip prompting
        if ($user->preferences && data_get($user->preferences, 'show_hiking_preferences') === false) {
            Log::debug('[EnsureHikingPreferencesSet] user preferences indicate to not show hiking preferences prompt', ['user_id' => $user->id]);
            return $next($request);
        }

        // Otherwise redirect to the onboarding preferences route (first time after verification)
        if ($request->method() === 'GET') {
            Log::debug('[EnsureHikingPreferencesSet] redirecting to onboarding preferences', ['user_id' => $user->id]);
            return redirect()->route('onboard.preferences')->with('info', 'Please set your hiking preferences to get personalized trail recommendations.');
        }

        return $next($request);
    }
}
