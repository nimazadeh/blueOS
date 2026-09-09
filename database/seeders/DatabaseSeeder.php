<?php

namespace Database\Seeders;

use App\Core\Settings\SettingsService;
use Illuminate\Database\Seeder;

/**
 * Base seeder.
 *
 * Phase 1A deliberately creates NO business content and NO demo users:
 * - no fake clients, portfolio items, products or statistics (brief rule);
 * - the admin account is created via `php artisan blue:create-admin` (or
 *   tinker) using real credentials — never seeded.
 *
 * Only platform defaults that are true and needed for boot are written.
 */
class DatabaseSeeder extends Seeder
{
    public function run(SettingsService $settings): void
    {
        // Honest platform defaults only (no fabricated claims).
        $settings->set('site.name', 'Blue Studio', 'string', isPublic: true);
    }
}
