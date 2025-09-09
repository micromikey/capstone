<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Trail;
use App\Observers\TrailObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    Trail::observe(TrailObserver::class);
    }
}
