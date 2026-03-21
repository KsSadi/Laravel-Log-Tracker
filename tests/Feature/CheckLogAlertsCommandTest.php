<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Services\LogAlertService;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CheckLogAlertsCommandTest extends TestCase
{
    private string $logName = 'laravel-cmd-alert-test.log';

    private string $logPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logPath = storage_path("logs/{$this->logName}");

        $this->app['config']->set('log-tracker.alerts.enabled', true);
        $this->app['config']->set('log-tracker.alerts.window_minutes', 60);
        $this->app['config']->set('log-tracker.alerts.cooldown_minutes', 15);
        $this->app['config']->set('log-tracker.alerts.thresholds', ['error' => 2]);
        $this->app['config']->set('log-tracker.alerts.channels', [
            'mail' => ['enabled' => false, 'to' => ''],
            'slack' => ['enabled' => false, 'webhook_url' => ''],
            'discord' => ['enabled' => false, 'webhook_url' => ''],
            'webhook' => ['enabled' => false, 'url' => ''],
        ]);
    }

    protected function tearDown(): void
    {
        if (File::exists($this->logPath)) {
            File::delete($this->logPath);
        }

        Cache::flush();

        parent::tearDown();
    }

    #[Test]
    public function command_exits_with_success_when_alerts_are_disabled(): void
    {
        $this->app['config']->set('log-tracker.alerts.enabled', false);

        $this->artisan('log-tracker:check-alerts')->assertExitCode(0);
    }

    #[Test]
    public function command_outputs_disabled_message_when_alerts_are_off(): void
    {
        $this->app['config']->set('log-tracker.alerts.enabled', false);

        $this->artisan('log-tracker:check-alerts')
            ->expectsOutputToContain('disabled')
            ->assertExitCode(0);
    }

    #[Test]
    public function command_outputs_no_alerts_when_thresholds_are_not_exceeded(): void
    {
        // Write 1 error — threshold is 2
        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Single error\n";
        File::put($this->logPath, $content);

        $this->artisan('log-tracker:check-alerts')
            ->expectsOutputToContain('No alert')
            ->assertExitCode(0);
    }

    #[Test]
    public function command_outputs_alert_message_when_threshold_is_exceeded(): void
    {
        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Repeated error\n";
        $content .= "[{$ts}] local.ERROR: Repeated error\n";
        File::put($this->logPath, $content);

        $this->artisan('log-tracker:check-alerts')
            ->expectsOutputToContain('Alert sent')
            ->assertExitCode(0);
    }

    #[Test]
    public function command_exits_successfully_after_alert_is_sent(): void
    {
        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Error one\n";
        $content .= "[{$ts}] local.ERROR: Error two\n";
        File::put($this->logPath, $content);

        $this->artisan('log-tracker:check-alerts')->assertExitCode(0);
    }

    #[Test]
    public function command_uses_mocked_alert_service(): void
    {
        $mock = $this->createMock(LogAlertService::class);
        $mock->expects($this->once())
            ->method('checkAndNotify')
            ->willReturn([
                ['file' => 'laravel.log', 'level' => 'error', 'count' => 55],
            ]);

        $this->app->instance(LogAlertService::class, $mock);

        $this->artisan('log-tracker:check-alerts')
            ->expectsOutputToContain('Alert sent')
            ->assertExitCode(0);
    }

    #[Test]
    public function command_is_registered_and_available(): void
    {
        $this->artisan('list')
            ->expectsOutputToContain('log-tracker:check-alerts')
            ->assertExitCode(0);
    }
}
