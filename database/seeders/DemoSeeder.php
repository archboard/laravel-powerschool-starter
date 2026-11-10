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
 * Demo mode data, rebuilt on a schedule when app.demo is on: one tenant with two schools,
 * courses, sections, enrolled students, a district admin, and a guardian of three students.
 */
class DemoSeeder extends Seeder
{
    public const string ADMIN_EMAIL = 'admin@demo.test';

    public const string GUARDIAN_EMAIL = 'guardian@demo.test';

    /**
     * @var array<string, array{timezone: string, courses: list<string>, teachers: list<array{0: string, 1: string}>}>
     */
    protected const array SCHOOLS = [
        'Example High School' => [
            'timezone' => 'America/Chicago',
            'courses' => ['Algebra II', 'Geometry', 'Pre-Calculus', 'Biology', 'Chemistry', 'Physics', 'English 10', 'AP Literature', 'US History', 'World History', 'Spanish II', 'Art Studio'],
            'teachers' => [['Maria', 'Alvarez'], ['James', 'Okafor'], ['Linda', 'Park'], ['Robert', 'Nguyen'], ['Sarah', 'Thompson'], ['David', 'Cohen'], ['Emily', 'Brooks'], ['Michael', 'Rossi']],
        ],
        'Example Middle School' => [
            'timezone' => 'America/Chicago',
            'courses' => ['Math 6', 'Math 7', 'Pre-Algebra', 'Earth Science', 'Life Science', 'Physical Science', 'Language Arts 6', 'Language Arts 7', 'Social Studies', 'Band', 'Physical Education', 'Computer Science'],
            'teachers' => [['Karen', 'Liu'], ['Anthony', 'Baker'], ['Priya', 'Shah'], ['Thomas', 'Wright'], ['Grace', 'Kim'], ['Daniel', 'Murphy'], ['Olivia', 'Grant'], ['Samuel', 'Adeyemi']],
        ],
    ];

    public function run(): void
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Example School District',
            'domain' => Uri::of(config('app.url'))->host(),
            'allow_password_auth' => true,
            'allow_oidc_login' => false,
            // ponytail: placeholder credentials only satisfy Tenant::installed(); nothing syncs in demo mode
            'sis_config' => [
                'url' => 'https://powerschool.demo.test',
                'client_id' => 'demo',
                'client_secret' => 'demo',
            ],
        ]);
        $tenant->makeCurrent();

        BouncerFacade::allow(Role::DISTRICT_ADMIN->value)->everything();

        $schools = collect(self::SCHOOLS)
            ->map(fn (array $config, string $name) => $this->seedSchool($tenant, $name, $config))
            ->values();

        $admin = $this->makeUser($schools->first(), [
            'first_name' => 'Ada',
            'last_name' => 'Admin',
            'email' => self::ADMIN_EMAIL,
        ]);
        $admin->schools()->attach($schools->skip(1)->pluck('id'));
        $admin->assignRole(Role::DISTRICT_ADMIN);

        $guardian = $this->makeUser($schools->first(), [
            'first_name' => 'Gwen',
            'last_name' => 'Guardian',
            'email' => self::GUARDIAN_EMAIL,
            'user_type' => UserType::guardian,
        ]);
        $children = Student::query()->where('school_id', $schools[0]->id)->take(2)->get()
            ->merge(Student::query()->where('school_id', $schools[1]->id)->take(1)->get());
        $guardian->students()->attach($children, ['relationship' => 'Mother']);
    }

    /**
     * @param  array{timezone: string, courses: list<string>, teachers: list<array{0: string, 1: string}>}  $config
     */
    protected function seedSchool(Tenant $tenant, string $name, array $config): School
    {
        $school = School::factory()->for($tenant)->create([
            'name' => $name,
            'timezone' => $config['timezone'],
        ]);
        $scope = ['tenant_id' => $tenant->id, 'school_id' => $school->id];

        $teachers = collect($config['teachers'])->map(fn (array $teacher) => $this->makeUser($school, [
            'first_name' => $teacher[0],
            'last_name' => $teacher[1],
            'email' => strtolower("{$teacher[0]}.{$teacher[1]}@demo.test"),
        ]));

        $sections = collect($config['courses'])->flatMap(function (string $course, int $i) use ($scope, $teachers) {
            $course = Course::factory()->create([...$scope, 'name' => $course, 'course_number' => 100 + $i]);

            return collect(range(1, $i % 3 === 0 ? 2 : 1))->map(fn (int $number) => Section::factory()->for($course)->create([
                ...$scope,
                'section_number' => $number,
                'expression' => $number.'(A)',
                'user_id' => $teachers[($i + $number) % $teachers->count()]->id,
            ]));
        });

        Student::factory(50)->create($scope)
            ->each(fn (Student $student) => $student->sections()->attach($sections->random(rand(4, 6))->pluck('id')));

        return $school;
    }

    /** @param array<string, mixed> $attributes */
    protected function makeUser(School $school, array $attributes): User
    {
        return tap(User::factory()->create([
            'tenant_id' => $school->tenant_id,
            'school_id' => $school->id,
            'user_type' => UserType::staff,
            'timezone' => $school->timezone,
            ...$attributes,
        ]), fn (User $user) => $user->schools()->attach($school));
    }
}
