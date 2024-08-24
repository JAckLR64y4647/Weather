<?php

namespace App\Http\Controllers;

use App\Exports\WeatherExport;
use App\Models\Location;
use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Excel;

class WeatherController extends Controller
{
	/**
	 * @param Location $location
	 * @param WeatherService $weatherService
	 *
	 * @return JsonResponse
	 */
    public function get(Location $location, WeatherService $weatherService): JsonResponse
    {
        $weather = $location
            ->weather()
            ->whereDate('forecasted_at', '>=', now()->startOfDay())
            ->where('forecasted_at', '<=', now()->addDays(7)->endOfDay())
            ->get();

		/** @var User|null $user */
		$user = \Auth::guard('sanctum')->user();

		if ($user && $user->locationViews()->orderBy('created_at', 'desc')->first()?->location_id !== $location->id) {
			$user->locationViews()->create(['location_id' => $location->id]);

			$user->locationViews()
				->where('created_at', '<', now()->subDays(7))
				->delete();
		}

        // If the weather data is not up-to-date, update it.
        if ($weather->count() < 168) {
            $weatherService->updateWeather($location);

            $weather = $location
                ->weather()
                ->whereDate('forecasted_at', '>=', now()->startOfDay())
                ->where('forecasted_at', '<=', now()->addDays(7)->endOfDay())
				->orderBy('forecasted_at')
                ->get();
        }

        return response()->json(['weather' => $weather]);
    }

	public function export(Location $location): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
	{
		return (new WeatherExport($location))->download(
			"weather_$location->slug.csv",
			Excel::CSV,
			['Content-Type' => 'text/csv'],
		);
	}
}
