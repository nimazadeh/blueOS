<?php

namespace App\Domains\Products\Policies;

use App\Domains\Products\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('products.update');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('products.create');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasPermission('products.update');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermission('products.delete');
    }

    public function publish(User $user, Product $product): bool
    {
        return $user->hasPermission('products.publish');
    }
}
