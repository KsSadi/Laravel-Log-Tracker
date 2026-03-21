<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DashboardControllerTest extends TestCase
{
    private string $logPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logPath = storage_path('logs/test-dashboard.log');

        // Disable caching so all existing tests work without cache side-effects
        $this->app['config']->set('log-tracker.dashboard_cache_ttl', 0);
        Cache::flush();

        $logContent = "[2025-06-01 10:00:00] local.ERROR: Test error message\n";
        $logContent .= "[2025-06-01 10:01:00] local.WARNING: Test warning message\n";
        $logContent .= "[2025-06-01 10:02:00] local.INFO: Test info message\n";

        File::put($this->logPath, $logContent);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        File::delete($this->logPath);
        parent::tearDown();
    }

    #[Test]
    public function it_renders_the_dashboard_page(): void
    {
        $response = $this->get(route('log-tracker.dashboard'));

        $response->assertStatus(200);
    }

    #[Test]
    public function dashboard_contains_expected_view_data(): void
    {
        $response = $this->get(route('log-tracker.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'summary',
            'dates',
            'logTypesCount',
            'newLogsToday',
            'logStyles',
        ]);
    }

    #[Test]
    public function dashboard_summary_contains_total_count(): void
    {
        $response = $this->get(route('log-tracker.dashboard'));

        $data = $response->viewData('summary');
        $this->assertArrayHasKey('total', $data);
        $this->assertGreaterThanOrEqual(3, $data['total']);
    }

    #[Test]
    public function dashboard_log_styles_are_built_from_config(): void
    {
        $response = $this->get(route('log-tracker.dashboard'));

        $logStyles = $response->viewData('logStyles');
        $this->assertIsArray($logStyles);

        foreach ($logStyles as $level => $style) {
            $this->assertArrayHasKey('color', $style);
            $this->assertArrayHasKey('icon', $style);
        }
    }

    #[Test]
    public function refresh_endpoint_returns_json(): void
    {
        $response = $this->getJson(route('log-tracker.api.dashboard.refresh'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'summary',
            'dates',
            'logTypesCount',
            'timestamp',
        ]);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function refresh_returns_correct_summary_structure(): void
    {
        $response = $this->getJson(route('log-tracker.api.dashboard.refresh'));

        $data = $response->json();
        $this->assertArrayHasKey('total', $data['summary']);
        $this->assertIsInt($data['summary']['total']);
    }

    #[Test]
    public function dashboard_works_with_no_log_files(): void
    {
        File::delete($this->logPath);

        // Remove all .log files to ensure clean state
        $logDir = storage_path('logs');
        $existingLogs = File::glob($logDir.'/*.log');
        foreach ($existingLogs as $logFile) {
            File::delete($logFile);
        }

        $response = $this->get(route('log-tracker.dashboard'));

        $response->assertStatus(200);
        $data = $response->viewData('summary');
        $this->assertSame(0, $data['total']);
    }

    #[Test]
    public function dashboard_dates_contain_last_seven_days(): void
    {
        $response = $this->get(route('log-tracker.dashboard'));

        $dates = $response->viewData('dates');
        $this->assertCount(7, $dates);

        $today = now()->format('Y-m-d');
        $this->assertArrayHasKey($today, $dates);
    }

    #[Test]
    public function it_caches_dashboard_stats_when_ttl_is_configured(): void
    {
        $this->app['config']->set('log-tracker.dashboard_cache_ttl', 60);

        $this->get(route('log-tracker.dashboard'));

        $this->assertTrue(Cache::has('log-tracker:dashboard'));
    }

    #[Test]
    public function it_refresh_populates_the_cache_with_fresh_stats(): void
    {
        $this->app['config']->set('log-tracker.dashboard_cache_ttl', 60);

        // Seed the cache with stale data
        Cache::put('log-tracker:dashboard', ['summary' => ['total' => 0]], 60);

        $response = $this->getJson(route('log-tracker.api.dashboard.refresh'));
        $response->assertJson(['success' => true]);

        // Cache must now contain fresh data reflecting our 3 test log entries
        $cached = Cache::get('log-tracker:dashboard');
        $this->assertNotNull($cached);
        $this->assertGreaterThan(0, $cached['summary']['total']);
    }
}
