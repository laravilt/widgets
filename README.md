![Widgets](./arts/screenshot.jpg)

# Laravilt Widgets

[![Latest Stable Version](https://poser.pugx.org/laravilt/widgets/version.svg)](https://packagist.org/packages/laravilt/widgets)
[![License](https://poser.pugx.org/laravilt/widgets/license.svg)](https://packagist.org/packages/laravilt/widgets)
[![Downloads](https://poser.pugx.org/laravilt/widgets/d/total.svg)](https://packagist.org/packages/laravilt/widgets)

Complete dashboard widget system with stats, charts, and custom widgets for Laravilt. Display key metrics and data visualizations with beautiful, responsive widgets.

## Features

- 📊 **Stats Overview** - Display key metrics with trends
- 📈 **Charts** - Line, Bar, Pie/Doughnut charts
- 🎨 **Customization** - Colors, themes, icons
- 📱 **Responsive** - Mobile-friendly layouts

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
