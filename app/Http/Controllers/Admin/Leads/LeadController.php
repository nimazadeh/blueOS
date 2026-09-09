<?php

namespace App\Http\Controllers\Admin\Leads;

use App\Domains\Leads\Actions\AddLeadNote;
use App\Domains\Leads\Actions\ChangeLeadStatus;
use App\Domains\Leads\Http\Requests\StoreLeadNoteRequest;
use App\Domains\Leads\Http\Requests\UpdateLeadStatusRequest;
use App\Domains\Leads\Models\Lead;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lead::class);

        $status = $request->query('status');
        $status = is_string($status) && in_array($status, Lead::STATUSES, true) ? $status : null;

        return view('admin.leads.index', [
            'leads' => Lead::query()
                ->status($status)
                ->ordered()
                ->paginate(15)
                ->withQueryString(),
            'statuses' => Lead::STATUSES,
            'activeStatus' => $status,
        ]);
    }

    public function show(Lead $lead): View
    {
        $this->authorize('view', $lead);

        return view('admin.leads.show', [
            'lead' => $lead->load('notes.user', 'statusHistory.user'),
            'statuses' => Lead::STATUSES,
        ]);
    }

    public function updateStatus(
        UpdateLeadStatusRequest $request,
        Lead $lead,
        ChangeLeadStatus $changeStatus,
    ): RedirectResponse {
        $changeStatus($lead, $request->validated('status'));

        return back()->with('status', __('blue.admin.flash.updated'));
    }

    public function storeNote(
        StoreLeadNoteRequest $request,
        Lead $lead,
        AddLeadNote $addNote,
    ): RedirectResponse {
        $addNote($lead, $request->validated('note'));

        return back()->with('status', __('blue.admin.flash.updated'));
    }
}
