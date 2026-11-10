<?php

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

use App\Enums\Permission;
use App\Enums\UserType;
use App\Models\Course;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Browser');
uses(RefreshDatabase::class)->in('Feature', 'Browser');

// ponytail: a running `npm run dev` writes public/hot, which the headless browser cannot reach; always serve built assets.
uses()->beforeEach(fn () => Vite::useHotFile(storage_path('framework/testing/no-hot')))->in('Browser');

pest()->browser()
    ->inChrome()
    ->withHost('localhost')
    // ponytail: CI runners need longer than the 5s default to load Vite chunks
    ->timeout(15000);

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

function seedSection(?User $user = null): Section
{
    $user ??= test()->user;
    $course = Course::factory()->create();
    $section = Section::factory()
        ->for($course)
        ->create(['user_id' => $user->id]);
    $students = Student::factory(3)->create();
    $section->students()->attach($students);

    return $section;
}

function seedGuardian(Student ...$students): User
{
    $guardian = seedUser(['user_type' => UserType::guardian]);
    $guardian->students()->attach($students);

    return $guardian;
}
