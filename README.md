![Widgets](./arts/screenshot.jpg)

# Widgets Plugin for Laravilt

[![Latest Stable Version](https://poser.pugx.org/laravilt/widgets/version.svg)](https://packagist.org/packages/laravilt/widgets)
[![License](https://poser.pugx.org/laravilt/widgets/license.svg)](https://packagist.org/packages/laravilt/widgets)
[![Downloads](https://poser.pugx.org/laravilt/widgets/d/total.svg)](https://packagist.org/packages/laravilt/widgets)
[![Dependabot Updates](https://github.com/laravilt/widgets/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/laravilt/widgets/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/laravilt/widgets/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/laravilt/widgets/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/laravilt/widgets/actions/workflows/tests.yml/badge.svg)](https://github.com/laravilt/widgets/actions/workflows/tests.yml)

plugin for Laravilt

## Installation

You can install the plugin via composer:

```bash
composer require laravilt/widgets
```

The package will automatically register its service provider which handles all Laravel-specific functionality (views, migrations, config, etc.).

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="widgets-config"
```

## Assets

Publish the plugin assets:

```bash
php artisan vendor:publish --tag="widgets-assets"
```

## Testing

```bash
composer test
```

## Code Style

```bash
composer format
```

## Static Analysis

```bash
composer analyse
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
