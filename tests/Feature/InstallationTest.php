<?php

use App\Jobs\SyncSchools;
use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Uri;
use Inertia\Testing\AssertableInertia;

function fakeLicenseValidation(bool $valid = true): void
{
    Http::fake([
        'archboard.io/verify/*' => Http::response(compact('valid')),
    ]);
}

function getPowerSchoolInstallationRequest(array $attributes = []): array
{
    return [
        'name' => fake()->company(),
        'domain' => Uri::of(config('app.url'))->host(),
        'sis_config' => [
            'url' => env('POWERSCHOOL_ADDRESS'),
            'client_id' => env('POWERSCHOOL_CLIENT_ID'),
            'client_secret' => env('POWERSCHOOL_CLIENT_SECRET'),
        ],
        ...$attributes,
    ];
}

it('cant access installation on cloud', function () {
    asCloud()
        ->get('/install')
        ->assertNotFound();
});

it('cant access installation when already installed', function () {
    $this->get('/install')
        ->assertNotFound();
});

it('cant access installation when user has no permission', function () {
    logIn();
    $this->tenant->update(['sis_config' => null]);

    $this->get('/install')
        ->assertNotFound();
});

it('redirects to install page when not installed and unauthenticated', function () {
    asSelfHosted();
    $this->tenant->update(['sis_config' => null]);

    $this->get('/login')
        ->assertRedirect('/install');
});

it('redirects to install page when not installed and authenticated', function () {
    asSelfHosted();
    logIn();
    $this->tenant->update(['sis_config' => null]);

    $this->get('/')
        ->assertRedirect('/install');
});

it('can view installation page unauthenticated', function () {
    $this->tenant->update(['sis_config' => null]);
    asSelfHosted();

    $this->get('/install')
        ->assertViewHas('title')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('title')
            ->has('installationValues')
            ->has('isCloud')
            ->component('Install')
        );
});

it('can view installation page authenticated', function () {
    $this->tenant->update(['sis_config' => null]);
    asSelfHosted();
    logIn();

    $this->user->allow()->everything();

    $this->get('/install')
        ->assertViewHas('title')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('title')
            ->has('installationValues')
            ->has('isCloud')
            ->component('Install')
        );
});

it('can successfully install without existing tenant', function () {
    Queue::fake();

    $this->tenant->delete();
    Tenant::forgetCurrent();

    $data = getPowerSchoolInstallationRequest();

    fakeLicenseValidation();

    asSelfHosted()
        ->post('/install', $data)
        ->assertSessionHas('success')
        ->assertRedirect(route('install.user'));

    $this->assertDatabaseHas('tenants', Arr::only($data, ['name', 'domain']));
    $tenant = Tenant::firstWhere('domain', $data['domain']);
    $this->assertEquals($tenant->sis_config->toArray(), $data['sis_config']);

    Queue::assertPushed(SyncSchools::class);
});

it('can successfully install with existing tenant', function () {
    Queue::fake();

    $data = getPowerSchoolInstallationRequest();

    fakeLicenseValidation();
    $this->tenant->update(['sis_config' => null]);

    asSelfHosted()
        ->postJson('/install', $data)
        ->assertSessionHas('success')
        ->assertRedirect(route('install.user'));

    $this->assertDatabaseHas('tenants', Arr::only($data, ['name', 'domain']));
    $tenant = Tenant::firstWhere('domain', $data['domain']);
    $this->assertEquals($tenant->sis_config->toArray(), $data['sis_config']);

    Queue::assertPushed(SyncSchools::class);
});

it('cant view user selection when uninstalled', function () {
    $this->tenant->update(['sis_config' => null]);

    asSelfHosted()
        ->get(route('install.user'))
        ->assertRedirect(route('install'));
});

it('cant view user selection when admin user already exists', function () {
    $admin = seedUser();
    $admin->assignRole(App\Enums\Role::DISTRICT_ADMIN);

    asSelfHosted()
        ->get(route('install.user'))
        ->assertSessionHas('error')
        ->assertRedirect();
});

it('can view user import page', function () {
    asSelfHosted()
        ->get(route('install.user'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('InstallUser')
            ->where('endpoint', route('install.user'))
        );
});
