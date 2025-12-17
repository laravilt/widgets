![widgets](https://raw.githubusercontent.com/laravilt/widgets/master/arts/screenshot.jpg)

# Laravilt Widgets

[![Latest Stable Version](https://poser.pugx.org/laravilt/widgets/version.svg)](https://packagist.org/packages/laravilt/widgets)
[![License](https://poser.pugx.org/laravilt/widgets/license.svg)](https://packagist.org/packages/laravilt/widgets)
[![Downloads](https://poser.pugx.org/laravilt/widgets/d/total.svg)](https://packagist.org/packages/laravilt/widgets)
[![Dependabot Updates](https://github.com/laravilt/widgets/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/laravilt/widgets/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/laravilt/widgets/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/laravilt/widgets/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/laravilt/widgets/actions/workflows/tests.yml/badge.svg)](https://github.com/laravilt/widgets/actions/workflows/tests.yml)


Complete dashboard widget system with stats, charts, and custom widgets for Laravilt. Display key metrics and data visualizations with beautiful, responsive widgets.

## Features

- 📊 **Stats Overview** - Display key metrics with trends and mini charts
- 📈 **Line Charts** - Area and line charts with multiple datasets
- 📊 **Bar Charts** - Vertical/horizontal bars with stacking
- 🥧 **Pie Charts** - Pie and doughnut with percentage legends
- 🔄 **Auto-Polling** - Real-time data updates
- 🎨 **Customization** - Colors, themes, icons
- 📱 **Responsive** - Mobile-friendly layouts

## Widget Types

| Widget | Description |
|--------|-------------|
| `StatsOverviewWidget` | Multiple stats in responsive grid |
| `LineChartWidget` | Line/area charts with trends |
| `BarChartWidget` | Vertical/horizontal bar charts |
| `PieChartWidget` | Pie and doughnut charts |

## Quick Examples

### Stats Overview

```php
use Laravilt\Widgets\StatsOverviewWidget;
use Laravilt\Widgets\Stat;

StatsOverviewWidget::make()
    ->columns(4)
    ->stats([
        Stat::make('Revenue', fn() => '$' . number_format(Order::sum('total'), 2))
            ->description('+12% from yesterday')
            ->descriptionIcon('TrendingUp', 'success')
            ->icon('DollarSign')
            ->color('success'),

        Stat::make('Orders', fn() => Order::count())
            ->description('Completed today')
            ->icon('ShoppingCart')
            ->color('primary')
            ->chart('bar', [8, 12, 15, 18, 22], 'primary'),
    ])
    ->polling(30); // Refresh every 30 seconds
```

### Line Chart

```php
use Laravilt\Widgets\LineChartWidget;

LineChartWidget::make(
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
    datasets: [[
        'label' => 'Sales',
        'data' => [4500, 5200, 4800, 6100, 5900],
        'borderColor' => 'rgb(34, 197, 94)',
    ]]
)
    ->heading('Revenue Trend')
    ->curved()
    ->fill()
    ->height(350);
```

### Pie Chart

```php
use Laravilt\Widgets\PieChartWidget;

PieChartWidget::make(
    labels: ['Featured', 'Regular'],
    data: [150, 350]
)
    ->heading('Product Distribution')
    ->doughnut()
    ->showLegend()
    ->showPercentage();
```

## Installation

```bash
composer require laravilt/widgets
```

## Generator Commands

```bash
php artisan make:widget UserStatsWidget --stats
php artisan make:widget SalesChartWidget --chart=line
php artisan make:widget RevenueChartWidget --chart=bar
php artisan make:widget CategoryChartWidget --chart=pie
```

## Documentation

- **[Complete Documentation](docs/index.md)** - All widget types and configuration
- **[MCP Server Guide](docs/mcp-server.md)** - AI agent integration

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
