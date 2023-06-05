<?php

beforeEach(function () {
    logIn();
});

it("can't be accessed without permission", function () {
    $this->get(route('settings.tenant.edit'))
        ->assertForbidden();
});

it("can update smtp settings", function () {
    fullPermissions();

    $data = [
        'host' => fake()->domainName(),
        'port' => fake()->randomElement([587, 465, 25, 2525]),
        'username' => fake()->email(),
        'password' => fake()->word(),
        'from_name' => fake()->name(),
        'from_address' => fake()->email(),
        'encryption' => fake()->randomElement(['tls', 'ssl']),
    ];

    $this->put(route('settings.tenant.smtp'), $data)
        ->assertSessionHas('success')
        ->assertRedirect(route('settings.tenant.edit'));

    $this->tenant->refresh();
    $this->assertEquals($data, $this->tenant->smtp_config->toArray());
});
