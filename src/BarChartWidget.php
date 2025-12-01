<?php

declare(strict_types=1);

namespace Laravilt\Widgets;

class BarChartWidget extends ChartWidget
{
    protected ?string $chartType = 'bar';

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

    public function horizontal(bool $condition = true): static
    {
        $this->options['horizontal'] = $condition;

        return $this;
    }

    public function stacked(bool $condition = true): static
    {
        $this->options['stacked'] = $condition;

        return $this;
    }

    public function showGrid(bool $condition = true): static
    {
        $this->options['showGrid'] = $condition;

        return $this;
    }

    public function barThickness(int $thickness): static
    {
        $this->options['barThickness'] = $thickness;

        return $this;
    }
}
