<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ItineraryGeneratorService;
use App\Services\TrailCalculatorService;
use App\Services\WeatherHelperService;
use App\Services\DataNormalizerService;
use App\Services\IntelligentItineraryService;
use App\Services\DurationParserService;
use App\Services\GoogleMapsService;

class ItineraryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TrailCalculatorService::class, function ($app) {
            return new TrailCalculatorService();
        });

        $this->app->singleton(WeatherHelperService::class, function ($app) {
            return new WeatherHelperService();
        });

        $this->app->singleton(DataNormalizerService::class, function ($app) {
            return new DataNormalizerService();
        });

        $this->app->singleton(DurationParserService::class, function ($app) {
            return new DurationParserService();
        });

        $this->app->singleton(IntelligentItineraryService::class, function ($app) {
            return new IntelligentItineraryService(
                $app->make(TrailCalculatorService::class),
                $app->make(WeatherHelperService::class)
            );
        });

        $this->app->singleton(ItineraryGeneratorService::class, function ($app) {
            return new ItineraryGeneratorService(
                $app->make(TrailCalculatorService::class),
                $app->make(WeatherHelperService::class),
                $app->make(DataNormalizerService::class),
                $app->make(IntelligentItineraryService::class),
                $app->make(DurationParserService::class),
                $app->make(GoogleMapsService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}