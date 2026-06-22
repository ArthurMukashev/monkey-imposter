<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'section' => $this->section,
            'title' => $this->title,
            'shortDescription' => $this->short_description,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'city' => new CityResource($this->whenLoaded('city')),
            'coordinates' => $this->latitude && $this->longitude
                ? ['lat' => (float)$this->latitude, 'lng' => (float)$this->longitude]
                : null,
            'coverImage' => $this->getCoverImage(), // метод в модели
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'schedule' => $this->schedule, // уже массив из casts
        ];
    }

    private function getCoverImage()
    {
        $cover = $this->images?->firstWhere('is_cover', true);
        if ($cover) {
            return [
                'url' => $cover->url,
                'alt' => $cover->alt,
                'title' => $cover->title,
                'isCover' => $cover->is_cover,
            ];
        }
        return null;
    }
}
