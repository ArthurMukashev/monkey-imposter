<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class CorsTest extends TestCase
{
    public function test_preflight_allows_kiosk_origin(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'http://localhost',
            'Access-Control-Request-Method' => 'GET',
        ])->options('/api/v1/health');

        $response->assertHeader('Access-Control-Allow-Origin', 'http://localhost');
    }
}
