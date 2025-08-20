<?php

namespace Kssadi\LogTracker\Console\Commands;

use Illuminate\Console\Command;
use Kssadi\LogTracker\Services\ThemeManager;

class ThemeCommand extends Command
{
    protected $signature = 'log-tracker:theme
                           {action : The action to perform (list|current|set)}
                           {theme? : Theme name (required for set action)}';

    protected $description = 'Manage Log Tracker themes';

    protected $themeManager;

    public function __construct(ThemeManager $themeManager)
    {
        parent::__construct();
        $this->themeManager = $themeManager;
    }

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                return $this->listThemes();
            case 'current':
                return $this->showCurrentTheme();
            case 'set':
                return $this->setTheme();
            default:
                $this->error("Invalid action: {$action}. Available: list, current, set");
                return 1;
        }
    }

    protected function listThemes()
    {
        $themes = $this->themeManager->getAvailableThemes();
        $current = $this->themeManager->getCurrentTheme();

        $this->info('Available Log Tracker Themes:');
        $this->newLine();

        foreach ($themes as $theme) {
            $marker = $theme === $current ? ' ← Current' : '';
            $this->line("  • {$theme}{$marker}");
        }

        return 0;
    }

    protected function showCurrentTheme()
    {
        $current = $this->themeManager->getCurrentTheme();
        $this->info("Current Theme: {$current}");
        return 0;
    }

    protected function setTheme()
    {
        $theme = $this->argument('theme');

        if (!$theme) {
            $this->error('Theme name is required when using set action');
            return 1;
        }

        if (!$this->themeManager->isThemeAvailable($theme)) {
            $this->error("Theme '{$theme}' is not available");
            $this->call('log-tracker:theme', ['action' => 'list']);
            return 1;
        }

        // Update config file
        $configPath = config_path('log-tracker.php');
        if (file_exists($configPath)) {
            $content = file_get_contents($configPath);
            $pattern = "/'theme'\s*=>\s*'[^']*'/";
            $replacement = "'theme' => '{$theme}'";
            $newContent = preg_replace($pattern, $replacement, $content);

            if ($newContent !== $content) {
                file_put_contents($configPath, $newContent);
                $this->info("Theme set to '{$theme}' in config file");
            } else {
                $this->warn("Could not update config file. Please manually set 'theme' => '{$theme}' in config/log-tracker.php");
            }
        } else {
            $this->warn("Config file not published. Run 'php artisan vendor:publish --tag=config' first");
        }

        $this->themeManager->setTheme($theme);
        $this->info("Active theme changed to: {$theme}");

        return 0;
    }
}
