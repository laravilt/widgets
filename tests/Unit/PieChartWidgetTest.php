<?php

declare(strict_types=1);

namespace Laravilt\Widgets\Tests\Unit;

use Laravilt\Widgets\PieChartWidget;
use Laravilt\Widgets\Tests\TestCase;

class PieChartWidgetTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated(): void
    {
        $widget = PieChartWidget::make();

        expect($widget)->toBeInstanceOf(PieChartWidget::class);
    }

    /** @test */
    public function it_can_be_instantiated_with_data(): void
    {
        $widget = PieChartWidget::make(
            labels: ['Red', 'Blue', 'Yellow'],
            data: [300, 50, 100]
        );

        $props = $widget->toInertiaProps();

        expect($props['data']['labels'])->toBe(['Red', 'Blue', 'Yellow'])
            ->and($props['data']['datasets'][0]['data'])->toBe([300, 50, 100]);
    }

    /** @test */
    public function it_has_chart_type_pie(): void
    {
        $widget = PieChartWidget::make();
        $props = $widget->toInertiaProps();

        expect($props['chartType'])->toBe('pie');
    }

    /** @test */
    public function it_can_set_heading(): void
    {
        $widget = PieChartWidget::make()->heading('Market Share');
        $props = $widget->toInertiaProps();

        expect($props['heading'])->toBe('Market Share');
    }

    /** @test */
    public function it_can_set_description(): void
    {
        $widget = PieChartWidget::make()->description('Distribution by category');
        $props = $widget->toInertiaProps();

        expect($props['description'])->toBe('Distribution by category');
    }

    /** @test */
    public function it_can_set_data(): void
    {
        $widget = PieChartWidget::make()->data([
            'labels' => ['A', 'B', 'C'],
            'datasets' => [
                [
                    'data' => [100, 200, 150],
                ],
            ],
        ]);

        $props = $widget->toInertiaProps();

        expect($props['data']['labels'])->toBe(['A', 'B', 'C'])
            ->and($props['data']['datasets'][0]['data'])->toBe([100, 200, 150]);
    }

    /** @test */
    public function it_can_be_doughnut(): void
    {
        $widget = PieChartWidget::make()->doughnut();
        $props = $widget->toInertiaProps();

        expect($props['chartType'])->toBe('doughnut');
    }

    /** @test */
    public function it_can_toggle_doughnut(): void
    {
        $widget = PieChartWidget::make()->doughnut(false);
        $props = $widget->toInertiaProps();

        expect($props['chartType'])->toBe('pie');
    }

    /** @test */
    public function it_can_show_legend(): void
    {
        $widget = PieChartWidget::make()->showLegend();
        $props = $widget->toInertiaProps();

        expect($props['options']['showLegend'])->toBeTrue();
    }

    /** @test */
    public function it_can_hide_legend(): void
    {
        $widget = PieChartWidget::make()->showLegend(false);
        $props = $widget->toInertiaProps();

        expect($props['options']['showLegend'])->toBeFalse();
    }

    /** @test */
    public function it_can_show_percentage(): void
    {
        $widget = PieChartWidget::make()->showPercentage();
        $props = $widget->toInertiaProps();

        expect($props['options']['showPercentage'])->toBeTrue();
    }

    /** @test */
    public function it_can_set_height(): void
    {
        $widget = PieChartWidget::make()->height(300);
        $props = $widget->toInertiaProps();

        expect($props['height'])->toBe(300);
    }

    /** @test */
    public function it_can_set_color(): void
    {
        $widget = PieChartWidget::make()->color('primary');
        $props = $widget->toInertiaProps();

        expect($props['color'])->toBe('primary');
    }

    /** @test */
    public function it_can_enable_polling(): void
    {
        $widget = PieChartWidget::make()->polling(20);
        $props = $widget->toInertiaProps();

        expect($props['polling']['enabled'])->toBeTrue()
            ->and($props['polling']['interval'])->toBe(20);
    }

    /** @test */
    public function it_serializes_to_inertia_props(): void
    {
        $widget = PieChartWidget::make(
            labels: ['A', 'B'],
            data: [100, 200]
        )
            ->heading('Distribution')
            ->description('Category distribution')
            ->height(300)
            ->doughnut()
            ->showLegend();

        $props = $widget->toInertiaProps();

        expect($props)->toBeArray()
            ->toHaveKeys(['component', 'heading', 'description', 'chartType', 'data', 'options', 'height', 'polling'])
            ->and($props['component'])->toBe('ChartWidget')
            ->and($props['chartType'])->toBe('doughnut');
    }

    /** @test */
    public function it_can_chain_methods(): void
    {
        $widget = PieChartWidget::make()
            ->heading('Category Chart')
            ->description('Distribution')
            ->data(['labels' => ['A', 'B']])
            ->height(350)
            ->color('info')
            ->doughnut()
            ->showLegend()
            ->showPercentage()
            ->polling(15);

        expect($widget)->toBeInstanceOf(PieChartWidget::class);

        $props = $widget->toInertiaProps();

        expect($props['heading'])->toBe('Category Chart')
            ->and($props['height'])->toBe(350)
            ->and($props['color'])->toBe('info')
            ->and($props['chartType'])->toBe('doughnut')
            ->and($props['options']['showLegend'])->toBeTrue()
            ->and($props['options']['showPercentage'])->toBeTrue();
    }
}
