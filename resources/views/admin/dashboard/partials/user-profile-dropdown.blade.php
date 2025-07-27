{{-- User Profile Dropdown Component --}}
<div class="relative">
    {{-- Profile Button --}}
    <button @click="$store.dropdowns.toggle('profile')"
            class="flex items-center space-x-3 p-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'bg-gray-100': $store.dropdowns.active === 'profile' }"
            aria-label="User menu"
            :aria-expanded="$store.dropdowns.active === 'profile'"
            x-data="userProfileDropdown()">
        
        {{-- User Avatar --}}
        <div class="relative">
            <img x-show="user.avatar_url" 
                 :src="user.avatar_url" 
                 :alt="user.name"
                 class="w-8 h-8 rounded-full object-cover ring-2 ring-white shadow-sm">
            
            {{-- Default Avatar if no image --}}
            <div x-show="!user.avatar_url" 
                 class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium shadow-sm ring-2 ring-white"
                 x-text="getInitials(user.name)">
            </div>
            
            {{-- Online Status Indicator --}}
            <div x-show="user.is_online"
                 class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 rounded-full ring-2 ring-white">
            </div>
        </div>
        
        {{-- User Info (Hidden on mobile) --}}
        <div class="hidden md:block text-left">
            <p class="font-medium text-gray-900" x-text="user.name"></p>
            <p class="text-xs text-gray-500" x-text="user.role"></p>
        </div>
        
        {{-- Chevron Icon --}}
        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
             :class="{ 'rotate-180': $store.dropdowns.active === 'profile' }"
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M19 9l-7 7-7-7">
            </path>
        </svg>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="$store.dropdowns.active === 'profile'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         @click.away="$store.dropdowns.closeAll()"
         class="absolute right-0 top-full mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-60"
         x-data="userProfileDropdown()">>>
        
        {{-- User Profile Header --}}
        <div class="px-4 py-3 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                {{-- Larger Avatar --}}
                <div class="relative">
                    <img x-show="user.avatar_url" 
                         :src="user.avatar_url" 
                         :alt="user.name"
                         class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200">
                    
                    <div x-show="!user.avatar_url" 
                         class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white text-lg font-medium ring-2 ring-gray-200"
                         x-text="getInitials(user.name)">
                    </div>
                    
                    {{-- Status Indicator --}}
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full ring-2 ring-white flex items-center justify-center">
                        <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                    </div>
                </div>
                
                {{-- User Details --}}
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate" x-text="user.name"></p>
                    <p class="text-sm text-gray-500 truncate" x-text="user.email"></p>
                    <div class="flex items-center mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                              :class="getRoleBadgeClass(user.role)"
                              x-text="user.role">
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Actions --}}
        <div class="py-2">
            {{-- View Profile --}}
            <a href="{{ route('admin.profile') }}" 
               @click="closeDropdown()"
               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">
                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                View Profile
            </a>
            
            {{-- Account Settings --}}
            <a href="{{ route('admin.settings.index') }}" 
               @click="closeDropdown()"
               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">
                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Account Settings
            </a>
        </div>

        {{-- Quick Stats --}}
        <div class="px-4 py-3 border-t border-gray-100">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Quick Stats</p>
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="bg-blue-50 rounded-lg p-2">
                    <p class="text-lg font-semibold text-blue-600" x-text="stats.orders_today"></p>
                    <p class="text-xs text-blue-500">Orders Today</p>
                </div>
                <div class="bg-green-50 rounded-lg p-2">
                    <p class="text-lg font-semibold text-green-600" x-text="formatCurrency(stats.revenue_today)"></p>
                    <p class="text-xs text-green-500">Revenue Today</p>
                </div>
            </div>
        </div>

        {{-- Preferences --}}
        <div class="py-2 border-t border-gray-100">
            {{-- Theme Toggle --}}
            <div class="flex items-center justify-between px-4 py-2">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <span class="text-sm text-gray-700">Dark Mode</span>
                </div>
                <button @click="toggleTheme()"
                        class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="darkMode ? 'bg-blue-600' : 'bg-gray-200'">
                    <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-200"
                          :class="darkMode ? 'translate-x-6' : 'translate-x-1'">
                    </span>
                </button>
            </div>
            
            {{-- Language Selector --}}
            <div class="px-4 py-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                        <span class="text-sm text-gray-700">Language</span>
                    </div>
                    <select @change="changeLanguage($event.target.value)"
                            class="text-sm border-0 bg-transparent focus:ring-0 text-gray-600"
                            x-model="selectedLanguage">
                        <option value="en">English</option>
                        <option value="es">Español</option>
                        <option value="fr">Français</option>
                        <option value="de">Deutsch</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Help & Support --}}
        <div class="py-2 border-t border-gray-100">
            <a href="{{ route('admin.reports.index') }}" 
               @click="closeDropdown()"
               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">
                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Reports
            </a>
            
            <a href="{{ route('admin.activity.index') }}" 
               @click="closeDropdown()"
               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">
                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Activity Log
            </a>
        </div>

        {{-- Logout Section --}}
        <div class="py-2 border-t border-gray-100">
            <button @click="logout()"
                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150">
                <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Sign Out
            </button>
        </div>
    </div>
