<?php

namespace App\Services;

use Illuminate\Support\Str;

class LocationService
{
    public static function generateSlug(string $locationName): string
    {
        $firstPart = explode('/', str_replace('м.', '', $locationName))[0];

        $transliterated = \Transliterator::create('Ukrainian-Latin/BGN')->transliterate($firstPart);

        return Str::slug($transliterated);
    }
}
