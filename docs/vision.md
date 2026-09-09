# Blue Studio OS — Product Vision

## 1. What Blue Studio is

Blue Studio is a digital product and software studio. It builds original products,
custom systems, and end-to-end software for clients. The studio's public presence —
the website itself — is a **premium flagship portfolio project**: it must demonstrate
the engineering quality, product thinking, and visual standard the studio sells.

**Blue Studio OS** is the name of the platform that powers that public presence:
a content-managed marketing/product site, lead engine, and (later) an operations hub
for the studio's products and services. It is deliberately named "OS" because it is
built as a system of domains — products, portfolio, services, insights, leads, media,
admin — with clean boundaries, not as a marketing page with a CMS bolted on.

## 2. The four business purposes (one platform)

1. **Present Blue Studio** as a premium software/product studio.
2. **Showcase original Blue Studio products** as professional case studies and
   portfolio pieces.
3. **Provide live demos and commercial information** for those products.
4. **Generate and manage leads** for custom client projects.

A visitor should be able to answer, in under 30 seconds: *What does this studio build?*
*Is it good?* *What can I buy or try right now?* *How do I start a project?*

## 3. Primary audiences

| Audience | Who | What they need | Primary action |
|---|---|---|---|
| **Visitor** | Someone exploring Blue Studio | Instant credibility, clear positioning, fast orientation | Browse, learn, return |
| **Potential Client** | Someone with a custom project need | Evidence of capability, services as outcomes, a low-friction inquiry path | Start a Project → lead |
| **Product Buyer / Visitor** | Someone evaluating or buying a Blue Studio product | Live demo, screenshots, features, tech stack, price, purchase link | Try demo / go to marketplace |
| **Admin (Blue Control operator)** | The studio's internal operator (owner) | Edit products, portfolio, services, blog, view/manage leads, media, settings | Operate content without code |
| **Future Editor / Staff** | Potential additional content roles | Scoped access to specific content modules | Edit within permission scope |

**Phase 0 decision:** the admin model is deliberately small — an internal operator
(studio owner) with a small set of roles, designed so Editor/Staff can be added later
without schema rework. See [`admin-blue-control.md`](admin-blue-control.md) and
[`adr/0006-authentication-strategy.md`](adr/0006-authentication-strategy.md).

## 4. Product positioning pillars

Blue Studio communicates five things, in priority order:

1. **Engineering ability** — real systems, real architecture, controlled by evidence
   (product pages, case studies, demo CTAs), never by generic claims.
2. **Product thinking** — the studio ships its own products; it does not only execute
   client briefs. Products are treated as first-class portfolio pieces.
3. **Visual quality** — a premium, restrained, technical aesthetic; the site itself is
   the strongest proof of this pillar.
4. **Reliability** — accessible, fast, secure, maintainable. Performance and
   accessibility are product requirements, not polish.
5. **Originality** — no template feel; no generic AI aesthetic; no stock clichés.

## 5. What Blue Studio OS is NOT

- Not an agency-template site.
- Not a generic "projects" showcase where products, client work, and experiments are
  indistinguishable (see [`adr/0011-domain-separation.md`](adr/0011-domain-separation.md)).
- Not an API-first platform in v1 (boundaries are designed so an API can come later).
- Not a payment platform in v1 (products link to external marketplaces).
- Not a client portal in v1 (capability is designed for, not built).
- Not a three.js showcase — 3D is used only where it earns its cost.

## 6. Product model definition

**Blue Studio OS as a product** is defined by these promises:

- **One platform, four jobs:** present, showcase/demo, commercialize, generate leads.
- **Content without code:** normal content operations (products, portfolio, services,
  posts, media, SEO) are managed in Blue Control; only structural/design changes need
  a developer.
- **Domain-clean:** Products (owned), Portfolio (client work), Lab (experiments) are
  separate bounded domains with separate schemas, URLs, and admin sections.
- **Persian-first alongside English:** Persian (RTL) is a first-class experience, not
  a translation add-on. The architecture is RTL-native (see
  [`design-system.md`](design-system.md) and [`adr/0013-i18n-rtl.md`](adr/0013-i18n-rtl.md)).
- **Future-proof boundaries:** authentication/RBAC, media storage, SEO, analytics,
  and lead management are isolated so future phases (API, client portal, AI features,
  project management) layer on without rework.
- **Production-grade by default:** security, accessibility, performance, and testing
  are baseline constraints from the first line of code.

## 7. Success measures (qualitative for Phase 0)

The platform will be judged on:

- Does a potential client trust the studio within one visit? (funnel completion rate)
- Does a product visitor reach demo or purchase in one click from product content?
- Can the operator publish/update content without touching code?
- Does the site pass its own performance and accessibility budgets?
- Does every future phase build on Phase 0 decisions without architectural rework?

## 8. Assumptions for Phase 0 (documented, not guessed)

1. The studio operates primarily in **English and Persian**; other locales may be
   added later via the same locale mechanism.
2. Products may be commercialized through **external marketplaces**; in-house
   payment is out of scope until a future phase explicitly introduces it.
3. The operator is familiar with Laravel conventions; the codebase must be
   maintainable by another professional Laravel developer.
4. Domain/URL structure may change in a future phase, so slugs and routing are
   designed for stability and migration (see [`seo.md`](seo.md)).
5. No fictitious clients, statistics, or portfolio items exist or will be fabricated
   to populate the site (Phase 0 rule 38). The database design supports **real**
   content added by the operator.
