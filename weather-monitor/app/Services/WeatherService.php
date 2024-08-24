<?php

namespace App\Services;

use App\Contracts\WeatherApiContract;
use App\Models\Location;
use App\Models\Weather;
use App\Structures\Dtos\WeatherDto;
use Illuminate\Database\Eloquent\Collection;

class WeatherService
{
    public function __construct(protected WeatherApiContract $weatherService)
    {
        //
    }

    public function updateWeather(Location $location): Location
    {
        $weatherCollection = $this->weatherService->fetchWeather($location->latitude, $location->longitude);

        $weatherCollection->each(fn (WeatherDto $dto) => $location->weather()->updateOrCreate(
            ['forecasted_at' => $dto->forecastedAt],
            [
                'location_id' => $location->id,
                'forecasted_at' => $dto->forecastedAt,
                'temperature' => $dto->temperature,
                'humidity' => $dto->humidity,
                'pressure' => $dto->pressure,
                'type' => $dto->weatherCode,
                'wind_speed' => $dto->windSpeed,
                'wind_direction' => $dto->windDirection,
            ],
        ));

        return $location;
    }

	public function updateWeatherForLocations(Collection $locations): void
	{
		$this->weatherService->bulkFetchWeather(
			$locations->pluck('latitude')->toArray(),
			$locations->pluck('longitude')->toArray(),
		)->each(function (\Illuminate\Support\Collection $data, $index) use ($locations) {
			$data->each(function (WeatherDto $dto) use ($locations, $index) {
				$locationId = $locations->toArray()[$index]['id'];

				Weather::query()->updateOrCreate(
					['forecasted_at' => $dto->forecastedAt, 'location_id' => $locationId],
					[
						'location_id' => $locationId,
						'forecasted_at' => $dto->forecastedAt,
						'temperature' => $dto->temperature,
						'humidity' => $dto->humidity,
						'pressure' => $dto->pressure,
						'type' => $dto->weatherCode,
						'wind_speed' => $dto->windSpeed,
						'wind_direction' => $dto->windDirection,
					],
				);
			});
		});
	}
}
