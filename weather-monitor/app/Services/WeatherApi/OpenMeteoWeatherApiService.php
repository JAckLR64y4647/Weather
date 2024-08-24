<?php

namespace App\Services\WeatherApi;

use App\Contracts\WeatherApiContract;
use App\Services\SettingService;
use App\Structures\Dtos\WeatherDto;
use App\Structures\Enums\WeatherCodeEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OpenMeteoWeatherApiService implements WeatherApiContract
{
    public function fetchWeather(float $latitude, float $longitude): Collection
    {
        $response = Http::get(SettingService::get('openmeteo_api_url'), [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'hourly' => [
                'temperature_2m',
                'relative_humidity_2m',
                'precipitation_probability',
                'precipitation',
                'weather_code',
                'surface_pressure',
                'wind_speed_10m',
                'wind_direction_10m'
            ]
        ]);

        $data = $response->json();

        /** @var Collection<WeatherDto> $collection */
        $collection = new Collection();

        // Populate the collection with WeatherDto objects
        array_map(fn ($hourly) => $collection->push(new WeatherDto()), $data['hourly']['time']);

        foreach ($data['hourly'] as $dataKey => $dataItems) {
            foreach ($dataItems as $key => $value) {
                if ($dataKey === 'time') {
                    $collection[$key]->forecastedAt = Carbon::parse($value);
                    continue;
                }

                if ($dataKey === 'temperature_2m') {
                    $collection[$key]->temperature = $value;
                    continue;
                }

                if ($dataKey === 'relative_humidity_2m') {
                    $collection[$key]->humidity = $value;
                    continue;
                }

                if ($dataKey === 'surface_pressure') {
                    $collection[$key]->pressure = $value;
                    continue;
                }

                if ($dataKey === 'wind_speed_10m') {
                    $collection[$key]->windSpeed = $value;
                    continue;
                }

                if ($dataKey === 'wind_direction_10m') {
                    $collection[$key]->windDirection = $value;
                    continue;
                }

                if ($dataKey === 'weather_code') {
                    $collection[$key]->weatherCode = WeatherCodeEnum::fromValue($value);
                }
            }
        }

        return $collection;
    }

	function bulkFetchWeather(array $latitudes, array $longitudes): Collection
	{
		$response = Http::get(SettingService::get('openmeteo_api_url'), [
			'latitude' => $latitudes,
			'longitude' => $longitudes,
			'hourly' => [
				'temperature_2m',
				'relative_humidity_2m',
				'precipitation_probability',
				'precipitation',
				'weather_code',
				'surface_pressure',
				'wind_speed_10m',
				'wind_direction_10m'
			]
		]);

		$responses = $response->json();
		$result = new Collection();

		foreach ($responses as $data) {
			/** @var Collection<WeatherDto> $collection */
			$collection = new Collection();

			// Populate the collection with WeatherDto objects
			array_map(fn ($hourly) => $collection->push(new WeatherDto()), $data['hourly']['time']);

			foreach ($data['hourly'] as $dataKey => $dataItems) {
				foreach ($dataItems as $key => $value) {
					if ($dataKey === 'time') {
						$collection[$key]->forecastedAt = Carbon::parse($value);
						continue;
					}

					if ($dataKey === 'temperature_2m') {
						$collection[$key]->temperature = $value;
						continue;
					}

					if ($dataKey === 'relative_humidity_2m') {
						$collection[$key]->humidity = $value;
						continue;
					}

					if ($dataKey === 'surface_pressure') {
						$collection[$key]->pressure = $value;
						continue;
					}

					if ($dataKey === 'wind_speed_10m') {
						$collection[$key]->windSpeed = $value;
						continue;
					}

					if ($dataKey === 'wind_direction_10m') {
						$collection[$key]->windDirection = $value;
						continue;
					}

					if ($dataKey === 'weather_code') {
						$collection[$key]->weatherCode = WeatherCodeEnum::fromValue($value);
					}
				}
			}

			$result->push($collection);
		}

		return $result;
	}
}
