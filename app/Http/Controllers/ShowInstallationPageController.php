<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class ShowInstallationPageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Tenant $tenant
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function __invoke(Request $request)
    {
        $title = __('Install FMS');
        $tenant = Tenant::firstOrNew(['domain' => $request->getHost()]);

        return inertia('Install', [
            'title' => $title,
            'tenant' => [
                'name' => $tenant->name,
                'domain' => $tenant->domain,
                'ps_url' => $tenant->ps_url,
                'ps_client_id' => $tenant->ps_client_id,
                'ps_secret' => $tenant->ps_secret,
                'license' => $tenant->license,
            ],
            'email' => optional($request->user())->email,
        ])->withViewData(compact('title'));
    }
}
