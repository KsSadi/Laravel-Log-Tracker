<?php

namespace Kssadi\LogTracker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Kssadi\LogTracker\Services\ThemeManager;

class ThemeCommand extends Command
{
    protected $signature = 'log-tracker:theme
                           {action : The action to perform (list|current|set)}
                           {theme? : Theme name (required for set action)}';

    protected $description = 'Manage Log Tracker themes';

    public function __construct(private ThemeManager $themeManager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $action = $this->argument('action');

        if (! in_array($action, ['list', 'current', 'set'])) {
            $this->error("Invalid action: {$action}. Available: list, current, set");

            return self::FAILURE;
        }

        return match ($action) {
            'list' => $this->listThemes(),
            'current' => $this->showCurrentTheme(),
            'set' => $this->setTheme(),
        };
    }

    private function listThemes(): int
    {
        $themes = $this->themeManager->getAvailableThemes();
        $current = $this->themeManager->getCurrentTheme();

        $this->info('Available Log Tracker Themes:');
        $this->newLine();

        foreach ($themes as $theme) {
            $marker = $theme === $current ? ' ← Current' : '';
            $this->line("  • {$theme}{$marker}");
        }

        return self::SUCCESS;
    }

    private function showCurrentTheme(): int
    {
        $current = $this->themeManager->getCurrentTheme();
        $this->info("Current Theme: {$current}");

        return self::SUCCESS;
    }

    private function setTheme(): int
    {
        $theme = $this->argument('theme');

        if (! $theme) {
            $this->error('Theme name is required when using set action');

            return self::FAILURE;
        }

        if (! $this->themeManager->isThemeAvailable($theme)) {
            $this->error("Theme '{$theme}' is not available");
            $this->call('log-tracker:theme', ['action' => 'list']);

            return self::FAILURE;
        }

        // Update config file
        $configPath = config_path('log-tracker.php');
        if (File::exists($configPath)) {
            $content = File::get($configPath);
            $pattern = "/'theme'\s*=>\s*'[^']*'/";
            $replacement = "'theme' => '{$theme}'";
            $newContent = preg_replace($pattern, $replacement, $content);

            if ($newContent !== $content) {
                File::put($configPath, $newContent);
                $this->info("Theme set to '{$theme}' in config file");
            } else {
                $this->warn("Could not update config file. Please manually set 'theme' => '{$theme}' in config/log-tracker.php");
            }
        } else {
            $this->warn("Config file not published. Run 'php artisan vendor:publish --tag=config' first");
        }

        $this->themeManager->setTheme($theme);
        $this->info("Active theme changed to: {$theme}");

        return self::SUCCESS;
    }
}
