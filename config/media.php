<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media service
    |--------------------------------------------------------------------------
    |
    | Foundation configuration for App\Core\Media\MediaService. Phase 1A only
    | establishes validation and the storage boundary — variant generation,
    | responsive images and the media registry arrive with the Media domain
    | phase (see docs/media-architecture.md).
    |
    */

    'disk' => env('MEDIA_DISK', 'media'),

    'max_upload_bytes' => (int) env('MEDIA_MAX_UPLOAD_BYTES', 12 * 1024 * 1024),

    // Collection → allowed MIME types. SVG stays disabled by default (XSS /
    // entity risk); it is opt-in later with sanitization.
    'collections' => [
        'covers' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
        'gallery' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
        'posts' => ['image/jpeg', 'image/png', 'image/webp'],
        'documents' => ['application/pdf'],
        'default' => ['image/jpeg', 'image/png', 'image/webp'],
    ],

    'max_dimensions' => [
        'width' => 8000,
        'height' => 8000,
    ],

];
