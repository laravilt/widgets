<?php

declare(strict_types=1);

namespace Laravilt\Widgets;

use Closure;

abstract class ChartWidget extends Widget
{
    protected array $data = [];

    protected array $options = [];

    protected ?string $chartType = null;

    protected ?int $height = null;

    /**
     * @param  array<string, mixed>  $data
     */
    public function data(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    protected function evaluateData(): array
    {
        $evaluatedData = [];

        foreach ($this->data as $key => $value) {
            $evaluatedData[$key] = $value instanceof Closure
                ? $value()
                : $value;
        }

        return $evaluatedData;
    }

    public function toInertiaProps(): array
    {
        return [
            'component' => 'ChartWidget',
            'heading' => $this->heading,
            'description' => $this->description,
            'chartType' => $this->chartType,
            'data' => $this->evaluateData(),
            'options' => $this->options,
            'height' => $this->height,
            'color' => $this->color,
            'polling' => [
                'enabled' => $this->pollingEnabled,
                'interval' => $this->pollingInterval,
            ],
        ];
    }
}
