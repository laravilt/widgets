<?php

declare(strict_types=1);

namespace Laravilt\Widgets;

class StatsOverviewWidget extends Widget
{
    /** @var array<int, Stat> */
    protected array $stats = [];

    protected int $columns = 3;

    /**
     * @param  array<int, Stat>  $stats
     */
    public function stats(array $stats): static
    {
        $this->stats = $stats;

        return $this;
    }

    public function columns(int $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function toInertiaProps(): array
    {
        return [
            'component' => 'StatsOverviewWidget',
            'heading' => $this->heading,
            'description' => $this->description,
            'stats' => array_map(
                fn (Stat $stat) => $stat->toInertiaProps(),
                $this->stats
            ),
            'columns' => $this->columns,
            'polling' => [
                'enabled' => $this->pollingEnabled,
                'interval' => $this->pollingInterval,
            ],
        ];
    }
}
