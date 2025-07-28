<!-- Activity Feed Widget Component -->
<div x-data="activityFeed()" 
     x-init="init()"
     class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Live Activity Feed</h3>
            <p class="text-sm text-gray-500">Real-time system activities</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Filter Dropdown -->
            <select x-model="selectedFilter" 
                    @change="applyFilter()"
                    class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="all">All Activities</option>
                <option value="orders">Orders</option>
                <option value="users">Users</option>
                <option value="products">Products</option>
                <option value="payments">Payments</option>
            </select>
            
            <!-- Auto-refresh Toggle -->
            <button @click="toggleAutoRefresh()" 
                    :class="autoRefresh ? 'text-green-600' : 'text-gray-400'"
                    class="p-2 hover:bg-gray-100 rounded-md transition-colors duration-200">
                <i class="fas fa-sync-alt text-sm" :class="{ 'animate-spin': loading }"></i>
            </button>
            
            <!-- Live Indicator -->
            <div class="flex items-center space-x-2" x-show="autoRefresh">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-xs text-gray-500">Live</span>
            </div>
        </div>
    </div>

    <!-- Activity List -->
    <div class="space-y-3 max-h-96 overflow-y-auto">
        <template x-for="activity in filteredActivities" :key="activity.id || Math.random()">
            <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <!-- Activity Icon -->
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center"
                         :class="getIconBgClass(activity.type)">
                        <i :class="getIconClass(activity.type) + ' text-sm'"></i>
                    </div>
                </div>
                
                <!-- Activity Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900" x-text="activity.title"></p>
                        <span class="text-xs text-gray-500" x-text="formatTime(activity.timestamp)"></span>
                    </div>
                    
                    <p class="text-sm text-gray-600 mt-1" x-text="activity.description"></p>
                    
                    <!-- Activity Meta -->
                    <div class="flex items-center mt-2 space-x-4" x-show="activity.amount || activity.user">
                        <span x-show="activity.amount" 
                              class="text-xs font-medium text-green-600" 
                              x-text="'KES ' + Number(activity.amount).toLocaleString()"></span>
                        <span x-show="activity.user" 
                              class="text-xs text-gray-500" 
                              x-text="activity.user"></span>
                    </div>
                </div>
                
                <!-- Action Button -->
                <div class="flex-shrink-0" x-show="activity.action_url">
                    <button @click="viewActivity(activity)" 
                            class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                        View
                    </button>
                </div>
            </div>
        </template>
        
        <!-- Empty State -->
        <div x-show="filteredActivities.length === 0" 
             class="text-center py-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-history text-gray-400 text-xl"></i>
            </div>
            <p class="text-sm text-gray-500">No activities found</p>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" 
         class="flex items-center justify-center py-8">
        <div class="flex items-center space-x-2 text-gray-500">
            <div class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm">Loading activities...</span>
        </div>
    </div>

    <!-- View More Button -->
    <div class="mt-4 pt-4 border-t border-gray-200" x-show="!loading && hasMore">
        <button @click="loadMore()" 
                class="w-full py-2 text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
            Load More Activities
        </button>
    </div>
</div>

<script>
function activityFeed() {
    return {
        // Configuration
        refreshInterval: 30000, // 30 seconds
        maxActivities: 50,
        
        // Data
        activities: [],
        selectedFilter: 'all',
        autoRefresh: true,
        hasMore: true,
        page: 1,
        
        // State
        loading: false,
        refreshTimer: null,
        
        init() {
            this.loadActivities();
            this.startAutoRefresh();
        },
        
        destroy() {
            if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
            }
        },
        
        startAutoRefresh() {
            if (!this.autoRefresh) return;
            
            this.refreshTimer = setInterval(() => {
                this.loadActivities(true);
            }, this.refreshInterval);
        },
        
        stopAutoRefresh() {
            if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
                this.refreshTimer = null;
            }
        },
        
        toggleAutoRefresh() {
            this.autoRefresh = !this.autoRefresh;
            
            if (this.autoRefresh) {
                this.startAutoRefresh();
            } else {
                this.stopAutoRefresh();
            }
        },
        
        async loadActivities(refresh = false) {
            this.loading = true;
            
            try {
                const response = await fetch('/api/dashboard/activity-feed');
                const result = await response.json();
                
                if (result.success) {
                    if (refresh) {
                        this.activities = result.data;
                    } else {
                        this.activities = [...this.activities, ...result.data];
                    }
                    
                    this.hasMore = result.data.length >= 10;
                }
            } catch (error) {
                console.error('Failed to load activities:', error);
            } finally {
                this.loading = false;
            }
        },
        
        loadMore() {
            this.page++;
            this.loadActivities();
        },
        
        applyFilter() {
            // Filter is applied via computed property
        },
        
        viewActivity(activity) {
            if (activity.action_url) {
                window.location.href = activity.action_url;
            }
        },
        
        getIconClass(type) {
            const icons = {
                'order': 'fas fa-shopping-cart',
                'user': 'fas fa-user-plus',
                'product': 'fas fa-box',
                'payment': 'fas fa-credit-card',
                'inventory': 'fas fa-warehouse',
                'system': 'fas fa-cog',
                'notification': 'fas fa-bell',
                'default': 'fas fa-circle'
            };
            
            return icons[type] || icons.default;
        },
        
        getIconBgClass(type) {
            const classes = {
                'order': 'bg-blue-100 text-blue-600',
                'user': 'bg-green-100 text-green-600',
                'product': 'bg-purple-100 text-purple-600',
                'payment': 'bg-yellow-100 text-yellow-600',
                'inventory': 'bg-red-100 text-red-600',
                'system': 'bg-gray-100 text-gray-600',
                'notification': 'bg-indigo-100 text-indigo-600',
                'default': 'bg-gray-100 text-gray-600'
            };
            
            return classes[type] || classes.default;
        },
        
        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            const seconds = Math.floor(diff / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);
            
            if (seconds < 60) {
                return 'Just now';
            } else if (minutes < 60) {
                return `${minutes}m ago`;
            } else if (hours < 24) {
                return `${hours}h ago`;
            } else if (days < 7) {
                return `${days}d ago`;
            } else {
                return date.toLocaleDateString();
            }
        },
        
        get filteredActivities() {
            if (this.selectedFilter === 'all') {
                return this.activities;
            }
            
            return this.activities.filter(activity => activity.type === this.selectedFilter);
        }
    }
}
</script>
