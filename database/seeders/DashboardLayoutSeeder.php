<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardLayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layouts = [
            [
                'name' => 'Professional Dashboard',
                'slug' => 'professional',
                'description' => 'Clean and professional layout perfect for business environments',
                'layout_config' => json_encode([
                    'grid' => [
                        'columns' => 12,
                        'rows' => 'auto',
                        'gap' => 16,
                        'padding' => 20
                    ],
                    'responsive' => [
                        'mobile' => ['columns' => 1],
                        'tablet' => ['columns' => 6],
                        'desktop' => ['columns' => 12]
                    ],
                    'constraints' => [
                        'min_widget_width' => 2,
                        'max_widgets' => 20
                    ]
                ]),
                'widget_positions' => json_encode([
                    'revenue-kpi' => ['x' => 0, 'y' => 0, 'width' => 3, 'height' => 2],
                    'orders-kpi' => ['x' => 3, 'y' => 0, 'width' => 3, 'height' => 2],
                    'users-kpi' => ['x' => 6, 'y' => 0, 'width' => 3, 'height' => 2],
                    'system-health' => ['x' => 9, 'y' => 0, 'width' => 3, 'height' => 2],
                    'revenue-chart' => ['x' => 0, 'y' => 2, 'width' => 8, 'height' => 4],
                    'recent-orders' => ['x' => 8, 'y' => 2, 'width' => 4, 'height' => 4],
                    'top-products' => ['x' => 0, 'y' => 6, 'width' => 6, 'height' => 3],
                    'activity-feed' => ['x' => 6, 'y' => 6, 'width' => 6, 'height' => 3]
                ]),
                'type' => 'system',
                'is_active' => true,
                'is_public' => true,
                'created_by' => null,
                'usage_count' => 0,
                'metadata' => json_encode([
                    'version' => '1.0',
                    'author' => 'System',
                    'tags' => ['business', 'professional', 'clean'],
                    'preview_image' => '/images/layouts/professional-preview.png'
                ])
            ],
            [
                'name' => 'Minimal Dashboard',
                'slug' => 'minimal',
                'description' => 'Simple and clean layout with essential widgets only',
                'layout_config' => json_encode([
                    'grid' => [
                        'columns' => 8,
                        'rows' => 'auto',
                        'gap' => 12,
                        'padding' => 16
                    ],
                    'responsive' => [
                        'mobile' => ['columns' => 1],
                        'tablet' => ['columns' => 4],
                        'desktop' => ['columns' => 8]
                    ],
                    'constraints' => [
                        'min_widget_width' => 2,
                        'max_widgets' => 12
                    ]
                ]),
                'widget_positions' => json_encode([
                    'revenue-kpi' => ['x' => 0, 'y' => 0, 'width' => 2, 'height' => 2],
                    'orders-kpi' => ['x' => 2, 'y' => 0, 'width' => 2, 'height' => 2],
                    'users-kpi' => ['x' => 4, 'y' => 0, 'width' => 2, 'height' => 2],
                    'system-health' => ['x' => 6, 'y' => 0, 'width' => 2, 'height' => 2],
                    'revenue-chart' => ['x' => 0, 'y' => 2, 'width' => 8, 'height' => 3],
                    'recent-orders' => ['x' => 0, 'y' => 5, 'width' => 8, 'height' => 3]
                ]),
                'type' => 'system',
                'is_active' => true,
                'is_public' => true,
                'created_by' => null,
                'usage_count' => 0,
                'metadata' => json_encode([
                    'version' => '1.0',
                    'author' => 'System',
                    'tags' => ['minimal', 'simple', 'clean'],
                    'preview_image' => '/images/layouts/minimal-preview.png'
                ])
            ],
            [
                'name' => 'Executive Dashboard',
                'slug' => 'executive',
                'description' => 'High-level overview perfect for executives and managers',
                'layout_config' => json_encode([
                    'grid' => [
                        'columns' => 16,
                        'rows' => 'auto',
                        'gap' => 20,
                        'padding' => 24
                    ],
                    'responsive' => [
                        'mobile' => ['columns' => 1],
                        'tablet' => ['columns' => 8],
                        'desktop' => ['columns' => 16]
                    ],
                    'constraints' => [
                        'min_widget_width' => 3,
                        'max_widgets' => 25
                    ]
                ]),
                'widget_positions' => json_encode([
                    'revenue-kpi' => ['x' => 0, 'y' => 0, 'width' => 4, 'height' => 3],
                    'orders-kpi' => ['x' => 4, 'y' => 0, 'width' => 4, 'height' => 3],
                    'users-kpi' => ['x' => 8, 'y' => 0, 'width' => 4, 'height' => 3],
                    'system-health' => ['x' => 12, 'y' => 0, 'width' => 4, 'height' => 3],
                    'revenue-chart' => ['x' => 0, 'y' => 3, 'width' => 10, 'height' => 5],
                    'sales-funnel' => ['x' => 10, 'y' => 3, 'width' => 6, 'height' => 5],
                    'top-products' => ['x' => 0, 'y' => 8, 'width' => 8, 'height' => 4],
                    'customer-activity' => ['x' => 8, 'y' => 8, 'width' => 8, 'height' => 4],
                    'alerts' => ['x' => 0, 'y' => 12, 'width' => 16, 'height' => 2]
                ]),
                'type' => 'system',
                'is_active' => true,
                'is_public' => true,
                'created_by' => null,
                'usage_count' => 0,
                'metadata' => json_encode([
                    'version' => '1.0',
                    'author' => 'System',
                    'tags' => ['executive', 'overview', 'comprehensive'],
                    'preview_image' => '/images/layouts/executive-preview.png'
                ])
            ],
            [
                'name' => 'Analytics Focus',
                'slug' => 'analytics',
                'description' => 'Chart and analytics heavy layout for data-driven insights',
                'layout_config' => json_encode([
                    'grid' => [
                        'columns' => 12,
                        'rows' => 'auto',
                        'gap' => 16,
                        'padding' => 20
                    ],
                    'responsive' => [
                        'mobile' => ['columns' => 1],
                        'tablet' => ['columns' => 6],
                        'desktop' => ['columns' => 12]
                    ],
                    'constraints' => [
                        'min_widget_width' => 3,
                        'max_widgets' => 18
                    ]
                ]),
                'widget_positions' => json_encode([
                    'revenue-chart' => ['x' => 0, 'y' => 0, 'width' => 6, 'height' => 4],
                    'sales-funnel' => ['x' => 6, 'y' => 0, 'width' => 6, 'height' => 4],
                    'traffic-sources' => ['x' => 0, 'y' => 4, 'width' => 6, 'height' => 4],
                    'top-products' => ['x' => 6, 'y' => 4, 'width' => 6, 'height' => 4],
                    'revenue-kpi' => ['x' => 0, 'y' => 8, 'width' => 3, 'height' => 2],
                    'orders-kpi' => ['x' => 3, 'y' => 8, 'width' => 3, 'height' => 2],
                    'users-kpi' => ['x' => 6, 'y' => 8, 'width' => 3, 'height' => 2],
                    'system-health' => ['x' => 9, 'y' => 8, 'width' => 3, 'height' => 2]
                ]),
                'type' => 'system',
                'is_active' => true,
                'is_public' => true,
                'created_by' => null,
                'usage_count' => 0,
                'metadata' => json_encode([
                    'version' => '1.0',
                    'author' => 'System',
                    'tags' => ['analytics', 'charts', 'data'],
                    'preview_image' => '/images/layouts/analytics-preview.png'
                ])
            ]
        ];

        foreach ($layouts as $layout) {
            DB::table('dashboard_layouts')->insertOrIgnore([
                'name' => $layout['name'],
                'slug' => $layout['slug'],
                'description' => $layout['description'],
                'layout_config' => $layout['layout_config'],
                'widget_positions' => $layout['widget_positions'],
                'type' => $layout['type'],
                'is_active' => $layout['is_active'],
                'is_public' => $layout['is_public'],
                'created_by' => $layout['created_by'],
                'usage_count' => $layout['usage_count'],
                'metadata' => $layout['metadata'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
