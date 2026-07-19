<?php

namespace Tests\Unit;

use App\Support\PublicImageUrl;
use Tests\TestCase;

class PublicImageUrlTest extends TestCase
{
    public function test_absolute_http_url_is_returned_as_is(): void
    {
        $url = 'http://cdn.example.test/images/cover.jpg';

        $this->assertSame($url, PublicImageUrl::for($url));
    }

    public function test_absolute_https_url_is_returned_as_is(): void
    {
        $url = 'https://cdn.example.test/images/cover.jpg';

        $this->assertSame($url, PublicImageUrl::for($url));
    }

    public function test_relative_storage_path_is_mapped_to_public_disk_url(): void
    {
        $result = PublicImageUrl::for('sync/cover.jpg');

        $this->assertNotNull($result);
        $this->assertStringContainsString('/storage/sync/cover.jpg', $result);
    }

    public function test_null_returns_null(): void
    {
        $this->assertNull(PublicImageUrl::for(null));
    }

    public function test_empty_string_returns_null(): void
    {
        $this->assertNull(PublicImageUrl::for(''));
    }
}
