<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Services\LocationService;
use App\Services\SettingService;
use App\Structures\Enums\LocationTypeEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchPlacesCommand extends Command
{
    protected $signature = 'places:fetch';

    protected $description = 'Fetches all Ukraine places from dovidnyk.in.ua and inserts them into the database.';

	protected $dovidnykUrl = 'https://dovidnyk.in.ua/newtemp/koatuu/get_koatuu.php';

    public function handle(): void
    {
        $places = Http::get($this->dovidnykUrl)->json();

        foreach ($places as $place) {
            $location = Location::query()->where('code', $place['code'])->first();

            if (!$location) {
                $coordinates = $this->fetchCoordinates($place['label']);

                $name = $this->formatName(explode('/', $place['label'])[0], true);

                $location = Location::query()->create([
                    'name' => $name,
                    'slug' => LocationService::generateSlug($name),
                    'code' => $place['code'],
                    'type' => LocationTypeEnum::REGION,
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                    'parent_id' => null,
                ]);

                $this->info("Location $name has been added.");
            }

            if (isset($place['load_on_demand'])) {
                $places = Http::get($this->dovidnykUrl, [
                    'node' => $place['id'],
                    'level' => 1,
                ])->json();

                $cities = Http::get($this->dovidnykUrl, [
                    'node' => $places[0]['id'],
                    'level' => 2,
                ])->json();

                foreach ($cities as $city) {
                    $locationCity = Location::query()->where('code', $city['code'])->first();

                    if (!$locationCity) {
                        $coordinates = $this->fetchCoordinates($place['label']);

                        $name = $this->formatName($city['label'], false);

                        Location::query()->create([
                            'name' => $name,
                            'slug' => LocationService::generateSlug($name),
                            'code' => $city['code'],
                            'type' => LocationTypeEnum::CITY,
                            'latitude' => $coordinates['latitude'],
                            'longitude' => $coordinates['longitude'],
                            'parent_id' => $location->id,
                        ]);

                        $this->info("Location {$city['label']} has been added.");
                    }
                }
            }
        }
    }

    function fetchCoordinates(string $name): array
    {
        $coordinates = Http::get("https://geocode.maps.co/search", [
			'q' => $name,
			'api_key' => SettingService::get('geocode_api_key'),
		])->json();

        // Sleep for 1 second to avoid rate limiting.
        sleep(1);

        return ['latitude' => $coordinates[0]['lat'], 'longitude' => $coordinates[0]['lon']];
    }

    function formatName(string $locationName, bool $isRegion): string
    {
        $names = explode('/', str_replace('М.', '', $locationName));

        $name = Str::title($names[0]);
        $city = $names[1] ?? '';

        if (!empty($city)) {
            $city = ($isRegion ? '' : 'м.') . Str::title($city);
        } else {
            $name = ($isRegion ? '' : 'м.') . Str::title($name);
        }

        return !empty($city) ? $name . '/' . $city : $name;
    }
}
