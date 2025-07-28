<div 
    x-data="trendAnalysisWidget()" 
    x-init="init()" 
    class="bg-white rounded-xl shadow-lg p-6 border border-gray-100"
>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Trend Analysis</h3>
                <p class="text-sm text-gray-500">Data insights & forecasting</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Period Selector -->
            <select 
                x-model="selectedPeriod" 
                @change="fetchData()"
                class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
            >
                <option value="7d">Last 7 Days</option>
                <option value="30d">Last 30 Days</option>
                <option value="90d">Last 90 Days</option>
                <option value="1y">Last Year</option>
            </select>
            
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
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-gray-100 transition ease-in-out duration-150">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading trend analysis...
        </div>
    </div>

    <!-- Error State -->
    <div x-show="error && !loading" class="text-center py-8">
        <div class="text-red-500 text-sm">
            <p>Failed to load trend analysis</p>
            <button @click="refreshData()" class="mt-2 text-blue-500 hover:text-blue-700 underline">
                Try again
            </button>
        </div>
    </div>

    <!-- Content -->
    <div x-show="!loading && !error" class="space-y-6">
        <!-- Sales Trend Chart -->
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">Sales Trend</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="h-64">
                    <canvas x-ref="salesTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Key Insights -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Insights Panel -->
            <div>
                <h4 class="text-lg font-medium text-gray-900 mb-4">Key Insights</h4>
                <div class="space-y-3">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-start space-x-3">
                            <div class="p-1 bg-blue-100 rounded-full">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Top Insight</p>
                                <p class="text-sm text-blue-700" x-text="data.insights?.top_insight || 'No insights available'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h5 class="text-sm font-medium text-gray-700">Recommendations</h5>
                        <template x-for="recommendation in data.insights?.recommendations || []" :key="recommendation">
                            <div class="flex items-start space-x-2">
                                <div class="w-1.5 h-1.5 bg-teal-500 rounded-full mt-2 flex-shrink-0"></div>
                                <p class="text-sm text-gray-600" x-text="recommendation"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Forecasts -->
            <div>
                <h4 class="text-lg font-medium text-gray-900 mb-4">Forecasts</h4>
                <div class="space-y-4">
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <h5 class="text-sm font-medium text-green-800 mb-2">Revenue Forecast</h5>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-green-700">Next 7 days:</span>
                                <span class="text-sm font-medium text-green-900" x-text="formatCurrency(data.forecasts?.revenue_forecast?.next_7_days || 0)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-green-700">Next 30 days:</span>
                                <span class="text-sm font-medium text-green-900" x-text="formatCurrency(data.forecasts?.revenue_forecast?.next_30_days || 0)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-green-700">Confidence:</span>
                                <span class="text-sm font-medium text-green-900" x-text="(data.forecasts?.revenue_forecast?.confidence || 0) + '%'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                        <h5 class="text-sm font-medium text-purple-800 mb-2">Orders Forecast</h5>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-700">Next 7 days:</span>
                                <span class="text-sm font-medium text-purple-900" x-text="(data.forecasts?.orders_forecast?.next_7_days || 0).toLocaleString()"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-700">Next 30 days:</span>
                                <span class="text-sm font-medium text-purple-900" x-text="(data.forecasts?.orders_forecast?.next_30_days || 0).toLocaleString()"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-700">Confidence:</span>
                                <span class="text-sm font-medium text-purple-900" x-text="(data.forecasts?.orders_forecast?.confidence || 0) + '%'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Trend Chart -->
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">Conversion Rate Trend</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="h-48">
                    <canvas x-ref="conversionTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Geographic Trends -->
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">Top Countries</h4>
            <div class="space-y-3">
                <template x-for="country in data.geographic_trend?.top_countries || []" :key="country.country">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900" x-text="country.country"></p>
                            <p class="text-sm text-gray-500">
                                <span x-text="country.sessions.toLocaleString()"></span> sessions • 
                                <span x-text="country.orders.toLocaleString()"></span> orders
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900" x-text="((country.orders / country.sessions) * 100).toFixed(2) + '%'"></p>
                            <p class="text-xs text-gray-500">conversion</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- User Trend Chart -->
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">User Acquisition Trend</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="h-48">
                    <canvas x-ref="userTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <div x-show="data.insights?.alerts && data.insights.alerts.length > 0">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Alerts</h4>
            <div class="space-y-2">
                <template x-for="alert in data.insights?.alerts || []" :key="alert">
                    <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <p class="text-sm text-yellow-800" x-text="alert"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Period Info -->
        <div class="text-center text-sm text-gray-500">
            <p>Analysis period: <span x-text="formatDateRange()"></span></p>
            <p class="mt-1">Last updated: <span x-text="lastUpdated"></span></p>
        </div>
    </div>
