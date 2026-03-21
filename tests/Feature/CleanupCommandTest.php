<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CleanupCommandTest extends TestCase
{
    private string $exportPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exportPath = storage_path('app/exports');

        if (! File::exists($this->exportPath)) {
            File::makeDirectory($this->exportPath, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        if (File::exists($this->exportPath)) {
            File::cleanDirectory($this->exportPath);
        }

        parent::tearDown();
    }

    #[Test]
    public function it_runs_cleanup_successfully(): void
    {
        $this->artisan('log-tracker:cleanup')
            ->expectsOutputToContain('Cleaned up log export files older than 7 day(s)')
            ->assertSuccessful();
    }

    #[Test]
    public function it_accepts_custom_days_option(): void
    {
        $this->artisan('log-tracker:cleanup', ['--days' => 30])
            ->expectsOutputToContain('Cleaned up log export files older than 30 day(s)')
            ->assertSuccessful();
    }

    #[Test]
    public function it_removes_old_export_files(): void
    {
        $oldFile = $this->exportPath.'/old_export.csv';
        File::put($oldFile, 'old data');
        touch($oldFile, time() - (86400 * 10));

        $recentFile = $this->exportPath.'/recent_export.csv';
        File::put($recentFile, 'recent data');

        $this->artisan('log-tracker:cleanup', ['--days' => 7])
            ->assertSuccessful();

        $this->assertFileDoesNotExist($oldFile);
        $this->assertFileExists($recentFile);
    }

    #[Test]
    public function it_handles_empty_export_directory(): void
    {
        $this->artisan('log-tracker:cleanup')
            ->assertSuccessful();
    }

    #[Test]
    public function it_handles_missing_export_directory(): void
    {
        if (File::exists($this->exportPath)) {
            File::deleteDirectory($this->exportPath);
        }

        $this->artisan('log-tracker:cleanup')
            ->assertSuccessful();
    }
}
