<!-- Live Metrics Card Component -->
<div x-data="liveMetricsCard()" 
     x-init="init()"
     class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-blue-200 group">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
            <div class="p-2 rounded-lg transition-all duration-300 group-hover:scale-110" :class="iconBgClass">
                <i :class="iconClass + ' text-lg transition-transform duration-300 group-hover:rotate-12'"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors duration-200" x-text="title"></h3>
                <p class="text-xs text-gray-400" x-text="subtitle"></p>
            </div>
        </div>
        
        <!-- Live indicator with enhanced animation -->
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse shadow-green-400/50 shadow-sm"></div>
            <span class="text-xs text-gray-500 group-hover:text-green-600 transition-colors duration-200">Live</span>
        </div>
    </div>

    <!-- Main Value with count-up animation -->
    <div class="mb-4">
        <div class="flex items-end space-x-2">
            <span class="text-3xl font-bold text-gray-900 transition-all duration-500 group-hover:text-blue-600" 
                  x-text="animatedValue || formatValue(currentValue)"
                  x-ref="valueDisplay"></span>
            <span class="text-sm text-gray-500" x-text="unit"></span>
        </div>
        
        <!-- Change Indicator with enhanced animation -->
        <div class="flex items-center mt-2 transform transition-all duration-300" x-show="showChange" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="flex items-center space-x-1 px-2 py-1 rounded-full" :class="changeClass + ' transition-all duration-300 hover:scale-105'">
                <i :class="changeIcon + ' text-xs transition-transform duration-300'" 
                   :style="changePercentage > 0 ? 'transform: translateY(-1px)' : changePercentage < 0 ? 'transform: translateY(1px)' : ''"></i>
                <span class="text-sm font-medium" x-text="Math.abs(changePercentage) + '%'"></span>
            </div>
            <span class="text-xs text-gray-500 ml-2" x-text="changeText"></span>
        </div>
    </div>

    <!-- Mini Chart with loading animation -->
    <div class="h-20 mb-4 relative" x-show="showChart">
        <div x-show="chartLoading" class="absolute inset-0 flex items-center justify-center bg-gray-50 rounded">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
        </div>
        <canvas :id="chartId" class="w-full h-full transition-opacity duration-300" 
                :class="{ 'opacity-0': chartLoading, 'opacity-100': !chartLoading }"></canvas>
    </div>

    <!-- Quick Actions -->
    <div class="flex space-x-2" x-show="actions.length > 0">
        <template x-for="action in actions" :key="action.id">
            <button @click="handleAction(action)"
                    class="flex-1 px-3 py-2 text-xs font-medium rounded-md transition-colors duration-200"
                    :class="action.primary ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                <i :class="action.icon + ' mr-1'"></i>
                <span x-text="action.label"></span>
            </button>
        </template>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
        <div class="flex items-center space-x-2 text-gray-500">
            <div class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm">Updating...</span>
        </div>
    </div>
</div>

<script>
function liveMetricsCard() {
    return {
        // Configuration
        title: '',
        subtitle: '',
        currentValue: 0,
        previousValue: 0,
        changePercentage: 0,
        unit: '',
        iconClass: '',
        iconBgClass: '',
        chartId: '',
        chartType: 'line',
        refreshInterval: 30000, // 30 seconds
        showChange: true,
        showChart: true,
        actions: [],
        
        // Animation state
        animatedValue: null,
        chartLoading: false,
        
        // State
        loading: false,
        chart: null,
        refreshTimer: null,
        
        init() {
            this.chartId = 'chart-' + Math.random().toString(36).substr(2, 9);
            this.startAutoRefresh();
            this.initChart();
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
                this.fetchData();
            }, this.refreshInterval);
        },
        
        async fetchData() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/dashboard/metrics');
                const data = await response.json();
                
                if (data.success) {
                    this.updateData(data.data);
                }
            } catch (error) {
                console.error('Failed to fetch metrics:', error);
            } finally {
                this.loading = false;
            }
        },
        
        updateData(data) {
            // This would be customized based on the specific metric
            this.previousValue = this.currentValue;
            // Update currentValue based on data
            this.calculateChange();
            this.updateChart(data);
        },
        
        calculateChange() {
            if (this.previousValue === 0) {
                this.changePercentage = this.currentValue > 0 ? 100 : 0;
            } else {
                this.changePercentage = ((this.currentValue - this.previousValue) / this.previousValue) * 100;
            }
        },
        
        initChart() {
            if (!this.showChart) return;
            
            const ctx = document.getElementById(this.chartId);
            if (!ctx) return;
            
            this.chart = new Chart(ctx, {
                type: this.chartType,
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        pointRadius: 0,
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { display: false },
                        y: { display: false }
                    },
                    elements: {
                        point: { radius: 0 }
                    }
                }
            });
        },
        
        updateChart(data) {
            if (!this.chart || !data.chartData) return;
            
            this.chart.data.labels = data.chartData.labels || [];
            this.chart.data.datasets[0].data = data.chartData.data || [];
            this.chart.update('none');
        },
        
        formatValue(value) {
            if (typeof value === 'number') {
                if (value >= 1000000) {
                    return (value / 1000000).toFixed(1) + 'M';
                } else if (value >= 1000) {
                    return (value / 1000).toFixed(1) + 'K';
                }
                return value.toLocaleString();
            }
            return value;
        },
        
        // Enhanced count-up animation
        animateValue(start, end, duration = 1000) {
            const range = end - start;
            const increment = range / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                    current = end;
                    clearInterval(timer);
                }
                this.animatedValue = this.formatValue(Math.floor(current));
            }, 16);
        },
        
        // Trigger value update animation
        updateValue(newValue) {
            const oldValue = this.currentValue || 0;
            this.currentValue = newValue;
            this.animateValue(oldValue, newValue);
        },
        
        handleAction(action) {
            // Add ripple effect
            if (action.url) {
                // Smooth transition effect
                this.$el.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.$el.style.transform = 'scale(1)';
                    window.location.href = action.url;
                }, 100);
            } else if (action.callback) {
                action.callback();
            }
        },
        
        get changeClass() {
            const baseClasses = 'text-xs px-2 py-1 rounded-full font-medium';
            if (this.changePercentage > 0) {
                return baseClasses + ' bg-green-50 text-green-600 border border-green-200';
            } else if (this.changePercentage < 0) {
                return baseClasses + ' bg-red-50 text-red-600 border border-red-200';
            }
            return baseClasses + ' bg-gray-50 text-gray-600 border border-gray-200';
        },
        
        get changeIcon() {
            if (this.changePercentage > 0) {
                return 'fas fa-arrow-up';
            } else if (this.changePercentage < 0) {
                return 'fas fa-arrow-down';
            }
            return 'fas fa-minus';
        },
        
        get changeText() {
            if (this.changePercentage > 0) {
                return 'vs yesterday';
            } else if (this.changePercentage < 0) {
                return 'vs yesterday';
            }
            return 'no change';
        }
    }
}
</script>
