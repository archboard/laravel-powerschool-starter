<?php

namespace App\Http\Controllers;

use App\Jobs\SyncSchools;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Silber\Bouncer\BouncerFacade;

class CreateTenantController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        $tenant = Tenant::firstOrNew($request->only('license'));

        // Call home to validate the license?
        $data = $request->validate([
            'license' => 'required|uuid',
            'name' => 'required',
            'domain' => [
                'required',
                Rule::unique('tenants')->ignoreModel($tenant),
            ],
            'ps_url' => 'required|url',
            'ps_client_id' => 'required|uuid',
            'ps_secret' => 'required|uuid',
            'email' => 'required|email',
        ]);

        // Save the tenant
        $tenant->forceFill(Arr::except($data, 'email'));
        $tenant->save();

        // Save the user and give them full privileges
        /** @var User $user */
        $user = $tenant->users()->updateOrCreate(Arr::only($data, 'email'));
        BouncerFacade::scope()->to($tenant->id);
        BouncerFacade::allow($user)->everything();
        auth()->login($user);

        // Kick off job to sync schools
        dispatch(new SyncSchools($tenant));

        session()->flash('success', __('Installation complete. Sync has been started.'));

        return back();
    }
}
