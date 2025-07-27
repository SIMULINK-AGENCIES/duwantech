<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    
    <title>@yield('title', config('app.name') . ' - Admin Dashboard')</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    
    <!-- Custom Admin Styles -->
    <style>
        /* CSS Custom Properties for Dynamic Theming */
        :root {
            --sidebar-width: 16rem;
            --sidebar-collapsed-width: 4rem;
            --header-height: 4rem;
            --transition-speed: 0.3s;
            --border-radius: 0.375rem;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        
        /* Smooth scrolling for better UX */
        html {
            scroll-behavior: smooth;
        }
        
        /* Custom scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            :root {
                --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.2);
                --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.3), 0 2px 4px -2px rgb(0 0 0 / 0.3);
                --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.3), 0 4px 6px -4px rgb(0 0 0 / 0.3);
            }
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            :root {
                --transition-speed: 0.01ms;
            }
            
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
        
        /* Focus management for accessibility */
        .focus-visible:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        /* Skip link for screen readers */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: #000;
            color: #fff;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
        }
        
        .skip-link:focus {
            top: 6px;
        }
        
        /* Print styles support */
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white !important;
                color: black !important;
            }
        }
        
        /* Cross-browser compatibility features */
        .sidebar {
            -webkit-transform: translateX(0);
            -ms-transform: translateX(0);
            transform: translateX(0);
        }
        
        .flex-container {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
        }
    </style>
    
    @stack('styles')
</head>

