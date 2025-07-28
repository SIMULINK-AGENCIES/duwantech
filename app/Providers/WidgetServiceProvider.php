<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Dashboard\WidgetService;
use App\Contracts\Dashboard\WidgetServiceInterface;
use App\Widgets\UserActivityWidget;

class WidgetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the widget service interface to implementation
        $this->app->singleton(WidgetServiceInterface::class, function ($app) {
            return new WidgetService();
        });
        
        // Also bind the concrete class for backward compatibility
        $this->app->singleton(WidgetService::class, function ($app) {
            return $app->make(WidgetServiceInterface::class);
        });
    }

    /**
     * Bootstrap services and register widgets.
     */
    public function boot(): void
    {
        /** @var WidgetServiceInterface $widgetService */
        $widgetService = $this->app->make(WidgetServiceInterface::class);
        
        // Register core widgets
        $this->registerCoreWidgets($widgetService);
        
        // Register custom widgets
        $this->registerCustomWidgets($widgetService);
        
        // Register discovered widgets
        $this->registerDiscoveredWidgets($widgetService);
    }
    
    /**
     * Register core system widgets
     */
    private function registerCoreWidgets(WidgetServiceInterface $widgetService): void
    {
        // Only register widgets that weren't auto-discovered
        if (!$widgetService->getWidget('user_activity')) {
            // User Activity Widget (fallback registration)
            $widgetService->register('user_activity', [
                'title' => 'User Activity',
                'description' => 'Monitor user activity and engagement',
                'category' => 'users',
                'size' => ['width' => 6, 'height' => 4],
                'template' => 'admin.widgets.user-activity',
            ]);
        }
        
        // Sales Overview Widget
        if (!$widgetService->getWidget('sales_overview')) {
            $widgetService->register('sales_overview', [
                'title' => 'Sales Overview',
                'description' => 'Display sales metrics and trends',
                'category' => 'ecommerce',
                'size' => ['width' => 6, 'height' => 4],
                'template' => 'admin.widgets.sales-overview',
            ]);
        }
        
        // Recent Orders Widget
        $widgetService->register('recent_orders', [
            'title' => 'Recent Orders',
            'description' => 'Display list of recent orders',
            'category' => 'ecommerce',
            'size' => ['width' => 6, 'height' => 6],
            'permissions' => ['admin.orders.view'],
            'supports_config' => true,
            'refresh_interval' => 180,
            'tags' => ['orders', 'recent'],
            'version' => '1.0.0',
            'author' => 'DuwanTech'
        ]);
        
        // System Status Widget
        $widgetService->register('system_status', [
            'title' => 'System Status',
            'description' => 'Monitor server health and performance',
            'category' => 'system',
            'size' => ['width' => 4, 'height' => 4],
            'permissions' => ['admin.system.view'],
            'supports_config' => false,
            'refresh_interval' => 60,
            'tags' => ['system', 'health', 'monitoring'],
            'version' => '1.0.0',
            'author' => 'DuwanTech'
        ]);
        
        // Top Products Widget
        $widgetService->register('top_products', [
            'title' => 'Top Products',
            'description' => 'Best selling products by revenue or quantity',
            'category' => 'ecommerce',
            'size' => ['width' => 6, 'height' => 5],
            'permissions' => ['admin.products.view'],
            'supports_config' => true,
            'refresh_interval' => 600,
            'tags' => ['products', 'bestsellers', 'analytics'],
            'version' => '1.0.0',
            'author' => 'DuwanTech'
        ]);
        
        // User Growth Chart Widget
        $widgetService->register('user_growth_chart', [
            'title' => 'User Growth Chart',
            'description' => 'Visual chart of user registration growth',
            'category' => 'users',
            'size' => ['width' => 8, 'height' => 4],
            'permissions' => ['admin.users.view'],
            'supports_config' => true,
            'refresh_interval' => 600,
            'tags' => ['users', 'growth', 'chart'],
            'version' => '1.0.0',
            'author' => 'DuwanTech'
        ]);
        
        // Quick Actions Widget
        $widgetService->register('quick_actions', [
            'title' => 'Quick Actions',
            'description' => 'Common administrative actions and shortcuts',
            'category' => 'productivity',
            'size' => ['width' => 4, 'height' => 3],
            'permissions' => [],
            'supports_config' => true,
            'refresh_interval' => 0, // No auto-refresh needed
            'tags' => ['actions', 'productivity', 'shortcuts'],
            'version' => '1.0.0',
            'author' => 'DuwanTech'
        ]);
        
        // Notifications Widget
        $widgetService->register('notifications', [
            'title' => 'Recent Notifications',
            'description' => 'Display recent system notifications',
            'category' => 'communication',
            'size' => ['width' => 6, 'height' => 4],
            'permissions' => ['admin.notifications.view'],
            'supports_config' => true,
            'refresh_interval' => 120,
            'tags' => ['notifications', 'alerts'],
            'version' => '1.0.0',
            'author' => 'DuwanTech'
        ]);
        
        // Revenue Chart Widget
        $widgetService->register('revenue_chart', [
            'title' => 'Revenue Chart',
            'description' => 'Monthly/daily revenue visualization',
            'category' => 'analytics',
            'size' => ['width' => 12, 'height' => 5],
            'permissions' => ['admin.analytics.view'],
            'supports_config' => true,
            'refresh_interval' => 600,
            'tags' => ['revenue', 'analytics', 'chart'],
            'version' => '1.0.0',
            'author' => 'DuwanTech'
        ]);
        
        // Customer Satisfaction Widget
        $widgetService->register('customer_satisfaction', [
            'title' => 'Customer Satisfaction',
            'description' => 'Customer feedback and rating metrics',
            'category' => 'analytics',
            'size' => ['width' => 4, 'height' => 4],
            'permissions' => ['admin.analytics.view'],
            'supports_config' => false,
            'refresh_interval' => 1800,
            'tags' => ['customers', 'satisfaction', 'feedback'],
            'version' => '1.0.0',
            'author' => 'DuwanTech'
        ]);
    }
    
    /**
     * Register custom user-defined widgets
     */
    private function registerCustomWidgets(WidgetServiceInterface $widgetService): void
    {
        // Load custom widgets from database or configuration
        // This could be extended to load from a widget_definitions table
        
        $customWidgets = config('widgets.custom', []);
        
        foreach ($customWidgets as $id => $config) {
            try {
                $widgetService->register($id, $config);
            } catch (\Exception $e) {
                \Log::warning("Failed to register custom widget: {$id}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Register widgets discovered through the discovery mechanism
     */
    private function registerDiscoveredWidgets(WidgetServiceInterface $widgetService): void
    {
        // The discovery mechanism is handled in the WidgetService constructor
        // This method can be used for additional discovery logic
        
        // Example: Register widgets from external packages
        $packageWidgets = $this->discoverPackageWidgets();
        
        foreach ($packageWidgets as $id => $config) {
            try {
                $widgetService->register($id, $config);
            } catch (\Exception $e) {
                \Log::warning("Failed to register discovered widget: {$id}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Discover widgets from installed packages
     */
    private function discoverPackageWidgets(): array
    {
        $widgets = [];
        
        // Look for composer packages that provide widgets
        $composerFile = base_path('composer.json');
        if (file_exists($composerFile)) {
            $composer = json_decode(file_get_contents($composerFile), true);
            $packages = array_merge(
                array_keys($composer['require'] ?? []),
                array_keys($composer['require-dev'] ?? [])
            );
            
            foreach ($packages as $package) {
                if (strpos($package, 'widget') !== false || strpos($package, 'dashboard') !== false) {
                    // Check if package has widget definitions
                    $packagePath = base_path("vendor/{$package}");
                    $widgetFile = "{$packagePath}/widgets.json";
                    
                    if (file_exists($widgetFile)) {
                        $packageWidgets = json_decode(file_get_contents($widgetFile), true);
                        if (is_array($packageWidgets)) {
                            $widgets = array_merge($widgets, $packageWidgets);
                        }
                    }
                }
            }
        }
        
        return $widgets;
    }
}
