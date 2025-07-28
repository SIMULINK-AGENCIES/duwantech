<x-admin.layouts.master title="Performance Monitoring">
    <x-slot name="breadcrumbs">
        @php
            $breadcrumbs = [
                ['title' => 'Performance Monitoring', 'url' => route('admin.performance.index')]
            ];
        @endphp
    </x-slot>

    @push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <style>
    .metric-card {
        transition: all 0.3s ease;
    }
    
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }
    
    .status-healthy { background-color: #10b981; }
    .status-warning { background-color: #f59e0b; }
    .status-critical { background-color: #ef4444; }
    
    .performance-chart {
        height: 300px;
    }
    
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
    </style>
    @endpush

    <div x-data="performanceMonitoring()" x-init="init()" class="space-y-6">
        <!-- Performance Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Performance Monitoring</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Real-time system performance and optimization
                </p>
            </div>
            
            <!-- Health Status -->
            <div x-show="healthSummary" class="mt-4 sm:mt-0 flex items-center space-x-4">
                <div class="text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">System Health</div>
                    <div class="flex items-center">
                        <span 
                            :class="{
                                'status-healthy': healthSummary?.status === 'healthy',
                                'status-warning': healthSummary?.status === 'warning', 
                                'status-critical': healthSummary?.status === 'critical'
                            }" 
                            class="status-indicator"
                        ></span>
                        <span class="font-semibold capitalize" x-text="healthSummary?.status || 'Checking...'"></span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Score</div>
                    <div class="text-2xl font-bold" 
                         :class="{
                             'text-green-600': healthSummary?.score >= 80,
                             'text-yellow-600': healthSummary?.score >= 60 && healthSummary?.score < 80,
                             'text-red-600': healthSummary?.score < 60
                         }"
                         x-text="healthSummary?.score + '%' || '...'">
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" x-model="autoRefresh" class="sr-only">
                    <div class="relative">
                        <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"
                              :class="autoRefresh ? 'bg-blue-600' : 'bg-gray-200'"></div>
                    </div>
                    <span class="ml-2 text-sm text-gray-700">Auto Refresh</span>
                </label>
                
                <button @click="refreshData()" 
                        :disabled="loading"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors">
                    <span x-show="!loading">Refresh</span>
                    <span x-show="loading" class="flex items-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                        Loading...
                    </span>
                </button>
            </div>
        </div>
        
        <!-- Alerts Section -->
        <div x-show="healthSummary?.critical_alerts?.length > 0" class="mt-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <svg class="h-5 w-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 class="text-sm font-medium text-red-800">Critical Alerts</h3>
                </div>
                <ul class="text-sm text-red-700 space-y-1">
                    <template x-for="alert in healthSummary.critical_alerts" :key="alert.message">
                        <li x-text="alert.message"></li>
                    </template>
                </ul>
            </div>
        </div>
    </div>

    <!-- System Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 stagger-animation">
        <!-- Database Status -->
        <div class="metric-card bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Database</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1" x-text="metrics.database?.connections?.active || 0"></p>
                    <p class="text-xs text-gray-500 mt-1">Active Connections</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-database text-blue-600 text-lg"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center">
                <span class="status-indicator" :class="getDatabaseStatus()"></span>
                <span class="text-sm text-gray-600" x-text="getDatabaseStatusText()"></span>
            </div>
        </div>

        <!-- Cache Status -->
        <div class="metric-card bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Cache</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1" x-text="metrics.cache?.hit_rate || 0"></p>
                    <p class="text-xs text-gray-500 mt-1">Hit Rate (%)</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-memory text-green-600 text-lg"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center">
                <span class="status-indicator" :class="getCacheStatus()"></span>
                <span class="text-sm text-gray-600" x-text="getCacheStatusText()"></span>
            </div>
        </div>

        <!-- Queue Status -->
        <div class="metric-card bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Queue</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1" x-text="getTotalQueueJobs()"></p>
                    <p class="text-xs text-gray-500 mt-1">Jobs in Queue</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-tasks text-yellow-600 text-lg"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center">
                <span class="status-indicator" :class="getQueueStatus()"></span>
                <span class="text-sm text-gray-600" x-text="getQueueStatusText()"></span>
            </div>
        </div>

        <!-- Memory Usage -->
        <div class="metric-card bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-600">Memory</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1" x-text="formatBytes(metrics.memory?.usage || 0)"></p>
                    <p class="text-xs text-gray-500 mt-1">Current Usage</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-microchip text-purple-600 text-lg"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center">
                <span class="status-indicator" :class="getMemoryStatus()"></span>
                <span class="text-sm text-gray-600" x-text="getMemoryStatusText()"></span>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Database Performance Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 slideInLeft">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Database Performance</h3>
                <div class="relative" x-show="loading.database">
                    <div class="loading-overlay">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    </div>
                </div>
            </div>
            <div class="performance-chart">
                <canvas id="databaseChart"></canvas>
            </div>
        </div>

        <!-- Cache Performance Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 slideInRight">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Cache Performance</h3>
                <div class="relative" x-show="loading.cache">
                    <div class="loading-overlay">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                    </div>
                </div>
            </div>
            <div class="performance-chart">
                <canvas id="cacheChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Queue Details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 slideInUp">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Queue Status</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Queue</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waiting</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delayed</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reserved</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(queue, name) in metrics.queue" :key="name">
                            <tr x-show="typeof queue === 'object' && queue.waiting !== undefined">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="name"></td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="queue.waiting || 0"></td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="queue.delayed || 0"></td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="queue.reserved || 0"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 slideInUp" style="animation-delay: 0.1s;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">PHP Version</span>
                    <span class="text-sm font-medium text-gray-900" x-text="metrics.system?.php_version || 'N/A'"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Laravel Version</span>
                    <span class="text-sm font-medium text-gray-900" x-text="metrics.system?.laravel_version || 'N/A'"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Memory Limit</span>
                    <span class="text-sm font-medium text-gray-900" x-text="metrics.memory?.limit || 'N/A'"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Peak Memory</span>
                    <span class="text-sm font-medium text-gray-900" x-text="formatBytes(metrics.memory?.peak || 0)"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">System Uptime</span>
                    <span class="text-sm font-medium text-gray-900" x-text="metrics.system?.uptime || 'N/A'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div x-show="notification.show" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         class="fixed top-4 right-4 z-50 max-w-sm bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center"
                         :class="notification.type === 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                        <i :class="notification.type === 'success' ? 'fas fa-check' : 'fas fa-times'" class="text-sm"></i>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                    <p class="text-sm text-gray-500 mt-1" x-text="notification.message"></p>
                </div>
                <button @click="notification.show = false" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function performanceMonitoring() {
    return {
        metrics: {},
        healthSummary: null,
        loading: {
            database: false,
            cache: false,
            queue: false,
        },
        autoRefresh: false,
        refreshInterval: null,
        notification: {
            show: false,
            type: 'success',
            title: '',
            message: ''
        },
        
        async init() {
            await this.loadMetrics();
            await this.loadHealthSummary();
            this.initCharts();
        },
        
        async loadMetrics() {
            try {
                const response = await fetch('/admin/performance/metrics');
                const data = await response.json();
                
                if (data.success) {
                    this.metrics = data.data;
                } else {
                    this.showNotification('error', 'Error', 'Failed to load metrics');
                }
            } catch (error) {
                console.error('Failed to load metrics:', error);
                this.showNotification('error', 'Error', 'Failed to load metrics');
            }
        },
        
        async loadHealthSummary() {
            try {
                const response = await fetch('/admin/performance/health/summary');
                const data = await response.json();
                
                if (data.success) {
                    this.healthSummary = data.data;
                }
            } catch (error) {
                console.error('Failed to load health summary:', error);
            }
        },
        
        toggleAutoRefresh() {
            this.autoRefresh = !this.autoRefresh;
            
            if (this.autoRefresh) {
                this.refreshInterval = setInterval(() => {
                    this.loadMetrics();
                }, 30000); // Refresh every 30 seconds
                this.showNotification('success', 'Auto-refresh', 'Auto-refresh enabled');
            } else {
                if (this.refreshInterval) {
                    clearInterval(this.refreshInterval);
                    this.refreshInterval = null;
                }
                this.showNotification('success', 'Auto-refresh', 'Auto-refresh disabled');
            }
        },
        
        async optimizeCache() {
            try {
                const response = await fetch('/admin/performance/optimize-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('success', 'Cache Optimized', data.message);
                    await this.loadMetrics();
                } else {
                    this.showNotification('error', 'Error', data.error);
                }
            } catch (error) {
                this.showNotification('error', 'Error', 'Failed to optimize cache');
            }
        },
        
        async optimizeQueues() {
            try {
                const response = await fetch('/admin/performance/optimize-queues', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('success', 'Queues Optimized', data.message);
                    await this.loadMetrics();
                } else {
                    this.showNotification('error', 'Error', data.error);
                }
            } catch (error) {
                this.showNotification('error', 'Error', 'Failed to optimize queues');
            }
        },
        
        async clearCache() {
            try {
                const response = await fetch('/admin/performance/clear-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('success', 'Cache Cleared', data.message);
                    await this.loadMetrics();
                } else {
                    this.showNotification('error', 'Error', data.error);
                }
            } catch (error) {
                this.showNotification('error', 'Error', 'Failed to clear cache');
            }
        },
        
        async warmupCache() {
            try {
                const response = await fetch('/admin/performance/warmup-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('success', 'Cache Warmed Up', data.message);
                    await this.loadMetrics();
                } else {
                    this.showNotification('error', 'Error', data.error);
                }
            } catch (error) {
                this.showNotification('error', 'Error', 'Failed to warmup cache');
            }
        },
        
        initCharts() {
            // Initialize charts here
            // This would typically show performance trends over time
        },
        
        getDatabaseStatus() {
            const connections = this.metrics.database?.connections?.active || 0;
            return connections > 0 ? 'status-healthy' : 'status-critical';
        },
        
        getDatabaseStatusText() {
            const connections = this.metrics.database?.connections?.active || 0;
            return connections > 0 ? 'Connected' : 'Disconnected';
        },
        
        getCacheStatus() {
            const hitRate = this.metrics.cache?.hit_rate || 0;
            if (hitRate >= 80) return 'status-healthy';
            if (hitRate >= 60) return 'status-warning';
            return 'status-critical';
        },
        
        getCacheStatusText() {
            const hitRate = this.metrics.cache?.hit_rate || 0;
            if (hitRate >= 80) return 'Excellent';
            if (hitRate >= 60) return 'Good';
            return 'Poor';
        },
        
        getQueueStatus() {
            const totalJobs = this.getTotalQueueJobs();
            if (totalJobs < 100) return 'status-healthy';
            if (totalJobs < 500) return 'status-warning';
            return 'status-critical';
        },
        
        getQueueStatusText() {
            const totalJobs = this.getTotalQueueJobs();
            if (totalJobs < 100) return 'Normal';
            if (totalJobs < 500) return 'Busy';
            return 'Overloaded';
        },
        
        getMemoryStatus() {
            const usage = this.metrics.memory?.usage || 0;
            const limit = this.parseMemoryLimit(this.metrics.memory?.limit || '0');
            
            if (limit > 0) {
                const percentage = (usage / limit) * 100;
                if (percentage < 70) return 'status-healthy';
                if (percentage < 90) return 'status-warning';
                return 'status-critical';
            }
            
            return 'status-healthy';
        },
        
        getMemoryStatusText() {
            const usage = this.metrics.memory?.usage || 0;
            const limit = this.parseMemoryLimit(this.metrics.memory?.limit || '0');
            
            if (limit > 0) {
                const percentage = Math.round((usage / limit) * 100);
                return `${percentage}% used`;
            }
            
            return 'Normal';
        },
        
        getTotalQueueJobs() {
            let total = 0;
            for (const queue in this.metrics.queue || {}) {
                if (typeof this.metrics.queue[queue] === 'object' && this.metrics.queue[queue].total) {
                    total += this.metrics.queue[queue].total;
                }
            }
            return total;
        },
        
        formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        parseMemoryLimit(limit) {
            if (!limit || limit === '-1') return 0;
            
            const value = parseFloat(limit);
            const unit = limit.slice(-1).toUpperCase();
            
            switch (unit) {
                case 'G': return value * 1024 * 1024 * 1024;
                case 'M': return value * 1024 * 1024;
                case 'K': return value * 1024;
                default: return value;
            }
        },
        
        showNotification(type, title, message) {
            this.notification = {
                show: true,
                type: type,
                title: title,
                message: message
            };
            
            setTimeout(() => {
                this.notification.show = false;
            }, 5000);
        }
    }
}
</script>
@endpush
</x-admin.layouts.master>
