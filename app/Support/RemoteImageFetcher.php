<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class RemoteImageFetcher
{
    private const MAX_BYTES = 5 * 1024 * 1024;

    private const EXTENSION_BY_MIME = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    /**
     * Download an image from a user-supplied URL and store it on the
     * "public" disk, returning the stored path.
     *
     * @throws InvalidArgumentException if the URL is unsafe or not an image
     */
    public static function download(string $url, string $directory): string
    {
        [$body, $extension] = self::fetchBytes($url);

        $path = "{$directory}/".Str::random(40).".{$extension}";

        Storage::disk('public')->put($path, $body);

        return $path;
    }

    /**
     * Download and validate an image from a user-supplied URL, returning its
     * raw bytes and file extension without storing it — lets a caller that
     * needs to write the same bytes to several disk paths (e.g. applying
     * one photo to many rooms at once) fetch the URL just once.
     *
     * Guards against SSRF (only http/https, rejects loopback/private/
     * reserved IP ranges, disables redirect-following since a redirect
     * could otherwise repoint an already-validated host at an internal
     * address) and against non-image or oversized responses. Reasonable
     * for this feature's actual exposure — the routes that call this are
     * already restricted to Admin/Warden — rather than exhaustive
     * SSRF-hardening for a public, untrusted-caller endpoint.
     *
     * @return array{0: string, 1: string} [raw bytes, file extension]
     *
     * @throws InvalidArgumentException if the URL is unsafe or not an image
     */
    public static function fetchBytes(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME);

        if (! $host || ! in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('Only http:// or https:// image URLs are supported.');
        }

        $ip = filter_var($host, FILTER_VALIDATE_IP) ? $host : gethostbyname($host);

        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            throw new InvalidArgumentException('That URL points to an address this app is not allowed to fetch.');
        }

        try {
            $response = Http::withOptions(['allow_redirects' => false])
                ->timeout(8)
                ->get($url);
        } catch (ConnectionException) {
            throw new InvalidArgumentException('Could not reach that URL.');
        }

        if (! $response->successful()) {
            throw new InvalidArgumentException('Could not download the image from that URL.');
        }

        $contentType = strtolower(trim(explode(';', $response->header('Content-Type') ?? '')[0]));

        if (! isset(self::EXTENSION_BY_MIME[$contentType])) {
            throw new InvalidArgumentException('That URL does not point to a supported image type (JPEG, PNG, WebP, or GIF).');
        }

        $body = $response->body();

        if (strlen($body) > self::MAX_BYTES) {
            throw new InvalidArgumentException('The image is too large (max 5MB).');
        }

        if (@getimagesizefromstring($body) === false) {
            throw new InvalidArgumentException('The downloaded file is not a valid image.');
        }

        return [$body, self::EXTENSION_BY_MIME[$contentType]];
    }
}
