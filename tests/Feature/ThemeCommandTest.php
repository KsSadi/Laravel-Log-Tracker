<?php

namespace Kssadi\LogTracker\Tests\Feature;

use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ThemeCommandTest extends TestCase
{
    #[Test]
    public function it_lists_available_themes(): void
    {
        $this->artisan('log-tracker:theme', ['action' => 'list'])
            ->expectsOutputToContain('Available Log Tracker Themes')
            ->expectsOutputToContain('LiteFlow')
            ->expectsOutputToContain('GlowStack')
            ->assertSuccessful();
    }

    #[Test]
    public function it_shows_current_theme(): void
    {
        $this->artisan('log-tracker:theme', ['action' => 'current'])
            ->expectsOutputToContain('Current Theme:')
            ->assertSuccessful();
    }

    #[Test]
    public function it_fails_for_invalid_action(): void
    {
        $this->artisan('log-tracker:theme', ['action' => 'invalid'])
            ->expectsOutputToContain('Invalid action')
            ->assertFailed();
    }

    #[Test]
    public function it_fails_to_set_theme_without_theme_name(): void
    {
        $this->artisan('log-tracker:theme', ['action' => 'set'])
            ->expectsOutputToContain('Theme name is required')
            ->assertFailed();
    }

    #[Test]
    public function it_fails_to_set_unavailable_theme(): void
    {
        $this->artisan('log-tracker:theme', ['action' => 'set', 'theme' => 'NonExistent'])
            ->expectsOutputToContain("Theme 'NonExistent' is not available")
            ->assertFailed();
    }

    #[Test]
    public function it_sets_valid_theme_when_config_published(): void
    {
        $configPath = config_path('log-tracker.php');
        $configDir = dirname($configPath);

        if (! File::exists($configDir)) {
            File::makeDirectory($configDir, 0755, true);
        }

        File::put($configPath, "<?php\nreturn [\n    'theme' => 'LiteFlow',\n];\n");

        try {
            $this->artisan('log-tracker:theme', ['action' => 'set', 'theme' => 'GlowStack'])
                ->expectsOutputToContain('Active theme changed to: GlowStack')
                ->assertSuccessful();

            $content = File::get($configPath);
            $this->assertStringContainsString("'theme' => 'GlowStack'", $content);
        } finally {
            File::delete($configPath);
        }
    }

    #[Test]
    public function it_warns_when_config_not_published(): void
    {
        $configPath = config_path('log-tracker.php');

        if (File::exists($configPath)) {
            File::delete($configPath);
        }

        $this->artisan('log-tracker:theme', ['action' => 'set', 'theme' => 'GlowStack'])
            ->expectsOutputToContain('Active theme changed to: GlowStack')
            ->assertSuccessful();
    }
}
