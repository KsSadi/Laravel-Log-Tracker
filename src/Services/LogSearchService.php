<?php

namespace Kssadi\LogTracker\Services;

class LogSearchService
{
    public function __construct(private readonly LogParserService $parser) {}

    /**
     * Search across log files with optional filters.
     *
     * Returns a paginated result set with matching log entries.
     *
     * @return array{entries: array, total: int, current_page: int, per_page: int, last_page: int, from: int, to: int}
     */
    public function search(
        ?string $query,
        ?string $level,
        ?string $dateFrom,
        ?string $dateTo,
        ?string $file,
        int $page,
        int $perPage,
    ): array {
        $files = $this->resolveFiles($file, $dateFrom, $dateTo);
        $allEntries = $this->gatherAndFilterEntries($files, $query, $level, $dateFrom, $dateTo);

        $total = count($allEntries);
        $lastPage = (int) max(ceil($total / $perPage), 1);
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;

        return [
            'entries' => array_slice($allEntries, $offset, $perPage),
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $total),
        ];
    }

    /**
     * Resolve the list of log files to search, narrowed by date range or specific file.
     *
     * @return array<int, string>
     */
    private function resolveFiles(?string $file, ?string $dateFrom, ?string $dateTo): array
    {
        $allFiles = $this->parser->getLogFiles();
        rsort($allFiles);

        if ($file !== null) {
            return in_array($file, $allFiles, true) ? [$file] : [];
        }

        if ($dateFrom === null && $dateTo === null) {
            return $allFiles;
        }

        return array_values(array_filter(
            $allFiles,
            fn (string $f): bool => $this->fileIsWithinDateRange($f, $dateFrom, $dateTo)
        ));
    }

    /**
     * Determine if a log file's date falls within the given range.
     * Non-date-stamped files are always included.
     */
    private function fileIsWithinDateRange(string $file, ?string $dateFrom, ?string $dateTo): bool
    {
        if (! preg_match('/laravel-(\d{4}-\d{2}-\d{2})\.log/', $file, $matches)) {
            return true;
        }

        $fileDate = $matches[1];

        if ($dateFrom !== null && $fileDate < $dateFrom) {
            return false;
        }

        if ($dateTo !== null && $fileDate > $dateTo) {
            return false;
        }

        return true;
    }

    /**
     * Collect all entries from the given files, apply filters, and sort newest-first.
     *
     * @param  array<int, string>  $files
     * @return array<int, array<string, mixed>>
     */
    private function gatherAndFilterEntries(
        array $files,
        ?string $query,
        ?string $level,
        ?string $dateFrom,
        ?string $dateTo,
    ): array {
        $normalizedQuery = ($query !== null && $query !== '') ? mb_strtolower(trim($query)) : null;
        $entries = [];

        foreach ($files as $file) {
            $data = $this->parser->getAllLogEntries($file);

            if (isset($data['error'])) {
                continue;
            }

            foreach ($data['entries'] as $entry) {
                if ($this->matchesAllFilters($entry, $normalizedQuery, $level, $dateFrom, $dateTo)) {
                    $entries[] = array_merge($entry, ['file' => $file]);
                }
            }
        }

        // Newest first
        usort($entries, fn (array $a, array $b): int => strcmp($b['timestamp'], $a['timestamp']));

        return $entries;
    }

    /**
     * Check that a single log entry passes all active filters.
     *
     * @param  array<string, string>  $entry
     */
    private function matchesAllFilters(
        array $entry,
        ?string $normalizedQuery,
        ?string $level,
        ?string $dateFrom,
        ?string $dateTo,
    ): bool {
        if ($level !== null && strtolower($entry['level']) !== $level) {
            return false;
        }

        // Timestamps are in "YYYY-MM-DD HH:MM:SS" format — substring comparison is safe and fast.
        $entryDate = substr($entry['timestamp'], 0, 10);

        if ($dateFrom !== null && $entryDate < $dateFrom) {
            return false;
        }

        if ($dateTo !== null && $entryDate > $dateTo) {
            return false;
        }

        if ($normalizedQuery !== null) {
            $haystack = mb_strtolower($entry['message'].($entry['stack'] ?? ''));

            if (! str_contains($haystack, $normalizedQuery)) {
                return false;
            }
        }

        return true;
    }
}
