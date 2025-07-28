<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js for dropdown functionality -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
            <div class="flex items-center justify-center h-16 bg-blue-600">
                <h1 class="text-xl font-bold text-white ">{{ config('app.name') }} Admin</h1>
            </div>
            
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-blue-700' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('admin.products.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.products.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Products
                    </a>
                    
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.categories.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Categories
                    </a>
                    
                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.orders.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Orders
                    </a>
                    
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Users
                    </a>
                    
                    <a href="{{ route('admin.activity.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.activity.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Activity Feed
                    </a>
                    
                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Reports
                    </a>
                    
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Settings</p>
                        
                        <a href="{{ route('admin.frontend.index') }}" 
                           class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.frontend.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Frontend
                        </a>
                        
                        <a href="{{ route('admin.mpesa.index') }}" 
                           class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.mpesa.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            M-Pesa
                            @if(function_exists('is_mpesa_enabled') && is_mpesa_enabled())
                                <span class="ml-auto">
                                    <span class="inline-flex h-2 w-2 rounded-full bg-green-400"></span>
                                </span>
                            @endif
                        </a>
                        
                        <a href="{{ route('admin.settings.index') }}" 
                           class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="ml-64">
            <!-- Professional Premium Navigation Bar -->
            <header class="bg-gradient-to-r from-gray-900 via-slate-900 to-gray-900 backdrop-blur-xl border-b border-slate-700/50 relative z-50 shadow-2xl">
                <!-- Premium Background Pattern -->
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(59,130,246,0.1),transparent_50%)]"></div>
                <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-blue-500/30 to-transparent"></div>
                
                <!-- Main Navigation Container -->
                <div class="relative flex items-center justify-between px-8 py-4">
                    
                    <!-- LEFT: Enterprise Brand Identity -->
                    <div class="flex items-center space-x-6">
                        <!-- Premium Logo -->
                        <div class="flex items-center space-x-4">
                            <!-- Sophisticated Logo Design -->
                            <div class="relative group">
                                <div class="w-11 h-11 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-xl ring-1 ring-white/10 group-hover:ring-white/20 transition-all duration-300 group-hover:scale-105">
                                    <span class="text-white font-bold text-lg tracking-tight">D</span>
                                </div>
                                <!-- Subtle Glow Effect -->
                                <div class="absolute inset-0 w-11 h-11 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-xl opacity-20 blur-lg group-hover:opacity-30 transition-opacity duration-300"></div>
                            </div>
                            
                            <!-- Premium Brand Typography -->
                            <div class="hidden lg:block">
                                <h1 class="text-xl font-bold bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent tracking-tight">
                                    DuwaneTech
                                </h1>
                                <p class="text-slate-400 text-xs font-medium tracking-wide">
                                    Enterprise Admin Suite
                                </p>
                            </div>
                        </div>
                        
                        <!-- Elegant Separator -->
                        <div class="h-8 w-px bg-gradient-to-b from-transparent via-slate-600 to-transparent"></div>
                        
                        <!-- Current Context Indicator -->
                        <div class="hidden xl:flex items-center space-x-3 px-4 py-2 bg-slate-800/50 rounded-lg backdrop-blur-sm border border-slate-700/50">
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                            <span class="text-slate-300 text-sm font-medium">@yield('title', 'Dashboard')</span>
                        </div>
                    </div>

                    <!-- CENTER: Quick Actions & Search -->
                    <div class="flex-1 max-w-lg mx-8 hidden md:block">
                        <!-- Future: Global Search & Quick Actions -->
                        <div class="relative">
                            <div class="w-full h-10 bg-slate-800/30 border border-slate-700/50 rounded-lg flex items-center px-3 backdrop-blur-sm">
                                <svg class="w-4 h-4 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" placeholder="Quick search..." class="bg-transparent text-slate-300 placeholder-slate-500 text-sm flex-1 outline-none">
                                <kbd class="hidden sm:inline-block px-2 py-1 text-xs text-slate-500 bg-slate-700/50 rounded border border-slate-600">⌘K</kbd>
                            </div>
                        </div>
                    </div>
                    <!-- RIGHT: Professional Action Center -->
                    <div class="flex items-center space-x-4">
                        
                        <!-- System Health Indicator -->
                        <div class="hidden lg:flex items-center space-x-2 px-3 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-lg backdrop-blur-sm">
                            <div class="relative">
                                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                <div class="absolute inset-0 w-2 h-2 bg-emerald-400 rounded-full animate-ping opacity-75"></div>
                            </div>
                            <span class="text-emerald-400 text-xs font-semibold tracking-wide">ONLINE</span>
                        </div>
                        
                        <!-- Premium Notifications Bell -->
                        <div class="relative">
                            @include('admin.components.notification-center')
                        </div>
                        
                        <!-- Action Separator -->
                        <div class="h-6 w-px bg-slate-700/50"></div>
                        
                        <!-- Live Store Access -->
                        <a href="{{ url('/') }}" target="_blank" 
                           class="group relative overflow-hidden px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-xl font-semibold text-sm shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-0.5 border border-blue-500/20">
                            
                            <!-- Button Glow Effect -->
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 opacity-0 group-hover:opacity-20 transition-opacity duration-300 blur-xl"></div>
                            
                            <div class="relative flex items-center space-x-2">
                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                <span>View Store</span>
                                <div class="w-1 h-1 bg-white/60 rounded-full group-hover:bg-white/80 transition-colors duration-300"></div>
                            </div>
                        </a>
                        
                        <!-- Executive User Profile -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="group flex items-center space-x-3 p-2 hover:bg-slate-800/50 rounded-xl transition-all duration-300 border border-transparent hover:border-slate-700/50 backdrop-blur-sm">
                                
                                <!-- Premium Avatar -->
                                <div class="relative">
                                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 via-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg ring-2 ring-slate-700/50 group-hover:ring-slate-600/50 transition-all duration-300">
                                        <span class="text-white font-bold text-sm">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <!-- Status Indicator -->
                                    <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-slate-900 shadow-lg">
                                        <div class="w-full h-full bg-emerald-400 rounded-full animate-pulse"></div>
                                    </div>
                                </div>
                                
                                <!-- User Info -->
                                <div class="hidden xl:block text-left">
                                    <p class="text-slate-200 font-semibold text-sm leading-tight">{{ Auth::user()->name }}</p>
                                    <p class="text-slate-500 text-xs">Administrator</p>
                                </div>
                                
                                <!-- Dropdown Arrow -->
                                <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-400 group-hover:rotate-180 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Executive User Dropdown -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                                 class="absolute right-0 mt-3 w-80 bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-700/50 py-6 z-[10000] overflow-hidden">
                                
                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-purple-500/5"></div>
                                
                                <!-- Profile Header -->
                                <div class="relative px-6 pb-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-violet-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-xl">
                                            <span class="text-white font-bold text-xl">
                                                {{ substr(Auth::user()->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-slate-100 font-bold text-lg leading-tight">{{ Auth::user()->name }}</h3>
                                            <p class="text-slate-400 text-sm">{{ Auth::user()->email }}</p>
                                            <div class="flex items-center space-x-2 mt-2">
                                                <div class="px-2 py-1 bg-violet-500/20 border border-violet-500/30 rounded-lg">
                                                    <span class="text-violet-300 text-xs font-semibold">ADMIN</span>
                                                </div>
                                                <div class="px-2 py-1 bg-emerald-500/20 border border-emerald-500/30 rounded-lg">
                                                    <span class="text-emerald-300 text-xs font-semibold">ONLINE</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="relative px-6 space-y-2">
                                    <a href="{{ route('admin.profile') }}" class="group flex items-center space-x-3 p-3 hover:bg-slate-800/50 rounded-xl transition-all duration-200 border border-transparent hover:border-slate-700/50">
                                        <div class="w-10 h-10 bg-blue-500/20 border border-blue-500/30 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-colors duration-200">
                                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-slate-200 font-semibold text-sm">Profile Settings</p>
                                            <p class="text-slate-500 text-xs">Manage your account</p>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-600 group-hover:text-slate-400 group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                    
                                    <a href="{{ route('admin.settings.index') }}" class="group flex items-center space-x-3 p-3 hover:bg-slate-800/50 rounded-xl transition-all duration-200 border border-transparent hover:border-slate-700/50">
                                        <div class="w-10 h-10 bg-purple-500/20 border border-purple-500/30 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-colors duration-200">
                                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-slate-200 font-semibold text-sm">System Settings</p>
                                            <p class="text-slate-500 text-xs">Configure preferences</p>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-600 group-hover:text-slate-400 group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                                
                                <!-- Divider -->
                                <div class="mx-6 my-4 h-px bg-gradient-to-r from-transparent via-slate-700 to-transparent"></div>
                                
                                <!-- Sign Out -->
                                <div class="relative px-6">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="group flex items-center space-x-3 w-full p-3 hover:bg-red-500/10 rounded-xl transition-all duration-200 border border-transparent hover:border-red-500/30">
                                            <div class="w-10 h-10 bg-red-500/20 border border-red-500/30 rounded-lg flex items-center justify-center group-hover:bg-red-500/30 transition-colors duration-200">
                                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1 text-left">
                                                <p class="text-red-400 font-semibold text-sm">Sign Out</p>
                                                <p class="text-red-500/70 text-xs">End your session</p>
                                            </div>
                                            <svg class="w-4 h-4 text-red-600 group-hover:text-red-400 group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Pusher JS for real-time functionality -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>