<?php

namespace Tests\Feature;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RemoteDataSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_remote_public_contract_is_synchronized_into_local_api_models(): void
    {
        config()->set('app.remote_api_url', 'https://remote.test/api/v1');

        Http::fake([
            'https://remote.test/api/v1/cities*' => Http::response([
                'data' => [[
                    'id' => '11111111-1111-4111-8111-111111111111',
                    'slug' => 'orenburg',
                    'title' => 'Оренбург',
                ]],
                'meta' => $this->meta(),
            ]),
            'https://remote.test/api/v1/categories*' => Http::response([
                'data' => [[
                    'id' => '22222222-2222-4222-8222-222222222222',
                    'slug' => 'culture',
                    'section' => 'active',
                    'title' => 'Культура',
                    'shortDescription' => 'События культуры.',
                ]],
                'meta' => $this->meta(),
            ]),
            'https://remote.test/api/v1/tags*' => Http::response([
                'data' => [[
                    'id' => '33333333-3333-4333-8333-333333333333',
                    'slug' => 'vecherniy',
                    'title' => 'Вечерний',
                    'color' => '#5E548E',
                ]],
                'meta' => $this->meta(),
            ]),
            'https://remote.test/api/v1/cuisine-types*' => Http::response([
                'data' => [[
                    'id' => '44444444-4444-4444-8444-444444444444',
                    'slug' => 'mestnaya-kuhnya',
                    'title' => 'Местная кухня',
                ]],
                'meta' => $this->meta(),
            ]),
            'https://remote.test/api/v1/promos*' => Http::response([
                'data' => [[
                    'id' => '66666666-6666-4666-8666-666666666666',
                    'placement' => 'kiosk-home',
                    'priority' => 100,
                    'activeFrom' => '2026-07-01T00:00:00+05:00',
                    'activeUntil' => '2026-08-01T00:00:00+05:00',
                    'title' => 'Культурный вечер',
                    'teaser' => 'Главное событие недели.',
                    'image' => [
                        'url' => 'https://images.example.test/promo.jpg',
                        'alt' => 'Зрительный зал',
                        'title' => 'Культурный вечер',
                        'isCover' => true,
                    ],
                    'target' => [
                        'type' => 'place',
                        'slug' => 'remote-event',
                        'url' => null,
                    ],
                ]],
                'meta' => $this->meta(),
            ]),
            'https://remote.test/api/v1/places/remote-event' => Http::response([
                'data' => $this->placeDetails(),
            ]),
            'https://remote.test/api/v1/places*' => Http::response([
                'data' => [$this->placeListItem()],
                'meta' => $this->meta(),
            ]),
        ]);

        $this->artisan('sync:remote-data')->assertSuccessful();

        $this->assertDatabaseHas('categories', [
            'id' => '22222222-2222-4222-8222-222222222222',
            'slug' => 'culture',
            'section' => 'active',
            'short_description' => 'События культуры.',
        ]);
        $this->assertDatabaseHas('tags', [
            'id' => '33333333-3333-4333-8333-333333333333',
            'color' => '#5E548E',
        ]);
        $this->assertDatabaseHas('promos', [
            'id' => '66666666-6666-4666-8666-666666666666',
            'placement' => 'kiosk-home',
        ]);

        $place = Place::query()->findOrFail('55555555-5555-4555-8555-555555555555');

        $this->assertSame('Полное описание.', $place->description_html);
        $this->assertSame('ул. Советская, 1', $place->address);
        $this->assertSame('21:00', $place->schedule['endTime']);
        $this->assertSame(['33333333-3333-4333-8333-333333333333'], $place->tags()->pluck('tags.id')->all());
        $this->assertSame(
            ['44444444-4444-4444-8444-444444444444'],
            $place->cuisineTypes()->pluck('cuisine_types.id')->all()
        );
        $this->assertDatabaseHas('images', [
            'imageable_id' => $place->id,
            'url' => 'https://images.example.test/place.jpg',
            'is_cover' => true,
        ]);
        $this->assertTrue(Cache::has('last_sync_time'));

        Http::assertSent(
            fn (Request $request): bool => $request->url() === 'https://remote.test/api/v1/places/remote-event'
        );
    }

    private function meta(): array
    {
        return [
            'page' => 1,
            'limit' => 100,
            'total' => 1,
            'totalPages' => 1,
        ];
    }

    private function placeListItem(): array
    {
        return [
            'id' => '55555555-5555-4555-8555-555555555555',
            'slug' => 'remote-event',
            'section' => 'active',
            'title' => 'Удалённое событие',
            'shortDescription' => 'Короткое описание.',
            'category' => [
                'id' => '22222222-2222-4222-8222-222222222222',
                'slug' => 'culture',
                'title' => 'Культура',
            ],
            'city' => [
                'id' => '11111111-1111-4111-8111-111111111111',
                'slug' => 'orenburg',
                'title' => 'Оренбург',
            ],
            'coordinates' => ['lat' => 51.7682, 'lng' => 55.0969],
            'coverImage' => [
                'url' => 'https://images.example.test/place.jpg',
                'alt' => 'Сцена',
                'title' => 'Удалённое событие',
                'isCover' => true,
            ],
            'tags' => [[
                'id' => '33333333-3333-4333-8333-333333333333',
                'slug' => 'vecherniy',
                'title' => 'Вечерний',
                'color' => '#5E548E',
            ]],
            'schedule' => [
                'date' => '2026-07-20',
                'time' => '19:00',
                'endDate' => '2026-07-20',
                'endTime' => '21:00',
                'timezone' => '+05:00',
            ],
        ];
    }

    private function placeDetails(): array
    {
        return [
            ...$this->placeListItem(),
            'descriptionHtml' => 'Полное описание.',
            'address' => 'ул. Советская, 1',
            'workingHours' => 'По афише',
            'averageBill' => null,
            'cuisineTypes' => [[
                'id' => '44444444-4444-4444-8444-444444444444',
                'slug' => 'mestnaya-kuhnya',
                'title' => 'Местная кухня',
            ]],
            'menuHtml' => null,
            'images' => [[
                'url' => 'https://images.example.test/place.jpg',
                'alt' => 'Сцена',
                'title' => 'Удалённое событие',
                'isCover' => true,
            ]],
            'seo' => [
                'title' => 'Удалённое событие',
                'description' => 'Короткое описание.',
                'canonicalPath' => '/events/remote-event',
            ],
        ];
    }
}
