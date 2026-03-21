<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SearchControllerTest extends TestCase
{
    private string $logPathA;

    private string $logPathB;

    private string $logNameA = 'laravel-2025-06-01.log';

    private string $logNameB = 'laravel-2025-06-02.log';

    protected function setUp(): void
    {
        parent::setUp();

        $this->logPathA = storage_path("logs/{$this->logNameA}");
        $this->logPathB = storage_path("logs/{$this->logNameB}");

        $contentA = "[2025-06-01 08:00:00] local.ERROR: Database connection failed\n";
        $contentA .= "[2025-06-01 09:00:00] local.INFO: User authenticated successfully\n";
        $contentA .= "[2025-06-01 10:00:00] local.WARNING: Slow query detected\n";
        $contentA .= "[2025-06-01 11:00:00] local.DEBUG: Cache hit for key users.list\n";

        $contentB = "[2025-06-02 07:00:00] local.CRITICAL: Payment gateway timeout\n";
        $contentB .= "[2025-06-02 08:00:00] local.ERROR: SQLSTATE integrity constraint violation\n";
        $contentB .= "[2025-06-02 09:00:00] local.INFO: Scheduled task completed\n";

        File::put($this->logPathA, $contentA);
        File::put($this->logPathB, $contentB);
    }

    protected function tearDown(): void
    {
        File::delete([$this->logPathA, $this->logPathB]);
        parent::tearDown();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Rendering
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_renders_the_search_page_without_filters(): void
    {
        $response = $this->get(route('log-tracker.search'));

        $response->assertStatus(200);
    }

    #[Test]
    public function search_page_without_filters_has_null_results(): void
    {
        $response = $this->get(route('log-tracker.search'));

        $response->assertViewHas('results', null);
    }

    #[Test]
    public function search_page_passes_log_files_to_view(): void
    {
        $response = $this->get(route('log-tracker.search'));

        $response->assertViewHas('logFiles');
        $logFiles = $response->viewData('logFiles');
        $this->assertContains($this->logNameA, $logFiles);
        $this->assertContains($this->logNameB, $logFiles);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Keyword Search
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_returns_results_for_keyword_search(): void
    {
        $response = $this->get(route('log-tracker.search', ['query' => 'database connection']));

        $response->assertStatus(200);
        $results = $response->viewData('results');
        $this->assertNotNull($results);
        $this->assertSame(1, $results['total']);
        $this->assertStringContainsStringIgnoringCase('database connection failed', $results['entries'][0]['message']);
    }

    #[Test]
    public function keyword_search_is_case_insensitive(): void
    {
        $response = $this->get(route('log-tracker.search', ['query' => 'DATABASE']));

        $results = $response->viewData('results');
        $this->assertSame(1, $results['total']);
    }

    #[Test]
    public function keyword_search_across_multiple_files(): void
    {
        $response = $this->get(route('log-tracker.search', ['query' => 'sqlstate']));

        $results = $response->viewData('results');
        $this->assertSame(1, $results['total']);
        $this->assertSame($this->logNameB, $results['entries'][0]['file']);
    }

    #[Test]
    public function it_returns_empty_results_when_keyword_not_found(): void
    {
        $response = $this->get(route('log-tracker.search', ['query' => 'xyznonexistent999']));

        $results = $response->viewData('results');
        $this->assertSame(0, $results['total']);
        $this->assertEmpty($results['entries']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Level Filter
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_filters_by_log_level(): void
    {
        $response = $this->get(route('log-tracker.search', ['level' => 'error']));

        $results = $response->viewData('results');
        $this->assertSame(2, $results['total']);

        foreach ($results['entries'] as $entry) {
            $this->assertSame('error', $entry['level']);
        }
    }

    #[Test]
    public function it_filters_by_info_level(): void
    {
        $response = $this->get(route('log-tracker.search', ['level' => 'info']));

        $results = $response->viewData('results');
        $this->assertSame(2, $results['total']);
    }

    #[Test]
    public function entries_have_color_and_icon_from_config(): void
    {
        $response = $this->get(route('log-tracker.search', ['level' => 'error']));

        $results = $response->viewData('results');
        $this->assertArrayHasKey('color', $results['entries'][0]);
        $this->assertArrayHasKey('icon', $results['entries'][0]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Date Filters
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_filters_by_date_from(): void
    {
        $response = $this->get(route('log-tracker.search', ['date_from' => '2025-06-02']));

        $results = $response->viewData('results');
        $this->assertSame(3, $results['total']);

        foreach ($results['entries'] as $entry) {
            $this->assertStringStartsWith('2 Jun 2025', $entry['timestamp']);
        }
    }

    #[Test]
    public function it_filters_by_date_to(): void
    {
        $response = $this->get(route('log-tracker.search', ['date_to' => '2025-06-01']));

        $results = $response->viewData('results');
        $this->assertSame(4, $results['total']);
    }

    #[Test]
    public function it_filters_by_date_range(): void
    {
        $response = $this->get(route('log-tracker.search', [
            'date_from' => '2025-06-02',
            'date_to' => '2025-06-02',
        ]));

        $results = $response->viewData('results');
        $this->assertSame(3, $results['total']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // File Filter
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_filters_by_specific_file(): void
    {
        $response = $this->get(route('log-tracker.search', ['file' => $this->logNameA]));

        $results = $response->viewData('results');
        $this->assertSame(4, $results['total']);

        foreach ($results['entries'] as $entry) {
            $this->assertSame($this->logNameA, $entry['file']);
        }
    }

    #[Test]
    public function it_returns_empty_when_non_existent_file_specified(): void
    {
        $response = $this->get(route('log-tracker.search', ['file' => 'ghost.log']));

        $results = $response->viewData('results');
        $this->assertSame(0, $results['total']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Combined Filters
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_combines_keyword_and_level_filters(): void
    {
        $response = $this->get(route('log-tracker.search', [
            'query' => 'authenticated',
            'level' => 'info',
        ]));

        $results = $response->viewData('results');
        $this->assertSame(1, $results['total']);
        $this->assertStringContainsStringIgnoringCase('authenticated', $results['entries'][0]['message']);
    }

    #[Test]
    public function it_combines_file_and_level_filters(): void
    {
        $response = $this->get(route('log-tracker.search', [
            'file' => $this->logNameB,
            'level' => 'critical',
        ]));

        $results = $response->viewData('results');
        $this->assertSame(1, $results['total']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Results Ordering & Structure
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function results_are_sorted_newest_first(): void
    {
        $response = $this->get(route('log-tracker.search', ['level' => 'info']));

        $results = $response->viewData('results');
        $this->assertSame(2, $results['total']);

        // First entry should be from logNameB (2025-06-02), second from logNameA (2025-06-01)
        $this->assertSame($this->logNameB, $results['entries'][0]['file']);
        $this->assertSame($this->logNameA, $results['entries'][1]['file']);
    }

    #[Test]
    public function results_contain_file_key(): void
    {
        $response = $this->get(route('log-tracker.search', ['query' => 'completed']));

        $results = $response->viewData('results');
        $this->assertArrayHasKey('file', $results['entries'][0]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Validation
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function it_rejects_invalid_log_level(): void
    {
        $response = $this->get(route('log-tracker.search', ['level' => 'invalid_level']));

        $response->assertRedirect();
        $response->assertSessionHasErrors('level');
    }

    #[Test]
    public function it_rejects_date_to_before_date_from(): void
    {
        $response = $this->get(route('log-tracker.search', [
            'date_from' => '2025-06-05',
            'date_to' => '2025-06-01',
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('date_to');
    }

    #[Test]
    public function it_rejects_query_exceeding_max_length(): void
    {
        $response = $this->get(route('log-tracker.search', ['query' => str_repeat('a', 201)]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('query');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Pagination
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function results_include_pagination_metadata(): void
    {
        $response = $this->get(route('log-tracker.search', ['query' => 'a']));

        $results = $response->viewData('results');
        $this->assertArrayHasKey('current_page', $results);
        $this->assertArrayHasKey('last_page', $results);
        $this->assertArrayHasKey('from', $results);
        $this->assertArrayHasKey('to', $results);
        $this->assertArrayHasKey('per_page', $results);
        $this->assertSame(1, $results['current_page']);
    }
}
