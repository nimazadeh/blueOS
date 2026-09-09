# Blue Studio OS — Database Design

**Status:** Accepted (Phase 0). This document is the authoritative source for
Phase 1 migrations. Any schema change requires an ADR update.

---

## 1. Conventions

| Concern | Convention |
|---|---|
| Engine | InnoDB, `utf8mb4` / `utf8mb4_unicode_ci` |
| Primary keys | `id` `BIGINT UNSIGNED` auto-increment (Laravel default) |
| Foreign keys | `foreignId()->constrained()` — `ON DELETE CASCADE` for owned children, `RESTRICT` for referenced masters (media, categories) — see per-table notes |
| Timestamps | `created_at`, `updated_at` on all business tables |
| Soft deletes | `deleted_at` where content can be admin-restored (products, portfolio, lab, services, posts, media); **not** on leads (audit) or activity/SEO/redirect per policy |
| Statuses | PHP backed enums, stored as string (`varchar(20)`) with DB check-free enforcement at app layer (MySQL CHECK support exists, but app-level enum + migration validation is Laravel-idiomatic) |
| Slugs | `varchar(191)` unique per resource; generated/updated via SlugService; see §3 |
| Localization | JSON columns: `varchar`-free `json` (`name`, `short_description`, `description`, `meta_title`, `meta_description`, `body`…) keyed by locale (`en`, `fa`) — see §4 |
| Money | `decimal(12,2)` + `currency varchar(3)` (no floating point) |
| URLs | public nullable fields with validation — never trust incoming URLs in v1 |

## 2. Localization schema pattern

```json
// columns of type json, e.g. products.name
{ "en": "Agentflow X", "fa": "ایجنت‌فلو ایکس" }
```

- Always store **all enabled locales** where content is required (validated in
  FormRequest); missing locale handled by `Translation` accessor returning `en` as
  fallback.
