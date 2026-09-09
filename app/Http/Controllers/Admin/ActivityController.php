<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __invoke(Request $request): View
    {
        $this->authorize('activity.view');

        return view('admin.activity.index', [
            'entries' => ActivityLog::query()
                ->with('actor')
                ->latest()
                ->paginate(25)
                ->withQueryString(),
        ]);
    }
}
