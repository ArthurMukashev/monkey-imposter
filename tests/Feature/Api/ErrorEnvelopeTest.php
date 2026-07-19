<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorEnvelopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_not_found_returns_contract_error_envelope(): void
    {
        $response = $this->getJson('/api/v1/places/no-such-place');

        $response->assertNotFound()
            ->assertJsonStructure(['error' => ['code', 'message', 'details']]);
    }

    public function test_internal_error_returns_contract_error_envelope(): void
    {
        \Route::middleware('api')->get('/api/v1/__boom', function () {
            throw new \RuntimeException('boom');
        });

        $response = $this->getJson('/api/v1/__boom');

        $response->assertStatus(500)
            ->assertJsonStructure(['error' => ['code', 'message', 'details']]);
    }
}
