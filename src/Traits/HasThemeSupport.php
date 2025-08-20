<?php

namespace Kssadi\LogTracker\Traits;

use Kssadi\LogTracker\Services\ThemeManager;

trait HasThemeSupport
{
    protected ThemeManager $themeManager;

    /**
     * Initialize theme support
     */
    protected function initializeTheme(): void
    {
        $this->themeManager = app(ThemeManager::class);
    }

    /**
     * Get themed view
     */
    protected function themedView($view, $data = array())
    {
        if (!isset($this->themeManager)) {
            $this->initializeTheme();
        }

        return $this->themeManager->view($view, $data);
    }

    /**
     * Get current theme
     */
    protected function getCurrentTheme(): string
    {
        if (!isset($this->themeManager)) {
            $this->initializeTheme();
        }

        return $this->themeManager->getCurrentTheme();
    }

    /**
     * Get available themes for selection
     */
    protected function getAvailableThemes(): array
    {
        if (!isset($this->themeManager)) {
            $this->initializeTheme();
        }

        return $this->themeManager->getAvailableThemes();
    }
}
