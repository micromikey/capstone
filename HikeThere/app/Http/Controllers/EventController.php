<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('is_public', true)->orderBy('start_at','asc')->paginate(12);
        return view('events.index', compact('events'));
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
    if (!$event->is_public && Auth::id() !== $event->user_id) abort(403);
        // Show a hiker-optimized view when the current user is a hiker
        if (Auth::check() && optional(Auth::user())->user_type === 'hiker') {
            return view('hiker.events.show', compact('event'));
        }

        return view('events.show', compact('event'));
    }
}
