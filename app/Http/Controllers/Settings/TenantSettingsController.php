<?php

namespace App\Http\Controllers\Settings;

use App\Enums\Sis;
use App\Forms\SmtpForm;
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
    public function edit()
    {
        $title = __('Tenant Settings');
        $smtpForm = config('app.self_hosted')
            ? new SmtpForm(Tenant::current())
            : null;

        return inertia('settings/Tenant', [
            'title' => $title,
            'tenant' => Tenant::current()->toArray(),
            'smtpForm' => $smtpForm?->toInertia(),
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
            'sis_provider' => ['required', Rule::enum(Sis::class)],
            'allow_password_auth' => ['required', 'boolean'],
            'allow_oidc_login' => ['required', 'boolean'],
            'smtp_config' => ['array'],
            'smtp_config.host' => [Rule::requiredIf(config('app.self_hosted'))],
            'smtp_config.port' => [Rule::requiredIf(config('app.self_hosted'))],
            'smtp_config.username' => ['nullable'],
            'smtp_config.password' => ['nullable'],
            'smtp_config.from_name' => [Rule::requiredIf(config('app.self_hosted'))],
            'smtp_config.from_address' => [Rule::requiredIf(config('app.self_hosted')), 'email'],
            'smtp_config.encryption' => ['nullable'],
            ...$tenant->getInstallationFields()
                ->toValidationRules(),
        ]);

        $tenant->update($data);

        session()->flash('success', __('Settings updated successfully.'));

        return back();
    }
}
