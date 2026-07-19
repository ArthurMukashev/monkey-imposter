<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Place;
use App\Support\PublicImageUrl;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'placement' => $this->placement,
            'section' => $this->section,
            'priority' => (int) $this->priority,
            'activeFrom' => $this->active_from?->toIso8601String(),
            'activeUntil' => $this->active_until?->toIso8601String(),
            'title' => $this->title,
            'teaser' => $this->teaser,
            'image' => $this->getImageData(), // метод в модели
            'target' => [
                'type' => $this->target_type,
                'slug' => $this->target_slug,
                'url' => $this->target_url,
                'href' => $this->getTargetHref(),
            ],
        ];
    }

    private function getImageData()
    {
        if ($this->relationLoaded('image') && $this->image) {
            return [
                'url' => PublicImageUrl::for($this->image->url),
                'alt' => $this->image->alt,
                'title' => $this->image->title,
                'isCover' => (bool) $this->image->is_cover,
            ];
        }

        return null;
    }

    private function getTargetHref(): ?string
    {
        if ($this->target_type === 'external') {
            return $this->target_url;
        }

        if ($this->target_type === 'section') {
            return match ($this->target_slug) {
                'active' => '/events',
                'culture' => '/events?category=culture',
                'tourism' => '/routes',
                'gastronomy' => '/gastronomy',
                default => null,
            };
        }

        if ($this->target_type !== 'place' || $this->target_slug === null) {
            return null;
        }

        $section = Place::query()
            ->where('slug', $this->target_slug)
            ->published()
            ->value('section');

        return match ($section) {
            'active' => '/events/'.$this->target_slug,
            'tourism' => '/routes/'.$this->target_slug,
            'gastronomy' => '/gastronomy/'.$this->target_slug,
            default => null,
        };
    }
}
