<?php

namespace Database\Seeders;

use App\Domains\Admin\Models\Permission;
use App\Domains\Admin\Models\Role;
use Illuminate\Database\Seeder;

/**
 * RBAC seed (real, minimal role matrix):
 *
 *   owner  → everything (Gate::before also bypasses)
 *   admin  → everything except settings.manage
 *   editor → content create/update + leads/media management
 *
 * No demo users are created.
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * @var array<string, array{name: string, group: string}>
     */
    private const PERMISSIONS = [
        'products.create' => ['name' => 'Create products', 'group' => 'products'],
        'products.update' => ['name' => 'Update products', 'group' => 'products'],
        'products.delete' => ['name' => 'Delete products', 'group' => 'products'],
        'products.publish' => ['name' => 'Publish products', 'group' => 'products'],
        'portfolio.create' => ['name' => 'Create portfolio projects', 'group' => 'portfolio'],
        'portfolio.update' => ['name' => 'Update portfolio projects', 'group' => 'portfolio'],
        'portfolio.delete' => ['name' => 'Delete portfolio projects', 'group' => 'portfolio'],
        'portfolio.publish' => ['name' => 'Publish portfolio projects', 'group' => 'portfolio'],
        'services.create' => ['name' => 'Create services', 'group' => 'services'],
        'services.update' => ['name' => 'Update services', 'group' => 'services'],
        'services.delete' => ['name' => 'Delete services', 'group' => 'services'],
        'services.publish' => ['name' => 'Publish services', 'group' => 'services'],
        'leads.manage' => ['name' => 'Manage leads', 'group' => 'leads'],
        'media.manage' => ['name' => 'Manage media', 'group' => 'media'],
        'settings.manage' => ['name' => 'Manage settings', 'group' => 'settings'],
        'activity.view' => ['name' => 'View activity log', 'group' => 'activity'],
    ];

    public function run(): void
    {
        $permissions = collect(self::PERMISSIONS)->mapWithKeys(function (array $meta, string $slug) {
            $permission = Permission::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $meta['name'], 'group' => $meta['group']],
            );

            return [$slug => $permission];
        });

        $owner = Role::query()->updateOrCreate(
            ['slug' => 'owner'],
            ['name' => 'Owner', 'description' => 'Full control of Blue Studio OS.'],
        );

        $admin = Role::query()->updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'Operational management; no platform settings.'],
        );

        $editor = Role::query()->updateOrCreate(
            ['slug' => 'editor'],
            ['name' => 'Editor', 'description' => 'Creates and updates published content; no destructive actions.'],
        );

        $owner->permissions()->sync($permissions->pluck('id')->all());

        $admin->permissions()->sync(
            $permissions->except('settings.manage')->pluck('id')->all(),
        );

        $editor->permissions()->sync(
            $permissions->only([
                'products.create', 'products.update',
                'portfolio.create', 'portfolio.update',
                'services.create', 'services.update',
                'leads.manage',
                'media.manage',
            ])->pluck('id')->all(),
        );
    }
}
