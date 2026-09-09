<?php

namespace Database\Seeders;

use App\Domains\Products\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Starter technology taxonomy (real technologies only — the exact list the
 * studio stated in the Phase 2 brief). Operators extend it in Blue Control
 * (products/portfolio forms); domain code never hardcodes technology names.
 */
class TechnologySeeder extends Seeder
{
    /**
     * @var array<string, string> name => category
     */
    private const CATALOG = [
        'Laravel' => 'backend',
        'PHP' => 'backend',
        'MySQL' => 'database',
        'Python' => 'backend',
        'JavaScript' => 'frontend',
        'Three.js' => 'frontend',
        'SCSS' => 'frontend',
        'Vue' => 'frontend',
        'React' => 'frontend',
        'Telegram API' => 'integration',
    ];

    public function run(): void
    {
        foreach (self::CATALOG as $name => $category) {
            Technology::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'category' => $category],
            );
        }
    }
}
