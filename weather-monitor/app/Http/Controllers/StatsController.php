<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Review;
use App\Models\User;
use App\Models\Weather;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
	public function get(): JsonResponse
	{
		return response()->json([
			'stats' => [
				[
					'label' => 'Users',
					'value' => User::count(),
				],
				[
					'label' => 'Locations',
					'value' => Location::count(),
				],
				[
					'label' => 'Reviews',
					'value' => Review::count(),
				],
				[
					'label' => 'Weather entries',
					'value' => Weather::count(),
				],
			],
			'users_chart_data' => $this->getUsersChartData(),
		]);
    }

	public function getUsersChartData(): array
	{
		return User::query()->selectRaw('DATE(created_at) as date, COUNT(id) as count')
			->where('created_at', '>=', now()->subMonth())
			->groupBy('date')
			->orderBy('date')
			->get()
			->toArray();
	}
}
