<?php

declare(strict_types=1);

namespace Laravilt\Widgets\Tests\Unit;

use Laravilt\Widgets\Stat;
use Laravilt\Widgets\StatsOverviewWidget;
use Laravilt\Widgets\Tests\TestCase;

class StatsOverviewWidgetTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated(): void
    {
        $widget = StatsOverviewWidget::make();

        expect($widget)->toBeInstanceOf(StatsOverviewWidget::class);
    }

    /** @test */
    public function it_can_set_heading(): void
    {
        $widget = StatsOverviewWidget::make()->heading('Overview');
        $props = $widget->toInertiaProps();

        expect($props['heading'])->toBe('Overview');
    }

    /** @test */
    public function it_can_set_description(): void
    {
        $widget = StatsOverviewWidget::make()->description('Monthly statistics');
        $props = $widget->toInertiaProps();

        expect($props['description'])->toBe('Monthly statistics');
    }

    /** @test */
    public function it_can_set_stats(): void
    {
        $stats = [
            Stat::make('Users', 1500),
            Stat::make('Revenue', 45000),
        ];

        $widget = StatsOverviewWidget::make()->stats($stats);
        $props = $widget->toInertiaProps();

        expect($props['stats'])->toHaveCount(2)
            ->and($props['stats'][0]['label'])->toBe('Users')
            ->and($props['stats'][0]['value'])->toBe(1500);
    }

    /** @test */
    public function it_can_set_columns(): void
    {
        $widget = StatsOverviewWidget::make()->columns(4);
        $props = $widget->toInertiaProps();

        expect($props['columns'])->toBe(4);
    }

    /** @test */
    public function it_has_default_columns_of_3(): void
    {
        $widget = StatsOverviewWidget::make();
        $props = $widget->toInertiaProps();

        expect($props['columns'])->toBe(3);
    }

    /** @test */
    public function it_can_enable_polling(): void
    {
        $widget = StatsOverviewWidget::make()->polling(30);
        $props = $widget->toInertiaProps();

        expect($props['polling']['enabled'])->toBeTrue()
            ->and($props['polling']['interval'])->toBe(30);
    }

    /** @test */
    public function it_has_default_polling_interval(): void
    {
        $widget = StatsOverviewWidget::make()->polling();
        $props = $widget->toInertiaProps();

        expect($props['polling']['enabled'])->toBeTrue()
            ->and($props['polling']['interval'])->toBe(10);
    }

    /** @test */
    public function it_serializes_to_inertia_props(): void
    {
        $stats = [
            Stat::make('Users', 1500),
        ];

        $widget = StatsOverviewWidget::make()
            ->heading('Overview')
            ->description('Statistics')
            ->stats($stats)
            ->columns(4);

        $props = $widget->toInertiaProps();

        expect($props)->toBeArray()
            ->toHaveKeys(['component', 'heading', 'description', 'stats', 'columns', 'polling'])
            ->and($props['component'])->toBe('StatsOverviewWidget');
    }

    /** @test */
    public function it_can_chain_methods(): void
    {
        $stats = [
            Stat::make('Users', 1500),
        ];

        $widget = StatsOverviewWidget::make()
            ->heading('Overview')
            ->description('Statistics')
            ->stats($stats)
            ->columns(4)
            ->polling(30);

        expect($widget)->toBeInstanceOf(StatsOverviewWidget::class);

        $props = $widget->toInertiaProps();

        expect($props['heading'])->toBe('Overview')
            ->and($props['description'])->toBe('Statistics')
            ->and($props['columns'])->toBe(4)
            ->and($props['polling']['interval'])->toBe(30);
    }
}
