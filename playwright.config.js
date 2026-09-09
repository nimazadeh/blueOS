import { defineConfig, devices } from '@playwright/test';

/**
 * E2E configuration (Phase 1B foundation).
 *
 * Requires a running application (default: `php artisan serve` on :8000).
 * Browser binaries are not available in the sandbox — run
 * `npx playwright install chromium` once on the provisioned dev machine/CI.
 */
export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    reporter: process.env.CI ? 'github' : 'list',
    use: {
        baseURL: 'http://127.0.0.1:8000',
        trace: 'on-first-retry',
    },
    webServer: {
        command: 'php artisan serve --host=127.0.0.1 --port=8000',
        url: 'http://127.0.0.1:8000/health',
        reuseExistingServer: !process.env.CI,
        timeout: 60_000,
    },
    projects: [
        {
            name: 'chromium-desktop',
            use: { ...devices['Desktop Chrome'] },
        },
        {
            name: 'chromium-mobile',
            use: { ...devices['Pixel 7'] },
        },
    ],
});
