<?php

namespace App\Forms;

use App\Enums\FieldType;
use App\Fields\FormField;
use App\Fields\FormFieldCollection;
use App\Forms\Traits\ValidatesTenantFields;
use App\Models\Tenant;

class SmtpForm extends BaseForm
{
    use ValidatesTenantFields;

    public function __construct(protected Tenant $tenant)
    {
    }

    public function title(): string
    {
        return __('SMTP Settings');
    }

    public function method(): string
    {
        return 'put';
    }

    public function endpoint(): string
    {
        return route('settings.tenant.smtp');
    }

    public function fields(): FormFieldCollection
    {
        $rules = $this->smtpRules();

        return FormFieldCollection::make([
                'host' => FormField::make(__('Host'))
                    ->rules($rules['host']),
                'port' => FormField::make(__('Port'))
                    ->component(FieldType::number)
                    ->rules($rules['port']),
                'username' => FormField::make(__('Username'))
                    ->rules($rules['username']),
                'password' => FormField::make(__('Password'))
                    ->component(FieldType::password)
                    ->rules($rules['password']),
                'from_name' => FormField::make(__('From name'))
                    ->rules($rules['from_name']),
                'from_address' => FormField::make(__('From address'))
                    ->component(FieldType::email)
                    ->rules($rules['from_address']),
                'encryption' => FormField::make(__('Encryption'))
                    ->rules($rules['encryption']),
            ])
            ->map(fn (FormField $field, string $key) => $field
                ->withValue($this->tenant->getInstallationFieldValue($key))
                ->keyedBy($key)
            );
    }
}
