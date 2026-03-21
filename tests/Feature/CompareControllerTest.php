<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CompareControllerTest extends TestCase
{
    private string $logPathA;

    private string $logPathB;

    private string $logNameA = 'laravel-compare-a.log';

    private string $logNameB = 'laravel-compare-b.log';

    protected function setUp(): void
    {
        parent::setUp();

        $this->logPathA = storage_path("logs/{$this->logNameA}");
        $this->logPathB = storage_path("logs/{$this->logNameB}");

        // File A: ERROR shared, WARNING unique-to-A, INFO shared
        $contentA = "[2025-06-01 08:00:00] local.ERROR: Database connection failed\n";
        $contentA .= "[2025-06-01 09:00:00] local.WARNING: Slow query detected\n";
        $contentA .= "[2025-06-01 10:00:00] local.INFO: User authenticated successfully\n";

        // File B: ERROR shared, DEBUG unique-to-B, INFO shared
        $contentB = "[2025-06-02 08:00:00] local.ERROR: Database connection failed\n";
        $contentB .= "[2025-06-02 11:00:00] local.DEBUG: Cache hit for key users.list\n";
        $contentB .= "[2025-06-02 10:00:00] local.INFO: User authenticated successfully\n";

        File::put($this->logPathA, $contentA);
        File::put($this->logPathB, $contentB);
    }

    protected function tearDown(): void
    {
        File::delete([$this->logPathA, $this->logPathB]);
        parent::tearDown();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Routing & rendering
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function compare_page_loads_successfully_with_no_query_params(): void
    {
        $response = $this->get(route('log-tracker.compare'));

        $response->assertStatus(200);
    }

    #[Test]
    public function compare_page_passes_log_files_to_view(): void
    {
        $response = $this->get(route('log-tracker.compare'));

        $response->assertViewHas('logFiles');
        $logFiles = $response->viewData('logFiles');
        $this->assertContains($this->logNameA, $logFiles);
        $this->assertContains($this->logNameB, $logFiles);
    }

    #[Test]
    public function compare_page_has_null_comparison_when_no_files_selected(): void
    {
        $response = $this->get(route('log-tracker.compare'));

        $response->assertViewHas('comparison', null);
        $response->assertViewHas('fileA', null);
        $response->assertViewHas('fileB', null);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Same-file guard
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function compare_returns_null_comparison_when_same_file_selected_for_both(): void
    {
        $response = $this->get(route('log-tracker.compare', [
            'file_a' => $this->logNameA,
            'file_b' => $this->logNameA,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('comparison', null);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Comparison payload structure
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function compare_returns_all_expected_keys_in_comparison_payload(): void
    {
        $response = $this->get(route('log-tracker.compare', [
            'file_a' => $this->logNameA,
            'file_b' => $this->logNameB,
        ]));

        $response->assertStatus(200);

        $comparison = $response->viewData('comparison');
        $this->assertNotNull($comparison);

        foreach (['fileA', 'fileB', 'totalA', 'totalB', 'levelsA', 'levelsB', 'allLevels', 'onlyInA', 'onlyInB', 'shared'] as $key) {
            $this->assertArrayHasKey($key, $comparison, "Missing key: {$key}");
        }
    }

    #[Test]
    public function compare_correctly_counts_total_entries_for_each_file(): void
    {
        $response = $this->get(route('log-tracker.compare', [
            'file_a' => $this->logNameA,
            'file_b' => $this->logNameB,
        ]));

        $comparison = $response->viewData('comparison');

        $this->assertSame(3, $comparison['totalA']);
        $this->assertSame(3, $comparison['totalB']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Shared / only-in-A / only-in-B logic
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function compare_correctly_identifies_entries_only_in_file_a(): void
    {
        $response = $this->get(route('log-tracker.compare', [
            'file_a' => $this->logNameA,
            'file_b' => $this->logNameB,
        ]));

        $comparison = $response->viewData('comparison');

        // File A has WARNING:Slow query detected — not in B
        $this->assertCount(1, $comparison['onlyInA']);
        $this->assertSame('Slow query detected', $comparison['onlyInA'][0]['message']);
        $this->assertSame('warning', strtolower($comparison['onlyInA'][0]['level']));
    }

    #[Test]
    public function compare_correctly_identifies_entries_only_in_file_b(): void
    {
        $response = $this->get(route('log-tracker.compare', [
            'file_a' => $this->logNameA,
            'file_b' => $this->logNameB,
        ]));

        $comparison = $response->viewData('comparison');

        // File B has DEBUG:Cache hit for key users.list — not in A
        $this->assertCount(1, $comparison['onlyInB']);
        $this->assertSame('Cache hit for key users.list', $comparison['onlyInB'][0]['message']);
        $this->assertSame('debug', strtolower($comparison['onlyInB'][0]['level']));
    }

    #[Test]
    public function compare_correctly_identifies_shared_entries(): void
    {
        $response = $this->get(route('log-tracker.compare', [
            'file_a' => $this->logNameA,
            'file_b' => $this->logNameB,
        ]));

        $comparison = $response->viewData('comparison');

        // ERROR:Database connection failed and INFO:User authenticated successfully are in both
        $this->assertCount(2, $comparison['shared']);

        $sharedMessages = array_column($comparison['shared'], 'message');
        $this->assertContains('Database connection failed', $sharedMessages);
        $this->assertContains('User authenticated successfully', $sharedMessages);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Level counts
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function compare_returns_correct_level_counts_for_each_file(): void
    {
        $response = $this->get(route('log-tracker.compare', [
            'file_a' => $this->logNameA,
            'file_b' => $this->logNameB,
        ]));

        $comparison = $response->viewData('comparison');

        // File A: error=1, warning=1, info=1
        $this->assertSame(1, $comparison['levelsA']['error']);
        $this->assertSame(1, $comparison['levelsA']['warning']);
        $this->assertSame(1, $comparison['levelsA']['info']);

        // File B: error=1, debug=1, info=1
        $this->assertSame(1, $comparison['levelsB']['error']);
        $this->assertSame(1, $comparison['levelsB']['debug']);
        $this->assertSame(1, $comparison['levelsB']['info']);
    }

    #[Test]
    public function compare_all_levels_is_sorted_union_of_both_files(): void
    {
        $response = $this->get(route('log-tracker.compare', [
            'file_a' => $this->logNameA,
            'file_b' => $this->logNameB,
        ]));

        $comparison = $response->viewData('comparison');
        $allLevels = $comparison['allLevels'];

        // Should contain debug, error, info, warning (from both files combined)
        $this->assertContains('debug', $allLevels);
        $this->assertContains('error', $allLevels);
        $this->assertContains('info', $allLevels);
        $this->assertContains('warning', $allLevels);

        // Should be sorted alphabetically
        $expected = $allLevels;
        sort($expected);
        $this->assertSame($expected, $allLevels);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Decoration (color / icon)
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function compare_decorates_entries_with_color_and_icon(): void
    {
        $response = $this->get(route('log-tracker.compare', [
            'file_a' => $this->logNameA,
            'file_b' => $this->logNameB,
        ]));

        $comparison = $response->viewData('comparison');

        foreach (['onlyInA', 'onlyInB', 'shared'] as $group) {
            foreach ($comparison[$group] as $entry) {
                $this->assertArrayHasKey('color', $entry, "{$group} entry missing 'color'");
                $this->assertArrayHasKey('icon', $entry, "{$group} entry missing 'icon'");
                $this->assertNotEmpty($entry['color']);
                $this->assertNotEmpty($entry['icon']);
            }
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Graceful handling — missing / empty file
    // ──────────────────────────────────────────────────────────────────────────

    #[Test]
    public function compare_handles_nonexistent_file_gracefully(): void
    {
        $response = $this->get(route('log-tracker.compare', [
            'file_a' => 'does-not-exist.log',
            'file_b' => $this->logNameB,
        ]));

        $response->assertStatus(200);

        $comparison = $response->viewData('comparison');
        $this->assertNotNull($comparison);
        $this->assertSame(0, $comparison['totalA']);
        $this->assertCount(0, $comparison['onlyInA']);
        $this->assertCount($comparison['totalB'], $comparison['onlyInB']);
    }
}
