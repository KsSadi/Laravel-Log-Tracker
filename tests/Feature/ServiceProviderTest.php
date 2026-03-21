<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Kssadi\LogTracker\Facades\LogTracker;
use Kssadi\LogTracker\LogTrackerServiceProvider;
use Kssadi\LogTracker\Services\LogParserService;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_the_service_provider(): void
    {
        $this->assertArrayHasKey(
            LogTrackerServiceProvider::class,
            $this->app->getLoadedProviders()
        );
    }

    #[Test]
    public function it_binds_log_tracker_to_the_container(): void
    {
        $this->assertTrue($this->app->bound('log-tracker'));
    }

    #[Test]
    public function it_binds_log_tracker_as_singleton(): void
    {
        $instance1 = $this->app->make('log-tracker');
        $instance2 = $this->app->make('log-tracker');

        $this->assertSame($instance1, $instance2);
    }

    #[Test]
    public function it_loads_the_default_config(): void
    {
        $this->assertSame('log-tracker', config('log-tracker.route_prefix'));
        $this->assertIsInt(config('log-tracker.log_per_page'));
        $this->assertIsArray(config('log-tracker.log_levels'));
        $this->assertIsArray(config('log-tracker.export'));
    }

    #[Test]
    public function it_registers_the_facade(): void
    {
        $resolved = LogTracker::getFacadeRoot();

        $this->assertInstanceOf(LogParserService::class, $resolved);
    }
}
