{{-- Premium Notification Center Component --}}
<div x-data="notificationBell()" 
     x-init="init()"
     class="relative"
     @click.away="open = false">
     
    {{-- Professional Notification Bell Button --}}
    <button @click="toggleDropdown()" 
            class="group relative p-3 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-xl transition-all duration-300 border border-transparent hover:border-slate-700/50 backdrop-blur-sm"
            :class="{ 'bg-slate-800/50 text-white border-slate-700/50': open }"
            title="Notifications (Alt+N)">
        
        {{-- Premium Bell Icon --}}
        <div class="relative">
        {{-- Premium Bell Icon --}}
        <div class="relative">
        {{-- Premium Bell Icon --}}
        <div class="relative">
            <svg x-ref="bellIcon"
                 class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="M15 17h5l-3.5 3.5-.5-3.5z"></path>
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="M15 17H9a2 2 0 01-2-2V9a6 6 0 0112 0v8z"></path>
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="M13.73 21a2 2 0 01-3.46 0"></path>
            </svg>
            
            {{-- Enhanced Notification Count Badge --}}
            <span x-show="unreadCount > 0" 
                  x-text="unreadCount > 99 ? '99+' : unreadCount"
                  class="absolute -top-1.5 -right-1.5 min-w-[22px] h-5.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-full flex items-center justify-center px-1.5 shadow-lg ring-2 ring-slate-900 animate-pulse"
                  x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0 scale-0"
                  x-transition:enter-end="opacity-100 scale-100"
                  x-transition:leave="transition ease-in duration-200"
                  x-transition:leave-start="opacity-100 scale-100"
                  x-transition:leave-end="opacity-0 scale-0"></span>
                  
            {{-- Notification Pulse Ring --}}
            <div x-show="unreadCount > 0" 
                 class="absolute inset-0 w-6 h-6 bg-red-500/20 rounded-xl animate-ping"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-0"
                 x-transition:enter-end="opacity-100 scale-100"></div>
        </div>
    </button>
    
    {{-- Premium Notification Dropdown --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="transform opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="transform opacity-0 scale-95 translate-y-4"
         class="absolute right-0 mt-4 w-96 bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-700/50 py-6 z-[10000] max-h-[85vh] overflow-hidden">
        
        {{-- Gradient Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-purple-500/5 rounded-2xl"></div>
        
        {{-- Professional Header --}}
        <div class="relative px-6 pb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-bold text-slate-100 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.5 3.5-.5-3.5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17H9a2 2 0 01-2-2V9a6 6 0 0112 0v8z"></path>
                        </svg>
                        <span>Notifications</span>
                    </h3>
                    <p class="text-slate-400 text-sm" x-text="`${unreadCount} unread notifications`"></p>
                </div>
                
                {{-- Header Actions --}}
                <div class="flex items-center space-x-2">
                    {{-- Sound Toggle --}}
                    <button @click="toggleSound()" 
                            class="p-2 text-slate-500 hover:text-slate-300 rounded-lg hover:bg-slate-800/50 transition-all duration-200 border border-transparent hover:border-slate-700/50"
                            :title="soundEnabled ? 'Disable sounds' : 'Enable sounds'">
                        <svg x-show="soundEnabled" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M9 12H5a1 1 0 01-1-1V9a1 1 0 011-1h4l5-5v16l-5-5z"></path>
                        </svg>
                        <svg x-show="!soundEnabled" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path>
                        </svg>
                    </button>
                    
                    {{-- Mark All Read --}}
                    <button @click="markAllAsRead()" 
                            x-show="unreadCount > 0"
                            class="px-3 py-1.5 text-xs font-semibold text-blue-400 hover:text-blue-300 bg-blue-500/10 hover:bg-blue-500/20 rounded-lg transition-all duration-200 border border-blue-500/20 hover:border-blue-500/30">
                        Mark all read
                    </button>
                    
                    {{-- Settings --}}
                    <a href="/admin/notifications" 
                       class="p-2 text-slate-500 hover:text-slate-300 rounded-lg hover:bg-slate-800/50 transition-all duration-200 border border-transparent hover:border-slate-700/50"
                       title="Notification settings">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </a>
                </div>
            </div>
            
            {{-- Quick Filter Tabs --}}
            <div class="flex space-x-1 bg-slate-800/50 p-1 rounded-lg border border-slate-700/50">
                <button class="px-3 py-1.5 text-xs font-semibold text-blue-400 bg-blue-500/20 border border-blue-500/30 rounded-md">All</button>
                <button class="px-3 py-1.5 text-xs font-medium text-slate-400 hover:text-slate-300 hover:bg-slate-700/50 rounded-md transition-colors duration-200">Orders</button>
                <button class="px-3 py-1.5 text-xs font-medium text-slate-400 hover:text-slate-300 hover:bg-slate-700/50 rounded-md transition-colors duration-200">System</button>
                <button class="px-3 py-1.5 text-xs font-medium text-slate-400 hover:text-slate-300 hover:bg-slate-700/50 rounded-md transition-colors duration-200">Critical</button>
            </div>
        </div>
        
        {{-- Enhanced Loading State --}}
        <div x-show="loading" class="relative px-6 py-12 text-center">
            <div class="inline-flex flex-col items-center space-y-4">
                <div class="relative">
                    <div class="w-8 h-8 border-4 border-slate-700 border-t-blue-500 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 w-8 h-8 border-4 border-transparent border-r-purple-500 rounded-full animate-spin animation-delay-150"></div>
                </div>
                <p class="text-slate-400 text-sm font-medium">Loading notifications...</p>
            </div>
        </div>
        
        {{-- Premium Notifications List --}}
        <div x-show="!loading" class="relative">
            {{-- Notifications Container --}}
            <div class="max-h-96 overflow-y-auto px-6 space-y-3 scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-transparent">
                
                {{-- Sample Notification Items --}}
                <template x-for="notification in notifications.slice(0, 10)" :key="notification.id">
                    <div class="group relative p-4 bg-slate-800/30 hover:bg-slate-800/50 border border-slate-700/30 hover:border-slate-600/50 rounded-xl transition-all duration-300 cursor-pointer"
                         @click="markAsRead(notification.id); window.location.href = '/admin/notifications'">
                        
                        {{-- Priority Indicator --}}
                        <div class="absolute left-0 top-4 w-1 h-12 rounded-r-full"
                             :class="{
                                 'bg-red-500': notification.priority === 'critical',
                                 'bg-orange-500': notification.priority === 'high', 
                                 'bg-yellow-500': notification.priority === 'medium',
                                 'bg-green-500': notification.priority === 'low'
                             }"></div>
                        
                        <div class="flex items-start space-x-4 ml-4">
                            {{-- Notification Icon --}}
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                     :class="{
                                         'bg-red-500/20 border border-red-500/30': notification.type === 'order',
                                         'bg-green-500/20 border border-green-500/30': notification.type === 'payment',
                                         'bg-yellow-500/20 border border-yellow-500/30': notification.type === 'inventory',
                                         'bg-blue-500/20 border border-blue-500/30': notification.type === 'user',
                                         'bg-purple-500/20 border border-purple-500/30': notification.type === 'system'
                                     }">
                                    {{-- Dynamic Icons Based on Type --}}
                                    <template x-if="notification.type === 'order'">
                                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </template>
                                    <template x-if="notification.type === 'payment'">
                                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </template>
                                    <template x-if="notification.type === 'inventory'">
                                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 8l8-4"></path>
                                        </svg>
                                    </template>
                                    <template x-if="notification.type === 'user'">
                                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </template>
                                    <template x-if="notification.type === 'system'">
                                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </template>
                                </div>
                            </div>
                            
                            {{-- Notification Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-slate-200 font-semibold text-sm leading-tight group-hover:text-white transition-colors duration-200" 
                                            x-text="notification.title"></h4>
                                        <p class="text-slate-400 text-xs mt-1 line-clamp-2" 
                                           x-text="notification.message"></p>
                                    </div>
                                    
                                    {{-- Action Menu --}}
                                    <div class="flex items-center space-x-2 ml-3">
                                        {{-- Unread Indicator --}}
                                        <div x-show="!notification.read_at" 
                                             class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></div>
                                        
                                        {{-- Quick Action --}}
                                        <button @click.stop="markAsRead(notification.id)" 
                                                class="opacity-0 group-hover:opacity-100 p-1.5 text-slate-500 hover:text-slate-300 hover:bg-slate-700/50 rounded-lg transition-all duration-200"
                                                title="Mark as read">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- Timestamp & Type --}}
                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-slate-500 text-xs" x-text="notification.time_ago"></span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full border"
                                          :class="{
                                              'text-red-400 bg-red-500/10 border-red-500/20': notification.priority === 'critical',
                                              'text-orange-400 bg-orange-500/10 border-orange-500/20': notification.priority === 'high',
                                              'text-yellow-400 bg-yellow-500/10 border-yellow-500/20': notification.priority === 'medium',
                                              'text-green-400 bg-green-500/10 border-green-500/20': notification.priority === 'low'
                                          }"
                                          x-text="notification.priority.toUpperCase()"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                {{-- Empty State --}}
                <div x-show="notifications.length === 0" class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-700/50">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.5 3.5-.5-3.5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17H9a2 2 0 01-2-2V9a6 6 0 0112 0v8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-slate-300 font-semibold mb-2">No notifications</h3>
                    <p class="text-slate-500 text-sm">You're all caught up! New notifications will appear here.</p>
                </div>
            </div>
            
            {{-- Footer Action --}}
            <div class="relative px-6 pt-4 border-t border-slate-700/50 mt-4">
                <a href="/admin/notifications" 
                   class="group flex items-center justify-center w-full p-3 text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 rounded-xl transition-all duration-200 border border-transparent hover:border-slate-700/50">
                    <span class="text-sm font-medium">View All Notifications</span>
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
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
