<?php

use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    logIn();
    setSchool();
});

it("can't be accessed without permission", function () {
    $this->get(route('settings.school.edit'))
        ->assertForbidden();
});

it('can be accessed with permission', function () {
    fullPermissions();

    $this->get(route('settings.school.edit'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('title')
            ->has('school')
        )
        ->assertOk();
});

it('can update school settings', function () {
    fullPermissions();
    $timezone = fake()->timezone();

    $this->put(route('settings.school.update'), ['timezone' => $timezone])
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('success');

    expect($this->school->refresh()->timezone)->toBe($timezone);
});

it('rejects an invalid timezone', function () {
    fullPermissions();

    $this->put(route('settings.school.update'), ['timezone' => 'Mars/Olympus'])
        ->assertSessionHasErrors('timezone');
});
