<?php

namespace Kssadi\LogTracker\Tests;

use Kssadi\LogTracker\LogTrackerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LogTrackerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('log-tracker.middleware', ['web']);
        $app['config']->set('log-tracker.route_prefix', 'log-tracker');
        $app['config']->set('log-tracker.theme', 'LiteFlow');
    }
}
