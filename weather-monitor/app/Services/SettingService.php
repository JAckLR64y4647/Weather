<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
	public static function get(string $key): string
	{
		return Setting::where('key', $key)->value('value');
	}
}
