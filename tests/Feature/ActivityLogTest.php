<?php

use App\Models\Activity;

beforeEach(function () {
    logIn()->setSchool();
});

it('logs school settings changes', function () {
    $this->school->update(['timezone' => 'America/Denver']);

    $activity = Activity::where('subject_type', $this->school->getMorphClass())->latest('id')->first();
    expect($activity->attribute_changes['attributes']['timezone'])->toBe('America/Denver')
        ->and($activity->school_id)->toBe($this->school->id);
});

it('logs tenant changes without secrets', function () {
    $this->tenant->update(['name' => 'Renamed', 'smtp_config' => collect(['password' => 'hunter2'])]);

    $activity = Activity::where('subject_type', $this->tenant->getMorphClass())->latest('id')->first();
    expect($activity->attribute_changes['attributes'])->toHaveKey('name')
        ->and(json_encode($activity->attribute_changes))->not->toContain('hunter2');
});

it('scopes activity to the current tenant', function () {
    Activity::withoutGlobalScopes()->create(['description' => 'elsewhere', 'tenant_id' => $this->tenant->id + 1]);

    expect(Activity::where('description', 'elsewhere')->exists())->toBeFalse()
        ->and(Activity::withoutGlobalScopes()->where('description', 'elsewhere')->exists())->toBeTrue();
});
