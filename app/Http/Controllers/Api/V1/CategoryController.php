<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResourceCollection;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();
        if ($request->has('section')) {
            $query->where('section', $request->section);
        }
        $categories = $query->orderBy('sort_order')->paginate(100);
        return new CategoryResourceCollection($categories);
    }
}
