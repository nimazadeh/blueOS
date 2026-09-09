<?php

/*
|--------------------------------------------------------------------------
| Pest configuration
|--------------------------------------------------------------------------
|
| Feature tests refresh the database (MySQL in CI, in-memory SQLite locally).
| Unit tests stay database-free.
|
*/

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in('Feature');

uses(Tests\TestCase::class)->in('Feature', 'Unit');

/**
 * Create a user with the given role and the real RBAC seed applied.
 */
function roleUser(string $role): User
{
    app(RolePermissionSeeder::class)->run(); // idempotent (updateOrCreate/sync)

    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}
