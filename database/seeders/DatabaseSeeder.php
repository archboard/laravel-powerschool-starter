<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\UserType;
use App\Models\Course;
use App\Models\School;
use App\Models\Section;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Uri;
use Silber\Bouncer\BouncerFacade;

/**
 * Local QA data: one tenant on APP_URL with an admin, a teacher with a section of three
 * students, and a guardian of the first student.
 * All users share the password "password".
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::factory()->create(['domain' => Uri::of(config('app.url'))->host()]);
        $tenant->makeCurrent();

        $school = School::factory()->for($tenant)->create([
            'name' => 'Example High School',
            'timezone' => config('app.timezone'),
        ]);
        $scope = ['tenant_id' => $tenant->id, 'school_id' => $school->id];

        $makeUser = fn (array $attributes) => tap(User::factory()->create([
            ...$scope,
            'user_type' => UserType::staff,
            ...$attributes,
        ]), fn (User $user) => $user->schools()->attach($school));

        BouncerFacade::allow(Role::DISTRICT_ADMIN->value)->everything();
        $makeUser(['first_name' => 'Ada', 'last_name' => 'Admin', 'email' => 'admin@example.com'])
            ->assignRole(Role::DISTRICT_ADMIN);

        $teacher = $makeUser(['first_name' => 'Tom', 'last_name' => 'Teacher', 'email' => 'teacher@example.com']);
        $section = Section::factory()
            ->for(Course::factory()->create([...$scope, 'name' => 'Algebra I']))
            ->create([...$scope, 'user_id' => $teacher->id]);
        $students = Student::factory(3)->create($scope);
        $section->students()->attach($students);

        $makeUser(['first_name' => 'Gwen', 'last_name' => 'Guardian', 'email' => 'guardian@example.com', 'user_type' => UserType::guardian])
            ->students()->attach($students->first(), ['relationship' => 'Mother']);

    }
}
