<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\WeatherResource;
use App\Models\Weather;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
	public function index(Request $request): JsonResponse
	{
		$query = Weather::query();

		if ($request->has('sort_by')) {
			$query->orderBy(
				$request->input('sort_by'),
				$request->input('sort_dir', 1) == 1 ? 'asc' : 'desc'
			);
		}

		$paginator = $query->paginate($request->input('per_page', 10));

		return response()->json([
			'data' => WeatherResource::collection($paginator),
			'meta' => ['total' => $paginator->total()],
		]);
    }

	public function create(Request $request): JsonResponse
	{
		$data = $request->validate([
			'location_id' => ['required', 'exists:locations,id'],
			'forecasted_at' => ['required', 'date'],
			'temperature' => ['required', 'numeric'],
			'humidity' => ['required', 'numeric'],
			'pressure' => ['required', 'numeric'],
			'wind_speed' => ['required', 'numeric'],
			'wind_direction' => ['required', 'numeric'],
			'type' => ['required', 'numeric'],
		]);

		$weather = Weather::create($data);

		return response()->json(WeatherResource::make($weather), 201);
	}

	public function update(Request $request, Weather $weather): JsonResponse
	{
		$data = $request->validate([
			'location_id' => ['required', 'exists:locations,id'],
			'forecasted_at' => ['required', 'date'],
			'temperature' => ['required', 'numeric', 'min:-200', 'max:200'],
			'humidity' => ['required', 'numeric', 'min:0', 'max:100'],
			'pressure' => ['required', 'numeric'],
			'wind_speed' => ['required', 'numeric', 'min:0'],
			'wind_direction' => ['required', 'numeric', 'min:0', 'max:360'],
			'type' => ['required', 'numeric'],
		]);

		$weather->update($data);

		return response()->json(WeatherResource::make($weather));
	}

	public function delete(Weather $weather): JsonResponse
	{
		$weather->delete();

		return response()->json(null, 204);
	}
}
