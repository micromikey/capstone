<?php

namespace App\Listeners;

use App\Services\WeatherNotificationService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class SendWeatherNotificationOnLogin
{

    protected $weatherNotificationService;

    /**
     * Create the event listener.
     */
    public function __construct(WeatherNotificationService $weatherNotificationService)
    {
        $this->weatherNotificationService = $weatherNotificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        Log::info('SendWeatherNotificationOnLogin: Login event received');
        
        $user = $event->user;
        
        Log::info('SendWeatherNotificationOnLogin: User', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'name' => $user->name
        ]);

        // Only send weather notifications to hikers
        if ($user->user_type !== 'hiker') {
            Log::info('SendWeatherNotificationOnLogin: User is not a hiker, skipping');
            return;
        }

        // Check if we should send the notification (not already sent in the last 6 hours)
        if (!$this->weatherNotificationService->shouldSendWeatherNotification($user)) {
            Log::info('SendWeatherNotificationOnLogin: Weather notification already sent in the last 6 hours, skipping');
            return;
        }

        try {
            Log::info('SendWeatherNotificationOnLogin: Attempting to send weather notification');
            $this->weatherNotificationService->sendLoginWeatherNotification($user);
            Log::info('SendWeatherNotificationOnLogin: Weather notification sent successfully');
        } catch (\Exception $e) {
            Log::error('Failed to send weather notification on login: ' . $e->getMessage());
        }
    }
}

