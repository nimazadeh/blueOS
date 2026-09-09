<?php

namespace App\Core\Slug;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * SEO-friendly, stable, unique slugs (foundation).
 *
 * Rules (see docs/information-architecture.md):
 *  - lowercase, ASCII kebab-case (unicode transliterated);
 *  - unique per resource type (suffix -2, -3 … on collision);
 *  - never shortened below readability; max length enforced by caller.
 *
 * Full rules + redirect handling arrive with the domain phases. This class is
 * deliberately dependency-free (no model coupling) so tests stay cheap.
 */
class SlugService
{
    /**
     * Convert an arbitrary string into a slug candidate.
     */
    public function slugify(string $value): string
    {
        $slug = Str::slug($value, '-', 'en');

        return $slug === '' ? 'item' : $slug;
    }

    /**
     * Build a unique slug for a value against a model's slug column.
     */
    public function uniqueFor(string $value, string $modelClass, ?int $ignoreId = null, string $column = 'slug'): string
    {
        $base = $this->slugify($value);
        $candidate = $base;
        $suffix = 2;

        while ($this->exists($modelClass, $candidate, $ignoreId, $column)) {
            $candidate = $base.'-'.$suffix++;
        }

        return $candidate;
    }

    /**
     * Alias for readability across domains.
     *
     * @param  class-string<Model>  $modelClass
     */
    public function make(string $value, string $modelClass, ?int $ignoreId = null, string $column = 'slug'): string
    {
        return $this->uniqueFor($value, $modelClass, $ignoreId, $column);
    }

    private function exists(string $modelClass, string $slug, ?int $ignoreId, string $column): bool
    {
        $query = $modelClass::query()->where($column, $slug);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->exists();
    }
}
