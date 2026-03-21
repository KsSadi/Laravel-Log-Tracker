<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Kssadi\LogTracker\Mail\LogAlertMail;
use Kssadi\LogTracker\Services\LogAlertService;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LogAlertServiceTest extends TestCase
{
    private string $logName = 'laravel-alert-test.log';

    private string $logPath;

    private LogAlertService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logPath = storage_path("logs/{$this->logName}");
        $this->service = $this->app->make(LogAlertService::class);

        // Default: alerts enabled, low thresholds so tests can trigger easily
        $this->app['config']->set('log-tracker.alerts.enabled', true);
        $this->app['config']->set('log-tracker.alerts.window_minutes', 60);
        $this->app['config']->set('log-tracker.alerts.cooldown_minutes', 15);
        $this->app['config']->set('log-tracker.alerts.thresholds', [
            'error' => 2,
            'warning' => 5,
        ]);
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

    // ──────────────────────────────────────────────
    // enabled flag
    // ──────────────────────────────────────────────

    #[Test]
    public function returns_empty_when_alerts_are_disabled(): void
    {
        $this->app['config']->set('log-tracker.alerts.enabled', false);

        $result = $this->service->checkAndNotify();

        $this->assertEmpty($result);
    }

    // ──────────────────────────────────────────────
    // threshold logic
    // ──────────────────────────────────────────────

    #[Test]
    public function returns_empty_when_no_log_files_exist(): void
    {
        // Ensure no log files interfere (test isolation via unique log name)
        $result = $this->service->checkAndNotify();

        $this->assertEmpty($result);
    }

    #[Test]
    public function returns_empty_when_count_is_below_threshold(): void
    {
        // threshold for error = 2; write only 1
        $content = '['.now()->format('Y-m-d H:i:s').'] local.ERROR: Under threshold'."\n";
        File::put($this->logPath, $content);

        $result = $this->service->checkAndNotify();

        $this->assertEmpty($result);
    }

    #[Test]
    public function triggers_alert_when_count_meets_threshold(): void
    {
        // threshold for error = 2; write exactly 2
        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Repeated error\n";
        $content .= "[{$ts}] local.ERROR: Repeated error\n";
        File::put($this->logPath, $content);

        $result = $this->service->checkAndNotify();

        $this->assertCount(1, $result);
        $this->assertSame($this->logName, $result[0]['file']);
        $this->assertSame('error', $result[0]['level']);
        $this->assertSame(2, $result[0]['count']);
    }

    #[Test]
    public function does_not_count_entries_outside_the_time_window(): void
    {
        // threshold = 2; write 2 entries but outside the 60-min window
        $old = now()->subHours(2)->format('Y-m-d H:i:s');
        $content = "[{$old}] local.ERROR: Old error\n";
        $content .= "[{$old}] local.ERROR: Old error\n";
        File::put($this->logPath, $content);

        $result = $this->service->checkAndNotify();

        $this->assertEmpty($result);
    }

    // ──────────────────────────────────────────────
    // cooldown
    // ──────────────────────────────────────────────

    #[Test]
    public function cooldown_prevents_a_second_alert_within_the_window(): void
    {
        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Repeated error\n";
        $content .= "[{$ts}] local.ERROR: Repeated error\n";
        File::put($this->logPath, $content);

        // First call — should alert
        $first = $this->service->checkAndNotify();
        $this->assertCount(1, $first);

        // Second call — still within cooldown
        $second = $this->service->checkAndNotify();
        $this->assertEmpty($second);
    }

    #[Test]
    public function alert_fires_again_after_cooldown_expires(): void
    {
        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Repeated error\n";
        $content .= "[{$ts}] local.ERROR: Repeated error\n";
        File::put($this->logPath, $content);

        // Manually place an already-expired cooldown
        $cacheKey = "log-tracker:cooldown:{$this->logName}:error";
        Cache::put($cacheKey, true, Carbon::now()->subSecond());

        $result = $this->service->checkAndNotify();

        $this->assertCount(1, $result);
    }

    // ──────────────────────────────────────────────
    // countRecentByLevel helper
    // ──────────────────────────────────────────────

    #[Test]
    public function count_recent_by_level_counts_within_window(): void
    {
        $cutoff = Carbon::now()->subHour();

        $entries = [
            ['timestamp' => now()->subMinutes(10)->format('Y-m-d H:i:s'), 'level' => 'error'],
            ['timestamp' => now()->subMinutes(20)->format('Y-m-d H:i:s'), 'level' => 'error'],
            ['timestamp' => now()->subHours(2)->format('Y-m-d H:i:s'), 'level' => 'error'],   // outside
            ['timestamp' => now()->subMinutes(5)->format('Y-m-d H:i:s'), 'level' => 'warning'],
        ];

        $counts = $this->service->countRecentByLevel($entries, $cutoff);

        $this->assertSame(2, $counts['error']);
        $this->assertSame(1, $counts['warning']);
        $this->assertArrayNotHasKey('info', $counts);
    }

    #[Test]
    public function count_recent_by_level_ignores_unparsable_timestamps(): void
    {
        $cutoff = Carbon::now()->subHour();
        $entries = [
            ['timestamp' => 'not-a-date', 'level' => 'error'],
            ['timestamp' => now()->subMinutes(5)->format('Y-m-d H:i:s'), 'level' => 'error'],
        ];

        $counts = $this->service->countRecentByLevel($entries, $cutoff);

        $this->assertSame(1, $counts['error']);
    }

    // ──────────────────────────────────────────────
    // buildMessage
    // ──────────────────────────────────────────────

    #[Test]
    public function build_message_contains_expected_fields(): void
    {
        $payload = [
            'file' => 'laravel.log',
            'level' => 'error',
            'count' => 55,
            'threshold' => 50,
            'window_minutes' => 60,
        ];

        $message = $this->service->buildMessage($payload);

        $this->assertStringContainsString('ERROR', $message);
        $this->assertStringContainsString('laravel.log', $message);
        $this->assertStringContainsString('55', $message);
        $this->assertStringContainsString('50', $message);
        $this->assertStringContainsString('60', $message);
    }

    // ──────────────────────────────────────────────
    // Mail channel
    // ──────────────────────────────────────────────

    #[Test]
    public function sends_mail_when_mail_channel_is_enabled(): void
    {
        Mail::fake();

        $this->app['config']->set('log-tracker.alerts.channels.mail', [
            'enabled' => true,
            'to' => 'admin@example.com',
        ]);

        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Mail test\n";
        $content .= "[{$ts}] local.ERROR: Mail test\n";
        File::put($this->logPath, $content);

        $this->service->checkAndNotify();

        Mail::assertSent(LogAlertMail::class, fn ($mail) => $mail->hasTo('admin@example.com'));
    }

    #[Test]
    public function does_not_send_mail_when_mail_channel_is_disabled(): void
    {
        Mail::fake();

        $this->app['config']->set('log-tracker.alerts.channels.mail.enabled', false);

        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Mail skip test\n";
        $content .= "[{$ts}] local.ERROR: Mail skip test\n";
        File::put($this->logPath, $content);

        $this->service->checkAndNotify();

        Mail::assertNothingSent();
    }

    // ──────────────────────────────────────────────
    // Slack channel
    // ──────────────────────────────────────────────

    #[Test]
    public function sends_slack_webhook_when_slack_channel_is_enabled(): void
    {
        Http::fake();

        $this->app['config']->set('log-tracker.alerts.channels.slack', [
            'enabled' => true,
            'webhook_url' => 'https://hooks.slack.com/fake',
        ]);

        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Slack test\n";
        $content .= "[{$ts}] local.ERROR: Slack test\n";
        File::put($this->logPath, $content);

        $this->service->checkAndNotify();

        Http::assertSent(fn ($req) => str_contains((string) $req->url(), 'hooks.slack.com'));
    }

    // ──────────────────────────────────────────────
    // Discord channel
    // ──────────────────────────────────────────────

    #[Test]
    public function sends_discord_webhook_when_discord_channel_is_enabled(): void
    {
        Http::fake();

        $this->app['config']->set('log-tracker.alerts.channels.discord', [
            'enabled' => true,
            'webhook_url' => 'https://discord.com/api/webhooks/fake',
        ]);

        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Discord test\n";
        $content .= "[{$ts}] local.ERROR: Discord test\n";
        File::put($this->logPath, $content);

        $this->service->checkAndNotify();

        Http::assertSent(fn ($req) => str_contains((string) $req->url(), 'discord.com'));
    }

    // ──────────────────────────────────────────────
    // Generic webhook channel
    // ──────────────────────────────────────────────

    #[Test]
    public function sends_generic_webhook_post_when_webhook_channel_is_enabled(): void
    {
        Http::fake();

        $this->app['config']->set('log-tracker.alerts.channels.webhook', [
            'enabled' => true,
            'url' => 'https://my-app.example.com/hooks/log-alert',
            'method' => 'POST',
            'headers' => ['Authorization' => 'Bearer secret'],
        ]);

        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: Webhook test\n";
        $content .= "[{$ts}] local.ERROR: Webhook test\n";
        File::put($this->logPath, $content);

        $this->service->checkAndNotify();

        Http::assertSent(fn ($req) => str_contains((string) $req->url(), 'my-app.example.com'));
    }

    #[Test]
    public function does_not_send_webhook_when_webhook_channel_is_disabled(): void
    {
        Http::fake();

        $this->app['config']->set('log-tracker.alerts.channels.webhook.enabled', false);

        $ts = now()->format('Y-m-d H:i:s');
        $content = "[{$ts}] local.ERROR: No webhook test\n";
        $content .= "[{$ts}] local.ERROR: No webhook test\n";
        File::put($this->logPath, $content);

        $this->service->checkAndNotify();

        Http::assertNothingSent();
    }
}
