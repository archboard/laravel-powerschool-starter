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
        URL::forceRootUrl(config('app.url'));

        Inertia::share('tenant', function () use ($tenant) {
            return new TenantResource($tenant);
        });
    }

    public function forgetCurrent(): void
    {
        Config::set('app.url', $this->originalUrl);

        URL::forceRootUrl($this->originalUrl);
    }
}
