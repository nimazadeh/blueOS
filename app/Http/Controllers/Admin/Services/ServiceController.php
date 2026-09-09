<?php

namespace App\Http\Controllers\Admin\Services;

use App\Domains\Services\Actions\ChangeServiceStatus;
use App\Domains\Services\Actions\CreateService;
use App\Domains\Services\Actions\DeleteService;
use App\Domains\Services\Actions\UpdateService;
use App\Domains\Services\Http\Requests\StoreServiceRequest;
use App\Domains\Services\Http\Requests\UpdateServiceRequest;
use App\Domains\Services\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Service::class);

        $status = $request->query('status');
        $status = is_string($status) && in_array($status, Service::STATUSES, true) ? $status : null;

        return view('admin.services.index', [
            'services' => Service::query()
                ->when($status, fn ($query) => $query->where('status', $status))
                ->orderBy('sort_order')
                ->paginate(15)
                ->withQueryString(),
            'statuses' => Service::STATUSES,
            'activeStatus' => $status,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Service::class);

        return view('admin.services.create', [
            'statuses' => Service::STATUSES,
        ]);
    }

    public function store(StoreServiceRequest $request, CreateService $createService): RedirectResponse
    {
        $service = $createService($request->validated());

        return redirect()
            ->route('admin.services.edit', $service)
            ->with('status', __('blue.admin.flash.created'));
    }

    public function edit(Service $service): View
    {
        $this->authorize('update', $service);

        return view('admin.services.edit', [
            'service' => $service,
            'statuses' => Service::STATUSES,
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service, UpdateService $updateService): RedirectResponse
    {
        $updateService($service, $request->validated());

        return redirect()
            ->route('admin.services.edit', $service)
            ->with('status', __('blue.admin.flash.updated'));
    }

    public function destroy(Service $service, DeleteService $deleteService): RedirectResponse
    {
        $this->authorize('delete', $service);

        $deleteService($service);

        return redirect()
            ->route('admin.services.index')
            ->with('status', __('blue.admin.flash.deleted'));
    }

    public function publish(Service $service, ChangeServiceStatus $changeStatus): RedirectResponse
    {
        $this->authorize('publish', $service);

        $changeStatus($service, Service::STATUS_PUBLISHED);

        return back()->with('status', __('blue.admin.flash.published'));
    }

    public function unpublish(Service $service, ChangeServiceStatus $changeStatus): RedirectResponse
    {
        $this->authorize('publish', $service);

        $changeStatus($service, Service::STATUS_DRAFT);

        return back()->with('status', __('blue.admin.flash.unpublished'));
    }
}
