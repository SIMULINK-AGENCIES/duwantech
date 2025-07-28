<?php

namespace Tests\Unit\Services\Dashboard;

use Tests\TestCase;
use App\Services\Dashboard\WidgetService;
use App\Services\Dashboard\ThemeService;
use App\Services\Dashboard\ConfigurationService;
use App\Contracts\Dashboard\WidgetServiceInterface;
use App\Contracts\Dashboard\ThemeServiceInterface;
use App\Contracts\Dashboard\ConfigurationServiceInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardServicesIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_widget_service_interface_resolution()
    {
        $service = app(WidgetServiceInterface::class);
        $this->assertInstanceOf(WidgetService::class, $service);
    }

    public function test_theme_service_interface_resolution()
    {
        $service = app(ThemeServiceInterface::class);
        $this->assertInstanceOf(ThemeService::class, $service);
    }

    public function test_configuration_service_interface_resolution()
    {
        $service = app(ConfigurationServiceInterface::class);
        $this->assertInstanceOf(ConfigurationService::class, $service);
    }

    public function test_widget_service_basic_functionality()
    {
        $service = app(WidgetServiceInterface::class);
        
        // Test getting available widgets
        $widgets = $service->getAvailableWidgets();
        $this->assertIsArray($widgets);
        $this->assertNotEmpty($widgets);
        
        // Test getting user widgets
        $userWidgets = $service->getUserWidgets($this->user->id);
        $this->assertIsArray($userWidgets);
        
        // Test getting categories
        $categories = $service->getCategories();
        $this->assertIsArray($categories);
        $this->assertContains('kpi', $categories);
    }

    public function test_theme_service_basic_functionality()
    {
        $service = app(ThemeServiceInterface::class);
        
        // Test getting available themes
        $themes = $service->getAvailableThemes();
        $this->assertIsArray($themes);
        $this->assertArrayHasKey('professional', $themes);
        $this->assertArrayHasKey('light', $themes);
        $this->assertArrayHasKey('dark', $themes);
        
        // Test getting user theme
        $userTheme = $service->getUserTheme($this->user->id);
        $this->assertIsArray($userTheme);
        $this->assertArrayHasKey('name', $userTheme);
        
        // Test default theme
        $defaultTheme = $service->getDefaultTheme();
        $this->assertIsArray($defaultTheme);
        $this->assertEquals('professional', $defaultTheme['name']);
    }

    public function test_configuration_service_basic_functionality()
    {
        $service = app(ConfigurationServiceInterface::class);
        
        // Test getting user configuration
        $config = $service->getUserConfiguration($this->user->id);
        $this->assertIsArray($config);
        $this->assertArrayHasKey('layout', $config);
        $this->assertArrayHasKey('display', $config);
        $this->assertArrayHasKey('notifications', $config);
        
        // Test default configuration
        $defaultConfig = $service->getDefaultConfiguration();
        $this->assertIsArray($defaultConfig);
        
        // Test global settings
        $globalSettings = $service->getGlobalSettings();
        $this->assertIsArray($globalSettings);
        
        // Test get/set functionality
        $testValue = $service->get('maintenance_mode', true);
        $this->assertIsBool($testValue);
    }

    public function test_services_cache_clearing()
    {
        $widgetService = app(WidgetServiceInterface::class);
        $themeService = app(ThemeServiceInterface::class);
        $configService = app(ConfigurationServiceInterface::class);
        
        // These should not throw exceptions
        $widgetService->clearUserCache($this->user->id);
        $themeService->clearUserThemeCache($this->user->id);
        $configService->clearCache($this->user->id);
        
        $this->assertTrue(true); // If we reach here, no exceptions were thrown
    }

    public function test_services_validation_functionality()
    {
        $widgetService = app(WidgetServiceInterface::class);
        $themeService = app(ThemeServiceInterface::class);
        $configService = app(ConfigurationServiceInterface::class);
        
        // Test widget validation with empty array (should pass)
        $widgetErrors = $widgetService->validateWidgetConfig([]);
        $this->assertIsArray($widgetErrors);
        
        // Test theme validation with valid theme
        $themeErrors = $themeService->validateTheme('professional');
        $this->assertIsArray($themeErrors);
        
        // Test configuration validation with empty config
        $configErrors = $configService->validateConfiguration([]);
        $this->assertIsArray($configErrors);
    }

    public function test_services_analytics_functionality()
    {
        $widgetService = app(WidgetServiceInterface::class);
        $themeService = app(ThemeServiceInterface::class);
        
        // Test widget analytics
        $widgetAnalytics = $widgetService->getUsageAnalytics();
        $this->assertIsArray($widgetAnalytics);
        $this->assertArrayHasKey('total_widgets', $widgetAnalytics);
        
        // Test theme statistics
        $themeStats = $themeService->getThemeStatistics();
        $this->assertIsArray($themeStats);
        $this->assertArrayHasKey('total_themes', $themeStats);
    }
}
