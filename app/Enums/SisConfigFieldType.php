<?php

namespace App\Enums;

enum SisConfigFieldType: string
{
    case TEXT = 'text';
    case URL = 'url';
    case CHECKBOX = 'checkbox';

    /**
     * The Vue field component that renders this type.
     */
    public function component(): string
    {
        return match ($this) {
            self::TEXT, self::URL => 'InputField',
            self::CHECKBOX => 'CheckboxField',
        };
    }

    /**
     * A human-readable label describing what this field type represents.
     */
    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Text',
            self::URL => 'URL',
            self::CHECKBOX => 'Checkbox',
        };
    }
}
