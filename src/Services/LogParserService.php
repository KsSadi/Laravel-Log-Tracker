<?php

namespace Kssadi\LogTracker\Services;

use Illuminate\Support\Facades\File;

class LogParserService
{
    /**
     * Reject pattern: lines matching this are NOT stack traces.
     * Combined alternation — replaces 4 individual preg_match calls.
     */
    private const STACK_TRACE_REJECT_PATTERN = '/^[a-zA-Z][a-zA-Z0-9\s]*:|^\d{4}-\d{2}-\d{2}|^(?:GET|POST|PUT|DELETE|PATCH)\s|^(?:INFO|DEBUG|ERROR|WARNING)/';

    /**
     * Accept pattern: lines matching this ARE stack traces.
     * Combined alternation — replaces 12 individual preg_match calls.
     */
    private const STACK_TRACE_ACCEPT_PATTERN = '/^#\d+\s|^Stack trace:|^[a-zA-Z]:\\\\.*\.php\(\d+\)|^\/.*\.php\(\d+\)|\s+at\s.*\(|\s+in\s.*\.php:\d+|thrown in\s.*\.php\son\sline\s\d+|^\s+Object\(.*\)|^\s+Closure\(.*\)|Illuminate\\\\.*::|^\s*\}\s*$|^Previous exception:|[\\\\\/].*\.[a-z]+\(\d+\)/';

    /**
     * Get configured log level names (excluding display-only 'total').
     *
     * @return array<int, string>
     */
    public static function logLevelNames(): array
    {
        return array_values(array_filter(
            array_keys(config('log-tracker.log_levels', [])),
            fn (string $level): bool => $level !== 'total'
        ));
    }

    public function getLogFiles(): array
    {
        $logPath = storage_path('logs');
        $logFiles = File::files($logPath);

        $logFiles = array_filter($logFiles, fn ($file) => $file->getExtension() === 'log');

        return array_map(fn ($file) => $file->getFilename(), array_values($logFiles));
    }

