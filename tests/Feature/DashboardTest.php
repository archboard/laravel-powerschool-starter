<?php

use Inertia\Testing\AssertableInertia;

it('shows the dashboard to a signed-in user with a school', function () {
    logIn()->setSchool();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Dashboard'));
});

it('sends guests to the login page', function () {
    $this->get(route('home'))->assertRedirect(route('login'));
});
