<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Professional Admin Dashboard Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all the configuration options for the professional
    | admin dashboard system including layouts, widgets, themes, and features.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Dashboard Settings
    |--------------------------------------------------------------------------
    */
    'enabled' => env('DASHBOARD_ENABLED', true),
    'cache_ttl' => env('DASHBOARD_CACHE_TTL', 3600), // 1 hour
    'debug' => env('DASHBOARD_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Layout Configuration
    |--------------------------------------------------------------------------
    */
    'layout' => [
        'default_template' => 'professional',
        'grid_columns' => 12,
        'max_widgets_per_row' => 6,
        'responsive_breakpoints' => [
            'sm' => 640,
            'md' => 768,
            'lg' => 1024,
            'xl' => 1280,
            '2xl' => 1536,
        ],
        'available_templates' => [
            'professional' => [
                'name' => 'Professional',
                'description' => 'Clean and modern professional layout',
                'columns' => 4,
                'sidebar' => true,
                'header' => true,
            ],
            'minimal' => [
                'name' => 'Minimal',
                'description' => 'Simple and clean minimal layout',
                'columns' => 3,
                'sidebar' => false,
                'header' => true,
            ],
            'executive' => [
                'name' => 'Executive',
                'description' => 'Executive dashboard with key metrics focus',
                'columns' => 2,
                'sidebar' => true,
                'header' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Configuration
    |--------------------------------------------------------------------------
    */
    'widgets' => [
        'auto_refresh_interval' => env('DASHBOARD_REFRESH_INTERVAL', 30000), // 30 seconds
        'max_widgets_per_user' => env('DASHBOARD_MAX_WIDGETS', 20),
        'default_widget_size' => [
            'width' => 4,
            'height' => 6,
        ],
        'categories' => [
            'analytics' => [
                'name' => 'Analytics',
                'icon' => 'chart-bar',
                'color' => 'blue',
            ],
            'sales' => [
                'name' => 'Sales',
                'icon' => 'currency-dollar',
                'color' => 'green',
            ],
            'users' => [
                'name' => 'Users',
                'icon' => 'users',
                'color' => 'purple',
            ],
            'system' => [
                'name' => 'System',
                'icon' => 'cog',
                'color' => 'gray',
            ],
            'marketing' => [
                'name' => 'Marketing',
                'icon' => 'megaphone',
                'color' => 'orange',
            ],
            'inventory' => [
                'name' => 'Inventory',
                'icon' => 'cube',
                'color' => 'indigo',
            ],
        ],
        'default_widgets' => [
            'revenue_overview',
            'sales_chart',
            'user_activity',
            'top_products',
            'recent_orders',
            'system_status',
            'quick_stats',
            'performance_metrics',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    */
    'themes' => [
        'default' => env('DASHBOARD_DEFAULT_THEME', 'light'),
        'available' => [
            'light' => [
                'name' => 'Light Theme',
                'description' => 'Clean light theme',
                'colors' => [
                    'primary' => '#3b82f6',
                    'secondary' => '#64748b',
                    'success' => '#10b981',
                    'warning' => '#f59e0b',
                    'danger' => '#ef4444',
                    'info' => '#06b6d4',
                    'background' => '#ffffff',
                    'surface' => '#f8fafc',
                    'text' => '#1e293b',
                ],
            ],
            'dark' => [
                'name' => 'Dark Theme',
                'description' => 'Modern dark theme',
                'colors' => [
                    'primary' => '#3b82f6',
                    'secondary' => '#64748b',
                    'success' => '#10b981',
                    'warning' => '#f59e0b',
                    'danger' => '#ef4444',
                    'info' => '#06b6d4',
                    'background' => '#0f172a',
                    'surface' => '#1e293b',
                    'text' => '#f1f5f9',
                ],
            ],
            'professional' => [
                'name' => 'Professional Theme',
                'description' => 'Corporate professional theme',
                'colors' => [
                    'primary' => '#1e3a8a',
                    'secondary' => '#475569',
                    'success' => '#059669',
                    'warning' => '#d97706',
                    'danger' => '#dc2626',
                    'info' => '#0891b2',
                    'background' => '#ffffff',
                    'surface' => '#f7f9fc',
                    'text' => '#1e293b',
                ],
            ],
        ],
        'allow_custom_themes' => env('DASHBOARD_ALLOW_CUSTOM_THEMES', true),
        'theme_storage' => 'database', // 'database' or 'cache'
    ],

    /*
    |--------------------------------------------------------------------------
    | Real-time Features
    |--------------------------------------------------------------------------
    */
    'realtime' => [
        'enabled' => env('DASHBOARD_REALTIME_ENABLED', true),
        'driver' => env('DASHBOARD_REALTIME_DRIVER', 'pusher'), // 'pusher', 'redis', 'null'
        'channels' => [
            'dashboard_updates' => 'dashboard.updates',
            'user_activity' => 'dashboard.activity',
            'system_alerts' => 'dashboard.alerts',
            'notifications' => 'dashboard.notifications',
        ],
        'polling_fallback' => [
            'enabled' => true,
            'interval' => 10000, // 10 seconds
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'cache_enabled' => env('DASHBOARD_CACHE_ENABLED', true),
        'cache_driver' => env('DASHBOARD_CACHE_DRIVER', 'redis'),
        'lazy_loading' => env('DASHBOARD_LAZY_LOADING', true),
        'data_compression' => env('DASHBOARD_DATA_COMPRESSION', true),
        'asset_optimization' => [
            'minify_css' => env('DASHBOARD_MINIFY_CSS', true),
            'minify_js' => env('DASHBOARD_MINIFY_JS', true),
            'combine_assets' => env('DASHBOARD_COMBINE_ASSETS', true),
        ],
        'query_optimization' => [
            'use_indexes' => true,
            'eager_loading' => true,
            'query_cache' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */
    'security' => [
        'require_permission' => env('DASHBOARD_REQUIRE_PERMISSION', true),
        'default_permission' => 'view-dashboard',
        'widget_permissions' => [
            'view' => 'view-dashboard-widgets',
            'create' => 'create-dashboard-widgets',
            'update' => 'update-dashboard-widgets',
            'delete' => 'delete-dashboard-widgets',
        ],
        'layout_permissions' => [
            'customize' => 'customize-dashboard-layout',
            'save_templates' => 'save-dashboard-templates',
            'share_templates' => 'share-dashboard-templates',
        ],
        'csrf_protection' => true,
        'rate_limiting' => [
            'enabled' => true,
            'max_requests_per_minute' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */
    'api' => [
        'enabled' => env('DASHBOARD_API_ENABLED', true),
        'prefix' => 'api/dashboard',
        'middleware' => ['auth:sanctum', 'throttle:60,1'],
        'versioning' => [
            'enabled' => true,
            'default_version' => 'v1',
            'header_name' => 'Accept-Version',
        ],
        'documentation' => [
            'enabled' => env('DASHBOARD_API_DOCS', true),
            'path' => '/admin/dashboard/api/docs',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    */
    'export' => [
        'enabled' => env('DASHBOARD_EXPORT_ENABLED', true),
        'formats' => ['pdf', 'excel', 'csv', 'json'],
        'storage_disk' => 'local',
        'temp_directory' => 'dashboard/exports',
        'cleanup_after' => 24, // hours
        'pdf_settings' => [
            'format' => 'A4',
            'orientation' => 'portrait',
            'margin' => [
                'top' => 10,
                'right' => 10,
                'bottom' => 10,
                'left' => 10,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled' => env('DASHBOARD_NOTIFICATIONS_ENABLED', true),
        'channels' => ['database', 'broadcast'],
        'types' => [
            'system_alerts' => [
                'enabled' => true,
                'sound' => true,
                'persist' => true,
            ],
            'performance_alerts' => [
                'enabled' => true,
                'sound' => false,
                'persist' => true,
            ],
            'user_activity' => [
                'enabled' => true,
                'sound' => false,
                'persist' => false,
            ],
        ],
        'cleanup' => [
            'enabled' => true,
            'keep_days' => 30,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Configuration
    |--------------------------------------------------------------------------
    */
    'analytics' => [
        'enabled' => env('DASHBOARD_ANALYTICS_ENABLED', true),
        'tracking' => [
            'user_interactions' => true,
            'widget_usage' => true,
            'performance_metrics' => true,
            'error_tracking' => true,
        ],
        'retention_period' => 90, // days
        'aggregation_intervals' => ['hourly', 'daily', 'weekly', 'monthly'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Customization Options
    |--------------------------------------------------------------------------
    */
    'customization' => [
        'allow_widget_creation' => env('DASHBOARD_ALLOW_WIDGET_CREATION', false),
        'allow_layout_sharing' => env('DASHBOARD_ALLOW_LAYOUT_SHARING', true),
        'allow_theme_customization' => env('DASHBOARD_ALLOW_THEME_CUSTOMIZATION', true),
        'max_custom_widgets' => env('DASHBOARD_MAX_CUSTOM_WIDGETS', 5),
        'widget_code_validation' => [
            'enabled' => true,
            'allowed_functions' => [],
            'blocked_functions' => ['exec', 'shell_exec', 'system', 'passthru'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup and Recovery
    |--------------------------------------------------------------------------
    */
    'backup' => [
        'enabled' => env('DASHBOARD_BACKUP_ENABLED', true),
        'frequency' => 'daily',
        'retention_days' => 30,
        'storage_disk' => 'backups',
        'include' => [
            'layouts' => true,
            'widgets' => true,
            'themes' => true,
            'user_preferences' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    */
    'development' => [
        'debug_mode' => env('DASHBOARD_DEBUG_MODE', false),
        'profiling' => env('DASHBOARD_PROFILING', false),
        'mock_data' => env('DASHBOARD_MOCK_DATA', false),
        'widget_hot_reload' => env('DASHBOARD_HOT_RELOAD', false),
    ],
];
