<?php

namespace Kssadi\LogTracker\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class LogExportService
{
    protected $logParserService;

    public function __construct(LogParserService $logParserService)
    {
        $this->logParserService = $logParserService;
    }

    /**
     * Export logs to CSV format
     */
    public function exportToCsv($logFiles, $filters = [])
    {
        $data = $this->prepareExportData($logFiles, $filters);

        $filename = 'logs_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!File::exists(dirname($filepath))) {
            File::makeDirectory(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // Add CSV headers with BOM for Excel compatibility
        fwrite($file, "\xEF\xBB\xBF");
        fputcsv($file, ['Timestamp', 'Level', 'Message', 'File', 'Stack Trace']);

        foreach ($data as $entry) {
            fputcsv($file, [
                $entry['timestamp'],
                $entry['level'],
                $this->cleanTextForCsv($entry['message']),
                $entry['file'],
                $this->cleanTextForCsv($entry['stack'] ?? '')
            ]);
        }

        fclose($file);

        return $filepath;
    }

    /**
     * Export logs to JSON format
     */
    public function exportToJson($logFiles, $filters = [])
    {
        $data = $this->prepareExportData($logFiles, $filters);

        $filename = 'logs_export_' . date('Y-m-d_H-i-s') . '.json';
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!File::exists(dirname($filepath))) {
            File::makeDirectory(dirname($filepath), 0755, true);
        }

        $exportData = [
            'export_info' => [
                'generated_at' => Carbon::now()->toISOString(),
                'total_entries' => count($data),
                'filters_applied' => $filters,
                'exported_by' => auth()->user()->name ?? 'System',
                'version' => '1.5.0'
            ],
            'logs' => $data
        ];

        File::put($filepath, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $filepath;
    }

    /**
     * Export logs to Excel XML format (Native Excel without dependencies)
     */
    public function exportToExcel($logFiles, $filters = [])
    {
        $data = $this->prepareExportData($logFiles, $filters);

        $filename = 'logs_export_' . date('Y-m-d_H-i-s') . '.xls';
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!File::exists(dirname($filepath))) {
            File::makeDirectory(dirname($filepath), 0755, true);
        }

        $excel = $this->generateExcelXml($data, $filters);
        File::put($filepath, $excel);

        return $filepath;
    }

    /**
     * Generate PDF-style HTML report (Print-ready)
     */
    public function exportToPdf($logFiles, $filters = [])
    {
        $data = $this->prepareExportData($logFiles, $filters);
        $summary = $this->generateSummary($data);

        $filename = 'logs_export_' . date('Y-m-d_H-i-s') . '.html';
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!File::exists(dirname($filepath))) {
            File::makeDirectory(dirname($filepath), 0755, true);
        }

        $html = $this->generatePrintableHtml($data, $summary, $filters);
        File::put($filepath, $html);

