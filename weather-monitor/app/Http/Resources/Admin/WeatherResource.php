<?php

namespace App\Http\Resources\Admin;

use App\Models\Weather;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Weather */
class WeatherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'id' => $this->id,
			'location_id' => $this->location_id,
			'temperature' => $this->temperature,
			'humidity' => $this->humidity,
			'wind_speed' => $this->wind_speed,
			'wind_direction' => $this->wind_direction,
			'pressure' => $this->pressure,
			'type' => $this->type,
			'forecasted_at' => $this->forecasted_at,
		];
    }
}
