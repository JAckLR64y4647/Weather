<?php

namespace App\Exports;

use App\Models\Location;
use App\Models\Weather;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WeatherExport implements FromCollection, WithMapping, WithHeadings
{
	use Exportable;

	public function __construct(public Location $location)
	{
		//
	}

	/**
    * @return Collection
    */
    public function collection(): Collection
	{
        return Weather::query()
			->whereDate('forecasted_at', '>=', now()->startOfDay())
			->where('forecasted_at', '<=', now()->addDays(7)->endOfDay())
			->where('location_id', $this->location->id)
			->orderBy('forecasted_at')
			->get();
    }

	public function headings(): array
	{
		return [
			'Date',
			'Temperature',
			'Humidity',
			'Pressure',
			'Wind Speed',
			'Wind Direction',
			'Type',
		];
	}

	/**
	 * @param Weather $weather
	 *
	 * @return array
	 */
	public function map($weather): array
	{
		return [
			$weather->forecasted_at->toDateTime(),
			$weather->temperature,
			$weather->humidity,
			$weather->pressure,
			$weather->wind_speed,
			$weather->wind_direction,
			(string) $weather->type,
		];
	}
}
