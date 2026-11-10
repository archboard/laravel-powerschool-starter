<?php

use App\Enums\ActivityEvent;
use App\Models\Activity;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    logIn()->setSchool();
});

it('requires permission to view activity', function () {
    $this->get(route('activity.index'))->assertForbidden();
});

it('lists activity for the current school with filters', function () {
    activity()->performedOn($this->school)->event('updated')->log('updated');
    activity()->performedOn($this->tenant)->event('smtp_updated')->log('smtp_updated');
    Activity::create(['description' => 'other', 'event' => 'updated', 'school_id' => $this->school->id + 1]);
    $inSchool = Activity::where('school_id', $this->school->id)->count();

    fullPermissions();

    $this->get(route('activity.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('activity/Index')
            ->has('activities.data', $inSchool)
            ->has('events')
            ->has('subjectTypes'));

    $this->get(route('activity.index', ['event' => 'smtp_updated']))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('activities.data', 1)
            ->where('activities.data.0.causer', $this->user->name));

    $this->get(route('activity.index', ['from' => now()->addDay()->toDateString()]))
        ->assertInertia(fn (AssertableInertia $page) => $page->has('activities.data', 0));
});

it('stores english descriptions and translates them for display', function () {
    $this->user->update(['locale' => 'es']);
    ActivityEvent::smtp_updated->log($this->tenant);

    expect(Activity::latest('id')->first()->description)->toBe('SMTP settings updated');

    fullPermissions();

    $this->get(route('activity.index'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('activities.data.0.description', __('SMTP settings updated', locale: 'es')));
});
