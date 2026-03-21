<?php

namespace Kssadi\LogTracker\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class LogExportService
{
    public function __construct(
        private readonly LogParserService $logParserService
    ) {}

    /**
     * Generate a timestamped export file path and ensure the directory exists.
     */
    private function generateExportPath(string $extension): string
    {
        $filename = 'logs_export_'.Carbon::now()->format('Y-m-d_H-i-s').'.'.$extension;
        $filepath = storage_path('app/exports/'.$filename);
        $this->ensureExportDirectory($filepath);

        return $filepath;
    }

    /**
     * Export logs to CSV format
     */
    public function exportToCsv(array|string $logFiles, array $filters = []): string
    {
        $data = $this->prepareExportData($logFiles, $filters);
        $filepath = $this->generateExportPath('csv');

        $file = fopen($filepath, 'w');

        try {
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Timestamp', 'Level', 'Message', 'File', 'Stack Trace']);

            foreach ($data as $entry) {
                fputcsv($file, [
                    $entry['timestamp'],
                    $entry['level'],
                    $this->cleanTextForCsv($entry['message']),
                    $entry['file'],
                    $this->cleanTextForCsv($entry['stack'] ?? ''),
                ]);
            }
        } finally {
            fclose($file);
        }

        return $filepath;
    }

    /**
     * Export logs to JSON format
     */
    public function exportToJson(array|string $logFiles, array $filters = []): string
    {
        $data = $this->prepareExportData($logFiles, $filters);
        $filepath = $this->generateExportPath('json');

        $exportData = [
            'export_info' => [
                'generated_at' => Carbon::now()->toISOString(),
                'total_entries' => count($data),
                'filters_applied' => $filters,
                'exported_by' => auth()->user()?->name ?? 'System',
            ],
            'logs' => $data,
        ];

        File::put($filepath, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $filepath;
    }

    /**
     * Export logs to Excel XML format (Native Excel without dependencies)
     */
    public function exportToExcel(array|string $logFiles, array $filters = []): string
    {
        $data = $this->prepareExportData($logFiles, $filters);
        $filepath = $this->generateExportPath('xls');

        $excel = $this->generateExcelXml($data);
        File::put($filepath, $excel);

        return $filepath;
    }

    /**
     * Generate PDF-style HTML report (Print-ready)
     */
    public function exportToPdf(array|string $logFiles, array $filters = []): string
    {
        $data = $this->prepareExportData($logFiles, $filters);
        $summary = $this->generateSummary($data);
        $filepath = $this->generateExportPath('html');

        $html = $this->generatePrintableHtml($data, $summary);
        File::put($filepath, $html);

        return $filepath;
    }

    /**
     * Generate Excel XML format using native PHP.
     */
    private function generateExcelXml(array $data): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'."\n"
            .' xmlns:o="urn:schemas-microsoft-com:office:office"'."\n"
            .' xmlns:x="urn:schemas-microsoft-com:office:excel"'."\n"
            .' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"'."\n"
            .' xmlns:html="http://www.w3.org/TR/REC-html40">'."\n"
            .'<Styles>'."\n"
            .'<Style ss:ID="Header"><Font ss:Bold="1"/><Interior ss:Color="#D3D3D3" ss:Pattern="Solid"/></Style>'."\n"
            .'<Style ss:ID="Error"><Font ss:Color="#FF0000"/></Style>'."\n"
            .'<Style ss:ID="Warning"><Font ss:Color="#FFA500"/></Style>'."\n"
            .'</Styles>'."\n"
            .'<Worksheet ss:Name="Log Export"><Table>'."\n"
            .'<Row>'
            .'<Cell ss:StyleID="Header"><Data ss:Type="String">Timestamp</Data></Cell>'
            .'<Cell ss:StyleID="Header"><Data ss:Type="String">Level</Data></Cell>'
            .'<Cell ss:StyleID="Header"><Data ss:Type="String">Message</Data></Cell>'
            .'<Cell ss:StyleID="Header"><Data ss:Type="String">File</Data></Cell>'
            .'</Row>'."\n";

        foreach ($data as $entry) {
            $style = match ($entry['level']) {
                'error' => ' ss:StyleID="Error"',
                'warning' => ' ss:StyleID="Warning"',
                default => '',
            };

            $xml .= '<Row>'
                .'<Cell><Data ss:Type="String">'.htmlspecialchars($entry['timestamp']).'</Data></Cell>'
                .'<Cell'.$style.'><Data ss:Type="String">'.htmlspecialchars(strtoupper($entry['level'])).'</Data></Cell>'
                .'<Cell><Data ss:Type="String">'.htmlspecialchars($this->truncateText($entry['message'], 500)).'</Data></Cell>'
                .'<Cell><Data ss:Type="String">'.htmlspecialchars($entry['file']).'</Data></Cell>'
                .'</Row>'."\n";
        }

        return $xml.'</Table></Worksheet></Workbook>';
    }

