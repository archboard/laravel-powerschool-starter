<?php

namespace App\Navigation;

class NavigationItem
{
    public string $component = 'InertiaLink';

    public string $method = 'get';

    public string $target = '_self';

    public string $label = '';

    public string $endpoint = '';

    public string $as = 'a';

    public string $icon = '';

    public bool $current = false;

    public static function make(): self
    {
        return new self;
    }

    public function useComponent(string $component): self
    {
        $this->component = $component;

        return $this;
    }

    public function inNewTab(): self
    {
        $this->target = '_blank';

        return $this;
    }

    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function labeled(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function to(string $url): self
    {
        $this->endpoint = $url;

        return $this;
    }

    public function asButton(): self
    {
        $this->as = 'button';

        return $this;
    }

    public function withIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function isCurrent(bool $current = true): self
    {
        $this->current = $current;

        return $this;
    }

    /**
     * @return array{url: string, label: string, method: string, target: string, as: string, component: string, icon: string, current: bool}
     */
    public function toArray(): array
    {
        return [
            'url' => $this->endpoint,
            'label' => $this->label,
            'method' => $this->method,
            'target' => $this->target,
            'as' => $this->as,
            'component' => $this->component,
            'icon' => $this->icon,
            'current' => $this->current,
        ];
    }
}
