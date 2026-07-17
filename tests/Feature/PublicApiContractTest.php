<?php

namespace Tests\Feature;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PublicApiContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_places_use_the_public_list_envelope_and_apply_catalog_filters(): void
    {
        $response = $this->getJson('/api/v1/places?section=active&category=culture&q=%D1%84%D0%B8%D0%BB%D0%B0%D1%80%D0%BC%D0%BE%D0%BD%D0%B8%D0%B8&limit=5');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'orenburgskaya-filarmoniya')
            ->assertJsonPath('meta.page', 1)
            ->assertJsonPath('meta.limit', 5)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('meta.totalPages', 1)
            ->assertJsonMissingPath('links');

        $this->getJson('/api/v1/places?section=active&category=culture')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.category.slug', 'culture')
            ->assertJsonPath('data.1.category.slug', 'culture');
    }

    public function test_reference_and_promo_lists_use_the_same_public_envelope(): void
    {
        foreach ([
            '/api/v1/categories?section=active',
            '/api/v1/cities',
            '/api/v1/tags',
            '/api/v1/cuisine-types',
            '/api/v1/promos?placement=kiosk-home',
        ] as $path) {
            $this->getJson($path)
                ->assertOk()
                ->assertJsonStructure([
                    'data',
                    'meta' => ['page', 'limit', 'total', 'totalPages'],
                ])
                ->assertJsonMissingPath('links');
        }

        $this->getJson('/api/v1/promos?placement=kiosk-home')
            ->assertJsonPath('data.0.target.href', '/routes/nacionalnaya-derevnya');
    }

    public function test_local_health_reports_configured_source_and_sync_timestamp(): void
    {
        config()->set('app.api_source', 'local');
        Cache::put('last_sync_time', '2026-07-17T09:30:00+05:00');

        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('data.status', 'ok')
            ->assertJsonPath('data.source', 'local')
            ->assertJsonPath('data.lastSyncAt', '2026-07-17T09:30:00+05:00');
    }

    public function test_schedule_preserves_optional_end_fields(): void
    {
        $place = Place::query()->where('slug', 'orenburgskaya-filarmoniya')->firstOrFail();
        $place->update([
            'schedule' => [
                'date' => '2026-07-20',
                'time' => '19:00',
                'endDate' => null,
                'endTime' => '21:00',
                'timezone' => '+05:00',
            ],
        ]);

        $this->getJson('/api/v1/places/orenburgskaya-filarmoniya')
            ->assertJsonPath('data.seo.canonicalPath', '/events/orenburgskaya-filarmoniya')
            ->assertOk()
            ->assertJsonPath('data.schedule.date', '2026-07-20')
            ->assertJsonPath('data.schedule.time', '19:00')
            ->assertJsonPath('data.schedule.endDate', null)
            ->assertJsonPath('data.schedule.endTime', '21:00')
            ->assertJsonPath('data.schedule.timezone', '+05:00');
    }
}
