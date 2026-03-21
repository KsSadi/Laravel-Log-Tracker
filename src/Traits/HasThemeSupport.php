<?php

namespace Kssadi\LogTracker\Traits;

use Illuminate\Contracts\View\View;
use Kssadi\LogTracker\Services\ThemeManager;

trait HasThemeSupport
{
    protected ?ThemeManager $themeManager = null;

    /**
     * Lazily resolve the ThemeManager singleton.
     */
    private function resolveThemeManager(): ThemeManager
    {
        return $this->themeManager ??= app(ThemeManager::class);
    }

    /**
     * Get themed view.
     */
    protected function themedView(string $view, array $data = []): View
    {
        return $this->resolveThemeManager()->view($view, $data);
    }

    /**
     * Get current theme.
     */
    protected function getCurrentTheme(): string
    {
        return $this->resolveThemeManager()->getCurrentTheme();
    }

    /**
     * Get available themes for selection.
     */
    protected function getAvailableThemes(): array
    {
        return $this->resolveThemeManager()->getAvailableThemes();
    }
}
