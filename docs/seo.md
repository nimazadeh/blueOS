# Blue Studio OS — SEO Architecture

**Status:** Accepted (Phase 0). SEO is built into rendering, not bolted on later.

---

## 1. Strategy summary (ADR-008)

- **Server-rendered metadata:** every public page resolves its `<title>`,
  `meta[name=description]`, canonical, Open Graph, Twitter, and structured data
  through `MetaResolver` — never hardcoded in Blade, never client-injected.
- **Morph-based SEO records:** optional `seo_meta` per content entity (per locale;
  see [`database.md`](database.md)) with sensible fallbacks from content fields.
- **Cacheable, generated assets:** sitemap generated to storage on
  publish/unpublish (not per-request); robots.txt static/config-driven.
- **Clean URLs:** no public IDs; slugs only; stable + redirects.

## 2. Metadata contract per page

| Element | Source |
|---|---|
| Title | `seo_meta.meta_title` → content title + site suffix (configurable `site.seo.title_suffix`) |
| Description | `seo_meta.meta_description` → content summary (≤160 chars) |
| Canonical | explicit `seo_meta.canonical_url` → current route (locale-aware, https, no trailing slash) |
| OG title/description/image | seo_meta → fallback (title, excerpt, cover/og variant) |
| Twitter card | `summary_large_image` (or summary) — same content |
| Robots | `noindex` if status != published or seo_meta.noindex; `archive` policy config |
| Structured data | per content type (§4) or `schema_json` override |

`MetaResolver` outputs one `HeadMeta` value object consumed by the layout:
`<x-seo.head :meta="$meta">` — one component, zero page-level SEO logic.

## 3. URL / canonical rules

- Protocol: HTTPS always; canonical never includes query params except when
  canonical is an intentional pagination/filter page (avoid index of filter pages —
  `noindex,follow` for `?category=` filter views where duplicate content risk exists).
- Locale: canonical points to the same locale URL; `hreflang` alternates generated
  for all enabled locales; `x-default` = default locale.
- Trailing slash: none (301 enforce at nginx).
- www vs bare: one canonical host enforced at nginx + config.
- Case: paths lowercase (normalized at nginx/route binding).
- IDs: never in URLs (slug only).

## 4. Structured data

| Page | Schema |
|---|---|
| Home | `Organization` (+ `WebSite`) |
| Product detail | `Product` (with `offers` only if price/marketplace exists; `image`, `description`, `brand`, `aggregateRating` **only if real**) |
| Portfolio detail | `CreativeWork`/`Article` + `AboutPage` pattern (no fake ratings) |
| Service detail | `Service` (+ `Provider` ref) |
| Insight detail | `Article` (headline, datePublished, dateModified, author if real, image) |
| Lab detail | `Article`/`CreativeWork` (generic; no product schema) |
| Breadcrumb | `BreadcrumbList` on all index→detail pages |
| FAQ (if added) | `FAQPage` only for genuinely asked questions |

**Rule: never emit schema for data we don't truthfully have** (fake ratings,
fictitious authors, invented metrics). No schema for 404s/admin/forms.

## 5. Sitemap & robots

- `/sitemap.xml` → index of per-locale sitemaps (`/sitemap-en.xml`, `/sitemap-fa.xml`).
- Contents: published products, portfolio, lab, services, insights (+ paginated
  insights index), static pages; `lastmod` = `updated_at`/`published_at`; never
  drafts/admin.
- Generated: `php artisan sitemap:generate` on publish/unpublish events; served from
  storage/public cache with 1h cache; daily scheduled fallback.
- `/robots.txt`: allow all (public), deny `/admin`, `/demo/*` internal paths,
  sitemap ref per enabled locale; generated config-driven.
- `noindex` respected for archived/draft content at render and sitemap exclusion.

## 6. Redirects

- `redirects` table (from_path unique, to_path, 301 default, enabled, reason).
- Automatic redirect creation when a published slug changes (SlugService →
  RedirectObserver).
- Admin CRUD for redirects (Blue Control SEO section) with validation (no self-loop,
  external paths allowed only to configured domains, 301 only in v1).
- 404 handler checks redirects before 404 (middleware/cache-friendly lookup);
  unknown → branded 404 with helpful nav.

## 7. Index/control rules (summary)

| Case | robots |
|---|---|
| Published content | `index,follow` |
| Draft/archived | `noindex,nofollow` (render blocked anyway) |
| Filter query views | `noindex,follow` |
| Admin (all) | `noindex,nofollow` + `X-Robots-Tag` |
| Search results (Phase 4) | `noindex,follow` |
| Paginated content | `index,follow` (canonical to self) |

## 8. Performance relationships (CWV)

- Metadata is server-rendered (no JS SEO).
- Images for OG generated; cover variants used in `og:image` with absolute URL.
- `preload` of LCP hero image; CSP allows self/CDN images; structured data in HTML
  (not JSON-injected client).
- SSR keeps TTFB low; Blade fragment caching; sitemap cached.

## 9. SEO acceptance checklist

- [x] Per-page metadata via resolver (title/desc/canonical/OG/Twitter)
- [x] hreflang/alternate per locale, x-default
- [x] Structured data per content type, honest data rule
- [x] Sitemap (per-locale, cached, event-regenerated)
- [x] Robots.txt + noindex/no-follow rules
- [x] Redirects (301, slug change automation, admin CRUD)
- [x] Clean stable slugs, no public IDs
- [x] Breadcrumbs
- [x] Standards: no fake schema/metrics; admin unindexed

## 10. Deferred

- Search indexing strategy / full-text (Phase 4)
- Schema.org `ItemList` for index pages (Phase 4 with metrics)
- International content hreflang maintenance tooling (Phase 3 admin)
- RSS/Atom (Phase 3 optional)
