<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CityResource;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::orderBy('sort_order')->paginate(100);
        return CityResource::collection($cities);
    }
}
