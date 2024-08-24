<?php

namespace App\Jobs;

use App\Models\Location;
use App\Services\WeatherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWeatherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(WeatherService $weatherService): void
    {
		Location::all()
			->chunk(50)
			->each(fn ($locations) => $weatherService->updateWeatherForLocations($locations));
    }
}
