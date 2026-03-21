<?php

namespace Kssadi\LogTracker\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Kssadi\LogTracker\Facades\LogTracker;
use Kssadi\LogTracker\Services\LogExportService;
use Kssadi\LogTracker\Traits\HasThemeSupport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    use HasThemeSupport;

    public function __construct(private readonly LogExportService $exportService) {}

    public function form(): View
    {
        $logFiles = LogTracker::getLogFiles();
        rsort($logFiles);

        return $this->themedView('export', compact('logFiles'));
    }

    public function export(Request $request): RedirectResponse|Response|BinaryFileResponse
    {
        if (! config('log-tracker.export.enabled', true)) {
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
            'search' => 'nullable|string|max:255',
        ]);

        $format = $request->input('format');

        if (! config("log-tracker.export.formats.{$format}.enabled", true)) {
            return back()->with('error', "The '{$format}' export format is not enabled.");
        }

        $logFiles = $request->input('log_files', []);

        if (empty($logFiles)) {
            $logFiles = LogTracker::getLogFiles();
        }

        $filters = array_filter([
            'levels' => $request->input('levels'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'search' => $request->input('search'),
        ]);

        try {
            return $this->streamExport($format, $logFiles, $filters);
        } catch (\Throwable $e) {
            Log::error('Export failed: '.$e->getMessage());

            return back()->with('error', 'Export failed: '.$e->getMessage());
        }
    }

    public function quickExport(string $logName, string $format = 'csv'): RedirectResponse|Response|BinaryFileResponse
    {
        $logName = basename($logName);

        if (! in_array($format, ['csv', 'json', 'excel', 'pdf'])) {
            $format = 'csv';
        }

        if (! config("log-tracker.export.formats.{$format}.enabled", true)) {
            return redirect()->route('log-tracker.show', $logName)
                ->with('error', "The '{$format}' export format is not enabled.");
        }

        try {
            return $this->streamExport($format, [$logName], []);
        } catch (\Throwable $e) {
            return redirect()->route('log-tracker.show', $logName)
                ->with('error', 'Export failed: '.$e->getMessage());
        }
    }

    private function streamExport(string $format, array $logFiles, array $filters): RedirectResponse|Response|BinaryFileResponse
    {
        if ($format === 'pdf') {
            $filePath = $this->exportService->exportToPdf($logFiles, $filters);
            $response = response(File::get($filePath), 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'inline; filename="'.str_replace('.html', '.pdf', basename($filePath)).'"',
            ]);
            File::delete($filePath);

            return $response;
        }

        [$filePath, $mimeType] = match ($format) {
            'csv' => [$this->exportService->exportToCsv($logFiles, $filters), 'text/csv'],
            'json' => [$this->exportService->exportToJson($logFiles, $filters), 'application/json'],
            'excel' => [$this->exportService->exportToExcel($logFiles, $filters), 'application/vnd.ms-excel'],
            default => abort(422, 'Unsupported export format.'),
        };

        return response()->download($filePath, basename($filePath), [
            'Content-Type' => $mimeType,
        ])->deleteFileAfterSend(true);
    }
}
