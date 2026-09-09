<?php

namespace App\Core\Media;

use App\Domains\Media\Models\Media;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

/**
 * Media boundary.
 *
 * Phase 1A: validation (MIME allowlist per collection, size + dimension caps)
 * and the storage-disk boundary (private `media` disk, swappable to object
 * storage). Phase 2 completes the registry path: attach/delete + metadata.
 *
 * Variant generation/responsive images remain later phases.
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
     * Store an upload and register it (optionally against a model).
     *
     * Library uploads (no model) stay unattached — they are never publicly
     * visible until attached to a published entity.
     *
     * @param  'covers'|'gallery'|string  $collection
     * @param  bool  $replace  delete existing files of the same collection
     *                         first (used for single-cover semantics)
     */
    public function attach(
        ?Model $model,
        UploadedFile $file,
        string $collection = 'covers',
        bool $replace = false,
        ?string $altText = null,
    ): Media {
        $this->validate($file, $collection);

        if ($replace && $model !== null) {
            $model->media()
                ->where('collection', $collection)
                ->get()
                ->each(fn (Media $existing) => $this->deleteFile($existing));
        }

        $extension = $file->guessExtension()
            ?: strtolower((string) pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));

        if ($extension === '' || $extension === 'bin') {
            $extension = 'bin';
        }

        $baseName = Str::limit(
            (string) pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            120,
            '',
        );

        $path = $file->store(date('Y/m').'/'.Str::uuid(), $this->disk());

        if ($path === false) {
            throw new RuntimeException('Media storage failed.');
        }

        [$width, $height] = $this->dimensionsOfStored($path);

        $media = Media::query()->create([
            'model_type' => $model?->getMorphClass(),
            'model_id' => $model?->getKey(),
            'collection' => $collection,
            'filename' => $baseName.'.'.$extension,
            'path' => $path,
            'disk' => $this->disk(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => (int) $file->getSize(),
            'width' => $width,
            'height' => $height,
            'alt_text' => $altText,
        ]);

        return $media;
    }

    /**
     * Remove the binary and the registry row.
     */
    public function deleteFile(Media $media): void
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();
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

    /**
     * @return array{0: int|null, 1: int|null} dimensions of the stored file
     */
    private function dimensionsOfStored(string $path): array
    {
        $real = Storage::disk($this->disk())->path($path);
        $size = @getimagesize($real);

        if ($size === false) {
            return [null, null];
        }

        return [(int) $size[0], (int) $size[1]];
    }
}
