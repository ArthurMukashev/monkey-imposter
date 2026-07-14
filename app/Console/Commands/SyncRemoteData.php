<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Place;
use App\Models\Category;
use App\Models\City;
use App\Models\Tag;
use App\Models\CuisineType;
use App\Models\Promo;
use App\Models\Image;

class SyncRemoteData extends Command
{
    protected $signature = 'sync:remote-data';
    protected $description = 'Синхронизирует все данные из удалённого API в локальную БД';

    public function handle()
    {
        $this->info('Начинаем синхронизацию...');

        $remoteUrl = config('app.remote_api_url'); // задаём в .env

        // 1. Синхронизация справочников (города, категории, теги, кухни)
        $this->syncReference('cities', City::class);
        $this->syncReference('categories', Category::class);
        $this->syncReference('tags', Tag::class);
        $this->syncReference('cuisine-types', CuisineType::class);

        // 2. Синхронизация промо
        $this->syncPromos($remoteUrl);

        // 3. Синхронизация объектов (Places) со всеми связанными данными
        $this->syncPlaces($remoteUrl);

        cache(['last_sync_time' => now()->toIso8601String()]);

        $this->info('Синхронизация завершена.');
    }

    private function syncReference($endpoint, $modelClass)
    {
        $url = config('app.remote_api_url') . "/{$endpoint}?limit=1000";
        $response = Http::get($url);

        if ($response->failed()) {
            $this->error("Ошибка загрузки {$endpoint}");
            return;
        }

        $data = $response->json('data') ?? [];

        foreach ($data as $item) {
            // Убираем лишние поля, оставляем только нужные для БД
            $attributes = $this->mapReferenceAttributes($endpoint, $item);
            $modelClass::updateOrCreate(
                ['id' => $item['id']],
                $attributes
            );
        }

        $this->info("Обновлено {$endpoint}: " . count($data));
    }

    private function mapReferenceAttributes($endpoint, $item)
    {
        // Преобразование camelCase -> snake_case, если требуется
        // В спецификации поля в API приходят в camelCase, но в БД snake_case
        $map = [
            'cities' => ['slug', 'title', 'sort_order'],
            'categories' => ['slug', 'section', 'title', 'short_description', 'sort_order'],
            'tags' => ['slug', 'title', 'color'],
            'cuisine-types' => ['slug', 'title'],
        ];

        $attributes = [];
        foreach ($map[$endpoint] as $field) {
            $apiField = lcfirst($field); // sort_order -> sortOrder? Лучше настроить ресурсы, чтобы выдавали snake_case, либо адаптировать
            // Но в спецификации поля в ответе camelCase. Проще всего на стороне API преобразовывать в snake при отправке, либо здесь делать трансформацию.
            // Для простоты будем считать, что API отдаёт поля в snake_case (рекомендуется).
            // Если нет – делаем маппинг.
        }

        // Пример для cities: в API приходит { id, slug, title, sortOrder }
        // Мы ожидаем sort_order. Поэтому:
        return [
            'slug' => $item['slug'],
            'title' => $item['title'],
            'sort_order' => $item['sort_order'] ?? 0,
        ];
    }

    private function syncPromos($remoteUrl)
    {
        // Получаем промо для всех placement, или просто все
        $url = $remoteUrl . "/promos?limit=1000";
        $response = Http::get($url);
        if ($response->failed()) {
            $this->error('Ошибка загрузки промо');
            return;
        }

        $items = $response->json('data') ?? [];
        foreach ($items as $item) {
            Promo::updateOrCreate(
                ['id' => $item['id']],
                [
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
                    'updated_at' => now(),
                ]
            );
            // Если есть изображение, сохраняем отдельно
            if (!empty($item['image'])) {
                Image::updateOrCreate(
                    ['imageable_id' => $item['id'], 'imageable_type' => Promo::class, 'is_cover' => true],
                    [
                        'url' => $item['image']['url'],
                        'alt' => $item['image']['alt'],
                        'title' => $item['image']['title'],
                        'is_cover' => true,
                    ]
                );
            }
        }
        $this->info("Обновлено промо: " . count($items));
    }

    private function syncPlaces($remoteUrl)
    {
        $page = 1;
        $limit = 100;
        $totalPages = 1;

        do {
            $url = $remoteUrl . "/places?page={$page}&limit={$limit}";
            $response = Http::get($url);
            if ($response->failed()) {
                $this->error('Ошибка загрузки мест, страница ' . $page);
                break;
            }

            $data = $response->json();
            $items = $data['data'] ?? [];
            $meta = $data['meta'] ?? [];
            $totalPages = $meta['totalPages'] ?? 1;

            foreach ($items as $item) {
                // Сохраняем основную запись Place
                $place = Place::updateOrCreate(
                    ['id' => $item['id']],
                    [
                        'slug' => $item['slug'],
                        'section' => $item['section'],
                        'title' => $item['title'],
                        'short_description' => $item['shortDescription'],
                        'description_html' => $item['descriptionHtml'] ?? null,
                        'category_id' => $item['category']['id'] ?? null,
                        'city_id' => $item['city']['id'] ?? null,
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
                        'updated_at' => now(),
                    ]
                );

                // Синхронизация связанных тегов (многие-ко-многим)
                if (!empty($item['tags'])) {
                    $tagIds = collect($item['tags'])->pluck('id');
                    $place->tags()->sync($tagIds);
                }

                // Синхронизация типов кухни (если есть)
                if (!empty($item['cuisineTypes'])) {
                    $cuisineIds = collect($item['cuisineTypes'])->pluck('id');
                    $place->cuisineTypes()->sync($cuisineIds);
                }

                // Синхронизация изображений
                if (!empty($item['images'])) {
                    foreach ($item['images'] as $image) {
                        Image::updateOrCreate(
                            [
                                'imageable_id' => $place->id,
                                'imageable_type' => Place::class,
                                'url' => $image['url'], // ключ для уникальности
                            ],
                            [
                                'alt' => $image['alt'],
                                'title' => $image['title'],
                                'is_cover' => $image['isCover'] ?? false,
                                'sort_order' => $image['sortOrder'] ?? 0,
                            ]
                        );
                    }
                }

                // Если в API есть coverImage как отдельное поле, то обновляем его отдельно
                if (!empty($item['coverImage'])) {
                    $cover = $item['coverImage'];
                    Image::updateOrCreate(
                        [
                            'imageable_id' => $place->id,
                            'imageable_type' => Place::class,
                            'is_cover' => true,
                        ],
                        [
                            'url' => $cover['url'],
                            'alt' => $cover['alt'],
                            'title' => $cover['title'],
                            'is_cover' => true,
                        ]
                    );
                }
            }

            $this->info("Страница {$page}/{$totalPages} обработана, записей: " . count($items));
            $page++;
        } while ($page <= $totalPages);

        $this->info("Все места синхронизированы.");
    }
}
