<?php

namespace App\Domains\Portfolio\Policies;

use App\Domains\Portfolio\Models\PortfolioProject;
use App\Models\User;

class PortfolioProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('portfolio.update');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('portfolio.create');
    }

    public function update(User $user, PortfolioProject $project): bool
    {
        return $user->hasPermission('portfolio.update');
    }

    public function delete(User $user, PortfolioProject $project): bool
    {
        return $user->hasPermission('portfolio.delete');
    }

    public function publish(User $user, PortfolioProject $project): bool
    {
        return $user->hasPermission('portfolio.publish');
    }
}
