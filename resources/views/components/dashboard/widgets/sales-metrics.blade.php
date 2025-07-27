<div 
    x-data="salesMetricsWidget()" 
    x-init="init()" 
    class="bg-white rounded-xl shadow-lg p-6 border border-gray-100"
>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Real-Time Sales</h3>
                <p class="text-sm text-gray-500">Live performance metrics</p>
            </div>
        </div>
        
        <!-- Refresh Button -->
        <button 
            @click="refreshData()"
            :disabled="loading"
            class="p-2 text-gray-400 hover:text-gray-600 transition-colors disabled:opacity-50"
            title="Refresh data"
        >
            <svg class="w-5 h-5" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </button>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-gray-100 transition ease-in-out duration-150">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading metrics...
        </div>
    </div>

    <!-- Error State -->
    <div x-show="error && !loading" class="text-center py-8">
        <div class="text-red-500 text-sm">
            <p>Failed to load sales metrics</p>
            <button @click="refreshData()" class="mt-2 text-blue-500 hover:text-blue-700 underline">
                Try again
            </button>
        </div>
    </div>

    <!-- Content -->
    <div x-show="!loading && !error" class="space-y-6">
        <!-- Today's Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Revenue Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Today's Revenue</p>
                        <p class="text-2xl font-bold text-blue-900" x-text="formatCurrency(data.today?.revenue || 0)"></p>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center">
                    <span 
                        class="text-sm font-medium"
                        :class="data.today?.revenue_change >= 0 ? 'text-green-600' : 'text-red-600'"
                    >
                        <span x-text="data.today?.revenue_change >= 0 ? '+' : ''"></span><span x-text="Math.abs(data.today?.revenue_change || 0).toFixed(1)"></span>%
                    </span>
                    <span class="text-sm text-gray-500 ml-1">vs yesterday</span>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Today's Orders</p>
                        <p class="text-2xl font-bold text-green-900" x-text="data.today?.orders || 0"></p>
                    </div>
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center">
                    <span 
                        class="text-sm font-medium"
                        :class="data.today?.orders_change >= 0 ? 'text-green-600' : 'text-red-600'"
                    >
                        <span x-text="data.today?.orders_change >= 0 ? '+' : ''"></span><span x-text="Math.abs(data.today?.orders_change || 0).toFixed(1)"></span>%
                    </span>
                    <span class="text-sm text-gray-500 ml-1">vs yesterday</span>
                </div>
            </div>

            <!-- Average Order Value Card -->
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 p-4 rounded-lg border border-purple-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600">Avg Order Value</p>
                        <p class="text-2xl font-bold text-purple-900" x-text="formatCurrency(data.today?.avg_order_value || 0)"></p>
                    </div>
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-sm text-gray-500">Per transaction</span>
                </div>
            </div>
        </div>

        <!-- Hourly Sales Chart -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-sm font-medium text-gray-700 mb-4">Hourly Sales Today</h4>
            <div class="h-64">
                <canvas x-ref="salesChart"></canvas>
            </div>
        </div>

        <!-- Weekly Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-700 mb-3">This Week</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Orders:</span>
                        <span class="text-sm font-medium" x-text="data.week?.orders || 0"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Revenue:</span>
                        <span class="text-sm font-medium" x-text="formatCurrency(data.week?.revenue || 0)"></span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-700 mb-3">This Month</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Orders:</span>
                        <span class="text-sm font-medium" x-text="data.month?.orders || 0"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Revenue:</span>
                        <span class="text-sm font-medium" x-text="formatCurrency(data.month?.revenue || 0)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Updated -->
        <div class="text-xs text-gray-500 text-center">
            Last updated: <span x-text="lastUpdated"></span>
        </div>
    </div>
</div>

<script>
function salesMetricsWidget() {
    return {
        data: {},
        loading: true,
        error: false,
        lastUpdated: '',
        chart: null,

        init() {
            this.fetchData();
            // Auto-refresh every 30 seconds
            setInterval(() => {
                this.fetchData();
            }, 30000);
        },

        async fetchData() {
            try {
                this.loading = true;
                this.error = false;

                const response = await fetch('/api/admin/analytics/real-time-sales');
                if (!response.ok) throw new Error('Failed to fetch data');
                
                const result = await response.json();
                if (result.success) {
                    this.data = result.data;
                    this.lastUpdated = new Date().toLocaleTimeString();
                    this.updateChart();
                } else {
                    throw new Error(result.message || 'Failed to load data');
                }
            } catch (error) {
                console.error('Sales metrics error:', error);
                this.error = true;
            } finally {
                this.loading = false;
            }
        },

        async refreshData() {
            await this.fetchData();
        },

        updateChart() {
            if (!this.data.hourly_sales) return;

            const ctx = this.$refs.salesChart;
            if (!ctx) return;

            if (this.chart) {
                this.chart.destroy();
            }

            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.data.hourly_sales.map(item => item.hour),
                    datasets: [{
                        label: 'Orders',
                        data: this.data.hourly_sales.map(item => item.orders),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Revenue',
                        data: this.data.hourly_sales.map(item => item.revenue),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Orders'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Revenue ($)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount);
        }
    }
}
</script>
