<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CuisineTypeResource;
use App\Models\CuisineType;

class CuisineTypeController extends Controller
{
    public function index()
    {
        $types = CuisineType::paginate(100);
        return CuisineTypeResource::collection($types);
    }
}
