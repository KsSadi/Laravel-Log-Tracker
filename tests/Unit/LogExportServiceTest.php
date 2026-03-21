<?php

namespace Kssadi\LogTracker\Tests\Unit;

use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Services\LogExportService;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LogExportServiceTest extends TestCase
{
    private LogExportService $exportService;

    private string $logPath;

    private string $exportPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exportService = app(LogExportService::class);
        $this->logPath = storage_path('logs/test-export.log');
        $this->exportPath = storage_path('app/exports');

        $logContent = "[2025-06-01 10:00:00] local.ERROR: Something went wrong\n";
        $logContent .= "[2025-06-01 10:01:00] local.WARNING: Low disk space\n";
        $logContent .= "[2025-06-01 10:02:00] local.INFO: User logged in\n";

        File::put($this->logPath, $logContent);
    }

    protected function tearDown(): void
    {
        File::delete($this->logPath);

        if (File::exists($this->exportPath)) {
            File::cleanDirectory($this->exportPath);
        }

        parent::tearDown();
    }

    #[Test]
    public function it_exports_to_csv(): void
    {
        $filePath = $this->exportService->exportToCsv('test-export.log');

        $this->assertFileExists($filePath);
        $this->assertStringEndsWith('.csv', $filePath);

        $content = File::get($filePath);
        $this->assertStringContainsString('Timestamp', $content);
        $this->assertStringContainsString('Level', $content);
        $this->assertStringContainsString('Something went wrong', $content);
    }

    #[Test]
    public function it_exports_to_json(): void
    {
        $filePath = $this->exportService->exportToJson('test-export.log');

        $this->assertFileExists($filePath);
        $this->assertStringEndsWith('.json', $filePath);

        $data = json_decode(File::get($filePath), true);
        $this->assertArrayHasKey('export_info', $data);
        $this->assertArrayHasKey('logs', $data);
        $this->assertSame(3, $data['export_info']['total_entries']);
    }

    #[Test]
    public function it_exports_to_excel(): void
    {
        $filePath = $this->exportService->exportToExcel('test-export.log');

        $this->assertFileExists($filePath);
        $this->assertStringEndsWith('.xls', $filePath);

        $content = File::get($filePath);
        $this->assertStringContainsString('Workbook', $content);
        $this->assertStringContainsString('Something went wrong', $content);
    }

    #[Test]
    public function it_exports_to_pdf_html(): void
    {
        $filePath = $this->exportService->exportToPdf('test-export.log');

        $this->assertFileExists($filePath);
        $this->assertStringEndsWith('.html', $filePath);

        $content = File::get($filePath);
        $this->assertStringContainsString('<!DOCTYPE html>', $content);
        $this->assertStringContainsString('Laravel Log Export Report', $content);
        $this->assertStringContainsString('Something went wrong', $content);
    }

    #[Test]
    public function it_accepts_array_of_log_files(): void
    {
        $secondLog = storage_path('logs/test-export-2.log');
        File::put($secondLog, "[2025-06-02 11:00:00] local.DEBUG: Debug message\n");

        $filePath = $this->exportService->exportToJson(['test-export.log', 'test-export-2.log']);

        $data = json_decode(File::get($filePath), true);
        $this->assertSame(4, $data['export_info']['total_entries']);

        File::delete($secondLog);
    }

    #[Test]
    public function it_accepts_string_log_file(): void
    {
        $filePath = $this->exportService->exportToJson('test-export.log');

        $data = json_decode(File::get($filePath), true);
        $this->assertSame(3, $data['export_info']['total_entries']);
    }

    #[Test]
    public function it_filters_by_level(): void
    {
        $filePath = $this->exportService->exportToJson('test-export.log', ['levels' => ['error']]);

        $data = json_decode(File::get($filePath), true);
        $this->assertSame(1, $data['export_info']['total_entries']);
        $this->assertSame('error', $data['logs'][0]['level']);
    }

    #[Test]
    public function it_filters_by_search_keyword(): void
    {
        $filePath = $this->exportService->exportToJson('test-export.log', ['search' => 'disk space']);

        $data = json_decode(File::get($filePath), true);
        $this->assertSame(1, $data['export_info']['total_entries']);
        $this->assertSame('warning', $data['logs'][0]['level']);
    }

    #[Test]
    public function it_filters_by_date_range(): void
    {
        $filePath = $this->exportService->exportToJson('test-export.log', [
            'date_from' => '2025-06-01',
            'date_to' => '2025-06-01',
        ]);

        $data = json_decode(File::get($filePath), true);
        $this->assertSame(3, $data['export_info']['total_entries']);
    }

    #[Test]
    public function it_filters_out_entries_outside_date_range(): void
    {
        $filePath = $this->exportService->exportToJson('test-export.log', [
            'date_from' => '2025-07-01',
            'date_to' => '2025-07-01',
        ]);

        $data = json_decode(File::get($filePath), true);
        $this->assertSame(0, $data['export_info']['total_entries']);
    }

    #[Test]
    public function it_respects_max_entries_limit(): void
    {
        $this->app['config']->set('log-tracker.export.limits.max_entries', 2);

        $filePath = $this->exportService->exportToJson('test-export.log');

        $data = json_decode(File::get($filePath), true);
        $this->assertLessThanOrEqual(2, $data['export_info']['total_entries']);
    }

    #[Test]
    public function it_creates_export_directory_if_missing(): void
    {
        if (File::exists($this->exportPath)) {
            File::deleteDirectory($this->exportPath);
        }

        $filePath = $this->exportService->exportToCsv('test-export.log');

        $this->assertFileExists($filePath);
        $this->assertDirectoryExists($this->exportPath);
    }

    #[Test]
    public function it_cleans_up_old_exports(): void
    {
        if (! File::exists($this->exportPath)) {
            File::makeDirectory($this->exportPath, 0755, true);
        }

        $oldFile = $this->exportPath.'/old_file.csv';
        File::put($oldFile, 'data');
        touch($oldFile, time() - (86400 * 10));

        $recentFile = $this->exportPath.'/recent_file.csv';
        File::put($recentFile, 'data');

        $this->exportService->cleanupOldExports(7);

        $this->assertFileDoesNotExist($oldFile);
        $this->assertFileExists($recentFile);
    }

    #[Test]
    public function cleanup_handles_missing_export_directory(): void
    {
        if (File::exists($this->exportPath)) {
            File::deleteDirectory($this->exportPath);
        }

        $this->exportService->cleanupOldExports(7);

        $this->assertDirectoryDoesNotExist($this->exportPath);
    }

    #[Test]
    public function csv_export_includes_bom_header(): void
    {
        $filePath = $this->exportService->exportToCsv('test-export.log');

        $content = File::get($filePath);
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
    }

    #[Test]
    public function json_export_includes_metadata(): void
    {
        $filePath = $this->exportService->exportToJson('test-export.log');

        $data = json_decode(File::get($filePath), true);
        $this->assertArrayHasKey('generated_at', $data['export_info']);
        $this->assertArrayHasKey('total_entries', $data['export_info']);
        $this->assertArrayHasKey('filters_applied', $data['export_info']);
        $this->assertArrayHasKey('exported_by', $data['export_info']);
    }

    #[Test]
    public function excel_export_has_proper_xml_structure(): void
    {
        $filePath = $this->exportService->exportToExcel('test-export.log');

        $content = File::get($filePath);
        $this->assertStringContainsString('<?xml version="1.0"', $content);
        $this->assertStringContainsString('<Worksheet ss:Name="Log Export">', $content);
        $this->assertStringContainsString('<Style ss:ID="Header">', $content);
    }

    #[Test]
    public function pdf_html_includes_summary_section(): void
    {
        $filePath = $this->exportService->exportToPdf('test-export.log');

        $content = File::get($filePath);
        $this->assertStringContainsString('summary-card', $content);
        $this->assertStringContainsString('Total Log Entries', $content);
    }
}
