<?php

use App\Enums\SisConfigFieldType;

it('has correct backing values', function () {
    expect(SisConfigFieldType::TEXT->value)->toBe('text')
        ->and(SisConfigFieldType::URL->value)->toBe('url')
        ->and(SisConfigFieldType::CHECKBOX->value)->toBe('checkbox');
});

it('maps to the correct vue component', function (SisConfigFieldType $type, string $component) {
    expect($type->component())->toBe($component);
})->with([
    [SisConfigFieldType::TEXT, 'InputField'],
    [SisConfigFieldType::URL, 'InputField'],
    [SisConfigFieldType::CHECKBOX, 'CheckboxField'],
]);

it('has a human-readable label', function (SisConfigFieldType $type, string $label) {
    expect($type->label())->toBe($label);
})->with([
    [SisConfigFieldType::TEXT, 'Text'],
    [SisConfigFieldType::URL, 'URL'],
    [SisConfigFieldType::CHECKBOX, 'Checkbox'],
]);
