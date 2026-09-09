<?php

namespace App\Http\Controllers\Public;

use App\Domains\Leads\Actions\CreateLead;
use App\Domains\Leads\Http\Requests\StoreLeadRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class LeadSubmissionController extends Controller
{
    public function __invoke(StoreLeadRequest $request, CreateLead $createLead): RedirectResponse
    {
        $createLead($request->validated());

        return back()
            ->with('status', __('blue.leads.success'))
            ->with('lead_submitted', true);
    }
}
