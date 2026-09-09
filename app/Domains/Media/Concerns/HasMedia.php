<?php

namespace App\Domains\Media\Concerns;

use App\Domains\Media\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Morph media attachment for domain models.
 *
 * Recommended collections:
 *   covers   — one cover per entity (replaced on new upload)
 *   gallery  — additional screenshots/preview images (append-only)
 */
trait HasMedia
{
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model')->orderBy('created_at');
    }

    public function mediaFor(string $collection): MorphMany
    {
        return $this->media()->where('collection', $collection);
    }

    /**
     * Resolve the cover, preferring the already-loaded `media` relation so
     * eager-loaded entities never trigger an extra query per card.
     */
    public function cover(): ?Media
    {
        if ($this->relationLoaded('media')) {
            return $this->media->firstWhere('collection', 'covers');
        }

        return $this->mediaFor('covers')->first();
    }

    public function coverUrl(): ?string
    {
        return $this->cover()?->url();
    }

    /**
     * Gallery entries (loaded-relation aware, see cover()).
     */
    public function galleryMedia(): Collection
    {
        if ($this->relationLoaded('media')) {
            return $this->media->where('collection', 'gallery')->values();
        }

        return $this->mediaFor('gallery')->get();
    }
}
