import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

/**
 * Phase 1B E2E foundation.
 * Runs against `php artisan serve` (see playwright.config.js webServer).
 */

test.describe('Homepage — Phase 1B foundation', () => {
    test('loads and renders the hero', async ({ page }) => {
        await page.goto('/');

        await expect(page).toHaveTitle(/Blue Studio/);
        await expect(page.locator('h1')).toBeVisible();
        await expect(page.locator('#start-project')).toBeVisible();
    });

    test('serves canonical + OG metadata', async ({ page }) => {
        await page.goto('/');

        await expect(page.locator('link[rel="canonical"]')).toHaveAttribute(
            'href',
            /^https?:\/\/.+\/$/,
        );
        await expect(page.locator('meta[property="og:type"]')).toHaveAttribute(
            'content',
            'website',
        );
        await expect(page.locator('meta[name="description"]')).not.toBeEmpty();
    });

    test('has default LTR for English', async ({ page }) => {
        await page.goto('/');

        await expect(page.locator('html')).toHaveAttribute('dir', 'ltr');
        await expect(page.locator('html')).toHaveAttribute('lang', 'en');
    });

    test('switches to Persian (RTL) through the footer locale switcher', async ({ page }) => {
        await page.goto('/');

        await page.locator('[data-locale-switcher] button').click();

        // redirect()->back() re-renders the same URL in Persian.
        await expect(page).toHaveTitle(/بلو استودیو/);
        await expect(page.locator('html')).toHaveAttribute('dir', 'rtl');
        await expect(page.locator('html')).toHaveAttribute('lang', 'fa');
    });

    test('renders section navigation anchors and empty states (no fake content)', async ({ page }) => {
        await page.goto('/');

        for (const id of ['products', 'services', 'portfolio', 'start-project']) {
            await expect(page.locator(`#${id}`)).toBeVisible();
        }

        // Both collections are intentionally empty at this phase.
        await expect(page.getByText('No products published yet')).toBeVisible();
        await expect(page.getByText('No portfolio projects yet')).toBeVisible();
    });

    test('opens and closes the mobile navigation drawer', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'chromium-mobile', 'drawer only exists on mobile viewport');

        await page.goto('/');

        const toggle = page.locator('[data-nav-toggle]');
        await expect(toggle).toBeVisible();
        await toggle.click();

        const drawer = page.locator('#mobile-nav');
        await expect(drawer).toHaveAttribute('aria-hidden', 'false');
        await expect(toggle).toHaveAttribute('aria-expanded', 'true');

        await page.keyboard.press('Escape');
        await expect(drawer).toHaveAttribute('aria-hidden', 'true');
        await expect(toggle).toHaveAttribute('aria-expanded', 'false');
    });

    test('keeps focus management inside the drawer', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'chromium-mobile', 'drawer only exists on mobile viewport');

        await page.goto('/');
        await page.locator('[data-nav-toggle]').click();

        const close = page.locator('#mobile-nav button[data-drawer-close][aria-label]').first();
        await expect(close).toBeFocused();

        await page.keyboard.press('Tab');
        await expect(page.locator('#mobile-nav a').first()).toBeFocused();
    });

    test('has no serious accessibility violations (axe smoke)', async ({ page }) => {
        await page.goto('/');

        const results = await new AxeBuilder({ page }).analyze();

        const serious = results.violations.filter(
            (v) => ['serious', 'critical'].includes(v.impact),
        );
        expect(serious, JSON.stringify(serious, null, 2)).toEqual([]);
    });
});
