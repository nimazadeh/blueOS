# Blue Studio OS — Testing Strategy

**Status:** Phase 1A (updated with the implemented tooling).

---

## 1. Tooling (decided)

| Layer | Tool | Command |
|---|---|---|
| Test framework | **Pest 5** (PHPUnit 13.3 under the hood) | `composer test` |
| Formatting | **Laravel Pint 1.31** | `composer format` / `./vendor/bin/pint --test` |
| Static analysis | **PHPStan + Larastan 3.11** (level 5, raised over time) | `composer analyse` |
| Browser E2E | Playwright — **deferred to Phase 1B+** (browser binaries were unavailable in this environment; CI will host them once added) | — |

## 2. How to run

```bash
composer test            # full suite (defaults to in-memory SQLite locally)
composer test:unit       # database-free unit tests
composer test:feature    # feature tests (RefreshDatabase)
./vendor/bin/pint --test # format check
composer analyse         # static analysis
```

Before running tests that render pages, `npm run build` must exist (the
`@vite` directive needs the manifest). CI builds first.

**Database in tests:** `phpunit.xml` uses `sqlite :memory:`;
the CI workflow overrides with the MySQL 8.4 service — the authoritative gate.

## 3. Implemented suites

### Unit (no database)

| Suite | Covers |
|---|---|
| `tests/Unit/Core/Slug/SlugServiceTest.php` | slugify (kebab-case), unicode transliteration, empty safety |
| `tests/Unit/Core/Seo/MetaResolverTest.php` | title suffix, description fallback + length, overrides, serialization |

### Feature (RefreshDatabase)

| Suite | Covers |
|---|---|
| `Feature/HomepageTest.php` | home 200, SEO head output, 404 page |
| `Feature/HealthTest.php` | `/health` DB connectivity (proves "database connects") |
| `Feature/Admin/AuthTest.php` | login form, guest redirect, valid login (admin guard), invalid credentials, inactive user blocked, logout/session invalidation, dashboard access + noindex |
| `Feature/Core/SettingsServiceTest.php` | settings get/set/upsert semantics |
| `Feature/Core/ActivityLoggerTest.php` | audit entries (actor/subject, system events) |
| `Feature/Core/MediaValidationTest.php` | MIME allowlist, size limit, decode-on-image, disk config |
| `Feature/Core/Slug/SlugUniquenessTest.php` | collision suffix, ignore-current-record |
| `Feature/Locale/RtlTest.php` | locale registry, `lang`/`dir` en + fa rendering, locale switch route, invalid locale 404 |

## 4. CI pipeline (`.github/workflows/ci.yml`)

```
PHP 8.5 · MySQL 8.4 service · Node 22
  1. composer install/update
  2. npm ci
  3. npm run build
  4. pint --test
  5. phpstan analyse
  6. pest (MySQL 8.4)
  7. artisan about (environment smoke)
```

Gates fail the workflow on any error — format/analysis/pristine tests are
release prerequisites from the first commit.

## 5. Planned additions (next phases)

- FormRequest validation matrices for domain forms (leads, content).
- RBAC permission tests once roles land.
- Playwright E2E: public journeys, admin auth flow, RTL, reduced-motion, axe.
- Pest arch tests enforcing the modular boundary (no cross-domain imports).
- Coverage threshold (≥80% on domain logic) once domains exist.

## 6. Policy reminders

- One behavior per test; deterministic; no sleeps/waits.
- No business content in factories that could leak (factories are test-scoped).
- Production seeder creates no demo accounts (see `DatabaseSeeder`).
- CI runs MySQL, never SQLite-only, because schema differences must not hide.
