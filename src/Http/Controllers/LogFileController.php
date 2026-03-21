<?php

namespace Kssadi\LogTracker\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Facades\LogTracker;
use Kssadi\LogTracker\Services\LogParserService;
use Kssadi\LogTracker\Traits\HasThemeSupport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogFileController extends Controller
{
    use HasThemeSupport;

    public function index(): View
    {
        $logFiles = LogTracker::getLogFiles();
        rsort($logFiles);

        $totalFiles = count($logFiles);
        $logFilesPerPage = config('log-tracker.log_files_per_page', 10);
        $page = request()->integer('page', 1);
        $lastPage = (int) max(ceil($totalFiles / $logFilesPerPage), 1);
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $logFilesPerPage;
        $paginatedFiles = array_slice($logFiles, $offset, $logFilesPerPage);

        $counts = [];
        $fileSizes = [];
        $formattedFileNames = [];
        $logLevels = config('log-tracker.log_levels', []);

        foreach ($paginatedFiles as $logFile) {
            $formattedFileNames[$logFile] = $this->formatFileName($logFile);
            $fileSizes[$logFile] = $this->formatFileSize($logFile);
            $counts[$logFile] = $this->countFileLevels($logFile);
        }

        $pagination = [
            'current_page' => $page,
            'last_page' => $lastPage,
            'per_page' => $logFilesPerPage,
            'total' => $totalFiles,
            'from' => $offset + 1,
            'to' => min($offset + $logFilesPerPage, $totalFiles),
        ];

        return $this->themedView('logs', compact('paginatedFiles', 'logFiles', 'counts', 'totalFiles', 'fileSizes', 'formattedFileNames', 'logLevels', 'pagination'));
    }

    public function show(string $logName): View
    {
        $logName = basename($logName);
        $logFiles = LogTracker::getLogFiles();
        $logData = LogTracker::getAllLogEntries($logName);
        $logConfig = config('log-tracker.log_levels', []);

        if (isset($logData['error'])) {
            return $this->themedView('log-details', [
                'logFiles' => $logFiles,
                'logName' => $logName,
                'entries' => [],
                'counts' => ['total' => 0, 'error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0],
                'logLevels' => [],
                'totalEntries' => 0,
                'perPage' => config('log-tracker.log_per_page', 50),
                'frequentEntries' => [],
                'error' => $logData['error'],
                'file_size_mb' => $logData['file_size_mb'] ?? null,
                'max_size_mb' => $logData['max_size_mb'] ?? null,
            ]);
        }

        $entries = collect($logData['entries'] ?? [])->map(function (array $entry) use ($logConfig): array {
            $level = strtolower($entry['level']);

            return [
                'timestamp' => Carbon::parse($entry['timestamp'])->format('j M Y, h:i:s A'),
                'level' => $level,
                'message' => $entry['message'],
                'stack' => $entry['stack'] ?? '',
                'color' => $logConfig[$level]['color'] ?? '#6c757d',
                'icon' => $logConfig[$level]['icon'] ?? 'fas fa-circle',
            ];
        })->toArray();

        $counts = ['total' => $logData['total']];
        $logLevels = [];

        foreach ($logConfig as $level => $config) {
            if ($level === 'total') {
                continue;
            }

            $counts[$level] = count(array_filter($entries, fn ($e) => $e['level'] === $level));

            if ($counts[$level] > 0) {
                $logLevels[$level] = [
                    'color' => $config['color'] ?? '#6c757d',
                    'icon' => $config['icon'] ?? 'fas fa-circle',
                ];
            }
        }

        $totalEntries = count($entries);
        $perPage = config('log-tracker.log_per_page', 50);
        // Pass already-parsed entries so the file is not read a second time.
        $frequentEntries = $this->buildFrequentEntries($logData['entries'], $logConfig);

        return $this->themedView('log-details', compact('logFiles', 'logName', 'entries', 'counts', 'logLevels', 'totalEntries', 'perPage', 'frequentEntries'));
    }

    public function download(string $logName): BinaryFileResponse
    {
        if (! config('log-tracker.allow_download', true)) {
            abort(403, 'Log download is disabled.');
        }

        $logName = basename($logName);
        $logPath = storage_path("logs/{$logName}");

        if (! File::exists($logPath)) {
            abort(404, 'Log file not found.');
        }

        return response()->download($logPath);
    }

    public function delete(string $logName): RedirectResponse
    {
        $logName = basename($logName);

        if (! config('log-tracker.allow_delete', false)) {
            abort(403, 'Log deletion is disabled.');
        }

        $logFilePath = storage_path("logs/{$logName}");

        if (! File::exists($logFilePath)) {
            abort(404, 'Log file not found.');
        }

        if (! File::delete($logFilePath)) {
            abort(500, 'Failed to delete log file.');
        }

        return redirect()
            ->route('log-tracker.index')
            ->with('success', 'Log file has been deleted.');
    }

    public function clear(string $logName): RedirectResponse
    {
        if (! config('log-tracker.allow_delete', false)) {
            return redirect()->back()->with('error', 'Log clearing is disabled. Enable it in log-tracker.allow_delete configuration.');
        }

        $logName = basename($logName);
        $logFilePath = storage_path("logs/{$logName}");

        if (! File::exists($logFilePath)) {
            return redirect()->back()->with('error', 'Log file not found.');
        }

        try {
            File::put($logFilePath, '');

            return redirect()->back()->with('success', "Log file '{$logName}' has been cleared successfully.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to clear log file: '.$e->getMessage());
        }
    }

    /**
     * Format a log file name for display.
     */
    private function formatFileName(string $logFile): string
    {
        if (preg_match('/laravel-(\d{4})-(\d{2})-(\d{2})\.log/', $logFile, $matches)) {
            return Carbon::createFromDate($matches[1], $matches[2], $matches[3])->format('d F Y');
        }

        return $logFile;
    }

    /**
     * Format a log file's size for display.
     */
    private function formatFileSize(string $logFile): string
    {
        $filePath = storage_path("logs/{$logFile}");

        if (! File::exists($filePath)) {
            return '0 KB';
        }

        $bytes = File::size($filePath);

        return $bytes < 102400
            ? round($bytes / 1024, 2).' KB'
            : round($bytes / 1048576, 2).' MB';
    }

    /**
     * Count log entries by level using a fast regex scan.
     *
     * Reads the raw file content and counts header-line level tokens with
     * preg_match_all — avoiding a full parse + entry struct allocation.
     *
     * @return array<string, int|string>
     */
    private function countFileLevels(string $logFile): array
    {
        $logPath = storage_path("logs/{$logFile}");

        $primaryLevels = ['critical', 'error'];
        $levelNames = array_merge(['total'], LogParserService::logLevelNames());
        $result = array_fill_keys($levelNames, 0);
        $result['other'] = 0;

        if (! File::exists($logPath)) {
            return array_merge($result, ['file_error' => 'Log file not found']);
        }

        $maxFileSizeMB = config('log-tracker.max_file_size', 50);
        $fileSizeMB = File::size($logPath) / 1048576;

        if ($fileSizeMB > $maxFileSizeMB) {
            return array_merge($result, ['file_error' => 'File too large']);
        }

        $content = File::get($logPath);

        // Match only the level token from each log header line, e.g. "local.error:"
        preg_match_all('/\]\s\w+\.(\w+):\s/', $content, $matches);

        foreach ($matches[1] as $raw) {
            $level = strtolower($raw);
            $result['total']++;
            if (isset($result[$level])) {
                $result[$level]++;
            }
            if (! in_array($level, $primaryLevels, true)) {
                $result['other']++;
            }
        }

        return $result;
    }

    /**
     * Build the top repeated log entries, enriched with colour/icon from config.
     * Accepts already-parsed entries to avoid reading the log file a second time.
     *
     * @param  array<int, array<string, mixed>>  $rawEntries  Pre-parsed entries from getAllLogEntries()
     * @param  array<string, mixed>  $logConfig
     * @return array<int, array<string, mixed>>
     */
    private function buildFrequentEntries(array $rawEntries, array $logConfig): array
    {
        $parser = app(LogParserService::class);
        $raw = $parser->calculateFrequency($rawEntries);

        return array_map(function (array $item) use ($logConfig): array {
            $level = $item['level'];

            return array_merge($item, [
                'first_seen' => Carbon::parse($item['first_seen'])->format('j M Y, h:i A'),
                'last_seen' => Carbon::parse($item['last_seen'])->format('j M Y, h:i A'),
                'color' => $logConfig[$level]['color'] ?? '#6c757d',
                'icon' => $logConfig[$level]['icon'] ?? 'fas fa-circle',
            ]);
        }, $raw);
    }
}
