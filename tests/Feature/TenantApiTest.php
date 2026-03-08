<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

beforeEach(function () {
    asCloud();

    $this->token = Str::random();

    DB::table('machine_api_tokens')
        ->insert(['api_token' => hash('sha256', $this->token)]);

    $this->headers = [
        'Authorization' => "Bearer {$this->token}",
    ];
});

it('cant access cloud endpoints from self hosted', function () {
    asSelfHosted()
        ->getJson('/api/tenants')
        ->assertNotFound();
});

it('needs a machine token to access endpoints', function () {
    $this->getJson('/api/tenants')
        ->assertUnauthorized();
});

it('can get tenants', function () {
    Tenant::factory()->count(3)->create();

    $json = $this->getJson('/api/tenants', $this->headers)
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links'])
        ->json();

    $this->assertCount(4, $json['data']);
});

it('can create a tenant', function () {
    Queue::fake();

    $data = [
        'name' => fake()->company(),
        'domain' => fake()->domainName(),
        'license' => fake()->uuid(),
        'subscription_started_at' => now()->subMonth()->toDateTimeString(),
        'subscription_expires_at' => now()->addYear()->toDateTimeString(),
    ];

    $this->postJson('/api/tenants', $data, $this->headers)
        ->assertCreated();

    $this->assertTrue(
        Tenant::query()->where('license', $data['license'])->exists()
    );
});
