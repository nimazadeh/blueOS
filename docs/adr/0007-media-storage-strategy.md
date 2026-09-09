# ADR-007 — Media Storage Strategy

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

Images (covers, gallery, OG, post images) must be managed centrally, generated into
responsive variants, replaceable, validated strictly, and swappable to object
storage later without rewriting domains. Binary blobs in MySQL are not acceptable.

## Decision

- **Central media registry** (`media` morph table) on top of **Laravel Filesystem
  abstraction**; a single `MediaService` owns all file operations and variant
  generation.
- Generation strategy: store **original** on configured disk; generate variants
  (thumb 240 / card 720 / desktop 1280 / hero 1920 / og 1200×630) with a
  local-capable image processor (Intervention Image v3 is the Phase-1 candidate)
  into a separate cache directory; responsive `srcset` metadata stored on the
  media row.
- `spatie/laravel-medialibrary` is the recommended implementation baseline because
  it already implements morph registry + variants + storage abstraction; the
  contract exposed to the app is **our MediaService API**, so swapping to a custom
  implementation later is contained. (If medialibrary's local generator doesn't
  meet WebP/AVIF needs, swap the generator behind the same service — no domain
  change.)
- Strict validation: MIME sniff + whitelist + size/dimension caps; **SVG uploads
  disabled by default** (XSS/entity risk; sanitizer + opt-in later).
- Naming: random UUID storage names under `media/{collection}/{YYYY}/{MM}/`;
  `original_name` kept for display only.
- Deletion: soft-delete registry row → files retained → purge command after 30 days;
  replacement swaps atomically; orphan cleanup artisan command.
- Disks: `local` dev → `media` / object storage (`s3`) prod. Moving storage =
  config + `media:sync` command; zero domain changes.

## Alternatives considered

1. **Custom MediaService from scratch (no package)** — viable but slower to build
   correctly (variants, responsive, conversions); package chosen as baseline with
   service abstraction as the seam.
2. **Intervention standalone only (no registry)** — rejected: no central metadata,
   no relations model.
3. **Store originals in public/ directly** — rejected: unsafe path control, no
   variant management, poor object-storage migration.
4. **Database blobs/BLOBs** — rejected: MySQL bloat, no CDN, no variants.
5. **Cloudinary-style external service** — deferred: external dependency,
   data-residency/privacy decisions needed; abstraction keeps it possible later.

## Consequences

- ✅ One media API for public + admin + SEO; predictable storage; CDN-ready.
- ✅ Strong validation + sanitized naming; safe replacement/deletion lifecycle.
- ⚠️ Package baseline must be reviewed at install (license, maintenance) — recorded
  in Phase 1 dependency review.
- ⚠️ Variant generation CPU cost — generated on upload (queued in Phase 2);
  uploads are internal-only, so cost is bounded.
