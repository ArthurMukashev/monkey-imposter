<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\City;
use App\Models\CuisineType;
use App\Models\Place;
use App\Models\Promo;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class SyncRemoteData extends Command
{
    private const PROMO_PLACEMENTS = ['home', 'section', 'place-details', 'kiosk-home'];

    protected $signature = 'sync:remote-data';

    protected $description = 'Синхронизирует публичные данные удалённого API в локальную БД';

    private string $remoteUrl;

    public function handle(): int
    {
        $this->remoteUrl = rtrim((string) config('app.remote_api_url'), '/');

        if ($this->remoteUrl === '' || str_contains($this->remoteUrl, 'example.com')) {
            $this->error('REMOTE_API_URL не настроен.');

            return self::FAILURE;
        }

        $this->info('Начинаем синхронизацию...');

        try {
            $references = [
                'cities' => $this->fetchList('cities'),
                'categories' => $this->fetchList('categories'),
                'tags' => $this->fetchList('tags'),
                'cuisine-types' => $this->fetchList('cuisine-types'),
            ];
            $promos = $this->fetchPromos();
            $places = $this->fetchPlaceDetails();

            DB::transaction(function () use ($references, $promos, $places): void {
                $this->storeReferences($references);
                $this->storePromos($promos);
                $this->storePlaces($places);
            });

            Cache::put('last_sync_time', now()->toIso8601String());
        } catch (Throwable $error) {
            $this->error('Синхронизация не выполнена: '.$error->getMessage());

            return self::FAILURE;
        }

        $this->info('Синхронизация завершена.');

        return self::SUCCESS;
    }

    private function fetchList(string $endpoint, array $query = []): array
    {
        $items = [];
        $page = 1;

        do {
            $payload = $this->getJson($endpoint, [
                ...$query,
                'page' => $page,
                'limit' => 100,
            ]);
            $pageItems = $payload['data'] ?? null;
            $totalPages = $payload['meta']['totalPages'] ?? null;

            if (! is_array($pageItems) || ! is_numeric($totalPages)) {
                throw new RuntimeException("Некорректный list envelope: {$endpoint}");
            }

            array_push($items, ...$pageItems);
            $page++;
        } while ($page <= (int) $totalPages);

        return $items;
    }

    private function fetchPromos(): array
    {
        $promos = [];

        foreach (self::PROMO_PLACEMENTS as $placement) {
            foreach ($this->fetchList('promos', ['placement' => $placement]) as $promo) {
                $promos[$promo['id']] = $promo;
            }
        }

        return array_values($promos);
    }

    private function fetchPlaceDetails(): array
    {
        $details = [];

        foreach ($this->fetchList('places') as $place) {
            $slug = $place['slug'] ?? null;

            if (! is_string($slug) || $slug === '') {
                throw new RuntimeException('В списке places отсутствует slug.');
            }

            $payload = $this->getJson('places/'.rawurlencode($slug));
            $detail = $payload['data'] ?? null;

            if (! is_array($detail)) {
                throw new RuntimeException("Некорректный detail envelope: {$slug}");
            }

            $details[] = $detail;
        }

        return $details;
    }

    private function getJson(string $endpoint, array $query = []): array
    {
        $response = Http::acceptJson()
            ->timeout(15)
            ->retry(2, 250)
            ->get($this->remoteUrl.'/'.$endpoint, $query)
            ->throw()
            ->json();

        if (! is_array($response)) {
            throw new RuntimeException("API вернул не JSON-объект: {$endpoint}");
        }

        return $response;
    }

    private function storeReferences(array $references): void
    {
        foreach ($references['cities'] as $item) {
            $this->upsertByRemoteIdentity(City::class, $item, [
                'slug' => $item['slug'],
                'title' => $item['title'],
                'sort_order' => $item['sortOrder'] ?? 0,
            ]);
        }

        foreach ($references['categories'] as $item) {
            $section = $item['section'] ?? null;

            if (! in_array($section, ['tourism', 'active', 'gastronomy'], true)) {
                throw new RuntimeException('Категория содержит некорректный section.');
            }

            $this->upsertByRemoteIdentity(Category::class, $item, [
                'slug' => $item['slug'],
                'section' => $section,
                'title' => $item['title'],
                'short_description' => $item['shortDescription'] ?? null,
                'sort_order' => $item['sortOrder'] ?? 0,
            ]);
        }

        foreach ($references['tags'] as $item) {
            $this->upsertByRemoteIdentity(Tag::class, $item, [
                'slug' => $item['slug'],
                'title' => $item['title'],
                'color' => $item['color'] ?? null,
            ]);
        }

        foreach ($references['cuisine-types'] as $item) {
            $this->upsertByRemoteIdentity(CuisineType::class, $item, [
                'slug' => $item['slug'],
                'title' => $item['title'],
            ]);
        }
    }

    private function upsertByRemoteIdentity(string $modelClass, array $item, array $attributes): Model
    {
        $remoteId = $item['id'] ?? null;
        $slug = $item['slug'] ?? null;

        if (! is_string($remoteId) || ! is_string($slug)) {
            throw new RuntimeException("{$modelClass}: отсутствуют id или slug.");
        }

        $model = $modelClass::query()
            ->whereKey($remoteId)
            ->orWhere('slug', $slug)
            ->first() ?? new $modelClass;

        if (! $model->exists) {
            $model->setAttribute($model->getKeyName(), $remoteId);
        }

        $model->forceFill($attributes)->save();

        return $model;
    }

    private function storePromos(array $items): void
    {
        $syncedIds = [];

        foreach ($items as $item) {
            $remoteId = $item['id'] ?? null;

            if (! is_string($remoteId)) {
                throw new RuntimeException('Промо не содержит id.');
            }

            $promo = Promo::query()->find($remoteId)
                ?? Promo::query()
                    ->where('placement', $item['placement'])
                    ->where('title', $item['title'])
                    ->first()
                ?? new Promo;

            if (! $promo->exists) {
                $promo->id = $remoteId;
            }

            $promo->forceFill([
                'placement' => $item['placement'],
                'priority' => $item['priority'],
                'section' => $item['section'] ?? null,
                'active_from' => $item['activeFrom'] ?? null,
                'active_until' => $item['activeUntil'] ?? null,
                'title' => $item['title'],
                'teaser' => $item['teaser'],
                'target_type' => $item['target']['type'],
                'target_slug' => $item['target']['slug'] ?? null,
                'target_url' => $item['target']['url'] ?? null,
            ])->save();

            $this->syncPromoImage($promo, $item['image'] ?? null);
            $syncedIds[] = $promo->id;
        }

        $stalePromos = Promo::query()
            ->when($syncedIds !== [], fn ($query) => $query->whereNotIn('id', $syncedIds))
            ->get();

        foreach ($stalePromos as $promo) {
            $promo->image()->delete();
            $promo->delete();
        }
    }

    private function syncPromoImage(Promo $promo, mixed $image): void
    {
        if (! is_array($image)) {
            $promo->image()->delete();

            return;
        }

        $promo->image()->updateOrCreate([], [
            'url' => $image['url'],
            'alt' => $image['alt'],
            'title' => $image['title'],
            'is_cover' => true,
            'sort_order' => 0,
        ]);
    }

    private function storePlaces(array $items): void
    {
        $syncedIds = [];

        foreach ($items as $item) {
            $category = Category::query()->where('slug', $item['category']['slug'])->firstOrFail();
            $city = City::query()->where('slug', $item['city']['slug'])->firstOrFail();
            $remoteId = $item['id'] ?? null;
            $slug = $item['slug'] ?? null;

            if (! is_string($remoteId) || ! is_string($slug)) {
                throw new RuntimeException('Place не содержит id или slug.');
            }

            $place = Place::query()
                ->whereKey($remoteId)
                ->orWhere('slug', $slug)
                ->first() ?? new Place;

            if (! $place->exists) {
                $place->id = $remoteId;
            }

            $place->forceFill([
                'slug' => $slug,
                'section' => $item['section'],
                'title' => $item['title'],
                'short_description' => $item['shortDescription'],
                'description_html' => $item['descriptionHtml'] ?? null,
                'category_id' => $category->id,
                'city_id' => $city->id,
                'latitude' => $item['coordinates']['lat'] ?? null,
                'longitude' => $item['coordinates']['lng'] ?? null,
                'address' => $item['address'] ?? null,
                'working_hours' => $item['workingHours'] ?? null,
                'average_bill' => $item['averageBill'] ?? null,
                'menu_html' => $item['menuHtml'] ?? null,
                'schedule' => $item['schedule'] ?? null,
                'seo_title' => $item['seo']['title'] ?? null,
                'seo_description' => $item['seo']['description'] ?? null,
                'seo_canonical_path' => $item['seo']['canonicalPath'] ?? null,
                'is_published' => true,
            ])->save();

            $this->syncPlaceRelations($place, $item);
            $this->syncPlaceImages($place, $item['images'] ?? []);
            $syncedIds[] = $place->id;
        }

        Place::query()
            ->when($syncedIds !== [], fn ($query) => $query->whereNotIn('id', $syncedIds))
            ->update(['is_published' => false]);
    }

    private function syncPlaceRelations(Place $place, array $item): void
    {
        $tagSlugs = collect($item['tags'] ?? [])->pluck('slug')->filter()->values();
        $tagIds = Tag::query()->whereIn('slug', $tagSlugs)->pluck('id');

        if ($tagIds->count() !== $tagSlugs->count()) {
            throw new RuntimeException("Не все теги найдены для {$place->slug}.");
        }

        $cuisineSlugs = collect($item['cuisineTypes'] ?? [])->pluck('slug')->filter()->values();
        $cuisineIds = CuisineType::query()->whereIn('slug', $cuisineSlugs)->pluck('id');

        if ($cuisineIds->count() !== $cuisineSlugs->count()) {
            throw new RuntimeException("Не все типы кухни найдены для {$place->slug}.");
        }

        $place->tags()->sync($tagIds->all());
        $place->cuisineTypes()->sync($cuisineIds->all());
    }

    private function syncPlaceImages(Place $place, array $images): void
    {
        $syncedUrls = [];

        foreach ($images as $index => $image) {
            $place->images()->updateOrCreate(
                ['url' => $image['url']],
                [
                    'alt' => $image['alt'],
                    'title' => $image['title'],
                    'is_cover' => $image['isCover'] ?? false,
                    'sort_order' => $image['sortOrder'] ?? $index,
                ]
            );
            $syncedUrls[] = $image['url'];
        }

        $place->images()
            ->when($syncedUrls !== [], fn ($query) => $query->whereNotIn('url', $syncedUrls))
            ->delete();
    }
}
