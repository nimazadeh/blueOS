<?php

namespace App\Domains\Media\Policies;

use App\Domains\Media\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('media.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('media.manage');
    }

    public function update(User $user, Media $media): bool
    {
        return $user->hasPermission('media.manage');
    }

    public function delete(User $user, Media $media): bool
    {
        return $user->hasPermission('media.manage');
    }
}
