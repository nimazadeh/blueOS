# Environment Record — Phase 0 Discovery

**Date of discovery:** 2026-09-09
**How:** direct inspection of the sandbox (`uname`, `php --version`, `composer --version`,
`node --version`, `npm --version`, `git --version`, `mysql --version`, process/port scans,
connectivity probes). No version was assumed; everything below was observed.

## 1. Host

| Item | Discovered value |
|---|---|
| OS | Debian GNU/Linux 12 (bookworm) |
| Kernel | Linux 6.1.158 (x86_64) |
| CPU | 2 vCPU |
| RAM | 3.8 GiB total, ~3.5 GiB free (no swap) |
| Disk | ~20 GiB free on `/` |
| Locales installed | `C`, `C.utf8`, `POSIX` (no `fa_IR`, not required for rendering) |
| Timezone context | User local timezone UTC (2026-09-09) |

## 2. Installed toolchain (observed versions)

| Tool | Status | Version |
|---|---|---|
| Git | ✅ installed | 2.39.5 |
| Node.js | ✅ installed | 22.22.3 |
| npm | ✅ installed | 10.9.8 |
| yarn | ✅ installed | 1.22.22 |
| corepack | ✅ installed | 0.34.6 |
| Python 3 (tooling only) | ✅ installed | 3.11 (`pip` 23.0.1) |
| curl / OpenSSL | ✅ installed | curl 7.88.1 / OpenSSL 3.0.20 |
| Playwright CLI (via npx) | ⚠️ CLI present | 1.63.0 — **browsers NOT installed** |
| PHP | ❌ **NOT installed** | no `php` binary anywhere on the system |
| Composer | ❌ **NOT installed** | no `composer` binary anywhere on the system |
| MySQL / MariaDB | ❌ **NOT installed** | no client, no server, no daemon |
| SQLite3 CLI | ❌ not installed | (SQLite is irrelevant to the chosen stack) |
| PostgreSQL | ❌ not installed | (not chosen; noted for completeness) |
| Docker / docker compose | ❌ **NOT installed** | — |
| Redis | ❌ not installed | (deferred; not required for v1) |
| Browser binaries (Chromium) | ❌ **NOT installed** | no browser in `PATH`; no `~/.cache/ms-playwright` |

## 3. Network reachability (observed, 2026-09-09)

| Endpoint | Result |
|---|---|
| `registry.npmjs.org` | ✅ HTTP 200 (npm installs work) |
| `github.com` (HTML) | ✅ HTTP 200 |
| `api.github.com` | ✅ HTTP 200 |
| `repo.packagist.org` | ❌ unreachable |
| `getcomposer.org` | ❌ unreachable |
| `deb.debian.org` (apt) | ❌ unreachable (HTTP/80 blocked) |
| `objects.githubusercontent.com` (GitHub release assets) | ❌ unreachable |
| `raw.githubusercontent.com` | ❌ unreachable |
| `cdn.playwright.dev` (browser binaries) | ❌ unreachable |
| `npmmirror.com`, aliyun mirrors | ❌ unreachable |

**Consequence — critical:** the sandbox **cannot** currently install PHP, Composer,
MySQL, or Playwright browsers, and even a hypothetically installed Composer could not
resolve packages from Packagist. The requested stack (Laravel + MySQL) is *not*
runnable in this sandbox as-is.

## 4. Current stack versions verified from live registries (not assumed)

Verified via `api.github.com` / `registry.npmjs.org` on 2026-09-09:

| Component | Latest observed | Notes |
|---|---|---|
| Laravel framework | **13.31.0** (2026-09-08) | requires PHP `^8.3` |
| Laravel skeleton | 13.10.1 | — |
| Composer | **2.10.3** (2026-08-27) | — |
| PHP | **8.5.10** stable (8.6 in beta) | minimum for Laravel 13: 8.3 |
| Vite | 8.2.2 | Node 22.22.3 satisfies its Node requirement |
| laravel-vite-plugin | 3.2.0 | — |
| sass (dart-sass) | 1.104.0 | modern API |
| GSAP | 3.15.0 | ScrollTrigger included |
| Lenis | 1.3.26 | — |
| three | 0.186.0 | — |
| Alpine.js | 3.17.2 | — |
| TypeScript | 7.0.2 latest (6.0 beta, 5.x stable line) | adopt **5.x LTS line** until TS 7 ecosystem is proven |
| @playwright/test | 1.63.0 | — |
| Vazirmatn (Persian font, npm) | 33.0.3 (also `@fontsource/vazirmatn` 5.3.0) | self-hostable via npm |

## 5. Repository state

| Item | Discovered value |
|---|---|
| Repository | `nimazadeh/blueOS` (GitHub, `origin`) |
| Git branch at discovery | `arena/01a086c9-blueos` (working branch) |
| Commits | 1 — `b9e71a7 Initial commit` |
| Tracked files | 1 — `README.md` (9 bytes: `# blueOS`) |
| Laravel project present | ❌ No |
| Schema / migrations | ❌ None |
| Assets | ❌ None |
| Deployment files | ❌ None |
| Tests | ❌ None |
| `.gitignore` | ❌ None (added in Phase 0) |
| Conclusion | **GREENFIELD** |

## 6. Gaps to resolve for Phase 1 (ordered)

1. **Runtime provisioning.** PHP ≥ 8.3 (target 8.5), Composer ≥ 2.10, MySQL 8.x,
   and a web server/queue for local development. Because this sandbox blocks apt and
   Packagist, the recommended path is:
   - **Primary:** develop on a local machine or staging VM using Laravel Herd /
     Docker Compose (PHP-FPM 8.5 + MySQL 8.0+ + optional Redis), and use the sandbox
     as a **documentation/review workspace** (all its npm tooling works).
   - **Secondary (if sandbox runtime is required):** the host must allow outbound
     access to `deb.debian.org` **and** `repo.packagist.org` (or a proxy for them).
     Without both, no Laravel install or `composer install` is possible.
2. **Browser automation.** `@playwright/test` can be installed from npm, but browser
   binaries cannot be downloaded here (`cdn.playwright.dev` blocked). E2E must run on
   a provisioned dev machine or CI.
3. **GitHub release assets** are also blocked, so static PHP builds cannot be fetched
   from release assets in this sandbox either.

## 7. What the sandbox CAN do today

- Author and review all documentation, ADRs, migrations/schema SQL, tokens, and
  front-end source (CSS/JS/TS) — anything that does not need PHP to execute.
- Run `npm` installs and Node-based tooling (SCSS compile, Vite builds, linting,
  TypeScript type-checking) once the front-end scaffold exists.
- Run git operations against GitHub.

This is why Phase 0's deliverable — **documentation — is fully achievable now**,
and why Phase 1 cannot execute Laravel code in this sandbox until provisioning is
resolved (see [`adr/0014-toolchain-and-environments.md`](adr/0014-toolchain-and-environments.md)
and [`deployment.md`](deployment.md)).
