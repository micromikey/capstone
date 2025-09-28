<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Trail;
use App\Observers\TrailObserver;
use App\Models\TrailPackage;
use App\Observers\TrailPackageObserver;
use App\Models\Event;
use App\Observers\EventObserver;

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
    TrailPackage::observe(TrailPackageObserver::class);
    Event::observe(EventObserver::class);

    // Ensure DB session timezone is set to Asia/Manila (+08:00) so time-only values
    // stored in the database are interpreted consistently when parsed by Carbon.
    // Only attempt this for MySQL connections and swallow any exceptions so it
    // doesn't break booting in other environments.
        try {
            // Use DB_SESSION_TIMEZONE env var to control whether the app sets a session
            // timezone on MySQL connections. If left empty, the app will not issue a
            // `SET time_zone` and will instead rely on the server/global timezone.
            $tz = env('DB_SESSION_TIMEZONE', null);

            if ($tz) {
                $connection = DB::connection();
                $driver = $connection->getDriverName();
                if ($driver === 'mysql') {
                    // set session time_zone to the configured value (e.g. '+08:00')
                    $connection->statement("SET time_zone = '{$tz}'");
                }
            }
        } catch (\Throwable $e) {
            // do not interrupt application bootstrap if setting timezone fails
            // optionally log in non-production environments
            if (config('app.env') !== 'production') {
                Log::warning('Unable to set DB session time_zone: ' . $e->getMessage());
            }
        }
    }
}
