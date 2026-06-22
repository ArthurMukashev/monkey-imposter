<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'placement' => $this->placement,
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
            ],
        ];
    }

    private function getImageData()
    {
        if ($this->relationLoaded('image') && $this->image) {
            return [
                'url' => $this->image->url,
                'alt' => $this->image->alt,
                'title' => $this->image->title,
                'isCover' => (bool) $this->image->is_cover,
            ];
        }
        return null;
    }
}
