<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardWidgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $widgets = [
            // KPI Widgets
            [
                'widget_id' => 'revenue-kpi',
                'name' => 'Revenue KPI',
                'description' => 'Key revenue metrics including total revenue, growth rate, and trends',
                'category' => 'kpi',
                'size' => 'small',
                'component_path' => 'admin.widgets.revenue-kpi',
                'default_config' => json_encode([
                    'period' => 'month',
                    'show_growth' => true,
                    'currency' => 'USD',
                    'decimal_places' => 2
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'period' => ['type' => 'string', 'enum' => ['day', 'week', 'month', 'year']],
                        'show_growth' => ['type' => 'boolean'],
                        'currency' => ['type' => 'string'],
                        'decimal_places' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 4]
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/revenue-kpi',
                'refresh_interval' => 300,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-revenue']),
                'icon' => 'heroicon-o-currency-dollar',
                'preview_image' => '/images/widgets/revenue-kpi-preview.png',
                'sort_order' => 1
            ],
            [
                'widget_id' => 'orders-kpi',
                'name' => 'Orders KPI',
                'description' => 'Order volume metrics including total orders, conversion rates, and status breakdown',
                'category' => 'kpi',
                'size' => 'small',
                'component_path' => 'admin.widgets.orders-kpi',
                'default_config' => json_encode([
                    'period' => 'month',
                    'show_status_breakdown' => true,
                    'show_conversion_rate' => true
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'period' => ['type' => 'string', 'enum' => ['day', 'week', 'month', 'year']],
                        'show_status_breakdown' => ['type' => 'boolean'],
                        'show_conversion_rate' => ['type' => 'boolean']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/orders-kpi',
                'refresh_interval' => 300,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-orders']),
                'icon' => 'heroicon-o-shopping-cart',
                'preview_image' => '/images/widgets/orders-kpi-preview.png',
                'sort_order' => 2
            ],
            [
                'widget_id' => 'users-kpi',
                'name' => 'Users KPI',
                'description' => 'User registration and activity metrics including new users, active users, and retention',
                'category' => 'kpi',
                'size' => 'small',
                'component_path' => 'admin.widgets.users-kpi',
                'default_config' => json_encode([
                    'period' => 'month',
                    'show_active_users' => true,
                    'show_retention_rate' => true
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'period' => ['type' => 'string', 'enum' => ['day', 'week', 'month', 'year']],
                        'show_active_users' => ['type' => 'boolean'],
                        'show_retention_rate' => ['type' => 'boolean']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/users-kpi',
                'refresh_interval' => 600,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-users']),
                'icon' => 'heroicon-o-users',
                'preview_image' => '/images/widgets/users-kpi-preview.png',
                'sort_order' => 3
            ],

            // Chart Widgets
            [
                'widget_id' => 'revenue-chart',
                'name' => 'Revenue Chart',
                'description' => 'Interactive revenue trends chart with customizable time periods and data visualization',
                'category' => 'charts',
                'size' => 'large',
                'component_path' => 'admin.widgets.revenue-chart',
                'default_config' => json_encode([
                    'chart_type' => 'line',
                    'period' => 'month',
                    'show_comparison' => true,
                    'data_points' => 12,
                    'colors' => ['#3b82f6', '#10b981']
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'chart_type' => ['type' => 'string', 'enum' => ['line', 'bar', 'area']],
                        'period' => ['type' => 'string', 'enum' => ['day', 'week', 'month', 'year']],
                        'show_comparison' => ['type' => 'boolean'],
                        'data_points' => ['type' => 'integer', 'minimum' => 6, 'maximum' => 24],
                        'colors' => ['type' => 'array', 'items' => ['type' => 'string']]
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/revenue-chart',
                'refresh_interval' => 600,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-revenue']),
                'icon' => 'heroicon-o-chart-bar',
                'preview_image' => '/images/widgets/revenue-chart-preview.png',
                'sort_order' => 4
            ],
            [
                'widget_id' => 'sales-funnel',
                'name' => 'Sales Funnel',
                'description' => 'Conversion funnel analysis showing customer journey from leads to sales',
                'category' => 'charts',
                'size' => 'medium',
                'component_path' => 'admin.widgets.sales-funnel',
                'default_config' => json_encode([
                    'stages' => ['leads', 'prospects', 'qualified', 'closed'],
                    'show_percentages' => true,
                    'show_conversion_rates' => true
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'stages' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'show_percentages' => ['type' => 'boolean'],
                        'show_conversion_rates' => ['type' => 'boolean']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/sales-funnel',
                'refresh_interval' => 900,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-analytics']),
                'icon' => 'heroicon-o-funnel',
                'preview_image' => '/images/widgets/sales-funnel-preview.png',
                'sort_order' => 5
            ],
            [
                'widget_id' => 'traffic-sources',
                'name' => 'Traffic Sources',
                'description' => 'Website traffic source breakdown with detailed analytics and referrer information',
                'category' => 'charts',
                'size' => 'medium',
                'component_path' => 'admin.widgets.traffic-sources',
                'default_config' => json_encode([
                    'chart_type' => 'pie',
                    'period' => 'month',
                    'show_details' => true,
                    'max_sources' => 10
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'chart_type' => ['type' => 'string', 'enum' => ['pie', 'doughnut', 'bar']],
                        'period' => ['type' => 'string', 'enum' => ['day', 'week', 'month', 'year']],
                        'show_details' => ['type' => 'boolean'],
                        'max_sources' => ['type' => 'integer', 'minimum' => 5, 'maximum' => 20]
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/traffic-sources',
                'refresh_interval' => 1800,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-analytics']),
                'icon' => 'heroicon-o-globe-alt',
                'preview_image' => '/images/widgets/traffic-sources-preview.png',
                'sort_order' => 6
            ],

            // Data Widgets
            [
                'widget_id' => 'recent-orders',
                'name' => 'Recent Orders',
                'description' => 'Latest customer orders with order details, status, and quick actions',
                'category' => 'data',
                'size' => 'large',
                'component_path' => 'admin.widgets.recent-orders',
                'default_config' => json_encode([
                    'limit' => 10,
                    'show_customer_info' => true,
                    'show_status' => true,
                    'show_actions' => true,
                    'auto_refresh' => true
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'integer', 'minimum' => 5, 'maximum' => 50],
                        'show_customer_info' => ['type' => 'boolean'],
                        'show_status' => ['type' => 'boolean'],
                        'show_actions' => ['type' => 'boolean'],
                        'auto_refresh' => ['type' => 'boolean']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/recent-orders',
                'refresh_interval' => 60,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-orders']),
                'icon' => 'heroicon-o-clipboard-document-list',
                'preview_image' => '/images/widgets/recent-orders-preview.png',
                'sort_order' => 7
            ],
            [
                'widget_id' => 'top-products',
                'name' => 'Top Products',
                'description' => 'Best performing products by sales, revenue, or custom metrics',
                'category' => 'data',
                'size' => 'medium',
                'component_path' => 'admin.widgets.top-products',
                'default_config' => json_encode([
                    'limit' => 10,
                    'metric' => 'revenue',
                    'period' => 'month',
                    'show_images' => true,
                    'show_stock' => true
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'integer', 'minimum' => 5, 'maximum' => 20],
                        'metric' => ['type' => 'string', 'enum' => ['revenue', 'quantity', 'views']],
                        'period' => ['type' => 'string', 'enum' => ['day', 'week', 'month', 'year']],
                        'show_images' => ['type' => 'boolean'],
                        'show_stock' => ['type' => 'boolean']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/top-products',
                'refresh_interval' => 1800,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-products']),
                'icon' => 'heroicon-o-star',
                'preview_image' => '/images/widgets/top-products-preview.png',
                'sort_order' => 8
            ],
            [
                'widget_id' => 'customer-activity',
                'name' => 'Customer Activity',
                'description' => 'Recent customer interactions, registrations, and engagement metrics',
                'category' => 'data',
                'size' => 'medium',
                'component_path' => 'admin.widgets.customer-activity',
                'default_config' => json_encode([
                    'limit' => 15,
                    'activity_types' => ['registration', 'login', 'purchase', 'review'],
                    'show_avatars' => true,
                    'real_time' => true
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'integer', 'minimum' => 5, 'maximum' => 30],
                        'activity_types' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'show_avatars' => ['type' => 'boolean'],
                        'real_time' => ['type' => 'boolean']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/customer-activity',
                'refresh_interval' => 300,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-customers']),
                'icon' => 'heroicon-o-user-group',
                'preview_image' => '/images/widgets/customer-activity-preview.png',
                'sort_order' => 9
            ],

            // System Widgets
            [
                'widget_id' => 'system-health',
                'name' => 'System Health',
                'description' => 'System performance indicators including CPU, memory, disk usage, and response times',
                'category' => 'system',
                'size' => 'medium',
                'component_path' => 'admin.widgets.system-health',
                'default_config' => json_encode([
                    'metrics' => ['cpu', 'memory', 'disk', 'response_time'],
                    'show_alerts' => true,
                    'alert_thresholds' => [
                        'cpu' => 80,
                        'memory' => 85,
                        'disk' => 90
                    ]
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'metrics' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'show_alerts' => ['type' => 'boolean'],
                        'alert_thresholds' => ['type' => 'object']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/system-health',
                'refresh_interval' => 120,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => false, // Real-time data
                'permissions' => json_encode(['view-system']),
                'icon' => 'heroicon-o-cpu-chip',
                'preview_image' => '/images/widgets/system-health-preview.png',
                'sort_order' => 10
            ],
            [
                'widget_id' => 'activity-feed',
                'name' => 'Activity Feed',
                'description' => 'Recent system activities including user actions, system events, and notifications',
                'category' => 'system',
                'size' => 'large',
                'component_path' => 'admin.widgets.activity-feed',
                'default_config' => json_encode([
                    'limit' => 20,
                    'event_types' => ['user', 'system', 'security', 'orders'],
                    'show_timestamps' => true,
                    'show_user_info' => true,
                    'real_time' => true
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'integer', 'minimum' => 10, 'maximum' => 50],
                        'event_types' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'show_timestamps' => ['type' => 'boolean'],
                        'show_user_info' => ['type' => 'boolean'],
                        'real_time' => ['type' => 'boolean']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/activity-feed',
                'refresh_interval' => 60,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-system']),
                'icon' => 'heroicon-o-rss',
                'preview_image' => '/images/widgets/activity-feed-preview.png',
                'sort_order' => 11
            ],
            [
                'widget_id' => 'storage-usage',
                'name' => 'Storage Usage',
                'description' => 'Disk space and storage metrics with usage breakdown and alerts',
                'category' => 'system',
                'size' => 'small',
                'component_path' => 'admin.widgets.storage-usage',
                'default_config' => json_encode([
                    'show_breakdown' => true,
                    'alert_threshold' => 85,
                    'units' => 'GB',
                    'show_trends' => false
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'show_breakdown' => ['type' => 'boolean'],
                        'alert_threshold' => ['type' => 'integer', 'minimum' => 70, 'maximum' => 95],
                        'units' => ['type' => 'string', 'enum' => ['MB', 'GB', 'TB']],
                        'show_trends' => ['type' => 'boolean']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/storage-usage',
                'refresh_interval' => 3600,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-system']),
                'icon' => 'heroicon-o-server-stack',
                'preview_image' => '/images/widgets/storage-usage-preview.png',
                'sort_order' => 12
            ],

            // Notification Widgets
            [
                'widget_id' => 'alerts',
                'name' => 'System Alerts',
                'description' => 'Important system notifications and alerts requiring immediate attention',
                'category' => 'notifications',
                'size' => 'medium',
                'component_path' => 'admin.widgets.alerts',
                'default_config' => json_encode([
                    'severity_levels' => ['critical', 'warning', 'info'],
                    'limit' => 10,
                    'auto_dismiss' => false,
                    'show_actions' => true,
                    'sound_enabled' => true
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'severity_levels' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'limit' => ['type' => 'integer', 'minimum' => 5, 'maximum' => 25],
                        'auto_dismiss' => ['type' => 'boolean'],
                        'show_actions' => ['type' => 'boolean'],
                        'sound_enabled' => ['type' => 'boolean']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/alerts',
                'refresh_interval' => 30,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => false, // Real-time alerts
                'permissions' => json_encode(['view-alerts']),
                'icon' => 'heroicon-o-exclamation-triangle',
                'preview_image' => '/images/widgets/alerts-preview.png',
                'sort_order' => 13
            ],
            [
                'widget_id' => 'tasks',
                'name' => 'Pending Tasks',
                'description' => 'Tasks and to-dos requiring attention with priority and deadline tracking',
                'category' => 'notifications',
                'size' => 'medium',
                'component_path' => 'admin.widgets.tasks',
                'default_config' => json_encode([
                    'limit' => 15,
                    'priority_filter' => ['high', 'medium', 'low'],
                    'show_deadlines' => true,
                    'show_assignees' => true,
                    'group_by_priority' => true
                ]),
                'config_schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'integer', 'minimum' => 5, 'maximum' => 30],
                        'priority_filter' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'show_deadlines' => ['type' => 'boolean'],
                        'show_assignees' => ['type' => 'boolean'],
                        'group_by_priority' => ['type' => 'boolean']
                    ]
                ]),
                'data_endpoint' => '/api/dashboard/widgets/tasks',
                'refresh_interval' => 300,
                'is_premium' => false,
                'is_active' => true,
                'cache_enabled' => true,
                'permissions' => json_encode(['view-tasks']),
                'icon' => 'heroicon-o-clipboard-document-check',
                'preview_image' => '/images/widgets/tasks-preview.png',
                'sort_order' => 14
            ]
        ];

        foreach ($widgets as $widget) {
            DB::table('dashboard_widgets')->insertOrIgnore([
                'widget_id' => $widget['widget_id'],
                'name' => $widget['name'],
                'description' => $widget['description'],
                'category' => $widget['category'],
                'size' => $widget['size'],
                'component_path' => $widget['component_path'],
                'default_config' => $widget['default_config'],
                'config_schema' => $widget['config_schema'],
                'data_endpoint' => $widget['data_endpoint'],
                'refresh_interval' => $widget['refresh_interval'],
                'is_premium' => $widget['is_premium'],
                'is_active' => $widget['is_active'],
                'cache_enabled' => $widget['cache_enabled'],
                'permissions' => $widget['permissions'],
                'icon' => $widget['icon'],
                'preview_image' => $widget['preview_image'],
                'sort_order' => $widget['sort_order'],
                'metadata' => json_encode([
                    'version' => '1.0',
                    'author' => 'System',
                    'tags' => [$widget['category']]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
