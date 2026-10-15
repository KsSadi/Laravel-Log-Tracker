<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DashboardRefreshRouteTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('log-tracker.route_prefix', 'admin/logs');
        $app['config']->set('log-tracker.theme', 'GlowStack');
        $app['config']->set('log-tracker.dashboard_cache_ttl', 0);
    }

    #[Test]
    public function live_refresh_uses_the_configured_route_prefix(): void
    {
        $response = $this->get('/admin/logs');

        $response->assertStatus(200);
        $response->assertSee(json_encode(route('log-tracker.api.dashboard.refresh')), false);
        $response->assertDontSee("'/log-tracker/api/dashboard-refresh'", false);
    }
}
