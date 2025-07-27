{{-- Mobile Navigation Header Component --}}
<header class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-white border-b border-gray-200 shadow-sm"
        x-data="mobileNavigation()">
    
    <div class="flex items-center justify-between px-4 py-3">
        {{-- Mobile Menu Button (Hamburger) --}}
        <button @click="toggleSidebar()" 
                class="flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'text-blue-600': sidebarOpen }"
                aria-label="Toggle navigation menu"
                :aria-expanded="sidebarOpen ? 'true' : 'false'">
            
            {{-- Animated Hamburger Icon --}}
            <div class="w-6 h-6 flex flex-col justify-center items-center relative">
                <span class="w-5 h-0.5 bg-current transform transition-all duration-300 ease-out"
                      :class="{ 'rotate-45 translate-y-1': sidebarOpen, 'rotate-0 translate-y-0': !sidebarOpen }"></span>
                <span class="w-5 h-0.5 bg-current transition-all duration-300 ease-out mt-1"
                      :class="{ 'opacity-0': sidebarOpen, 'opacity-100': !sidebarOpen }"></span>
                <span class="w-5 h-0.5 bg-current transform transition-all duration-300 ease-out mt-1"
                      :class="{ '-rotate-45 -translate-y-1': sidebarOpen, 'rotate-0 translate-y-0': !sidebarOpen }"></span>
            </div>
        </button>

        {{-- Mobile Brand --}}
        <div class="flex items-center space-x-2">
            <img class="w-8 h-8" 
                 src="{{ asset('images/logo-icon.svg') }}" 
                 alt="Logo" 
                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiByeD0iOCIgZmlsbD0iIzM5ODNGNiIvPgo8cGF0aCBkPSJNMTYgOEwxNiAyNCIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KPHBhdGggZD0iTTggMTZMMjQgMTYiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+Cjwvc3ZnPgo='">
            <div class="flex flex-col">
                <h1 class="text-lg font-bold text-gray-900">Admin</h1>
            </div>
        </div>

        {{-- Mobile Actions --}}
        <div class="flex items-center space-x-2">
            {{-- Notifications --}}
            <button class="relative flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-label="Notifications">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 17H9a6 6 0 01-6-6V9a6 6 0 016-6h6a6 6 0 016 6v8z"></path>
                </svg>
                {{-- Notification Badge --}}
                <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full text-xs flex items-center justify-center text-white">3</span>
            </button>

            {{-- User Menu --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" 
                        @click.away="open = false"
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="User menu"
                        :aria-expanded="open ? 'true' : 'false'">
                    <span class="text-sm font-medium">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                </button>

                {{-- User Dropdown --}}
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                     role="menu">
                    
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                    </div>
                    
                    <a href="{{ route('admin.profile') }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                       role="menuitem">Profile</a>
                    
                    <a href="{{ route('admin.settings.index') }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                       role="menuitem">Settings</a>
                    
                    <div class="border-t border-gray-100 mt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200"
                                    role="menuitem">Sign out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Mobile Navigation Sidebar Overlay --}}