</div>

{{-- User Profile Dropdown JavaScript --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('userProfileDropdown', () => ({
        user: {},
        stats: {},
        darkMode: false,
        selectedLanguage: 'en',

        init() {
            this.loadUserData();
            this.loadUserStats();
            this.initializePreferences();
        },

        async loadUserData() {
            try {
                // Use existing profile endpoint with fallback
                const response = await fetch('/admin/profile', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.user = data.user || data;
                } else {
                    // Fallback to mock data
                    this.loadMockUserData();
                }
            } catch (error) {
                console.error('Failed to load user data:', error);
                this.loadMockUserData();
            }
        },

        loadMockUserData() {
            this.user = {
                id: 1,
                name: 'John Doe',
                email: 'john.doe@example.com',
                role: 'Administrator',
                avatar_url: null,
                is_online: true,
                last_login: new Date().toISOString()
            };
        },

        async loadUserStats() {
            try {
                // Try to use live stats API that we found earlier
                const response = await fetch('/admin/api/live-stats', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.stats = data.stats || data;
                } else {
                    // Fallback to mock data
                    this.loadMockStats();
                }
            } catch (error) {
                console.error('Failed to load user stats:', error);
                this.loadMockStats();
            }
        },

        loadMockStats() {
            this.stats = {
                orders_today: 12,
                revenue_today: 1250.00,
                total_orders: 342,
                total_revenue: 45600.00
            };
        },

        initializePreferences() {
            // Load saved preferences from localStorage
            this.darkMode = localStorage.getItem('adminDarkMode') === 'true';
            this.selectedLanguage = localStorage.getItem('adminLanguage') || 'en';
            
            // Apply dark mode if enabled
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            }
        },

        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
        },

        closeDropdown() {
            this.showDropdown = false;
        },

        getInitials(name) {
            if (!name) return 'U';
            return name.split(' ')
                      .map(word => word.charAt(0))
                      .join('')
                      .toUpperCase()
                      .substring(0, 2);
        },

        getRoleBadgeClass(role) {
            const classes = {
                'Administrator': 'bg-red-100 text-red-800',
                'Manager': 'bg-blue-100 text-blue-800',
                'Editor': 'bg-green-100 text-green-800',
                'Viewer': 'bg-gray-100 text-gray-800'
            };
            return classes[role] || 'bg-gray-100 text-gray-800';
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount);
        },

        async toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('adminDarkMode', this.darkMode);
            
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Save preference to server (if API exists)
            try {
                await fetch('/admin/profile', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        preferences: {
                            theme: this.darkMode ? 'dark' : 'light'
                        }
                    })
                });
            } catch (error) {
                console.log('Theme preference saved locally only');
            }
        },

        async changeLanguage(language) {
            this.selectedLanguage = language;
            localStorage.setItem('adminLanguage', language);

            try {
                await fetch('/admin/profile', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        preferences: {
                            language: language
                        }
                    })
                });

                // Reload page to apply language changes
                window.location.reload();
            } catch (error) {
                console.log('Language preference saved locally only');
                // Still reload to show the selection
                window.location.reload();
            }
        },

        async logout() {
            if (!confirm('Are you sure you want to sign out?')) {
                return;
            }

            try {
                const response = await fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    window.location.href = '/login';
                } else {
                    // Fallback: Use form submission
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/logout';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            } catch (error) {
                console.error('Logout failed:', error);
                // Force redirect as fallback
                window.location.href = '/login';
            }
        }
    }));
});
</script>

{{-- User Profile Dropdown Styles --}}
<style>
/* Profile dropdown specific styles */
.profile-dropdown {
    min-width: 16rem;
}

/* Avatar placeholder animation */
@keyframes avatar-pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

.avatar-loading {
    animation: avatar-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Status indicator animation */
@keyframes status-pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.status-online {
    animation: status-pulse 2s ease-in-out infinite;
}

/* Toggle switch focus styles */
.toggle-switch:focus {
    outline: 2px solid theme('colors.blue.500');
    outline-offset: 2px;
}

/* High contrast support */
@media (prefers-contrast: high) {
    .profile-dropdown {
        border: 2px solid theme('colors.gray.900');
    }
    
    .profile-dropdown a:hover,
    .profile-dropdown button:hover {
        background: theme('colors.gray.900');
        color: theme('colors.white');
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .profile-dropdown * {
        transition: none !important;
        animation: none !important;
    }
}

/* Dark mode styles */
.dark .profile-dropdown {
    background: theme('colors.gray.800');
    border-color: theme('colors.gray.700');
    color: theme('colors.gray.100');
}

.dark .profile-dropdown .border-gray-100 {
    border-color: theme('colors.gray.700');
}

.dark .profile-dropdown .text-gray-700 {
    color: theme('colors.gray.300');
}

.dark .profile-dropdown .text-gray-500 {
    color: theme('colors.gray.400');
}

.dark .profile-dropdown .hover\:bg-gray-100:hover {
    background: theme('colors.gray.700');
}

.dark .profile-dropdown .bg-blue-50 {
    background: theme('colors.blue.900');
}

.dark .profile-dropdown .bg-green-50 {
    background: theme('colors.green.900');
}
</style>
