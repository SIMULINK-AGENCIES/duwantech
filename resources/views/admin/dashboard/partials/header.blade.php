{{-- Enhanced Top Header Component --}}
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30"
        x-data="headerComponent()">
    
    <div class="flex items-center justify-between px-4 py-3 lg:px-6">
        
        {{-- Left Section: Mobile Menu & Breadcrumbs --}}
        <div class="flex items-center space-x-4">
            {{-- Mobile Menu Toggle --}}
            <button @click="toggleMobileSidebar()"
                    class="lg:hidden flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-label="Open navigation menu">
                
                {{-- Animated Hamburger --}}
                <div class="w-6 h-6 flex flex-col justify-center items-center relative">
                    <span class="w-5 h-0.5 bg-current transform transition-all duration-300 ease-out"
                          :class="{ 'rotate-45 translate-y-1': mobileMenuOpen, 'rotate-0 translate-y-0': !mobileMenuOpen }"></span>
                    <span class="w-5 h-0.5 bg-current transition-all duration-300 ease-out mt-1"
                          :class="{ 'opacity-0': mobileMenuOpen, 'opacity-100': !mobileMenuOpen }"></span>
                    <span class="w-5 h-0.5 bg-current transform transition-all duration-300 ease-out mt-1"
                          :class="{ '-rotate-45 -translate-y-1': mobileMenuOpen, 'rotate-0 translate-y-0': !mobileMenuOpen }"></span>
                </div>
            </button>

            {{-- Breadcrumbs --}}
            <nav class="hidden md:flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}" 
                   class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    Dashboard
                </a>
                @if(request()->routeIs('admin.dashboard'))
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">Overview</span>
                @elseif(request()->routeIs('admin.orders*'))
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">Orders</span>
                @elseif(request()->routeIs('admin.products*'))
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">Products</span>
                @elseif(request()->routeIs('admin.users*'))
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">Customers</span>
                @elseif(request()->routeIs('admin.reports*'))
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">Analytics</span>
                @elseif(request()->routeIs('admin.settings*'))
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">Settings</span>
                @endif
            </nav>
        </div>

        {{-- Center Section: Global Search --}}
        <div class="flex-1 max-w-lg mx-4 hidden md:block" x-data="{ localSearchOpen: false }">
            <div class="relative">
                {{-- Search Input --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text"
                           x-data="globalSearch()"
                           x-model="searchQuery"
                           @input.debounce.300ms="performSearch"
                           @keydown.escape="clearSearch(); localSearchOpen = false; $store.dropdowns.closeAll()"
                           @keydown.arrow-down.prevent="highlightNext"
                           @keydown.arrow-up.prevent="highlightPrev"
                           @keydown.enter.prevent="selectHighlighted"
                           @focus="if(searchQuery.length >= 2) { localSearchOpen = true; $store.dropdowns.open('search'); }"
                           @click="if(searchQuery.length >= 2) { localSearchOpen = true; $store.dropdowns.open('search'); }"
                           class="block w-full pl-10 pr-12 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors duration-200"
                           placeholder="Search orders, products, customers... (Ctrl+K)"
                           autocomplete="off">
                    
                    {{-- Search Shortcut Hint --}}
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <kbd class="hidden sm:inline-flex items-center px-2 py-1 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded">
                            ⌘K
                        </kbd>
                    </div>
                </div>

                {{-- Search Results Dropdown --}}
                <div x-show="$store.dropdowns.active === 'search' && searchQuery.length >= 2 && (searchResults.length > 0 || isLoading)"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     @click.away="$store.dropdowns.closeAll()"
                     class="absolute z-50 mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-auto"
                     x-data="globalSearch()">
                    
                    {{-- Loading State --}}
                    <div x-show="isLoading" class="px-4 py-3 text-sm text-gray-500 flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Searching...
                    </div>

                    {{-- Search Results --}}
                    <div x-show="!isLoading && searchResults.length > 0">
                        {{-- Category Headers and Results --}}
                        <template x-for="(category, categoryName) in groupedResults" :key="categoryName">
                            <div>
                                {{-- Category Header --}}
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100">
                                    <span x-text="categoryName"></span>
                                    <span class="ml-2 text-gray-400" x-text="`(${category.length})`"></span>
                                </div>
                                
                                {{-- Category Results --}}
                                <template x-for="(result, index) in category" :key="`${categoryName}-${index}`">
                                    <a :href="result.url"
                                       @click="selectResult(result)"
                                       class="block px-4 py-3 hover:bg-gray-50 transition-colors duration-150 border-b border-gray-50 last:border-b-0"
                                       :class="{ 'bg-blue-50': result.highlighted }">
                                        <div class="flex items-center space-x-3">
                                            {{-- Result Icon --}}
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                                     :class="result.iconBg">
                                                    <span x-html="result.icon" class="w-4 h-4"></span>
                                                </div>
                                            </div>
                                            
                                            {{-- Result Content --}}
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate" x-text="result.title"></p>
                                                <p class="text-sm text-gray-500 truncate" x-text="result.description"></p>
                                            </div>
                                            
                                            {{-- Result Badge --}}
                                            <div x-show="result.badge" class="flex-shrink-0">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                      :class="result.badgeClass"
                                                      x-text="result.badge"></span>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </template>

                        {{-- Quick Actions Footer --}}
                        <div class="px-4 py-2 bg-gray-50 border-t border-gray-100">
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Press Enter to select</span>
                                <span>ESC to close</span>
                            </div>
                        </div>
                    </div>

                    {{-- No Results --}}
                    <div x-show="!isLoading && searchQuery.length >= 2 && searchResults.length === 0"
                         class="px-4 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try searching for orders, products, or customers.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Section: Actions --}}
        <div class="flex items-center space-x-2">
            
            {{-- Mobile Search Toggle --}}
            <button @click="showMobileSearch = !showMobileSearch"
                    class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-label="Search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            {{-- Quick Add Dropdown --}}
            <div class="relative">
                <button @click="$store.dropdowns.toggle('quickAdd')"
                        class="flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="{ 'text-blue-600 bg-blue-50': $store.dropdowns.active === 'quickAdd' }"
                        aria-label="Quick add"
                        :aria-expanded="$store.dropdowns.active === 'quickAdd'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>

                <div x-show="$store.dropdowns.active === 'quickAdd'"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     @click.away="$store.dropdowns.closeAll()"
                     class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                    
                    <a href="{{ route('admin.products.create') }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <span>New Product</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.categories.create') }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <span>New Category</span>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Notifications Center --}}
            @include('admin.dashboard.partials.notification-center')

            {{-- User Profile Dropdown --}}
            @include('admin.dashboard.partials.user-profile-dropdown')
        </div>
    </div>

    {{-- Mobile Search Overlay --}}
    <div x-show="showMobileSearch"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="md:hidden absolute top-full left-0 right-0 bg-white border-b border-gray-200 shadow-lg p-4 z-40">
        
        <div class="relative" x-data="globalSearch()">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text"
                   x-model="searchQuery"
                   @input.debounce.300ms="performSearch"
                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Search orders, products, customers..."
                   autocomplete="off">
        </div>
    </div>
</header>

{{-- Header Component JavaScript --}}
<script>
document.addEventListener('alpine:init', () => {
    // Global dropdown store for managing state across components
    Alpine.store('dropdowns', {
        active: null,
        
        open(dropdown) {
            this.active = dropdown;
        },
        
        close() {
            this.active = null;
        },
        
        closeAll() {
            this.active = null;
        },
        
        toggle(dropdown) {
            if (this.active === dropdown) {
                this.active = null;
            } else {
                this.active = dropdown;
            }
        },
        
        isOpen(dropdown) {
            return this.active === dropdown;
        }
    });

    // Main header component
    Alpine.data('headerComponent', () => ({
        mobileMenuOpen: false,
        showMobileSearch: false,

        init() {
            // Global keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Ctrl+K or Cmd+K to focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.querySelector('input[placeholder*="Search"]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }
                
                // ESC to close all dropdowns
                if (e.key === 'Escape') {
                    this.$store.dropdowns.closeAll();
                    this.showMobileSearch = false;
                }
            });

            // Global click handler to close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                // Check if click is outside any dropdown
                const isDropdownClick = e.target.closest('[x-data*="dropdown"]') || 
                                      e.target.closest('[data-dropdown]') ||
                                      e.target.closest('.dropdown-content');
                
                if (!isDropdownClick) {
                    this.$store.dropdowns.closeAll();
                }
            });
        },

        toggleMobileSidebar() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
            // Trigger sidebar toggle in main component
            if (typeof openMobileSidebar === 'function') {
                openMobileSidebar();
            }
        }
    }));

    // Global search component
    Alpine.data('globalSearch', () => ({
        searchQuery: '',
        searchResults: [],
        groupedResults: {},
        isLoading: false,
        highlightedIndex: -1,

        init() {
            // Watch for search query changes
            this.$watch('searchQuery', (value) => {
                if (!value.trim() || value.length < 2) {
                    this.searchResults = [];
                    this.groupedResults = {};
                    this.$store.dropdowns.close();
                }
            });
        },

        async performSearch() {
            if (!this.searchQuery.trim() || this.searchQuery.length < 2) {
                this.searchResults = [];
                this.groupedResults = {};
                this.$store.dropdowns.close();
                return;
            }

            this.isLoading = true;
            this.$store.dropdowns.open('search');

            try {
                // Try to fetch from actual API endpoint
                const response = await fetch(`/admin/api/search?q=${encodeURIComponent(this.searchQuery)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.searchResults = data.results || [];
                } else {
                    // Fallback to mock data for demo
                    this.searchResults = this.getMockSearchResults();
                }
            } catch (error) {
                console.error('Search error:', error);
                // Fallback to mock data
                this.searchResults = this.getMockSearchResults();
            }

            this.groupedResults = this.groupSearchResults(this.searchResults);
            this.isLoading = false;
            this.highlightedIndex = -1;
        },

        getMockSearchResults() {
            const query = this.searchQuery.toLowerCase();
            const mockData = [
                {
                    type: 'order',
                    title: `Order #ORD-${Math.floor(Math.random() * 10000)}`,
                    description: `Customer order containing "${query}"`,
                    url: '/admin/orders/1',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11h8"></path></svg>',
                    iconBg: 'bg-blue-100 text-blue-600',
                    badge: 'Pending',
                    badgeClass: 'bg-yellow-100 text-yellow-800'
                },
                {
                    type: 'product',
                    title: `Product: ${query.charAt(0).toUpperCase() + query.slice(1)} Item`,
                    description: 'Available in stock - Category: Electronics',
                    url: '/admin/products/1',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
                    iconBg: 'bg-green-100 text-green-600',
                    badge: 'In Stock',
                    badgeClass: 'bg-green-100 text-green-800'
                },
                {
                    type: 'customer',
                    title: `Customer: ${query.charAt(0).toUpperCase() + query.slice(1)} User`,
                    description: 'Active customer since 2024',
                    url: '/admin/users/1',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
                    iconBg: 'bg-purple-100 text-purple-600',
                    badge: 'Active',
                    badgeClass: 'bg-blue-100 text-blue-800'
                }
            ];

            return mockData.filter(item => 
                item.title.toLowerCase().includes(query) || 
                item.description.toLowerCase().includes(query)
            );
        },

        groupSearchResults(results) {
            const grouped = {};
            
            results.forEach(result => {
                let category;
                switch (result.type) {
                    case 'order':
                        category = 'Orders';
                        break;
                    case 'product':
                        category = 'Products';
                        break;
                    case 'customer':
                        category = 'Customers';
                        break;
                    default:
                        category = 'Other';
                }
                
                if (!grouped[category]) {
                    grouped[category] = [];
                }
                grouped[category].push(result);
            });

            return grouped;
        },

        highlightNext() {
            if (this.searchResults.length === 0) return;
            this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.searchResults.length - 1);
            this.updateHighlighted();
        },

        highlightPrev() {
            if (this.searchResults.length === 0) return;
            this.highlightedIndex = Math.max(this.highlightedIndex - 1, 0);
            this.updateHighlighted();
        },

        updateHighlighted() {
            this.searchResults.forEach((result, index) => {
                result.highlighted = index === this.highlightedIndex;
            });
        },

        selectHighlighted() {
            if (this.highlightedIndex >= 0 && this.searchResults[this.highlightedIndex]) {
                this.selectResult(this.searchResults[this.highlightedIndex]);
            }
        },

        selectResult(result) {
            // Navigate to result
            window.location.href = result.url;
            this.clearSearch();
        },

        clearSearch() {
            this.searchQuery = '';
            this.searchResults = [];
            this.groupedResults = {};
            this.highlightedIndex = -1;
            this.$store.dropdowns.close();
        }
    }));
});
</script>

{{-- Header Styles --}}
<style>
/* Header specific styles */
.header-search-results {
    scrollbar-width: thin;
    scrollbar-color: theme('colors.gray.300') transparent;
}

.header-search-results::-webkit-scrollbar {
    width: 6px;
}

.header-search-results::-webkit-scrollbar-track {
    background: transparent;
}

.header-search-results::-webkit-scrollbar-thumb {
    background-color: theme('colors.gray.300');
    border-radius: 3px;
}

.header-search-results::-webkit-scrollbar-thumb:hover {
    background-color: theme('colors.gray.400');
}

/* Keyboard shortcut styling */
kbd {
    font-family: ui-monospace, SFMono-Regular, "SF Mono", Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

/* Focus styles for accessibility */
.header-search-result:focus {
    outline: 2px solid theme('colors.blue.500');
    outline-offset: -2px;
}

/* Mobile search overlay */
@media (max-width: 768px) {
    .mobile-search-overlay {
        backdrop-filter: blur(4px);
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .header-search-results {
        border: 2px solid;
    }
    
    .search-result-item:hover {
        outline: 2px solid;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .header-search-results,
    .search-result-item {
        transition: none;
    }
}
</style>
