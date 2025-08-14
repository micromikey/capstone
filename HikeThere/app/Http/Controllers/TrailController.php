<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use Illuminate\Http\Request;

class TrailController extends Controller
{
    public function index()
    {
        return view('trails.explore');
    }

    public function show(Trail $trail)
    {
        $trail->load(['location', 'images', 'reviews.user']);
        return view('trails.show', compact('trail'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $trails = Trail::active()
            ->with(['location', 'primaryImage', 'user'])
            ->where(function($q) use ($query) {
                $q->where('trail_name', 'like', '%' . $query . '%')
                  ->orWhere('mountain_name', 'like', '%' . $query . '%')
                  ->orWhereHas('location', function($locationQuery) use ($query) {
                      $locationQuery->where('name', 'like', '%' . $query . '%')
                                   ->orWhere('province', 'like', '%' . $query . '%');
                  });
            })
            ->get();

        return view('trails.search-results', compact('trails', 'query'));
    }
}