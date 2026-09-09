<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Blue Control dashboard placeholder.
     *
     * Phase 1A only proves the admin boundary: this route requires the
     * `admin` guard and renders the admin layout. Real metrics and widgets
     * arrive with the Blue Control phase.
     */
    public function __invoke()
    {
        /** @var User $user */
        $user = auth('admin')->user();

        return view('admin.dashboard', [
            'user' => $user,
        ]);
    }
}
