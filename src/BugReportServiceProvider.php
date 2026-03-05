<?php

namespace Arriendo\BugReport;

use Arriendo\BugReport\Console\Commands\InstallCommand;
use Arriendo\BugReport\Console\Commands\TestEmailCommand;
use Illuminate\Support\ServiceProvider;

class BugReportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->registerPublishables();
        $this->registerCommands();
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerTranslations();
        $this->registerViews();
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/bug-report.php',
            'bug-report'
        );
    }

    /**
     * Register publishable resources.
     */
    protected function registerPublishables(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/../config/bug-report.php' => config_path('bug-report.php'),
            ], 'bug-report-config');

            // Publish migrations
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'bug-report-migrations');

            // Publish translations
            $this->publishes([
                __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/bug-report'),
            ], 'bug-report-translations');

            // Publish email views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/bug-report'),
            ], 'bug-report-views');

            // Publish Vue components (optional)
            $this->publishes([
                __DIR__ . '/../resources/js' => resource_path('js/vendor/bug-report'),
            ], 'bug-report-components');
        }
    }

    /**
     * Register package commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                TestEmailCommand::class,
            ]);
        }
    }

    /**
     * Register package routes.
     */
    protected function registerRoutes(): void
    {
        // Routes are registered by the install command to the consuming app's routes/api.php
        // This allows the consuming app to control middleware and route configuration
    }

    /**
     * Register package migrations.
     */
    protected function registerMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Register package translations.
     */
    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'bug-report');
    }

    /**
     * Register package views.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'bug-report');
    }
}
