<?php

namespace Kssadi\LogTracker\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LogParserService
{
    public function getLogFiles()
    {
        $logPath = storage_path('logs');
        $logFiles = File::files($logPath);

        return array_map(function ($file) {
            return $file->getFilename();
        }, $logFiles);
    }
    public function getLogEntries($logName, $page = 1, $perPage = null)
    {
        // Use config value if perPage is not provided
        if ($perPage === null) {
            $perPage = config('log-tracker.log_per_page', 50);
        }

        $logFile = storage_path("logs/{$logName}");

        if (!File::exists($logFile)) {
            return [
                'entries' => [],
                'total' => 0,
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => 1,
                'from' => 0,
                'to' => 0,
                'error' => 'Log file not found',
            ];
        }

        // Check file size limit
        $maxFileSizeMB = config('log-tracker.max_file_size', 50);
        $fileSizeBytes = filesize($logFile);
        $fileSizeMB = $fileSizeBytes / 1024 / 1024;

        if ($fileSizeMB > $maxFileSizeMB) {
            return [
                'entries' => [],
                'total' => 0,
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => 1,
                'from' => 0,
                'to' => 0,
                'error' => "File size (" . round($fileSizeMB, 2) . " MB) exceeds maximum allowed size ({$maxFileSizeMB} MB)",
                'file_size_mb' => $fileSizeMB,
                'max_size_mb' => $maxFileSizeMB,
            ];
        }

        $logContents = File::get($logFile);
        $logLines = explode("\n", trim($logContents));
        $logLines = array_reverse($logLines); // Show newest logs first

        $entries = [];
        $currentEntry = null;

        foreach ($logLines as $line) {
            // Match Laravel log format: [timestamp] environment.LEVEL: message
            if (preg_match('/\[(.*?)\]\s(\w+)\.(\w+):\s(.*)/', $line, $matches)) {
                // Save previous entry before starting a new one
                if ($currentEntry) {
                    $entries[] = $currentEntry;
                }

                // Start new log entry
                $currentEntry = [
                    'timestamp' => $matches[1],
                    'level' => strtolower($matches[3]), // Extract log level correctly
                    'message' => $matches[4],
                    'stack' => '', // Stack trace will be collected separately
                ];
            } elseif ($currentEntry) {
                // This is part of a stack trace, append it to the last entry
                $currentEntry['stack'] .= "\n" . $line;
            }
        }

        // Save the last log entry
        if ($currentEntry) {
            $entries[] = $currentEntry;
        }

        $total = count($entries);
        $lastPage = max(ceil($total / $perPage), 1);
        
        // Calculate pagination
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;
        $paginatedEntries = array_slice($entries, $offset, $perPage);
        
        $from = $total > 0 ? $offset + 1 : 0;
        $to = min($offset + $perPage, $total);

        return [
            'entries' => $paginatedEntries,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'from' => $from,
            'to' => $to,
        ];
    }

    /**
     * Get all log entries without pagination for overview/counting purposes
     */
    public function getAllLogEntries($logName)
    {
        $logFile = storage_path("logs/{$logName}");

        if (!File::exists($logFile)) {
            return [
                'entries' => [],
                'total' => 0,
                'error' => 'Log file not found',
            ];
        }

        // Check file size limit
        $maxFileSizeMB = config('log-tracker.max_file_size', 50);
        $fileSizeBytes = filesize($logFile);
        $fileSizeMB = $fileSizeBytes / 1024 / 1024;

        if ($fileSizeMB > $maxFileSizeMB) {
            return [
                'entries' => [],
                'total' => 0,
                'error' => "File size (" . round($fileSizeMB, 2) . " MB) exceeds maximum allowed size ({$maxFileSizeMB} MB)",
                'file_size_mb' => $fileSizeMB,
                'max_size_mb' => $maxFileSizeMB,
            ];
        }

        $logContents = File::get($logFile);
        $logLines = explode("\n", trim($logContents));
        $logLines = array_reverse($logLines); // Show newest logs first

        $entries = [];
        $currentEntry = null;

        foreach ($logLines as $line) {
            // Match Laravel log format: [timestamp] environment.LEVEL: message
            if (preg_match('/\[(.*?)\]\s(\w+)\.(\w+):\s(.*)/', $line, $matches)) {
                // Save previous entry before starting a new one
                if ($currentEntry) {
                    $entries[] = $currentEntry;
                }

                // Start new log entry
                $currentEntry = [
                    'timestamp' => $matches[1],
                    'level' => strtolower($matches[3]), // Extract log level correctly
                    'message' => $matches[4],
                    'stack' => '', // Stack trace will be collected separately
                ];
            } elseif ($currentEntry) {
                // This is part of a stack trace, append it to the last entry
                $currentEntry['stack'] .= "\n" . $line;
            }
        }

        // Save the last log entry
        if ($currentEntry) {
            $entries[] = $currentEntry;
        }

        return [
            'entries' => $entries,
            'total' => count($entries),
        ];
    }


}
