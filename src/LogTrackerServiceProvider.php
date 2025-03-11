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
    }


    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/log-tracker.php', 'log-tracker'
        );

        // Manually bind facade
        $this->app->bind('log-tracker', function () {
            return new \Kssadi\LogTracker\Services\LogParserService();
        });
    }

}
