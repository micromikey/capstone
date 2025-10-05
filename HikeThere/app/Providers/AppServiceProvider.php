<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use Google\Cloud\Storage\StorageClient;
use App\Models\Trail;
use App\Observers\TrailObserver;
use App\Models\TrailPackage;
use App\Observers\TrailPackageObserver;
use App\Models\Event as EventModel;
use App\Observers\EventObserver;
use Illuminate\Auth\Events\Login;
use App\Listeners\SendWeatherNotificationOnLogin;

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
        // Register Google Cloud Storage driver
        Storage::extend('gcs', function ($app, $config) {
            $storageClient = new StorageClient([
                'projectId' => $config['project_id'],
                'keyFile' => $config['key_file'],
            ]);

            $bucket = $storageClient->bucket($config['bucket']);
            $adapter = new GoogleCloudStorageAdapter($bucket, $config['path_prefix'] ?? '');
            $filesystem = new Filesystem($adapter, $config);
            
            // Return Laravel's FilesystemAdapter wrapper with custom URL generator
            return new FilesystemAdapter($filesystem, $adapter, [
                ...$config,
                'url' => "https://storage.googleapis.com/{$config['bucket']}",
            ]);
        });

        Trail::observe(TrailObserver::class);
        TrailPackage::observe(TrailPackageObserver::class);
        EventModel::observe(EventObserver::class);

        // Register event listeners
        Event::listen(
            Login::class,
            SendWeatherNotificationOnLogin::class
        );

        // Ensure DB session timezone is set to Asia/Manila (+08:00) so time-only values
        // stored in the database are interpreted consistently when parsed by Carbon.
        // Only attempt this for MySQL connections and swallow any exceptions so it
        // doesn't break booting in other environments.
        try {
            // Use DB_SESSION_TIMEZONE config to control whether the app sets a session
            // timezone on MySQL connections. If left empty, the app will not issue a
            // `SET time_zone` and will instead rely on the server/global timezone.
            $tz = config('database.connections.mysql.session_timezone');

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
