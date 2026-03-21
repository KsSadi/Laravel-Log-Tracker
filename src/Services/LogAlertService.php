<?php

namespace Kssadi\LogTracker\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Kssadi\LogTracker\Mail\LogAlertMail;

class LogAlertService
{
    public function __construct(private readonly LogParserService $parser) {}

    /**
     * Check all log files against configured thresholds and send alerts.
     *
     * Returns a list of triggered alerts (empty when alerts are disabled or
     * no threshold is exceeded).
     *
     * @return array<int, array{file: string, level: string, count: int}>
     */
    public function checkAndNotify(): array
    {
        if (! config('log-tracker.alerts.enabled', false)) {
            return [];
        }

        $windowMinutes = (int) config('log-tracker.alerts.window_minutes', 60);
        $cooldown = (int) config('log-tracker.alerts.cooldown_minutes', 15);
        $thresholds = (array) config('log-tracker.alerts.thresholds', []);
        $cutoff = Carbon::now()->subMinutes($windowMinutes);

        $alertsSent = [];

        foreach ($this->parser->getLogFiles() as $logName) {
            $result = $this->parser->getAllLogEntries($logName);
            $counts = $this->countRecentByLevel($result['entries'], $cutoff);

            foreach ($thresholds as $level => $threshold) {
                if ((int) $threshold <= 0) {
                    continue;
                }

                $count = $counts[$level] ?? 0;
                $cacheKey = "log-tracker:cooldown:{$logName}:{$level}";

                if ($count >= (int) $threshold && ! Cache::has($cacheKey)) {
                    $payload = [
                        'file' => $logName,
                        'level' => $level,
                        'count' => $count,
                        'threshold' => (int) $threshold,
                        'window_minutes' => $windowMinutes,
                    ];

                    $this->dispatch($payload);

                    Cache::put($cacheKey, true, Carbon::now()->addMinutes($cooldown));

                    $alertsSent[] = ['file' => $logName, 'level' => $level, 'count' => $count];
                }
            }
        }

        return $alertsSent;
    }

    /**
     * Count log entries per level that fall within the given time window.
     *
     * @param  array<int, array{timestamp: string, level: string}>  $entries
     * @param  Carbon  $cutoff  Earliest timestamp to include
     * @return array<string, int>
     */
    public function countRecentByLevel(array $entries, Carbon $cutoff): array
    {
        $counts = [];

        foreach ($entries as $entry) {
            try {
                $entryAt = Carbon::parse($entry['timestamp']);
            } catch (\Exception) {
                continue;
            }

            if ($entryAt->lessThan($cutoff)) {
                continue;
            }

            $level = strtolower($entry['level']);
            $counts[$level] = ($counts[$level] ?? 0) + 1;
        }

        return $counts;
    }

    /**
     * Dispatch alert to every enabled channel.
     *
     * @param  array{file: string, level: string, count: int, threshold: int, window_minutes: int}  $payload
     */
    private function dispatch(array $payload): void
    {
        $message = $this->buildMessage($payload);

        $this->sendMail($payload, $message);
        $this->sendSlack($message);
        $this->sendDiscord($message);
        $this->sendWebhook($payload, $message);
    }

    /**
     * Build the human-readable alert message body.
     *
     * @param  array{file: string, level: string, count: int, threshold: int, window_minutes: int}  $payload
     */
    public function buildMessage(array $payload): string
    {
        return sprintf(
            "[Log Tracker Alert] %s threshold exceeded in %s\nCount: %d (threshold: %d) in the last %d minute(s)\nDetected at: %s",
            strtoupper($payload['level']),
            $payload['file'],
            $payload['count'],
            $payload['threshold'],
            $payload['window_minutes'],
            Carbon::now()->format('Y-m-d H:i:s'),
        );
    }

    // ─────────────────────────────────────────────
    // Private channel senders
    // ─────────────────────────────────────────────

    /**
     * @param  array{file: string, level: string}  $payload
     */
    private function sendMail(array $payload, string $message): void
    {
        /** @var array{enabled?: bool, to?: string} $cfg */
        $cfg = (array) config('log-tracker.alerts.channels.mail', []);

        if (empty($cfg['enabled']) || empty($cfg['to'])) {
            return;
        }

        Mail::to($cfg['to'])->send(new LogAlertMail($payload, $message));
    }

    private function sendSlack(string $message): void
    {
        /** @var array{enabled?: bool, webhook_url?: string} $cfg */
        $cfg = (array) config('log-tracker.alerts.channels.slack', []);

        if (empty($cfg['enabled']) || empty($cfg['webhook_url'])) {
            return;
        }

        Http::post($cfg['webhook_url'], ['text' => $message]);
    }

    private function sendDiscord(string $message): void
    {
        /** @var array{enabled?: bool, webhook_url?: string} $cfg */
        $cfg = (array) config('log-tracker.alerts.channels.discord', []);

        if (empty($cfg['enabled']) || empty($cfg['webhook_url'])) {
            return;
        }

        // Discord message cap is 2 000 characters
        Http::post($cfg['webhook_url'], ['content' => substr($message, 0, 2000)]);
    }

    /**
     * @param  array{file: string, level: string, count: int, threshold: int, window_minutes: int}  $payload
     */
    private function sendWebhook(array $payload, string $message): void
    {
        /** @var array{enabled?: bool, url?: string, method?: string, headers?: array<string, string>} $cfg */
        $cfg = (array) config('log-tracker.alerts.channels.webhook', []);

        if (empty($cfg['enabled']) || empty($cfg['url'])) {
            return;
        }

        $method = strtolower((string) ($cfg['method'] ?? 'post'));
        $headers = (array) ($cfg['headers'] ?? []);
        $body = array_merge($payload, [
            'message' => $message,
            'sent_at' => Carbon::now()->toIso8601String(),
        ]);

        $request = Http::withHeaders($headers);

        if ($method === 'get') {
            $request->get($cfg['url'], $body);
        } else {
            $request->post($cfg['url'], $body);
        }
    }
}
