<?php

namespace App\Structures\Dtos;

use App\Structures\Enums\WeatherCodeEnum;
use Illuminate\Support\Carbon;

class WeatherDto
{
    public function __construct(
        public float $temperature = 0,
        public float $humidity = 0,
        public float $pressure = 0,
        public float $windSpeed = 0,
        public int $windDirection = 0,
        public WeatherCodeEnum $weatherCode = WeatherCodeEnum::CLEAR_SKY,
        public Carbon $forecastedAt = new Carbon(),
    ) {
        //
    }
}
