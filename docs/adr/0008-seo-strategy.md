# ADR-008 — SEO Strategy

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

SEO must be "built in from the beginning", not an afterthought. Content is
per-locale, pages are server-rendered, slugs must be stable, and site structure
(products vs portfolio vs lab) must map to honest, crawlable URLs.

## Decision

- **Server-rendered metadata** through one `MetaResolver` + `HeadMeta` value object +
  one Blade component token; no per-page hardcoded SEO, no client-side injection.
- **Morph `seo_meta` table** (per entity, per locale) with fallback chain:
  `seo_meta → content fields → site defaults`; `noindex` on non-published; per-page
  static-page records included.
- **Clean URLs:** slugs only, no IDs; slug changes create 301 `redirects` records
  via SlugService/observer; redirect lookup on 404.
- **Locale SEO:** canonical per-locale URL + `hreflang` alternates + `x-default`;
  prefixed locale routes; default locale unprefixed.
- **Sitemap:** per-locale XML generated on publish/unpublish events to storage +
  cached; `/sitemap.xml` index; robots.txt static/config-driven; admin + demo paths
  excluded.
- **Structured data:** honest per type (Organization, Product with real offers only,
  Service, Article, BreadcrumbList); never fabricate ratings, authors, or metrics.
- **Index rules:** `index,follow` published; `noindex,follow` filters/search;
  `noindex,nofollow` + header on admin/errors.

## Alternatives considered

1. **SEO fields inside content tables only** — rejected: static pages would need
   dummy records; morph record keeps one model plus history-free override.
2. **Client-side SEO (meta via JS)** — rejected: crawlers/CSR risk; conflicts with
   SSR decision.
3. **Package (spatie/laravel-seo, artesaos) evaluation** — deferred: small enough
   surface to build the resolver with full test control; package may be introduced
   later if structured-data needs grow (ADR required).
4. **Slug = ID-based URLs** — rejected: unstable, poor UX, migration-unfriendly.

## Consequences

- ✅ Crawlable, locale-correct, honest SEO surface with minimal custom infrastructure.
- ✅ Slug migration safety via 301s; redirect management in admin.
- ⚠️ Per-locale SEO editing increases admin form complexity (tabbed locale fields) —
  documented as required UX.
- ⚠️ Sitemap cache invalidation events must cover all content domains (tested).
