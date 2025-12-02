# Laravilt Widgets Documentation

Complete dashboard widget system with stats, charts, and custom widgets for Laravilt.

## Table of Contents

1. [Getting Started](#getting-started)
2. [Architecture](#architecture)
3. [Widget Generation](#widget-generation)
4. [Widget Types](#widget-types)
5. [API Reference](#api-reference)
6. [MCP Server Integration](mcp-server.md)

## Overview

Laravilt Widgets provides a comprehensive dashboard widget system with:

- **Stats Overview**: Display key metrics with trends and comparisons
- **Line Charts**: Time-series data visualization
- **Bar Charts**: Comparison data visualization
- **Pie/Doughnut Charts**: Proportion data visualization
- **Custom Widgets**: Build your own widget types
- **Responsive**: Mobile-friendly responsive layouts
- **Inertia Integration**: Seamless Vue 3 integration

## Quick Start

```bash
# Generate a stats widget
php artisan make:widget UserStatsWidget --stats

# Generate a line chart widget
php artisan make:widget SalesChartWidget --chart=line

# Generate a bar chart widget
php artisan make:widget RevenueChartWidget --chart=bar

# Generate a pie chart widget
php artisan make:widget CategoryChartWidget --chart=pie

# Use in your dashboard
use App\Widgets\UserStatsWidget;

$widget = UserStatsWidget::make();

return Inertia::render('Dashboard', [
    'widget' => $widget->toInertiaProps(),
]);
```

## Key Features

### 📊 Stats Overview
- Multiple stat cards
- Trend indicators (up/down)
- Description icons
- Color customization
- Percentage changes

### 📈 Charts
- **Line Charts**: Time-series data with multiple datasets
- **Bar Charts**: Comparison data with grouping
- **Pie/Doughnut Charts**: Proportion data with labels

### 🎨 Customization
- Colors and themes
- Icons (Lucide icons)
- Labels and descriptions
- Tooltips
- Chart options

### ⚡ Performance
- Lazy loading
- Data caching
- Efficient rendering

## System Requirements

- PHP 8.3+
- Laravel 12+
- Inertia.js v2+
- Vue 3
- Chart.js (for chart widgets)

## Installation

```bash
composer require laravilt/widgets
```

The service provider is auto-discovered and will register automatically.

## Widget Types

### Stats Overview Widget

Display key metrics with trends and icons.

```php
<?php

namespace App\Widgets;

use Laravilt\Widgets\StatsOverviewWidget;
use Laravilt\Widgets\Stat;

class UserStatsWidget extends StatsOverviewWidget
{
    public function getStats(): array
    {
        return [
            Stat::make('Total Users', 1234)
                ->description('Total registered users')
                ->descriptionIcon('trending-up')
                ->chart([10, 20, 15, 30, 25, 40, 35])
                ->color('success'),

            Stat::make('Active Users', 856)
                ->description('12% increase')
                ->descriptionIcon('trending-up')
                ->chart([15, 25, 20, 35, 30, 45, 40])
                ->color('primary'),

            Stat::make('Revenue', '$12,345')
                ->description('8% increase')
                ->descriptionIcon('trending-up')
                ->chart([20, 30, 25, 40, 35, 50, 45])
                ->color('success'),

            Stat::make('Bounce Rate', '42%')
                ->description('5% decrease')
                ->descriptionIcon('trending-down')
                ->chart([50, 45, 40, 35, 30, 25, 20])
                ->color('danger'),
        ];
    }
}
```

### Line Chart Widget

Display time-series data with multiple datasets.

```php
<?php

namespace App\Widgets;

use Laravilt\Widgets\LineChartWidget;

class SalesChartWidget extends LineChartWidget
{
    protected static ?string $heading = 'Sales Overview';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => [1200, 1900, 1500, 2100, 1800, 2400, 2200],
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Revenue',
                    'data' => [1000, 1600, 1300, 1800, 1500, 2000, 1900],
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
```

### Bar Chart Widget

Display comparison data with grouped bars.

```php
<?php

namespace App\Widgets;

use Laravilt\Widgets\BarChartWidget;

class RevenueChartWidget extends BarChartWidget
{
    protected static ?string $heading = 'Monthly Revenue';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Product Sales',
                    'data' => [5000, 6000, 5500, 7000, 6500, 8000],
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Service Sales',
                    'data' => [3000, 4000, 3500, 5000, 4500, 6000],
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
```

### Pie/Doughnut Chart Widget

Display proportion data with colored segments.

```php
<?php

namespace App\Widgets;

use Laravilt\Widgets\PieChartWidget;

class CategoryChartWidget extends PieChartWidget
{
    protected static ?string $heading = 'Sales by Category';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [30, 25, 20, 15, 10],
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                    ],
                ],
            ],
            'labels' => ['Electronics', 'Clothing', 'Home & Garden', 'Sports', 'Books'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // or 'pie'
    }
}
```

## Stat Configuration

### Basic Stat

```php
Stat::make('Total Users', 1234)
    ->description('Total registered users')
    ->icon('users')
    ->color('primary');
```

### Stat with Trend

```php
Stat::make('Revenue', '$12,345')
    ->description('12% increase')
    ->descriptionIcon('trending-up')
    ->color('success');
```

### Stat with Chart

```php
Stat::make('Page Views', 45678)
    ->description('Last 7 days')
    ->chart([100, 120, 110, 150, 130, 170, 160])
    ->color('primary');
```

### Stat Colors

Available colors:
- `primary` - Blue
- `secondary` - Gray
- `success` - Green
- `danger` - Red
- `warning` - Orange
- `info` - Light blue

## Chart Configuration

### Chart Options

```php
protected function getOptions(): array
{
    return [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'bottom',
            ],
            'tooltip' => [
                'enabled' => true,
                'mode' => 'index',
                'intersect' => false,
            ],
        ],
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'ticks' => [
                    'precision' => 0,
                ],
            ],
        ],
    ];
}
```

### Custom Colors

```php
protected function getData(): array
{
    return [
        'datasets' => [
            [
                'label' => 'Sales',
                'data' => [1200, 1900, 1500],
                'borderColor' => '#3b82f6',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'borderWidth' => 2,
                'tension' => 0.4, // Curve smoothness for line charts
            ],
        ],
        'labels' => ['Jan', 'Feb', 'Mar'],
    ];
}
```

## Using Widgets in Dashboard

### Controller Example

```php
use App\Widgets\UserStatsWidget;
use App\Widgets\SalesChartWidget;
use App\Widgets\RevenueChartWidget;
use Inertia\Inertia;

public function dashboard()
{
    return Inertia::render('Dashboard', [
        'stats' => UserStatsWidget::make()->toInertiaProps(),
        'salesChart' => SalesChartWidget::make()->toInertiaProps(),
        'revenueChart' => RevenueChartWidget::make()->toInertiaProps(),
    ]);
}
```

### Vue Component Example

```vue
<template>
  <div class="grid gap-6">
    <!-- Stats Overview -->
    <StatsOverviewWidget :data="stats" />

    <!-- Charts -->
    <div class="grid gap-6 md:grid-cols-2">
      <LineChartWidget :data="salesChart" />
      <BarChartWidget :data="revenueChart" />
    </div>
  </div>
</template>

<script setup>
import StatsOverviewWidget from '@/components/widgets/StatsOverviewWidget.vue'
import LineChartWidget from '@/components/widgets/LineChartWidget.vue'
import BarChartWidget from '@/components/widgets/BarChartWidget.vue'

const props = defineProps({
  stats: Object,
  salesChart: Object,
  revenueChart: Object
})
</script>
```

## Generator Command

```bash
# Generate a stats widget
php artisan make:widget UserStatsWidget --stats

# Generate a line chart widget
php artisan make:widget SalesChartWidget --chart=line

# Generate a bar chart widget
php artisan make:widget RevenueChartWidget --chart=bar

# Generate a pie chart widget
php artisan make:widget CategoryChartWidget --chart=pie

# Force overwrite existing file
php artisan make:widget UserStatsWidget --force
```

## Best Practices

1. **Use Widget Classes**: Create dedicated widget classes for reusability
2. **Cache Data**: Cache expensive calculations
3. **Optimize Queries**: Use efficient database queries
4. **Limit Data Points**: Don't overload charts with too many data points
5. **Use Appropriate Chart Types**: Choose the right chart for your data
6. **Add Descriptions**: Provide context for stats
7. **Responsive Design**: Test widgets on mobile devices
8. **Loading States**: Show loading indicators for async data

## Advanced Examples

### Real-time Stats Widget

```php
class RealTimeStatsWidget extends StatsOverviewWidget
{
    protected static ?int $refreshInterval = 5; // Refresh every 5 seconds

    public function getStats(): array
    {
        $activeUsers = Cache::remember('active_users_count', 5, function () {
            return User::where('last_seen_at', '>', now()->subMinutes(5))->count();
        });

        $todaySales = Cache::remember('today_sales', 60, function () {
            return Order::whereDate('created_at', today())->sum('total');
        });

        return [
            Stat::make('Active Users', $activeUsers)
                ->description('Online now')
                ->icon('users')
                ->color('success'),

            Stat::make('Today Sales', '$' . number_format($todaySales, 2))
                ->description('Since midnight')
                ->icon('dollar-sign')
                ->color('primary'),
        ];
    }
}
```

### Filtered Chart Widget

```php
class FilteredSalesChartWidget extends LineChartWidget
{
    protected static ?string $heading = 'Sales Overview';

    public ?string $filter = '7days';

    protected function getData(): array
    {
        $days = match ($this->filter) {
            'today' => 1,
            '7days' => 7,
            '30days' => 30,
            default => 7,
        };

        $sales = Order::where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $sales->pluck('total')->toArray(),
                ],
            ],
            'labels' => $sales->pluck('date')->toArray(),
        ];
    }

    protected function getFilters(): array
    {
        return [
            'today' => 'Today',
            '7days' => 'Last 7 days',
            '30days' => 'Last 30 days',
        ];
    }
}
```

## Support

- GitHub Issues: github.com/laravilt/widgets
- Documentation: docs.laravilt.com
- Discord: discord.laravilt.com
