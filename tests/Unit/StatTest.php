<?php

declare(strict_types=1);

namespace Laravilt\Widgets\Tests\Unit;

use Laravilt\Widgets\Stat;
use Laravilt\Widgets\Tests\TestCase;

class StatTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated(): void
    {
        $stat = Stat::make('Users', 1500);

        expect($stat)->toBeInstanceOf(Stat::class);
    }

    /** @test */
    public function it_can_set_label_and_value(): void
    {
        $stat = Stat::make('Revenue', 45000);
        $props = $stat->toInertiaProps();

        expect($props['label'])->toBe('Revenue')
            ->and($props['value'])->toBe(45000);
    }

    /** @test */
    public function it_can_use_closure_for_value(): void
    {
        $stat = Stat::make('Dynamic', fn () => 100 * 2);
        $props = $stat->toInertiaProps();

        expect($props['value'])->toBe(200);
    }

    /** @test */
    public function it_can_set_description(): void
    {
        $stat = Stat::make('Users', 1500)->description('Total active users');
        $props = $stat->toInertiaProps();

        expect($props['description'])->toBe('Total active users');
    }

    /** @test */
    public function it_can_set_icon(): void
    {
        $stat = Stat::make('Users', 1500)->icon('users');
        $props = $stat->toInertiaProps();

        expect($props['icon'])->toBe('users');
    }

    /** @test */
    public function it_can_set_description_icon(): void
    {
        $stat = Stat::make('Revenue', 45000)->descriptionIcon('trending-up', 'success');
        $props = $stat->toInertiaProps();

        expect($props['descriptionIcon'])->toBeTrue()
            ->and($props['icon'])->toBe('trending-up')
            ->and($props['descriptionColor'])->toBe('success');
    }

    /** @test */
    public function it_can_set_color(): void
    {
        $stat = Stat::make('Users', 1500)->color('primary');
        $props = $stat->toInertiaProps();

        expect($props['color'])->toBe('primary');
    }

    /** @test */
    public function it_can_set_chart(): void
    {
        $stat = Stat::make('Revenue', 45000)->chart('line', [10, 20, 30, 40], 'success');
        $props = $stat->toInertiaProps();

        expect($props['chart'])->toBe('line')
            ->and($props['chartData'])->toBe([10, 20, 30, 40])
            ->and($props['chartColor'])->toBe('success');
    }

    /** @test */
    public function it_can_set_url(): void
    {
        $stat = Stat::make('Users', 1500)->url('/users');
        $props = $stat->toInertiaProps();

        expect($props['url'])->toBe('/users');
    }

    /** @test */
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

    /** @test */
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
