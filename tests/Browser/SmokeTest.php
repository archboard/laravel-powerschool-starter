<?php

beforeEach(function () {
    $this->tenant->update(['allow_password_auth' => true]);
    setSchool();
    $this->withoutExceptionHandling();
});

it('loads guest pages without smoke', function () {
    visit(['/login', '/forgot-password'])->assertNoSmoke();
});

it('loads authenticated pages without smoke', function () {
    logIn();
    fullPermissions();

    visit([
        '/',
        '/activity',
        '/courses',
        '/sections',
        '/select-school',
        '/settings/personal/edit',
        '/settings/school/edit',
        '/settings/tenant/edit',
        '/students',
        '/users',
    ])->assertNoSmoke();
});

it('loads record pages without smoke', function () {
    logIn();
    fullPermissions();
    $section = seedSection();
    $student = $section->students->first();
    $teacher = $section->teacher;

    visit([
        "/courses/{$section->course_id}",
        "/sections/{$section->id}",
        "/students/{$student->id}",
        "/users/{$teacher->id}",
        "/users/{$teacher->id}/permissions",
    ])->assertNoSmoke();
});
