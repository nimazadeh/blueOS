<?php

namespace App\Models;

use App\Domains\Admin\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Platform user.
 *
 * The same table backs the `web` and `admin` guards. RBAC is internal
 * (roles/permissions tables — see docs/admin.md) and intentionally close to
 * spatie/laravel-permission's shape for a low-cost later swap.
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'is_active',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array<string, string>|null per-request permission cache */
    private ?array $permissionCache = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    /**
     * Effective permission slugs via assigned roles.
     *
     * @return list<string>
     */
    public function permissionSlugs(): array
    {
        if ($this->permissionCache === null) {
            $this->permissionCache = $this->roles()
                ->with('permissions')
                ->get()
                ->flatMap(fn (Role $role) => $role->permissions->pluck('slug'))
                ->unique()
                ->values()
                ->all();
        }

        return $this->permissionCache;
    }

    public function hasRole(string|array $slugs): bool
    {
        $slugs = (array) $slugs;

        return $this->roles()->whereIn('slug', $slugs)->exists();
    }

    public function hasPermission(string $slug): bool
    {
        return in_array($slug, $this->permissionSlugs(), true);
    }

    /**
     * Assign one or more roles by slug (keeps existing assignments).
     */
    public function assignRole(string ...$slugs): self
    {
        $roles = Role::query()->whereIn('slug', $slugs)->get();

        $this->roles()->syncWithoutDetaching($roles->pluck('id')->all());
        $this->permissionCache = null;

        return $this;
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }
}
