<?php

namespace Tests;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DebugContentTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(): User
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($adminRole);
        return $user;
    }

    public function test_debug_content_structure()
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user)->get('/admin');
        
        $content = $response->getContent();
        
        // Check for CSS custom properties
        $this->assertStringContainsString('--sidebar-width', $content, 'CSS custom properties should be present');
        
        // Check for Alpine.js
        $this->assertStringContainsString('x-data', $content, 'Alpine.js should be present');
        $this->assertStringContainsString('dashboardLayout()', $content, 'Dashboard layout function should be present');
        
        // Check for accessibility
        $this->assertStringContainsString('Skip to main content', $content, 'Skip link should be present');
        $this->assertStringContainsString('role="navigation"', $content, 'Navigation role should be present');
        $this->assertStringContainsString('role="main"', $content, 'Main role should be present');
        
        // Check for layout components
        $this->assertStringContainsString('id="sidebar"', $content, 'Sidebar ID should be present');
        $this->assertStringContainsString('id="main-content"', $content, 'Main content ID should be present');
        $this->assertStringContainsString('main-header', $content, 'Header class should be present');
        
        // Check for responsive grid in dashboard content
        $this->assertStringContainsString('md:grid-cols-2', $content, 'Responsive grid should be present in dashboard');
        
        // Check for cross-browser support
        $this->assertStringContainsString('-webkit-transform', $content, 'Webkit transform should be present');
        $this->assertStringContainsString('-ms-flexbox', $content, 'MS flexbox should be present');
        
        // Check for media queries
        $this->assertStringContainsString('@media print', $content, 'Print media query should be present');
        $this->assertStringContainsString('prefers-reduced-motion', $content, 'Reduced motion query should be present');
        $this->assertStringContainsString('prefers-contrast: high', $content, 'High contrast query should be present');
        
        // Check for performance features
        $this->assertStringContainsString('rel="preconnect"', $content, 'Preconnect links should be present');
        $this->assertStringContainsString('display=swap', $content, 'Font display swap should be present');
        
        // Check for breadcrumbs
        $this->assertStringContainsString('aria-label="Breadcrumb"', $content, 'Breadcrumb ARIA label should be present');
        $this->assertStringContainsString('breadcrumb', $content, 'Breadcrumb class should be present');
        
        // Check for keyboard navigation
        $this->assertStringContainsString('tabindex', $content, 'Tabindex should be present for keyboard navigation');
        $this->assertStringContainsString('focus:outline-none', $content, 'Focus styles should be present');
        $this->assertStringContainsString('focus:ring-2', $content, 'Focus ring styles should be present');
        
        $response->assertStatus(200);
    }

    public function test_flash_message_rendering()
    {
        $user = $this->createAdminUser();
        session()->flash('success', 'Test message');
        
        $response = $this->actingAs($user)->get('/admin');
        $content = $response->getContent();
        
        $this->assertStringContainsString('Test message', $content, 'Flash message should be rendered');
        $this->assertStringContainsString('flash-message', $content, 'Flash message class should be present');
        $this->assertStringContainsString('role="alert"', $content, 'Alert role should be present');
        
        $response->assertStatus(200);
    }
}
