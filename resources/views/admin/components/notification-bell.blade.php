<!-- Notification Bell Component -->
<div class="relative" x-data="{ open: false }">
    <!-- Bell Icon with Badge -->
    <button id="notification-bell" 
            @click="open = !open"
            class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg"
            aria-label="Notifications"
            aria-expanded="false">
        
        <!-- Bell SVG Icon -->
        <svg class="w-6 h-6 transition-transform duration-200 hover:scale-110" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Notification Badge -->
        <span id="notification-badge" 
              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-blue-500 rounded-full min-w-[1.25rem] h-5 hidden">
            0
        </span>
    </button>

    <!-- Notification Dropdown -->
    <div id="notification-dropdown"
         @click.away="open = false"
         x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 hidden"
         style="max-height: 600px;">
        
        <!-- Dropdown Header -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                    <span id="notification-count" 
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        0
                    </span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <!-- Mark All as Read Button -->
                    <button id="mark-all-read"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200"
                            title="Mark all as read">
                        Mark all read
                    </button>
                    
                    <!-- View All Link -->
                    <a href="{{ route('admin.notifications.index') }}"
                       class="text-xs text-gray-500 hover:text-gray-700 font-medium transition-colors duration-200"
                       title="View all notifications">
                        View all
                    </a>
                </div>
            </div>
        </div>

        <!-- Notification List -->
        <div id="notification-list" 
             class="overflow-y-auto"
             style="max-height: 400px;">
            <!-- Notifications will be populated by JavaScript -->
            <div class="p-6 text-center text-gray-500">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-3"></div>
                <p class="text-sm">Loading notifications...</p>
            </div>
        </div>

        <!-- Dropdown Footer -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.notifications.index') }}"
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                    View all notifications →
                </a>
                
                <div class="flex items-center space-x-2 text-xs text-gray-500">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span>Live updates</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Styles -->
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .notification-item:hover .delete-notification {
        opacity: 1;
    }
    
    .notification-item.group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }
    
    @keyframes bell-ring {
        0%, 50%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(15deg); }
        75% { transform: rotate(-15deg); }
    }
    
    .animate-bell-ring {
        animation: bell-ring 0.6s ease-in-out;
    }
</style>

<!-- Load Notification Bell JavaScript -->
@push('scripts')
<script>
    // Ensure the notification bell is initialized
    document.addEventListener('DOMContentLoaded', function() {
        // The NotificationBell class will be loaded from the compiled JS bundle
        if (typeof window.notificationBell === 'undefined') {
            // Fallback initialization if module not loaded
            console.warn('NotificationBell module not loaded, initializing fallback');
            
            // Basic functionality without real-time updates
            const bell = document.getElementById('notification-bell');
            const dropdown = document.getElementById('notification-dropdown');
            
            if (bell && dropdown) {
                bell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });
                
                document.addEventListener('click', function(e) {
                    if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
        }
    });
</script>
@endpush
