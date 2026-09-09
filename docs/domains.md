# Blue Studio OS — Business Domains (Phase 2)

Five domains live under `app/Domains/*`. Each follows the same internal
layout (Laravel-compatible, modular monolith):

```
app/Domains/<Domain>/
├── Actions/          single-purpose invokable classes (write path / logic)
├── Http/Requests/    FormRequest validation + authorization
├── Models/           Eloquent models + relationships
└── Policies/         authorization policies
```

Migrations stay in `database/migrations` (framework convention) and are
grouped by timestamp blocks per domain (see `docs/database.md`). Public/admin
controllers live in `app/Http/Controllers/{Public,Admin}/…` and remain thin:
validate (FormRequest) → invoke Action → present (View).

**Rules:** no business logic in views; no querying in Blade; no controllers
bigger than a router of the domain actions; no duplicated logic (slug/media/
activity boundaries are reused, never re-implemented).

## Products (`App\Domains\Products`)

- `Product`: title, slug (unique, SlugService), excerpt, description, status
  (`draft|review|published|archived`), type (`software|saas|template|other`),
  `featured`, `published_at`.
- `ProductFeature`: ordered capability list on a product (title, description,
  sort_order).
- `Technology`: shared taxonomy (products ↔ portfolio), name/slug/icon/
  category — **never hardcoded in domain code**; seeded starter catalog
  (real technologies only): Laravel, PHP, MySQL, Python, JavaScript,
  Three.js, SCSS, Vue, React, Telegram API.
- Media: `covers` (single, replaced on upload) + `gallery` (append).
- Workflow: draft → review → published (publishing stamps `published_at`)
  → archived. Unpublishing clears `published_at`.

**Public pages:** `/products`, `/products/{slug}` — published only, eager
media, SEO via MetaResolver.

## Portfolio (`App\Domains\Portfolio`)

- `PortfolioProject`: title, slug, summary, challenge, solution, results,
  status (same workflow), featured, published_at. **A case study, not a
  gallery** — the public page renders Challenge → Approach → Outcome.
- Technologies + media (covers/gallery) like Products.
- Rules: only real client work with permission; never invented metrics.
  Empty fields render honest "being prepared" placeholders, not filler.

**Public pages:** `/portfolio`, `/portfolio/{slug}`.

## Services (`App\Domains\Services`)

- `Service`: title, slug, short_description, description, icon (single
  glyph), status (`draft|published|archived`), sort_order.
- Ordering is numeric `sort_order` (admin-editable); public pages order by
  it. No drag-and-drop yet (Phase 2 scope).

**Public pages:** `/services`, `/services/{slug}`.

## Leads (`App\Domains\Leads`)

- `Lead`: name, email, phone?, company?, project_type?, message, status,
  source (`website`), timestamps.
- Pipeline (Phase 2 brief, supersedes the Phase 0 IA sketch):
  `new → reviewing → contacted → proposal_sent → won | lost`.
- `LeadNote`: operator notes (user_id, append list).
- `LeadStatusHistory`: append-only transitions (`old_status`, `new_status`,
  `user_id`) — no `updated_at` on purpose.
- Public intake: `POST /leads` (throttle 5/min per IP + CSRF) with strict
  validation and no trusting of status/source from the request.
- Leads are never hard-deleted while in the pipeline; terminal states are
  statuses, not row removal.

## Media (`App\Domains\Media`)

- `Media` registry: morph `model_type`/`model_id` (nullable for library
  uploads), collection (`covers|gallery`), filename, path, disk, mime_type,
  size, width, height, alt_text.
- Originals live on the private `media` disk (webroot never sees them);
  `GET /media/{media}` serves files attached to **published** entities only
  (or to authenticated Blue Control operators). Unattached library files are
  never public.
- SVG disabled everywhere (XSS/entity risk) — JPEG/PNG/WebP/AVIF (+GIF only
  in gallery); size ≤ 12 MB; dimensions ≤ 8000×8000, validated from real
  contents (MIME sniff + `getimagesize`), not headers.
- Trait `Concerns\HasMedia` provides `media()`, `mediaFor()`, `cover()`,
  `coverUrl()`, `galleryMedia()` — loaded-relation aware (no N+1).

## Cross-domain conventions

- Slug generation always through `App\Core\Slug\SlugService`.
- Media always through `App\Core\Media\MediaService` (validation + storage +
  registry) — never `Storage::put` in domain code.
- Audit always through `App\Core\Activity\ActivityLogger`.
- Site settings always through `App\Core\Settings\SettingsService`.
- SEO always through `App\Core\Seo\MetaResolver` — views never hand-build
  metadata.
- Statuses are strings + app-level enums (constants on models); MySQL CHECK
  is deliberately not used (SQLite-compatible tests).
