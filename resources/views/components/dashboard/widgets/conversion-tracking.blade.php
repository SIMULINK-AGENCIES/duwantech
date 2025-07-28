<div 
    x-data="conversionTrackingWidget()" 
    x-init="init()" 
    class="bg-white rounded-xl shadow-lg p-6 border border-gray-100"
>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Conversion Tracking</h3>
                <p class="text-sm text-gray-500">Customer journey analysis</p>
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
            Loading conversion data...
        </div>
    </div>

    <!-- Error State -->
    <div x-show="error && !loading" class="text-center py-8">
        <div class="text-red-500 text-sm">
            <p>Failed to load conversion tracking data</p>
            <button @click="refreshData()" class="mt-2 text-blue-500 hover:text-blue-700 underline">
                Try again
            </button>
        </div>
    </div>

    <!-- Content -->
    <div x-show="!loading && !error">
        <!-- Conversion Rate Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-100">
                <div class="text-center">
                    <p class="text-sm font-medium text-blue-600">Today</p>
                    <p class="text-3xl font-bold text-blue-900" x-text="(data.conversion_rates?.today || 0).toFixed(2) + '%'"></p>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-100">
                <div class="text-center">
                    <p class="text-sm font-medium text-green-600">This Week</p>
                    <p class="text-3xl font-bold text-green-900" x-text="(data.conversion_rates?.week || 0).toFixed(2) + '%'"></p>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 p-4 rounded-lg border border-purple-100">
                <div class="text-center">
                    <p class="text-sm font-medium text-purple-600">This Month</p>
                    <p class="text-3xl font-bold text-purple-900" x-text="(data.conversion_rates?.month || 0).toFixed(2) + '%'"></p>
                </div>
            </div>
        </div>

        <!-- Conversion Funnel -->
        <div class="mb-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Conversion Funnel</h4>
            <div class="space-y-3">
                <template x-for="(stage, index) in data.funnel?.stages || []" :key="stage.name">
                    <div class="relative">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div 
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium"
                                        :class="getFunnelStageColor(index)"
                                        x-text="index + 1"
                                    ></div>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900" x-text="stage.name"></p>
                                    <p class="text-sm text-gray-500" x-text="stage.count.toLocaleString() + ' users'"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-900" x-text="stage.percentage.toFixed(1) + '%'"></p>
                                <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                                    <div 
                                        class="h-2 rounded-full transition-all duration-500"
                                        :class="getFunnelBarColor(index)"
                                        :style="`width: ${stage.percentage}%`"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <!-- Drop-off indicator -->
                        <div x-show="index < (data.funnel?.stages?.length || 0) - 1" class="absolute left-1/2 transform -translate-x-1/2 -bottom-2">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Channel Performance -->
        <div class="mb-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Channel Performance</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="[channel, metrics] in Object.entries(data.channel_performance || {})" :key="channel">
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="font-medium text-gray-900 capitalize" x-text="channel"></h5>
                            <div 
                                class="px-2 py-1 text-xs font-medium rounded-full"
                                :class="getChannelBadgeColor(channel)"
                                x-text="((metrics.orders / metrics.sessions) * 100).toFixed(1) + '%'"
                            ></div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Sessions:</span>
                                <span class="font-medium" x-text="metrics.sessions.toLocaleString()"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Orders:</span>
                                <span class="font-medium" x-text="metrics.orders.toLocaleString()"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Revenue:</span>
                                <span class="font-medium" x-text="formatCurrency(metrics.revenue)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Top Converting Products -->
        <div class="mb-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Top Converting Products</h4>
            <div class="space-y-3">
                <template x-for="product in data.product_conversions?.slice(0, 5) || []" :key="product.product_id">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900" x-text="product.product_name"></p>
                            <p class="text-sm text-gray-500">
                                <span x-text="product.views.toLocaleString()"></span> views • 
                                <span x-text="product.orders.toLocaleString()"></span> orders
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-semibold text-gray-900" x-text="product.conversion_rate.toFixed(2) + '%'"></p>
                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                <div 
                                    class="bg-gradient-to-r from-orange-400 to-red-500 h-2 rounded-full transition-all duration-500"
                                    :style="`width: ${Math.min(product.conversion_rate, 100)}%`"
                                ></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- User Journey Metrics -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-lg font-medium text-gray-900 mb-4">User Journey Insights</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-gray-900" x-text="Math.round(data.user_journey?.avg_session_duration || 0) + ' min'"></p>
                    <p class="text-sm text-gray-500">Avg Session</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900" x-text="Math.round(data.user_journey?.avg_pages_per_session || 0)"></p>
                    <p class="text-sm text-gray-500">Pages/Session</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900" x-text="Math.round(data.user_journey?.bounce_rate || 0) + '%'"></p>
                    <p class="text-sm text-gray-500">Bounce Rate</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900" x-text="Math.round(data.user_journey?.return_visitor_rate || 0) + '%'"></p>
                    <p class="text-sm text-gray-500">Return Visitors</p>
                </div>
            </div>
        </div>

        <!-- Last Updated -->
        <div class="text-xs text-gray-500 text-center mt-4">
            Last updated: <span x-text="lastUpdated"></span>
        </div>
    </div>
</div>

<script>
function conversionTrackingWidget() {
    return {
        data: {},
        loading: true,
        error: false,
        lastUpdated: '',

        init() {
            this.fetchData();
            // Auto-refresh every 2 minutes
            setInterval(() => {
                this.fetchData();
            }, 120000);
        },

        async fetchData() {
            try {
                this.loading = true;
                this.error = false;

                const response = await fetch('/api/admin/analytics/conversion');
                if (!response.ok) throw new Error('Failed to fetch data');
                
                const result = await response.json();
                if (result.success) {
                    this.data = result.data;
                    this.lastUpdated = new Date().toLocaleTimeString();
                } else {
                    throw new Error(result.message || 'Failed to load data');
                }
            } catch (error) {
                console.error('Conversion tracking error:', error);
                this.error = true;
            } finally {
                this.loading = false;
            }
        },

        async refreshData() {
            await this.fetchData();
        },

        getFunnelStageColor(index) {
            const colors = [
                'bg-blue-500',
                'bg-green-500', 
                'bg-yellow-500',
                'bg-orange-500',
                'bg-red-500'
            ];
            return colors[index] || 'bg-gray-500';
        },

        getFunnelBarColor(index) {
            const colors = [
                'bg-blue-400',
                'bg-green-400', 
                'bg-yellow-400',
                'bg-orange-400',
                'bg-red-400'
            ];
            return colors[index] || 'bg-gray-400';
        },

        getChannelBadgeColor(channel) {
            const colors = {
                'direct': 'bg-blue-100 text-blue-800',
                'organic': 'bg-green-100 text-green-800',
                'social': 'bg-purple-100 text-purple-800',
                'paid': 'bg-orange-100 text-orange-800'
            };
            return colors[channel] || 'bg-gray-100 text-gray-800';
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
