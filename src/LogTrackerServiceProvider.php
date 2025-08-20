<?php

namespace Kssadi\LogTracker;

use Illuminate\Support\ServiceProvider;

class LogTrackerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'log-tracker');

        $this->publishes([
            __DIR__ . '/../config/log-tracker.php' => config_path('log-tracker.php'),
        ], 'config');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Kssadi\LogTracker\Console\Commands\ThemeCommand::class,
            ]);
        }
    }


    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/log-tracker.php', 'log-tracker'
        );

        // Register Theme Manager as singleton
        $this->app->singleton(\Kssadi\LogTracker\Services\ThemeManager::class, function ($app) {
            return new \Kssadi\LogTracker\Services\ThemeManager();
        });

        // Manually bind facade
       $this->app->bind('log-tracker', function () {
            return new \Kssadi\LogTracker\Services\LogParserService();
        });

        // NEW: Register LogExportService
        $this->app->bind(\Kssadi\LogTracker\Services\LogExportService::class, function ($app) {
            return new \Kssadi\LogTracker\Services\LogExportService(
                $app->make(\Kssadi\LogTracker\Services\LogParserService::class)
            );
        });

    }

}
