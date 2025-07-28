<?php

namespace Tests\Feature\Layout;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MasterLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles and permissions for testing
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);
    }

    protected function createAdminUser()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        return $user;
    }

    public function test_master_layout_renders_successfully()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('sidebar');
        $response->assertSee('main-content');
    }

    public function test_responsive_meta_tags_present()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin/dashboard');
        
        $response->assertSee('<meta name="viewport"', false);
        $response->assertSee('width=device-width', false);
        $response->assertSee('initial-scale=1', false);
    }

    public function test_accessibility_features_present()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin/dashboard');
        
        // Check for skip link
        $response->assertSee('Skip to main content');
        
        // Check for ARIA attributes
        $response->assertSee('role="navigation"', false);
        $response->assertSee('role="main"', false);
        $response->assertSee('aria-label="Main navigation"', false);
        
        // Check for proper heading structure
        $response->assertSee('<h1', false);
    }

    public function test_css_custom_properties_loaded()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin/dashboard');
        
        // Check for CSS custom properties
        $response->assertSee('--sidebar-width', false);
        $response->assertSee('--transition-speed', false);
        $response->assertSee('--border-radius', false);
    }

    public function test_javascript_functionality_loaded()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin/dashboard');
        
        // Check for Alpine.js
        $response->assertSee('x-data', false);
        $response->assertSee('dashboardLayout()', false);
        
        // Check for custom scripts
        $response->assertSee('toggleSidebar', false);
    }

    public function test_cross_browser_compatibility_features()
    {
        $response = $this->get('/admin/dashboard');
        
        // Check for vendor prefixes and fallbacks
        $response->assertSee('-webkit-transform', false);
        $response->assertSee('-ms-flexbox', false);
        
        // Check for progressive enhancement
        $response->assertSee('@supports', false);
    }

    public function test_dark_mode_support()
    {
        $response = $this->get('/admin/dashboard');
        
        // Check for dark mode classes and variables
        $response->assertSee('dark:', false);
        $response->assertSee('prefers-color-scheme', false);
    }

    public function test_reduced_motion_support()
    {
        $response = $this->get('/admin/dashboard');
        
        // Check for reduced motion media query
        $response->assertSee('prefers-reduced-motion', false);
        $response->assertSee('animation-duration: 0.01ms', false);
    }

    public function test_high_contrast_mode_support()
    {
        $response = $this->get('/admin/dashboard');
        
        // Check for high contrast support
        $response->assertSee('prefers-contrast: high', false);
    }

    public function test_layout_components_structure()
    {
        $response = $this->get('/admin/dashboard');
        
        // Check for main layout components
        $response->assertSee('id="sidebar"', false);
        $response->assertSee('id="main-content"', false);
        $response->assertSee('class="main-header"', false);
        
        // Check for sidebar navigation
        $response->assertSee('nav-link', false);
        
        // Check for header actions
        $response->assertSee('header-actions', false);
    }

    public function test_responsive_grid_system()
    {
        $response = $this->get('/admin/dashboard');
        
        // Check for responsive classes
        $response->assertSee('grid-cols-1', false);
        $response->assertSee('md:grid-cols-2', false);
        $response->assertSee('lg:grid-cols-4', false);
        
        // Check for container system
        $response->assertSee('container', false);
    }

    public function test_print_styles_support()
    {
        $response = $this->get('/admin/dashboard');
        
        // Check for print media query
        $response->assertSee('@media print', false);
        $response->assertSee('.no-print', false);
    }

    public function test_performance_optimization_features()
    {
        $response = $this->get('/admin/dashboard');
        
        // Check for preconnect
        $response->assertSee('rel="preconnect"', false);
        
        // Check for font optimization
        $response->assertSee('font-display=swap', false);
        
        // Check for lazy loading attributes
        $response->assertSee('data-lazy', false);
    }

    public function test_flash_messages_integration()
    {
        // Test with flash message
        session()->flash('success', 'Test message');
        
        $response = $this->get('/admin/dashboard');
        
        $response->assertSee('Test message');
        $response->assertSee('flash-message', false);
        $response->assertSee('role="alert"', false);
    }

    public function test_breadcrumb_navigation()
    {
        $response = $this->get('/admin/dashboard');
        
        // Check for breadcrumb structure
        $response->assertSee('aria-label="Breadcrumb"', false);
        $response->assertSee('breadcrumb', false);
    }

    public function test_keyboard_navigation_support()
    {
        $response = $this->get('/admin/dashboard');
        
        // Check for keyboard navigation attributes
        $response->assertSee('tabindex', false);
        $response->assertSee('focus:outline-none', false);
        $response->assertSee('focus:ring-2', false);
    }
}
