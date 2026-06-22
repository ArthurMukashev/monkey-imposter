<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceDetailedResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'slug' => $this->slug,
            'section' => $this->section,
            'title' => $this->title,
            'shortDescription' => $this->short_description,
            'descriptionHtml' => $this->description_html,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'city' => new CityResource($this->whenLoaded('city')),
            'coordinates' => $this->latitude && $this->longitude
                ? ['lat' => (float) $this->latitude, 'lng' => (float) $this->longitude]
                : null,
            'address' => $this->address,
            'workingHours' => $this->working_hours,
            'averageBill' => $this->average_bill,
            'cuisineTypes' => CuisineTypeResource::collection($this->whenLoaded('cuisineTypes')),
            'menuHtml' => $this->menu_html,
            'schedule' => $this->schedule,
            'images' => $this->getAllImages(),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'seo' => [
                'title' => $this->seo_title,
                'description' => $this->seo_description,
                'canonicalPath' => $this->seo_canonical_path,
            ],
        ];

        // Удаляем null-поля для чистоты ответа (по желанию)
        return array_filter($data, fn ($value) => $value !== null);
    }

    private function getAllImages()
    {
        return $this->images->map(function ($image) {
            return [
                'url' => $image->url,
                'alt' => $image->alt,
                'title' => $image->title,
                'isCover' => (bool) $image->is_cover,
            ];
        });
    }
}
