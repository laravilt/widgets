<?php

declare(strict_types=1);

namespace Laravilt\Widgets;

class LineChartWidget extends ChartWidget
{
    protected ?string $chartType = 'line';

    /**
     * @param  array<int, string|int|float>  $data
     */
    public static function make(array $labels = [], array $datasets = []): static
    {
        $widget = new static;

        if (! empty($labels) && ! empty($datasets)) {
            $widget->data([
                'labels' => $labels,
                'datasets' => $datasets,
            ]);
        }

        return $widget;
    }

    public function curved(bool $condition = true): static
    {
        $this->options['tension'] = $condition ? 0.4 : 0;

        return $this;
    }

    public function fill(bool $condition = true): static
    {
        $this->options['fill'] = $condition;

        return $this;
    }

    public function showPoints(bool $condition = true): static
    {
        $this->options['showPoints'] = $condition;

        return $this;
    }

    public function showGrid(bool $condition = true): static
    {
        $this->options['showGrid'] = $condition;

        return $this;
    }
}
