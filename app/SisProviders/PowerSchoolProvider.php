<?php

namespace App\SisProviders;

use App\Models\School;
use App\Models\Section;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use GrantHolle\PowerSchool\Api\RequestBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PowerSchoolProvider implements SisProvider
{
    protected Tenant $tenant;
    protected RequestBuilder $builder;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->builder = new RequestBuilder(
            $tenant->ps_url,
            $tenant->ps_client_id,
            $tenant->ps_secret
        );
    }

    public function getAllSchools(): array
    {
        $response = $this->builder
            ->to('/ws/v1/district/school')
            ->get();

        // Only get the schools that exist already
        if (config('app.cloud')) {
            $schools = $this->tenant->schools->pluck('sis_id');
            return array_filter($response->schools->school, function ($school) use ($schools) {
                return $schools->contains($school->id);
            });
        }

        return $response->schools->school;
    }

    public function syncSchools(): Collection
    {
        return collect($this->getAllSchools())
            ->map(function ($school) {
                return $this->tenant
                    ->schools()
                    ->updateOrCreate(
                        ['sis_id' => $school->id],
                        [
                            'name' => $school->name,
                            'school_number' => $school->school_number,
                            'low_grade' => $school->low_grade,
                            'high_grade' => $school->high_grade,
                        ]
                    );
            });
    }

    public function getSchool($sisId)
    {
        $results = $this->builder
            ->to("/ws/v1/school/{$sisId}")
            ->get();

        if (
            config('app.cloud') &&
            !$this->tenant->schools()->where('sis_id', $sisId)->exists()
        ) {
            throw new \Exception("Your license does not support this school. Please update your license and try again.");
        }

        return $results->school;
    }

    public function syncSchool($sisId): School
    {
        $sisSchool = $this->getSchool($sisId);

        /** @var School $school */
        $school = $this->tenant
            ->schools()
            ->updateOrCreate(
                ['sis_id' => $sisSchool->id],
                [
                    'name' => $sisSchool->name,
                    'school_number' => $sisSchool->school_number,
                    'low_grade' => $sisSchool->low_grade,
                    'high_grade' => $sisSchool->high_grade,
                ]
            );

        return $school;
    }

    public function syncSchoolStaff($sisId)
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

            $entries = array_reduce(
                $results,
                function ($entries, $user) use ($school, $existingUsers) {
                    $email = strtolower(optional($user->emails)->work_email);

                    if (!$email) {
                        return $entries;
                    }

                    /** @var User $existingUser */
                    if ($existingUser = $existingUsers->get($user->users_dcid)) {
                        $existingUser->update([
                            'email' => $email,
                            'first_name' => optional($user->name)->first_name,
                            'last_name' => optional($user->name)->last_name,
                        ]);
                        /** @var School $existingSchool */
                        $existingSchool = $existingUser->schools->firstWhere('id', $school->id);

                        // If the school record exists already, update the staff id just in case
                        if ($existingSchool && $existingSchool->pivot->staff_id !== $user->id) {
                            $existingUser->schools()
                                ->updateExistingPivot($school->id, ['staff_id' => $user->id]);
                        }
                        // If there isn't a school relationship, add it here
                        elseif (!$existingSchool) {
                            $entries[] = [
                                'school_id' => $school->id,
                                'user_id' => $existingUser->id,
                                'staff_id' => $user->id,
                            ];
                        }

                        return $entries;
                    }

                    /** @var User $newUser */
                    $newUser = $this->tenant
                        ->users()
                        ->create([
                            'sis_id' => $user->users_dcid,
                            'email' => $email,
                            'first_name' => optional($user->name)->first_name,
                            'last_name' => optional($user->name)->last_name,
                            'school_id' => $school->id,
                        ]);

                    $entries[] = [
                        'school_id' => $school->id,
                        'user_id' => $newUser->id,
                        'staff_id' => $user->id,
                    ];

                    return $entries;
                }
            );

            if (!empty($entries)) {
                DB::table('school_user')->insert($entries);
            }
        }
    }

    public function syncSchoolStudents($sisId)
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

            $entries = array_reduce(
                $results,
                function ($entries, $student) use ($school, $now, $existingStudents) {
                    $email = optional($student->contact_info)->email;

                    // If it exists, then update
                    if ($existingStudent = $existingStudents->get($student->id)) {
                        $existingStudent->update([
                            'student_number' => $student->local_id,
                            'first_name' => optional($student->name)->first_name,
                            'last_name' => optional($student->name)->last_name,
                            'email' => $email ? strtolower($email) : null,
                        ]);

                        return $entries;
                    }

                    // It's a new course
                    $entries[] = [
                        'tenant_id' => $this->tenant->id,
                        'school_id' => $school->id,
                        'sis_id' => $student->id,
                        'student_number' => $student->local_id,
                        'first_name' => optional($student->name)->first_name,
                        'last_name' => optional($student->name)->last_name,
                        'email' => $email ? strtolower($email) : null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    return $entries;
                }, []
            );

            if (!empty($entries)) {
                DB::table('students')->insert($entries);
            }
        }
    }

    public function syncSchoolCourses($sisId)
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

            $entries = array_reduce(
                $results,
                function ($entries, $course) use ($school, $now, $existingCourses) {
                    // If it exists, then update
                    if ($existingCourse = $existingCourses->get($course->id)) {
                        $existingCourse->update([
                            'name' => $course->course_name,
                            'course_number' => $course->course_number,
                        ]);

                        return $entries;
                    }

                    // It's a new course
                    $entries[] = [
                        'tenant_id' => $this->tenant->id,
                        'school_id' => $school->id,
                        'name' => $course->course_name,
                        'course_number' => $course->course_number,
                        'sis_id' => $course->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    return $entries;
                }, []
            );

            if (!empty($entries)) {
                DB::table('courses')->insert($entries);
            }
        }
    }

    public function syncSchoolSections($sisId)
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

            $entries = array_reduce(
                $results,
                function ($entries, $section) use (&$activeSections, $school, $staff, $courses, $now, $existingSections) {
                    $course = $courses->get($section->course_id);
                    $teacher = $staff->get($section->staff_id);

                    // If the course or staff doesn't exists, don't do anything
                    if (!$course || !$teacher) {
                        return $entries;
                    }

                    // If the section exists, then update
                    if ($existingSection = $existingSections->get($section->id)) {
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
                        'sis_id' => $section->id,
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
    }

    public function syncSchoolStudentEnrollment($sisId)
    {
        $school = $this->tenant->getSchoolFromSisId($sisId);

        $school->sections
            ->each(function (Section $section) {
                $results = $this->builder
                    ->extensions('s_cc_x,s_cc_edfi_x')
                    ->to("/ws/v1/section/{$section->sis_id}/section_enrollment")
                    ->get();

                $enrollments = $results->section_enrollments->section_enrollment ?? [];

                if (!is_array($enrollments)) {
                    $enrollments = [$enrollments];
                }

                // Get the sis id's of the students who haven't dropped
                $studentSisIds = collect($enrollments)
                    ->each(function ($enrollment) use ($enrollments) {
                        if (!is_object($enrollment)) {
                            dd($enrollments);
                        }
                    })
                    ->filter(fn ($enrollment) => !$enrollment->dropped)
                    ->pluck('student_id');

                $students = Student::whereIn('sis_id', $studentSisIds)
                    ->pluck('id');

                $section->students()->sync($students);
            });
    }

    /**
     * Syncs everything for a school:
     * staff, students, courses, sections, and enrollment
     *
     * @param int|string $sisId
     */
    public function fullSchoolSync($sisId)
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
}
