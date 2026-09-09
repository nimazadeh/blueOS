<?php

namespace App\Domains\Services\Policies;

use App\Domains\Services\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('services.update');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('services.create');
    }

    public function update(User $user, Service $service): bool
    {
        return $user->hasPermission('services.update');
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->hasPermission('services.delete');
    }

    public function publish(User $user, Service $service): bool
    {
        return $user->hasPermission('services.publish');
    }
}
