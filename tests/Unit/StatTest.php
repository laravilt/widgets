<?php

declare(strict_types=1);

namespace Laravilt\Widgets\Tests\Unit;

use Laravilt\Widgets\Stat;
use Laravilt\Widgets\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class StatTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated(): void
    {
        $stat = Stat::make('Users', 1500);

        expect($stat)->toBeInstanceOf(Stat::class);
    }

    #[Test]
    public function it_can_set_label_and_value(): void
    {
        $stat = Stat::make('Revenue', 45000);
        $props = $stat->toInertiaProps();

        expect($props['label'])->toBe('Revenue')
            ->and($props['value'])->toBe(45000);
    }

    #[Test]
    public function it_can_use_closure_for_value(): void
    {
        $stat = Stat::make('Dynamic', fn () => 100 * 2);
        $props = $stat->toInertiaProps();

        expect($props['value'])->toBe(200);
    }

    #[Test]
    public function it_can_set_description(): void
    {
        $stat = Stat::make('Users', 1500)->description('Total active users');
        $props = $stat->toInertiaProps();

        expect($props['description'])->toBe('Total active users');
    }

    #[Test]
    public function it_can_set_icon(): void
    {
        $stat = Stat::make('Users', 1500)->icon('users');
        $props = $stat->toInertiaProps();

        expect($props['icon'])->toBe('users');
    }

    #[Test]
    public function it_can_set_description_icon(): void
    {
        $stat = Stat::make('Revenue', 45000)->descriptionIcon('trending-up', 'success');
        $props = $stat->toInertiaProps();

        expect($props['descriptionIcon'])->toBeTrue()
            ->and($props['icon'])->toBe('trending-up')
            ->and($props['descriptionColor'])->toBe('success');
    }

    #[Test]
    public function it_can_set_color(): void
    {
        $stat = Stat::make('Users', 1500)->color('primary');
        $props = $stat->toInertiaProps();

        expect($props['color'])->toBe('primary');
    }

    #[Test]
    public function it_can_set_chart(): void
    {
        $stat = Stat::make('Revenue', 45000)->chart('line', [10, 20, 30, 40], 'success');
        $props = $stat->toInertiaProps();

        expect($props['chart'])->toBe('line')
            ->and($props['chartData'])->toBe([10, 20, 30, 40])
            ->and($props['chartColor'])->toBe('success');
    }

    #[Test]
    public function it_can_set_chart_with_data_first(): void
    {
        $stat = Stat::make('Revenue', 45000)->chart([5, 15, 25], 'bar', 'primary');
        $props = $stat->toInertiaProps();

        expect($props['chart'])->toBe('bar')
            ->and($props['chartData'])->toBe([5, 15, 25])
            ->and($props['chartColor'])->toBe('primary');
    }

    #[Test]
    public function it_rejects_a_non_string_chart_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Stat::make('Revenue', 45000)->chart([5, 15], []);
    }

    #[Test]
    public function it_can_set_url(): void
    {
        $stat = Stat::make('Users', 1500)->url('/users');
        $props = $stat->toInertiaProps();

        expect($props['url'])->toBe('/users');
    }

    #[Test]
    public function it_serializes_to_inertia_props(): void
    {
        $stat = Stat::make('Users', 1500)
            ->description('Active users')
            ->icon('users')
            ->color('primary')
            ->url('/users');

        $props = $stat->toInertiaProps();

        expect($props)->toBeArray()
            ->toHaveKeys(['label', 'value', 'description', 'icon', 'color', 'url']);
    }

    #[Test]
    public function it_can_chain_methods(): void
    {
        $stat = Stat::make('Revenue', 45000)
            ->description('+12% from last month')
            ->icon('dollar-sign')
            ->descriptionIcon('trending-up', 'success')
            ->color('primary')
            ->chart('line', [10, 20, 30], 'success')
            ->url('/revenue');

        expect($stat)->toBeInstanceOf(Stat::class);

        $props = $stat->toInertiaProps();

        expect($props['label'])->toBe('Revenue')
            ->and($props['value'])->toBe(45000)
            ->and($props['description'])->toBe('+12% from last month')
            ->and($props['descriptionIcon'])->toBeTrue()
            ->and($props['icon'])->toBe('trending-up')
            ->and($props['color'])->toBe('primary')
            ->and($props['chart'])->toBe('line')
            ->and($props['chartData'])->toBe([10, 20, 30])
            ->and($props['url'])->toBe('/revenue');
    }
}
