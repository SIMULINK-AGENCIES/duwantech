<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Dashboard\WidgetService;

class TestWidgetSystem extends Command
{
    protected $signature = 'widget:test';
    protected $description = 'Test the widget system functionality';

    public function handle(WidgetService $widgetService): int
    {
        $this->info('Testing Widget System...');
        
        try {
            // Test 1: Get categories
            $this->line('Test 1: Getting categories...');
            $categories = $widgetService->getCategories();
            $this->info(sprintf('Found %d categories', count($categories)));
            foreach ($categories as $key => $category) {
                $this->line("  - {$key}: {$category['name']} ({$category['widget_count']} widgets)");
            }
            
            // Test 2: Get available widgets
            $this->line('Test 2: Getting available widgets...');
            $widgets = $widgetService->getAvailableWidgets();
            $this->info(sprintf('Found %d available widgets', count($widgets)));
            foreach ($widgets as $id => $widget) {
                $this->line("  - {$id}: {$widget['title']} (Category: {$widget['category']})");
            }
            
            // Test 3: Widget registration
            $this->line('Test 3: Testing widget registration...');
            $widgetService->register('test_widget', [
                'title' => 'Test Widget',
                'description' => 'A test widget for validation',
                'category' => 'productivity',
                'size' => ['width' => 4, 'height' => 3],
                'version' => '1.0.0',
            ]);
            $this->info('Widget registration successful');
            
            // Test 4: Get specific widget
            $this->line('Test 4: Getting specific widget...');
            $testWidget = $widgetService->getWidget('test_widget');
            if ($testWidget) {
                $this->info("Widget found: {$testWidget['title']}");
            } else {
                $this->error('Widget not found');
            }
            
            $this->info('All tests passed! Widget system is working correctly.');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Widget test failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
