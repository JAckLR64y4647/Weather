<?php

namespace App\Structures\Enums;

use App\Traits\BaseEnumTrait;

enum WeatherCodeEnum: int
{
    use BaseEnumTrait;

    case CLEAR_SKY = 0;
    case MAINLY_CLEAR = 1;
    case PARTLY_CLOUDY = 2;
    case OVERCAST = 3;
    case FOG = 45;
    case RIME_FOG = 48;
    case DRIZZLE_LIGHT = 51;
    case DRIZZLE_MODERATE = 53;
    case DRIZZLE_DENSE = 55;
    case FREEZING_DRIZZLE_LIGHT = 56;
    case FREEZING_DRIZZLE_DENSE = 57;
    case RAIN_SLIGHT = 61;
    case RAIN_MODERATE = 63;
    case RAIN_HEAVY = 65;
    case SNOW_SLIGHT = 71;
    case SNOW_MODERATE = 73;
    case SNOW_HEAVY = 75;
    case SNOW_GRAINS = 77;
    case RAIN_SHOWERS_SLIGHT = 80;
    case RAIN_SHOWERS_MODERATE = 81;
    case RAIN_SHOWERS_VIOLENT = 82;
    case SNOW_SHOWERS_SLIGHT = 85;
    case SNOW_SHOWERS_HEAVY = 86;
    case THUNDERSTORM = 95;
    case THUNDERSTORM_WITH_HAIL_SLIGHT = 96;
    case THUNDERSTORM_WITH_HAIL_HEAVY = 99;
}
