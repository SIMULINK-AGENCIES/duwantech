<?php

namespace Tests;

use Tests\TestCase;

class SimpleLayoutTest extends TestCase
{
    public function test_master_layout_exists()
    {
        $layoutPath = resource_path('views/admin/layouts/master.blade.php');
        $this->assertFileExists($layoutPath, 'Master layout file should exist');
        
        $content = file_get_contents($layoutPath);
        
        // Test for all 16 advanced features
        
        // 1. CSS Custom Properties
        $this->assertStringContainsString('--sidebar-width', $content, 'CSS custom properties should be present');
        $this->assertStringContainsString('--transition-speed', $content, 'Transition speed variable should be present');
        $this->assertStringContainsString('--border-radius', $content, 'Border radius variable should be present');
        
        // 2. Alpine.js Integration
        $this->assertStringContainsString('x-data="dashboardLayout()"', $content, 'Alpine.js should be integrated');
        $this->assertStringContainsString('x-init="init()"', $content, 'Alpine.js init should be present');
        $this->assertStringContainsString('function dashboardLayout()', $content, 'Dashboard layout function should be present');
        
        // 3. Accessibility Features
        $this->assertStringContainsString('Skip to main content', $content, 'Skip link should be present');
        $this->assertStringContainsString('role="navigation"', $content, 'Navigation role should be present');
        $this->assertStringContainsString('role="main"', $content, 'Main role should be present');
        $this->assertStringContainsString('aria-label="Main navigation"', $content, 'ARIA labels should be present');
        
        // 4. Performance Optimization
        $this->assertStringContainsString('preconnect', $content, 'Preconnect links should be present');
        $this->assertStringContainsString('fonts.googleapis.com', $content, 'Google Fonts preconnect should be present');
        $this->assertStringContainsString('display=swap', $content, 'Font display swap should be present');
        
        // 5. Cross-browser Compatibility
        $this->assertStringContainsString('-webkit-transform', $content, 'Webkit prefixes should be present');
        $this->assertStringContainsString('-ms-transform', $content, 'MS prefixes should be present');
        $this->assertStringContainsString('-webkit-box', $content, 'Webkit flex prefixes should be present');
        
        // 6. Loading Screen
        $this->assertStringContainsString('loading-screen', $content, 'Loading screen should be present');
        $this->assertStringContainsString('Loading Dashboard', $content, 'Loading text should be present');
        
        // 7. Sidebar Functionality
        $this->assertStringContainsString('id="sidebar"', $content, 'Sidebar ID should be present');
        $this->assertStringContainsString('toggleSidebar()', $content, 'Toggle sidebar function should be present');
        $this->assertStringContainsString('sidebarCollapsed', $content, 'Sidebar collapsed state should be present');
        
        // 8. Flash Messages Support
        $this->assertStringContainsString('flash-message', $content, 'Flash message class should be present');
        $this->assertStringContainsString('session(\'success\')', $content, 'Session success check should be present');
        
        // 9. Breadcrumb Navigation
        $this->assertStringContainsString('breadcrumb', $content, 'Breadcrumb class should be present');
        $this->assertStringContainsString('aria-label="Breadcrumb"', $content, 'Breadcrumb ARIA label should be present');
        
        // 10. Keyboard Navigation Support
        $this->assertStringContainsString('tabindex', $content, 'Tabindex should be present for keyboard navigation');
        $this->assertStringContainsString('focus:outline', $content, 'Focus outline styles should be present');
        $this->assertStringContainsString('focus:ring', $content, 'Focus ring styles should be present');
        
        // 11. Print Styles
        $this->assertStringContainsString('@media print', $content, 'Print media query should be present');
        $this->assertStringContainsString('no-print', $content, 'No-print class should be present');
        
        // 12. Reduced Motion Support
        $this->assertStringContainsString('prefers-reduced-motion', $content, 'Reduced motion support should be present');
        $this->assertStringContainsString('animation-duration: 0.01ms', $content, 'Reduced motion animation duration should be present');
        
        // 13. High Contrast Support
        $this->assertStringContainsString('prefers-contrast: high', $content, 'High contrast support should be present');
        
        // 14. Mobile Responsive Design
        $this->assertStringContainsString('lg:hidden', $content, 'Mobile responsive classes should be present');
        $this->assertStringContainsString('isMobile', $content, 'Mobile detection should be present');
        $this->assertStringContainsString('checkIsMobile()', $content, 'Mobile check function should be present');
        
        // 15. Custom Scrollbar Styling
        $this->assertStringContainsString('::-webkit-scrollbar', $content, 'Custom scrollbar styles should be present');
        $this->assertStringContainsString('::-webkit-scrollbar-thumb', $content, 'Scrollbar thumb styles should be present');
        
        // 16. Smooth Scrolling
        $this->assertStringContainsString('scroll-behavior: smooth', $content, 'Smooth scrolling should be present');
        
        // Verify template structure
        $this->assertStringContainsString('<!DOCTYPE html>', $content, 'HTML doctype should be present');
        $this->assertStringContainsString('id="main-content"', $content, 'Main content ID should be present');
        $this->assertStringContainsString('@yield(\'content\')', $content, 'Content yield should be present');
        
        // Verify Blade directives
        $this->assertStringContainsString('@vite([\'resources/css/app.css\'])', $content, 'CSS Vite directive should be present');
        $this->assertStringContainsString('@vite([\'resources/js/app.js\'])', $content, 'JS Vite directive should be present');
        $this->assertStringContainsString('@stack(\'styles\')', $content, 'Styles stack should be present');
        $this->assertStringContainsString('@stack(\'scripts\')', $content, 'Scripts stack should be present');
    }
    
    public function test_dashboard_index_extends_master_layout()
    {
        $dashboardPath = resource_path('views/admin/dashboard/index.blade.php');
        $this->assertFileExists($dashboardPath, 'Dashboard index file should exist');
        
        $content = file_get_contents($dashboardPath);
        $this->assertStringContainsString('@extends(\'admin.layouts.master\')', $content, 'Dashboard should extend master layout');
    }
}
