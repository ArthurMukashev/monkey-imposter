<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PlaceDetailedResource;
use App\Http\Resources\Api\V1\PlaceResourceCollection;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{


//    public function index(Request $request)
//    {
//        if ($request->has('section') && !in_array($request->section, ['tourism', 'active', 'gastronomy'])) {
//            return response()->json([
//                'error' => [
//                    'code' => 'bad_request',
//                    'message' => 'Некорректное значение параметра section',
//                    'details' => ['field' => 'section', 'value' => $request->section]
//                ]
//            ], 400);
//        }
//
//        $query = Place::query()
//            ->with(['category', 'city', 'tags', 'images'])
//            ->published();
//
//        // Фильтр по секции
//        if ($request->has('section')) {
//            $query->section($request->section);
//        }
//
//        // Фильтр по городу (slug)
//        if ($request->has('city')) {
//            $query->whereHas('city', fn($q) => $q->where('slug', $request->city));
//        }
//
//        // Фильтр по категории (slug)
//        if ($request->has('category')) {
//            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
//        }
//
//        // Фильтр по тегам (AND, список через запятую)
//        if ($request->has('tags')) {
//            $tags = explode(',', $request->tags);
//            foreach ($tags as $tagSlug) {
//                $query->whereHas('tags', fn($q) => $q->where('slug', $tagSlug));
//            }
//        }
//
//        // Поиск (q) – поиск по title и shortDescription
//        if ($request->filled('q')) {
//            $search = $request->q;
//            $query->where(function ($q) use ($search) {
//                $q->where('title', 'ILIKE', "%{$search}%")
//                    ->orWhere('short_description', 'ILIKE', "%{$search}%");
//            });
//        }
//
//        // Пагинация
//        $page = $request->input('page', 1);
//        $limit = min($request->input('limit', 20), 100);
//
//        $places = $query->paginate($limit, ['*'], 'page', $page);
//
////        return PlaceResource::collection($places);
//        return new PlaceResourceCollection($places);
//    }

    public function show($slug)
    {
        $place = Place::query()
            ->with(['category', 'city', 'tags', 'cuisineTypes', 'images'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return new PlaceDetailedResource($place);
    }
}
