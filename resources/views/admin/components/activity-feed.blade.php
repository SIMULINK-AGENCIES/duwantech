{{-- Activity Feed Component --}}
<div x-data="activityFeed({{ json_encode($config ?? []) }})" 
     class="bg-white rounded-xl shadow-sm border border-gray-200">
     
    {{-- Header --}}
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-indigo-100 rounded-lg">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                <p class="text-sm text-gray-500">Live activity feed</p>
            </div>
        </div>
        
        {{-- Controls --}}
        <div class="flex items-center space-x-3">
            {{-- Connection Status --}}
            <div class="flex items-center space-x-2">
                <div class="flex items-center space-x-1">
                    <div :class="[
                        'w-2 h-2 rounded-full transition-colors duration-300',
                        connected ? 'bg-green-500' : 'bg-red-500',
                        connected ? 'animate-pulse' : ''
                    ]"></div>
                    <span :class="connectionStatusClass" class="text-xs font-medium" x-text="connectionStatus"></span>
                </div>
            </div>
            
            {{-- Refresh Button --}}
            <button @click="refresh()" 
                    :disabled="loading"
                    class="p-2 rounded-md hover:bg-gray-100 transition-colors duration-200 disabled:opacity-50">
                <svg :class="loading ? 'animate-spin' : ''" 
                     class="w-4 h-4 text-gray-400" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
            </button>
            
            {{-- Filters Toggle --}}
            <button @click="config.showFilters = !config.showFilters"
                    class="p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z">
                    </path>
                </svg>
            </button>
        </div>
    </div>
    
    {{-- Filters Section --}}
    <div x-show="config.showFilters" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="p-4 bg-gray-50 border-b border-gray-200">
         
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Activity Type Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Activity Type</label>
                <select x-model="filters.action" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <template x-for="(label, value) in activityTypes" :key="value">
                        <option :value="value" x-text="label"></option>
                    </template>
                </select>
            </div>
            
            {{-- Date From --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" 
                       x-model="filters.date_from"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            
            {{-- Date To --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" 
                       x-model="filters.date_to"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            
            {{-- Clear Filters --}}
            <div class="flex items-end">
                <button @click="clearFilters()" 
                        :disabled="!hasFilters"
                        class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Clear Filters
                </button>
            </div>
        </div>
    </div>
    
    {{-- Activity Statistics --}}
    <div class="p-4 bg-blue-50 border-b border-gray-200">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600" x-text="stats.today.total">0</div>
                <div class="text-sm text-blue-700">Today</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600" x-text="stats.today.logins">0</div>
                <div class="text-sm text-green-700">Logins</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-indigo-600" x-text="stats.today.orders">0</div>
                <div class="text-sm text-indigo-700">Orders</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600" x-text="stats.today.payments">0</div>
                <div class="text-sm text-purple-700">Payments</div>
            </div>
        </div>
    </div>
    
    {{-- Loading State --}}
    <div x-show="loading && activities.length === 0" class="p-6">
        <div class="animate-pulse space-y-4">
            <template x-for="i in 5" :key="i">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-gray-200 rounded-lg"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="h-3 bg-gray-200 rounded w-16"></div>
                </div>
            </template>
        </div>
    </div>
    
    {{-- Error State --}}
    <div x-show="error && !loading" 
         class="p-6 text-center">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <svg class="w-8 h-8 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                </path>
            </svg>
            <p class="text-red-700 text-sm" x-text="error"></p>
            <button @click="refresh()" 
                    class="mt-2 text-red-600 hover:text-red-800 text-sm font-medium underline">
                Try Again
            </button>
        </div>
    </div>
    
    {{-- Activity List --}}
    <div x-show="!loading || activities.length > 0" class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
        <div x-ref="activityList">
            <template x-for="activity in activities" :key="activity.id">
                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-start space-x-4">
                        {{-- Activity Icon --}}
                        <div :class="getActivityColorClasses(activity.color)" 
                             class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <g x-html="getActivityIconSvg(activity.icon)"></g>
                            </svg>
                        </div>
                        
                        {{-- Activity Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900" x-text="activity.description"></p>
                                <span class="text-xs text-gray-500" x-text="formatTime(activity.created_at)"></span>
                            </div>
                            
                            <div class="mt-1 flex items-center space-x-2">
                                {{-- User Info --}}
                                <span class="text-sm text-gray-600" 
                                      x-text="activity.user ? activity.user.name : 'Guest'"></span>
                                
                                {{-- Priority Badge --}}
                                <span :class="{
                                    'bg-red-100 text-red-700': activity.priority === 'high',
                                    'bg-yellow-100 text-yellow-700': activity.priority === 'medium',
                                    'bg-gray-100 text-gray-700': activity.priority === 'low'
                                }" 
                                class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                x-show="activity.priority !== 'medium'"
                                x-text="activity.priority"></span>
                            </div>
                            
                            {{-- Metadata --}}
                            <div class="mt-2 text-xs text-gray-500" x-show="activity.metadata && Object.keys(activity.metadata).length > 0">
                                <span x-text="activity.ip_address"></span>
                                <span x-show="activity.metadata.location" 
                                      x-text="activity.metadata.location ? ` • ${activity.metadata.location.city}, ${activity.metadata.location.country}` : ''"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        {{-- Empty State --}}
        <div x-show="!loading && activities.length === 0" class="p-8 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Activity Yet</h3>
            <p class="text-gray-500">Activity will appear here as users interact with the system.</p>
        </div>
    </div>
    
    {{-- Footer --}}
    <div x-show="activities.length > 0" class="p-4 bg-gray-50 border-t border-gray-200">
        <div class="flex items-center justify-between text-sm text-gray-600">
            <span x-text="`Showing ${filteredActivitiesCount} activities`"></span>
            <div class="flex items-center space-x-2">
                <span x-show="hasFilters" class="text-blue-600">Filtered</span>
                <button @click="loadActivities()" 
                        class="text-indigo-600 hover:text-indigo-800 font-medium">
                    Load More
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Custom Animations --}}
<style>
@keyframes pulse-once {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes slide-in-top {
    0% {
        transform: translateY(-10px);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-pulse-once {
    animation: pulse-once 1s ease-in-out;
}

.animate-slide-in-top {
    animation: slide-in-top 0.5s ease-out;
}
</style>
