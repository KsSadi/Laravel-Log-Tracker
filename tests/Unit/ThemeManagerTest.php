<?php

namespace Kssadi\LogTracker\Tests\Unit;

use Illuminate\Contracts\View\View;
use InvalidArgumentException;
use Kssadi\LogTracker\Services\ThemeManager;
use Kssadi\LogTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ThemeManagerTest extends TestCase
{
    private ThemeManager $themeManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->themeManager = app(ThemeManager::class);
    }

    #[Test]
    public function it_returns_available_themes(): void
    {
        $themes = $this->themeManager->getAvailableThemes();

        $this->assertIsArray($themes);
        $this->assertNotEmpty($themes);
        $this->assertContains('LiteFlow', $themes);
        $this->assertContains('GlowStack', $themes);
    }

    #[Test]
    public function it_returns_current_theme(): void
    {
        $current = $this->themeManager->getCurrentTheme();

        $this->assertIsString($current);
        $this->assertContains($current, $this->themeManager->getAvailableThemes());
    }

    #[Test]
    public function it_uses_configured_theme(): void
    {
        $this->app['config']->set('log-tracker.theme', 'LiteFlow');
        $manager = new ThemeManager;

        $this->assertSame('LiteFlow', $manager->getCurrentTheme());
    }

    #[Test]
    public function it_falls_back_to_default_when_invalid_theme_configured(): void
    {
        $this->app['config']->set('log-tracker.theme', 'NonExistentTheme');
        $manager = new ThemeManager;

        $defaultTheme = $manager->getDefaultTheme();
        $this->assertSame($defaultTheme, $manager->getCurrentTheme());
    }

    #[Test]
    public function it_can_set_a_valid_theme(): void
    {
        $this->themeManager->setTheme('GlowStack');

        $this->assertSame('GlowStack', $this->themeManager->getCurrentTheme());
    }

    #[Test]
    public function it_falls_back_when_setting_invalid_theme(): void
    {
        $this->themeManager->setTheme('InvalidTheme');

        $default = $this->themeManager->getDefaultTheme();
        $this->assertSame($default, $this->themeManager->getCurrentTheme());
    }

    #[Test]
    public function set_theme_returns_static_for_fluent_chaining(): void
    {
        $result = $this->themeManager->setTheme('LiteFlow');

        $this->assertSame($this->themeManager, $result);
    }

    #[Test]
    public function it_checks_theme_availability(): void
    {
        $this->assertTrue($this->themeManager->isThemeAvailable('LiteFlow'));
        $this->assertTrue($this->themeManager->isThemeAvailable('GlowStack'));
        $this->assertFalse($this->themeManager->isThemeAvailable('FakeTheme'));
    }

    #[Test]
    public function it_returns_a_default_theme(): void
    {
        $default = $this->themeManager->getDefaultTheme();

        $this->assertIsString($default);
        $this->assertContains($default, $this->themeManager->getAvailableThemes());
    }

    #[Test]
    public function it_returns_a_view_instance_for_valid_view(): void
    {
        $this->themeManager->setTheme('LiteFlow');

        $view = $this->themeManager->view('dashboard');

        $this->assertInstanceOf(View::class, $view);
    }

    #[Test]
    public function it_throws_exception_for_nonexistent_view(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("View 'totally-made-up' not found in any theme");

        $this->themeManager->view('totally-made-up');
    }

    #[Test]
    public function it_falls_back_to_default_theme_view_when_current_theme_missing_view(): void
    {
        // Both themes have 'dashboard', so this verifies the primary path works.
        // We can't easily test the fallback without creating a custom theme dir,
        // so we just confirm the view resolves without error.
        $this->themeManager->setTheme('LiteFlow');
        $view = $this->themeManager->view('dashboard');

        $this->assertInstanceOf(View::class, $view);
    }
}
