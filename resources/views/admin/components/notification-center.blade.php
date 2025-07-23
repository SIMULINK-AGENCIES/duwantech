{{-- Notification Center Component --}}
<div x-data="notificationBell()" 
     x-init="init()"
     class="relative"
     @click.away="open = false">
     
    {{-- Notification Bell Button --}}
    <button @click="toggleDropdown()" 
            class="relative p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-300 group"
            :class="{ 'bg-white/10 text-white': open }"
            title="Notifications (Alt+N)">
        
        {{-- Bell Icon --}}
        <svg x-ref="bellIcon"
             class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M15 17h5l-5 5v-5z"></path>
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M11 19H6.5A2.5 2.5 0 0 1 4 16.5v-9A2.5 2.5 0 0 1 6.5 5h8A2.5 2.5 0 0 1 17 7.5v5"></path>
        </svg>
        
        {{-- Notification Count Badge --}}
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 min-w-[20px] h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse px-1"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 scale-0"
              x-transition:enter-end="opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 scale-100"
              x-transition:leave-end="opacity-0 scale-0"></span>
    </button>
    
    {{-- Notification Dropdown --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
         class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-200 max-h-[80vh] overflow-hidden">
        
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                    <p class="text-sm text-gray-500" x-text="`You have ${unreadCount} unread notifications`"></p>
                </div>
                
                {{-- Header Actions --}}
                <div class="flex items-center space-x-2">
                    {{-- Sound Toggle --}}
                    <button @click="toggleSound()" 
                            class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                            :title="soundEnabled ? 'Disable sounds' : 'Enable sounds'">
                        <svg x-show="soundEnabled" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M9 12H5a1 1 0 01-1-1V9a1 1 0 011-1h4l5-5v16l-5-5z"></path>
                        </svg>
                        <svg x-show="!soundEnabled" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" clip-rule="evenodd"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path>
                        </svg>
                    </button>
                    
                    {{-- Mark All Read --}}
                    <button @click="markAllAsRead()" 
                            x-show="unreadCount > 0"
                            class="px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                        Mark all read
                    </button>
                    
                    {{-- Settings --}}
                    <a href="/admin/notifications" 
                       class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                       title="Notification settings">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        
        {{-- Loading State --}}
        <div x-show="loading" class="px-6 py-8 text-center">
            <div class="inline-flex items-center space-x-2 text-gray-500">
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Loading notifications...</span>
            </div>
        </div>
        
        {{-- Notifications List --}}
        <div x-show="!loading" class="max-h-96 overflow-y-auto">
            {{-- Empty State --}}
            <div x-show="notifications.length === 0" class="px-6 py-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19H6.5A2.5 2.5 0 0 1 4 16.5v-9A2.5 2.5 0 0 1 6.5 5h8A2.5 2.5 0 0 1 17 7.5v5"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                <p class="text-gray-500">You're all caught up! Check back later for new notifications.</p>
            </div>
            
            {{-- Notification Items --}}
            <template x-for="notification in notifications" :key="notification.id">
                <div class="relative border-b border-gray-100 last:border-b-0">
                    {{-- Unread Indicator --}}
                    <div x-show="!notification.is_read" 
                         class="absolute left-2 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-blue-500 rounded-full"></div>
                    
                    {{-- Notification Content --}}
                    <div @click="handleNotificationClick(notification)"
                         class="px-6 py-4 hover:bg-gray-50 cursor-pointer transition-colors duration-200"
                         :class="{ 'bg-blue-50': !notification.is_read, 'pl-8': !notification.is_read }">
                        
                        <div class="flex items-start space-x-3">
                            {{-- Icon --}}
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                                 :class="notification.color">
                                <span x-text="getNotificationIcon(notification.type)" class="text-lg"></span>
                            </div>
                            
                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                        <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                                        
                                        {{-- Priority Badge --}}
                                        <div class="flex items-center mt-2 space-x-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                  :class="getPriorityColor(notification.priority)"
                                                  x-text="notification.priority.charAt(0).toUpperCase() + notification.priority.slice(1)"></span>
                                            <span class="text-xs text-gray-500" x-text="notification.created_at"></span>
                                        </div>
                                    </div>
                                    
                                    {{-- Actions --}}
                                    <div class="flex items-center space-x-1 ml-2">
                                        {{-- Mark as Read/Unread --}}
                                        <button @click.stop="markAsRead(notification.id)"
                                                x-show="!notification.is_read"
                                                class="p-1 text-gray-400 hover:text-blue-600 rounded transition-colors duration-200"
                                                title="Mark as read">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        
                                        {{-- Delete --}}
                                        <button @click.stop="deleteNotification(notification.id)"
                                                class="p-1 text-gray-400 hover:text-red-600 rounded transition-colors duration-200"
                                                title="Delete notification">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        {{-- Footer --}}
        <div x-show="notifications.length > 0" class="px-6 py-3 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <button @click="requestNotificationPermission()"
                        x-show="'Notification' in window && Notification.permission === 'default'"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Enable browser notifications
                </button>
                
                <a href="/admin/notifications" 
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    View all notifications
                </a>
            </div>
        </div>
    </div>
</div>
