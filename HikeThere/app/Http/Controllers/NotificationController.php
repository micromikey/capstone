<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('message', 'Notifications marked as read.');
    }
}
