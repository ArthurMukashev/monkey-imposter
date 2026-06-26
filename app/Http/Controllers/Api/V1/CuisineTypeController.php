<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CuisineTypeResourceCollection;
use App\Models\CuisineType;

class CuisineTypeController extends Controller
{
    public function index()
    {
        $types = CuisineType::paginate(100);
        return new CuisineTypeResourceCollection($types);
    }
}
