<?php

namespace App\Http\Controllers\Admin;

use App\Core\Activity\ActivityLogger;
use App\Core\Settings\SettingsService;
use App\Domains\Admin\Http\Requests\UpdateSettingsRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Operator-facing settings. Never secrets — the settings table is read by
 * public pages for identity/SEO defaults only.
 */
class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingsService $settings,
        private readonly ActivityLogger $activity,
    ) {
    }

    public function index(): View
    {
        $this->authorize('settings.manage');

        return view('admin.settings.index', [
            'values' => [
                'site.name' => $this->settings->get('site.name', config('blue.site.name')),
                'site.description' => $this->settings->get('site.description', ''),
                'contact.email' => $this->settings->get('contact.email', ''),
                'contact.phone' => $this->settings->get('contact.phone', ''),
                'seo.default_description' => $this->settings->get('seo.default_description', ''),
            ],
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            $this->settings->set($key, $value, 'string', isPublic: str_starts_with($key, 'site.'));
        }

        $this->activity->record(
            auth('admin')->user(),
            'settings.updated',
            null,
            ['keys' => array_keys($request->validated())],
        );

        return back()->with('status', __('blue.admin.flash.updated'));
    }
}
