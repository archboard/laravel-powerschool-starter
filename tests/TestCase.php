<?php

namespace Tests;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected bool $signIn = false;
    protected Tenant $tenant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $domain = env('TESTING_APP_URL');

        $this->tenant = Tenant::factory()->create(compact('domain'));
        $this->tenant->domain = $domain;
        $this->tenant->makeCurrent();

        if ($this->signIn) {
            $this->logIn();
        }
    }

    public function logIn(User $user = null, array $attributes = [], string $role = 'admin'): static
    {
        /** @var User $user */
        $user = $user ?? User::factory()->create(array_merge(['tenant_id' => $this->tenant?->id], $attributes));

        $user->assign($role);
        $this->be($user);
        $this->user = $user;

        return $this;
    }

    public function asCloud(): static
    {
        config()->set('app.cloud', true);

        return $this;
    }

    public function asSelfHosted(): static
    {
        config()->set('app.cloud', false);
        config()->set('app.self_hosted', true);

        return $this;
    }

    public function tapUser(callable $callback): static
    {
        $callback($this->user);

        return $this;
    }
}