        return $filepath;
    }

    /**
     * Generate Excel XML format using native PHP
     */
    protected function generateExcelXml($data, $filters)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

        // Styles
        $xml .= '<Styles>' . "\n";
        $xml .= '<Style ss:ID="Header">' . "\n";
        $xml .= '<Font ss:Bold="1"/>' . "\n";
        $xml .= '<Interior ss:Color="#D3D3D3" ss:Pattern="Solid"/>' . "\n";
        $xml .= '</Style>' . "\n";
        $xml .= '<Style ss:ID="Error"><Font ss:Color="#FF0000"/></Style>' . "\n";
        $xml .= '<Style ss:ID="Warning"><Font ss:Color="#FFA500"/></Style>' . "\n";
        $xml .= '</Styles>' . "\n";

        // Worksheet
        $xml .= '<Worksheet ss:Name="Log Export">' . "\n";
        $xml .= '<Table>' . "\n";

        // Headers
        $xml .= '<Row>' . "\n";
        $xml .= '<Cell ss:StyleID="Header"><Data ss:Type="String">Timestamp</Data></Cell>' . "\n";
        $xml .= '<Cell ss:StyleID="Header"><Data ss:Type="String">Level</Data></Cell>' . "\n";
        $xml .= '<Cell ss:StyleID="Header"><Data ss:Type="String">Message</Data></Cell>' . "\n";
        $xml .= '<Cell ss:StyleID="Header"><Data ss:Type="String">File</Data></Cell>' . "\n";
        $xml .= '</Row>' . "\n";

        // Data rows
        foreach ($data as $entry) {
            $style = '';
            if ($entry['level'] === 'error') {
                $style = ' ss:StyleID="Error"';
            } elseif ($entry['level'] === 'warning') {
                $style = ' ss:StyleID="Warning"';
            }

            $xml .= '<Row>' . "\n";
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($entry['timestamp']) . '</Data></Cell>' . "\n";
            $xml .= '<Cell' . $style . '><Data ss:Type="String">' . htmlspecialchars(strtoupper($entry['level'])) . '</Data></Cell>' . "\n";
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($this->truncateText($entry['message'], 500)) . '</Data></Cell>' . "\n";
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($entry['file']) . '</Data></Cell>' . "\n";
            $xml .= '</Row>' . "\n";
        }

        $xml .= '</Table></Worksheet></Workbook>';
        return $xml;
    }

    /**
     * Generate print-ready HTML
     */
    protected function generatePrintableHtml($data, $summary, $filters)
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laravel Log Export Report</title>
    <style>
        @page { margin: 1in; size: A4; }
        @media print { .no-print { display: none !important; } }
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #007bff; padding-bottom: 20px; }
        .header h1 { color: #007bff; margin: 0; font-size: 28px; }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .summary-card { background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; text-align: center; }
        .summary-card h3 { margin: 0 0 10px 0; font-size: 24px; color: #007bff; }
        .log-table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 12px; }
        .log-table th, .log-table td { border: 1px solid #dee2e6; padding: 8px; text-align: left; }
        .log-table th { background: #f8f9fa; font-weight: 600; }
        .level-error { color: #dc3545; font-weight: bold; }
        .level-warning { color: #ffc107; font-weight: bold; }
        .level-info { color: #0d6efd; font-weight: bold; }
        .print-controls { position: fixed; top: 20px; right: 20px; z-index: 1000; }
        .btn { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 0 5px; }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button class="btn" onclick="window.print()">🖨️ Print to PDF</button>
        <button class="btn" onclick="history.back()">✕ Back</button>
    </div>

    <div class="header">
        <h1>📊 Laravel Log Export Report</h1>
        <p>Generated on ' . Carbon::now()->format('F j, Y \a\t g:i A') . '</p>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <h3>' . number_format(count($data)) . '</h3>
            <p>Total Log Entries</p>
        </div>' .
            (isset($summary['levels']) ?
                implode('', array_map(function($level, $count) {
                    return '<div class="summary-card"><h3 class="level-' . $level . '">' . number_format($count) . '</h3><p>' . ucfirst($level) . ' Logs</p></div>';
                }, array_keys($summary['levels']), $summary['levels'])) : ''
            ) .
            '</div>

    <h2>📝 Log Entries</h2>
    <table class="log-table">
        <thead>
            <tr><th>Timestamp</th><th>Level</th><th>Message</th><th>File</th></tr>
        </thead>
        <tbody>' .
            implode('', array_map(function($entry) {
                return '<tr>
                <td>' . htmlspecialchars($entry['timestamp']) . '</td>
                <td class="level-' . $entry['level'] . '">' . strtoupper($entry['level']) . '</td>
                <td>' . htmlspecialchars($this->truncateText($entry['message'], 200)) . '</td>
                <td>' . htmlspecialchars($entry['file']) . '</td>
            </tr>';
            }, array_slice($data, 0, 1000))) .
            '</tbody>
    </table>

    <script>
        if (window.location.hash === "#print") { window.print(); }
        window.addEventListener("afterprint", function() { if (window.opener) { window.close(); } });
    </script>
</body>
</html>';
    }

    /**
     * Prepare export data with filtering
     */
    protected function prepareExportData($logFiles, $filters = [])
    {
        $allEntries = collect();

        if (is_string($logFiles)) {
            $logFiles = [$logFiles];
        }

        foreach ($logFiles as $logFile) {
            $logData = $this->logParserService->getLogEntries($logFile);

            foreach ($logData['entries'] as $entry) {
                $entry['file'] = $logFile;
                $allEntries->push($entry);
            }
        }

        // Apply filters
        if (!empty($filters['levels'])) {
            $allEntries = $allEntries->whereIn('level', $filters['levels']);
        }

        if (!empty($filters['date_from'])) {
            $allEntries = $allEntries->filter(function ($entry) use ($filters) {
                $entryDate = Carbon::parse($entry['timestamp'])->format('Y-m-d');
                return $entryDate >= $filters['date_from'];
            });
        }

        if (!empty($filters['date_to'])) {
            $allEntries = $allEntries->filter(function ($entry) use ($filters) {
                $entryDate = Carbon::parse($entry['timestamp'])->format('Y-m-d');
                return $entryDate <= $filters['date_to'];
            });
        }

        if (!empty($filters['search'])) {
            $allEntries = $allEntries->filter(function ($entry) use ($filters) {
                return stripos($entry['message'], $filters['search']) !== false;
            });
        }

        return $allEntries->sortByDesc('timestamp')->values()->toArray();
    }

    /**
     * Generate summary statistics
     */
    protected function generateSummary($data)
    {
        $summary = [
            'total_logs' => count($data),
            'levels' => [],
            'date_range' => []
        ];

        foreach ($data as $entry) {
            $level = $entry['level'];
            if (!isset($summary['levels'][$level])) {
                $summary['levels'][$level] = 0;
            }
            $summary['levels'][$level]++;
        }

        if (!empty($data)) {
            $timestamps = array_column($data, 'timestamp');
            $summary['date_range'] = [
                'from' => min($timestamps),
                'to' => max($timestamps)
            ];
        }

        return $summary;
    }

    /**
     * Clean text for CSV export
     */
    protected function cleanTextForCsv($text)
    {
        $text = str_replace(["\r\n", "\r", "\n"], " | ", $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Truncate text to specified length
     */
    protected function truncateText($text, $length = 100)
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length - 3) . '...';
    }

    /**
     * Clean up old export files
     */
    public function cleanupOldExports($daysOld = 7)
    {
        $exportPath = storage_path('app/exports');

        if (!File::exists($exportPath)) {
            return;
        }

        $files = File::files($exportPath);
        $cutoffTime = Carbon::now()->subDays($daysOld)->timestamp;

        foreach ($files as $file) {
            if ($file->getMTime() < $cutoffTime) {
                File::delete($file->getPathname());
            }
        }
    }
}
