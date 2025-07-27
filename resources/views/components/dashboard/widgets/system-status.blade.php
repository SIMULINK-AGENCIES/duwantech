<!-- System Status Widget Component -->
<div x-data="systemStatus()" 
     x-init="init()"
     class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">System Status</h3>
            <p class="text-sm text-gray-500">Real-time system health monitoring</p>
        </div>
        
        <div class="flex items-center space-x-2">
            <div :class="overallStatusClass" class="w-3 h-3 rounded-full"></div>
            <span class="text-sm font-medium" :class="overallStatusTextClass" x-text="overallStatus"></span>
        </div>
    </div>

    <!-- System Metrics -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <!-- Server Load -->
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Server Load</span>
                <span class="text-sm text-gray-500" x-text="Math.round(serverLoad * 100) + '%'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-300" 
                     :class="getLoadBarClass(serverLoad)"
                     :style="`width: ${serverLoad * 100}%`"></div>
            </div>
        </div>

        <!-- Response Time -->
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Response Time</span>
                <span class="text-sm text-gray-500" x-text="responseTimeText"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-300" 
                     :class="getResponseTimeBarClass()"
                     :style="`width: ${Math.min(responseTime / 2 * 100, 100)}%`"></div>
            </div>
        </div>

        <!-- Cache Hit Rate -->
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Cache Hit Rate</span>
                <span class="text-sm text-gray-500" x-text="Math.round(cacheHitRate * 100) + '%'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                     :style="`width: ${cacheHitRate * 100}%`"></div>
            </div>
        </div>

        <!-- Queue Size -->
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Queue Size</span>
                <span class="text-sm text-gray-500" x-text="queueSize + ' jobs'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-300" 
                     :class="getQueueBarClass()"
                     :style="`width: ${Math.min(queueSize / 100 * 100, 100)}%`"></div>
            </div>
        </div>
    </div>

    <!-- Error Rate -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Error Rate</span>
            <span class="text-sm" :class="errorRate > 0.05 ? 'text-red-600' : 'text-green-600'" 
                  x-text="Math.round(errorRate * 100 * 100) / 100 + '%'"></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-300" 
                 :class="errorRate > 0.05 ? 'bg-red-500' : 'bg-green-500'"
                 :style="`width: ${Math.min(errorRate * 100 * 10, 100)}%`"></div>
        </div>
    </div>

    <!-- Service Status -->
    <div class="space-y-3">
        <h4 class="text-sm font-medium text-gray-900">Service Status</h4>
        
        <template x-for="service in services" :key="service.name">
            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-md">
                <div class="flex items-center space-x-3">
                    <div :class="service.status === 'operational' ? 'bg-green-400' : 
                                 service.status === 'degraded' ? 'bg-yellow-400' : 'bg-red-400'" 
                         class="w-2 h-2 rounded-full"></div>
                    <span class="text-sm font-medium text-gray-700" x-text="service.name"></span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500" x-text="service.uptime"></span>
                    <span class="text-xs px-2 py-1 rounded-full" 
                          :class="service.status === 'operational' ? 'bg-green-100 text-green-800' : 
                                  service.status === 'degraded' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'"
                          x-text="service.status"></span>
                </div>
            </div>
        </template>
    </div>

    <!-- Actions -->
    <div class="flex space-x-2 mt-6 pt-4 border-t border-gray-200">
        <button @click="refreshStatus()" 
                :disabled="loading"
                class="flex-1 px-3 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors duration-200">
            <i class="fas fa-sync-alt mr-2" :class="{ 'animate-spin': loading }"></i>
            Refresh Status
        </button>
        
        <button @click="viewLogs()" 
                class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200">
            <i class="fas fa-file-alt mr-2"></i>
            View Logs
        </button>
    </div>

    <!-- Loading State -->
    <div x-show="loading" 
         class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
        <div class="flex items-center space-x-2 text-gray-500">
            <div class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm">Updating status...</span>
        </div>
    </div>
</div>

<script>
function systemStatus() {
    return {
        // Data
        overallStatus: 'Healthy',
        serverLoad: 0.65,
        responseTime: 0.25,
        cacheHitRate: 0.95,
        queueSize: 15,
        errorRate: 0.02,
        services: [
            { name: 'Web Server', status: 'operational', uptime: '99.9%' },
            { name: 'Database', status: 'operational', uptime: '99.8%' },
            { name: 'Cache', status: 'operational', uptime: '99.9%' },
            { name: 'Queue', status: 'operational', uptime: '99.7%' },
            { name: 'Mail Service', status: 'operational', uptime: '99.5%' },
        ],
        
        // State
        loading: false,
        refreshInterval: 30000, // 30 seconds
        refreshTimer: null,
        
        init() {
            this.startAutoRefresh();
            this.loadStatus();
        },
        
        destroy() {
            if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
            }
        },
        
        startAutoRefresh() {
            this.refreshTimer = setInterval(() => {
                this.loadStatus();
            }, this.refreshInterval);
        },
        
        async loadStatus() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/dashboard/system-health');
                const result = await response.json();
                
                if (result.success) {
                    this.updateStatus(result.data);
                }
            } catch (error) {
                console.error('Failed to load system status:', error);
            } finally {
                this.loading = false;
            }
        },
        
        updateStatus(data) {
            this.overallStatus = data.overall_status;
            this.serverLoad = data.performance?.server_load || this.serverLoad;
            this.responseTime = data.performance?.response_time || this.responseTime;
            this.cacheHitRate = data.performance?.cache_hit_rate || this.cacheHitRate;
            this.queueSize = data.performance?.queue_size || this.queueSize;
            this.errorRate = data.performance?.error_rate || this.errorRate;
        },
        
        refreshStatus() {
            this.loadStatus();
        },
        
        viewLogs() {
            window.location.href = '/admin/logs';
        },
        
        getLoadBarClass(load) {
            if (load > 0.8) return 'bg-red-500';
            if (load > 0.6) return 'bg-yellow-500';
            return 'bg-green-500';
        },
        
        getResponseTimeBarClass() {
            if (this.responseTime > 1) return 'bg-red-500';
            if (this.responseTime > 0.5) return 'bg-yellow-500';
            return 'bg-green-500';
        },
        
        getQueueBarClass() {
            if (this.queueSize > 50) return 'bg-red-500';
            if (this.queueSize > 20) return 'bg-yellow-500';
            return 'bg-green-500';
        },
        
        get overallStatusClass() {
            switch(this.overallStatus.toLowerCase()) {
                case 'healthy': return 'bg-green-400 animate-pulse';
                case 'warning': return 'bg-yellow-400 animate-pulse';
                case 'critical': return 'bg-red-400 animate-pulse';
                default: return 'bg-gray-400';
            }
        },
        
        get overallStatusTextClass() {
            switch(this.overallStatus.toLowerCase()) {
                case 'healthy': return 'text-green-700';
                case 'warning': return 'text-yellow-700';
                case 'critical': return 'text-red-700';
                default: return 'text-gray-700';
            }
        },
        
        get responseTimeText() {
            if (this.responseTime < 0.5) {
                return Math.round(this.responseTime * 1000) + 'ms';
            }
            return this.responseTime.toFixed(2) + 's';
        }
    }
}
</script>
