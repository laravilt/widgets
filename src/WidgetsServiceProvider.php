<?php

namespace Laravilt\Widgets;

use Illuminate\Support\ServiceProvider;

class WidgetsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravilt-widgets.php',
            'laravilt-widgets'
        );

        // Register any services, bindings, or singletons here
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'widgets');

        // Load web routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__.'/../config/laravilt-widgets.php' => config_path('laravilt-widgets.php'),
            ], 'laravilt-widgets-config');

            // Publish assets
            $this->publishes([
                __DIR__.'/../dist' => public_path('vendor/laravilt/widgets'),
            ], 'laravilt-widgets-assets');

            // Register commands
            $this->commands([
                Commands\InstallWidgetsCommand::class,
                Commands\MakeWidgetCommand::class,
            ]);
        }
    }
}
