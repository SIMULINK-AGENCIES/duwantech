<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DuwanTech') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Animation Styles -->
        <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        <!-- Page Loading Overlay -->
        <style>
            .page-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
            }
            
            .page-loader.fade-out {
                opacity: 0;
                visibility: hidden;
            }
            
            .loader-content {
                text-align: center;
                color: white;
            }
            
            .loader-spinner {
                width: 50px;
                height: 50px;
                border: 3px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top-color: white;
                animation: spin 1s ease-in-out infinite;
                margin: 0 auto 1rem;
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <!-- Page Loading Overlay -->
        <div id="pageLoader" class="page-loader">
            <div class="loader-content">
                <div class="loader-spinner"></div>
                <p class="text-sm font-medium">Loading Dashboard...</p>
            </div>
        </div>
        
        <div class="min-h-screen bg-gray-50 fadeIn" style="animation-delay: 0.3s;">
            <!-- Admin Bar (only visible to admin users) -->
            @auth
                @if(Auth::user()->hasRole('admin'))
                    <div class="bg-gray-900 text-white slideInDown">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-4 h-4 text-blue-400 transition-transform duration-300 hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Admin View</span>
                                    <span class="text-xs text-gray-400">You are viewing the site as an administrator</span>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <span class="text-xs text-gray-400">Logged in as {{ Auth::user()->name }}</span>
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="inline-flex items-center px-3 py-1 border border-gray-600 rounded-md text-xs font-medium text-white bg-gray-800 hover:bg-gray-700 transition-all duration-200 hover:scale-105 hover:shadow-md">
                                        <svg class="w-3 h-3 mr-1 transition-transform duration-200 hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Admin Panel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth
            
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
        
        <!-- Page Loading Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Hide page loader after content is loaded
                setTimeout(() => {
                    const loader = document.getElementById('pageLoader');
                    if (loader) {
                        loader.classList.add('fade-out');
                        setTimeout(() => {
                            loader.style.display = 'none';
                        }, 500);
                    }
                }, 800);
                
                // Enhanced link navigation with loading
                document.addEventListener('click', function(e) {
                    const link = e.target.closest('a');
                    if (link && !link.hasAttribute('target') && link.hostname === window.location.hostname) {
                        const href = link.getAttribute('href');
                        if (href && !href.startsWith('#') && !href.includes('javascript:')) {
                            const loader = document.getElementById('pageLoader');
                            if (loader) {
                                loader.style.display = 'flex';
                                loader.classList.remove('fade-out');
                            }
                        }
                    }
                });
                
                // Add smooth scroll behavior
                document.documentElement.style.scrollBehavior = 'smooth';
                
                // Initialize animations for elements coming into view
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -100px 0px'
                };
                
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-fadeInUp');
                            observer.unobserve(entry.target);
                        }
                    });
                }, observerOptions);
                
                // Observe elements with .animate-on-scroll class
                document.querySelectorAll('.animate-on-scroll').forEach(el => {
                    observer.observe(el);
                });
            });
        </script>
    </body>
</html>
