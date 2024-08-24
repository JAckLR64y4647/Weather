<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
	public function index(): JsonResponse
	{
		return response()->json(Setting::all());
    }

	public function update(Request $request): JsonResponse
	{
		$data = $request->validate([
			'settings' => ['required', 'array'],
			'settings.*.key' => ['required', 'string'],
			'settings.*.value' => ['required', 'string'],
		]);

		foreach ($data['settings'] as $setting) {
			Setting::where('key', $setting['key'])->update(['value' => $setting['value']]);
		}

		return response()->json();
	}
}
