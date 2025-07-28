<!-- Main Navigation -->
<ul class="space-y-1 px-3" role="menu">
    <!-- Dashboard -->
    <li role="none">
        <a href="{{ route('admin.dashboard') }}" 
           class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
           role="menuitem"
           :title="sidebarCollapsed ? 'Dashboard' : ''"
           aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : 'false' }}">
            <svg class="flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v4H8V5z"></path>
            </svg>
            <span class="transition-opacity duration-300"
                  :class="{ 'opacity-0 hidden': sidebarCollapsed }"
                  x-show="!sidebarCollapsed"
                  x-transition>Dashboard</span>
        </a>
    </li>

    <!-- Analytics -->
    <li role="none">
        <a href="{{ route('admin.analytics') }}" 
           class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.analytics*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
           role="menuitem"
           :title="sidebarCollapsed ? 'Analytics' : ''"
           aria-current="{{ request()->routeIs('admin.analytics*') ? 'page' : 'false' }}">
            <svg class="flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200 {{ request()->routeIs('admin.analytics*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span class="transition-opacity duration-300"
                  :class="{ 'opacity-0 hidden': sidebarCollapsed }"
                  x-show="!sidebarCollapsed"
                  x-transition>Analytics</span>
        </a>
    </li>

    <!-- Orders Section -->
    <li class="pt-4" role="none" x-show="!sidebarCollapsed" x-transition>
        <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider" 
             role="presentation"
             id="orders-section">
            Orders
        </div>
    </li>

    <!-- All Orders -->
    <li role="none">
        <a href="{{ route('admin.orders.index') }}" 
           class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.orders.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
           role="menuitem"
           :title="sidebarCollapsed ? 'Orders' : ''"
           aria-current="{{ request()->routeIs('admin.orders.*') ? 'page' : 'false' }}"
           aria-describedby="{{ !sidebarCollapsed ? 'orders-section' : '' }}">
            <svg class="flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200 {{ request()->routeIs('admin.orders.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11h8"></path>
            </svg>
            <span class="transition-opacity duration-300"
                  :class="{ 'opacity-0 hidden': sidebarCollapsed }"
                  x-show="!sidebarCollapsed"
                  x-transition>Orders</span>
        </a>
    </li>

    <!-- Products Section -->
    <li class="pt-4" role="none" x-show="!sidebarCollapsed" x-transition>
        <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider" 
             role="presentation"
             id="products-section">
            Products
        </div>
    </li>

    <!-- All Products -->
    <li role="none">
        <a href="{{ route('admin.products.index') }}" 
           class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.categories.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
           role="menuitem"
           :title="sidebarCollapsed ? 'Products' : ''"
           aria-current="{{ request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.categories.*') ? 'page' : 'false' }}"
           aria-describedby="{{ !sidebarCollapsed ? 'products-section' : '' }}">
            <svg class="flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200 {{ request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.categories.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <span class="transition-opacity duration-300"
                  :class="{ 'opacity-0 hidden': sidebarCollapsed }"
                  x-show="!sidebarCollapsed"
                  x-transition>Products</span>
        </a>
    </li>

    <!-- Categories -->
    <li role="none">
        <a href="{{ route('admin.products.categories.index') }}" 
           class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.products.categories.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
           role="menuitem"
           :title="sidebarCollapsed ? 'Categories' : ''"
           aria-current="{{ request()->routeIs('admin.products.categories.*') ? 'page' : 'false' }}"
           aria-describedby="{{ !sidebarCollapsed ? 'products-section' : '' }}">
            <svg class="flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200 {{ request()->routeIs('admin.products.categories.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span class="transition-opacity duration-300"
                  :class="{ 'opacity-0 hidden': sidebarCollapsed }"
                  x-show="!sidebarCollapsed"
                  x-transition>Categories</span>
        </a>
    </li>

    <!-- Customers Section -->
    <li class="pt-4" role="none" x-show="!sidebarCollapsed" x-transition>
        <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider" 
             role="presentation"
             id="customers-section">
            Customers
        </div>
    </li>

    <!-- All Customers -->
    <li role="none">
        <a href="{{ route('admin.customers.index') }}" 
           class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.customers.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
           role="menuitem"
           :title="sidebarCollapsed ? 'Customers' : ''"
           aria-current="{{ request()->routeIs('admin.customers.*') ? 'page' : 'false' }}"
           aria-describedby="{{ !sidebarCollapsed ? 'customers-section' : '' }}">
            <svg class="flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200 {{ request()->routeIs('admin.customers.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <span class="transition-opacity duration-300"
                  :class="{ 'opacity-0 hidden': sidebarCollapsed }"
                  x-show="!sidebarCollapsed"
                  x-transition>Customers</span>
        </a>
    </li>

    <!-- Settings Section -->
    <li class="pt-4" role="none" x-show="!sidebarCollapsed" x-transition>
        <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider" 
             role="presentation"
             id="settings-section">
            Settings
        </div>
    </li>

    <!-- System Settings -->
    <li role="none">
        <a href="{{ route('admin.settings.index') }}" 
           class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
           role="menuitem"
           :title="sidebarCollapsed ? 'Settings' : ''"
           aria-current="{{ request()->routeIs('admin.settings.*') ? 'page' : 'false' }}"
           aria-describedby="{{ !sidebarCollapsed ? 'settings-section' : '' }}">
            <svg class="flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200 {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="transition-opacity duration-300"
                  :class="{ 'opacity-0 hidden': sidebarCollapsed }"
                  x-show="!sidebarCollapsed"
                  x-transition>Settings</span>
        </a>
    </li>

    <!-- User Management -->
    <li role="none">
        <a href="{{ route('admin.users.index') }}" 
           class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
           role="menuitem"
           :title="sidebarCollapsed ? 'Users' : ''"
           aria-current="{{ request()->routeIs('admin.users.*') ? 'page' : 'false' }}"
           aria-describedby="{{ !sidebarCollapsed ? 'settings-section' : '' }}">
            <svg class="flex-shrink-0 w-5 h-5 mr-3 transition-colors duration-200 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <span class="transition-opacity duration-300"
                  :class="{ 'opacity-0 hidden': sidebarCollapsed }"
                  x-show="!sidebarCollapsed"
                  x-transition>Users</span>
        </a>
    </li>
</ul>

<!-- Collapsed Sidebar Tooltips -->
<div x-show="sidebarCollapsed" x-transition class="mt-4 px-3">
    <div class="border-t border-gray-200 pt-4">
        <div class="flex flex-col items-center space-y-2">
            <!-- Quick Action Buttons for Collapsed State -->
            <button type="button" 
                    class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors duration-200 flex items-center justify-center"
                    title="New Order"
                    aria-label="Create new order">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </button>
            
            <button type="button" 
                    class="w-10 h-10 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors duration-200 flex items-center justify-center"
                    title="Quick Search"
                    aria-label="Quick search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<style>
/* Custom focus styles for navigation links */
.nav-link:focus {
    outline: 2px solid #3b82f6;
    outline-offset: -2px;
    border-radius: 0.375rem;
}

/* Smooth hover animations */
.nav-link {
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.nav-link:hover::before {
    left: 100%;
}

/* Active state enhancement */
.nav-link[aria-current="page"] {
    font-weight: 600;
    position: relative;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .nav-link {
        border: 1px solid transparent;
    }
    
    .nav-link:hover {
        border-color: currentColor;
    }
    
    .nav-link[aria-current="page"] {
        border-color: #3b82f6;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .nav-link,
    .nav-link::before {
        transition: none;
    }
}
</style>
