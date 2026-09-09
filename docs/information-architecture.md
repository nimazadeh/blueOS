# Blue Studio OS — Public Information Architecture

**Status:** Accepted (Phase 0).

---

## 1. Site map (v1)

```
/                              Home (hero, positioning, products preview, services
│                              preview, lab tease, trust, start-project CTA)
├── /products                  Products index (list, filters by category/status)
│   └── /products/{slug}       Product detail (rich page, demo CTA, purchase CTA)
├── /portfolio                 Portfolio index (client/custom work)
│   └── /portfolio/{slug}      Case study
├── /lab                       Lab index (experiments, R&D, prototypes)
│   └── /lab/{slug}            Experiment detail
├── /services                  Services index (outcome-led)
│   └── /services/{slug}       Service detail (offer, process, related work, CTA)
├── /insights                  Insights (blog) index (categories, tags)
│   └── /insights/{slug}       Insight/article detail
├── /about                     About (positioning, philosophy, how we work)
├── /start-a-project           Lead generation (project request)
│   └── /start-a-project/success   thank-you state
├── /contact                   Contact / alternative lead path (v1: pointer + form)
├── /privacy                   Privacy (required for lead forms; polish)
├── /admin                     Blue Control (login + dashboard + modules)
├── /sitemap.xml               XML sitemap
├── /robots.txt                Robots + sitemap reference
└── /404, /403, /500           Error pages (branded, no database dependence)
```

**Not in v1:** blog author pages, tag archives as separate routes (archived via query
params), search, RSS, client portal, pricing pages for internal products (price is
displayed on product detail).

## 2. URL & slug strategy

| Area | Pattern | Example |
|---|---|---|
| Product | `/products/{product-slug}` | `/products/agentflow-x` |
| Portfolio | `/portfolio/{project-slug}` | `/portfolio/nava` |
| Lab | `/lab/{experiment-slug}` | `/lab/parallel-sim` |
| Service | `/services/{service-slug}` | `/services/custom-software` |
| Insight | `/insights/{post-slug}` | `/insights/building-realtime-dashboards` |
| Category listings | query params | `/insights?category=engineering` |

**Slug rules (enforced by SlugService + DB):**

1. **Stable:** a slug never changes once published unless explicitly migrated; old
   slugs become redirects (see §6).
2. **Unique:** unique per resource type — *and*, because routes share a URL prefix,
   `products/slug` uniqueness within products; `/portfolio/slug` within portfolio.
   A global "reserved path" check prevents collision across prefixes.
3. **SEO-friendly:** lowercase, ASCII kebab-case; max 80 chars; no stop-word padding;
   optional manual override in admin; auto-suggestion from title (Perl/Unicode-aware
   for Persian → transliterated or explicit admin-set slug).
