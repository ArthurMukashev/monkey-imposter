<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PromoResource;
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

        // Лимит
        $limit = $request->input('limit');
        if ($limit) {
            $promos = $query->limit($limit)->get();
            // Для пагинации создадим искусственный paginator, если надо
            // но проще вернуть коллекцию без meta
            // однако спецификация требует meta, поэтому используем paginate с большим лимитом
            // чтобы сохранить формат, можно взять все и обернуть в LengthAwarePaginator
            $total = $query->count();
            $promos = $query->take($limit)->get();
            // Создадим пагинатор вручную
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $promos,
                $total,
                $limit ?: $total,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            return PromoResource::collection($paginator);
        }

        // Если лимит не указан, возвращаем все с пагинацией (по 100)
        $promos = $query->paginate(100);
        return PromoResource::collection($promos);
    }
}
