<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LogFileControllerTest extends TestCase
{
    private string $logPath;

    private string $logName = 'laravel-2025-06-01.log';

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->logPath = storage_path("logs/{$this->logName}");

        $logContent = "[2025-06-01 10:00:00] local.ERROR: Something went wrong\n";
        $logContent .= "[2025-06-01 10:01:00] local.INFO: All good\n";

        File::put($this->logPath, $logContent);
    }

    protected function tearDown(): void
    {
        if (File::exists($this->logPath)) {
            File::delete($this->logPath);
        }

        parent::tearDown();
    }

    #[Test]
    public function it_renders_the_log_files_index_page(): void
    {
        $response = $this->get(route('log-tracker.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function index_page_contains_expected_view_data(): void
    {
        $response = $this->get(route('log-tracker.index'));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'paginatedFiles',
            'counts',
            'fileSizes',
            'formattedFileNames',
            'pagination',
        ]);
    }

    #[Test]
    public function index_shows_formatted_date_for_laravel_log_files(): void
    {
        $response = $this->get(route('log-tracker.index'));

        $formattedNames = $response->viewData('formattedFileNames');
        $this->assertArrayHasKey($this->logName, $formattedNames);
        $this->assertSame('01 June 2025', $formattedNames[$this->logName]);
    }

    #[Test]
    public function index_shows_file_sizes(): void
    {
        $response = $this->get(route('log-tracker.index'));

        $fileSizes = $response->viewData('fileSizes');
        $this->assertArrayHasKey($this->logName, $fileSizes);
        $this->assertMatchesRegularExpression('/\d+(\.\d+)?\s(KB|MB)/', $fileSizes[$this->logName]);
    }

    #[Test]
    public function index_pagination_defaults_to_page_one(): void
    {
        $response = $this->get(route('log-tracker.index'));

        $pagination = $response->viewData('pagination');
        $this->assertSame(1, $pagination['current_page']);
    }

    #[Test]
    public function index_supports_page_parameter(): void
    {
        $response = $this->get(route('log-tracker.index', ['page' => 1]));

        $pagination = $response->viewData('pagination');
        $this->assertSame(1, $pagination['current_page']);
    }

    #[Test]
    public function it_shows_log_details(): void
    {
        $response = $this->get(route('log-tracker.show', $this->logName));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'logFiles',
            'logName',
            'entries',
            'counts',
            'logLevels',
            'totalEntries',
            'perPage',
        ]);
    }

    #[Test]
    public function show_contains_parsed_entries(): void
    {
        $response = $this->get(route('log-tracker.show', $this->logName));

        $entries = $response->viewData('entries');
        $this->assertCount(2, $entries);

        $errorEntry = collect($entries)->firstWhere('level', 'error');
        $this->assertNotNull($errorEntry);
        $this->assertStringContainsString('Something went wrong', $errorEntry['message']);
    }

    #[Test]
    public function show_handles_nonexistent_log_file(): void
    {
        $response = $this->get(route('log-tracker.show', 'nonexistent.log'));

        $response->assertStatus(200);
        $response->assertViewHas('error', 'Log file not found');
    }

    #[Test]
    public function show_sanitizes_path_traversal_attempts(): void
    {
        $response = $this->get(route('log-tracker.show', '../../../etc/passwd'));

        // Router strips slashes from route params, resulting in 404
        $response->assertStatus(404);
    }

    #[Test]
    public function it_downloads_a_log_file(): void
    {
        $response = $this->get(route('log-tracker.download', $this->logName));

        $response->assertStatus(200);
        $response->assertDownload($this->logName);
    }

    #[Test]
    public function download_returns_404_for_missing_file(): void
    {
        $response = $this->get(route('log-tracker.download', 'nonexistent.log'));

        $response->assertStatus(404);
    }

    #[Test]
    public function delete_is_disabled_by_default(): void
    {
        $response = $this->post(route('log-tracker.delete', $this->logName));

        $response->assertStatus(403);
    }

    #[Test]
    public function delete_removes_file_when_enabled(): void
    {
        $this->app['config']->set('log-tracker.allow_delete', true);

        $response = $this->post(route('log-tracker.delete', $this->logName));

        $response->assertRedirect(route('log-tracker.index'));
        $this->assertFileDoesNotExist($this->logPath);
    }

    #[Test]
    public function delete_returns_404_for_missing_file(): void
    {
        $this->app['config']->set('log-tracker.allow_delete', true);

        $response = $this->post(route('log-tracker.delete', 'nonexistent.log'));

        $response->assertStatus(404);
    }

    #[Test]
    public function clear_is_disabled_by_default(): void
    {
        $response = $this->post(route('log-tracker.clear', $this->logName));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function clear_empties_file_when_enabled(): void
    {
        $this->app['config']->set('log-tracker.allow_delete', true);

        $response = $this->post(route('log-tracker.clear', $this->logName));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame('', File::get($this->logPath));
    }

    #[Test]
    public function clear_returns_error_for_missing_file(): void
    {
        $this->app['config']->set('log-tracker.allow_delete', true);

        $response = $this->post(route('log-tracker.clear', 'nonexistent.log'));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function show_entries_contain_expected_fields(): void
    {
        $response = $this->get(route('log-tracker.show', $this->logName));

        $entries = $response->viewData('entries');
        $entry = $entries[0];

        $this->assertArrayHasKey('timestamp', $entry);
        $this->assertArrayHasKey('level', $entry);
        $this->assertArrayHasKey('message', $entry);
        $this->assertArrayHasKey('stack', $entry);
        $this->assertArrayHasKey('color', $entry);
        $this->assertArrayHasKey('icon', $entry);
    }

    #[Test]
    public function show_counts_include_total(): void
    {
        $response = $this->get(route('log-tracker.show', $this->logName));

        $counts = $response->viewData('counts');
        $this->assertArrayHasKey('total', $counts);
        $this->assertSame(2, $counts['total']);
    }
}
