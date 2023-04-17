<?php

beforeEach(function () {
    logIn();
});

it("can't be accessed without permission", function () {
    $this->get(route('settings.tenant.edit'))
        ->assertForbidden();
});
