<?php

namespace Kssadi\LogTracker\Services;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View as ViewFacade;
use InvalidArgumentException;

class ThemeManager
{
    private ?string $currentTheme = null;

    private array $availableThemes = [];

    private string $themePath;

    public function __construct()
    {
        $this->themePath = __DIR__.'/../resources/views/theme';
        $this->loadAvailableThemes();
        $this->setTheme(config('log-tracker.theme', 'LiteFlow'));
    }

    /**
     * Load available themes from filesystem
     */
    private function loadAvailableThemes(): void
    {
        $themeDirectories = File::directories($this->themePath);

        foreach ($themeDirectories as $dir) {
            $themeName = basename($dir);
            $this->availableThemes[] = $themeName;
        }
    }

    /**
     * Set the current theme
     */
    public function setTheme(string $theme): static
    {
        if (! $this->isThemeAvailable($theme)) {
            Log::warning("LogTracker: Invalid theme '{$theme}' configured. Available themes: ".implode(', ', $this->availableThemes));
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
        return $this->currentTheme ?? $this->getDefaultTheme();
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
    public function isThemeAvailable(string $theme): bool
    {
        return in_array($theme, $this->availableThemes);
    }

    /**
     * Get default theme
     */
    public function getDefaultTheme(): string
    {
        return $this->availableThemes[0] ?? 'LiteFlow';
    }

    /**
     * Get themed view
     */
    public function view(string $view, array $data = []): View
    {
        $viewName = $this->resolveViewName($view);

        return view($viewName, $data);
    }

    /**
     * Resolve view name with fallback
     */
    private function resolveViewName(string $view): string
    {
        $primaryView = "log-tracker::theme.{$this->currentTheme}.{$view}";

        // Check if view exists
        if (ViewFacade::exists($primaryView)) {
            return $primaryView;
        }

        // Fallback to default theme
        $fallbackView = "log-tracker::theme.{$this->getDefaultTheme()}.{$view}";
        if (ViewFacade::exists($fallbackView)) {
            return $fallbackView;
        }

        throw new InvalidArgumentException("View '{$view}' not found in any theme");
    }
}
