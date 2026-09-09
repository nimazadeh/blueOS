<?php

namespace App\Core\Seo;

use Illuminate\Support\Str;

/**
 * Resolves per-page SEO metadata (foundation).
 *
 * Fallback chain (full version arrives with the SEO domain):
 *   explicit route/page values → entity SEO record (later) → site defaults.
 *
 * Every public page must receive a Meta instance through this resolver —
 * views never build SEO metadata themselves.
 */
class MetaResolver
{
    /**
     * @param  array<string, mixed>  $overrides  title, description, canonical,
     *          robots, og_type, og_title, og_description, og_image,
     *          twitter_card, twitter_title, twitter_description, twitter_image
     */
    public function resolve(string $title, array $overrides = []): Meta
    {
        $defaults = config('blue.seo.defaults', []);
        $siteName = config('blue.site.name', 'Blue Studio');

        $baseTitle = rtrim($title, ' -');
        $suffix = (string) ($defaults['title_suffix'] ?? $siteName);

        $resolvedTitle = $baseTitle === $suffix
            ? $baseTitle
            : $baseTitle.' — '.$suffix;

        $canonical = $overrides['canonical'] ?? $this->defaultCanonical();

        return new Meta(
            title: $this->clean($resolvedTitle, 70),
            description: $this->clean(
                (string) ($overrides['description'] ?? $defaults['description'] ?? ''),
                160,
            ),
            canonical: is_string($canonical) ? $canonical : null,
            robots: (string) ($overrides['robots'] ?? $defaults['robots'] ?? 'index,follow'),
            ogType: (string) ($overrides['og_type'] ?? $defaults['og_type'] ?? 'website'),
            ogTitle: isset($overrides['og_title']) ? (string) $overrides['og_title'] : null,
            ogDescription: isset($overrides['og_description']) ? (string) $overrides['og_description'] : null,
            ogImage: isset($overrides['og_image']) ? (string) $overrides['og_image'] : null,
            twitterCard: (string) ($overrides['twitter_card'] ?? $defaults['twitter_card'] ?? 'summary_large_image'),
            twitterTitle: isset($overrides['twitter_title']) ? (string) $overrides['twitter_title'] : null,
            twitterDescription: isset($overrides['twitter_description']) ? (string) $overrides['twitter_description'] : null,
            twitterImage: isset($overrides['twitter_image']) ? (string) $overrides['twitter_image'] : null,
        );
    }

    private function defaultCanonical(): ?string
    {
        $host = config('blue.seo.defaults.canonical_host')
            ?? parse_url((string) config('app.url'), PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return null;
        }

        return trim((string) config('app.url'), '/').'/'.ltrim(request()->path(), '/');
    }

    private function clean(string $value, int $limit): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');

        return Str::limit($value, $limit, '');
    }
}
