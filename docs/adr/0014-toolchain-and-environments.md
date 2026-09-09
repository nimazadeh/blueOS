# ADR-014 — Toolchain & Environments (Sandbox Gap)

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

Phase 0 environment discovery found: Node 22.22.3 / npm 10.9.8 / Git 2.39.5 present;
**PHP, Composer, MySQL, browsers, Docker absent**; `apt` and Packagist unreachable;
npm and GitHub API reachable. The requested stack (Laravel + MySQL) cannot run in
this sandbox today.

## Decision

- **Phase 0 deliverable is documentation-only** (no app code required by the brief),
  and it is fully achievable in the sandbox — delivered.
- **Development environment (Phase 1+):** Laravel Herd/VSCode or Docker Compose on
  a developer machine or staging VM with PHP 8.5, Composer 2.10, MySQL 8.x
  (compose files + docs are part of Phase 1 scaffold). This sandbox remains a
  review/documentation workspace; its npm tooling can build frontend assets but not
  run Laravel.
- **If sandbox runtime is required:** outbound access to `deb.debian.org` **and**
  `repo.packagist.org` (or a proxy) must be enabled by the host; without both,
  no PHP/Composer/MySQL provisioning is possible (apt blocked + Packagist blocked).
- **Browser automation:** Playwright CLI is present but browser binaries cannot be
  downloaded (`cdn.playwright.dev` blocked) — E2E runs on provisioned dev machine
  or CI.
- Environments (`local/development/staging/production`) and their config rules are
  defined in [`../deployment.md`](../deployment.md); no secrets in source.

## Alternatives considered

1. **Install PHP via static binaries from GitHub releases** — blocked: release
   asset domains unreachable.
2. **Install via apt** — blocked: `deb.debian.org` unreachable.
3. **Use SQLite instead of MySQL in the sandbox** — rejected: silently replaces the
   requested stack (brief forbids).
4. **Use a Node-based backend** — rejected: brief baseline honored.
5. **Defer Phase 0 entirely until toolchain fixed** — rejected: no documentation
   work depends on PHP; deliver now, provision before Phase 1 code.

## Consequences

- ✅ Phase 0 completes with full documentation; no misleading claims about runnable code.
- ✅ Clear, honest provisioning path for Phase 1 (with exact blockers spelled out).
- ⚠️ Phase 1 cannot start coding in this sandbox until runtime is provisioned or
  owner approves the primary dev-machine path.
- ⚠️ Risk: if no provisioned environment becomes available, implementation is
  confined to frontend assets/docs; explicitly tracked in
  [`../phase-0-report.md`](../phase-0-report.md) risks.
