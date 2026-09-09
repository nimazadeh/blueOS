# Blue Studio OS — Media Architecture

**Status:** Accepted (Phase 0). Implements
[`adr/0007-media-storage-strategy.md`](adr/0007-media-storage-strategy.md).

---

## 1. Goals

- Upload once, reuse everywhere (covers, galleries, post images, logos).
- Responsive images with zero manual work for the editor.
- Storage abstraction that supports local disk today and S3-compatible object
  storage later, **without changing domain code**.
- Strict validation, sane naming, safe deletion/replacement.
- No binary blobs in MySQL (`media` table stores metadata only).

## 2. Architecture

```
Editor upload (Blue Control, Livewire)
   → MediaService::store(file, collection, model, options)
       → validates MIME whitelist, size, dimensions
       → generates safe storage key (uuid-based, extension preserved)
       → stores original on configured disk
       → registers Media row (morph, collection, disk, mime, dimensions)
       → generates variants via Intervention Image (local) / queue-fallback
       → sets responsive_images (srcset) metadata
       → re-generates on replace; invalidates caches / sitemap
Public render
   → MediaImage component (srcset, sizes, width/height, loading=lazy,
     decoding=async, alt from custom_properties, aspect-ratio placeholder)
   → no <img> without dimensions (CLS prevention)
```

## 3. Storage layout & naming

```
storage/app/
├── media/
│   └── {collection}/
│       └── {YYYY}/{MM}/
│           └── {uuid}.{ext}          ← original (never exposed directly)
├── media-variants/                    ← derived, cacheable, public-safe
│   ├── thumb/   (240w)
│   ├── mobile/  (480w)
│   ├── card/    (720w)
│   ├── desktop/ (1280w)
│   └── hero/    (1920w)
```

Rules:

- **Filenames:** storage filename is random UUID + validated extension — never user
  filename (collision, traversal, encoding issues). `original_name` preserved in DB
  for display.
- **Directory:** `media/{collection}/{YYYY}/{MM}/` is logical and horizontally
  shardable per month.
- **Public URL:** generated on the fly via a signed/public disk path; never
  user-controllable. Do not build public URLs from metadata in views.
- **Replacement:** replace operation stores the new original, regenerates variants
  in a transaction, atomically swaps `media` row, then deletes old files; failure
  rolls back and keeps old version.

## 4. Validation

| Type | Allowed | Max size | Max dimensions | Notes |
|---|---|---|---|---|
| JPEG | `image/jpeg` | 12 MB | 8,000×8,000 | uploads from cameras |
| PNG | `image/png` | 12 MB | 8,000×8,000 | |
| WebP | `image/webp` | 12 MB | 8,000×8,000 | |
| AVIF | `image/avif` | 12 MB | 8,000×8,000 | upload path later; generated first |
| SVG | `image/svg+xml` | 1 MB | — | **disabled by default** (XSS/entity risk); admin opt-in with sanitizer in Phase 3 |
| GIF | `image/gif` | 6 MB | 1,000×1,000 | animated only where needed |
| PDF | `application/pdf` | 20 MB | — | docs collection only |

- Check **decoded content** (getimagesize), not just declared MIME.
- Reject SVG by default (documented security decision,
  [`security.md`](security.md#uploads)).
- Reject filenames with control chars, `..`, path separators (defense-in-depth;
  randomization already removes most risk).

## 5. Variants & responsive output

| Variant | Width | Use |
|---|---|---|
| `thumb` | 240px | small cards, avatars, thumbnails |
| `card` | 720px | product/service/portfolio cards |
| `desktop` | 1280px | detail sections |
| `hero` | 1920px | hero/full-bleed |
| `og` | 1200×630 (crop) | Open Graph/Twitter |
| `favicon`/`logo` | per config | branding collection |

- Output generation: `width`/`height`-aware, JPEG quality 75–82, WebP conversion
  (`image/webp`) with AVIF generation deferred until tooling confirmed (Phase 2
  decision).
- `srcset` built from generated variants; `sizes` attribute per component context.
- Storage: variants on **local disk in v1**; in production, all generated in
  `storage` cache and served via public disk or CDN; object storage keeps the same
  logical layout.

## 6. Deletion & replacement lifecycle

1. **Soft delete** `media` row (content references preserved; image 404s handled by
   `onerror`/placeholder).
2. **Hard delete** only after 30 days (admin "purge" action) — files removed from
   disk, `activity_logs` records the purge.
3. **Replacement** (same media row): new original + variant regeneration + atomic swap.
4. Orphan variants (from interrupted generation) cleaned by a
   `media:clean-orphans` artisan command (Phase 2) + queued job on failure.
5. **Delete of a model** (CASCADE): media rows soft-deleted, files retained until
   registry purge; `MediaService` observer handles the chain. No dangling public URLs
   — cover/listed URLs re-resolved at render.

## 7. Storage abstraction

- Use Laravel Filesystem disks: `local` (dev), `media` (prod default),
  `media-public` (if public disk), and future `s3` disk (config-driven).
- `MediaService` is the only component that knows disks; domain code calls
  `MediaService::cover($model)` / `MediaService::gallery($model, 'gallery')`.
- To move to object storage: set `media.disk=s3` and run `php artisan media:sync` —
  no domain code changes.
- Public URLs in production should be CDN-backed; cache invalidation is by variant
  versioning key (`?v=hash`) — no stale CDN assets.

## 8. Integration points

- **Admin (Blue Control):** Livewire `MediaManager` — upload/dropzone, collection
  picker, alt/caption editable, crop/focal point (Phase 2), replace (Phase 2),
  delete/restore (Phase 2).
- **Public:** `MediaImage` component; gallery component with lightbox (accessibility
  per [`accessibility.md`](accessibility.md)); demo screenshots use same pipeline.
- **SEO:** OG image generated from cover variant (`og`).
- **Performance:** all images `loading="lazy"` below fold, `decoding="async"`,
  explicit `width`/`height` or `aspect-ratio` (CLS); hero/above-fold images
  `fetchpriority="high"` + responsive sizing (see [`performance.md`](performance.md)).

## 9. Configuration (to be added in Phase 1)

```php
// config/media.php — will be scaffolded in Phase 1 from this spec
'collections' => [ // collection => allowed types, sizes, dimensions
  'covers'   => ['image/jpeg','image/png','image/webp'],
  'gallery'  => ['image/jpeg','image/png','image/webp','image/gif'],
  'posts'    => ['image/jpeg','image/png','image/webp'],
  'documents'=> ['application/pdf'],
],
'default_disk' => env('MEDIA_DISK', 'local'),
'variants' => [...], // per §5
'max_upload_bytes' => 12 * 1024 * 1024,
'allowed_svg' => false,
```

## 10. Acceptance checklist

- [x] Naming/directory rules defined
- [x] MIME + size + dimension validation defined
- [x] Variant + responsive image pipeline defined
- [x] Deletion/replacement/orphan lifecycle defined
- [x] Storage abstraction (local → object) defined
- [x] No user-controlled paths in public URLs
- [x] CLS/performance integration defined
