{{-- Enhanced Sidebar Navigation Component --}}
<aside id="enhanced-sidebar" 
       x-data="sidebarNavigation()" 
       class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white border-r border-gray-200 shadow-lg transition-all duration-300 ease-in-out"
       :class="{
           'w-64': !collapsed,
           'w-16': collapsed,
           '-translate-x-full lg:translate-x-0': !mobileOpen,
           'translate-x-0': mobileOpen
       }"
       role="navigation"
       aria-label="Main navigation"
       @keydown.escape="mobileOpen = false"
       @click.away="isMobile && (mobileOpen = false)">

    {{-- Mobile Overlay --}}
    <div x-show="mobileOpen && isMobile" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-75 lg:hidden"
         aria-hidden="true"
         @click="mobileOpen = false"></div>

    {{-- Sidebar Header --}}
    <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200">
        <div class="flex items-center space-x-3" :class="{ 'justify-center': collapsed }">
            {{-- Logo --}}
            <div class="flex-shrink-0">
                <img class="w-8 h-8" 
                     src="{{ asset('images/logo-icon.svg') }}" 
                     alt="Logo" 
                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiByeD0iOCIgZmlsbD0iIzM5ODNGNiIvPgo8cGF0aCBkPSJNMTYgOEwxNiAyNCIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KPHBhdGggZD0iTTggMTZMMjQgMTYiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+Cjwvc3ZnPgo='">
            </div>
            
            {{-- Brand Text --}}
            <div x-show="!collapsed" 
                 x-transition:enter="transition-opacity duration-300 delay-100"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="flex flex-col">
                <h1 class="text-lg font-bold text-gray-900">Admin Panel</h1>
                <p class="text-xs text-gray-500">E-Commerce</p>
            </div>
        </div>

        {{-- Desktop Toggle Button --}}
        <button @click="toggleCollapse()" 
                class="hidden lg:flex items-center justify-center w-8 h-8 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                aria-label="Toggle sidebar">
            <svg class="w-4 h-4 transition-transform duration-200" 
                 :class="{ 'rotate-180': collapsed }"
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
        </button>

        {{-- Mobile Close Button --}}
        <button @click="mobileOpen = false" 
                class="lg:hidden flex items-center justify-center w-8 h-8 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                aria-label="Close sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    {{-- Navigation Content --}}
    <nav class="flex-1 px-3 py-4 overflow-y-auto"
         role="menubar" 
         :aria-label="collapsed ? 'Collapsed navigation menu' : 'Main navigation menu'">
        
        {{-- Navigation Menu --}}
        <ul class="space-y-1" role="none">
            
            {{-- Dashboard --}}
            <li role="none">
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                   :class="getNavItemClasses('{{ route('admin.dashboard') }}')"
                   role="menuitem"
                   :title="collapsed ? 'Dashboard' : ''"
                   :aria-current="isActive('{{ route('admin.dashboard') }}') ? 'page' : 'false'"
                   @keydown.arrow-down.prevent="focusNext($event.target)"
                   @keydown.arrow-up.prevent="focusPrev($event.target)"
                   tabindex="0">
                    
                    <svg class="nav-icon flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200" 
                         :class="getIconClasses('{{ route('admin.dashboard') }}')"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v4H8V5z"></path>
                    </svg>
                    
                    <span class="nav-text transition-all duration-300"
                          x-show="!collapsed"
                          x-transition:enter="transition-opacity delay-100 duration-300"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition-opacity duration-200"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0">Dashboard</span>
                </a>
            </li>

            {{-- Analytics --}}
            <li role="none">
                <a href="{{ route('admin.reports.index') }}" 
                   class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                   :class="getNavItemClasses('{{ route('admin.reports.index') }}')"
                   role="menuitem"
                   :title="collapsed ? 'Analytics' : ''"
                   :aria-current="isActive('{{ route('admin.reports.index') }}') ? 'page' : 'false'"
                   @keydown.arrow-down.prevent="focusNext($event.target)"
                   @keydown.arrow-up.prevent="focusPrev($event.target)"
                   tabindex="0">
                    
                    <svg class="nav-icon flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200" 
                         :class="getIconClasses('{{ route('admin.reports.index') }}')"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    
                    <span class="nav-text transition-all duration-300"
                          x-show="!collapsed"
                          x-transition:enter="transition-opacity delay-100 duration-300"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition-opacity duration-200"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0">Analytics</span>
                </a>
            </li>

            {{-- Orders Section --}}
            <li role="none" class="pt-4">
                <div x-show="!collapsed" 
                     x-transition:enter="transition-opacity delay-150 duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2" 
                     role="presentation"
                     id="orders-section">
                    Orders Management
                </div>
                
                {{-- Section Divider for collapsed state --}}
                <div x-show="collapsed" 
                     class="mx-3 h-px bg-gray-200 mb-2" 
                     role="presentation"
                     aria-hidden="true"></div>
            </li>

            {{-- Orders --}}
            <li role="none">
                <div x-data="{ hasSubmenu: true, submenuOpen: false }" class="relative">
                    {{-- Main Orders Link --}}
                    <button @click="toggleSubmenu('orders')" 
                            @keydown.arrow-down.prevent="focusNext($event.target)"
                            @keydown.arrow-up.prevent="focusPrev($event.target)"
                            @keydown.arrow-right.prevent="openSubmenu('orders')"
                            @keydown.arrow-left.prevent="closeSubmenu('orders')"
                            class="nav-item group flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                            :class="getNavItemClasses('{{ route('admin.orders.index') }}')"
                            role="menuitem"
                            :title="collapsed ? 'Orders' : ''"
                            :aria-expanded="submenus.orders ? 'true' : 'false'"
                            :aria-current="isActive('{{ route('admin.orders.index') }}') ? 'page' : 'false'"
                            tabindex="0">
                        
                        <svg class="nav-icon flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200" 
                             :class="getIconClasses('{{ route('admin.orders.index') }}')"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11h8"></path>
                        </svg>
                        
                        <span class="nav-text flex-1 text-left transition-all duration-300"
                              x-show="!collapsed"
                              x-transition:enter="transition-opacity delay-100 duration-300"
                              x-transition:enter-start="opacity-0"
                              x-transition:enter-end="opacity-100"
                              x-transition:leave="transition-opacity duration-200"
                              x-transition:leave-start="opacity-100"
                              x-transition:leave-end="opacity-0">Orders</span>
                        
                        {{-- Submenu Toggle Arrow --}}
                        <svg x-show="!collapsed" 
                             class="w-4 h-4 ml-auto transition-transform duration-200"
                             :class="{ 'rotate-90': submenus.orders }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    {{-- Orders Submenu --}}
                    <div x-show="submenus.orders && !collapsed" 
                         x-transition:enter="transition-all duration-200 ease-out"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition-all duration-150 ease-in"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="mt-1 ml-8 space-y-1" 
                         role="menu"
                         aria-labelledby="orders-section">
                        
                        <a href="{{ route('admin.orders.index') }}" 
                           class="submenu-item block px-3 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           :class="{ 'text-blue-600 bg-blue-50': isActive('{{ route('admin.orders.index') }}') }"
                           role="menuitem"
                           @keydown.arrow-down.prevent="focusNext($event.target)"
                           @keydown.arrow-up.prevent="focusPrev($event.target)"
                           tabindex="0">All Orders</a>
                        
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
                           class="submenu-item block px-3 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           role="menuitem"
                           @keydown.arrow-down.prevent="focusNext($event.target)"
                           @keydown.arrow-up.prevent="focusPrev($event.target)"
                           tabindex="0">Pending Orders</a>
                        
                        <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" 
                           class="submenu-item block px-3 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           role="menuitem"
                           @keydown.arrow-down.prevent="focusNext($event.target)"
                           @keydown.arrow-up.prevent="focusPrev($event.target)"
                           tabindex="0">Completed Orders</a>
                    </div>

                    {{-- Collapsed State Tooltip --}}
                    <div x-show="collapsed" 
                         x-data="tooltip()"
                         @mouseenter="show = true" 
                         @mouseleave="show = false"
                         class="relative">
                        <div x-show="show" 
                             x-transition:enter="transition-opacity duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition-opacity duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute left-full top-0 ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded-md whitespace-nowrap z-50"
                             role="tooltip">
                            Orders
                        </div>
                    </div>
                </div>
            </li>

            {{-- Products Section --}}
            <li role="none" class="pt-4">
                <div x-show="!collapsed" 
                     x-transition:enter="transition-opacity delay-150 duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2" 
                     role="presentation"
                     id="products-section">
                    Products & Inventory
                </div>
                
                {{-- Section Divider for collapsed state --}}
                <div x-show="collapsed" 
                     class="mx-3 h-px bg-gray-200 mb-2" 
                     role="presentation"
                     aria-hidden="true"></div>
            </li>

            {{-- Products --}}
            <li role="none">
                <div x-data="{ hasSubmenu: true }" class="relative">
                    {{-- Main Products Link --}}
                    <button @click="toggleSubmenu('products')" 
                            @keydown.arrow-down.prevent="focusNext($event.target)"
                            @keydown.arrow-up.prevent="focusPrev($event.target)"
                            @keydown.arrow-right.prevent="openSubmenu('products')"
                            @keydown.arrow-left.prevent="closeSubmenu('products')"
                            class="nav-item group flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                            :class="getNavItemClasses('{{ route('admin.products.index') }}')"
                            role="menuitem"
                            :title="collapsed ? 'Products' : ''"
                            :aria-expanded="submenus.products ? 'true' : 'false'"
                            :aria-current="isActive('{{ route('admin.products.index') }}') ? 'page' : 'false'"
                            tabindex="0">
                        
                        <svg class="nav-icon flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200" 
                             :class="getIconClasses('{{ route('admin.products.index') }}')"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        
                        <span class="nav-text flex-1 text-left transition-all duration-300"
                              x-show="!collapsed"
                              x-transition:enter="transition-opacity delay-100 duration-300"
                              x-transition:enter-start="opacity-0"
                              x-transition:enter-end="opacity-100"
                              x-transition:leave="transition-opacity duration-200"
                              x-transition:leave-start="opacity-100"
                              x-transition:leave-end="opacity-0">Products</span>
                        
                        {{-- Submenu Toggle Arrow --}}
                        <svg x-show="!collapsed" 
                             class="w-4 h-4 ml-auto transition-transform duration-200"
                             :class="{ 'rotate-90': submenus.products }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    {{-- Products Submenu --}}
                    <div x-show="submenus.products && !collapsed" 
                         x-transition:enter="transition-all duration-200 ease-out"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition-all duration-150 ease-in"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="mt-1 ml-8 space-y-1" 
                         role="menu"
                         aria-labelledby="products-section">
                        
                        <a href="{{ route('admin.products.index') }}" 
                           class="submenu-item block px-3 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           :class="{ 'text-blue-600 bg-blue-50': isActive('{{ route('admin.products.index') }}') }"
                           role="menuitem"
                           @keydown.arrow-down.prevent="focusNext($event.target)"
                           @keydown.arrow-up.prevent="focusPrev($event.target)"
                           tabindex="0">All Products</a>
                        
                        <a href="{{ route('admin.categories.index') }}" 
                           class="submenu-item block px-3 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           :class="{ 'text-blue-600 bg-blue-50': isActive('{{ route('admin.categories.index') }}') }"
                           role="menuitem"
                           @keydown.arrow-down.prevent="focusNext($event.target)"
                           @keydown.arrow-up.prevent="focusPrev($event.target)"
                           tabindex="0">Categories</a>
                        
                        <a href="{{ route('admin.products.index', ['status' => 'out-of-stock']) }}" 
                           class="submenu-item block px-3 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           role="menuitem"
                           @keydown.arrow-down.prevent="focusNext($event.target)"
                           @keydown.arrow-up.prevent="focusPrev($event.target)"
                           tabindex="0">Out of Stock</a>
                    </div>

                    {{-- Collapsed State Tooltip --}}
                    <div x-show="collapsed" 
                         x-data="tooltip()"
                         @mouseenter="show = true" 
                         @mouseleave="show = false"
                         class="relative">
                        <div x-show="show" 
                             x-transition:enter="transition-opacity duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition-opacity duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute left-full top-0 ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded-md whitespace-nowrap z-50"
                             role="tooltip">
                            Products
                        </div>
                    </div>
                </div>
            </li>

            {{-- Customers Section --}}
            <li role="none" class="pt-4">
                <div x-show="!collapsed" 
                     x-transition:enter="transition-opacity delay-150 duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2" 
                     role="presentation"
                     id="customers-section">
                    Customer Management
                </div>
                
                {{-- Section Divider for collapsed state --}}
                <div x-show="collapsed" 
                     class="mx-3 h-px bg-gray-200 mb-2" 
                     role="presentation"
                     aria-hidden="true"></div>
            </li>

            {{-- Customers --}}
            <li role="none">
                <a href="{{ route('admin.users.index') }}" 
                   class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                   :class="getNavItemClasses('{{ route('admin.users.index') }}')"
                   role="menuitem"
                   :title="collapsed ? 'Customers' : ''"
                   :aria-current="isActive('{{ route('admin.users.index') }}') ? 'page' : 'false'"
                   @keydown.arrow-down.prevent="focusNext($event.target)"
                   @keydown.arrow-up.prevent="focusPrev($event.target)"
                   tabindex="0">
                    
                    <svg class="nav-icon flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200" 
                         :class="getIconClasses('{{ route('admin.users.index') }}')"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    
                    <span class="nav-text transition-all duration-300"
                          x-show="!collapsed"
                          x-transition:enter="transition-opacity delay-100 duration-300"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition-opacity duration-200"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0">Customers</span>
                </a>
            </li>

            {{-- Settings Section --}}
            <li role="none" class="pt-4">
                <div x-show="!collapsed" 
                     x-transition:enter="transition-opacity delay-150 duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2" 
                     role="presentation"
                     id="settings-section">
                    System Settings
                </div>
                
                {{-- Section Divider for collapsed state --}}
                <div x-show="collapsed" 
                     class="mx-3 h-px bg-gray-200 mb-2" 
                     role="presentation"
                     aria-hidden="true"></div>
            </li>

            {{-- Settings --}}
            <li role="none">
                <a href="{{ route('admin.settings.index') }}" 
                   class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                   :class="getNavItemClasses('{{ route('admin.settings.index') }}')"
                   role="menuitem"
                   :title="collapsed ? 'Settings' : ''"
                   :aria-current="isActive('{{ route('admin.settings.index') }}') ? 'page' : 'false'"
                   @keydown.arrow-down.prevent="focusNext($event.target)"
                   @keydown.arrow-up.prevent="focusPrev($event.target)"
                   tabindex="0">
                    
                    <svg class="nav-icon flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200" 
                         :class="getIconClasses('{{ route('admin.settings.index') }}')"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    
                    <span class="nav-text transition-all duration-300"
                          x-show="!collapsed"
                          x-transition:enter="transition-opacity delay-100 duration-300"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition-opacity duration-200"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0">Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    {{-- Sidebar Footer --}}
    <div class="border-t border-gray-200 p-4">
        <div class="flex items-center space-x-2" :class="{ 'justify-center': collapsed }">
            {{-- User Profile --}}
            <div class="flex items-center space-x-2 flex-1" x-show="!collapsed" x-transition>
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-blue-600">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                </div>
            </div>
            
            {{-- Collapsed State Avatar --}}
            <div x-show="collapsed" 
                 x-data="tooltip()"
                 @mouseenter="show = true" 
                 @mouseleave="show = false"
                 class="relative flex items-center justify-center">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-blue-600">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                </div>
                <div x-show="show" 
                     x-transition:enter="transition-opacity duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute left-full bottom-0 ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded-md whitespace-nowrap z-50"
                     role="tooltip">
                    {{ auth()->user()->name ?? 'Admin' }}
                </div>
            </div>
        </div>
    </div>
