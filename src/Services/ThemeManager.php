<?php

namespace Kssadi\LogTracker\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use InvalidArgumentException;

class ThemeManager
{
    protected $currentTheme;
    protected $availableThemes;
    protected $themePath;

    public function __construct()
    {
        $this->themePath = __DIR__ . '/../resources/views/theme';
        $this->loadAvailableThemes();
        $this->setTheme(config('log-tracker.theme', 'LiteFlow'));
    }

    /**
     * Load available themes from filesystem
     */
    protected function loadAvailableThemes(): void
    {
        $this->availableThemes = [];
        $themeDirectories = File::directories($this->themePath);
        
        foreach ($themeDirectories as $dir) {
            $themeName = basename($dir);
            $this->availableThemes[] = $themeName;
        }
    }

    /**
     * Set the current theme
     */
    public function setTheme($theme)
    {
        if (!$this->isThemeAvailable($theme)) {
            \Log::warning("LogTracker: Invalid theme '{$theme}' configured. Available themes: " . implode(', ', $this->availableThemes));
            $theme = $this->getDefaultTheme();
        }

        $this->currentTheme = $theme;
        
        return $this;
    }

    /**
     * Get current theme name
     */
    public function getCurrentTheme(): string
    {
        return $this->currentTheme;
    }

    /**
     * Get all available themes
     */
    public function getAvailableThemes(): array
    {
        return $this->availableThemes;
    }

    /**
     * Check if theme is available
     */
    public function isThemeAvailable($theme)
    {
        return in_array($theme, $this->availableThemes);
    }

    /**
     * Get default theme
     */
    public function getDefaultTheme()
    {
        return isset($this->availableThemes[0]) ? $this->availableThemes[0] : 'LiteFlow';
    }

    /**
     * Get themed view
     */
    public function view($view, $data = array())
    {
        $viewName = $this->resolveViewName($view);
        return view($viewName, $data);
    }

    /**
     * Resolve view name with fallback
     */
    protected function resolveViewName($view)
    {
        $primaryView = "log-tracker::theme.{$this->currentTheme}.{$view}";
        
        // Check if view exists
        if (View::exists($primaryView)) {
            return $primaryView;
        }

        // Fallback to default theme
        $fallbackView = "log-tracker::theme.{$this->getDefaultTheme()}.{$view}";
        if (View::exists($fallbackView)) {
            return $fallbackView;
        }

        throw new InvalidArgumentException("View '{$view}' not found in any theme");
    }
}
