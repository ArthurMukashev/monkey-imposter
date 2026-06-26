<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PlaceDetailedResource;
use App\Http\Resources\Api\V1\PlaceResourceCollection;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{

    public function index(Request $request)
    {
        if ($request->has('section') && ! in_array($request->section, ['tourism', 'active', 'gastronomy'], true)) {
            return response()->json([
                'error' => [
                    'code' => 'bad_request',
                    'message' => 'Некорректное значение параметра section',
                    'details' => ['field' => 'section', 'value' => $request->section],
                ],
            ], 400);
        }

        $query = Place::query()
            ->with(['category', 'city', 'tags', 'images'])
            ->published();

        if ($request->has('section')) {
            $query->section($request->section);
        }

        if ($request->has('city')) {
            $query->whereHas('city', fn ($q) => $q->where('slug', $request->city));
        }

        if ($request->has('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->has('tags')) {
            $tags = explode(',', $request->tags);
            foreach ($tags as $tagSlug) {
                $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
            }
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                    ->orWhere('short_description', 'ILIKE', "%{$search}%");
            });
        }

        $page = $request->input('page', 1);
        $limit = min($request->input('limit', 20), 100);

        $places = $query->paginate($limit, ['*'], 'page', $page);

        return new PlaceResourceCollection($places);
    }

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
