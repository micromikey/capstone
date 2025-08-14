<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        // Get user data for hikers
        $user = null;
        $followedTrails = collect();
        $followingCount = 0;
        
        if (auth()->check() && auth()->user()->user_type === 'hiker') {
            $user = auth()->user();
            
            // Get trails from followed organizations
            $followedTrails = $user->followedOrganizationsTrails()
                ->with(['user', 'location', 'primaryImage'])
                ->get();
            
            // Get count of organizations being followed
            $followingCount = $user->following()->count();
        }

        return view('explore', [
            'user' => $user,
            'followedTrails' => $followedTrails,
            'followingCount' => $followingCount,
        ]);
    }
}