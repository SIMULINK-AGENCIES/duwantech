<?php

namespace App\Examples\WidgetDependencySystem;

use App\Services\Dashboard\WidgetService;
use App\Services\Dashboard\WidgetConfigurationSchema;
use App\Services\Dashboard\WidgetDependencyManager;
use App\Services\Dashboard\WidgetConfigurationManager;

/**
 * Example usage of the Widget Dependency Management System
 * This file demonstrates practical implementation scenarios
 */
class WidgetDependencyExamples
{
    private WidgetService $widgetService;
    
    public function __construct()
    {
        $configSchema = new WidgetConfigurationSchema();
        $dependencyManager = new WidgetDependencyManager();
        $configManager = new WidgetConfigurationManager($configSchema);
        
        $this->widgetService = new WidgetService($configSchema, $dependencyManager, $configManager);
    }
    
    /**
     * Example 1: Basic Widget Registration with Simple Dependencies
     */
    public function basicDependencyExample()
    {
        // Register base utility widget
        $this->widgetService->register('data_formatter', [
            'title' => 'Data Formatter Utility',
            'description' => 'Provides data formatting functions',
            'category' => 'utilities',
            'version' => '1.0.0',
            'dependencies' => [] // No dependencies
        ]);
        
        // Register chart widget that depends on data formatter
        $this->widgetService->register('basic_chart', [
            'title' => 'Basic Chart Widget',
            'description' => 'Simple chart visualization',
            'category' => 'charts',
            'version' => '2.1.0',
            'dependencies' => ['data_formatter'] // Simple dependency
        ]);
        
        echo "✅ Basic widgets registered successfully\n";
    }
    
    /**
     * Example 2: Complex Dependencies with Version Constraints
     */
    public function complexDependencyExample()
    {
        // Register analytics core with specific version
        $this->widgetService->register('analytics_core', [
            'title' => 'Analytics Core Engine',
            'description' => 'Core analytics processing',
            'category' => 'analytics',
            'version' => '3.2.1',
            'permissions' => ['read_analytics', 'process_data'],
            'dependencies' => ['data_formatter']
        ]);
        
        // Register advanced dashboard with complex dependencies
        $this->widgetService->register('advanced_dashboard', [
            'title' => 'Advanced Analytics Dashboard',
            'description' => 'Comprehensive analytics dashboard',
            'category' => 'dashboards',
            'version' => '1.5.0',
            'dependencies' => [
                [
                    'id' => 'analytics_core',
                    'version' => '>=3.0.0', // Version constraint
                    'category' => 'analytics',
                    'permissions' => ['read_analytics']
                ],
                [
                    'id' => 'basic_chart',
                    'version' => '^2.0.0', // Compatible version
                    'category' => 'charts'
                ]
            ]
        ]);
        
        echo "✅ Complex dependency widgets registered successfully\n";
    }
    
