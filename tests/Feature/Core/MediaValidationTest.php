<?php

use App\Core\Media\MediaService;
use Illuminate\Http\UploadedFile;

it('accepts an allowed image for the covers collection', function () {
    $file = UploadedFile::fake()->image('cover.jpg', 1200, 800);

    expect(app(MediaService::class)->validate($file, 'covers'))->toBeTrue();
});

it('rejects a disallowed mime type with a clear message', function () {
    $file = UploadedFile::fake()->create('notes.txt', 100, 'text/plain');

    try {
        app(MediaService::class)->validate($file, 'covers');
        $this->fail('Expected InvalidArgumentException');
    } catch (InvalidArgumentException $e) {
        expect($e->getMessage())->toContain('not allowed');
    }
});

it('rejects files above the configured size limit', function () {
    $file = UploadedFile::fake()->image('huge.jpg', 4000, 4000);

    // Fake files lie about size; use create with an explicit size via config.
    config(['media.max_upload_bytes' => 10]);

    try {
        app(MediaService::class)->validate($file, 'covers');
        $this->fail('Expected InvalidArgumentException');
    } catch (InvalidArgumentException $e) {
        expect($e->getMessage())->toContain('maximum upload size');
    }
});

it('exposes the configured media disk', function () {
    config(['media.disk' => 'media']);

    expect(app(MediaService::class)->disk())->toBe('media');
});
