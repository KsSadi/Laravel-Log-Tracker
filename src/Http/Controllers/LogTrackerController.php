<?php

namespace Kssadi\LogTracker\Http\Controllers;

use Illuminate\Routing\Controller;
use Kssadi\LogTracker\Facades\LogTracker;
use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Services\LogExportService;
use Kssadi\LogTracker\Traits\HasThemeSupport;
use Illuminate\Http\Request;

class LogTrackerController extends Controller
{
    use HasThemeSupport;

    public function __construct()
    {
        $this->initializeTheme();
    }

    public function dashboard()
    {
        $logFiles = LogTracker::getLogFiles();
        $summary = ['total' => 0, 'error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0];

        $dates = [];
        // Initialize log types count with all configured levels
        $configuredLevels = array_keys(config('log-tracker.log_levels', []));
        $logTypesCount = [];
        foreach ($configuredLevels as $level) {
            if ($level !== 'total') { // Exclude 'total' as it's not a real log level
                $logTypesCount[$level] = 0;
            }
        }
        
        $recentLogs = []; // Store all log entries
        $errorCounts = []; // Store error types and their counts
        $peakHours = array_fill(0, 24, 0); // Track error count per hour

        // Generate last 7 days' dates with dynamic levels
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[$date] = [];
            foreach ($configuredLevels as $level) {
                if ($level !== 'total') {
                    $dates[$date][$level] = 0;
                }
            }
        }

        // Today's total log count
        $today = now()->format('Y-m-d');
        $newLogsToday = ['error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0];
        $todaysTotalLogs = 0;
        $logStyles = [];

        foreach (['error', 'warning', 'info', 'debug', 'total'] as $level) {
            $config = config("log-tracker.log_levels.{$level}", ['color' => '#6c757d', 'icon' => 'fas fa-circle']);
            $logStyles[$level] = [
                'color' => $config['color'],
                'icon'  => $config['icon']
            ];
        }

        foreach ($logFiles as $logFile) {
            $logData = LogTracker::getAllLogEntries($logFile);

            foreach ($logData['entries'] as $entry) {
                $summary['total']++;

                // 🔹 **Store logs into `$recentLogs`**
                $recentLogs[] = [
                    'timestamp' => $entry['timestamp'],
                    'level' => $entry['level'],
                    'message' => $entry['message']
                ];

                // 🔹 **Count all-time logs**
                if (isset($logTypesCount[$entry['level']])) {
                    $logTypesCount[$entry['level']]++;
                }

                // 🔹 **Extract error types and count them**
                if ($entry['level'] === 'error') {
                    $errorType = explode(":", $entry['message'])[0]; // Extract first part of the message

                    if (!isset($errorCounts[$errorType])) {
                        $errorCounts[$errorType] = 1;
                    } else {
                        $errorCounts[$errorType]++;
                    }
                }

                // 🔹 **Extract date from timestamp**
                if (isset($entry['timestamp']) && preg_match('/(\d{4}-\d{2}-\d{2})/', $entry['timestamp'], $match)) {
                    $logDate = $match[1];

                    // Count logs for last 7 days with dynamic level support
                    if (isset($dates[$logDate])) {
                        // Ensure the level exists in this date, initialize if not
                        if (!isset($dates[$logDate][$entry['level']])) {
                            $dates[$logDate][$entry['level']] = 0;
                        }
                        $dates[$logDate][$entry['level']]++;
                    }

                    // Count today's logs with dynamic level support  
                    if ($logDate === $today) {
                        $todaysTotalLogs++;
                        if (!isset($newLogsToday[$entry['level']])) {
                            $newLogsToday[$entry['level']] = 0;
                        }
                        $newLogsToday[$entry['level']]++;
                    }
                }

                // 🔹 **Extract peak error hours**
                if ($entry['level'] === 'error' && isset($entry['timestamp']) && preg_match('/(\d{2}):\d{2}:\d{2}/', $entry['timestamp'], $match)) {
                    $hour = (int)$match[1]; // Extract hour from timestamp
                    $peakHours[$hour]++; // Increment error count for that hour
                }
            }
        }

        // 🔹 **Sort error types and get the top 5**
        arsort($errorCounts);
        $topErrors = array_slice($errorCounts, 0, 5, true);

        // 🔹 **Sort peak error hours and get the top 5**
        arsort($peakHours);
        $topPeakHours = array_slice($peakHours, 0, 5, true);

        // 🔹 **Ensure `$recentLogs` is sorted by timestamp**
        usort($recentLogs, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        // Get only the last 5 logs
        $lastFiveLogs = array_slice($recentLogs, 0, 5);

        return $this->themedView('dashboard', compact(
            'summary',
            'dates',
            'logTypesCount',
            'newLogsToday',
            'todaysTotalLogs',
            'lastFiveLogs',
            'topErrors',
            'topPeakHours',
            'logStyles'
        ));
    }

    public function dashboardRefresh()
    {
        try {
            $logFiles = LogTracker::getLogFiles();
            $summary = ['total' => 0, 'error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0];

            $dates = [];
            // Initialize log types count with all configured levels
            $configuredLevels = array_keys(config('log-tracker.log_levels', []));
            $logTypesCount = [];
            foreach ($configuredLevels as $level) {
                if ($level !== 'total') { // Exclude 'total' as it's not a real log level
                    $logTypesCount[$level] = 0;
                }
            }
            
            $recentLogs = []; // Store all log entries
            $errorCounts = []; // Store error types and their counts
            $peakHours = array_fill(0, 24, 0); // Track error count per hour

            // Generate last 7 days' dates with dynamic levels
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dates[$date] = [];
                foreach ($configuredLevels as $level) {
                    if ($level !== 'total') {
                        $dates[$date][$level] = 0;
                    }
                }
            }

            // Today's total log count
            $today = now()->format('Y-m-d');
            $newLogsToday = ['error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0];
            $todaysTotalLogs = 0;

            foreach ($logFiles as $logFile) {
                $logData = LogTracker::getAllLogEntries($logFile);

                foreach ($logData['entries'] as $entry) {
                    $summary['total']++;

                    // Store logs into $recentLogs
                    $recentLogs[] = [
                        'timestamp' => $entry['timestamp'],
                        'level' => $entry['level'],
                        'message' => $entry['message']
                    ];

                    // Count all-time logs
                    if (isset($logTypesCount[$entry['level']])) {
                        $logTypesCount[$entry['level']]++;
                    }

                    // Extract error types and count them
                    if ($entry['level'] === 'error') {
                        $errorType = explode(":", $entry['message'])[0]; // Extract first part of the message

                        if (!isset($errorCounts[$errorType])) {
                            $errorCounts[$errorType] = 1;
                        } else {
                            $errorCounts[$errorType]++;
                        }
                    }

                    // Extract date from timestamp
                    if (isset($entry['timestamp']) && preg_match('/(\d{4}-\d{2}-\d{2})/', $entry['timestamp'], $match)) {
                        $logDate = $match[1];

                        // Count logs for last 7 days with dynamic level support
                        if (isset($dates[$logDate])) {
                            // Ensure the level exists in this date, initialize if not
                            if (!isset($dates[$logDate][$entry['level']])) {
                                $dates[$logDate][$entry['level']] = 0;
                            }
                            $dates[$logDate][$entry['level']]++;
                        }

                        // Count today's logs with dynamic level support
                        if ($logDate === $today) {
                            $todaysTotalLogs++;
                            if (!isset($newLogsToday[$entry['level']])) {
                                $newLogsToday[$entry['level']] = 0;
                            }
                            $newLogsToday[$entry['level']]++;
                        }
                    }

                    // Extract peak error hours
                    if ($entry['level'] === 'error' && isset($entry['timestamp']) && preg_match('/(\d{2}):\d{2}:\d{2}/', $entry['timestamp'], $match)) {
                        $hour = (int)$match[1]; // Extract hour from timestamp
                        $peakHours[$hour]++; // Increment error count for that hour
                    }
                }
            }

            // Sort error types and get the top 5
            arsort($errorCounts);
            $topErrors = array_slice($errorCounts, 0, 5, true);

            // Sort peak error hours and get the top 5
            arsort($peakHours);
            $topPeakHours = array_slice($peakHours, 0, 5, true);

            // Ensure $recentLogs is sorted by timestamp
            usort($recentLogs, function ($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });

            // Get only the last 5 logs
            $lastFiveLogs = array_slice($recentLogs, 0, 5);

            // Return JSON response for AJAX
            return response()->json([
                'success' => true,
                'summary' => $summary,
                'dates' => $dates,
                'logTypesCount' => $logTypesCount,
                'newLogsToday' => $newLogsToday,
                'todaysTotalLogs' => $todaysTotalLogs,
                'lastFiveLogs' => $lastFiveLogs,
                'topErrors' => $topErrors,
                'topPeakHours' => $topPeakHours,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch dashboard data',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function index()
    {
        $logFiles = LogTracker::getLogFiles();
        rsort($logFiles); // Reverse sorting (latest logs first)

        $totalFiles = count($logFiles);
        $logFilesPerPage = config('log-tracker.log_files_per_page', 10);
        $page = request()->get('page', 1);
        $lastPage = max(ceil($totalFiles / $logFilesPerPage), 1);
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $logFilesPerPage;
        $paginatedFiles = array_slice($logFiles, $offset, $logFilesPerPage);

        $counts = [];
        $fileSizes = [];
        $formattedFileNames = [];
        $logLevels = config('log-tracker.log_levels');

        foreach ($paginatedFiles as $logFile) {
            $filePath = storage_path("logs/{$logFile}");
            if (preg_match('/laravel-(\d{4})-(\d{2})-(\d{2})\.log/', $logFile, $matches)) {
                $formattedFileNames[$logFile] = date('d F Y', strtotime("{$matches[1]}-{$matches[2]}-{$matches[3]}"));
            } else {
                $formattedFileNames[$logFile] = $logFile;
            }
            if (file_exists($filePath)) {
                $sizeInBytes = filesize($filePath);
                if ($sizeInBytes < 102400) {
                    $fileSizes[$logFile] = round($sizeInBytes / 1024, 2) . ' KB';
                } else {
                    $fileSizes[$logFile] = round($sizeInBytes / 1048576, 2) . ' MB';
                }
            } else {
                $fileSizes[$logFile] = '0 KB';
            }
            $logData = LogTracker::getAllLogEntries($logFile);
            
            // Check if file is too large or has error
            if (isset($logData['error'])) {
                $counts[$logFile] = [
                    'total' => 0,
                    'error' => 0,
                    'warning' => 0,
                    'info' => 0,
                    'file_error' => $logData['error'],
                ];
            } else {
                // Initialize all level counts
                $levelCounts = [
                    'total' => $logData['total'],
                    'emergency' => 0,
                    'alert' => 0,
                    'critical' => 0,
                    'error' => 0,
                    'warning' => 0,
                    'notice' => 0,
                    'info' => 0,
                    'debug' => 0
                ];
                
                // Count each level
                foreach ($logData['entries'] as $entry) {
                    $level = strtolower($entry['level']);
                    if (isset($levelCounts[$level])) {
                        $levelCounts[$level]++;
                    }
                }
                
                // Calculate "other" levels (everything except critical and error)
                $otherCount = $levelCounts['emergency'] + $levelCounts['alert'] + 
                             $levelCounts['warning'] + $levelCounts['notice'] + 
                             $levelCounts['info'] + $levelCounts['debug'];
                
                $counts[$logFile] = [
                    'total' => $levelCounts['total'],
                    'critical' => $levelCounts['critical'],
                    'error' => $levelCounts['error'],
                    'other' => $otherCount,
                    // Detailed counts for hover tooltips
                    'emergency' => $levelCounts['emergency'],
                    'alert' => $levelCounts['alert'],
                    'warning' => $levelCounts['warning'],
                    'notice' => $levelCounts['notice'],
                    'info' => $levelCounts['info'],
                    'debug' => $levelCounts['debug']
                ];
            }
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



    public function show($logName, Request $request)
    {
        $logFiles = LogTracker::getLogFiles();
        // Load ALL entries for client-side pagination and filtering
        $logData = LogTracker::getAllLogEntries($logName);
        $logConfig = config('log-tracker.log_levels', []);

        // Check if there's an error (file too large or not found)
        if (isset($logData['error'])) {
            return $this->themedView('log-details', [
                'logFiles' => $logFiles,
                'logName' => $logName,
                'entries' => [],
                'counts' => ['total' => 0, 'error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0],
                'logLevels' => [],
                'totalEntries' => 0,
                'perPage' => config('log-tracker.log_per_page', 50),
                'error' => $logData['error'],
                'file_size_mb' => isset($logData['file_size_mb']) ? $logData['file_size_mb'] : null,
                'max_size_mb' => isset($logData['max_size_mb']) ? $logData['max_size_mb'] : null,
            ]);
        }

        // Ensure $entries is defined and format timestamps
        $entries = collect(isset($logData['entries']) ? $logData['entries'] : [])->map(function ($entry) use ($logConfig) {
            $level = strtolower($entry['level']);

            return [
                'timestamp' => \Carbon\Carbon::parse($entry['timestamp'])->format('j M Y, h:i:s A'),
                'level' => $level,
                'message' => $entry['message'],
                'stack' => isset($entry['stack']) ? $entry['stack'] : '',
                'color' => isset($logConfig[$level]['color']) ? $logConfig[$level]['color'] : '#6c757d', // Default gray if not found
                'icon' => isset($logConfig[$level]['icon']) ? $logConfig[$level]['icon'] : 'fas fa-circle', // Default icon if not found
            ];
        })->toArray();

        // Count log levels for current page - get all configured levels
        $counts = ['total' => $logData['total']];
        
        // Initialize counts for all configured log levels
        foreach ($logConfig as $level => $config) {
            if ($level !== 'total') { // Skip 'total' as it's not a real log level
                $counts[$level] = count(array_filter($entries, function ($entry) use ($level) {
                    return $entry['level'] === $level;
                }));
            }
        }

        // Pass only log level configurations that have entries (count > 0) to the view
        $logLevels = [];
        foreach ($logConfig as $level => $config) {
            if ($level !== 'total' && isset($counts[$level]) && $counts[$level] > 0) {
                $logLevels[$level] = [
                    'color' => isset($config['color']) ? $config['color'] : '#6c757d',
                    'icon' => isset($config['icon']) ? $config['icon'] : 'fas fa-circle',
                ];
            }
        }

        // Client-side pagination data
        $totalEntries = count($entries);
        $perPage = config('log-tracker.log_per_page', 50);

        return $this->themedView('log-details', compact('logFiles', 'logName', 'entries', 'counts', 'logLevels', 'totalEntries', 'perPage'));
    }

    public function download($logName)
    {
        $logPath = storage_path("logs/{$logName}");

        if (!File::exists($logPath)) {
            return redirect()->route('log-tracker.index')->with('error', 'Log file not found.');
        }

        return response()->download($logPath);
    }


    public function delete($logName)
    {
        if (!config('log-tracker.allow_delete', false)) {
            abort(403, 'Log deletion is disabled.');
        }

        // Ensure the log file exists in the storage/logs directory
        $logFilePath = storage_path("logs/{$logName}");
        if (!File::exists($logFilePath)) {
            abort(404, 'Log file not found.');
        }

         unlink($logFilePath);


        return redirect()
            ->route('log-tracker.index')
            ->with('success', 'Log file has been cleared.');
    }

    /**
     * Show export form
     */
    public function exportForm()
    {
        $logFiles = LogTracker::getLogFiles();
        rsort($logFiles);

        return $this->themedView('export', compact('logFiles'));
    }

    /**
     * Handle export request
     */
    public function export(Request $request)
    {
        if (!config('log-tracker.export.enabled', true)) {
            return back()->with('error', 'Export functionality is disabled.');
        }

        $request->validate([
            'format' => 'required|in:csv,json,excel,pdf',
            'log_files' => 'array',
            'log_files.*' => 'string',
            'levels' => 'array',
            'levels.*' => 'in:emergency,alert,critical,error,warning,notice,info,debug',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'search' => 'nullable|string|max:255'
        ]);

        $format = $request->input('format');
        $logFiles = $request->input('log_files', []);

        if (empty($logFiles)) {
            $logFiles = LogTracker::getLogFiles();
        }

        $filters = array_filter([
            'levels' => $request->input('levels'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'search' => $request->input('search')
        ]);

        $exportService = app(LogExportService::class);

        try {
            switch ($format) {
                case 'csv':
                    $filePath = $exportService->exportToCsv($logFiles, $filters);
                    $mimeType = 'text/csv';
                    break;
                case 'json':
                    $filePath = $exportService->exportToJson($logFiles, $filters);
                    $mimeType = 'application/json';
                    break;
                case 'excel':
                    $filePath = $exportService->exportToExcel($logFiles, $filters);
                    $mimeType = 'application/vnd.ms-excel';
                    break;
                case 'pdf':
                    $filePath = $exportService->exportToPdf($logFiles, $filters);
                    return response(file_get_contents($filePath), 200, [
                        'Content-Type' => 'text/html',
                        'Content-Disposition' => 'inline; filename="' . str_replace('.html', '.pdf', basename($filePath)) . '"'
                    ]);
            }

            return response()->download($filePath, basename($filePath), [
                'Content-Type' => $mimeType,
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Export failed: ' . $e->getMessage());
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Quick export for single log file
     */
    public function quickExport($logName, $format = 'csv')
    {
        if (!in_array($format, ['csv', 'json', 'excel', 'pdf'])) {
            $format = 'csv';
        }

        $exportService = app(LogExportService::class);

        try {
            switch ($format) {
                case 'csv':
                    $filePath = $exportService->exportToCsv([$logName]);
                    $mimeType = 'text/csv';
                    break;
                case 'json':
                    $filePath = $exportService->exportToJson([$logName]);
                    $mimeType = 'application/json';
                    break;
                case 'excel':
                    $filePath = $exportService->exportToExcel([$logName]);
                    $mimeType = 'application/vnd.ms-excel';
                    break;
                case 'pdf':
                    $filePath = $exportService->exportToPdf([$logName]);
                    return response(file_get_contents($filePath), 200, [
                        'Content-Type' => 'text/html'
                    ]);
            }

            return response()->download($filePath, basename($filePath), [
                'Content-Type' => $mimeType,
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->route('log-tracker.show', $logName)
                ->with('error', 'Export failed: ' . $e->getMessage());
        }
    }


}
