<?php

namespace Kssadi\LogTracker;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Kssadi\LogTracker\Console\Commands\CheckLogAlertsCommand;
use Kssadi\LogTracker\Console\Commands\CleanupCommand;
use Kssadi\LogTracker\Console\Commands\ThemeCommand;
use Kssadi\LogTracker\Services\LogAlertService;
use Kssadi\LogTracker\Services\LogExportService;
use Kssadi\LogTracker\Services\LogParserService;
use Kssadi\LogTracker\Services\LogSearchService;
use Kssadi\LogTracker\Services\ThemeManager;

class LogTrackerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'log-tracker');

        $this->publishes([
            __DIR__.'/../config/log-tracker.php' => config_path('log-tracker.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/log-tracker'),
        ], 'views');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupCommand::class,
                ThemeCommand::class,
                CheckLogAlertsCommand::class,
            ]);
        }

        // Share the package version with all Log Tracker views dynamically.
        // Composer\InstalledVersions is available in all Composer 2+ projects
        // (composer/runtime-api) with no extra dependency required.
        $packageVersion = $this->resolvePackageVersion();
        $this->app['view']->composer('log-tracker::*', function ($view) use ($packageVersion): void {
            $view->with('logTrackerVersion', $packageVersion);
        });

        // Auto-schedule alert checks every minute when alerts are enabled
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            if (config('log-tracker.alerts.enabled', false)) {
                $schedule->command('log-tracker:check-alerts')->everyMinute();
            }
        });
    }

    /**
     * Resolve the installed version of this package at runtime.
     *
     * Uses Composer's InstalledVersions (available in Composer 2+) so the
     * version shown in the footer always matches the actually installed release
     * without any hardcoding.
     */
    private function resolvePackageVersion(): string
    {
        if (class_exists(\Composer\InstalledVersions::class)) {
            try {
                if (\Composer\InstalledVersions::isInstalled('kssadi/log-tracker')) {
                    return \Composer\InstalledVersions::getPrettyVersion('kssadi/log-tracker') ?? 'dev';
                }
            } catch (\Throwable) {
                // Fall through to default
            }
        }

        return 'dev';
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/log-tracker.php', 'log-tracker'
        );

        // Register Theme Manager as singleton
        $this->app->singleton(ThemeManager::class, function () {
            return new ThemeManager;
        });

        // Bind LogSearchService — resolved with LogParserService injected
        $this->app->singleton(LogSearchService::class, function ($app): LogSearchService {
            return new LogSearchService($app->make(LogParserService::class));
        });

        // Bind facade to LogParserService singleton
        $this->app->singleton('log-tracker', function () {
            return new LogParserService;
        });

        // Alias class resolution to the same singleton
        $this->app->alias('log-tracker', LogParserService::class);

        // Share the same LogParserService singleton with LogExportService
        $this->app->singleton(LogExportService::class, function ($app) {
            return new LogExportService($app->make('log-tracker'));
        });

        // LogAlertService — depends on the same LogParserService singleton
        $this->app->singleton(LogAlertService::class, function ($app): LogAlertService {
            return new LogAlertService($app->make('log-tracker'));
        });
    }
}
