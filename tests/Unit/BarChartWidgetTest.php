<?php

declare(strict_types=1);

namespace Laravilt\Widgets\Tests\Unit;

use Laravilt\Widgets\BarChartWidget;
use Laravilt\Widgets\Tests\TestCase;

class BarChartWidgetTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated(): void
    {
        $widget = BarChartWidget::make();

        expect($widget)->toBeInstanceOf(BarChartWidget::class);
    }

    /** @test */
    public function it_can_be_instantiated_with_data(): void
    {
        $widget = BarChartWidget::make(
            labels: ['Product A', 'Product B', 'Product C'],
            datasets: [
                [
                    'label' => 'Sales',
                    'data' => [100, 200, 150],
                ],
            ]
        );

        $props = $widget->toInertiaProps();

        expect($props['data']['labels'])->toBe(['Product A', 'Product B', 'Product C'])
            ->and($props['data']['datasets'])->toHaveCount(1);
    }

    /** @test */
    public function it_has_chart_type_bar(): void
    {
        $widget = BarChartWidget::make();
        $props = $widget->toInertiaProps();

        expect($props['chartType'])->toBe('bar');
    }

    /** @test */
    public function it_can_set_heading(): void
    {
        $widget = BarChartWidget::make()->heading('Product Sales');
        $props = $widget->toInertiaProps();

        expect($props['heading'])->toBe('Product Sales');
    }

    /** @test */
    public function it_can_set_description(): void
    {
        $widget = BarChartWidget::make()->description('Sales by product');
        $props = $widget->toInertiaProps();

        expect($props['description'])->toBe('Sales by product');
    }

    /** @test */
    public function it_can_set_data(): void
    {
        $widget = BarChartWidget::make()->data([
            'labels' => ['A', 'B', 'C'],
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [1000, 2000, 1500],
                ],
            ],
        ]);

        $props = $widget->toInertiaProps();

        expect($props['data']['labels'])->toBe(['A', 'B', 'C'])
            ->and($props['data']['datasets'][0]['label'])->toBe('Revenue');
    }

    /** @test */
    public function it_can_be_horizontal(): void
    {
        $widget = BarChartWidget::make()->horizontal();
        $props = $widget->toInertiaProps();

        expect($props['options']['horizontal'])->toBeTrue();
    }

    /** @test */
    public function it_can_disable_horizontal(): void
    {
        $widget = BarChartWidget::make()->horizontal(false);
        $props = $widget->toInertiaProps();

        expect($props['options']['horizontal'])->toBeFalse();
    }

    /** @test */
    public function it_can_be_stacked(): void
    {
        $widget = BarChartWidget::make()->stacked();
        $props = $widget->toInertiaProps();

        expect($props['options']['stacked'])->toBeTrue();
    }

    /** @test */
    public function it_can_show_grid(): void
    {
        $widget = BarChartWidget::make()->showGrid();
        $props = $widget->toInertiaProps();

        expect($props['options']['showGrid'])->toBeTrue();
    }

    /** @test */
    public function it_can_set_bar_thickness(): void
    {
        $widget = BarChartWidget::make()->barThickness(20);
        $props = $widget->toInertiaProps();

        expect($props['options']['barThickness'])->toBe(20);
    }

    /** @test */
    public function it_can_set_height(): void
    {
        $widget = BarChartWidget::make()->height(400);
        $props = $widget->toInertiaProps();

        expect($props['height'])->toBe(400);
    }

    /** @test */
    public function it_can_set_color(): void
    {
        $widget = BarChartWidget::make()->color('primary');
        $props = $widget->toInertiaProps();

        expect($props['color'])->toBe('primary');
    }

    /** @test */
    public function it_can_enable_polling(): void
    {
        $widget = BarChartWidget::make()->polling(45);
        $props = $widget->toInertiaProps();

        expect($props['polling']['enabled'])->toBeTrue()
            ->and($props['polling']['interval'])->toBe(45);
    }

    /** @test */
    public function it_serializes_to_inertia_props(): void
    {
        $widget = BarChartWidget::make(
            labels: ['A', 'B'],
            datasets: [['data' => [10, 20]]]
        )
            ->heading('Sales')
            ->description('Product sales')
            ->height(300)
            ->horizontal()
            ->stacked();

        $props = $widget->toInertiaProps();

        expect($props)->toBeArray()
            ->toHaveKeys(['component', 'heading', 'description', 'chartType', 'data', 'options', 'height', 'polling'])
            ->and($props['component'])->toBe('ChartWidget')
            ->and($props['chartType'])->toBe('bar');
    }

    /** @test */
    public function it_can_chain_methods(): void
    {
        $widget = BarChartWidget::make()
            ->heading('Product Chart')
            ->description('Sales by product')
            ->data(['labels' => ['A', 'B']])
            ->height(350)
            ->color('success')
            ->horizontal()
            ->stacked()
            ->showGrid()
            ->barThickness(25)
            ->polling(30);

        expect($widget)->toBeInstanceOf(BarChartWidget::class);

        $props = $widget->toInertiaProps();

        expect($props['heading'])->toBe('Product Chart')
            ->and($props['height'])->toBe(350)
            ->and($props['color'])->toBe('success')
            ->and($props['options']['horizontal'])->toBeTrue()
            ->and($props['options']['stacked'])->toBeTrue()
            ->and($props['options']['barThickness'])->toBe(25);
    }
}
