<?php

namespace App\Providers;

use App\Mail\ResendTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;

class ResendMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->extend(MailManager::class, function (MailManager $manager) {
            $manager->extend('resend', function () {
                return new ResendTransport(
                    config('services.resend.key')
                );
            });

            return $manager;
        });
    }
}