</div>

<script>
function trendAnalysisWidget() {
    return {
        data: {},
        loading: true,
        error: false,
        lastUpdated: '',
        selectedPeriod: '30d',
        salesTrendChart: null,
        conversionTrendChart: null,
        userTrendChart: null,

        init() {
            this.fetchData();
        },

        async fetchData() {
            try {
                this.loading = true;
                this.error = false;

                const response = await fetch(`/api/admin/analytics/trends?period=${this.selectedPeriod}`);
                if (!response.ok) throw new Error('Failed to fetch data');
                
                const result = await response.json();
                if (result.success) {
                    this.data = result.data;
                    this.lastUpdated = new Date().toLocaleTimeString();
                    
                    // Update charts after DOM update
                    this.$nextTick(() => {
                        this.updateCharts();
                    });
                } else {
                    throw new Error(result.message || 'Failed to load data');
                }
            } catch (error) {
                console.error('Trend analysis error:', error);
                this.error = true;
            } finally {
                this.loading = false;
            }
        },

        async refreshData() {
            await this.fetchData();
        },

        updateCharts() {
            this.updateSalesTrendChart();
            this.updateConversionTrendChart();
            this.updateUserTrendChart();
        },

        updateSalesTrendChart() {
            if (!this.data.sales_trend || !this.$refs.salesTrendChart) return;

            if (this.salesTrendChart) {
                this.salesTrendChart.destroy();
            }

            const ctx = this.$refs.salesTrendChart;
            this.salesTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.data.sales_trend.map(item => new Date(item.date).toLocaleDateString()),
                    datasets: [{
                        label: 'Orders',
                        data: this.data.sales_trend.map(item => item.orders),
                        borderColor: '#06B6D4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Revenue',
                        data: this.data.sales_trend.map(item => item.revenue),
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
                            title: { display: true, text: 'Orders' }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: { display: true, text: 'Revenue ($)' },
                            grid: { drawOnChartArea: false }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        },

        updateConversionTrendChart() {
            if (!this.data.conversion_trend || !this.$refs.conversionTrendChart) return;

            if (this.conversionTrendChart) {
                this.conversionTrendChart.destroy();
            }

            const ctx = this.$refs.conversionTrendChart;
            this.conversionTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.data.conversion_trend.map(item => new Date(item.date).toLocaleDateString()),
                    datasets: [{
                        label: 'Conversion Rate (%)',
                        data: this.data.conversion_trend.map(item => item.conversion_rate),
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Conversion Rate (%)' }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        },

        updateUserTrendChart() {
            if (!this.data.user_trend || !this.$refs.userTrendChart) return;

            if (this.userTrendChart) {
                this.userTrendChart.destroy();
            }

            const ctx = this.$refs.userTrendChart;
            this.userTrendChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.data.user_trend.map(item => new Date(item.date).toLocaleDateString()),
                    datasets: [{
                        label: 'New Users',
                        data: this.data.user_trend.map(item => item.new_users),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: '#3B82F6',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'New Users' }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount);
        },

        formatDateRange() {
            if (!this.data.date_range) return 'N/A';
            
            const start = new Date(this.data.date_range.start).toLocaleDateString();
            const end = new Date(this.data.date_range.end).toLocaleDateString();
            return `${start} - ${end}`;
        }
    }
}
</script>
