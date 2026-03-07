<?php

namespace App\Http\Controllers;

use App\Data\SmtpSettingsData;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;

class UpdateSmtpSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SmtpSettingsData $data, Tenant $tenant): RedirectResponse
    {
        $tenant->smtp_config = collect($data->toArray());
        $tenant->save();

        session()->flash('success', __('SMTP settings updated.'));

        return to_route('settings.tenant.edit');
    }
}
