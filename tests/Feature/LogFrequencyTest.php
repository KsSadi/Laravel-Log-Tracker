<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Facades\LogTracker;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LogFrequencyTest extends TestCase
{
    private string $logPath;

    private string $logName = 'laravel-freq-test.log';

    protected function setUp(): void
    {
        parent::setUp();
        $this->logPath = storage_path("logs/{$this->logName}");
    }

    protected function tearDown(): void
    {
        if (File::exists($this->logPath)) {
            File::delete($this->logPath);
        }
        parent::tearDown();
    }

    // ──────────────────────────────────────────────
    // LogParserService::getEntryFrequency()
    // ──────────────────────────────────────────────

    #[Test]
    public function it_returns_empty_array_when_log_file_does_not_exist(): void
    {
        $result = LogTracker::getEntryFrequency('nonexistent-file.log');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_empty_array_when_no_duplicate_messages_exist(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: First unique error\n";
        $content .= "[2025-06-01 09:00:00] local.INFO: Second unique message\n";
        $content .= "[2025-06-01 10:00:00] local.WARNING: Third unique warning\n";
        File::put($this->logPath, $content);

        $result = LogTracker::getEntryFrequency($this->logName);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function it_detects_a_message_that_appears_multiple_times(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: Database connection failed\n";
        $content .= "[2025-06-01 09:00:00] local.ERROR: Database connection failed\n";
        $content .= "[2025-06-01 10:00:00] local.ERROR: Database connection failed\n";
        File::put($this->logPath, $content);

        $result = LogTracker::getEntryFrequency($this->logName);

        $this->assertCount(1, $result);
        $entry = array_values($result)[0];
        $this->assertSame('Database connection failed', $entry['message']);
        $this->assertSame(3, $entry['count']);
        $this->assertSame('error', $entry['level']);
    }

    #[Test]
    public function it_excludes_messages_that_appear_only_once(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: Repeated message\n";
        $content .= "[2025-06-01 09:00:00] local.ERROR: Repeated message\n";
        $content .= "[2025-06-01 10:00:00] local.INFO: Unique message only once\n";
        File::put($this->logPath, $content);

        $result = LogTracker::getEntryFrequency($this->logName);

        $this->assertCount(1, $result);
        $messages = array_column(array_values($result), 'message');
        $this->assertContains('Repeated message', $messages);
        $this->assertNotContains('Unique message only once', $messages);
    }

    #[Test]
    public function it_sorts_results_by_count_descending(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: Alpha error\n";
        $content .= "[2025-06-01 08:01:00] local.ERROR: Beta error\n";
        $content .= "[2025-06-01 08:02:00] local.ERROR: Beta error\n";
        $content .= "[2025-06-01 08:03:00] local.ERROR: Beta error\n";
        $content .= "[2025-06-01 08:04:00] local.ERROR: Alpha error\n";
        $content .= "[2025-06-01 08:06:00] local.WARNING: Gamma warning\n";
        $content .= "[2025-06-01 08:07:00] local.WARNING: Gamma warning\n";
        $content .= "[2025-06-01 08:08:00] local.WARNING: Gamma warning\n";
        $content .= "[2025-06-01 08:09:00] local.WARNING: Gamma warning\n";
        File::put($this->logPath, $content);

        $result = array_values(LogTracker::getEntryFrequency($this->logName));

        $this->assertCount(3, $result);
        // Sorted descending: Gamma=4, Beta=3, Alpha=2
        $this->assertSame(4, $result[0]['count']);
        $this->assertSame(3, $result[1]['count']);
        $this->assertSame(2, $result[2]['count']);
    }

    #[Test]
    public function it_tracks_first_and_last_seen_timestamps(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: Repeated error\n";
        $content .= "[2025-06-01 10:30:00] local.ERROR: Repeated error\n";
        $content .= "[2025-06-01 15:45:00] local.ERROR: Repeated error\n";
        File::put($this->logPath, $content);

        $result = array_values(LogTracker::getEntryFrequency($this->logName));

        $this->assertCount(1, $result);
        $entry = $result[0];
        $this->assertSame('2025-06-01 08:00:00', $entry['first_seen']);
        $this->assertSame('2025-06-01 15:45:00', $entry['last_seen']);
    }

    #[Test]
    public function it_respects_the_limit_parameter(): void
    {
        $content = '';

        for ($i = 0; $i < 20; $i++) {
            $ts1 = '2025-06-01 08:'.str_pad($i, 2, '0', STR_PAD_LEFT).':00';
            $ts2 = '2025-06-01 09:'.str_pad($i, 2, '0', STR_PAD_LEFT).':00';
            $content .= "[{$ts1}] local.ERROR: Repeated error pattern {$i}\n";
            $content .= "[{$ts2}] local.ERROR: Repeated error pattern {$i}\n";
        }

        File::put($this->logPath, $content);

        $limited = LogTracker::getEntryFrequency($this->logName, 5);

        $this->assertLessThanOrEqual(5, count($limited));
    }

    #[Test]
    public function it_returns_required_keys_in_each_entry(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: Duplicate message\n";
        $content .= "[2025-06-01 09:00:00] local.ERROR: Duplicate message\n";
        File::put($this->logPath, $content);

        $result = array_values(LogTracker::getEntryFrequency($this->logName));

        $this->assertCount(1, $result);
        $entry = $result[0];

        $this->assertArrayHasKey('message', $entry);
        $this->assertArrayHasKey('level', $entry);
        $this->assertArrayHasKey('count', $entry);
        $this->assertArrayHasKey('first_seen', $entry);
        $this->assertArrayHasKey('last_seen', $entry);
    }

    // ──────────────────────────────────────────────
    // LogFileController — view integration
    // ──────────────────────────────────────────────

    #[Test]
    public function log_details_view_receives_frequent_entries_variable(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: Repeated error\n";
        $content .= "[2025-06-01 09:00:00] local.ERROR: Repeated error\n";
        File::put($this->logPath, $content);

        $response = $this->get(route('log-tracker.show', $this->logName));

        $response->assertOk();
        $response->assertViewHas('frequentEntries');
    }

    #[Test]
    public function log_details_view_has_empty_frequent_entries_when_no_duplicates(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: First unique error\n";
        $content .= "[2025-06-01 09:00:00] local.INFO: Second unique message\n";
        File::put($this->logPath, $content);

        $response = $this->get(route('log-tracker.show', $this->logName));

        $response->assertOk();
        $response->assertViewHas('frequentEntries', []);
    }

    #[Test]
    public function log_details_view_frequent_entries_contain_decorated_fields(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: Decorated repeated error\n";
        $content .= "[2025-06-01 09:00:00] local.ERROR: Decorated repeated error\n";
        File::put($this->logPath, $content);

        $response = $this->get(route('log-tracker.show', $this->logName));

        $response->assertOk();

        $entries = $response->viewData('frequentEntries');
        $this->assertNotEmpty($entries);
        $entry = array_values($entries)[0];

        $this->assertArrayHasKey('color', $entry);
        $this->assertArrayHasKey('icon', $entry);
        $this->assertArrayHasKey('first_seen', $entry);
        $this->assertArrayHasKey('last_seen', $entry);
        $this->assertArrayHasKey('count', $entry);
        $this->assertSame(2, $entry['count']);
    }

    #[Test]
    public function log_details_view_frequent_entries_timestamps_are_formatted(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: Formatted timestamp error\n";
        $content .= "[2025-06-01 14:30:00] local.ERROR: Formatted timestamp error\n";
        File::put($this->logPath, $content);

        $response = $this->get(route('log-tracker.show', $this->logName));

        $response->assertOk();

        $entries = $response->viewData('frequentEntries');
        $entry = array_values($entries)[0];

        // Formatted as "j M Y, h:i A" — e.g. "1 Jun 2025, 08:00 AM"
        $this->assertMatchesRegularExpression('/\d+ \w+ \d{4}, \d{2}:\d{2} (AM|PM)/', $entry['first_seen']);
        $this->assertMatchesRegularExpression('/\d+ \w+ \d{4}, \d{2}:\d{2} (AM|PM)/', $entry['last_seen']);
    }

    #[Test]
    public function log_details_view_shows_freq_section_html_when_duplicates_exist(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: Visible repeated error\n";
        $content .= "[2025-06-01 09:00:00] local.ERROR: Visible repeated error\n";
        File::put($this->logPath, $content);

        $response = $this->get(route('log-tracker.show', $this->logName));

        $response->assertOk();
        $response->assertSee('Repeated Log Entries');
        $response->assertSee('Visible repeated error');
    }

    #[Test]
    public function log_details_view_hides_freq_section_when_no_duplicates(): void
    {
        $content = "[2025-06-01 08:00:00] local.ERROR: Only once error A\n";
        $content .= "[2025-06-01 09:00:00] local.INFO: Only once message B\n";
        File::put($this->logPath, $content);

        $response = $this->get(route('log-tracker.show', $this->logName));

        $response->assertOk();
        $response->assertDontSee('Repeated Log Entries');
    }
}
