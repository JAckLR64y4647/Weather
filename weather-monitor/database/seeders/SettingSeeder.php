<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
			[
				'name' => 'Geocode API key',
				'description' => 'Key for the API that detects coordinates by the place name.',
				'key' => 'geocode_api_key',
				'value' => '',
			],
			[
				'name' => 'OpenMeteo API url',
				'description' => 'URL to the OpenMeteo API which is used to fetch weather.',
				'key' => 'openmeteo_api_url',
				'value' => 'https://api.open-meteo.com/v1/forecast',
			],
			[
				'name' => 'Weather update frequency',
				'description' => 'How often the weather data should be updated in minutes. Note that the free plan of OpenMeteo allows only 10.000 requests per day.',
				'key' => 'weather_update_frequency',
				'value' => '60',
			],
		];

		foreach ($settings as $setting) {
			if (Setting::where('key', $setting['key'])->exists()) {
				continue;
			}

			Setting::create($setting);
		}
    }
}
