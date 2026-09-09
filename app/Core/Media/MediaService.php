<?php

namespace App\Core\Media;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

/**
 * Media boundary (foundation).
 *
 * Phase 1A establishes only:
 *  - collection → MIME allowlist validation;
 *  - max upload size and raster dimension caps;
 *  - a single storage-disk boundary (media disk, swappable to object storage).
 *
 * Variant generation, responsive images and the media registry arrive with the
 * Media domain phase (docs/media-architecture.md).
 */
class MediaService
{
    public function __construct(
        private readonly string $disk = 'media',
    ) {
    }

    /**
     * Validate an upload against its collection rules.
     *
     * @throws InvalidArgumentException when the file is not acceptable
     */
    public function validate(UploadedFile $file, string $collection = 'default'): bool
    {
        $allowed = config("media.collections.$collection", config('media.collections.default', []));
        $maxBytes = (int) config('media.max_upload_bytes', 12 * 1024 * 1024);

        $mime = $file->getMimeType();

        if (! in_array($mime, $allowed, true)) {
            throw new InvalidArgumentException("File type [{$mime}] is not allowed for collection [{$collection}].");
        }

        if ($file->getSize() > $maxBytes) {
            throw new InvalidArgumentException('File exceeds the maximum upload size.');
        }

        if ($this->isRaster($mime)) {
            [$width, $height] = $this->dimensions($file);

            if ($width > config('media.max_dimensions.width') || $height > config('media.max_dimensions.height')) {
                throw new InvalidArgumentException('Image dimensions exceed the allowed maximum.');
            }
        }

        return true;
    }

    /**
     * The storage disk used for media files.
     */
    public function disk(): string
    {
        return $this->disk;
    }

    /**
     * Resolve the filesystem for the media disk.
     */
    public function filesystem(): Filesystem
    {
        return Storage::disk($this->disk());
    }

    private function isRaster(string $mime): bool
    {
        return str_starts_with($mime, 'image/');
    }

    /**
     * @return array{0: int, 1: int} [width, height] resolved from the actual
     *         file contents (never trusting the declared MIME alone).
     */
    private function dimensions(UploadedFile $file): array
    {
        $real = $file->getRealPath();

        if ($real === false) {
            throw new InvalidArgumentException('Unable to read uploaded file.');
        }

        $size = @getimagesize($real);

        if ($size === false) {
            throw new InvalidArgumentException('Uploaded image could not be decoded.');
        }

        return [(int) $size[0], (int) $size[1]];
    }
}
