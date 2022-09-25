<?php

namespace App\Fields;

use Illuminate\Support\Arr;

class FormField
{
    protected ?string $label = '';
    protected ?string $help = '';
    protected string $component = 'InputField';
    protected string $type = 'text';
    protected bool $required = false;
    protected array $rules = ['nullable'];
    protected mixed $options = [];
    protected bool $disabled = false;
    protected ?string $key = null;
    protected mixed $value = null;

    public static function make(string $label = null): static
    {
        return (new static())
            ->label($label);
    }

    public function label(string $label = null): static
    {
        $this->label = $label;

        return $this;
    }

    public function help(string $help = null): static
    {
        $this->help = $help;

        return $this;
    }

    public function component(string $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function rules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function withOptions(array|callable $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function withValue(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function keyedBy(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function datePicker(): static
    {
        $this->component = 'DatePickerField';

        return $this;
    }

    public function checkbox(): static
    {
        $this->component = 'CheckboxField';

        return $this;
    }

    public function combobox(array $options = []): static
    {
        $this->component = 'ObjectComboboxField';

        return $this->withOptions($options);
    }

    public function select(array $options = []): static
    {
        $this->component = 'SelectField';

        return $this->withOptions($options);
    }

    public function toResource(): array
    {
        $attributes = $this->toArray(true);
        Arr::forget($attributes, 'rules');

        return $attributes;
    }

    public function toArray(bool $loadOptions = false): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'help' => $this->help,
            'component' => $this->component,
            'type' => $this->type,
            'required' => empty($this->rules)
                ? $this->required
                : in_array('required', $this->rules),
            'disabled' => $this->disabled,
            'rules' => $this->rules,
            'options' => $loadOptions && is_callable($this->options)
                ? call_user_func($this->options)
                : $this->options,
        ];
    }
}
