<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PromoResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'page' => $this->resource->currentPage(),
                'limit' => $this->resource->perPage(),
                'total' => $this->resource->total(),
                'totalPages' => $this->resource->lastPage(),
            ]
        ];
    }}
