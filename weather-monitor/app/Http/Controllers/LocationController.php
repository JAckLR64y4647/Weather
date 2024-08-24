<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Location;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function index(): JsonResponse
	{
		$data = Location::query()
			->orderBy('name')
			->get();

        return response()->json($data);
    }

	public function toggleFavourite(Location $location): JsonResponse
	{
		/** @var User $user */
		$user = auth()->user();

		$user->favouriteLocations()->toggle($location);

		return response()->json();
	}

	public function getReviews(Location $location): JsonResponse
	{
		return response()->json(ReviewResource::collection($location->reviews()->with('user')->get()));
	}

	public function createReview(Location $location): JsonResponse
	{
		$request = request()->validate(['comment' => ['required', 'string', 'max:255']]);

		$location->reviews()->create([
			'user_id' => auth()->id(),
			'comment' => $request['comment'],
		]);

		return response()->json(null, 201);
	}

	public function deleteReview(int $location, Review $review): JsonResponse
	{
		$review->delete();

		return response()->json(null, 204);
	}
}