4. **Predictable:** URL == resource identity; no numeric IDs in public URLs.
5. **Migration-friendly:** slug changes flow through Redirect model (301); existing
   URLs keep working. See [`seo.md`](seo.md#redirects).

## 3. Localization and URLs

Phase 0 chooses a **locale-prefixed strategy with one default locale unprefixed**:

```
/                          → default locale (APP_DEFAULT_LOCALE, expected en)
/fa                        → Persian (RTL) home
/fa/products/agentflow-x   → Persian product detail
```

Rules:

- Every public route exists in all enabled locales; unsupported locale → 404 or
  (preferred) canonical redirect to default.
- `hreflang` alternates for each locale page; `x-default` → default locale URL.
- Locale is resolved from URL prefix first, then session/cookie, then `Accept-Language`;
  never from hostname in v1.
- Mixed LTR content (product names like `Agentflow X`, code, emails, URLs) is handled
  by RTL-aware markup (`dir="ltr"` isolation utilities) — see
  [`design-system.md`](design-system.md#rtl-and-bidi).
- Database stores localized fields as JSON columns (`name: {en, fa}`) — see
  [`database.md`](database.md#localization-schema-pattern).

See [`adr/0013-i18n-rtl.md`](adr/0013-i18n-rtl.md) for the full decision.

## 4. Domain distinction — Products vs Portfolio vs Lab (mandatory)

| Dimension | **Product** | **Portfolio Project** | **Lab Experiment** |
|---|---|---|---|
| Ownership | Blue Studio-owned, potentially commercial | Client / commissioned work | Blue Studio R&D |
| Business intent | Show + demo + sell | Show capability, win trust | Show exploration/innovation |
| CTA | Demo → Purchase (marketplace) | Services → Start a Project | Read/explore, contact |
| URL prefix | `/products` | `/portfolio` | `/lab` |
| Commercial fields | price, marketplace URL, purchase URL, version, release date | none | none |
| Demo support | **required-capable** (demo URL/type/status) | optional (client-permission) | often internal/self-hosted |
| Status semantics | draft/published/archived + featured | draft/published/archived + featured | visible/private/archived |
| Case-study content | optional `case_study` JSON/assoc | **primary content** (challenge/solution/results) | notes, hypothesis, findings |
| Admin section | Blue Control → Products | Blue Control → Portfolio | Blue Control → Lab |
| Relations | categories, features, technologies, tags, images | categories (project type), technologies, images, results | tags, categories, images |

**Rationale:** these three have different fields, CTAs, schemas, and SEO needs.
A single `projects` table would force nullable-union abuse and destroy future
products' commerce model. Documented as
[`adr/0011-domain-separation.md`](adr/0011-domain-separation.md).

## 5. Page blueprints (structure, not design)

### 5.1 Home

- **Hero:** one-sentence positioning, supporting line, primary CTA (Start a Project)
  + secondary CTA (Explore Products). Optional restrained visual module (see
  [`threejs-architecture.md`](threejs-architecture.md) — Digital Core is hero-candidate).
- **Trust strip:** capabilities in plain language (no fake stats; real facts only —
  e.g., product count, tech, principles).
- **Featured products:** 3 products with cover, category, one-line value, demo/purchase.
- **Services (outcomes):** 4–6 outcome cards, each linking to service detail.
- **Portfolio select:** 2–3 case studies with measurable (real) outcomes.
- **Lab tease:** 1–2 experiments, "new experiments" tag.
- **Process / how we work** (3-step: Discover → Build → Ship).
- **Start-project CTA banner** → `/start-a-project`.
- **Footer:** navigation, contact, legal, locale switch.

### 5.2 Product detail (rich — not an article)

Hero (title, subtitle, category, status badge, version/release) →
Demo CTA (primary if demo live; "Request demo" if pending) →
Purchase CTA (external marketplace link, price display) →
Summary & problem statement →
Capabilities/features (grouped, ICON list) →
Screenshots/gallery (responsive images, lightbox, reduced-motion aware) →
Live demo panel (embed/iframe or external link; handled by DemoService) →
Tech stack & architecture notes →
Case study (if present) →
Related products →
Final CTA (start a project).

### 5.3 Portfolio detail / case study

Hero (client metadata only if real + permission; challenge headline) →
Context → Challenge → Approach → Solution → Architecture → Features →
Technologies → Results (real metrics or qualitative outcomes, no fabricated numbers)
→ Gallery → CTA (Services / Start a Project).

### 5.4 Services

Index: outcome-first cards. Detail: outcome, what's included, process, deliverables,
related portfolio/products, CTA. **Services are outcomes** ("Ship a SaaS that scales")
not keyword dumps ("Laravel, PHP, React…").

### 5.5 Lab

Index: experiment cards (status: active/prototype/concept/archived) with "experimental"
visual treatment distinct from Products. Detail: hypothesis, approach, current state,
findings, tech notes.

### 5.6 Insights (blog)

Index: featured + list, category filter, tag filter, pagination. Detail: article with
byline (only real author names), category, tags, TOC for long posts, related articles,
SEO metadata, structured data (`Article`).

### 5.7 Start a Project

Funnel entry point (see §7). Fields: name, email, company/brand (optional), project
type (select), budget range (optional but recommended), timeline (select), description,
relevant links (optional), consent/privacy checkbox. Client-side + server-side
validation, honeypot, rate limiting; success state = thank-you confirmation (no fake
"we'll call within 24h" claim unless true — keep claim honest).

### 5.8 About

Positioning, philosophy, "how we work", team/people (only real info), contact CTA.

## 6. Navigation (primary, consistent)

```
Products        Portfolio        Lab        Services        Insights        About
CTA: Start a Project   ·   Locale switch (EN / فا)
```

Mobile: accessible drawer menu (keyboard + focus trap), no hamburger gimmickry.

## 7. Conversion architecture (funnels)

```
Funnel A — Custom Project
Visitor → Services / Portfolio → Start a Project → Lead submitted
        → Blue Control Lead pipeline (New → Contacted → Qualified →
          Proposal → Negotiation → Won/Lost/Archived)
        → Contact → Proposal → Client

Funnel B — Product
Visitor → Products index → Product detail → Live Demo → Purchase (external)
                                                  └→ Request demo (lead)
                                                  └→ Start a custom project (fallback)

Funnel C — Portfolio Trust
Visitor → Portfolio → Case Study → Services → Start a Project
```

**Conversion rules:**

- Every page has exactly one primary CTA; secondary CTAs are visually subordinate.
- Product detail is the only place that can carry both Demo and Purchase CTAs
  (they are distinct primary actions — demo first for evaluation, purchase second).
- Lead forms capture `source` (page), `referrer` info, and funnel step implicitly via
  route; see [`lead-management.md`](lead-management.md).
- No dark patterns: no fake urgency, no fake scarcity, no fake social proof.

## 8. Content ownership boundary (what's DB-driven vs static)

**DB-driven:** products, portfolio, lab, services, posts, categories, tags, media,
leads, leads notes, settings, SEO records, redirects, users/roles, activity logs.

**Static/system:** routes, blade components (design system), SCSS/design tokens,
module code, config, content structure, forms, layout, animation/3D architecture.
(Content model detailed in [`roadmap.md`](roadmap.md#content-model-boundary).)

## 9. Information architecture — acceptance checklist

- [x] Every audience has a clear path (visitor, client, buyer, admin)
- [x] Products / Portfolio / Lab are conceptually and URL-separate
- [x] Demo and commerce are product-level, not site-level
- [x] Lead flow has a single well-defined entry with multiple shallow paths
- [x] RTL/Persian is part of IA (locale prefixes, hreflang, default locale rule)
- [x] No placeholders/fabricated content in the architecture (real content only)
- [x] Every top-level area maps to a domain, controller, and slugs defined in
      [`database.md`](database.md)
