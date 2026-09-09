<?php

use App\Core\Media\MediaService;
use App\Domains\Media\Models\Media;
use App\Domains\Products\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Minimal valid 1x1 PNG (no GD dependency required in tests/CI).
 */
function pixelPng(string $name = 'pixel.png'): UploadedFile
{
    $png = base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
        true,
    );

    $path = tempnam(sys_get_temp_dir(), 'pix').'.png';
    file_put_contents($path, $png);

    return new UploadedFile($path, $name, 'image/png', null, true);
}

function fakeSvg(): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'pix').'.svg';
    file_put_contents($path, '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

    return new UploadedFile($path, 'payload.svg', 'image/svg+xml', null, true);
}

it('stores an image with metadata and serves it publicly for published entities', function () {
    Storage::fake('media');

    $product = Product::factory()->create();

    $media = app(MediaService::class)->attach(
        $product,
        pixelPng('cover.png'),
        collection: 'covers',
        altText: 'Product interface preview',
    );

    expect($media->width)->toBe(1)
        ->and($media->height)->toBe(1)
        ->and($media->collection)->toBe('covers')
        ->and($media->filename)->toBe('cover.png')
        ->and(Storage::disk('media')->exists($media->path))->toBeTrue();

    $this->get(route('media.show', $media))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');
});

it('does not serve media attached to unpublished entities', function () {
    Storage::fake('media');

    $product = Product::factory()->draft()->create();
    $media = app(MediaService::class)->attach($product, pixelPng());

    $this->get(route('media.show', $media))->assertNotFound();
});

it('does not serve unattached library uploads publicly', function () {
    Storage::fake('media');

    $media = app(MediaService::class)->attach(null, pixelPng(), collection: 'gallery');

    $this->get(route('media.show', $media))->assertNotFound();
});

it('rejects SVG uploads on the validation boundary', function () {
    Storage::fake('media');

    expect(fn () => app(MediaService::class)->attach(null, fakeSvg()))
        ->toThrow(InvalidArgumentException::class);

    $this->assertDatabaseCount('media', 0);
});

it('rejects SVG uploads through the admin upload form', function () {
    Storage::fake('media');
    $admin = roleUser('admin');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.media.store'), [
            'file' => fakeSvg(),
            'collection' => 'gallery',
        ])
        ->assertSessionHasErrors('file');

    $this->assertDatabaseCount('media', 0);
});

it('updates alt text and deletes media from the library', function () {
    Storage::fake('media');
    $admin = roleUser('admin');
    $media = app(MediaService::class)->attach(null, pixelPng(), collection: 'gallery');

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.media.update', $media), ['alt_text' => 'Meaningful alt text'])
        ->assertRedirect();

    expect($media->refresh()->alt_text)->toBe('Meaningful alt text');

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.media.destroy', $media))
        ->assertRedirect();

    expect(Storage::disk('media')->exists($media->path))->toBeFalse();
    $this->assertDatabaseMissing('media', ['id' => $media->id]);
});
