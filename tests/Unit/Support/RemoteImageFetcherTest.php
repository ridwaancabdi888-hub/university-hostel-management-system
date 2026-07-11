<?php

namespace Tests\Unit\Support;

use App\Support\RemoteImageFetcher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class RemoteImageFetcherTest extends TestCase
{
    /**
     * A minimal valid 1x1 transparent PNG.
     */
    private const TINY_PNG = "\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15\xc4\x89\x00\x00\x00\nIDATx\x9cc\x00\x01\x00\x00\x05\x00\x01\r\n-\xb4\x00\x00\x00\x00IEND\xaeB`\x82";

    public function test_it_rejects_a_url_pointing_to_a_private_address(): void
    {
        $this->expectException(InvalidArgumentException::class);

        RemoteImageFetcher::download('http://127.0.0.1/image.png', 'room-photos');
    }

    public function test_it_rejects_a_non_http_scheme(): void
    {
        $this->expectException(InvalidArgumentException::class);

        RemoteImageFetcher::download('ftp://example.com/image.png', 'room-photos');
    }

    public function test_it_rejects_a_response_that_is_not_an_image(): void
    {
        Http::fake([
            'example.com/*' => Http::response('<html>not an image</html>', 200, ['Content-Type' => 'text/html']),
        ]);

        $this->expectException(InvalidArgumentException::class);

        RemoteImageFetcher::download('https://example.com/page.html', 'room-photos');
    }

    public function test_it_downloads_and_stores_a_valid_image(): void
    {
        Storage::fake('public');

        Http::fake([
            'example.com/*' => Http::response(self::TINY_PNG, 200, ['Content-Type' => 'image/png']),
        ]);

        $path = RemoteImageFetcher::download('https://example.com/photo.png', 'room-photos');

        $this->assertStringStartsWith('room-photos/', $path);
        $this->assertStringEndsWith('.png', $path);
        Storage::disk('public')->assertExists($path);
    }
}
