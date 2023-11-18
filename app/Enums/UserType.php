<?php

namespace App\Enums;

use App\Enums\Traits\HasOptions;

enum UserType: string
{
    use HasOptions;

    case staff = 'staff';
    case guardian = 'guardian';
    case student = 'student';

    public function label(): string
    {
        return match($this) {
            self::staff => __('Staff'),
            self::guardian => __('Contact'),
            self::student => __('Student'),
        };
    }
}
