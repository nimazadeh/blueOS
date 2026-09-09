<?php

namespace App\Core\Seo;

/**
 * Immutable per-page SEO metadata value object.
 *
 * Produced by MetaResolver and consumed by the layout's head component.
 * Keep this class free of HTTP/DB dependencies.
 */
final class Meta
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly ?string $canonical = null,
        public readonly string $robots = 'index,follow',
        public readonly ?string $ogType = 'website',
        public readonly ?string $ogTitle = null,
        public readonly ?string $ogDescription = null,
        public readonly ?string $ogImage = null,
        public readonly ?string $twitterCard = 'summary_large_image',
        public readonly ?string $twitterTitle = null,
        public readonly ?string $twitterDescription = null,
        public readonly ?string $twitterImage = null,
    ) {
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'canonical' => $this->canonical,
            'robots' => $this->robots,
            'og_type' => $this->ogType,
            'og_title' => $this->ogTitle ?? $this->title,
            'og_description' => $this->ogDescription ?? $this->description,
            'og_image' => $this->ogImage,
            'twitter_card' => $this->twitterCard,
            'twitter_title' => $this->twitterTitle ?? $this->title,
            'twitter_description' => $this->twitterDescription ?? $this->description,
            'twitter_image' => $this->twitterImage,
        ];
    }
}