- Indexed/unique fields (slug) remain **single string** — locale prefix handled in
  routing, not duplicated per locale in DB (see
  [`information-architecture.md`](information-architecture.md#localization-and-urls)).
- Scopes: `whereLocale('fa')` resolver via custom Eloquent casts/accessors is the
  only place JSON is read; views never touch raw JSON.

## 3. Slug strategy (summary — full doc in [`information-architecture.md`](information-architecture.md#2-url--slug-strategy))

- `SlugService::make($model, $title)` → kebab-case, ASCII for Latin, admin-supplied
  for Persian (or transliteration), unique *within* resource, `-2`, `-3` suffix on
  collision.
- Slug columns unique-indexed. Historical slugs live in `redirects` (301).
- Public URL never exposes numeric IDs.

## 4. Domain entity map

```
Identity       users, roles, permissions (spatie/permission owns 3 tables)
Content        products, product_categories, product_features, product_tags,
               product_technologies, product_media
               portfolio_projects, portfolio_project_categories, portfolio_media
               lab_experiments, lab_experiment_tags, lab_media
               services, service_categories
               posts, post_categories, post_tags
Relations      joins (many-to-many) + media tables (morph via medialibrary or
               explicit join — see ADR-007)
Leads          leads, lead_notes, lead_status_history
Media          media (central registry; medialibrary filesystem variants)
Platform       seo_meta, redirects, settings, activity_logs, sessions,
               cache, jobs, failed_jobs (framework)
```

## 5. Tables (logical design)

> All tables have `id`, `created_at`, `updated_at` unless stated otherwise.
> `└` = child/join.

### 5.1 Identity & auth

**users**
| column | type | notes |
|---|---|---|
| id | bigint unsigned PK | |
| name | varchar(120) | |
| email | varchar(190) **unique** | |
| email_verified_at | timestamp nullable | |
| password | varchar(255) | bcrypt/argon2id |
| remember_token | varchar(100) nullable | |
| locale | varchar(5) default `en` | admin UI preference |
| two_factor_enabled | bool default false | Phase 0: column only; feature Phase 3 |
| is_active | bool default true | |
| last_login_at | timestamp nullable | |
| deleted_at | soft delete nullable | |

**roles**, **permissions**, **model_has_roles**, **model_has_permissions**,
**role_has_permissions** — provided by `spatie/laravel-permission` (see
[`adr/0006-authentication-strategy.md`](adr/0006-authentication-strategy.md)).

### 5.2 Catalog / domain — **Products**

**product_categories** (master, RESTRICT on delete)
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| name | json | `{en, fa}` |
| slug | varchar(191) **unique** | category without prefix |
| description | json nullable | |
| sort_order | int default 0 | |

**products**
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| name | json | `{en, fa}` |
| slug | varchar(191) **unique** | |
| subtitle | json nullable | |
| summary | json nullable | card/hero text |
| description | json | rich text (sanitized) |
| category_id | FK → product_categories **nullable, SET NULL** | category optional until assigned |
| status | enum string `draft` \| `published` \| `archived` | default `draft` |
| is_featured | bool default false | |
| version | varchar(30) nullable | e.g. `1.4.2` |
| release_date | date nullable | |
| price_display | varchar(60) nullable | e.g. `$49` (display only; no payment) |
| currency | varchar(3) nullable | |
| marketplace_url | varchar(2048) nullable | external marketplace |
| purchase_url | varchar(2048) nullable | external purchase |
| documentation_url | varchar(2048) nullable | docs |
| repository_url | varchar(2048) nullable | source |
| demo_url | varchar(2048) nullable | see demo model §6 |
| demo_type | enum `external` \| `internal` \| `embedded` \| `none` default `none` | |
| demo_status | enum `live` \| `pending` \| `unavailable` default `unavailable` | |
| demo_note | json nullable | Persian/En explanation |
| case_study | json nullable | optional case-study object |
| cover_media_id | FK → media nullable, SET NULL | primary card image |
| featured_order | int default 0 | |
| published_at | timestamp nullable | SEO/sitemap uses this |
| deleted_at | soft delete | |
| seo_meta_id | FK → seo_meta nullable, SET NULL | |
Indexes: `unique(slug)`, `index(status)`, `index(category_id)`, `index(is_featured)`,
`index(published_at)`, full-text optional (Phase 4).

**product_features**
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| product_id | FK → products **CASCADE** | |
| title | json | |
| description | json nullable | |
| sort_order | int default 0 | |
| is_highlight | bool default false | used to build hero feature list |

**product_tags** (master tags are typed; `products` ↔ `product_tags` join)
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| name | json | |
| slug | varchar(191) **unique** | |
| sort_order | int default 0 | |

**product_technologies**
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| name | varchar(100) | `Laravel` |
| version_hint | varchar(30) nullable | `12.x` |
| category | varchar(50) nullable | `backend`, `frontend`, `infra` |

**product_tag** (join) — `product_id` + `tag_id`, composite PK, cascade both.
**product_technology** (join) — `product_id` + `technology_id`, composite PK, cascade.

### 5.3 Portfolio (client/custom work)

**portfolio_project_categories** — `id, name json, slug unique, description json, sort_order`
(master; RESTRICT/`SET NULL` on project reference).

**portfolio_projects**
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| title | json | |
| slug | varchar(191) **unique** | |
| client_name | varchar(150) nullable | only real, client-permitted |
| client_type | enum `startup` \| `business` \| `enterprise` \| `personal` \| `other` nullable | |
| summary | json | card text |
| challenge | json | case study |
| context | json nullable | |
| approach | json nullable | |
| solution | json nullable | |
| architecture | json nullable | |
| results | json nullable | real metrics only |
| status | enum `draft` \| `published` \| `archived` default `draft` | |
| is_featured | bool default false | |
| project_type_id | FK → portfolio_project_categories nullable SET NULL | |
| started_at | date nullable | |
| completed_at | date nullable | |
| cover_media_id | FK → media nullable SET NULL | |
| published_at | timestamp nullable | |
| deleted_at | soft delete | |
| seo_meta_id | FK → seo_meta nullable SET NULL | |

Indexes: `unique(slug)`, `index(status)`, `index(is_featured)`, `index(published_at)`.

**portfolio_technology** join — `portfolio_project_id` + `technology_id` (reuses
`technologies` table — shared by product/portfolio; a single `technologies` master is
appropriate since a technology is a universal entity).
> Decision: `technologies` is global (not duplicated per module). Product-to-tech and
> portfolio-to-tech are separate join tables to keep domain isolation.

### 5.4 Lab

**lab_experiments**
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| title | json | |
| slug | varchar(191) **unique** | `/lab/…` |
| status | enum `draft` \| `visible` \| `archived` default `draft` | |
| experiment_state | enum `concept` \| `prototype` \| `active` \| `paused` default `concept` | |
| summary | json | card |
| hypothesis | json nullable | |
| findings | json nullable | |
| approach | json nullable | |
| repository_url | varchar(2048) nullable | |
| live_url | varchar(2048) nullable | internal/sandbox link |
| is_featured | bool default false | |
| cover_media_id | FK → media nullable | |
| published_at | timestamp nullable | |
| deleted_at | soft delete | |
| seo_meta_id | FK → seo_meta nullable | |

**lab_experiment_tags** — master `tags` table is typed and shared with posts
(below); join `lab_experiment_tag`.

### 5.5 Services

**service_categories** — `id, name json, slug unique, description json, sort_order`.

**services**
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| name | json | |
| slug | varchar(191) **unique** | `/services/…` |
| outcome | json | business outcome headline |
| summary | json | card |
| description | json | rich |
| deliverables | json nullable | list |
| process | json nullable | stages |
| category_id | FK → service_categories nullable SET NULL | |
| status | enum `draft` \| `published` \| `archived` default `draft` | |
| is_featured | bool default false | |
| cover_media_id | FK → media nullable SET NULL | |
| published_at | timestamp nullable | |
| deleted_at | soft delete | |
| seo_meta_id | FK → seo_meta nullable SET NULL | |

### 5.6 Insights (blog)

**post_categories** — `id, name json, slug unique, description json, sort_order`.
**tags** — global typed tags: `id, name json, slug unique, type enum(product|post|lab),
sort_order` (unique per `type + slug`).
**post_tags** join — `post_id` + `tag_id`, cascade.

**posts**
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| title | json | |
| slug | varchar(191) **unique** | `/insights/…` |
| excerpt | json | meta/card |
| body | json | rich (sanitized) |
| category_id | FK → post_categories nullable SET NULL | |
| author_id | FK → users nullable SET NULL | real author only |
| status | enum `draft` \| `review` \| `published` \| `archived` default `draft` | content workflow |
| is_featured | bool default false | |
| cover_media_id | FK → media nullable SET NULL | |
| published_at | timestamp nullable | |
| reading_time | smallint nullable | minutes (calculated/editable) |
| deleted_at | soft delete | |
| seo_meta_id | FK → seo_meta nullable SET NULL | |

Indexes: `unique(slug)`, `index(status)`, `index(category_id)`, `index(published_at)`.
(Post tags use join; no per-post JSON tags.)

### 5.7 Leads (business-critical — full doc [`lead-management.md`](lead-management.md))

**leads**
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| name | varchar(150) | |
| email | varchar(190) | indexed for dedupe/search |
| company | varchar(190) nullable | |
| project_type | varchar(100) nullable | enum-ish: `website`, `webapp`, `saas`, `backend`, `automation`, `bot`, `python`, `integration`, `ai`, `custom`, `other` |
| budget_range | varchar(50) nullable | not stored as number (display range) |
| timeline | varchar(50) nullable | enum-ish: `asap`, `1-3m`, `3-6m`, `flexible` |
| description | text | |
| relevant_links | json nullable | array of URLs |
| source | varchar(100) default `website` | `website`, `product-demo`, `portfolio`, `contact`, … |
| source_url | varchar(2048) nullable | page where submitted |
| referrer | varchar(2048) nullable | |
| status | enum `new` \| `contacted` \| `qualified` \| `proposal` \| `negotiation` \| `won` \| `lost` \| `archived` default `new` | lifecycle |
| assigned_to | FK → users nullable SET NULL | internal |
| first_contacted_at | timestamp nullable | |
| converted_at | timestamp nullable | |
| ip_hash | varchar(64) nullable | hashed IP for abuse analytics (GDPR-friendly) |
| user_agent | varchar(255) nullable | |
| locale | varchar(5) default `en` | |
| consent_at | timestamp nullable | privacy consent |
| notes_count | int default 0 | denormalized counter (optional) |
| deleted_at | nullable **only for admin archive action** | policy: audit retain; soft delete only via explicit |
Indexes: `index(status)`, `index(email)`, `index(created_at)`, `index(source)`,
`unique(email, source, created_at)` (spam dedupe via app validation instead if too strict — see §7).

**lead_notes** — `id, lead_id FK CASCADE, author_id FK users SET NULL, body text,
created_at` (no updated_at; immutable notes).
**lead_status_history** — `id, lead_id FK CASCADE, from_status, to_status,
changed_by FK users nullable, created_at`. (Audit trail — enables dashboard
conversion funnel.)

### 5.8 Media (central — see [`media-architecture.md`](media-architecture.md))

**media**
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| model_type + model_id (morph) | | medialibrary pattern |
| collection_name | varchar(60) | `covers`, `gallery`, `post_images`, `logos` |
| disk | varchar(60) | `local`, `s3` |
| file_name | varchar(255) | storage name |
| original_name | varchar(255) | user filename (sanitized for display) |
| mime_type | varchar(100) | validated whitelist |
| size | bigint unsigned | bytes |
| width, height | int unsigned nullable | known on raster |
| custom_properties | json | alt, caption, focal point |
| manipulations | json | generated variants metadata |
| responsive_images | json | srcset variants |
| order_column | int default 0 | gallery ordering |
| created_at / updated_at | | |
| deleted_at | soft delete | |

### 5.9 Platform / SEO / settings / audit

**seo_meta** (morphable, one per content record + standalone pages)
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| model_type + model_id | morph | product/portfolio/post/page |
| path_key | varchar(191) nullable unique | for static pages (`home`, `about`, `services-index`) |
| meta_title | varchar(70) nullable | per-locale via JSON: `meta_title` json |
| meta_description | varchar(160) nullable | json |
| canonical_url | varchar(2048) nullable | |
| og_title / og_description / og_image | json / varchar(2048) nullable | |
| twitter_title / twitter_description / twitter_image | nullable | |
| robots | varchar(100) default `index,follow` | |
| noindex | bool default false | |
| schema_json | json nullable | structured data override |
| locale | varchar(5) default `en` | one record per locale per entity |

**redirects** — `id, from_path varchar(2048) unique, to_path varchar(2048) not null,
status_code smallint default 301, enabled bool default true, reason varchar(100)
nullable, created_by FK users nullable, created/updated`. No soft delete; audit log
records changes.

**settings** (key/value with type)
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| key | varchar(120) **unique** | dot-notation (`site.name`, `seo.defaults`, `contact.email`) |
| value | json | typed |
| type | varchar(20) | `string, json, bool, int` |
| is_public | bool default false | exposed to views vs admin-only |
| updated_by | FK users nullable | |

**activity_logs**
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| actor_id | FK users nullable | null = system |
| action | varchar(60) | `create, update, delete, login, logout, publish, status_change` |
| subject_type + subject_id | morph nullable | |
| properties | json | before/after delta (redacted) |
| ip | varchar(45) nullable | |
| created_at | | (no updated_at — immutable) |
Indexes: `index(subject_type, subject_id)`, `index(actor_id)`, `index(created_at)`.

## 6. Product demo model (explicit)

```
products.demo_type:   none | external | internal | embedded
products.demo_status: live | pending | unavailable
products.demo_url:    [optional] target URL

demo_type semantics
- external  → demo_url is a full external URL (subdomain/other domain)
- internal  → route registered in the app (e.g. /demo/{product-slug}); demo_url
              may be null and resolved by DemoService from slug
- embedded  → demo_url is an iframe-embeddable URL for the product detail page
              (allowlist enforced: scheme https, host allowlist config, CSP frame-src)
- none      → no demo capability

DemoService resolves a DemoLink value object for the view:
  { type, url, status, label, is_embeddable, fallback_route }
```

Implications:

- Demo is decoupled from Blue Studio site delivery (external deployments are first-class).
- CSP `frame-src` only permits configured demo origins; no arbitrary iframe embedding.
- If demo is `pending`, the CTA becomes "Request demo" → lead with
  `source=product-demo`, not a broken link.
- See [`seo.md`](seo.md), [`security.md`](security.md) (CSP), and
  [`roadmap.md`](roadmap.md) (embedded-demo phase).

## 7. Data integrity & anti-abuse notes

- **Leads:** server-side honeypot field; rate limit 5/min/IP + 3/hour/email; email
  format validation; description length 10–4,000; URL fields validated with
  `url:http,https`; no HTML accepted (escaped on render).
- **Media:** MIME whitelist (jpeg/png/webp/avif/svg*/gif, pdf for docs; SVG sanitized
  or disallowed for upload originals), size caps, dimension caps, filename
  randomization, variant regeneration on replace, orphan cleanup.
- **SEO/meta:** length validators, canonical host enforced, `noindex` on draft content.
- **Soft deletes:** content never hard-deleted in v1; media and seo handling on delete
  defined in [`media-architecture.md`](media-architecture.md).
- **Transactions:** multi-table write paths (publish product + seo snapshot + sitemap
  invalidation) use DB transactions + queued/event invalidation.

## 8. ER summary (conceptual)

```
users ──roles──permissions (spatie)
users ──< lead_notes ─< leads ─< lead_status_history

product_categories ─< products ─< product_features
                              ├─< product_tags (join) >─ product_tags
                              ├─< product_technologies (join) >─ technologies
                              └─< media (morph)
portfolio_project_categories ─< portfolio_projects ─< media (morph)
                                                └─< portfolio_technologies >─ technologies
lab_experiments ─< media (morph) ─< lab_experiment_tags >─ tags
service_categories ─< services ─< media (morph)
post_categories ─< posts ─< media (morph) ─< post_tags >─ tags
seo_meta (morph) · redirects · settings · activity_logs
media (morph, central)
```

## 9. Growth & migration notes

- **Indexing:** all slugs, statuses, published timestamps, and join FKs indexed
  (per §4–§7). Full-text indexes deferred until Phase 4 (search) with a dedicated
  migration.
- **Vertical first:** v1 is a single MySQL schema. If load requires,
  `media`/`activity_logs` can move to separate storage without touching domain
  tables (already isolated).
- **JSON columns:** MySQL 8 JSON is sufficient for localized fields; if a future
  phase needs SQL querying on translations, convert to normalized `translations`
  tables — decision will be documented as an ADR.
- **Archival:** content archiving is a status, not a delete; deleted records remain
  restorable for 30 days via soft-delete recovery in Blue Control (Phase 3).

## 10. Open items intentionally deferred

- Passwordless login / magic links (Phase 3).
- Full-text search indexing strategy (Phase 4).
- AI features storage (Phase 6 — will use existing leads/posts tables + new
  `ai_artifacts` table; not pre-created).
- Client portal tables (Phase 7).
- Analytics event storage (Phase 5 — event log table planned then; DOM events
  documented now in [`analytics.md`](analytics.md)).

---

## 11. Phase 2 — implemented schema (supersedes the logical design where noted)

Phase 2 implements the Phase 2 brief's schema. Where the brief's explicit
column list differs from the Phase 0 logical design, **the brief wins** and
this section is authoritative:

- **Content columns are single-language** (`title`, `excerpt`,
  `description`, `summary`, `challenge`, `solution`, `results`,
  `short_description`) rather than the Phase 0 `{en, fa}` JSON plan.
  Localized content can be added later as JSON twin columns (`title_l10n`
  etc.) or a normalized `translations` table via a non-breaking migration —
  the present columns remain the default-language source.
- **Lead pipeline** follows the brief: `new → reviewing → contacted →
  proposal_sent → won | lost` (Phase 0 sketched `contacted → qualified →
  proposal → negotiation → archived`; brief wins).
- **Slugs** `varchar(191) unique` per resource, generated by `SlugService`.
- **Statuses** `varchar(20)` with app-level enum constants (no DB CHECKs —
  keeps SQLite-based tests identical to MySQL).
- **Pivots**: `product_technology`, `portfolio_project_technology`
  (`foreignId` × 2, composite primary, cascade).
- **Media**: morph registry without FK constraints (polymorphic), nullable
  `model_type`/`model_id` for library-first uploads.
- **RBAC** uses internal `roles/permissions/role_user/permission_role`
  instead of the spatie trio documented in §5.1 (see `docs/admin.md` for the
  rationale + swap path).
- **Soft deletes remain deferred** to a later phase (Phase 0 §9). Deleting
  products/portfolio/services currently hard-deletes (after media cleanup and
  audit log) — documented as a Phase 2 scope decision; recovery UI lands with
  soft deletes later.

### 11.1 Table inventory (Phase 2)

| Table | Purpose |
|---|---|
| `roles`, `permissions` | RBAC catalogs |
| `role_user`, `permission_role` | RBAC pivots |
| `media` | morph media registry (private disk + controlled public route) |
| `products` | product catalog (status/type/featured/published_at) |
| `product_features` | ordered capability list |
| `technologies` | shared taxonomy (products + portfolio) |
| `product_technology` | products ↔ technologies |
| `portfolio_projects` | case studies (challenge/solution/results) |
| `portfolio_project_technology` | portfolio ↔ technologies |
| `services` | service catalog (icon, sort_order) |
| `leads` | lead inbox (status pipeline, source) |
| `lead_notes` | operator notes |
| `lead_status_history` | append-only status transitions |

### 11.2 Migration timeline

`2026_09_09_0001xx_*` blocks one domain per migration group (100 RBAC, 101
media, 102 products, 103 portfolio, 104 services, 105 leads). Run order is
chronological; pivots reference their masters with cascade deletes.
