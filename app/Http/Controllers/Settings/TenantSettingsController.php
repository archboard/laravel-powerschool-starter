<?php

namespace App\Http\Controllers\Settings;

use App\Data\SmtpSettingsData;
use App\Data\TenantSettingsData;
use App\Enums\Sis;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;

class TenantSettingsController extends Controller
{
    /**
     * Shows the tenant settings form
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function edit(Tenant $tenant)
    {
        $title = __('Tenant Settings');
        $schools = $tenant->schools()
            ->orderBy('name')
            ->get();

        return inertia('settings/Tenant', [
            'title' => $title,
            'tenantSettings' => TenantSettingsData::fromTenant($tenant),
            'smtpSettings' => config('app.self_hosted') ? SmtpSettingsData::fromTenant($tenant) : null,
            'sisOptions' => Sis::selectOptions(),
            'schools' => $schools->map(fn (School $school) => [
                'id' => $school->id,
                'name' => $school->name,
                'active' => $school->active,
            ]),
            'editable' => config('app.self_hosted'),
        ])->withViewData(compact('title'));
    }

    /**
     * Updates attributes for the tenant
     */
    public function update(TenantSettingsData $data, Tenant $tenant): RedirectResponse
    {
        $tenant->update($data->toArray());

        session()->flash('success', __('Settings updated successfully.'));

        return to_route('settings.tenant.edit');
    }
}
