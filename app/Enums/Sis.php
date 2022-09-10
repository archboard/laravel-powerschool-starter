<?php

namespace App\Enums;

use App\Models\Tenant;
use App\SisProviders\PowerSchoolProvider;
use App\SisProviders\SisProvider;

enum Sis: string
{
    case PS = 'ps';
    case CLASS_LINK = 'class link';

    public function getProvider(Tenant $tenant): SisProvider
    {
        return match($this) {
            self::PS => new PowerSchoolProvider($tenant),
            self::CLASS_LINK => throw new \Exception('To be implemented'),
        };
    }

    public function getConfigFields(): array
    {
        return match($this) {
            self::PS => [
                'url' => [
                    'key' => 'url',
                    'label' => __('PowerSchool URL'),
                    'component' => 'AppInput',
                    'type' => 'url',
                    'required' => true,
                    'rules' => ['required', 'url'],
                ],
                'client_id' => [
                    'key' => 'client_id',
                    'label' => __('PowerSchool Client ID'),
                    'component' => 'AppInput',
                    'type' => 'text',
                    'required' => true,
                    'rules' => ['required', 'uuid'],
                ],
                'client_secret' => [
                    'key' => 'client_secret',
                    'label' => __('PowerSchool Client Secret'),
                    'component' => 'AppInput',
                    'type' => 'text',
                    'required' => true,
                    'rules' => ['required', 'uuid'],
                ],
            ],
            self::CLASS_LINK => throw new \Exception('To be implemented'),
        };
    }
}
