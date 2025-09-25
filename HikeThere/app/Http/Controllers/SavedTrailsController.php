<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedTrailsController extends Controller
{
    /** Show paginated saved trails for the authenticated user (server-side rendered) */
    public function index(Request $request)
    {
        $user = $request->user() ?: Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $favorites = $user->favoriteTrails()->with(['location', 'primaryImage'])->paginate(12);

        return view('profile.saved-trails', compact('favorites'));
    }
}
