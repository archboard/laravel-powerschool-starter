<?php

namespace App\Http\Controllers;

use App\Data\InstallationValuesData;
use App\Data\StoreInstallationData;
use App\Jobs\SyncSchools;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class InstallationController extends Controller
{
    public function index(Request $request): Response
    {
        $title = __('Installation');
        $tenant = Tenant::fromRequestAndFallback($request);

        return inertia('Install', [
            'title' => $title,
            'installationValues' => InstallationValuesData::fromTenant($tenant),
            'isCloud' => (bool) config('app.cloud'),
        ])->withViewData(compact('title'));
    }

    public function store(StoreInstallationData $data): RedirectResponse
    {
        $tenant = Tenant::fromRequestAndFallback(request());

        $tenant->fill($data->toArray())->save();
        $tenant->makeCurrent();

        // Kick off job to sync schools
        dispatch(new SyncSchools($tenant));

        session()->flash('success', __('Installation complete. Sync has been started.'));

        return to_route('install.user');
    }
}
