<?php

namespace Tests;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MasterLayoutTest extends TestCase
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
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('<meta name="viewport"', false);
        $response->assertSee('width=device-width', false);
        $response->assertSee('initial-scale=1', false);
    }

    public function test_accessibility_features_present()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        // Check for skip link
        $response->assertSee('Skip to main content');
        
        // Check for ARIA attributes
        $response->assertSee('role="navigation"', false);
        $response->assertSee('role="main"', false);
        $response->assertSee('aria-label="Main navigation"', false);
        
        // Check for proper heading structure
        $response->assertSee('<h1', false);
    }

    public function test_performance_optimization_features()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        // Check for preconnect links
        $response->assertSee('preconnect', false);
        $response->assertSee('fonts.googleapis.com', false);
        $response->assertSee('fonts.gstatic.com', false);
        
        // Check for optimized font loading
        $response->assertSee('display=swap', false);
    }

    public function test_css_custom_properties_present()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('--sidebar-width', false);
        $response->assertSee('--transition-speed', false);
        $response->assertSee('--border-radius', false);
    }

    public function test_alpinejs_integration()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('x-data="dashboardLayout()"', false);
        $response->assertSee('x-init="init()"', false);
        $response->assertSee('Alpine.js', false);
    }

    public function test_cross_browser_compatibility_features()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('-webkit-transform', false);
        $response->assertSee('-ms-transform', false);
        $response->assertSee('-webkit-box', false);
        $response->assertSee('-ms-flexbox', false);
    }

    public function test_loading_screen_present()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('loading-screen', false);
        $response->assertSee('Loading Dashboard', false);
    }

    public function test_sidebar_functionality()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('id="sidebar"', false);
        $response->assertSee('toggleSidebar()', false);
        $response->assertSee('sidebarCollapsed', false);
    }

    public function test_flash_messages_support()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('flash-message', false);
        $response->assertSee('session(\'success\')', false);
    }

    public function test_breadcrumb_navigation()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('breadcrumb', false);
        $response->assertSee('aria-label="Breadcrumb"', false);
    }

    public function test_keyboard_navigation_support()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('tabindex', false);
        $response->assertSee('focus:outline', false);
        $response->assertSee('focus:ring', false);
    }

    public function test_print_styles_present()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('@media print', false);
        $response->assertSee('no-print', false);
    }

    public function test_reduced_motion_support()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('prefers-reduced-motion', false);
        $response->assertSee('animation-duration: 0.01ms', false);
    }

    public function test_high_contrast_support()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('prefers-contrast: high', false);
    }

    public function test_mobile_responsive_design()
    {
        $user = $this->createAdminUser();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertSee('lg:hidden', false);
        $response->assertSee('isMobile', false);
        $response->assertSee('checkIsMobile()', false);
    }
}
