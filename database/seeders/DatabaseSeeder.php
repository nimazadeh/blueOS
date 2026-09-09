<?php

namespace Database\Seeders;

use App\Core\Settings\SettingsService;
use Illuminate\Database\Seeder;

/**
 * Platform seeder.
 *
 * Deliberately creates NO business content and NO demo users:
 * - no fake products/portfolio/services/leads (brief rule);
 * - the admin account is created via tinker/`php artisan` using real
 *   credentials — never seeded;
 * - only TRUE platform defaults (site identity, role/permission matrix) and
 *   the real technology taxonomy are written.
 */
class DatabaseSeeder extends Seeder
{
    public function run(SettingsService $settings): void
    {
        $settings->set('site.name', 'Blue Studio', 'string', isPublic: true);

        $this->call([
            RolePermissionSeeder::class,
            TechnologySeeder::class,
        ]);
    }
}
