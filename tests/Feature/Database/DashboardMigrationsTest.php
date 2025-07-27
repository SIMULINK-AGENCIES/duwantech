<?php

namespace Tests\Feature\Database;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Models\DashboardLayout;
use App\Models\DashboardWidget;
use App\Models\UserWidgetConfig;

class DashboardMigrationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_tables_exist()
    {
        $this->assertTrue(Schema::hasTable('dashboard_layouts'));
        $this->assertTrue(Schema::hasTable('dashboard_widgets'));
        $this->assertTrue(Schema::hasTable('user_widget_configs'));
    }

    public function test_dashboard_layouts_table_structure()
    {
        $columns = [
            'id', 'name', 'slug', 'description', 'layout_config',
            'widget_positions', 'type', 'is_active', 'is_public',
            'created_by', 'usage_count', 'metadata', 'created_at', 'updated_at'
        ];
        
        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('dashboard_layouts', $column),
                "Column {$column} does not exist in dashboard_layouts table"
            );
        }
    }

    public function test_dashboard_widgets_table_structure()
    {
        $columns = [
            'id', 'widget_id', 'name', 'description', 'category', 'size',
            'component_path', 'default_config', 'config_schema', 'data_endpoint',
            'refresh_interval', 'is_premium', 'is_active', 'cache_enabled',
            'permissions', 'icon', 'preview_image', 'sort_order', 'metadata',
            'created_at', 'updated_at'
        ];
        
        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('dashboard_widgets', $column),
                "Column {$column} does not exist in dashboard_widgets table"
            );
        }
    }

    public function test_user_widget_configs_table_structure()
    {
        $columns = [
            'id', 'user_id', 'widget_id', 'position', 'config',
            'is_enabled', 'sort_order', 'last_accessed', 'metadata',
            'created_at', 'updated_at'
        ];
        
        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('user_widget_configs', $column),
                "Column {$column} does not exist in user_widget_configs table"
            );
        }
    }

    public function test_seeded_data_exists()
    {
        // Run seeders
        Artisan::call('db:seed', ['--class' => 'DashboardLayoutSeeder']);
        Artisan::call('db:seed', ['--class' => 'DashboardWidgetSeeder']);
        
        $this->assertGreaterThan(0, DashboardLayout::count());
        $this->assertGreaterThan(0, DashboardWidget::count());
        
        // Test specific seeded data
        $this->assertNotNull(DashboardLayout::where('slug', 'professional')->first());
        $this->assertNotNull(DashboardWidget::where('widget_id', 'revenue-kpi')->first());
    }

    public function test_model_relationships()
    {
        // Seed data first
        Artisan::call('db:seed', ['--class' => 'DashboardLayoutSeeder']);
        Artisan::call('db:seed', ['--class' => 'DashboardWidgetSeeder']);
        
        $layout = DashboardLayout::first();
        $widget = DashboardWidget::first();
        
        $this->assertInstanceOf(DashboardLayout::class, $layout);
        $this->assertInstanceOf(DashboardWidget::class, $widget);
        
        // Test that JSON fields are properly cast
        $this->assertIsArray($layout->layout_config);
        $this->assertIsArray($widget->default_config);
    }

    public function test_model_scopes()
    {
        // Seed data first
        Artisan::call('db:seed', ['--class' => 'DashboardLayoutSeeder']);
        Artisan::call('db:seed', ['--class' => 'DashboardWidgetSeeder']);
        
        // Test layout scopes
        $activeLayouts = DashboardLayout::active()->get();
        $systemLayouts = DashboardLayout::system()->get();
        
        $this->assertGreaterThan(0, $activeLayouts->count());
        $this->assertGreaterThan(0, $systemLayouts->count());
        
        // Test widget scopes
        $activeWidgets = DashboardWidget::active()->get();
        $kpiWidgets = DashboardWidget::byCategory('kpi')->get();
        
        $this->assertGreaterThan(0, $activeWidgets->count());
        $this->assertGreaterThan(0, $kpiWidgets->count());
    }

    public function test_foreign_key_constraints()
    {
        // This test ensures foreign key constraints work
        // We'll test this by trying to create invalid references
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        // Try to create a user widget config with invalid user_id
        UserWidgetConfig::create([
            'user_id' => 99999, // Non-existent user
            'widget_id' => 'revenue-kpi',
            'position' => ['x' => 0, 'y' => 0, 'width' => 3, 'height' => 2],
            'is_enabled' => true
        ]);
    }

    public function test_json_fields_validation()
    {
        Artisan::call('db:seed', ['--class' => 'DashboardLayoutSeeder']);
        
        $layout = DashboardLayout::first();
        
        // Test that JSON fields are properly handled
        $this->assertIsArray($layout->layout_config);
        $this->assertIsArray($layout->widget_positions);
        $this->assertIsArray($layout->metadata);
        
        // Test updating JSON fields
        $layout->update([
            'layout_config' => ['columns' => 16, 'gap' => 20],
            'metadata' => ['updated' => true]
        ]);
        
        $layout->refresh();
        $this->assertEquals(16, $layout->layout_config['columns']);
        $this->assertTrue($layout->metadata['updated']);
    }
}
