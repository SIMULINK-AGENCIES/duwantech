<?php

namespace Tests\Unit\Services\Dashboard;

use Tests\TestCase;
use App\Services\Dashboard\WidgetService;
use App\Services\Dashboard\WidgetConfigurationSchema;
use App\Services\Dashboard\WidgetConfigurationManager;
use App\Contracts\Dashboard\WidgetServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class WidgetConfigurationTest extends TestCase
{
    use RefreshDatabase;

    private WidgetServiceInterface $widgetService;
    private WidgetConfigurationSchema $configSchema;
    private WidgetConfigurationManager $configManager;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->configSchema = new WidgetConfigurationSchema();
        $this->widgetService = new WidgetService($this->configSchema);
        $this->configManager = new WidgetConfigurationManager($this->widgetService);
    }

    /** @test */
    public function it_can_register_widget_with_configuration_schema()
    {
        $widgetConfig = [
            'title' => 'Test Analytics Widget',
            'description' => 'A test widget for analytics',
            'category' => 'analytics',
            'size' => ['width' => 6, 'height' => 4],
        ];

        $configSchema = [
            'metric_type' => [
                'type' => 'select',
                'label' => 'Metric Type',
                'description' => 'Choose the metric to display',
                'required' => true,
                'default' => 'sales',
                'options' => [
                    'sales' => 'Sales Revenue',
                    'orders' => 'Order Count',
                    'users' => 'User Registrations'
                ]
            ],
            'time_period' => [
                'type' => 'select',
                'label' => 'Time Period',
                'required' => true,
                'default' => '7d',
                'options' => [
                    '1d' => 'Last 24 Hours',
                    '7d' => 'Last 7 Days',
                    '30d' => 'Last 30 Days',
                    '90d' => 'Last 90 Days'
                ]
            ],
            'show_percentage' => [
                'type' => 'boolean',
                'label' => 'Show Percentage Change',
                'description' => 'Display percentage change from previous period',
                'default' => true
            ],
            'refresh_interval' => [
                'type' => 'number',
                'label' => 'Refresh Interval (seconds)',
                'min' => 30,
                'max' => 3600,
                'default' => 300
            ]
        ];

        // This should not throw an exception
        $this->widgetService->register('test_analytics', $widgetConfig, $configSchema);

        $registeredWidget = $this->widgetService->getWidget('test_analytics');
        
        $this->assertNotNull($registeredWidget);
        $this->assertEquals('Test Analytics Widget', $registeredWidget['title']);
        $this->assertEquals('analytics', $registeredWidget['category']);
        $this->assertNotNull($registeredWidget['config_schema']);
        $this->assertEquals($configSchema, $registeredWidget['config_schema']);
    }

    /** @test */
    public function it_validates_widget_configuration_schema()
    {
        // Valid schema
        $validSchema = [
            'text_field' => [
                'type' => 'text',
                'label' => 'Text Field',
                'required' => true,
                'max_length' => 100
            ]
        ];

        $errors = $this->configSchema->validateConfigurationSchema($validSchema);
        $this->assertEmpty($errors);

        // Invalid schema - missing type
        $invalidSchema = [
            'invalid_field' => [
                'label' => 'Invalid Field',
                'required' => true
            ]
        ];

        $errors = $this->configSchema->validateConfigurationSchema($invalidSchema);
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('invalid_field', $errors);
    }

    /** @test */
    public function it_validates_user_widget_configuration()
    {
        $schema = [
            'metric_type' => [
                'type' => 'select',
                'required' => true,
                'options' => ['sales', 'orders', 'users']
            ],
            'show_chart' => [
                'type' => 'boolean',
                'default' => true
            ],
            'max_items' => [
                'type' => 'number',
                'min' => 1,
                'max' => 100,
                'default' => 10
            ]
        ];

        // Valid user config
        $validUserConfig = [
            'metric_type' => 'sales',
            'show_chart' => false,
            'max_items' => 25
        ];

        $result = $this->configSchema->validateUserWidgetConfig($schema, $validUserConfig);
        $this->assertEmpty($result);

        // Invalid user config
        $invalidUserConfig = [
            'metric_type' => 'invalid_metric',
            'show_chart' => 'not_boolean',
            'max_items' => 150
        ];

        $result = $this->configSchema->validateUserWidgetConfig($schema, $invalidUserConfig);
        $this->assertNotEmpty($result);
    }

    /** @test */
    public function it_generates_configuration_form()
    {
        $widgetConfig = [
            'title' => 'Form Test Widget',
            'description' => 'Widget for testing form generation',
            'category' => 'analytics',
            'size' => ['width' => 4, 'height' => 3],
        ];

        $configSchema = [
            'title' => [
                'type' => 'text',
                'label' => 'Widget Title',
                'required' => true,
                'max_length' => 50,
                'placeholder' => 'Enter widget title'
            ],
            'chart_type' => [
                'type' => 'select',
                'label' => 'Chart Type',
                'required' => true,
                'options' => [
                    'line' => 'Line Chart',
                    'bar' => 'Bar Chart',
                    'pie' => 'Pie Chart'
                ]
            ],
            'auto_refresh' => [
                'type' => 'boolean',
                'label' => 'Auto Refresh',
                'description' => 'Automatically refresh data',
                'default' => true
            ]
        ];

        $this->widgetService->register('form_test', $widgetConfig, $configSchema);

        $form = $this->widgetService->generateConfigurationForm('form_test', [
            'title' => 'My Custom Widget',
            'chart_type' => 'bar'
        ]);

        $this->assertNotEmpty($form);
        $this->assertArrayHasKey('title', $form);
        $this->assertArrayHasKey('chart_type', $form);
        $this->assertArrayHasKey('auto_refresh', $form);

        // Check form field structure
        $titleField = $form['title'];
        $this->assertEquals('text', $titleField['type']);
        $this->assertEquals('Widget Title', $titleField['label']);
        $this->assertTrue($titleField['required']);
        $this->assertEquals('My Custom Widget', $titleField['current_value']);

        // Check validation rules
        $this->assertContains('required', $titleField['validation']);
        $this->assertContains('max:50', $titleField['validation']);

        // Check attributes
        $this->assertEquals('Enter widget title', $titleField['attributes']['placeholder']);
        $this->assertEquals(50, $titleField['attributes']['maxlength']);
    }

    /** @test */
    public function it_sanitizes_user_configuration()
    {
        $widgetConfig = [
            'title' => 'Sanitization Test Widget',
            'description' => 'Widget for testing configuration sanitization',
            'category' => 'system',
            'size' => ['width' => 6, 'height' => 4],
        ];

        $configSchema = [
            'is_enabled' => [
                'type' => 'boolean',
                'default' => true
            ],
            'max_count' => [
                'type' => 'number',
                'default' => 10
            ],
            'tags' => [
                'type' => 'multiselect',
                'options' => ['urgent', 'normal', 'low']
            ],
            'settings' => [
                'type' => 'json',
                'default' => []
            ]
        ];

        $this->widgetService->register('sanitization_test', $widgetConfig, $configSchema);

        $userConfig = [
            'is_enabled' => '1',  // String should be converted to boolean
            'max_count' => '25',  // String should be converted to number
            'tags' => 'urgent',   // Non-array should be converted to array
            'settings' => '{"theme": "dark"}',  // JSON string should be decoded
            'invalid_field' => 'should_be_ignored'  // Invalid fields should be ignored
        ];

        $result = $this->widgetService->validateUserConfiguration('sanitization_test', $userConfig);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
        
        $sanitized = $result['sanitized_config'];
        $this->assertTrue($sanitized['is_enabled']);
        $this->assertEquals(25.0, $sanitized['max_count']);
        $this->assertEquals([], $sanitized['tags']);  // 'urgent' as string becomes empty array
        $this->assertEquals(['theme' => 'dark'], $sanitized['settings']);
        $this->assertArrayNotHasKey('invalid_field', $sanitized);
    }

    /** @test */
    public function it_generates_json_schema()
    {
        $jsonSchema = $this->configSchema->generateJsonSchema();

        $this->assertIsArray($jsonSchema);
        $this->assertEquals('object', $jsonSchema['type']);
        $this->assertArrayHasKey('properties', $jsonSchema);
        
        // Check base schema properties
        $properties = $jsonSchema['properties'];
        $this->assertArrayHasKey('title', $properties);
        $this->assertArrayHasKey('description', $properties);
        $this->assertArrayHasKey('category', $properties);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}
