<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unread, read
        $type = $request->get('type');

        $query = Auth::user()->notifications();

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }

        if ($type) {
            $query->ofType($type);
        }

        $notifications = $query->paginate(20);

        return view('notifications.index', compact('notifications', 'filter', 'type'));
    }

    /**
     * Get notifications for AJAX/API requests.
     */
    public function getNotifications(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $notifications = Auth::user()
            ->notifications()
            ->take($limit)
            ->get();

        $unreadCount = Auth::user()->unreadNotificationsCount();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Get the latest unread notification for toast display.
     */
    public function getLatest(Request $request)
    {
        // Get the timestamp of last check from session
        $lastCheck = $request->session()->get('last_notification_check', now()->subMinutes(5));
        
        // Get the most recent unread notification created after last check
        $notification = Auth::user()
            ->notifications()
            ->unread()
            ->where('created_at', '>', $lastCheck)
            ->orderBy('created_at', 'desc')
            ->first();

        // Update last check timestamp
        $request->session()->put('last_notification_check', now());

        if ($notification) {
            return response()->json([
                'notification' => [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at->diffForHumans(),
                ]
            ]);
        }

        return response()->json([
            'notification' => null
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark a notification as unread.
     */
    public function markAsUnread($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsUnread();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as unread',
            ]);
        }

        return back()->with('success', 'Notification marked as unread.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()
            ->notifications()
            ->unread()
            ->update(['read_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted',
            ]);
        }

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Delete all read notifications.
     */
    public function destroyRead()
    {
        Auth::user()
            ->notifications()
            ->read()
            ->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All read notifications deleted',
            ]);
        }

        return back()->with('success', 'All read notifications deleted.');
    }
}

