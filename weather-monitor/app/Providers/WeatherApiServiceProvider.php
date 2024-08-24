<?php

namespace App\Providers;

use App\Contracts\WeatherApiContract;
use App\Services\WeatherApi\OpenMeteoWeatherApiService;
use Illuminate\Support\ServiceProvider;

class WeatherApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(WeatherApiContract::class, function () {
            // TODO: Add settings.
            $service = env('WEATHER_API_SERVICE', 'openmeteo');

            switch ($service) {
                case 'openmeteo':
                default:
                    return new OpenMeteoWeatherApiService();
            }
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