    /**
     * Generate print-ready HTML report.
     */
    private function generatePrintableHtml(array $data, array $summary): string
    {
        $date = Carbon::now()->format('F j, Y \a\t g:i A');
        $total = number_format(count($data));
        $levelCards = $this->buildLevelSummaryCards($summary);
        $tableRows = $this->buildLogTableRows(array_slice($data, 0, 1000));
        $css = $this->getExportCss();

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laravel Log Export Report</title>
            <style>{$css}</style>
        </head>
        <body>
            <div class="print-controls no-print">
                <button class="btn" onclick="window.print()">🖨️ Print to PDF</button>
                <button class="btn" onclick="history.back()">✕ Back</button>
            </div>
            <div class="header">
                <h1>📊 Laravel Log Export Report</h1>
                <p>Generated on {$date}</p>
            </div>
            <div class="summary-grid">
                <div class="summary-card"><h3>{$total}</h3><p>Total Log Entries</p></div>
                {$levelCards}
            </div>
            <h2>📝 Log Entries</h2>
            <table class="log-table">
                <thead><tr><th>Timestamp</th><th>Level</th><th>Message</th><th>File</th></tr></thead>
                <tbody>{$tableRows}</tbody>
            </table>
            <script>
                if (window.location.hash === "#print") { window.print(); }
                window.addEventListener("afterprint", function() { if (window.opener) { window.close(); } });
            </script>
        </body>
        </html>
        HTML;
    }

    /**
     * Get CSS styles for the export report.
     */
    private function getExportCss(): string
    {
        return <<<'CSS'
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
        CSS;
    }

    /**
     * Build HTML summary cards for each log level.
     */
    private function buildLevelSummaryCards(array $summary): string
    {
        if (empty($summary['levels'])) {
            return '';
        }

        $cards = '';
        foreach ($summary['levels'] as $level => $count) {
            $formatted = number_format($count);
            $label = ucfirst($level);
            $cards .= "<div class=\"summary-card\"><h3 class=\"level-{$level}\">{$formatted}</h3><p>{$label} Logs</p></div>";
        }

        return $cards;
    }

    /**
     * Build HTML table rows for log entries.
     */
    private function buildLogTableRows(array $data): string
    {
        $rows = '';
        foreach ($data as $entry) {
            $timestamp = htmlspecialchars($entry['timestamp']);
            $level = $entry['level'];
            $levelUpper = strtoupper($level);
            $message = htmlspecialchars($this->truncateText($entry['message'], 200));
            $file = htmlspecialchars($entry['file']);
            $rows .= "<tr><td>{$timestamp}</td><td class=\"level-{$level}\">{$levelUpper}</td><td>{$message}</td><td>{$file}</td></tr>";
        }

        return $rows;
    }

    /**
     * Prepare export data with filtering
     */
    private function prepareExportData(array|string $logFiles, array $filters = []): array
    {
        $allEntries = collect();

        if (is_string($logFiles)) {
            $logFiles = [$logFiles];
        }

        foreach ($logFiles as $logFile) {
            $logData = $this->logParserService->getAllLogEntries($logFile);

            foreach ($logData['entries'] as $entry) {
                $entry['file'] = $logFile;
                $allEntries->push($entry);
            }
        }

        if (! empty($filters['levels'])) {
            $allEntries = $allEntries->whereIn('level', $filters['levels']);
        }

        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        if ($dateFrom || $dateTo) {
            $allEntries = $allEntries->filter(function ($entry) use ($dateFrom, $dateTo) {
                $entryDate = Carbon::parse($entry['timestamp'])->format('Y-m-d');

                return (! $dateFrom || $entryDate >= $dateFrom)
                    && (! $dateTo || $entryDate <= $dateTo);
            });
        }

        if (! empty($filters['search'])) {
            $allEntries = $allEntries->filter(function ($entry) use ($filters) {
                return stripos($entry['message'], $filters['search']) !== false;
            });
        }

        $maxEntries = (int) config('log-tracker.export.limits.max_entries', 50000);

        return $allEntries->sortByDesc('timestamp')->take($maxEntries)->values()->toArray();
    }

    /**
     * Generate summary statistics
     */
    private function generateSummary(array $data): array
    {
        $collection = collect($data);
        $timestamps = $collection->pluck('timestamp')->filter();

        return [
            'total_logs' => $collection->count(),
            'levels' => $collection->countBy('level')->toArray(),
            'date_range' => $timestamps->isNotEmpty()
                ? ['from' => $timestamps->min(), 'to' => $timestamps->max()]
                : [],
        ];
    }

    /**
     * Clean text for CSV export
     */
    private function cleanTextForCsv(string $text): string
    {
        $text = str_replace(["\r\n", "\r", "\n"], ' | ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Truncate text to specified length
     */
    private function truncateText(string $text, int $length = 100): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length - 3).'...';
    }

    /**
     * Ensure the export directory exists, creating it if necessary.
     */
    private function ensureExportDirectory(string $filepath): void
    {
        if (! File::exists(dirname($filepath))) {
            File::makeDirectory(dirname($filepath), 0755, true);
        }
    }

    /**
     * Clean up old export files
     */
    public function cleanupOldExports(int $daysOld = 7): void
    {
        $exportPath = storage_path('app/exports');

        if (! File::exists($exportPath)) {
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
