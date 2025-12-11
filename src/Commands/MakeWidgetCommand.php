<?php

namespace Laravilt\Widgets\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeWidgetCommand extends Command
{
    protected $signature = 'laravilt:widget
                            {name? : The name of the widget}
                            {--panel= : The panel for the widget}
                            {--type= : Widget type (basic|stats|chart)}
                            {--chart= : Chart type (line|bar|pie|doughnut|area)}';

    protected $description = 'Create a new widget class with interactive options';

    protected string $widgetType = 'basic';

    protected ?string $chartType = null;

    protected bool $enablePolling = false;

    protected int $pollingInterval = 10;

    public function handle(): int
    {
        // Get widget name
        $name = $this->argument('name') ?? text(
            label: 'What is the widget name?',
            placeholder: 'StatsOverview',
            required: true,
            hint: 'Use StudlyCase (e.g., StatsOverview, RecentOrders)'
        );

        // Get panel
        $panels = $this->getAvailablePanels();
        $panel = $this->option('panel');

        if (! $panel && ! empty($panels)) {
            $panel = select(
                label: 'Which panel is this widget for?',
                options: array_merge(['(none)' => 'No specific panel (App\Widgets)'], array_combine($panels, $panels)),
                default: $panels[0] ?? '(none)'
            );
            if ($panel === '(none)') {
                $panel = null;
            }
        }

        // Get widget type
        $this->widgetType = $this->option('type') ?? select(
            label: 'What type of widget?',
            options: [
                'basic' => 'Basic Widget - Custom content',
                'stats' => 'Stats Overview - Display statistics',
                'chart' => 'Chart Widget - Display charts',
            ],
            default: 'basic'
        );

        // If chart widget, get chart type
        if ($this->widgetType === 'chart') {
            $this->chartType = $this->option('chart') ?? select(
                label: 'What type of chart?',
                options: [
                    'line' => 'Line Chart',
                    'bar' => 'Bar Chart',
                    'pie' => 'Pie Chart',
                    'doughnut' => 'Doughnut Chart',
                    'area' => 'Area Chart',
                ],
                default: 'line'
            );
        }

        // Ask about polling
        $this->enablePolling = confirm(
            label: 'Enable auto-refresh polling?',
            default: false,
            hint: 'Automatically refresh widget data at an interval'
        );

        if ($this->enablePolling) {
            $this->pollingInterval = (int) text(
                label: 'Polling interval (seconds)',
                default: '10',
                hint: 'How often to refresh the widget data'
            );
        }

        // Create the widget
        $this->createWidget($name, $panel);

        $this->newLine();
        $this->components->info("Widget [{$name}] created successfully!");
        $this->newLine();

        // Show usage example
        $namespace = $panel ? "App\\Laravilt\\{$panel}\\Widgets" : 'App\\Widgets';
        $this->components->bulletList([
            "Location: {$namespace}\\{$name}",
            "Usage: {$name}::make()",
        ]);

        // Clear caches (don't rebuild as closures can't be serialized)
        $this->newLine();
        $this->call('optimize:clear');

        return self::SUCCESS;
    }

    /**
     * Get available panels.
     */
    protected function getAvailablePanels(): array
    {
        $laraviltPath = app_path('Laravilt');

        if (! File::isDirectory($laraviltPath)) {
            return [];
        }

        return collect(File::directories($laraviltPath))
            ->map(fn ($dir) => basename($dir))
            ->values()
            ->toArray();
    }

    /**
     * Create the widget file.
     */
    protected function createWidget(string $name, ?string $panel): void
    {
        $studlyName = Str::studly($name);

        // Determine path
        if ($panel) {
            $namespace = "App\\Laravilt\\{$panel}\\Widgets";
            $path = app_path("Laravilt/{$panel}/Widgets/{$studlyName}.php");
        } else {
            $namespace = 'App\\Widgets';
            $path = app_path("Widgets/{$studlyName}.php");
        }

        File::ensureDirectoryExists(dirname($path));

        // Generate content based on type
        $content = match ($this->widgetType) {
            'stats' => $this->generateStatsWidget($namespace, $studlyName),
            'chart' => $this->generateChartWidget($namespace, $studlyName),
            default => $this->generateBasicWidget($namespace, $studlyName),
        };

        File::put($path, $content);

        $this->components->task('Creating widget class', fn () => true);
    }

    /**
     * Generate basic widget content.
     */
    protected function generateBasicWidget(string $namespace, string $name): string
    {
        $pollingProperties = $this->buildPollingProperties();
        $kebabName = Str::kebab($name);

        return <<<PHP
<?php

namespace {$namespace};

use Laravilt\Widgets\Widget;

class {$name} extends Widget
{
    protected ?string \$heading = '{$name}';
{$pollingProperties}
    public function toInertiaProps(): array
    {
        return [
            'component' => 'BasicWidget',
            'heading' => \$this->heading,
            'description' => \$this->description,
            'data' => \$this->getData(),
            'polling' => [
                'enabled' => \$this->pollingEnabled,
                'interval' => \$this->pollingInterval,
            ],
        ];
    }

    protected function getData(): array
    {
        return [
            // Add your widget data here
        ];
    }
}
PHP;
    }

    /**
     * Generate stats widget content.
     */
    protected function generateStatsWidget(string $namespace, string $name): string
    {
        $pollingProperties = $this->buildPollingProperties();

        return <<<PHP
<?php

namespace {$namespace};

use Laravilt\Widgets\StatsOverviewWidget;
use Laravilt\Widgets\Stat;

class {$name} extends StatsOverviewWidget
{
    protected ?string \$heading = '{$name}';
{$pollingProperties}
    public function __construct()
    {
        \$this->stats(\$this->getStats());
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', '1,234')
                ->description('12% increase')
                ->icon('TrendingUp')
                ->chart([7, 4, 6, 8, 5, 9, 10])
                ->color('success'),

            Stat::make('Total Orders', '567')
                ->description('8% decrease')
                ->icon('TrendingDown')
                ->chart([10, 9, 5, 8, 6, 4, 7])
                ->color('danger'),

            Stat::make('Revenue', '\$12,345')
                ->description('No change')
                ->icon('DollarSign')
                ->chart([5, 5, 5, 5, 5, 5, 5])
                ->color('secondary'),
        ];
    }
}
PHP;
    }

    /**
     * Generate chart widget content.
     */
    protected function generateChartWidget(string $namespace, string $name): string
    {
        $pollingProperties = $this->buildPollingProperties();
        $chartClass = ucfirst($this->chartType).'ChartWidget';

        // Check if specific chart widget exists, otherwise use base ChartWidget
        $baseClass = match ($this->chartType) {
            'line' => 'LineChartWidget',
            'bar' => 'BarChartWidget',
            'pie' => 'PieChartWidget',
            default => 'ChartWidget',
        };

        return <<<PHP
<?php

namespace {$namespace};

use Laravilt\Widgets\\{$baseClass};

class {$name} extends {$baseClass}
{
    protected ?string \$heading = '{$name}';

    protected ?string \$chartType = '{$this->chartType}';
{$pollingProperties}
    public function __construct()
    {
        \$this->data(\$this->getData());
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Dataset 1',
                    'data' => [65, 59, 80, 81, 56, 55, 40],
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        ];
    }
}
PHP;
    }

    /**
     * Build polling properties.
     */
    protected function buildPollingProperties(): string
    {
        if (! $this->enablePolling) {
            return '';
        }

        return <<<PHP

    protected bool \$pollingEnabled = true;

    protected ?int \$pollingInterval = {$this->pollingInterval};

PHP;
    }
}
