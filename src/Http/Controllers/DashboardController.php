<?php

namespace Kssadi\LogTracker\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Kssadi\LogTracker\Facades\LogTracker;
use Kssadi\LogTracker\Services\LogParserService;
use Kssadi\LogTracker\Traits\HasThemeSupport;

class DashboardController extends Controller
{
    use HasThemeSupport;

    public function index(): View
    {
        $logFiles = LogTracker::getLogFiles();
        $stats = $this->getCachedStats($logFiles);
        $logStyles = $this->buildLogStyles();

        return $this->themedView('dashboard', array_merge($stats, compact('logStyles')));
    }

    public function refresh(): JsonResponse
    {
        try {
            $logFiles = LogTracker::getLogFiles();
            $stats = $this->buildDashboardStats($logFiles);

            $ttl = (int) config('log-tracker.dashboard_cache_ttl', 60);
            if ($ttl > 0) {
                Cache::put('log-tracker:dashboard', $stats, $ttl);
            }

            return response()->json(array_merge(
                ['success' => true],
                $stats,
                ['timestamp' => now()->toISOString()],
            ));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch dashboard data',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Build display styles for each configured log level.
     *
     * @return array<string, array{color: string, icon: string}>
     */
    private function buildLogStyles(): array
    {
        $styles = [];
        foreach (config('log-tracker.log_levels', []) as $level => $config) {
            $styles[$level] = [
                'color' => $config['color'] ?? '#6c757d',
                'icon' => $config['icon'] ?? 'fas fa-circle',
            ];
        }

        return $styles;
    }

    /**
     * Return cached dashboard stats, or build fresh (and cache) if expired.
     *
     * @param  array<int, string>  $logFiles
     * @return array<string, mixed>
     */
    private function getCachedStats(array $logFiles): array
    {
        $ttl = (int) config('log-tracker.dashboard_cache_ttl', 60);

        if ($ttl <= 0) {
            return $this->buildDashboardStats($logFiles);
        }

        return Cache::remember('log-tracker:dashboard', $ttl, fn () => $this->buildDashboardStats($logFiles));
    }

    /**
     * Build aggregated stats from all log files.
     *
     * @param  array<int, string>  $logFiles
     * @return array<string, mixed>
     */
    private function buildDashboardStats(array $logFiles): array
    {
        $levels = LogParserService::logLevelNames();
        $stats = $this->initializeStatsAccumulator($levels);

        foreach ($logFiles as $logFile) {
            foreach (LogTracker::getAllLogEntries($logFile)['entries'] as $entry) {
                $this->accumulateEntry($entry, $stats);
            }
        }

        return $this->finalizeStats($stats);
    }

    /**
     * Initialize all stat buckets with zero values.
     *
     * @param  array<int, string>  $levels
     * @return array<string, mixed>
     */
    private function initializeStatsAccumulator(array $levels): array
    {
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates[now()->subDays($i)->format('Y-m-d')] = array_fill_keys($levels, 0);
        }

        return [
            'summary' => array_merge(['total' => 0], array_fill_keys($levels, 0)),
            'logTypesCount' => array_fill_keys($levels, 0),
            'newLogsToday' => array_fill_keys($levels, 0),
            'dates' => $dates,
            'todaysTotalLogs' => 0,
            'recentLogs' => [],
            'recentLogsCount' => 0,
            'errorCounts' => [],
            'peakHours' => array_fill(0, 24, 0),
            'today' => now()->format('Y-m-d'),
        ];
    }

    /**
     * Accumulate a single log entry into the stats buckets.
     *
     * @param  array<string, mixed>  $entry
     * @param  array<string, mixed>  $stats
     */
    private function accumulateEntry(array $entry, array &$stats): void
    {
        $level = $entry['level'];

        $stats['summary']['total']++;

        if (isset($stats['summary'][$level])) {
            $stats['summary'][$level]++;
        }

        if (isset($stats['logTypesCount'][$level])) {
            $stats['logTypesCount'][$level]++;
        }

        $stats['recentLogs'][] = [
            'timestamp' => $entry['timestamp'],
            'level' => $level,
            'message' => $entry['message'],
        ];
        $stats['recentLogsCount']++;

        // Keep the buffer bounded: sort and trim every 200 additions so we
        // never hold more than 210 entries in memory regardless of file size.
        if ($stats['recentLogsCount'] % 200 === 0) {
            usort($stats['recentLogs'], fn ($a, $b) => strcmp($b['timestamp'], $a['timestamp']));
            $stats['recentLogs'] = array_slice($stats['recentLogs'], 0, 10);
        }

        if ($level === 'error') {
            $errorType = explode(':', $entry['message'])[0];
            $stats['errorCounts'][$errorType] = ($stats['errorCounts'][$errorType] ?? 0) + 1;
        }

        if (isset($entry['timestamp']) && preg_match('/(\d{4}-\d{2}-\d{2})/', $entry['timestamp'], $match)) {
            $logDate = $match[1];

            if (isset($stats['dates'][$logDate])) {
                $stats['dates'][$logDate][$level] = ($stats['dates'][$logDate][$level] ?? 0) + 1;
            }

            if ($logDate === $stats['today']) {
                $stats['todaysTotalLogs']++;
                $stats['newLogsToday'][$level] = ($stats['newLogsToday'][$level] ?? 0) + 1;
            }
        }

        if ($level === 'error' && isset($entry['timestamp']) && preg_match('/(\d{2}):\d{2}:\d{2}/', $entry['timestamp'], $match)) {
            $stats['peakHours'][(int) $match[1]]++;
        }
    }

    /**
     * Post-process accumulated stats into the final return array.
     *
     * @param  array<string, mixed>  $stats
     * @return array<string, mixed>
     */
    private function finalizeStats(array $stats): array
    {
        arsort($stats['errorCounts']);
        arsort($stats['peakHours']);

        usort($stats['recentLogs'], fn ($a, $b) => strcmp($b['timestamp'], $a['timestamp']));

        return [
            'summary' => $stats['summary'],
            'dates' => $stats['dates'],
            'logTypesCount' => $stats['logTypesCount'],
            'newLogsToday' => $stats['newLogsToday'],
            'todaysTotalLogs' => $stats['todaysTotalLogs'],
            'lastFiveLogs' => array_slice($stats['recentLogs'], 0, 5),
            'topErrors' => array_slice($stats['errorCounts'], 0, 5, true),
            'topPeakHours' => array_slice($stats['peakHours'], 0, 5, true),
        ];
    }
}
