<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Cache;

class WidgetService
{
    protected array $registeredWidgets = [];
    
    /**
     * Register a widget
     */
    public function register(string $id, array $config): void
    {
        $this->registeredWidgets[$id] = array_merge([
            'id' => $id,
            'component' => "components.admin.widgets.{$id}",
            'size' => 'medium',
            'category' => 'general',
            'permissions' => [],
            'refresh_interval' => 60000,
            'cacheable' => true,
        ], $config);
    }
    
    /**
     * Get all available widgets
     */
    public function getAvailable(): array
    {
        return $this->registeredWidgets;
    }
    
    /**
     * Get widgets by category
     */
    public function getByCategory(string $category): array
    {
        return array_filter($this->registeredWidgets, function ($widget) use ($category) {
            return $widget['category'] === $category;
        });
    }
    
    /**
     * Get widget configuration
     */
    public function getWidget(string $id): ?array
    {
        return $this->registeredWidgets[$id] ?? null;
    }
    
    /**
     * Initialize default widgets
     */
    public function initializeDefaults(): void
    {
        // KPI Widgets
        $this->register('revenue-kpi', [
            'name' => 'Revenue KPI',
            'description' => 'Key revenue metrics and trends',
            'category' => 'kpi',
            'size' => 'small',
            'data_endpoint' => '/api/widgets/revenue-kpi'
        ]);
        
        $this->register('orders-kpi', [
            'name' => 'Orders KPI', 
            'description' => 'Order volume and status metrics',
            'category' => 'kpi',
            'size' => 'small',
            'data_endpoint' => '/api/widgets/orders-kpi'
        ]);
        
        // Chart Widgets
        $this->register('revenue-chart', [
            'name' => 'Revenue Chart',
            'description' => 'Revenue trends over time',
            'category' => 'charts',
            'size' => 'large',
            'data_endpoint' => '/api/widgets/revenue-chart'
        ]);
        
        $this->register('sales-funnel', [
            'name' => 'Sales Funnel',
            'description' => 'Conversion funnel analysis',
            'category' => 'charts', 
            'size' => 'medium',
            'data_endpoint' => '/api/widgets/sales-funnel'
        ]);
        
        // Data Widgets
        $this->register('recent-orders', [
            'name' => 'Recent Orders',
            'description' => 'Latest customer orders',
            'category' => 'data',
            'size' => 'large',
            'data_endpoint' => '/api/widgets/recent-orders'
        ]);
        
        $this->register('top-products', [
            'name' => 'Top Products',
            'description' => 'Best performing products',
            'category' => 'data',
            'size' => 'medium',
            'data_endpoint' => '/api/widgets/top-products'
        ]);
        
        // System Widgets
        $this->register('system-health', [
            'name' => 'System Health',
            'description' => 'System performance indicators',
            'category' => 'system',
            'size' => 'medium',
            'data_endpoint' => '/api/widgets/system-health'
        ]);
        
        $this->register('activity-feed', [
            'name' => 'Activity Feed',
            'description' => 'Recent system activities',
            'category' => 'system',
            'size' => 'large',
            'data_endpoint' => '/api/widgets/activity-feed'
        ]);
    }
}
