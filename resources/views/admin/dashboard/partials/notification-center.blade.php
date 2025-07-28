{{-- Real-time Notification Center Component --}}
<div class="relative">
    {{-- Notification Bell Button --}}
    <button @click="$store.dropdowns.toggle('notifications')"
            class="relative flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'text-blue-600 bg-blue-50': $store.dropdowns.active === 'notifications' }"
            aria-label="Notifications"
            :aria-expanded="$store.dropdowns.active === 'notifications'"
            x-data="notificationCenter()">
        
        {{-- Bell Icon --}}
        <svg class="w-5 h-5" 
             :class="{ 'animate-pulse': hasNewNotifications }"
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M15 17h5l-5 5v-5zM15 17H9a6 6 0 01-6-6V9a6 6 0 016-6h6a6 6 0 016 6v8z"></path>
        </svg>
        
        {{-- Notification Badge --}}
        <span x-show="unreadCount > 0"
              x-transition:enter="transition-all duration-200 ease-out"
              x-transition:enter-start="scale-0"
              x-transition:enter-end="scale-100"
              x-transition:leave="transition-all duration-150 ease-in"
              x-transition:leave-start="scale-100"
              x-transition:leave-end="scale-0"
              class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full"
              x-text="unreadCount > 99 ? '99+' : unreadCount">
        </span>
    </button>

    {{-- Notification Dropdown Panel --}}
    <div x-show="$store.dropdowns.active === 'notifications'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         @click.away="$store.dropdowns.closeAll()"
         class="absolute right-0 top-full mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-55 max-h-96 overflow-hidden"
         x-data="notificationCenter()">
        
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                <div class="flex items-center space-x-2">
                    {{-- Mark All Read --}}
                    <button x-show="unreadCount > 0"
                            @click="markAllAsRead()"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                        Mark all read
                    </button>
                    
                    {{-- Settings --}}
                    <button @click="openSettings()"
                            class="flex items-center justify-center w-6 h-6 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                            aria-label="Notification settings">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            {{-- Notification Categories Filter --}}
            <div class="mt-2 flex space-x-1">
                <button @click="activeFilter = 'all'"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors duration-200"
                        :class="activeFilter === 'all' ? 'bg-blue-100 text-blue-800' : 'text-gray-500 hover:text-gray-700'">
                    All
                </button>
                <button @click="activeFilter = 'orders'"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors duration-200"
                        :class="activeFilter === 'orders' ? 'bg-blue-100 text-blue-800' : 'text-gray-500 hover:text-gray-700'">
                    Orders
                </button>
                <button @click="activeFilter = 'system'"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors duration-200"
                        :class="activeFilter === 'system' ? 'bg-blue-100 text-blue-800' : 'text-gray-500 hover:text-gray-700'">
                    System
                </button>
                <button @click="activeFilter = 'updates'"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors duration-200"
                        :class="activeFilter === 'updates' ? 'bg-blue-100 text-blue-800' : 'text-gray-500 hover:text-gray-700'">
                    Updates
                </button>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="max-h-80 overflow-y-auto">
            {{-- Loading State --}}
            <div x-show="isLoading" class="px-4 py-8 text-center">
                <svg class="animate-spin mx-auto h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500">Loading notifications...</p>
            </div>

            {{-- Notification Items --}}
            <div x-show="!isLoading">
                <template x-for="notification in filteredNotifications" :key="notification.id">
                    <div class="relative group hover:bg-gray-50 transition-colors duration-150"
                         :class="{ 'bg-blue-50': !notification.read_at }">
                        
                        {{-- Unread Indicator --}}
                        <div x-show="!notification.read_at" 
                             class="absolute left-2 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-blue-500 rounded-full">
                        </div>
                        
                        {{-- Notification Content --}}
                        <div class="px-4 py-3 pl-8">
                            <div class="flex items-start space-x-3">
                                {{-- Notification Icon --}}
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                         :class="getNotificationIconBg(notification.type)">
                                        <span x-html="getNotificationIcon(notification.type)" 
                                              class="w-4 h-4"
                                              :class="getNotificationIconColor(notification.type)">
                                        </span>
                                    </div>
                                </div>
                                
                                {{-- Notification Details --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900" 
                                       x-text="notification.title">
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1" 
                                       x-text="notification.message">
                                    </p>
                                    <div class="flex items-center justify-between mt-2">
                                        <p class="text-xs text-gray-500" 
                                           x-text="formatTime(notification.created_at)">
                                        </p>
                                        <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            {{-- Mark as Read/Unread --}}
                                            <button @click="toggleReadStatus(notification)"
                                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium"
                                                    x-text="notification.read_at ? 'Mark unread' : 'Mark read'">
                                            </button>
                                            
                                            {{-- Delete --}}
                                            <button @click="deleteNotification(notification)"
                                                    class="text-xs text-red-600 hover:text-red-800 font-medium">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Action Button --}}
                                <div x-show="notification.action_url" class="flex-shrink-0">
                                    <a :href="notification.action_url"
                                       @click="markAsRead(notification)"
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty State --}}
                <div x-show="filteredNotifications.length === 0 && !isLoading" 
                     class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 17H9a6 6 0 01-6-6V9a6 6 0 016-6h6a6 6 0 016 6v8z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                    <p class="mt-1 text-sm text-gray-500" 
                       x-text="activeFilter === 'all' ? 'You\'re all caught up!' : `No ${activeFilter} notifications`">
                    </p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.notifications.index') }}" 
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                    View all notifications
                </a>
                <button @click="clearAllNotifications()"
                        x-show="notifications.length > 0"
                        class="text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    Clear all
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Notification Center JavaScript --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationCenter', () => ({
        notifications: [],
        unreadCount: 0,
        isLoading: false,
        activeFilter: 'all',
        hasNewNotifications: false,
        pollInterval: null,

        init() {
            this.loadNotifications();
            this.startRealTimePolling();
            
            // Listen for real-time updates (if using broadcasting)
            this.setupRealtimeListeners();
        },

        get filteredNotifications() {
            if (this.activeFilter === 'all') {
                return this.notifications;
            }
            return this.notifications.filter(notification => 
                notification.type === this.activeFilter
            );
        },

        async loadNotifications() {
            this.isLoading = true;
            
            try {
                const response = await fetch('/admin/api/notifications', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.notifications = data.notifications || [];
                    this.unreadCount = data.unread_count || 0;
                } else {
                    // Fallback to mock data
                    this.loadMockNotifications();
                }
            } catch (error) {
                console.error('Failed to load notifications:', error);
                this.loadMockNotifications();
            }

            this.isLoading = false;
        },

        loadMockNotifications() {
            this.notifications = [
                {
                    id: 1,
                    type: 'orders',
                    title: 'New Order Received',
                    message: 'Order #1234 from John Doe has been placed',
                    read_at: null,
                    created_at: new Date().toISOString(),
                    action_url: '/admin/orders/1234'
                },
                {
                    id: 2,
                    type: 'system',
                    title: 'System Update',
                    message: 'Your dashboard has been updated with new features',
                    read_at: new Date().toISOString(),
                    created_at: new Date(Date.now() - 3600000).toISOString(),
                    action_url: null
                },
                {
                    id: 3,
                    type: 'updates',
                    title: 'Weekly Report Available',
                    message: 'Your weekly sales report is ready for review',
                    read_at: null,
                    created_at: new Date(Date.now() - 7200000).toISOString(),
                    action_url: '/admin/reports'
                }
            ];
            this.unreadCount = this.notifications.filter(n => !n.read_at).length;
        },

        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
            
            if (this.showNotifications) {
                this.loadNotifications();
            }
        },

        closeNotifications() {
            this.showNotifications = false;
        },

        async markAsRead(notification) {
            if (notification.read_at) return;

            try {
                const response = await fetch(`/admin/api/notifications/${notification.id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
                // Optimistically update UI anyway
                notification.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
        },

        async toggleReadStatus(notification) {
            const wasRead = !!notification.read_at;
            const endpoint = wasRead ? 'unread' : 'read';

            try {
                const response = await fetch(`/admin/api/notifications/${notification.id}/${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    notification.read_at = wasRead ? null : new Date().toISOString();
                    this.unreadCount += wasRead ? 1 : -1;
                    this.unreadCount = Math.max(0, this.unreadCount);
                }
            } catch (error) {
                console.error('Failed to toggle notification status:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/admin/api/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    this.notifications.forEach(notification => {
                        notification.read_at = new Date().toISOString();
                    });
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        async deleteNotification(notification) {
            try {
                const response = await fetch(`/admin/api/notifications/${notification.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    const index = this.notifications.findIndex(n => n.id === notification.id);
                    if (index > -1) {
                        if (!this.notifications[index].read_at) {
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                        }
                        this.notifications.splice(index, 1);
                    }
                }
            } catch (error) {
                console.error('Failed to delete notification:', error);
            }
        },

        async clearAllNotifications() {
            if (!confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch('/admin/api/notifications/clear-old', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    this.notifications = [];
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Failed to clear notifications:', error);
            }
        },

        startRealTimePolling() {
            // Poll for new notifications every 30 seconds
            this.pollInterval = setInterval(() => {
                this.checkForNewNotifications();
            }, 30000);
        },

        async checkForNewNotifications() {
            try {
                const response = await fetch('/admin/api/notifications/count', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const newUnreadCount = data.unread_count || 0;
                    
                    if (newUnreadCount > this.unreadCount) {
                        this.hasNewNotifications = true;
                        setTimeout(() => {
                            this.hasNewNotifications = false;
                        }, 3000);
                        
                        // Reload notifications if panel is open
                        if (this.showNotifications) {
                            this.loadNotifications();
                        }
                    }
                    
                    this.unreadCount = newUnreadCount;
                }
            } catch (error) {
                console.error('Failed to check for new notifications:', error);
            }
        },

        setupRealtimeListeners() {
            // If using Laravel Echo/Pusher for real-time updates
            if (typeof Echo !== 'undefined') {
                Echo.private(`Admin.User.${window.Laravel.user.id}`)
                    .notification((notification) => {
                        this.notifications.unshift(notification);
                        this.unreadCount++;
                        this.hasNewNotifications = true;
                        
                        setTimeout(() => {
                            this.hasNewNotifications = false;
                        }, 3000);
                    });
            }
        },

        getNotificationIcon(type) {
            const icons = {
                orders: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11h8"></path>',
                system: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>',
                updates: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>'
            };
            return icons[type] || icons.system;
        },

        getNotificationIconBg(type) {
            const backgrounds = {
                orders: 'bg-blue-100',
                system: 'bg-gray-100',
                updates: 'bg-green-100'
            };
            return backgrounds[type] || backgrounds.system;
        },

        getNotificationIconColor(type) {
            const colors = {
                orders: 'text-blue-600',
                system: 'text-gray-600',
                updates: 'text-green-600'
            };
            return colors[type] || colors.system;
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (minutes < 1) return 'Just now';
            if (minutes < 60) return `${minutes}m ago`;
            if (hours < 24) return `${hours}h ago`;
            if (days < 7) return `${days}d ago`;
            return date.toLocaleDateString();
        },

        openSettings() {
            // Navigate to notification settings or open modal
            window.location.href = '/admin/settings#notifications';
        }
    }));
});
</script>

{{-- Notification Center Styles --}}
<style>
/* Notification center specific styles */
.notification-center {
    scrollbar-width: thin;
    scrollbar-color: theme('colors.gray.300') transparent;
}

.notification-center::-webkit-scrollbar {
    width: 6px;
}

.notification-center::-webkit-scrollbar-track {
    background: transparent;
}

.notification-center::-webkit-scrollbar-thumb {
    background-color: theme('colors.gray.300');
    border-radius: 3px;
}

.notification-center::-webkit-scrollbar-thumb:hover {
    background-color: theme('colors.gray.400');
}

/* Notification animations */
@keyframes notification-pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-notification-pulse {
    animation: notification-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* High contrast support */
@media (prefers-contrast: high) {
    .notification-item {
        border: 1px solid;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .notification-item,
    .notification-badge {
        transition: none;
    }
    
    .animate-pulse,
    .animate-notification-pulse {
        animation: none;
    }
}
</style>
