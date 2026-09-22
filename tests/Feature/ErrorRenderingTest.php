<?php

use Spatie\Multitenancy\Exceptions\NoCurrentTenant;

test('production error responses render the inertia error page', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->get('/this-route-does-not-exist')
        ->assertNotFound()
        ->assertInertia(fn ($page) => $page
            ->component('Error')
            ->where('status', 404)
        );
});

test('no current tenant exception renders as a 404', function () {
    Route::get('/__no-tenant-test', function () {
        throw new NoCurrentTenant;
    })->middleware('web');

    $this->get('/__no-tenant-test')->assertNotFound();
});
