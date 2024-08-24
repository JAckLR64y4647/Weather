<?php

namespace App\Contracts;

use App\Structures\Dtos\WeatherDto;
use Illuminate\Support\Collection;

interface WeatherApiContract
{
    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return Collection<WeatherDto>
     */
    public function fetchWeather(float $latitude, float $longitude): Collection;

	/**
	 * @param array $latitudes
	 * @param array $longitudes
	 *
	 * @return Collection<Collection<WeatherDto>>
	 */
	function bulkFetchWeather(array $latitudes, array $longitudes): Collection;
}
