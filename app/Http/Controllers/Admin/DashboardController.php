<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Leads\Models\Lead;
use App\Domains\Portfolio\Models\PortfolioProject;
use App\Domains\Products\Models\Product;
use App\Domains\Services\Models\Service;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Blue Control dashboard — visibility, not analytics. Counts use indexed
     * status columns; no premature metrics tables.
     */
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'products' => Product::query()->published()->count(),
                'portfolio' => PortfolioProject::query()->published()->count(),
                'services' => Service::query()->published()->count(),
                'new_leads' => Lead::query()->where('status', Lead::STATUS_NEW)->count(),
            ],
            'recentActivity' => ActivityLog::query()
                ->with('actor')
                ->latest()
                ->limit(10)
                ->get(),
            'totalLeads' => Lead::query()->count(),
        ]);
    }
}
