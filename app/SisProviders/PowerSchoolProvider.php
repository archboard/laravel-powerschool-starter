<?php

namespace App\SisProviders;

use App\Exceptions\LicenseException;
use App\Models\School;
use App\Models\Section;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use GrantHolle\PowerSchool\Api\RequestBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PowerSchoolProvider implements SisProvider
{
    protected RequestBuilder $builder;

    public function __construct(protected Tenant $tenant)
    {
        if (Arr::has($this->tenant->sis_config, ['url', 'client_id', 'client_secret'])) {
            $this->builder = new RequestBuilder(
                Arr::get($tenant->sis_config, 'url'),
                Arr::get($tenant->sis_config, 'client_id'),
                Arr::get($tenant->sis_config, 'client_secret')
            );
        }
    }

    public function getAllSchools(): Collection
    {
        $response = $this->builder
            ->to('/ws/v1/district/school')
            ->get();

        // Only get the schools that exist already
        if (config('app.cloud')) {
            $schools = $this->tenant->schools()->pluck('sis_id');

            return $response->collect()
                ->filter(fn (array $school) => $schools->contains($school['id']));
        }

        return $response->collect();
    }

    public function syncSchools(): Collection
    {
        return $this->getAllSchools()
            ->map(function ($school) {
                return $this->tenant
                    ->schools()
                    ->updateOrCreate(
                        ['sis_id' => $school['id']],
                        [
                            'name' => $school['name'],
                            'school_number' => $school['school_number'],
                            'low_grade' => $school['low_grade'],
                            'high_grade' => $school['high_grade'],
                        ]
                    );
            });
    }

    public function getSchool($sisId): array
    {
        $results = $this->builder
            ->to("/ws/v1/school/{$sisId}")
            ->get();

        if (
            config('app.cloud') &&
            !$this->tenant->schools()->where('sis_id', $sisId)->exists()
        ) {
            throw new LicenseException("Your license does not support this school. Please update your license and try again.");
        }

        return $results->toArray();
    }

    public function syncSchool($sisId): School
    {
        $sisSchool = $this->getSchool($sisId);

        /** @var School $school */
        $school = $this->tenant
            ->schools()
            ->updateOrCreate(
                ['sis_id' => $sisId],
                [
                    'name' => $sisSchool['name'],
                    'school_number' => $sisSchool['school_number'],
                    'low_grade' => $sisSchool['low_grade'],
                    'high_grade' => $sisSchool['high_grade'],
                ]
            );

        return $school;
    }

    public function syncSchoolStaff(School|int $sisId): static
    {
        $school = $this->tenant->getSchoolFromSisId($sisId);
        $builder = $this->builder
            ->method('get')
            ->to("/ws/v1/school/{$school->sis_id}/staff")
            ->expansions('emails');

        while ($results = $builder->paginate()) {
            $existingUsers = $this->tenant
                ->users()
                ->whereIn('sis_id', collect($results)->pluck('users_dcid'))
                ->with('schools')
                ->get()
                ->keyBy('sis_id');

            $entries = $results->collect()
                ->reduce(function (array $entries, array $user) use ($school, $existingUsers) {
                    $email = strtolower(Arr::get($user, 'emails.work_email', ''));

                    if (!$email) {
                        return $entries;
                    }

                    /** @var User $existingUser */
                    if ($existingUser = $existingUsers->get($user['users_dcid'])) {
                        $existingUser->update([
                            'email' => $email,
                            'first_name' => optional($user['name'])->first_name,
                            'last_name' => optional($user['name'])->last_name,
                        ]);
                        /** @var School $existingSchool */
                        $existingSchool = $existingUser->schools->firstWhere('id', $school->id);

                        // If the school record exists already, update the staff id just in case
                        if ($existingSchool && $existingSchool->pivot->staff_id !== $user['id']) {
                            $existingUser->schools()
                                ->updateExistingPivot($school->id, ['staff_id' => $user['id']]);
                        }
                        // If there isn't a school relationship, add it here
                        elseif (!$existingSchool) {
                            $entries[] = [
                                'school_id' => $school->id,
                                'user_id' => $existingUser->id,
                                'staff_id' => $user['id'],
                            ];
                        }

                        return $entries;
                    }

                    /** @var User $newUser */
                    $newUser = $this->tenant
                        ->users()
                        ->create([
                            'sis_id' => $user['users_dcid'],
                            'email' => $email,
                            'first_name' => Arr::get($user, 'name.first_name'),
                            'last_name' => Arr::get($user, 'name.last_name'),
                            'school_id' => $school->id,
                        ]);

                    $entries[] = [
                        'school_id' => $school->id,
                        'user_id' => $newUser->id,
                        'staff_id' => $user['id'],
                    ];

                    return $entries;
                },
                []
            );

            if (!empty($entries)) {
                DB::table('school_user')->insert($entries);
            }
        }

        return $this;
    }

    public function syncSchoolStudents($sisId): static
    {
        $school = $this->tenant->getSchoolFromSisId($sisId);
        $builder = $this->builder
            ->method('get')
            ->to("/ws/v1/school/{$school->sis_id}/student")
            ->q('school_enrollment.enroll_status==A')
            ->expansions('contact_info');

        while ($results = $builder->paginate()) {
            $now = now()->format('Y-m-d H:i:s');

            // Get courses that exist already
            $existingStudents = $school->students()
                ->whereIn('sis_id', collect($results)->pluck('id'))
                ->get()
                ->keyBy('sis_id');

            $entries = $results->collect()
                ->reduce(function (array $entries, array $student) use ($school, $now, $existingStudents) {
                    $email = strtolower(Arr::get($student, 'contact_info.email', ''));

                    // If it exists, then update
                    if ($existingStudent = $existingStudents->get($student['id'])) {
                        $existingStudent->update([
                            'student_number' => $student['local_id'],
                            'first_name' => Arr::get($student, 'name.first_name'),
                            'last_name' => Arr::get($student, 'name.last_name'),
                            'email' => $email ?: null,
                        ]);

                        return $entries;
                    }

                    // It's a new course
                    $entries[] = [
                        'tenant_id' => $this->tenant->id,
                        'school_id' => $school->id,
                        'sis_id' => $student['id'],
                        'student_number' => $student['local_id'],
                        'first_name' => Arr::get($student, 'name.first_name'),
                        'last_name' => Arr::get($student, 'name.last_name'),
                        'email' => $email ?: null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    return $entries;
                }, []);

            if (!empty($entries)) {
                DB::table('students')->insert($entries);
            }
        }

        return $this;
    }

    public function syncSchoolCourses($sisId): static
    {
        $school = $this->tenant->getSchoolFromSisId($sisId);
        $builder = $this->builder
            ->method('get')
            ->to("/ws/v1/school/{$school->sis_id}/course");

        while ($results = $builder->paginate()) {
            $now = now()->format('Y-m-d H:i:s');

            // Get courses that exist already
            $existingCourses = $school->courses()
                ->whereIn('sis_id', collect($results)->pluck('id'))
                ->get()
                ->keyBy('sis_id');

            $entries =$results->collect()
                ->reduce(function (array $entries, array $course) use ($school, $now, $existingCourses) {
                    // If it exists, then update
                    if ($existingCourse = $existingCourses->get($course['id'])) {
                        $existingCourse->update([
                            'name' => $course['course_name'],
                            'course_number' => $course['course_number'],
                        ]);

                        return $entries;
                    }

                    // It's a new course
                    $entries[] = [
                        'tenant_id' => $this->tenant->id,
                        'school_id' => $school->id,
                        'name' => $course['course_name'],
                        'course_number' => $course['course_number'],
                        'sis_id' => $course['id'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    return $entries;
                }, []);

            if (!empty($entries)) {
                DB::table('courses')->insert($entries);
            }
        }

        return $this;
    }

    public function syncSchoolSections($sisId): static
    {
        $school = $this->tenant->getSchoolFromSisId($sisId);
        $builder = $this->builder
            ->method('get')
            ->to("/ws/v1/school/{$school->sis_id}/section");
        $activeSections = [];
        $newEntries = [];

        while ($results = $builder->paginate()) {
            $now = now()->format('Y-m-d H:i:s');

            // Get courses that exist already
            $courses = $school->courses()
                ->whereIn('sis_id', collect($results)->pluck('course_id'))
                ->get()
                ->keyBy('sis_id');
            $existingSections = $school->sections()
                ->whereIn('sis_id', collect($results)->pluck('id'))
                ->get()
                ->keyBy('sis_id');
            $staff = $school->users()
                ->wherePivotIn('staff_id', collect($results)->pluck('staff_id'))
                ->get()
                ->keyBy('pivot.staff_id');

            $entries = $results->collect()
                ->reduce(function (array $entries, array $section) use (&$activeSections, $school, $staff, $courses, $now, $existingSections) {
                    $course = $courses->get($section['course_id']);
                    $teacher = $staff->get($section['staff_id']);

                    // If the course or staff doesn't exist, don't do anything
                    if (!$course || !$teacher) {
                        return $entries;
                    }

                    // If the section exists, then update
                    if ($existingSection = $existingSections->get($section['id'])) {
                        $activeSections[] = $existingSection->id;
                        $existingSection->update([
                            'section_number' => optional($section)->section_number,
                            'expression' => optional($section)->expression,
                            'external_expression' => optional($section)->external_expression,
                        ]);

                        return $entries;
                    }

                    // It's a new section
                    $entries[] = [
                        'tenant_id' => $this->tenant->id,
                        'school_id' => $school->id,
                        'course_id' => $course->id,
                        'user_id' => $teacher->id,
                        'sis_id' => $section['id'],
                        'section_number' => optional($section)->section_number,
                        'expression' => optional($section)->expression,
                        'external_expression' => optional($section)->external_expression,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // At this point they are a teacher
                    $teacher->assign(User::TEACHER);

                    return $entries;
                }, []);

            $newEntries = array_merge($newEntries, $entries);
        }

        // Delete the existing sections that didn't update
        // which means the other existing sections aren't active
        $school->sections()
            ->whereNotIn('id', $activeSections)
            ->delete();

        if (!empty($newEntries)) {
            DB::table('sections')->insert($newEntries);
        }

        return $this;
    }

    /**
     * This is HIGHLY inefficient...
     * TODO: Improve performance
     */
    public function syncSchoolStudentEnrollment($sisId): static
    {
        $school = $this->tenant->getSchoolFromSisId($sisId);
        $students = $this->tenant->students()
            ->pluck('id', 'sis_id');

        $school->sections
            ->each(function (Section $section) use ($students) {
                $results = $this->builder
                    ->extensions('s_cc_x,s_cc_edfi_x')
                    ->to("/ws/v1/section/{$section->sis_id}/section_enrollment")
                    ->get();

                $enrollments = Arr::isAssoc($results->toArray())
                    ? collect([$results->toArray()])
                    : $results->collect();

                // Get the sis id's of the students who haven't dropped
                $studentEnrollment = $enrollments
                    ->filter(fn (array $enrollment) => !$enrollment['dropped'] &&
                        $students->has($enrollment['student_id']))
                    ->map(fn (array $enrollment) => $students->get($enrollment['student_id']));

                $section->students()->sync($studentEnrollment);
            });

        return $this;
    }

    /**
     * Syncs everything for a school:
     * staff, students, courses, sections, and enrollment
     *
     * @param int|string $sisId
     */
    public function fullSchoolSync($sisId): void
    {
        $this->syncSchool($sisId);
        $this->syncSchoolStaff($sisId);
        $this->syncSchoolStudents($sisId);
        $this->syncSchoolCourses($sisId);
        $this->syncSchoolSections($sisId);
        $this->syncSchoolStudentEnrollment($sisId);
    }

    public function getBuilder(): RequestBuilder
    {
        return $this->builder;
    }

    public function syncStudent($sisId): Student
    {
        $results = $this->builder
            ->to("/ws/v1/student/{$sisId}")
            ->q('school_enrollment.enroll_status==A')
            ->expansions('contact_info')
            ->get();

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->tenant
            ->students()
            ->updateOrCreate(
                ['sis_id' => $sisId],
                [
                    'student_number' => $results['local_id'],
                    'first_name' => Arr::get($results, 'name.first_name'),
                    'last_name' => Arr::get($results, 'name.last_name'),
                    'email' => strtolower(Arr::get($results, 'contact_info.email')) ?: null,
                ],
            );
    }

    public function syncUser($sisId): User
    {
        throw new \Exception('Not implement.');
    }

    public function syncTeacher($sisId): User
    {
        throw new \Exception('Not implement.');
    }

    public function configured(): bool
    {
        return Arr::get($this->tenant->sis_config, 'url') &&
            Arr::get($this->tenant->sis_config, 'client_id') &&
            Arr::get($this->tenant->sis_config, 'client_secret');
    }
}
