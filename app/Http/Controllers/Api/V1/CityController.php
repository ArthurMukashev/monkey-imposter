<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CityResourceCollection;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::orderBy('sort_order')->paginate(100);
        return new CityResourceCollection($cities);
    }
}
