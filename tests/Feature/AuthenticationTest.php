<?php

use App\Enums\UserType;
use App\Providers\RouteServiceProvider;
use Inertia\Testing\AssertableInertia;

it('tenant without passwords cant login', function () {
    $user = seedUser(['user_type' => UserType::staff]);
    $this->tenant->update(['allow_password_auth' => false]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'user_type' => UserType::staff->value,
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
            ->where('userTypes.1', ['label' => 'Contact/Guardian', 'value' => 'guardian'])
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
    $user = seedUser(['user_type' => UserType::staff]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'user_type' => UserType::staff->value,
    ])
        ->assertRedirect(RouteServiceProvider::HOME);

    $this->assertAuthenticatedAs($user);
});

it('users can not authenticate with invalid password', function () {
    $user = seedUser(['user_type' => UserType::staff]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
        'user_type' => UserType::staff->value,
    ]);

    $this->assertGuest();
});

it('logs in as the selected user type when users share an email', function (UserType $userType) {
    $staff = seedUser(['user_type' => UserType::staff, 'email' => 'shared@example.com', 'password' => bcrypt('staff-password')]);
    $guardian = seedUser(['user_type' => UserType::guardian, 'email' => 'shared@example.com', 'password' => bcrypt('guardian-password')]);
    $user = $userType === UserType::staff ? $staff : $guardian;

    $this->post('/login', [
        'email' => 'Shared@example.com',
        'password' => "{$userType->value}-password",
        'user_type' => $userType->value,
    ])
        ->assertRedirect(RouteServiceProvider::HOME);

    $this->assertAuthenticatedAs($user);
})->with([
    'staff' => UserType::staff,
    'guardian' => UserType::guardian,
]);

it('users can not authenticate as a different user type', function () {
    $user = seedUser(['user_type' => UserType::staff]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'user_type' => UserType::guardian->value,
    ])
        ->assertSessionHasErrors(['email' => __('auth.failed')]);

    $this->assertGuest();
});

it('requires a valid user type to log in', function (?string $userType, string $message) {
    $user = seedUser(['user_type' => UserType::staff]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'user_type' => $userType,
    ])
        ->assertSessionHasErrors(['user_type' => $message]);

    $this->assertGuest();
})->with([
    'missing' => [null, 'This field is required.'],
    'unknown' => ['admin', 'The selected user type is invalid.'],
]);