</aside>

{{-- JavaScript for Sidebar Navigation --}}
<script>
document.addEventListener('alpine:init', () => {
    // Tooltip component
    Alpine.data('tooltip', () => ({
        show: false
    }));

    // Main sidebar navigation component
    Alpine.data('sidebarNavigation', () => ({
        collapsed: Alpine.$persist(false).as('sidebar-collapsed'),
        mobileOpen: false,
        isMobile: window.innerWidth < 1024,
        submenus: Alpine.$persist({
            orders: false,
            products: false,
            customers: false,
            settings: false
        }).as('sidebar-submenus'),

        init() {
            // Handle window resize
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 1024;
                if (!this.isMobile) {
                    this.mobileOpen = false;
                }
            });

            // Handle permissions
            this.checkPermissions();
        },

        toggleCollapse() {
            this.collapsed = !this.collapsed;
            // Close all submenus when collapsing
            if (this.collapsed) {
                Object.keys(this.submenus).forEach(key => {
                    this.submenus[key] = false;
                });
            }
        },

        toggleMobile() {
            this.mobileOpen = !this.mobileOpen;
        },

        toggleSubmenu(name) {
            if (this.collapsed) return;
            this.submenus[name] = !this.submenus[name];
        },

        openSubmenu(name) {
            if (this.collapsed) return;
            this.submenus[name] = true;
        },

        closeSubmenu(name) {
            this.submenus[name] = false;
        },

        isActive(route) {
            return window.location.href.includes(route) || window.location.pathname === route;
        },

        getNavItemClasses(route) {
            const isActive = this.isActive(route);
            return {
                'bg-blue-50 text-blue-700 border-r-4 border-blue-600': isActive,
                'text-gray-700 hover:bg-gray-50 hover:text-gray-900': !isActive,
                'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset': true
            };
        },

        getIconClasses(route) {
            const isActive = this.isActive(route);
            return {
                'text-blue-600': isActive,
                'text-gray-400 group-hover:text-gray-500': !isActive
            };
        },

        // Keyboard navigation
        focusNext(currentElement) {
            const focusableElements = this.getFocusableElements();
            const currentIndex = focusableElements.indexOf(currentElement);
            const nextIndex = (currentIndex + 1) % focusableElements.length;
            focusableElements[nextIndex].focus();
        },

        focusPrev(currentElement) {
            const focusableElements = this.getFocusableElements();
            const currentIndex = focusableElements.indexOf(currentElement);
            const prevIndex = currentIndex === 0 ? focusableElements.length - 1 : currentIndex - 1;
            focusableElements[prevIndex].focus();
        },

        getFocusableElements() {
            return Array.from(this.$el.querySelectorAll('a[tabindex="0"], button[tabindex="0"]'));
        },

        // Permission-based visibility
        checkPermissions() {
            // This would integrate with your permission system
            // For now, we'll assume all permissions are granted
            return true;
        }
    }));
});

