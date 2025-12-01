<?php

declare(strict_types=1);

namespace Laravilt\Widgets\Tests\Unit;

use Laravilt\Widgets\LineChartWidget;
use Laravilt\Widgets\Tests\TestCase;

class LineChartWidgetTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated(): void
    {
        $widget = LineChartWidget::make();

        expect($widget)->toBeInstanceOf(LineChartWidget::class);
    }

    /** @test */
    public function it_can_be_instantiated_with_data(): void
    {
        $widget = LineChartWidget::make(
            labels: ['Jan', 'Feb', 'Mar'],
            datasets: [
                [
                    'label' => 'Sales',
                    'data' => [100, 200, 150],
                ],
            ]
        );

        $props = $widget->toInertiaProps();

        expect($props['data']['labels'])->toBe(['Jan', 'Feb', 'Mar'])
            ->and($props['data']['datasets'])->toHaveCount(1);
    }

    /** @test */
    public function it_has_chart_type_line(): void
    {
        $widget = LineChartWidget::make();
        $props = $widget->toInertiaProps();

        expect($props['chartType'])->toBe('line');
    }

    /** @test */
    public function it_can_set_heading(): void
    {
        $widget = LineChartWidget::make()->heading('Sales Over Time');
        $props = $widget->toInertiaProps();

        expect($props['heading'])->toBe('Sales Over Time');
    }

    /** @test */
    public function it_can_set_description(): void
    {
        $widget = LineChartWidget::make()->description('Monthly sales data');
        $props = $widget->toInertiaProps();

        expect($props['description'])->toBe('Monthly sales data');
    }

    /** @test */
    public function it_can_set_data(): void
    {
        $widget = LineChartWidget::make()->data([
            'labels' => ['Jan', 'Feb', 'Mar'],
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [1000, 2000, 1500],
                ],
            ],
        ]);

        $props = $widget->toInertiaProps();

        expect($props['data']['labels'])->toBe(['Jan', 'Feb', 'Mar'])
            ->and($props['data']['datasets'][0]['label'])->toBe('Revenue');
    }

    /** @test */
    public function it_can_set_options(): void
    {
        $widget = LineChartWidget::make()->options(['responsive' => true]);
        $props = $widget->toInertiaProps();

        expect($props['options']['responsive'])->toBeTrue();
    }

    /** @test */
    public function it_can_set_height(): void
    {
        $widget = LineChartWidget::make()->height(400);
        $props = $widget->toInertiaProps();

        expect($props['height'])->toBe(400);
    }

    /** @test */
    public function it_can_set_color(): void
    {
        $widget = LineChartWidget::make()->color('primary');
        $props = $widget->toInertiaProps();

        expect($props['color'])->toBe('primary');
    }

    /** @test */
    public function it_can_be_curved(): void
    {
        $widget = LineChartWidget::make()->curved();
        $props = $widget->toInertiaProps();

        expect($props['options']['tension'])->toBe(0.4);
    }

    /** @test */
    public function it_can_disable_curve(): void
    {
        $widget = LineChartWidget::make()->curved(false);
        $props = $widget->toInertiaProps();

        expect($props['options']['tension'])->toBe(0);
    }

    /** @test */
    public function it_can_be_filled(): void
    {
        $widget = LineChartWidget::make()->fill();
        $props = $widget->toInertiaProps();

        expect($props['options']['fill'])->toBeTrue();
    }

    /** @test */
    public function it_can_show_points(): void
    {
        $widget = LineChartWidget::make()->showPoints();
        $props = $widget->toInertiaProps();

        expect($props['options']['showPoints'])->toBeTrue();
    }

    /** @test */
    public function it_can_show_grid(): void
    {
        $widget = LineChartWidget::make()->showGrid();
        $props = $widget->toInertiaProps();

        expect($props['options']['showGrid'])->toBeTrue();
    }

    /** @test */
    public function it_can_enable_polling(): void
    {
        $widget = LineChartWidget::make()->polling(60);
        $props = $widget->toInertiaProps();

        expect($props['polling']['enabled'])->toBeTrue()
            ->and($props['polling']['interval'])->toBe(60);
    }

    /** @test */
    public function it_evaluates_closure_data(): void
    {
        $widget = LineChartWidget::make()->data([
            'labels' => fn () => ['Jan', 'Feb'],
            'datasets' => fn () => [
                ['data' => [10, 20]],
            ],
        ]);

        $props = $widget->toInertiaProps();

        expect($props['data']['labels'])->toBe(['Jan', 'Feb'])
            ->and($props['data']['datasets'][0]['data'])->toBe([10, 20]);
    }

    /** @test */
    public function it_serializes_to_inertia_props(): void
    {
        $widget = LineChartWidget::make(
            labels: ['Jan', 'Feb'],
            datasets: [['data' => [10, 20]]]
        )
            ->heading('Sales')
            ->description('Monthly sales')
            ->height(300)
            ->curved()
            ->fill();

        $props = $widget->toInertiaProps();

        expect($props)->toBeArray()
            ->toHaveKeys(['component', 'heading', 'description', 'chartType', 'data', 'options', 'height', 'polling'])
            ->and($props['component'])->toBe('ChartWidget')
            ->and($props['chartType'])->toBe('line');
    }

    /** @test */
    public function it_can_chain_methods(): void
    {
        $widget = LineChartWidget::make()
            ->heading('Sales Chart')
            ->description('Quarterly sales')
            ->data(['labels' => ['Q1', 'Q2']])
            ->height(350)
            ->color('success')
            ->curved()
            ->fill()
            ->showPoints()
            ->showGrid()
            ->polling(30);

        expect($widget)->toBeInstanceOf(LineChartWidget::class);

        $props = $widget->toInertiaProps();

        expect($props['heading'])->toBe('Sales Chart')
            ->and($props['height'])->toBe(350)
            ->and($props['color'])->toBe('success')
            ->and($props['options']['tension'])->toBe(0.4)
            ->and($props['options']['fill'])->toBeTrue()
            ->and($props['options']['showPoints'])->toBeTrue();
    }
}
