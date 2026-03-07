<?php

use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->asCloud();
});

it('self hosted cant generate token', function () {
    $this->asSelfHosted();

    $this->assertTrue(
        DB::table('machine_api_tokens')->whereNotNull('api_token')->doesntExist()
    );

    $this->artisan('make:token')
        ->assertOk();

    $this->assertTrue(
        DB::table('machine_api_tokens')->whereNotNull('api_token')->doesntExist()
    );
});

it('can generate a new token', function () {
    $this->assertTrue(
        DB::table('machine_api_tokens')->whereNotNull('api_token')->doesntExist()
    );

    $this->artisan('make:token')
        ->assertOk();

    $this->assertTrue(
        DB::table('machine_api_tokens')->whereNotNull('api_token')->exists()
    );
});

it('can generate a new token over existing tokens', function () {
    DB::table('machine_api_tokens')->insert(['api_token' => 'existing']);

    $this->assertTrue(
        DB::table('machine_api_tokens')->whereNotNull('api_token')->exists()
    );

    $this->artisan('make:token')
        ->assertOk();

    $this->assertEquals(1, DB::table('machine_api_tokens')->whereNotNull('api_token')->count());
    $this->assertTrue(
        DB::table('machine_api_tokens')->where('api_token', 'existing')->doesntExist()
    );
});
