<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ExportControllerTest extends TestCase
{
    private string $logPath;

    private string $exportPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->logPath = storage_path('logs/test-export-ctrl.log');
        $this->exportPath = storage_path('app/exports');

        $logContent = "[2025-06-01 10:00:00] local.ERROR: Export test error\n";
        $logContent .= "[2025-06-01 10:01:00] local.INFO: Export test info\n";

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
    public function it_renders_the_export_form(): void
    {
        $response = $this->get(route('log-tracker.export.form'));

        $response->assertStatus(200);
        $response->assertViewHas('logFiles');
    }

    #[Test]
    public function export_form_lists_available_log_files(): void
    {
        $response = $this->get(route('log-tracker.export.form'));

        $logFiles = $response->viewData('logFiles');
        $this->assertIsArray($logFiles);
        $this->assertContains('test-export-ctrl.log', $logFiles);
    }

    #[Test]
    public function it_exports_csv_format(): void
    {
        $response = $this->post(route('log-tracker.export'), [
            'format' => 'csv',
            'log_files' => ['test-export-ctrl.log'],
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function it_exports_json_format(): void
    {
        $response = $this->post(route('log-tracker.export'), [
            'format' => 'json',
            'log_files' => ['test-export-ctrl.log'],
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('application/json', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function it_exports_excel_format(): void
    {
        $response = $this->post(route('log-tracker.export'), [
            'format' => 'excel',
            'log_files' => ['test-export-ctrl.log'],
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.ms-excel', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function it_exports_pdf_format(): void
    {
        $response = $this->post(route('log-tracker.export'), [
            'format' => 'pdf',
            'log_files' => ['test-export-ctrl.log'],
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function export_validates_format_is_required(): void
    {
        $response = $this->post(route('log-tracker.export'), []);

        $response->assertSessionHasErrors('format');
    }

    #[Test]
    public function export_validates_invalid_format(): void
    {
        $response = $this->post(route('log-tracker.export'), [
            'format' => 'invalid',
        ]);

        $response->assertSessionHasErrors('format');
    }

    #[Test]
    public function export_validates_levels(): void
    {
        $response = $this->post(route('log-tracker.export'), [
            'format' => 'csv',
            'levels' => ['invalid-level'],
        ]);

        $response->assertSessionHasErrors('levels.0');
    }

    #[Test]
    public function export_accepts_valid_levels(): void
    {
        $response = $this->post(route('log-tracker.export'), [
            'format' => 'json',
            'log_files' => ['test-export-ctrl.log'],
            'levels' => ['error', 'warning'],
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function export_uses_all_log_files_when_none_specified(): void
    {
        $response = $this->post(route('log-tracker.export'), [
            'format' => 'json',
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function export_returns_error_when_disabled(): void
    {
        $this->app['config']->set('log-tracker.export.enabled', false);

        $response = $this->post(route('log-tracker.export'), [
            'format' => 'csv',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Export functionality is disabled.');
    }

    #[Test]
    public function export_returns_error_when_specific_format_is_disabled(): void
    {
        $this->app['config']->set('log-tracker.export.formats.csv.enabled', false);

        $response = $this->post(route('log-tracker.export'), [
            'format' => 'csv',
            'log_files' => ['test-export-ctrl.log'],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function quick_export_returns_error_when_format_is_disabled(): void
    {
        $this->app['config']->set('log-tracker.export.formats.json.enabled', false);

        $response = $this->get(route('log-tracker.export.quick', [
            'logName' => 'test-export-ctrl.log',
            'format' => 'json',
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function quick_export_csv(): void
    {
        $response = $this->get(route('log-tracker.export.quick', [
            'logName' => 'test-export-ctrl.log',
            'format' => 'csv',
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function quick_export_json(): void
    {
        $response = $this->get(route('log-tracker.export.quick', [
            'logName' => 'test-export-ctrl.log',
            'format' => 'json',
        ]));

        $response->assertStatus(200);
    }

    #[Test]
    public function quick_export_defaults_to_csv_for_invalid_format(): void
    {
        $response = $this->get(route('log-tracker.export.quick', [
            'logName' => 'test-export-ctrl.log',
            'format' => 'invalid',
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function quick_export_sanitizes_log_name(): void
    {
        $response = $this->get(route('log-tracker.export.quick', [
            'logName' => '../../../etc/passwd',
            'format' => 'json',
        ]));

        // Router strips slashes from route params, resulting in 404
        $response->assertStatus(404);
    }

    #[Test]
    public function export_with_date_filter(): void
    {
        $response = $this->post(route('log-tracker.export'), [
            'format' => 'json',
            'log_files' => ['test-export-ctrl.log'],
            'date_from' => '2025-06-01',
            'date_to' => '2025-06-01',
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function export_with_search_filter(): void
    {
        $response = $this->post(route('log-tracker.export'), [
            'format' => 'json',
            'log_files' => ['test-export-ctrl.log'],
            'search' => 'Export test',
        ]);

        $response->assertStatus(200);
    }
}
