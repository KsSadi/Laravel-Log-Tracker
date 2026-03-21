<?php

namespace Kssadi\LogTracker\Tests\Unit;

use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Services\LogParserService;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LogParserServiceTest extends TestCase
{
    private LogParserService $service;

    private array $tempLogFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LogParserService;
    }

    protected function tearDown(): void
    {
        foreach ($this->tempLogFiles as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }
        parent::tearDown();
    }

    #[Test]
    public function it_returns_an_array_of_log_files(): void
    {
        $files = $this->service->getLogFiles();

        $this->assertIsArray($files);
    }

    #[Test]
    public function it_returns_error_when_log_file_not_found(): void
    {
        $result = $this->service->getLogEntries('non-existent-file.log');

        $this->assertArrayHasKey('error', $result);
        $this->assertSame([], $result['entries']);
        $this->assertSame(0, $result['total']);
    }

    #[Test]
    public function it_returns_error_for_get_all_log_entries_when_file_not_found(): void
    {
        $result = $this->service->getAllLogEntries('non-existent-file.log');

        $this->assertArrayHasKey('error', $result);
        $this->assertSame([], $result['entries']);
        $this->assertSame(0, $result['total']);
    }

    #[Test]
    public function it_parses_log_entries_correctly(): void
    {
        // Create a temporary log file
        $logPath = storage_path('logs/test-parser.log');
        $this->tempLogFiles[] = $logPath;
        $logContent = "[2025-01-01 10:00:00] local.ERROR: Something went wrong\n";
        $logContent .= "[2025-01-01 10:01:00] local.INFO: All good\n";

        File::put($logPath, $logContent);

        $result = $this->service->getAllLogEntries('test-parser.log');

        $this->assertSame(2, $result['total']);
        $this->assertIsArray($result['entries']);

        // Newest log should come first (reversed)
        $this->assertSame('info', $result['entries'][0]['level']);
        $this->assertSame('error', $result['entries'][1]['level']);
        $this->assertSame('Something went wrong', $result['entries'][1]['message']);
    }

    #[Test]
    public function it_paginates_log_entries(): void
    {
        $logPath = storage_path('logs/test-pagination.log');
        $logContent = '';
        for ($i = 1; $i <= 10; $i++) {
            $minute = str_pad($i, 2, '0', STR_PAD_LEFT);
            $logContent .= "[2025-01-01 10:{$minute}:00] local.INFO: Message {$i}\n";
        }

        File::put($logPath, $logContent);

        $result = $this->service->getLogEntries('test-pagination.log', 1, 5);

        $this->assertSame(10, $result['total']);
        $this->assertCount(5, $result['entries']);
        $this->assertSame(1, $result['current_page']);
        $this->assertSame(2, $result['last_page']);

        File::delete($logPath);
    }

    #[Test]
    public function it_correctly_assigns_stack_traces_to_their_entries(): void
    {
        $logPath = storage_path('logs/test-stack-trace.log');
        $this->tempLogFiles[] = $logPath;
        $logContent = "[2025-01-01 10:00:00] local.ERROR: Error with stack\n";
        $logContent .= "Stack trace:\n";
        $logContent .= "#0 /app/Handler.php(45): handle()\n";
        $logContent .= "#1 /app/Kernel.php(30): dispatch()\n";
        $logContent .= "[2025-01-01 10:01:00] local.INFO: Clean info\n";

        File::put($logPath, $logContent);

        $result = $this->service->getAllLogEntries('test-stack-trace.log');

        $this->assertSame(2, $result['total']);

        // Newest first: INFO at [0], ERROR at [1]
        $infoEntry = $result['entries'][0];
        $errorEntry = $result['entries'][1];

        $this->assertSame('info', $infoEntry['level']);
        $this->assertSame('', $infoEntry['stack'], 'INFO entry should have no stack trace');

        $this->assertSame('error', $errorEntry['level']);
        $this->assertStringContainsString('#0', $errorEntry['stack']);
        $this->assertStringContainsString('#1', $errorEntry['stack']);

        // #0 must appear before #1 (correct order, not reversed)
        $this->assertLessThan(
            strpos($errorEntry['stack'], '#1'),
            strpos($errorEntry['stack'], '#0'),
            'Stack trace lines must be in original order (#0 before #1)'
        );
    }

    #[Test]
    public function it_returns_correct_structure_for_log_entries(): void
    {
        $logPath = storage_path('logs/test-structure.log');
        $this->tempLogFiles[] = $logPath;
        File::put($logPath, "[2025-06-01 09:30:00] production.WARNING: Disk space low\n");

        $result = $this->service->getAllLogEntries('test-structure.log');

        $this->assertArrayHasKey('entries', $result);
        $this->assertArrayHasKey('total', $result);

        $entry = $result['entries'][0];
        $this->assertArrayHasKey('timestamp', $entry);
        $this->assertArrayHasKey('level', $entry);
        $this->assertArrayHasKey('message', $entry);
        $this->assertArrayHasKey('stack', $entry);
        $this->assertSame('warning', $entry['level']);
    }
}