// Mobile hamburger menu trigger (to be used in header)
function openMobileSidebar() {
    Alpine.store('sidebar').mobileOpen = true;
}
</script>

{{-- Sidebar Styles --}}
<style>
/* Enhanced Navigation Styles */
.nav-item {
    position: relative;
    overflow: hidden;
}

.nav-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
    transition: left 0.6s ease;
}

.nav-item:hover::before {
    left: 100%;
}

/* Active state enhancements */
.nav-item[aria-current="page"] {
    font-weight: 600;
    box-shadow: inset 4px 0 0 theme('colors.blue.600');
}

/* Submenu styles */
.submenu-item {
    position: relative;
    transition: all 0.2s ease;
}

.submenu-item::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 4px;
    background-color: theme('colors.gray.400');
    border-radius: 50%;
    transition: all 0.2s ease;
}

.submenu-item:hover::before {
    background-color: theme('colors.blue.600');
    transform: translateY(-50%) scale(1.5);
}

/* Focus styles for accessibility */
.nav-item:focus,
.submenu-item:focus {
    outline: 2px solid theme('colors.blue.500');
    outline-offset: -2px;
    border-radius: 0.5rem;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .nav-item {
        border: 1px solid transparent;
    }
    
    .nav-item:hover {
        border-color: currentColor;
    }
    
    .nav-item[aria-current="page"] {
        border-color: theme('colors.blue.600');
        background-color: theme('colors.blue.50');
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .nav-item,
    .nav-item::before,
    .submenu-item,
    .submenu-item::before {
        transition: none;
    }
    
    * {
        transition: none !important;
        animation: none !important;
    }
}

/* Mobile optimizations */
@media (max-width: 1023px) {
    #enhanced-sidebar {
        touch-action: pan-y;
    }
    
    .nav-item {
        min-height: 44px; /* Minimum touch target size */
    }
}

/* Dark mode support (if needed) */
@media (prefers-color-scheme: dark) {
    /* Dark mode styles would go here */
}
</style>
