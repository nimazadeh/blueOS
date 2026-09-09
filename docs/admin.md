# Blue Studio OS — Blue Control Admin (Phase 2)

## Access & routing

- Base path `/admin` (`routes/admin.php`), all sections behind
  `auth:admin` (session guard, shared `users` table in v1).
- Guests are redirected to `admin.login` via `redirectGuestsTo()` in
  `bootstrap/app.php`; already-authenticated operators are kept away from
  the login form by `RedirectIfAdminAuthenticated`.
- Login: email + password, `throttle:5,1`, session regeneration, `is_active`
  checked at attempt time.
- **Default guard is now `admin`** (`AUTH_GUARD=admin`): Gate/policies and
  `can:`-style authorization resolve the operator consistently. The public
  site remains anonymous; nothing public uses the web guard yet.

## RBAC (internal, phase-2 decision)

Tables: `roles`, `permissions`, `role_user`, `permission_role` — **shaped
like spatie/laravel-permission** (role/permission models + morph-free user
pivot) so a later swap is a migration + facade change, not a domain rewrite.
Spatie was not used because the dependency could not be installed/verified
in this environment; internal RBAC keeps the repo dependency-verifiable.

| Role | Permissions |
|---|---|
| `owner` | everything (also `Gate::before` bypass) |
| `admin` | all except `settings.manage` |
| `editor` | products/portfolio/services create+update, `leads.manage`, `media.manage` (no delete/publish, no settings, no activity) |

Permission slugs: `products.create|update|delete|publish`,
`portfolio.create|update|delete|publish`, `services.create|update|delete|
publish`, `leads.manage`, `media.manage`, `settings.manage`, `activity.view`.
Seed: `Database\Seeders\RolePermissionSeeder` (idempotent). No demo users
are seeded — create the operator via the create-admin flow (Phase 1A).

Authorization layers:
1. FormRequest `authorize()` → policy/gate (store/update/status/notes/uploads).
2. Controller `$this->authorize(...)` for index/edit/destroy/publish.
3. Policies per model (Owner bypass via `Gate::before` in
   `App\Providers\AppServiceProvider`).

## Section map

- **Dashboard** — counts (published per domain + new leads) + recent activity.
- **Products / Portfolio** — index (status filter, paginated), create/edit
  forms (features repeater, technology checkboxes, cover + gallery upload),
  delete (confirm), publish/unpublish.
- **Services** — ordered list (+ sort_order), CRUD, publish control.
- **Leads** — status filter, detail (message/notes/history), add note,
  change status.
- **Media** — upload (image-only, collection), edit alt text, delete;
  thumbnails served through the public media route (operator preview).
- **Settings** — site name/description, contact email/phone, default SEO
  description. Only `site.*` keys are public; **secrets never live here**.
- **Activity** — paginated audit trail (action, actor, subject, time).

## Forms & safety

- Every mutating form: `@csrf`, `@method`, server-side validation, `old()`
  re-population, inline field errors + `aria-invalid` wiring.
- Destructive actions use `data-confirm` (handled by
  `resources/js/modules/admin-ui.js` — no inline `onclick`).
- Status filters auto-submit via `data-auto-submit`.
- Uploads: MIME allowlists + size/dimension caps; SVG rejected; files are
  stored outside the webroot and delivered via the controlled media route.
- All outputs are Blade-escaped; content fields are plain text (no
  WYSIWYG/raw HTML surface in Phase 2 — documented choice).

## Layout & assets

- Shell: `resources/views/layouts/admin.blade.php` (sidebar nav, flash,
  route-section active state).
- Styles: `resources/scss/pages/_admin.scss` (tokens only), entries
  `admin.scss` + `resources/js/admin.js` (accessibility + admin-ui modules).
- Pagination partial: `resources/views/vendor/pagination/blue.blade.php`.
