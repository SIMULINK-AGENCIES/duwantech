<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Widget Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for the dashboard widget system
    |
    */

    // Widget discovery settings
    'discovery' => [
        'enabled' => true,
        'paths' => [
            app_path('Widgets'),
            resource_path('views/admin/widgets'),
            base_path('packages/*/widgets'),
        ],
        'cache_duration' => 3600, // 1 hour
    ],

    // Widget categories
    'categories' => [
        'analytics' => [
            'name' => 'Analytics & Reports',
            'description' => 'Data visualization and reporting widgets',
            'icon' => 'chart-bar',
            'color' => 'blue',
            'permissions' => ['admin.analytics.view'],
            'sort_order' => 1
        ],
        'ecommerce' => [
            'name' => 'E-Commerce',
            'description' => 'Sales, orders, and product management widgets',
            'icon' => 'shopping-cart',
            'color' => 'green',
            'permissions' => ['admin.orders.view', 'admin.products.view'],
            'sort_order' => 2
        ],
        'users' => [
            'name' => 'User Management',
            'description' => 'User activity and management widgets',
            'icon' => 'users',
            'color' => 'purple',
            'permissions' => ['admin.users.view'],
            'sort_order' => 3
        ],
        'system' => [
            'name' => 'System Monitoring',
            'description' => 'Server status and system health widgets',
            'icon' => 'server',
            'color' => 'red',
            'permissions' => ['admin.system.view'],
            'sort_order' => 4
        ],
        'content' => [
            'name' => 'Content Management',
            'description' => 'Content creation and management widgets',
            'icon' => 'document-text',
            'color' => 'yellow',
            'permissions' => ['admin.content.view'],
            'sort_order' => 5
        ],
        'communication' => [
            'name' => 'Communication',
            'description' => 'Messaging, notifications, and communication widgets',
            'icon' => 'mail',
            'color' => 'indigo',
            'permissions' => ['admin.notifications.view'],
            'sort_order' => 6
        ],
        'productivity' => [
            'name' => 'Productivity',
            'description' => 'Task management and productivity widgets',
            'icon' => 'clipboard-list',
            'color' => 'gray',
            'permissions' => [],
            'sort_order' => 7
        ],
        'custom' => [
            'name' => 'Custom Widgets',
            'description' => 'User-defined and third-party widgets',
            'icon' => 'puzzle-piece',
            'color' => 'pink',
            'permissions' => ['admin.widgets.manage'],
            'sort_order' => 8
        ]
    ],

    // Default widget settings
    'defaults' => [
        'cache_duration' => 300, // 5 minutes
        'refresh_interval' => 300, // 5 minutes
        'supports_resize' => true,
        'supports_config' => false,
        'version' => '1.0.0',
        'author' => 'System',
    ],

    // Widget size constraints
    'size_constraints' => [
        'min_width' => 1,
        'max_width' => 12,
        'min_height' => 1,
        'max_height' => 8,
        'grid_columns' => 12,
    ],

    // Performance settings
    'performance' => [
        'cache_enabled' => true,
        'lazy_loading' => true,
        'max_widgets_per_user' => 20,
        'max_concurrent_refreshes' => 5,
    ],

    // Security settings
    'security' => [
        'validate_permissions' => true,
        'check_feature_flags' => true,
        'audit_widget_access' => true,
    ],

    // Custom widget definitions
    'custom' => [
        // Add custom widget configurations here
        // Example:
        // 'my_custom_widget' => [
        //     'title' => 'My Custom Widget',
        //     'description' => 'A custom widget for my needs',
        //     'category' => 'custom',
        //     'size' => ['width' => 4, 'height' => 3],
        //     'permissions' => ['admin.custom.view'],
        //     'template' => 'admin.widgets.my-custom-widget',
        // ]
    ],

    // Feature flags for widget functionality
    'features' => [
        'discovery_enabled' => env('WIDGET_DISCOVERY_ENABLED', true),
        'custom_widgets_enabled' => env('CUSTOM_WIDGETS_ENABLED', true),
        'widget_marketplace' => env('WIDGET_MARKETPLACE_ENABLED', false),
        'real_time_updates' => env('WIDGET_REAL_TIME_UPDATES', true),
    ],

    // API settings for widget data
    'api' => [
        'rate_limit' => [
            'enabled' => true,
            'max_requests' => 100,
            'per_minutes' => 1,
        ],
        'authentication' => [
            'required' => true,
            'token_expiry' => 3600, // 1 hour
        ],
    ],

    // Development settings
    'development' => [
        'debug_mode' => env('WIDGET_DEBUG', false),
        'hot_reload' => env('WIDGET_HOT_RELOAD', false),
        'error_reporting' => env('WIDGET_ERROR_REPORTING', true),
    ],

];