    public function getLogEntries(string $logName, int $page = 1, ?int $perPage = null): array
    {
        $perPage ??= config('log-tracker.log_per_page', 50);

        $result = $this->loadEntries($logName);

        if (isset($result['error'])) {
            return array_merge(
                ['entries' => [], 'total' => 0, 'current_page' => $page, 'per_page' => $perPage, 'last_page' => 1, 'from' => 0, 'to' => 0],
                $result,
            );
        }

        $entries = $result['entries'];
        $total = count($entries);
        $lastPage = (int) max(ceil($total / $perPage), 1);
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;

        return [
            'entries' => array_slice($entries, $offset, $perPage),
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $total),
        ];
    }

    /**
     * Get repeated log entries grouped by message, sorted by occurrence count descending.
     *
     * Only entries that appear more than once are included.
     * Returns at most $limit groups, newest-last / oldest-first per group timestamps.
     *
     * @return array<int, array{message: string, level: string, count: int, first_seen: string, last_seen: string}>
     */
    public function getEntryFrequency(string $logName, int $limit = 15): array
    {
        $result = $this->loadEntries($logName);

        if (isset($result['error'])) {
            return [];
        }

        return $this->calculateFrequency($result['entries'], $limit);
    }

    /**
     * Calculate entry frequency from an already-loaded entries array.
     *
     * Use this when you already hold the parsed entries (e.g. from getAllLogEntries)
     * to avoid reading and parsing the log file a second time.
     *
     * @param  array<int, array<string, mixed>>  $entries
     * @return array<int, array{message: string, level: string, count: int, first_seen: string, last_seen: string}>
     */
    public function calculateFrequency(array $entries, int $limit = 15): array
    {
        $groups = [];

        foreach ($entries as $entry) {
            $key = $entry['message'];
            $level = strtolower($entry['level']);
            $ts = $entry['timestamp'];

            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'message' => $entry['message'],
                    'level' => $level,
                    'count' => 0,
                    'first_seen' => $ts,
                    'last_seen' => $ts,
                ];
            }

            $groups[$key]['count']++;

            if ($ts < $groups[$key]['first_seen']) {
                $groups[$key]['first_seen'] = $ts;
            }

            if ($ts > $groups[$key]['last_seen']) {
                $groups[$key]['last_seen'] = $ts;
            }
        }

        $groups = array_filter($groups, fn (array $g): bool => $g['count'] > 1);

        usort($groups, fn (array $a, array $b): int => $b['count'] <=> $a['count']);

        return array_slice(array_values($groups), 0, $limit);
    }

    /**
     * Get all log entries without pagination for overview/counting purposes.
     */
    public function getAllLogEntries(string $logName): array
    {
        $result = $this->loadEntries($logName);

        if (isset($result['error'])) {
            return array_merge(['entries' => [], 'total' => 0], $result);
        }

        return [
            'entries' => $result['entries'],
            'total' => count($result['entries']),
        ];
    }

    /**
     * Load and parse all entries from a log file.
     *
     * @return array{entries?: array, error?: string, file_size_mb?: float, max_size_mb?: int}
     */
    private function loadEntries(string $logName): array
    {
        $logName = basename($logName);
        $logFile = storage_path("logs/{$logName}");

        $validation = $this->validateLogFile($logFile);
        if ($validation !== null) {
            return $validation;
        }

        $logContents = File::get($logFile);
        $logLines = explode("\n", trim($logContents));

        return ['entries' => array_reverse($this->parseRawLogLines($logLines))];
    }

    /**
     * Validate that a log file exists and is within the configured size limit.
     *
     * Returns null when valid, or an error array when invalid.
     *
     * @return array{error: string, file_size_mb?: float, max_size_mb?: int}|null
     */
    private function validateLogFile(string $logFile): ?array
    {
        if (! File::exists($logFile)) {
            return ['error' => 'Log file not found'];
        }

        $maxFileSizeMB = config('log-tracker.max_file_size', 50);
        $fileSizeMB = File::size($logFile) / 1024 / 1024;

        if ($fileSizeMB > $maxFileSizeMB) {
            return [
                'error' => 'File size ('.round($fileSizeMB, 2)." MB) exceeds maximum allowed size ({$maxFileSizeMB} MB)",
                'file_size_mb' => $fileSizeMB,
                'max_size_mb' => $maxFileSizeMB,
            ];
        }

        return null;
    }

    /**
     * Parse raw log lines into structured log entry arrays.
     *
     * Lines are expected in forward (chronological) order; callers reverse
     * the resulting entries array for newest-first display.
     */
    private function parseRawLogLines(array $logLines): array
    {
        $entries = [];
        $currentEntry = null;

        foreach ($logLines as $line) {
            if (preg_match('/\[(.*?)\]\s(\w+)\.(\w+):\s(.*)/', $line, $matches)) {
                if ($currentEntry !== null) {
                    $currentEntry['stack'] = trim($currentEntry['stack']);
                    $entries[] = $currentEntry;
                }

                $currentEntry = [
                    'timestamp' => $matches[1],
                    'level' => strtolower($matches[3]),
                    'message' => $matches[4],
                    'stack' => '',
                ];
            } elseif ($currentEntry !== null && ! empty(trim($line))) {
                if ($this->isStackTraceLine(trim($line))) {
                    if (! empty($currentEntry['stack'])) {
                        $currentEntry['stack'] .= "\n";
                    }
                    $currentEntry['stack'] .= $line;
                }
            }
        }

        if ($currentEntry !== null) {
            $currentEntry['stack'] = trim($currentEntry['stack']);
            $entries[] = $currentEntry;
        }

        return $entries;
    }

    /**
     * Check if a line looks like part of a stack trace.
     */
    private function isStackTraceLine(string $line): bool
    {
        if (empty(trim($line))) {
            return false;
        }

        if (preg_match(self::STACK_TRACE_REJECT_PATTERN, $line)) {
            return false;
        }

        return (bool) preg_match(self::STACK_TRACE_ACCEPT_PATTERN, $line);
    }
}
