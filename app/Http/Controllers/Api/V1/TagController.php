<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TagResource;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::paginate(100);
        return TagResource::collection($tags);
    }
}
