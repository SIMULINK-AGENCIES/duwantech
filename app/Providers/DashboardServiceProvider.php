<?php

namespace App\Providers;

use App\Services\Dashboard\LayoutService;
use App\Services\Dashboard\WidgetService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register dashboard configuration
        $this->mergeConfigFrom(
            config_path('dashboard.php'), 'dashboard'
        );

        // Register dashboard services as singletons
        $this->app->singleton(LayoutService::class, function ($app) {
            return new LayoutService();
        });

        $this->app->singleton(WidgetService::class, function ($app) {
            return new WidgetService();
        });

        // Register dashboard aliases
        $this->app->alias(LayoutService::class, 'dashboard.layout');
        $this->app->alias(WidgetService::class, 'dashboard.widget');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load dashboard views
        $this->loadViewsFrom(resource_path('views/admin/dashboard'), 'dashboard');

        // Register Blade components for dashboard
        $this->registerBladeComponents();

        // Register dashboard permissions
        $this->registerPermissions();

        // Publish dashboard assets if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                public_path('admin/assets') => public_path('admin/assets'),
            ], 'dashboard-assets');
        }
    }

    /**
     * Register Blade components for dashboard
     */
    protected function registerBladeComponents(): void
    {
        // Register dashboard specific Blade components
        Blade::componentNamespace('App\\View\\Components\\Admin', 'admin');
        
        // Register common dashboard directives
        Blade::directive('dashboardTheme', function ($theme) {
            return "<?php echo app('dashboard.layout')->getThemeClass({$theme}); ?>";
        });

        Blade::directive('widgetPermission', function ($permission) {
            return "<?php if(auth()->user()->can({$permission})): ?>";
        });

        Blade::directive('endWidgetPermission', function () {
            return "<?php endif; ?>";
        });
    }

    /**
     * Register dashboard permissions
     */
    protected function registerPermissions(): void
    {
        if (config('dashboard.security.require_permission')) {
            // Define dashboard permissions
            $permissions = [
                'view-dashboard',
                'view-dashboard-widgets',
                'create-dashboard-widgets',
                'update-dashboard-widgets',
                'delete-dashboard-widgets',
                'customize-dashboard-layout',
                'save-dashboard-templates',
                'share-dashboard-templates',
            ];

            // Register permission gates
            foreach ($permissions as $permission) {
                Gate::define($permission, function ($user) use ($permission) {
                    return $user->hasPermissionTo($permission);
                });
            }
        }
    }
}
