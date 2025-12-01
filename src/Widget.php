<?php

declare(strict_types=1);

namespace Laravilt\Widgets;

use Laravilt\Support\Contracts\InertiaSerializable;

abstract class Widget implements InertiaSerializable
{
    protected ?string $heading = null;

    protected ?string $description = null;

    protected ?string $icon = null;

    protected ?string $color = null;

    protected bool $pollingEnabled = false;

    protected ?int $pollingInterval = null;

    protected ?string $extraAttributes = null;

    public static function make(): static
    {
        return new static;
    }

    public function heading(string $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function polling(?int $interval = 10): static
    {
        $this->pollingEnabled = true;
        $this->pollingInterval = $interval;

        return $this;
    }

    public function extraAttributes(string $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    abstract public function toInertiaProps(): array;
}
