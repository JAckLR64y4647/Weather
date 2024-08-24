<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController
{
	public function index(Request $request): JsonResponse
	{
		$query = User::withoutRole('admin');

		if ($request->has('sort_by')) {
			$query->orderBy(
				$request->input('sort_by'),
				$request->input('sort_dir', 1) == 1 ? 'asc' : 'desc'
			);
		}

		$paginator = $query->paginate($request->input('per_page', 10));

		return response()->json([
			'data' => UserResource::collection($paginator),
			'meta' => ['total' => $paginator->total()],
		]);
	}

	public function create(Request $request): JsonResponse
	{
		$data = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			'password' => ['required', 'string', 'min:8'],
		]);

		$user = User::create($data);

		return response()->json(UserResource::make($user), 201);
	}

	public function update(Request $request, User $user): JsonResponse
	{
		$data = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
			'is_blocked' => ['required', 'boolean'],
		]);

		$user->update($data);

		return response()->json(UserResource::make($user));
	}

	public function delete(User $user): JsonResponse
	{
		$user->delete();

		return response()->json(null, 204);
	}
}