<body class="h-full bg-gray-50 font-inter antialiased" x-data="dashboardLayout()" x-init="init()">
    <!-- Skip Navigation Link for Accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Loading Screen -->
    <div id="loading-screen" 
         class="fixed inset-0 bg-white z-50 flex items-center justify-center transition-opacity duration-500"
         x-show="loading" 
         x-transition:leave="transition-opacity duration-500"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: flex;">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600 text-sm">Loading Dashboard...</p>
        </div>
    </div>
    
    <!-- Main Dashboard Container -->
    <div class="min-h-screen flex" 
         :class="{ 'sidebar-collapsed': sidebarCollapsed }"
         x-cloak>
        
        <!-- Enhanced Sidebar Component -->
        @include('admin.dashboard.partials.sidebar')

        <!-- Mobile Navigation Component -->
        @include('admin.dashboard.partials.mobile-navigation')
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out lg:pt-0 pt-16"
             :class="{ 
                 'lg:ml-64': !sidebarCollapsed && !isMobile, 
                 'lg:ml-16': sidebarCollapsed && !isMobile,
                 'ml-0': isMobile 
             }">
            
            <!-- Enhanced Header Component -->
            @include('admin.dashboard.partials.header')
            
            <!-- Main Content -->
            <main id="main-content" 
                  class="flex-1 overflow-auto bg-gray-50 focus:outline-none"
                  tabindex="-1"
                  role="main"
                  aria-labelledby="page-title">
                
                <!-- Content Wrapper -->
                <div class="px-4 sm:px-6 lg:px-8 py-6">
                    <!-- Breadcrumb -->
                    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
                        <nav class="mb-6" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2 text-sm breadcrumb">
                                @foreach($breadcrumbs as $index => $breadcrumb)
                                    <li class="flex items-center">
                                        @if($index > 0)
                                            <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                        @if(isset($breadcrumb['url']) && !$loop->last)
                                            <a href="{{ $breadcrumb['url'] }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200" tabindex="0">
                                                {{ $breadcrumb['title'] }}
                                            </a>
                                        @else
                                            <span class="text-gray-900 font-medium">{{ $breadcrumb['title'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    @endif
                    
                    <!-- Flash Messages -->
                    @if(session('success') || session('error') || session('warning') || session('info'))
                        <div class="mb-6 space-y-2">
                            @if(session('success'))
                                <div class="flash-message bg-green-50 border border-green-200 rounded-lg p-4 flex items-start space-x-3" role="alert" aria-live="polite">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-green-800">Success</h3>
                                        <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Page Content -->
                    <div class="space-y-6">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    
    <!-- Fallback Loading Screen Handler -->
    <script>
        // Fallback to hide loading screen if Alpine.js fails
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up loading screen fallbacks');
            
            setTimeout(function() {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen && (loadingScreen.style.display !== 'none' && !loadingScreen.hidden)) {
                    console.log('Hiding loading screen via fallback - DOMContentLoaded');
                    loadingScreen.style.display = 'none';
                    loadingScreen.hidden = true;
                }
            }, 1500);
        });
        
        // Emergency fallback - hide after page load
        window.addEventListener('load', function() {
            console.log('Window loaded, checking loading screen');
            
            setTimeout(function() {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen) {
                    console.log('Hiding loading screen via fallback - window load');
                    loadingScreen.style.display = 'none';
                    loadingScreen.hidden = true;
                }
            }, 500);
        });
        
        // Immediate fallback for really bad cases
        setTimeout(function() {
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                console.log('Emergency fallback - hiding loading screen');
                loadingScreen.style.display = 'none';
                loadingScreen.hidden = true;
            }
        }, 3000);
    </script>
    
    <!-- Enhanced Dashboard Layout Alpine.js Component -->
    <script>
        function dashboardLayout() {
            return {
                loading: true,
                sidebarOpen: false,
                sidebarCollapsed: Alpine.$persist(false).as('sidebar-collapsed'),
                isMobile: false,
                
                init() {
                    // Initialize responsive behavior
                    this.checkIsMobile();
                    this.initializeLayout();
                    
                    // Set up event listeners
                    window.addEventListener('resize', () => {
                        this.checkIsMobile();
                        this.handleResize();
                    });
                    
                    // Handle escape key for mobile sidebar
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.sidebarOpen && this.isMobile) {
                            this.closeSidebar();
                        }
                    });
                    
                    // Initialize Alpine store for cross-component communication
                    Alpine.store('sidebar', {
                        mobileOpen: this.sidebarOpen,
                        collapsed: this.sidebarCollapsed
                    });
                    
                    // Remove loading screen after initialization
                    // Add multiple fallbacks to ensure loading screen is hidden
                    const hideLoading = () => {
                        this.loading = false;
                        console.log('Loading screen hidden at:', new Date().toISOString());
                    };
                    
                    // Primary timeout - fast for good UX
                    setTimeout(hideLoading, 300);
                    
                    // Fallback timeouts in case the first one fails
                    setTimeout(hideLoading, 1000);
                    setTimeout(hideLoading, 2000);
                    
                    // Emergency timeout - should never be needed
                    setTimeout(() => {
                        this.loading = false;
                        console.warn('Emergency loading screen timeout triggered');
                    }, 3000);
                    
                    // Immediate fallback for debugging
                    requestAnimationFrame(hideLoading);
                    
                    // Force hide on DOMContentLoaded if still showing
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', hideLoading);
                    } else {
                        hideLoading();
                    }
                    
                    // Additional fallback on window load
                    window.addEventListener('load', hideLoading);
                },
                
                checkIsMobile() {
                    this.isMobile = window.innerWidth < 1024;
                },
                
                initializeLayout() {
                    // Auto-collapse on mobile
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                        this.sidebarCollapsed = false;
                    }
                },
                
                toggleSidebar() {
                    if (this.isMobile) {
                        this.sidebarOpen = !this.sidebarOpen;
                        // Update store
                        Alpine.store('sidebar').mobileOpen = this.sidebarOpen;
                        
                        // Prevent body scrolling when mobile sidebar is open
                        if (this.sidebarOpen) {
                            document.body.classList.add('mobile-nav-open');
                        } else {
                            document.body.classList.remove('mobile-nav-open');
                        }
                    } else {
                        this.sidebarCollapsed = !this.sidebarCollapsed;
                        // Update store
                        Alpine.store('sidebar').collapsed = this.sidebarCollapsed;
                    }
                },
                
                closeSidebar() {
                    this.sidebarOpen = false;
                    Alpine.store('sidebar').mobileOpen = false;
                    document.body.classList.remove('mobile-nav-open');
                },
                
                handleResize() {
                    // Close mobile sidebar on desktop switch
                    if (!this.isMobile && this.sidebarOpen) {
                        this.closeSidebar();
                    }
                    
                    // Reset sidebar state on mobile/desktop switch
                    if (this.isMobile) {
                        this.sidebarCollapsed = false;
                    }
                }
            }
        }
        
        // Enhanced Accessibility and Keyboard Navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Add focus management for keyboard navigation
            const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
            
            // Trap focus in mobile sidebar when open
            document.addEventListener('keydown', function(e) {
                const sidebar = document.getElementById('enhanced-sidebar');
                if (!sidebar) return;
                
                const isMobile = window.innerWidth < 1024;
                const sidebarOpen = !sidebar.classList.contains('-translate-x-full');
                
                if (e.key === 'Tab' && isMobile && sidebarOpen) {
                    const focusable = sidebar.querySelectorAll(focusableElements);
                    const firstFocusable = focusable[0];
                    const lastFocusable = focusable[focusable.length - 1];
                    
                    if (e.shiftKey) {
                        if (document.activeElement === firstFocusable) {
                            lastFocusable.focus();
                            e.preventDefault();
                        }
                    } else {
                        if (document.activeElement === lastFocusable) {
                            firstFocusable.focus();
                            e.preventDefault();
                        }
                    }
                }
            });
            
            // Enhanced keyboard navigation for sidebar menu items
            document.addEventListener('keydown', function(e) {
                if (e.target.closest('#enhanced-sidebar')) {
                    switch(e.key) {
                        case 'ArrowDown':
                            e.preventDefault();
                            focusNextSidebarItem(e.target);
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            focusPrevSidebarItem(e.target);
                            break;
                        case 'Home':
                            e.preventDefault();
                            focusFirstSidebarItem();
                            break;
                        case 'End':
                            e.preventDefault();
                            focusLastSidebarItem();
                            break;
                        case 'Enter':
                        case ' ':
                            if (e.target.tagName === 'BUTTON') {
                                e.preventDefault();
                                e.target.click();
                            }
                            break;
                    }
                }
            });
            
            function focusNextSidebarItem(currentElement) {
                const sidebar = document.getElementById('enhanced-sidebar');
                if (!sidebar) return;
                
                const focusableItems = Array.from(sidebar.querySelectorAll('a[tabindex="0"], button[tabindex="0"]'));
                const currentIndex = focusableItems.indexOf(currentElement);
                const nextIndex = (currentIndex + 1) % focusableItems.length;
                focusableItems[nextIndex].focus();
            }
            
            function focusPrevSidebarItem(currentElement) {
                const sidebar = document.getElementById('enhanced-sidebar');
                if (!sidebar) return;
                
                const focusableItems = Array.from(sidebar.querySelectorAll('a[tabindex="0"], button[tabindex="0"]'));
                const currentIndex = focusableItems.indexOf(currentElement);
                const prevIndex = currentIndex === 0 ? focusableItems.length - 1 : currentIndex - 1;
                focusableItems[prevIndex].focus();
            }
            
            function focusFirstSidebarItem() {
                const sidebar = document.getElementById('enhanced-sidebar');
                if (!sidebar) return;
                
                const firstItem = sidebar.querySelector('a[tabindex="0"], button[tabindex="0"]');
                if (firstItem) firstItem.focus();
            }
            
            function focusLastSidebarItem() {
                const sidebar = document.getElementById('enhanced-sidebar');
                if (!sidebar) return;
                
                const focusableItems = Array.from(sidebar.querySelectorAll('a[tabindex="0"], button[tabindex="0"]'));
                const lastItem = focusableItems[focusableItems.length - 1];
                if (lastItem) lastItem.focus();
            }
        });
        
        // Global function for opening mobile sidebar (backwards compatibility)
        function openMobileSidebar() {
            const dashboardComponent = Alpine.$data(document.body);
            if (dashboardComponent) {
                dashboardComponent.sidebarOpen = true;
                dashboardComponent.toggleSidebar();
            }
        }
    </script>
    
    @stack('scripts')
</body>

</html>
