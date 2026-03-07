<?php

use App\Providers\RouteServiceProvider;
use Inertia\Testing\AssertableInertia;

it('tenant without passwords cant login', function () {
    $user = seedUser();
    $this->tenant->update(['allow_password_auth' => false]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertNotFound();
});

it('login screen can be rendered', function () {
    $this->get('/login')
        ->assertOk()
        ->assertViewHas('title')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Auth/Login')
            ->has('title')
            ->has('status')
        );
});

it('login screen can be rendered when passwords are disabled', function () {
    $this->tenant->update(['allow_password_auth' => false]);

    $this->get('/login')
        ->assertOk()
        ->assertViewHas('title')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Auth/Login')
            ->has('title')
            ->has('status')
        );
});

it('users can authenticate using the login screen', function () {
    $user = seedUser();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertRedirect(RouteServiceProvider::HOME);

    $this->assertAuthenticatedAs($user);
});

it('users can not authenticate with invalid password', function () {
    $user = seedUser();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
