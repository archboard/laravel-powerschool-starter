<?php

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)->in('Feature');
uses(RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Browser Tests
|--------------------------------------------------------------------------
|
| Browser tests use Playwright via pest-plugin-browser to test the live
| Herd-served application at APP_URL. They run against the real app and
| do not use RefreshDatabase — they are intentionally kept to public pages
| that do not require database setup.
|
*/
pest()->extend(TestCase::class)->group('browser')->in('Browser');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function logIn(array $attributes = [])
{
    return test()->logIn(attributes: $attributes);
}

function fullPermissions()
{
    return test()->fullPermission();
}

function seedUser(array $attributes = []): User
{
    return test()->seedUser($attributes);
}

function givePermission(Permission $permission)
{
    return test()->givePermission($permission);
}

function setSchool()
{
    return test()->setSchool();
}

function asCloud()
{
    return test()->asCloud();
}

function asSelfHosted()
{
    return test()->asSelfHosted();
}

function tapUser(callable $callback)
{
    return test()->tapUser($callback);
}
