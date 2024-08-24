<?php

namespace App\Jobs;

use App\Contracts\WeatherApiContract;
use App\Models\Location;
use App\Models\Weather;
use App\Services\WeatherService;
use App\Structures\Dtos\WeatherDto;
use App\Structures\Enums\LocationTypeEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchWeatherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected WeatherService $weatherService;

    public function __construct(protected WeatherApiContract $weatherApiService)
    {
        $this->weatherService = new WeatherService($weatherApiService);
    }

    public function handle(): void
    {
        Location::where('type', LocationTypeEnum::REGION)->each(function (Location $location) {
            $this->weatherService->updateWeather($location);
        });
    }
}