<div x-data="mobileSidebarOverlay()" class="lg:hidden">
    {{-- Backdrop --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40"
         @click="closeSidebar()"
         aria-hidden="true"></div>

    {{-- Mobile Sidebar --}}
    <aside x-show="sidebarOpen"
           x-transition:enter="transition ease-in-out duration-300 transform"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out duration-300 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-50 w-80 bg-white border-r border-gray-200 shadow-xl flex flex-col"
           @keydown.escape="closeSidebar()"
           role="navigation"
           aria-label="Mobile navigation">

        {{-- Mobile Sidebar Header --}}
        <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <img class="w-8 h-8" 
                     src="{{ asset('images/logo-icon.svg') }}" 
                     alt="Logo" 
                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiByeD0iOCIgZmlsbD0iIzM5ODNGNiIvPgo8cGF0aCBkPSJNMTYgOEwxNiAyNCIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KPHBhdGggZD0iTTggMTZMMjQgMTYiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+Cjwvc3ZnPgo='">
                <div class="flex flex-col">
                    <h1 class="text-lg font-bold text-gray-900">Admin Panel</h1>
                    <p class="text-xs text-gray-500">E-Commerce Dashboard</p>
                </div>
            </div>
            
            <button @click="closeSidebar()" 
                    class="flex items-center justify-center w-8 h-8 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-label="Close navigation">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Mobile Navigation Content --}}
        <nav class="flex-1 px-4 py-4 overflow-y-auto" role="menubar">
            <ul class="space-y-2" role="none">
                
                {{-- Dashboard --}}
                <li role="none">
                    <a href="{{ route('admin.dashboard') }}" 
                       @click="closeSidebar()"
                       class="mobile-nav-item group flex items-center px-4 py-3 text-base font-medium rounded-lg transition-all duration-200"
                       :class="{ 
                           'bg-blue-50 text-blue-700 border-l-4 border-blue-600': isActiveMobile('{{ route('admin.dashboard') }}'),
                           'text-gray-700 hover:bg-gray-50 hover:text-gray-900': !isActiveMobile('{{ route('admin.dashboard') }}')
                       }"
                       role="menuitem"
                       :aria-current="isActiveMobile('{{ route('admin.dashboard') }}') ? 'page' : 'false'">
                        
                        <svg class="flex-shrink-0 w-6 h-6 mr-4 transition-colors duration-200"
                             :class="{
                                 'text-blue-600': isActiveMobile('{{ route('admin.dashboard') }}'),
                                 'text-gray-400 group-hover:text-gray-500': !isActiveMobile('{{ route('admin.dashboard') }}')
                             }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v4H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>

                {{-- Analytics --}}
                <li role="none">
                    <a href="{{ route('admin.reports.index') }}" 
                       @click="closeSidebar()"
                       class="mobile-nav-item group flex items-center px-4 py-3 text-base font-medium rounded-lg transition-all duration-200"
                       :class="{ 
                           'bg-blue-50 text-blue-700 border-l-4 border-blue-600': isActiveMobile('{{ route('admin.reports.index') }}'),
                           'text-gray-700 hover:bg-gray-50 hover:text-gray-900': !isActiveMobile('{{ route('admin.reports.index') }}')
                       }"
                       role="menuitem"
                       :aria-current="isActiveMobile('{{ route('admin.reports.index') }}') ? 'page' : 'false'">
                        
                        <svg class="flex-shrink-0 w-6 h-6 mr-4 transition-colors duration-200"
                             :class="{
                                 'text-blue-600': isActiveMobile('{{ route('admin.reports.index') }}'),
                                 'text-gray-400 group-hover:text-gray-500': !isActiveMobile('{{ route('admin.reports.index') }}')
                             }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Analytics
                    </a>
                </li>

                {{-- Section Divider --}}
                <li role="none" class="py-2">
                    <div class="border-t border-gray-200"></div>
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Orders Management
                    </div>
                </li>

                {{-- Orders with Submenu --}}
                <li role="none" x-data="{ submenuOpen: false }">
                    <button @click="submenuOpen = !submenuOpen" 
                            class="mobile-nav-item group flex items-center w-full px-4 py-3 text-base font-medium rounded-lg transition-all duration-200"
                            :class="{ 
                                'bg-blue-50 text-blue-700': isActiveMobile('{{ route('admin.orders.index') }}'),
                                'text-gray-700 hover:bg-gray-50 hover:text-gray-900': !isActiveMobile('{{ route('admin.orders.index') }}')
                            }"
                            role="menuitem"
                            :aria-expanded="submenuOpen ? 'true' : 'false'">
                        
                        <svg class="flex-shrink-0 w-6 h-6 mr-4 transition-colors duration-200"
                             :class="{
                                 'text-blue-600': isActiveMobile('{{ route('admin.orders.index') }}'),
                                 'text-gray-400 group-hover:text-gray-500': !isActiveMobile('{{ route('admin.orders.index') }}')
                             }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11h8"></path>
                        </svg>
                        
                        <span class="flex-1 text-left">Orders</span>
                        
                        {{-- Submenu Toggle Arrow --}}
                        <svg class="w-5 h-5 transition-transform duration-200"
                             :class="{ 'rotate-90': submenuOpen }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    {{-- Orders Submenu --}}
                    <div x-show="submenuOpen" 
                         x-transition:enter="transition-all duration-200 ease-out"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition-all duration-150 ease-in"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="mt-2 ml-10 space-y-1" 
                         role="menu">
                        
                        <a href="{{ route('admin.orders.index') }}" 
                           @click="closeSidebar()"
                           class="mobile-submenu-item block px-4 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           :class="{ 'text-blue-600 bg-blue-50': isActiveMobile('{{ route('admin.orders.index') }}') }"
                           role="menuitem">All Orders</a>
                        
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
                           @click="closeSidebar()"
                           class="mobile-submenu-item block px-4 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           role="menuitem">Pending Orders</a>
                        
                        <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" 
                           @click="closeSidebar()"
                           class="mobile-submenu-item block px-4 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           role="menuitem">Completed Orders</a>
                    </div>
                </li>

                {{-- Section Divider --}}
                <li role="none" class="py-2">
                    <div class="border-t border-gray-200"></div>
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Products & Inventory
                    </div>
                </li>

                {{-- Products with Submenu --}}
                <li role="none" x-data="{ submenuOpen: false }">
                    <button @click="submenuOpen = !submenuOpen" 
                            class="mobile-nav-item group flex items-center w-full px-4 py-3 text-base font-medium rounded-lg transition-all duration-200"
                            :class="{ 
                                'bg-blue-50 text-blue-700': isActiveMobile('{{ route('admin.products.index') }}'),
                                'text-gray-700 hover:bg-gray-50 hover:text-gray-900': !isActiveMobile('{{ route('admin.products.index') }}')
                            }"
                            role="menuitem"
                            :aria-expanded="submenuOpen ? 'true' : 'false'">
                        
                        <svg class="flex-shrink-0 w-6 h-6 mr-4 transition-colors duration-200"
                             :class="{
                                 'text-blue-600': isActiveMobile('{{ route('admin.products.index') }}'),
                                 'text-gray-400 group-hover:text-gray-500': !isActiveMobile('{{ route('admin.products.index') }}')
                             }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        
                        <span class="flex-1 text-left">Products</span>
                        
                        {{-- Submenu Toggle Arrow --}}
                        <svg class="w-5 h-5 transition-transform duration-200"
                             :class="{ 'rotate-90': submenuOpen }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    {{-- Products Submenu --}}
                    <div x-show="submenuOpen" 
                         x-transition:enter="transition-all duration-200 ease-out"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition-all duration-150 ease-in"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="mt-2 ml-10 space-y-1" 
                         role="menu">
                        
                        <a href="{{ route('admin.products.index') }}" 
                           @click="closeSidebar()"
                           class="mobile-submenu-item block px-4 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           :class="{ 'text-blue-600 bg-blue-50': isActiveMobile('{{ route('admin.products.index') }}') }"
                           role="menuitem">All Products</a>
                        
                        <a href="{{ route('admin.categories.index') }}" 
                           @click="closeSidebar()"
                           class="mobile-submenu-item block px-4 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           :class="{ 'text-blue-600 bg-blue-50': isActiveMobile('{{ route('admin.categories.index') }}') }"
                           role="menuitem">Categories</a>
                        
                        <a href="{{ route('admin.products.index', ['status' => 'out-of-stock']) }}" 
                           @click="closeSidebar()"
                           class="mobile-submenu-item block px-4 py-2 text-sm text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200"
                           role="menuitem">Out of Stock</a>
                    </div>
                </li>

                {{-- Customers --}}
                <li role="none" class="py-2">
                    <div class="border-t border-gray-200"></div>
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Customer Management
                    </div>
                </li>

                <li role="none">
                    <a href="{{ route('admin.users.index') }}" 
                       @click="closeSidebar()"
                       class="mobile-nav-item group flex items-center px-4 py-3 text-base font-medium rounded-lg transition-all duration-200"
                       :class="{ 
                           'bg-blue-50 text-blue-700 border-l-4 border-blue-600': isActiveMobile('{{ route('admin.users.index') }}'),
                           'text-gray-700 hover:bg-gray-50 hover:text-gray-900': !isActiveMobile('{{ route('admin.users.index') }}')
                       }"
                       role="menuitem"
                       :aria-current="isActiveMobile('{{ route('admin.users.index') }}') ? 'page' : 'false'">
                        
                        <svg class="flex-shrink-0 w-6 h-6 mr-4 transition-colors duration-200"
                             :class="{
                                 'text-blue-600': isActiveMobile('{{ route('admin.users.index') }}'),
                                 'text-gray-400 group-hover:text-gray-500': !isActiveMobile('{{ route('admin.users.index') }}')
                             }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Customers
                    </a>
                </li>

                {{-- Settings --}}
                <li role="none" class="py-2">
                    <div class="border-t border-gray-200"></div>
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        System Settings
                    </div>
                </li>

                <li role="none">
                    <a href="{{ route('admin.settings.index') }}" 
                       @click="closeSidebar()"
                       class="mobile-nav-item group flex items-center px-4 py-3 text-base font-medium rounded-lg transition-all duration-200"
                       :class="{ 
                           'bg-blue-50 text-blue-700 border-l-4 border-blue-600': isActiveMobile('{{ route('admin.settings.index') }}'),
                           'text-gray-700 hover:bg-gray-50 hover:text-gray-900': !isActiveMobile('{{ route('admin.settings.index') }}')
                       }"
                       role="menuitem"
                       :aria-current="isActiveMobile('{{ route('admin.settings.index') }}') ? 'page' : 'false'">
                        
                        <svg class="flex-shrink-0 w-6 h-6 mr-4 transition-colors duration-200"
                             :class="{
                                 'text-blue-600': isActiveMobile('{{ route('admin.settings.index') }}'),
                                 'text-gray-400 group-hover:text-gray-500': !isActiveMobile('{{ route('admin.settings.index') }}')
                             }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>
                </li>
            </ul>
        </nav>

        {{-- Mobile Sidebar Footer --}}
        <div class="border-t border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-blue-600">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                </div>
            </div>
        </div>
    </aside>
