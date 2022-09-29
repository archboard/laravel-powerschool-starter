<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenantSettingsController extends Controller
{
    /**
     * Shows the tenant settings form
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $title = __('Tenant Settings');

        return inertia('settings/Tenant', [
            'title' => $title,
            'tenant' => Tenant::current()->toArray(),
        ])->withViewData(compact('title'));
    }

    /**
     * Updates attributes for the tenant
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $tenant = $request->tenant();

        $data = $request->validate([
            'name' => ['required'],
            'ps_url' => 'required|url',
            'ps_client_id' => 'required|uuid',
            'ps_secret' => 'required|uuid',
            'allow_password_auth' => 'required|boolean',
            'smtp_host' => [Rule::requiredIf(!config('app.cloud'))],
            'smtp_port' => [Rule::requiredIf(!config('app.cloud'))],
            'smtp_username' => ['nullable'],
            'smtp_password' => ['nullable'],
            'smtp_from_name' => [Rule::requiredIf(!config('app.cloud'))],
            'smtp_from_address' => [Rule::requiredIf(!config('app.cloud')), 'email'],
            'smtp_encryption' => ['nullable'],
        ]);

        Tenant::current()->update($data);

        session()->flash('success', __('Settings updated successfully.'));

        return back();
    }
}
