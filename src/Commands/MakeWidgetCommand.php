<?php

namespace Laravilt\Widgets\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeWidgetCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:widget {name : The name of the widget}
                            {--stats : Generate a stats overview widget}
                            {--chart= : Generate a chart widget (line|bar|pie)}
                            {--force : Overwrite existing file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new widget class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Widget';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('stats')) {
            $this->type = 'Stats Widget';
        } elseif ($this->option('chart')) {
            $this->type = ucfirst($this->option('chart')).' Chart Widget';
        }

        parent::handle();

        $this->components->info("{$this->type} [{$this->argument('name')}] created successfully.");

        // Show usage example
        $this->newLine();
        $this->components->bulletList([
            'Import: use App\Widgets\\'.str_replace('/', '\\', $this->argument('name')).';',
            'Usage: '.class_basename($this->argument('name')).'::make()',
        ]);
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        if ($this->option('stats')) {
            return __DIR__.'/../../stubs/widget.stats.stub';
        }

        if ($chart = $this->option('chart')) {
            return __DIR__."/../../stubs/widget.chart.{$chart}.stub";
        }

        return __DIR__.'/../../stubs/widget.stub';
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Widgets';
    }

    /**
     * Build the class with the given name.
     */
    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        return $this->replaceWidgetName($stub);
    }

    /**
     * Replace the widget name in the stub.
     */
    protected function replaceWidgetName(string $stub): string
    {
        $name = class_basename($this->argument('name'));
        $kebabName = Str::kebab($name);
        $snakeName = Str::snake($name);

        $stub = str_replace('{{ widgetKebab }}', $kebabName, $stub);
        $stub = str_replace('{{ widgetSnake }}', $snakeName, $stub);

        return $stub;
    }

    /**
     * Get the destination class path.
     */
    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }
}
