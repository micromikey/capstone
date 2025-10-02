<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a user.
     */
    public function create(User $user, string $type, string $title, string $message, array $data = [])
    {
        // Check user preferences before creating notification
        $preferences = $user->preferences;
        
        if ($preferences && !$this->shouldNotify($preferences, $type)) {
            return null;
        }

        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create a notification for multiple users.
     */
    public function createForMany($users, string $type, string $title, string $message, array $data = [])
    {
        $notifications = [];

        foreach ($users as $user) {
            $notification = $this->create($user, $type, $title, $message, $data);
            if ($notification) {
                $notifications[] = $notification;
            }
        }

        return $notifications;
    }

    /**
     * Send a trail update notification.
     */
    public function sendTrailUpdate(User $user, $trail, string $updateMessage)
    {
        return $this->create(
            $user,
            'trail_update',
            'Trail Update: ' . $trail->name,
            $updateMessage,
            [
                'trail_id' => $trail->id,
                'trail_slug' => $trail->slug,
                'trail_name' => $trail->name,
            ]
        );
    }

    /**
     * Send a security alert notification.
     */
    public function sendSecurityAlert(User $user, string $alertTitle, string $alertMessage)
    {
        return $this->create(
            $user,
            'security_alert',
            $alertTitle,
            $alertMessage
        );
    }

    /**
     * Send a booking confirmation notification.
     */
    public function sendBookingConfirmation(User $user, $booking)
    {
        return $this->create(
            $user,
            'booking',
            'Booking Confirmed',
            'Your booking for ' . $booking->trail->name . ' has been confirmed.',
            [
                'booking_id' => $booking->id,
                'trail_id' => $booking->trail_id,
                'trail_name' => $booking->trail->name,
            ]
        );
    }

    /**
     * Send a system notification.
     */
    public function sendSystemNotification(User $user, string $title, string $message, array $data = [])
    {
        return $this->create(
            $user,
            'system',
            $title,
            $message,
            $data
        );
    }

    /**
     * Send welcome notification to new users.
     */
    public function sendWelcomeNotification(User $user)
    {
        return $this->create(
            $user,
            'system',
            'Welcome to HikeThere!',
            'Thank you for joining HikeThere. Start exploring amazing trails in the Philippines!'
        );
    }

    /**
     * Send weather notification with current location and latest itinerary weather.
     */
    public function sendWeatherNotification(User $user, array $weatherData)
    {
        // Extract weather information
        $currentTemp = $weatherData['current_temp'] ?? null;
        $currentLocation = $weatherData['current_location'] ?? 'Current Location';
        $trailTemp = $weatherData['trail_temp'] ?? null;
        $trailName = $weatherData['trail_name'] ?? null;
        $itineraryId = $weatherData['itinerary_id'] ?? null;

        // Build the message
        $message = '';
        if ($currentTemp !== null) {
            $message .= "{$currentTemp}° in {$currentLocation}";
        }
        
        if ($trailTemp !== null && $trailName) {
            if ($message) {
                $message .= "\n";
            }
            $message .= "{$trailTemp}° in {$trailName}";
        }

        if (!$message) {
            return null; // Don't send if no weather data available
        }

        return $this->create(
            $user,
            'weather',
            'Weather Update',
            $message,
            [
                'current_temp' => $currentTemp,
                'current_location' => $currentLocation,
                'trail_temp' => $trailTemp,
                'trail_name' => $trailName,
                'itinerary_id' => $itineraryId,
                'weather_icon' => $weatherData['weather_icon'] ?? null,
                'current_condition' => $weatherData['current_condition'] ?? null,
                'trail_condition' => $weatherData['trail_condition'] ?? null,
            ]
        );
    }

    /**
     * Send new event notification to hikers.
     * Can notify all hikers or hikers interested in a specific trail.
     */
    public function sendNewEventNotification($event, $hikers = null)
    {
        // If no specific hikers provided, get all hikers
        if ($hikers === null) {
            $hikers = User::where('user_type', 'hiker')->get();
        }

        $organizationName = $event->user->name ?? 'An organization';
        $trailName = $event->trail ? $event->trail->name : $event->location_name;
        $eventDate = $event->start_at ? $event->start_at->format('M d, Y') : 'soon';

        $message = "{$organizationName} is hosting a new event at {$trailName}";
        if ($event->start_at) {
            $message .= " on {$eventDate}";
        }

        $notifications = [];
        foreach ($hikers as $hiker) {
            $notification = $this->create(
                $hiker,
                'new_event',
                'New Event: ' . $event->title,
                $message,
                [
                    'event_id' => $event->id,
                    'event_slug' => $event->slug,
                    'event_title' => $event->title,
                    'trail_id' => $event->trail_id,
                    'trail_name' => $trailName,
                    'organization_id' => $event->user_id,
                    'organization_name' => $organizationName,
                    'start_at' => $event->start_at?->toISOString(),
                    'end_at' => $event->end_at?->toISOString(),
                    'is_free' => $event->is_free,
                    'price' => $event->price,
                ]
            );

            if ($notification) {
                $notifications[] = $notification;
            }
        }

        return $notifications;
    }

    /**
     * Check if user should be notified based on their preferences.
     */
    protected function shouldNotify($preferences, string $type): bool
    {
        // If no preferences set, allow all notifications (default behavior)
        if (!$preferences) {
            return true;
        }

        // Map notification types to preference settings
        $typeMapping = [
            'trail_update' => 'trail_updates',
            'security_alert' => 'security_alerts',
            'newsletter' => 'newsletter',
            'new_event' => 'push_notifications', // Events use push notifications
            'weather' => 'push_notifications', // Weather uses push notifications
            'booking' => 'push_notifications', // Bookings use push notifications
            'system' => 'push_notifications', // System notifications use push notifications
        ];

        // If type has a specific preference, check it
        if (isset($typeMapping[$type])) {
            $preferenceKey = $typeMapping[$type];
            return $preferences->{$preferenceKey} ?? true;
        }

        // For other types, check general push notifications setting
        return $preferences->push_notifications ?? true;
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        return $notification->markAsRead();
    }

    /**
     * Mark all user notifications as read.
     */
    public function markAllAsRead(User $user)
    {
        return $user->notifications()->unread()->update(['read_at' => now()]);
    }

    /**
     * Delete old read notifications (cleanup).
     */
    public function deleteOldReadNotifications(int $daysOld = 30)
    {
        return Notification::read()
            ->where('read_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}
