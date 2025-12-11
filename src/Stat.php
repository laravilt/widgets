<?php

declare(strict_types=1);

namespace Laravilt\Widgets;

use Closure;

class Stat
{
    protected string $label;

    protected string|int|float|Closure $value;

    protected ?string $description = null;

    protected ?string $icon = null;

    protected ?string $color = null;

    protected ?string $chart = null;

    protected ?array $chartData = null;

    protected ?string $chartColor = null;

    protected ?string $url = null;

    protected bool $descriptionIcon = false;

    protected ?string $descriptionColor = null;

    public static function make(string $label, string|int|float|Closure $value): static
    {
        $stat = new static;
        $stat->label = $label;
        $stat->value = $value;

        return $stat;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function descriptionIcon(string $icon, ?string $color = null): static
    {
        $this->descriptionIcon = true;
        $this->icon = $icon;
        $this->descriptionColor = $color;

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

    public function chart( array $data, ?string $type="bar", ?string $color = null): static
    {
        $this->chart = $type;
        $this->chartData = $data;
        $this->chartColor = $color;

        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function toInertiaProps(): array
    {
        $value = $this->value instanceof Closure
            ? ($this->value)()
            : $this->value;

        return [
            'label' => $this->label,
            'value' => $value,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'chart' => $this->chart,
            'chartData' => $this->chartData,
            'chartColor' => $this->chartColor,
            'url' => $this->url,
            'descriptionIcon' => $this->descriptionIcon,
            'descriptionColor' => $this->descriptionColor,
        ];
    }
}
