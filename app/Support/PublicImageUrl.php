<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class PublicImageUrl
{
    public static function for(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return asset(Storage::disk('public')->url($url));
    }
}
