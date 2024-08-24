<?php

namespace App\Console\Commands;

use App\Contracts\WeatherApiContract;
use App\Models\Location;
use App\Services\WeatherService;
use Illuminate\Console\Command;

class FetchWeatherCommand extends Command
{
    protected $signature = 'weather:fetch';

    protected $description = 'Fetches and updates weather for location.';

    public function handle(WeatherApiContract $weatherApiService): void
    {
		Location::all()
			->chunk(50)
			->each(fn ($locations) => (new WeatherService($weatherApiService))->updateWeatherForLocations($locations));

        $this->info('Weather successfully fetched and updated.');
    }
}
