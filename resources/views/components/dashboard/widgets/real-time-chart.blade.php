<!-- Real-time Chart Widget Component -->
<div x-data="realTimeChart()" 
     x-init="init()"
     class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900" x-text="title"></h3>
            <p class="text-sm text-gray-500" x-text="subtitle"></p>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Time Period Selector -->
            <select x-model="selectedPeriod" 
                    @change="changePeriod()"
                    class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <template x-for="period in periods" :key="period.value">
                    <option :value="period.value" x-text="period.label"></option>
                </template>
            </select>
            
            <!-- Refresh Button -->
            <button @click="refreshData()" 
                    :disabled="loading"
                    class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <i class="fas fa-sync-alt text-sm" :class="{ 'animate-spin': loading }"></i>
            </button>
            
            <!-- Live Indicator -->
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-xs text-gray-500">Live</span>
            </div>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="relative">
        <div class="h-80 mb-4">
            <canvas :id="chartId" class="w-full h-full"></canvas>
        </div>
        
        <!-- Loading Overlay -->
        <div x-show="loading" 
             class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
            <div class="flex items-center space-x-2 text-gray-500">
                <div class="w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-sm">Loading chart data...</span>
            </div>
        </div>
    </div>

    <!-- Chart Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-200">
        <template x-for="stat in chartStats" :key="stat.label">
            <div class="text-center">
                <div class="text-lg font-semibold" :class="stat.color" x-text="stat.value"></div>
                <div class="text-xs text-gray-500" x-text="stat.label"></div>
            </div>
        </template>
    </div>

    <!-- Quick Actions -->
    <div class="flex space-x-2 mt-4" x-show="actions.length > 0">
        <template x-for="action in actions" :key="action.id">
            <button @click="handleAction(action)"
                    class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200"
                    :class="action.primary ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                <i :class="action.icon + ' mr-2'"></i>
                <span x-text="action.label"></span>
            </button>
        </template>
    </div>
</div>

<script>
function realTimeChart() {
    return {
        // Configuration
        title: '',
        subtitle: '',
        chartType: 'line',
        chartId: '',
        dataEndpoint: '',
        refreshInterval: 60000, // 1 minute
        
        // Data
        selectedPeriod: '24h',
        periods: [
            { value: '24h', label: 'Last 24 Hours' },
            { value: '7d', label: 'Last 7 Days' },
            { value: '30d', label: 'Last 30 Days' },
            { value: '12m', label: 'Last 12 Months' }
        ],
        chartStats: [],
        actions: [],
        
        // State
        loading: false,
        chart: null,
        refreshTimer: null,
        
        init() {
            this.chartId = 'chart-' + Math.random().toString(36).substr(2, 9);
            this.initChart();
            this.loadData();
            this.startAutoRefresh();
        },
        
        destroy() {
            if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
            }
            if (this.chart) {
                this.chart.destroy();
            }
        },
        
        startAutoRefresh() {
            this.refreshTimer = setInterval(() => {
                this.loadData();
            }, this.refreshInterval);
        },
        
        initChart() {
            const ctx = document.getElementById(this.chartId);
            if (!ctx) return;
            
            this.chart = new Chart(ctx, {
                type: this.chartType,
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            display: true,
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        }
                    },
                    elements: {
                        line: {
                            tension: 0.4
                        },
                        point: {
                            radius: 4,
                            hoverRadius: 6
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        },
        
        async loadData() {
            this.loading = true;
            
            try {
                const response = await fetch(`${this.dataEndpoint}?period=${this.selectedPeriod}`);
                const result = await response.json();
                
                if (result.success) {
                    this.updateChart(result.data);
                    this.updateStats(result.data);
                }
            } catch (error) {
                console.error('Failed to load chart data:', error);
            } finally {
                this.loading = false;
            }
        },
        
        updateChart(data) {
            if (!this.chart) return;
            
            this.chart.data.labels = data.labels || [];
            this.chart.data.datasets = data.datasets || [];
            this.chart.update('active');
        },
        
        updateStats(data) {
            if (!data.stats) return;
            
            this.chartStats = data.stats;
        },
        
        changePeriod() {
            this.loadData();
        },
        
        refreshData() {
            this.loadData();
        },
        
        handleAction(action) {
            if (action.url) {
                window.location.href = action.url;
            } else if (action.callback) {
                action.callback();
            }
        }
    }
}
</script>
