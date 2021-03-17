<?php

namespace App\Tasks;

use App\Http\Resources\TenantResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class ChangeConfigTask implements SwitchTenantTask
{
    private string $originalUrl;

    public function makeCurrent(Tenant $tenant): void
    {
        $this->originalUrl = config('app.url');

        Config::set('app.url', "https://{$tenant->domain}");
        Config::set('powerschool.server_address', $tenant->ps_url);
        Config::set('powerschool.client_id', $tenant->ps_client_id);
        Config::set('powerschool.client_secret', $tenant->ps_secret);

        URL::forceRootUrl(config('app.url'));

        Inertia::share('tenant', function () use ($tenant) {
            return new TenantResource($tenant);
        });

        Inertia::share('psEnabled', function () use ($tenant) {
            return $tenant->ps_url &&
                $tenant->ps_client_id &&
                $tenant->ps_secret;
        });
    }

    public function forgetCurrent(): void
    {
        Config::set('app.url', $this->originalUrl);
        Config::set('powerschool.server_address');
        Config::set('powerschool.client_id');
        Config::set('powerschool.client_secret');

        URL::forceRootUrl($this->originalUrl);
    }
}
