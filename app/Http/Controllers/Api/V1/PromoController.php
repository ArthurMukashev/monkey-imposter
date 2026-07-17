<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PromoResourceCollection;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index(Request $request)
    {
        $query = Promo::query()
            ->with(['image'])
            ->active();

        // Фильтр по placement (обязательный)
        $placement = $request->input('placement', 'home');
        $query->where('placement', $placement);

        // Фильтр по разделу (если placement = section)
        if ($placement === 'section' && $request->has('section')) {
            $query->where('section', $request->section);
        }

        // Сортировка по priority убыванию, при одинаковом - по created_at
        $query->orderBy('priority', 'desc')->orderBy('created_at', 'asc');

        $requestedLimit = (int) $request->input('limit', 100);
        $limit = min(max($requestedLimit, 1), 100);

        $promos = $query->paginate($limit);

        return new PromoResourceCollection($promos);
    }
}
