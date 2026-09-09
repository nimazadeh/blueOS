<?php

namespace App\Domains\Leads\Policies;

use App\Domains\Leads\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('leads.manage');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->hasPermission('leads.manage');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->hasPermission('leads.manage');
    }
}
