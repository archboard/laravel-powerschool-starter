<?php

namespace App\Http\Controllers;

use App\Jobs\SyncSchools;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class InstallationController extends Controller
{
    public function index(Request $request)
    {
        $title = __('Installation');
        $tenant = Tenant::fromRequestAndFallback($request);

        return inertia('Install', [
            'title' => $title,
            'installationValues' => [
                'name' => $tenant->name,
                'domain' => $tenant->domain,
                'custom_domain' => $tenant->custom_domain,
                'sis_config' => [
                    'url' => $tenant->sis_config?->get('url'),
                    'client_id' => $tenant->sis_config?->get('client_id'),
                    'client_secret' => $tenant->sis_config?->get('client_secret'),
                ],
            ],
            'isCloud' => (bool) config('app.cloud'),
        ])->withViewData(compact('title'));
    }

    public function store(Request $request)
    {
        $tenant = Tenant::fromRequestAndFallback($request);

        $data = $request->validate($tenant->getInstallationRules());

        $tenant->fill(Arr::undot(Arr::except($data, 'email')))
            ->save();
        $tenant->makeCurrent();

        // Kick off job to sync schools
        dispatch(new SyncSchools($tenant));

        session()->flash('success', __('Installation complete. Sync has been started.'));

        return to_route('install.user');
    }
}
