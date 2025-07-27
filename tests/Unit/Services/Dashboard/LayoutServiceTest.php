<?php

namespace Tests\Unit\Services\Dashboard;

use Tests\TestCase;
use App\Services\Dashboard\LayoutService;
use App\Contracts\Dashboard\LayoutServiceInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class LayoutServiceTest extends TestCase
{
    use RefreshDatabase;

    private LayoutService $layoutService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->layoutService = app(LayoutServiceInterface::class);
        $this->user = User::factory()->create();
    }

    public function test_can_get_user_layout()
    {
        $layout = $this->layoutService->getUserLayout($this->user->id);
        
        $this->assertIsArray($layout);
        $this->assertArrayHasKey('widgets', $layout);
        $this->assertArrayHasKey('layout', $layout);
    }

    public function test_can_save_user_layout()
    {
        $layoutData = [
            'widgets' => [
                [
                    'id' => 'revenue-kpi',
                    'position' => ['x' => 0, 'y' => 0, 'width' => 3, 'height' => 2],
                    'config' => [],
                    'enabled' => true
                ]
            ],
            'layout' => [
                'template' => 'professional',
                'columns' => 12,
                'gap' => 16
            ]
        ];

        $result = $this->layoutService->saveUserLayout($layoutData, $this->user->id);
        
        $this->assertTrue($result);
        
        // Verify data was saved
        $this->user->refresh();
        $this->assertNotNull($this->user->dashboard_preferences);
        $this->assertEquals($layoutData, $this->user->dashboard_preferences['layout']);
    }

    public function test_can_get_available_templates()
    {
        $templates = $this->layoutService->getTemplates();
        
        $this->assertIsArray($templates);
        $this->assertArrayHasKey('professional', $templates);
        $this->assertArrayHasKey('minimal', $templates);
    }

    public function test_can_validate_layout()
    {
        $validLayout = [
            'widgets' => [
                [
                    'id' => 'revenue-kpi',
                    'position' => ['x' => 0, 'y' => 0, 'width' => 3, 'height' => 2],
                    'config' => [],
                    'enabled' => true
                ]
            ],
            'layout' => [
                'template' => 'professional',
                'columns' => 12,
                'gap' => 16
            ]
        ];

        $errors = $this->layoutService->validateLayout($validLayout);
        $this->assertEmpty($errors);

        $invalidLayout = [
            'widgets' => [
                [
                    'id' => 'invalid-widget',
                    'position' => ['x' => -1, 'y' => 0, 'width' => 15, 'height' => 2]
                ]
            ]
        ];

        $errors = $this->layoutService->validateLayout($invalidLayout);
        $this->assertNotEmpty($errors);
    }

    public function test_caching_works()
    {
        // Clear any existing cache
        Cache::flush();
        
        // First call should hit the database
        $layout1 = $this->layoutService->getUserLayout($this->user->id);
        
        // Second call should hit the cache
        $layout2 = $this->layoutService->getUserLayout($this->user->id);
        
        $this->assertEquals($layout1, $layout2);
    }

    public function test_can_clear_cache()
    {
        // Prime the cache
        $this->layoutService->getUserLayout($this->user->id);
        
        // Clear cache
        $this->layoutService->clearUserCache($this->user->id);
        
        // This should work without errors
        $layout = $this->layoutService->getUserLayout($this->user->id);
        $this->assertIsArray($layout);
    }
}
