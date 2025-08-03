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
}