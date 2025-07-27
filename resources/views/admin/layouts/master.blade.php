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
        
        <!-- Sidebar -->
        <aside id="sidebar" 
               class="sidebar fixed inset-y-0 left-0 z-40 flex flex-col transition-all duration-300 ease-in-out bg-white border-r border-gray-200 shadow-sm"
               :class="{ 
                   'w-64': !sidebarCollapsed, 
                   'w-16': sidebarCollapsed,
                   '-translate-x-full': !sidebarOpen && isMobile,
                   'translate-x-0': sidebarOpen || !isMobile
               }"
               role="navigation"
               aria-label="Main navigation">
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                <div class="flex items-center space-x-3" :class="{ 'justify-center': sidebarCollapsed }">
                    <!-- Logo placeholder until assets are available -->
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-xs">D</span>
                    </div>
                    <span class="font-semibold text-gray-900 transition-opacity duration-300"
                          :class="{ 'opacity-0 hidden': sidebarCollapsed }"
                          x-show="!sidebarCollapsed"
                          x-transition>
                        {{ config('app.name') }}
                    </span>
                </div>
                
                <!-- Sidebar Toggle (Desktop) -->
                <button @click="toggleSidebar()" 
                        class="p-1.5 rounded-md hover:bg-gray-100 transition-colors duration-200 hidden lg:block"
                        :class="{ 'hidden': sidebarCollapsed }"
                        aria-label="Toggle sidebar"
                        tabindex="0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto py-4" aria-label="Sidebar navigation">
                <!-- Simple Navigation for now -->
                <div class="px-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" tabindex="0">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                </div>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-500">
                            <span class="text-xs font-medium leading-none text-white">A</span>
                        </span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700">Admin User</p>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Mobile Sidebar Overlay -->
        <div class="fixed inset-0 z-30 lg:hidden" 
             x-show="sidebarOpen && isMobile" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeSidebar()">
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
        </div>
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out"
             :class="{ 
                 'ml-64': !sidebarCollapsed && !isMobile, 
                 'ml-16': sidebarCollapsed && !isMobile,
                 'ml-0': isMobile 
             }">
            
            <!-- Top Header -->
            <header class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-sm main-header"
                    role="banner">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    
                    <!-- Mobile Menu Button -->
                    <button @click="toggleSidebar()" 
                            class="p-2 rounded-md hover:bg-gray-100 transition-colors duration-200 lg:hidden focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="Open sidebar"
                            tabindex="0">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <!-- Desktop Sidebar Toggle -->
                    <button @click="toggleSidebar()" 
                            class="p-2 rounded-md hover:bg-gray-100 transition-colors duration-200 hidden lg:block focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'block': sidebarCollapsed }"
                            x-show="sidebarCollapsed"
                            aria-label="Expand sidebar"
                            tabindex="0">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    
                    <!-- Page Title -->
                    <div class="flex-1 px-4">
                        <h1 class="text-xl font-semibold text-gray-900 truncate" id="page-title">
                            @yield('title', 'Dashboard')
                        </h1>
                    </div>
                    
                    <!-- Header Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Simple header actions -->
                        <button type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500" tabindex="0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V9a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h2"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </header>
            
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
    
    <!-- Dashboard Layout Alpine.js Component -->
    <script>
        function dashboardLayout() {
            return {
                loading: true,
                sidebarOpen: false,
                sidebarCollapsed: false,
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
                    
                    // Remove loading screen after initialization
                    // Add multiple fallbacks to ensure loading screen is hidden
                    const hideLoading = () => {
                        this.loading = false;
                    };
                    
                    // Primary timeout
                    setTimeout(hideLoading, 300);
                    
                    // Fallback timeouts in case the first one fails
                    setTimeout(hideLoading, 1000);
                    setTimeout(hideLoading, 2000);
                    
                    // Immediate fallback for debugging
                    requestAnimationFrame(hideLoading);
                    
                    // Force hide on DOMContentLoaded if still showing
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', hideLoading);
                    } else {
                        hideLoading();
                    }
                },
                
                checkIsMobile() {
                    this.isMobile = window.innerWidth < 1024;
                },
                
                initializeLayout() {
                    // Get saved preferences from localStorage
                    const savedCollapsed = localStorage.getItem('sidebar-collapsed');
                    if (savedCollapsed !== null) {
                        this.sidebarCollapsed = JSON.parse(savedCollapsed);
                    }
                    
                    // Auto-collapse on mobile
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                        this.sidebarCollapsed = false;
                    }
                },
                
                toggleSidebar() {
                    if (this.isMobile) {
                        this.sidebarOpen = !this.sidebarOpen;
                    } else {
                        this.sidebarCollapsed = !this.sidebarCollapsed;
                        // Save preference
                        localStorage.setItem('sidebar-collapsed', JSON.stringify(this.sidebarCollapsed));
                    }
                },
                
                closeSidebar() {
                    this.sidebarOpen = false;
                },
                
                handleResize() {
                    // Close mobile sidebar on desktop switch
                    if (!this.isMobile && this.sidebarOpen) {
                        this.sidebarOpen = false;
                    }
                }
            }
        }
        
        // Accessibility enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Add focus management for keyboard navigation
            const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
            
            // Trap focus in mobile sidebar when open
            document.addEventListener('keydown', function(e) {
                const sidebar = document.getElementById('sidebar');
                if (e.key === 'Tab' && sidebar && sidebar.classList.contains('translate-x-0')) {
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
        });
    </script>
    
    @stack('scripts')
</body>

</html>