</div>

{{-- JavaScript for Mobile Navigation --}}
<script>
document.addEventListener('alpine:init', () => {
    // Mobile navigation header component
    Alpine.data('mobileNavigation', () => ({
        sidebarOpen: false,

        init() {
            // Listen for sidebar state changes from other components
            this.$watch('$store.sidebar.mobileOpen', (value) => {
                this.sidebarOpen = value;
            });
        },

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            Alpine.store('sidebar', {
                mobileOpen: this.sidebarOpen
            });
        }
    }));

    // Mobile sidebar overlay component
    Alpine.data('mobileSidebarOverlay', () => ({
        sidebarOpen: false,

        init() {
            // Listen for sidebar state changes
            this.$watch('$store.sidebar.mobileOpen', (value) => {
                this.sidebarOpen = value;
            });
        },

        closeSidebar() {
            this.sidebarOpen = false;
            Alpine.store('sidebar', {
                mobileOpen: false
            });
        },

        isActiveMobile(route) {
            return window.location.href.includes(route) || window.location.pathname === route;
        }
    }));

    // Initialize sidebar store
    Alpine.store('sidebar', {
        mobileOpen: false,
        collapsed: Alpine.$persist(false).as('sidebar-collapsed')
    });
});

// Global function to open mobile sidebar (can be called from anywhere)
function openMobileSidebar() {
    Alpine.store('sidebar').mobileOpen = true;
}
</script>

{{-- Mobile Navigation Styles --}}
<style>
/* Mobile navigation specific styles */
.mobile-nav-item {
    min-height: 44px; /* Minimum touch target size */
    touch-action: manipulation;
}

.mobile-nav-item:active {
    transform: scale(0.98);
    transition: transform 0.1s ease;
}

.mobile-submenu-item {
    min-height: 40px;
    touch-action: manipulation;
}

.mobile-submenu-item:active {
    transform: scale(0.98);
    transition: transform 0.1s ease;
}

/* Ensure proper scrolling on mobile */
@media (max-width: 1023px) {
    body.mobile-nav-open {
        overflow: hidden;
    }
    
    .mobile-nav-item,
    .mobile-submenu-item {
        -webkit-tap-highlight-color: transparent;
    }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    .mobile-nav-item,
    .mobile-submenu-item {
        transition: none;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .mobile-nav-item {
        border: 1px solid transparent;
    }
    
    .mobile-nav-item:hover {
        border-color: currentColor;
    }
    
    .mobile-nav-item[aria-current="page"] {
        border-color: theme('colors.blue.600');
    }
}
</style>
