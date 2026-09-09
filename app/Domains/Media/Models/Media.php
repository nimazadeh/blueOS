<?php

namespace App\Domains\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * Media registry entry (morph attachment).
 *
 * The binary lives on the `media` disk (private by default); public delivery
 * is served through App\Http\Controllers\Public\MediaController for media
 * attached to published entities only.
 *
 * @property string $path  relative path on the media disk, e.g. 2026/09/uuid.jpg
 */
class Media extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'model_type',
        'model_id',
        'collection',
        'filename',
        'path',
        'disk',
        'mime_type',
        'size',
        'width',
        'height',
        'alt_text',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Resolve a public URL for this media entry (disk URL or null when the
     * disk has no public URL configured).
     */
    public function url(): ?string
    {
        $disk = Storage::disk($this->disk);

        if ($disk->url($this->path) === '') {
            return null;
        }

        return $disk->url($this->path);
    }

    /**
     * Whether the attached entity currently allows public display.
     */
    public function isPubliclyVisible(): bool
    {
        $model = $this->model;

        return $model !== null && $model->status === 'published';
    }
}
