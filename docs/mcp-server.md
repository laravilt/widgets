# MCP Server Integration

The Laravilt Widgets package can be integrated with MCP (Model Context Protocol) server for AI agent interaction.

## Available Generator Command

### make:widget
Generate a new widget class.

**Usage:**
```bash
php artisan make:widget UserStatsWidget --stats
php artisan make:widget SalesChartWidget --chart=line
php artisan make:widget RevenueChartWidget --chart=bar
php artisan make:widget CategoryChartWidget --chart=pie
php artisan make:widget CustomWidget --force
```

**Arguments:**
- `name` (string, required): Widget class name (StudlyCase)

**Options:**
- `--stats`: Generate a stats overview widget
- `--chart=TYPE`: Generate a chart widget (line, bar, pie)
- `--force`: Overwrite existing file

**Generated Structure (Stats):**
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
            Stat::make('Total Users', 0)
                ->description('Total registered users')
                ->descriptionIcon('trending-up')
                ->chart([0, 0, 0, 0, 0, 0, 0])
                ->color('primary'),

            Stat::make('Active Users', 0)
                ->description('Active in last 30 days')
                ->descriptionIcon('trending-up')
                ->chart([0, 0, 0, 0, 0, 0, 0])
                ->color('success'),
        ];
    }
}
```

**Generated Structure (Line Chart):**
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
                    'data' => [0, 0, 0, 0, 0, 0, 0],
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
```

**Generated Structure (Bar Chart):**
```php
<?php

namespace App\Widgets;

use Laravilt\Widgets\BarChartWidget;

class RevenueChartWidget extends BarChartWidget
{
    protected static ?string $heading = 'Revenue Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [0, 0, 0, 0, 0, 0],
                    'backgroundColor' => '#3b82f6',
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

**Generated Structure (Pie/Doughnut Chart):**
```php
<?php

namespace App\Widgets;

use Laravilt\Widgets\PieChartWidget;

class CategoryChartWidget extends PieChartWidget
{
    protected static ?string $heading = 'Category Distribution';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [0, 0, 0, 0],
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                    ],
                ],
            ],
            'labels' => ['Category 1', 'Category 2', 'Category 3', 'Category 4'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // or 'pie'
    }
}
```

## Widget Types Reference

For MCP tools to provide widget type information:

- **StatsOverviewWidget**: Display multiple stat cards with values, trends, and mini charts
- **LineChartWidget**: Time-series line charts with multiple datasets
- **BarChartWidget**: Comparison bar charts with grouped data
- **PieChartWidget**: Pie or doughnut charts for proportion data

## Integration Example

MCP server tools should provide:

1. **list-widgets** - List all widget classes in the application
2. **widget-info** - Get details about a specific widget class
3. **generate-widget** - Generate a new widget class with specified type
4. **list-widget-types** - List all available widget types

## Security

The MCP server runs with the same permissions as your Laravel application. Ensure:
- Proper file permissions on the app/Widgets directory
- Secure configuration of the MCP server
- Limited access to the MCP configuration file