    /**
     * Example 3: Circular Dependency Detection
     */
    public function circularDependencyExample()
    {
        try {
            // This will create a circular dependency
            $this->widgetService->register('widget_a', [
                'title' => 'Widget A',
                'category' => 'test',
                'dependencies' => ['widget_b']
            ]);
            
            $this->widgetService->register('widget_b', [
                'title' => 'Widget B',
                'category' => 'test',
                'dependencies' => ['widget_a'] // Creates circular dependency
            ]);
            
        } catch (\InvalidArgumentException $e) {
            echo "❌ Circular dependency detected (as expected): " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Example 4: Widget Loading Order Resolution
     */
    public function loadingOrderExample()
    {
        $widgetIds = ['advanced_dashboard', 'basic_chart', 'analytics_core', 'data_formatter'];
        
        $loadingOrder = $this->widgetService->resolveLoadingOrder($widgetIds);
        
        echo "🔄 Safe loading order for widgets:\n";
        foreach ($loadingOrder as $index => $widgetId) {
            echo "  " . ($index + 1) . ". {$widgetId}\n";
        }
        
        // Expected order: data_formatter -> analytics_core -> basic_chart -> advanced_dashboard
    }
    
    /**
     * Example 5: Dependency Graph Visualization
     */
    public function dependencyGraphExample()
    {
        $graph = $this->widgetService->getDependencyGraph();
        
        echo "📊 Dependency Graph Structure:\n";
        echo "Nodes (" . count($graph['nodes']) . "):\n";
        foreach ($graph['nodes'] as $node) {
            echo "  - {$node['id']} ({$node['category']}) v{$node['version']}\n";
        }
        
        echo "\nEdges (" . count($graph['edges']) . "):\n";
        foreach ($graph['edges'] as $edge) {
            echo "  - {$edge['from']} → {$edge['to']}\n";
        }
    }
    
    /**
     * Example 6: User Widget Selection Validation
     */
    public function userSelectionValidationExample()
    {
        // User selects widgets but forgets dependencies
        $userSelection = ['advanced_dashboard']; // Missing dependencies
        
        $validation = $this->widgetService->validateUserWidgetSelection($userSelection);
        
        echo "👤 User Selection Validation:\n";
        echo "Valid: " . ($validation['valid'] ? 'Yes' : 'No') . "\n";
        
        if (!empty($validation['errors'])) {
            echo "Errors:\n";
            foreach ($validation['errors'] as $error) {
                echo "  - {$error}\n";
            }
        }
        
        if (!empty($validation['warnings'])) {
            echo "Warnings:\n";
            foreach ($validation['warnings'] as $warning) {
                echo "  - {$warning}\n";
            }
        }
        
        // Now with complete selection
        $completeSelection = ['advanced_dashboard', 'basic_chart', 'analytics_core', 'data_formatter'];
        $validValidation = $this->widgetService->validateUserWidgetSelection($completeSelection);
        
        echo "\n✅ Complete Selection Validation: " . ($validValidation['valid'] ? 'Valid' : 'Invalid') . "\n";
        echo "Suggested loading order: " . implode(' → ', $validValidation['loading_order']) . "\n";
    }
    
    /**
     * Example 7: Widget Compatibility Checking
     */
    public function compatibilityCheckExample()
    {
        // Register conflicting widgets
        $this->widgetService->register('theme_dark', [
            'title' => 'Dark Theme Widget',
            'category' => 'themes',
            'conflicts' => ['theme_light'] // Conflicts with light theme
        ]);
        
        $this->widgetService->register('theme_light', [
            'title' => 'Light Theme Widget',
            'category' => 'themes',
            'conflicts' => ['theme_dark'] // Conflicts with dark theme
        ]);
        
        $compatibility = $this->widgetService->checkWidgetCompatibility(['theme_dark', 'theme_light']);
        
        echo "⚠️ Widget Compatibility Check:\n";
        if (!empty($compatibility)) {
            foreach ($compatibility as $conflict) {
                echo "  Conflict: {$conflict['widget1']} ↔ {$conflict['widget2']} ({$conflict['reason']})\n";
            }
        } else {
            echo "  All widgets are compatible\n";
        }
    }
    
    /**
     * Example 8: Reverse Dependencies Analysis
     */
    public function reverseDependenciesExample()
    {
        $reverseDeps = $this->widgetService->getReverseDependencies('data_formatter');
        
        echo "🔍 Widgets that depend on 'data_formatter':\n";
        foreach ($reverseDeps as $widgetId) {
            echo "  - {$widgetId}\n";
        }
        
        // This should show: analytics_core, basic_chart (indirectly advanced_dashboard)
    }
    
    /**
     * Example 9: Configuration Form Generation with Dependencies
     */
    public function configurationFormExample()
    {
        // Register widget with configuration schema
        $configSchema = [
            'chart_type' => [
                'type' => 'select',
                'label' => 'Chart Type',
                'required' => true,
                'options' => ['bar', 'line', 'pie'],
                'default' => 'bar'
            ],
            'data_source' => [
                'type' => 'text',
                'label' => 'Data Source URL',
                'required' => true,
                'validation' => ['url']
            ]
        ];
        
        $this->widgetService->register('configurable_chart', [
            'title' => 'Configurable Chart Widget',
            'category' => 'charts',
            'dependencies' => ['data_formatter'],
            'config_schema' => $configSchema
        ],
        $configSchema);
        
        $form = $this->widgetService->generateConfigurationForm('configurable_chart');
        
        echo "📋 Generated Configuration Form:\n";
        foreach ($form['fields'] as $field) {
            echo "  - {$field['label']} ({$field['type']}) " . 
                 ($field['required'] ? '[Required]' : '[Optional]') . "\n";
        }
    }
    
    /**
     * Run all examples
     */
    public function runAllExamples()
    {
        echo "🚀 Widget Dependency Management System Examples\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
        
        echo "1. Basic Dependencies:\n";
        $this->basicDependencyExample();
        echo "\n";
        
        echo "2. Complex Dependencies:\n";
        $this->complexDependencyExample();
        echo "\n";
        
        echo "3. Circular Dependency Detection:\n";
        $this->circularDependencyExample();
        echo "\n";
        
        echo "4. Loading Order Resolution:\n";
        $this->loadingOrderExample();
        echo "\n";
        
        echo "5. Dependency Graph:\n";
        $this->dependencyGraphExample();
        echo "\n";
        
        echo "6. User Selection Validation:\n";
        $this->userSelectionValidationExample();
        echo "\n";
        
        echo "7. Widget Compatibility:\n";
        $this->compatibilityCheckExample();
        echo "\n";
        
        echo "8. Reverse Dependencies:\n";
        $this->reverseDependenciesExample();
        echo "\n";
        
        echo "9. Configuration Forms:\n";
        $this->configurationFormExample();
        echo "\n";
        
        echo "✅ All examples completed successfully!\n";
    }
}

// Usage example:
// $examples = new WidgetDependencyExamples();
// $examples->runAllExamples();
