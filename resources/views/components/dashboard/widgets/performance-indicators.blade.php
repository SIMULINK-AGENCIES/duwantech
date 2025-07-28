<div 
    x-data="performanceIndicatorsWidget()" 
    x-init="init()" 
    class="bg-white rounded-xl shadow-lg p-6 border border-gray-100"
>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Performance Indicators</h3>
                <p class="text-sm text-gray-500">Key business metrics</p>
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
            Loading performance data...
        </div>
    </div>

    <!-- Error State -->
    <div x-show="error && !loading" class="text-center py-8">
        <div class="text-red-500 text-sm">
            <p>Failed to load performance indicators</p>
            <button @click="refreshData()" class="mt-2 text-blue-500 hover:text-blue-700 underline">
                Try again
            </button>
        </div>
    </div>

    <!-- Content -->
    <div x-show="!loading && !error" class="space-y-6">
        <!-- KPI Summary -->
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">Key Performance Indicators</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Total Revenue -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600">Total Revenue</p>
                            <p class="text-2xl font-bold text-green-900" x-text="formatCurrency(data.kpi_summary?.total_revenue || 0)"></p>
                        </div>
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600">Total Orders</p>
                            <p class="text-2xl font-bold text-blue-900" x-text="(data.kpi_summary?.total_orders || 0).toLocaleString()"></p>
                        </div>
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Customers -->
                <div class="bg-gradient-to-r from-purple-50 to-violet-50 p-4 rounded-lg border border-purple-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600">Total Customers</p>
                            <p class="text-2xl font-bold text-purple-900" x-text="(data.kpi_summary?.total_customers || 0).toLocaleString()"></p>
                        </div>
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Average Order Value -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-4 rounded-lg border border-yellow-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-600">Avg Order Value</p>
                            <p class="text-2xl font-bold text-yellow-900" x-text="formatCurrency(data.kpi_summary?.avg_order_value || 0)"></p>
                        </div>
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Customer Lifetime Value -->
                <div class="bg-gradient-to-r from-pink-50 to-rose-50 p-4 rounded-lg border border-pink-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-pink-600">Customer LTV</p>
                            <p class="text-2xl font-bold text-pink-900" x-text="formatCurrency(data.kpi_summary?.customer_lifetime_value || 0)"></p>
                        </div>
                        <div class="p-2 bg-pink-100 rounded-lg">
                            <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Churn Rate -->
                <div class="bg-gradient-to-r from-red-50 to-pink-50 p-4 rounded-lg border border-red-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-600">Churn Rate</p>
                            <p class="text-2xl font-bold text-red-900" x-text="(data.kpi_summary?.churn_rate || 0).toFixed(1) + '%'"></p>
                        </div>
                        <div class="p-2 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Growth Metrics -->
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">Growth Metrics</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Revenue Growth</p>
                            <p class="text-xl font-bold text-gray-900">
                                <span x-text="data.growth_metrics?.revenue_growth >= 0 ? '+' : ''"></span><span x-text="(data.growth_metrics?.revenue_growth || 0).toFixed(1)"></span>%
                            </p>
                        </div>
                        <div 
                            class="p-2 rounded-lg"
                            :class="data.growth_metrics?.revenue_growth >= 0 ? 'bg-green-100' : 'bg-red-100'"
                        >
                            <svg 
                                class="w-5 h-5"
                                :class="data.growth_metrics?.revenue_growth >= 0 ? 'text-green-600' : 'text-red-600'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path 
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    :d="data.growth_metrics?.revenue_growth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6'"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Customer Growth</p>
                            <p class="text-xl font-bold text-gray-900">
                                <span x-text="data.growth_metrics?.customer_growth >= 0 ? '+' : ''"></span><span x-text="(data.growth_metrics?.customer_growth || 0).toFixed(1)"></span>%
                            </p>
                        </div>
                        <div 
                            class="p-2 rounded-lg"
                            :class="data.growth_metrics?.customer_growth >= 0 ? 'bg-green-100' : 'bg-red-100'"
                        >
                            <svg 
                                class="w-5 h-5"
                                :class="data.growth_metrics?.customer_growth >= 0 ? 'text-green-600' : 'text-red-600'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path 
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    :d="data.growth_metrics?.customer_growth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6'"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Order Growth</p>
                            <p class="text-xl font-bold text-gray-900">
                                <span x-text="data.growth_metrics?.order_growth >= 0 ? '+' : ''"></span><span x-text="(data.growth_metrics?.order_growth || 0).toFixed(1)"></span>%
                            </p>
                        </div>
                        <div 
                            class="p-2 rounded-lg"
                            :class="data.growth_metrics?.order_growth >= 0 ? 'bg-green-100' : 'bg-red-100'"
                        >
                            <svg 
                                class="w-5 h-5"
                                :class="data.growth_metrics?.order_growth >= 0 ? 'text-green-600' : 'text-red-600'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path 
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    :d="data.growth_metrics?.order_growth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6'"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Efficiency Metrics -->
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">Efficiency Metrics</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900" x-text="(data.efficiency_metrics?.conversion_rate || 0).toFixed(2) + '%'"></p>
                    <p class="text-sm text-gray-500">Conversion Rate</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900" x-text="(data.efficiency_metrics?.bounce_rate || 0).toFixed(1) + '%'"></p>
                    <p class="text-sm text-gray-500">Bounce Rate</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900" x-text="Math.round(data.efficiency_metrics?.session_duration || 0) + ' min'"></p>
                    <p class="text-sm text-gray-500">Avg Session</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900" x-text="Math.round(data.efficiency_metrics?.pages_per_session || 0)"></p>
                    <p class="text-sm text-gray-500">Pages/Session</p>
                </div>
            </div>
        </div>

        <!-- Top Performing Products -->
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">Top Performing Products</h4>
            <div class="space-y-3">
                <template x-for="(product, index) in data.product_performance || []" :key="product.name">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div 
                                class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium"
                                :class="getProductRankColor(index)"
                                x-text="index + 1"
                            ></div>
                            <div>
                                <p class="font-medium text-gray-900" x-text="product.name"></p>
                                <p class="text-sm text-gray-500" x-text="product.orders + ' orders'"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900" x-text="formatCurrency(product.revenue)"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- User Engagement -->
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">User Engagement</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-2xl font-bold text-blue-900" x-text="data.user_engagement?.active_users_today || 0"></p>
                    <p class="text-sm text-blue-600">Active Today</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg border border-green-100">
                    <p class="text-2xl font-bold text-green-900" x-text="data.user_engagement?.new_users_today || 0"></p>
                    <p class="text-sm text-green-600">New Today</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <p class="text-2xl font-bold text-purple-900" x-text="(data.user_engagement?.returning_users || 0) + '%'"></p>
                    <p class="text-sm text-purple-600">Returning</p>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg border border-orange-100">
                    <p class="text-2xl font-bold text-orange-900" x-text="(data.user_engagement?.user_retention_rate || 0) + '%'"></p>
                    <p class="text-sm text-orange-600">Retention</p>
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
function performanceIndicatorsWidget() {
    return {
        data: {},
        loading: true,
        error: false,
        lastUpdated: '',

        init() {
            this.fetchData();
            // Auto-refresh every 5 minutes
            setInterval(() => {
                this.fetchData();
            }, 300000);
        },

        async fetchData() {
            try {
                this.loading = true;
                this.error = false;

                const response = await fetch('/api/admin/analytics/performance');
                if (!response.ok) throw new Error('Failed to fetch data');
                
                const result = await response.json();
                if (result.success) {
                    this.data = result.data;
                    this.lastUpdated = new Date().toLocaleTimeString();
                } else {
                    throw new Error(result.message || 'Failed to load data');
                }
            } catch (error) {
                console.error('Performance indicators error:', error);
                this.error = true;
            } finally {
                this.loading = false;
            }
        },

        async refreshData() {
            await this.fetchData();
        },

        getProductRankColor(index) {
            const colors = [
                'bg-yellow-500',  // Gold
                'bg-gray-400',    // Silver
                'bg-orange-600',  // Bronze
                'bg-blue-500',
                'bg-green-500'
            ];
            return colors[index] || 'bg-gray-500';
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
