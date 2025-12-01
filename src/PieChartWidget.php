<?php

declare(strict_types=1);

namespace Laravilt\Widgets;

class PieChartWidget extends ChartWidget
{
    protected ?string $chartType = 'pie';

    /**
     * @param  array<int, string|int|float>  $data
     */
    public static function make(array $labels = [], array $data = []): static
    {
        $widget = new static;

        if (! empty($labels) && ! empty($data)) {
            $widget->data([
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $data,
                    ],
                ],
            ]);
        }

        return $widget;
    }

    public function doughnut(bool $condition = true): static
    {
        $this->chartType = $condition ? 'doughnut' : 'pie';

        return $this;
    }

    public function showLegend(bool $condition = true): static
    {
        $this->options['showLegend'] = $condition;

        return $this;
    }

    public function showPercentage(bool $condition = true): static
    {
        $this->options['showPercentage'] = $condition;

        return $this;
    }
}
