@extends('admin.layout')

@section('title', 'Live Dashboard')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="{{ asset('css/animations.css') }}">
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    
    .widget-container {
        position: relative;
        overflow: hidden;
        animation: slideInUp 0.6s ease-out;
    }
    
    .widget-container:nth-child(1) { animation-delay: 0.1s; }
    .widget-container:nth-child(2) { animation-delay: 0.2s; }
    .widget-container:nth-child(3) { animation-delay: 0.3s; }
    .widget-container:nth-child(4) { animation-delay: 0.4s; }
    
    @media (min-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        
        .widget-large {
            grid-column: span 2;
            animation: slideInLeft 0.8s ease-out;
        }
        
        .widget-full {
            grid-column: span 4;
            animation: fadeInScale 1s ease-out;
        }
    }
    
    /* Enhanced widget hover effects */
    .widget-container:hover {
        transform: translateY(-2px);
        transition: transform 0.3s ease;
    }
    
    /* Staggered animation for dashboard sections */
    .stagger-animation > * {
        opacity: 0;
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    .stagger-animation > *:nth-child(1) { animation-delay: 0.1s; }
    .stagger-animation > *:nth-child(2) { animation-delay: 0.2s; }
    .stagger-animation > *:nth-child(3) { animation-delay: 0.3s; }
    .stagger-animation > *:nth-child(4) { animation-delay: 0.4s; }
    .stagger-animation > *:nth-child(5) { animation-delay: 0.5s; }
</style>
@endpush

@section('content')
<div x-data="liveDashboard()" x-init="init()" class="space-y-6 stagger-animation">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between slideInDown">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Live Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">Real-time insights and metrics</p>
        </div>
        
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <!-- Auto-refresh Toggle -->
            <button @click="toggleAutoRefresh()" 
                    :class="autoRefresh ? 'bg-green-100 text-green-700 scale-105' : 'bg-gray-100 text-gray-700'"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div :class="autoRefresh ? 'bg-green-400' : 'bg-gray-400'" 
                     class="w-2 h-2 rounded-full transition-all duration-300" 
                     :class="{ 'animate-pulse': autoRefresh }"></div>
                <span x-text="autoRefresh ? 'Live Updates On' : 'Live Updates Off'"></span>
            </button>
            
            <!-- Last Updated -->
            <div class="text-xs text-gray-500 fadeIn">
                Last updated: <span x-text="lastUpdated"></span>
            </div>
            
            <!-- Manual Refresh -->
            <button @click="refreshAll()" 
                    :disabled="loading"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200 hover:scale-110 hover:shadow-md">
                <i class="fas fa-sync-alt transition-transform duration-300" :class="{ 'animate-spin': loading }"></i>
            </button>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 stagger-animation">
        <!-- Total Orders Widget -->
        <div class="widget-container">
            <div x-data="{ 
                title: 'Total Orders',
                subtitle: 'Today vs Yesterday',
                currentValue: {{ $quickStats['orders']['today'] ?? 0 }},
                changePercentage: {{ $quickStats['orders']['change'] ?? 0 }},
                unit: '',
                iconClass: 'fas fa-shopping-cart',
                iconBgClass: 'bg-blue-100 text-blue-600',
                showChart: true,
                actions: [
                    { id: 'view', label: 'View All', icon: 'fas fa-eye', url: '{{ route('admin.orders.index') }}', primary: true }
                ]
            }"
                 x-init="Object.assign($data, $el.dataset)">
                @include('components.dashboard.widgets.live-metrics-card')
            </div>
        </div>

        <!-- Revenue Widget -->
        <div class="widget-container">
            <div x-data="{ 
                title: 'Revenue',
                subtitle: 'Today vs Yesterday',
                currentValue: {{ $quickStats['revenue']['today'] ?? 0 }},
                changePercentage: {{ $quickStats['revenue']['change'] ?? 0 }},
                unit: 'KES',
                iconClass: 'fas fa-dollar-sign',
                iconBgClass: 'bg-green-100 text-green-600',
                showChart: true,
                actions: [
                    { id: 'reports', label: 'Reports', icon: 'fas fa-chart-line', url: '{{ route('admin.reports.revenue') }}', primary: true }
                ]
            }">
                @include('components.dashboard.widgets.live-metrics-card')
            </div>
        </div>

        <!-- New Users Widget -->
        <div class="widget-container">
            <div x-data="{ 
                title: 'New Users',
                subtitle: 'Today vs Yesterday',
                currentValue: {{ $quickStats['users']['today'] ?? 0 }},
                changePercentage: {{ $quickStats['users']['change'] ?? 0 }},
                unit: '',
                iconClass: 'fas fa-users',
                iconBgClass: 'bg-purple-100 text-purple-600',
                showChart: true,
                actions: [
                    { id: 'manage', label: 'Manage', icon: 'fas fa-user-cog', url: '{{ route('admin.users.index') }}', primary: true }
                ]
            }">
                @include('components.dashboard.widgets.live-metrics-card')
            </div>
        </div>

        <!-- System Alerts Widget -->
        <div class="widget-container">
            <div x-data="{ 
                title: 'System Alerts',
                subtitle: 'Unread notifications',
                currentValue: {{ $quickStats['alerts']['total'] ?? 0 }},
                changePercentage: 0,
                unit: '',
                iconClass: 'fas fa-bell',
                iconBgClass: 'bg-red-100 text-red-600',
                showChart: false,
                actions: [
                    { id: 'view', label: 'View All', icon: 'fas fa-bell', url: '{{ route('admin.notifications.index') }}', primary: true }
                ]
            }">
                @include('components.dashboard.widgets.live-metrics-card')
            </div>
        </div>
    </div>

    <!-- Charts and Geographic Map Row -->
    <div class="dashboard-grid">
        <!-- Sales Chart -->
        <div class="widget-large">
            <div x-data="{ 
                title: 'Sales Overview',
                subtitle: 'Revenue trends over time',
                chartType: 'line',
                dataEndpoint: '/api/dashboard/chart-data?type=sales',
                actions: [
                    { id: 'export', label: 'Export', icon: 'fas fa-download', primary: false },
                    { id: 'fullscreen', label: 'Fullscreen', icon: 'fas fa-expand', primary: true }
                ]
            }">
                @include('components.dashboard.widgets.real-time-chart')
            </div>
        </div>

        <!-- World Map Widget -->
        <div class="widget-large">
            @include('components.dashboard.widgets.world-map')
        </div>
    </div>

    <!-- Additional Charts Row -->
    <div class="dashboard-grid">
        <!-- Orders Chart -->
        <div class="widget-large">
            <div x-data="{ 
                title: 'Orders Overview',
                subtitle: 'Order volume trends',
                chartType: 'bar',
                dataEndpoint: '/api/dashboard/chart-data?type=orders',
                actions: [
                    { id: 'details', label: 'View Details', icon: 'fas fa-list', primary: true }
                ]
            }">
                @include('components.dashboard.widgets.real-time-chart')
            </div>
        </div>

        <!-- System Status Widget -->
        <div class="widget-large">
            @include('components.dashboard.widgets.system-status')
        </div>
    </div>

    <!-- Activity Feed Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">        
        <!-- Activity Feed Widget -->
        <div class="relative">
            @include('components.dashboard.widgets.activity-feed')
        </div>
        
        <!-- Quick Actions Widget -->
        <div class="relative">
            @include('components.dashboard.widgets.quick-actions')
        </div>
    </div>

    <!-- Advanced Analytics Row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <!-- Sales Metrics Widget -->
        <div class="relative">
            @include('components.dashboard.widgets.sales-metrics')
        </div>
        
        <!-- Conversion Tracking Widget -->
        <div class="relative">
            @include('components.dashboard.widgets.conversion-tracking')
        </div>
    </div>

    <!-- Performance and Trends Row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <!-- Performance Indicators Widget -->
        <div class="relative">
            @include('components.dashboard.widgets.performance-indicators')
        </div>
        
        <!-- Trend Analysis Widget -->
        <div class="relative">
            @include('components.dashboard.widgets.trend-analysis')
        </div>
    </div>

    <!-- Recent Data Tables -->
    <div class="dashboard-grid">
        <!-- Recent Orders -->
        <div class="widget-large bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                    <p class="text-sm text-gray-500">Latest customer orders</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">#{{ $order->order_number }}</div>
                                <div class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                KES {{ number_format($order->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Products -->
        <div class="widget-large bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Top Products</h3>
                    <p class="text-sm text-gray-500">Best performing products</p>
                </div>
                <a href="{{ route('admin.products.index') }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All
                </a>
            </div>
            
            <div class="space-y-4">
                @foreach($topProducts as $product)
                <div class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50">
                    <div class="flex-shrink-0">
                        @if($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                                 class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-gray-400"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                        <p class="text-sm text-gray-500">{{ $product->orders_count }} orders</p>
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                        KES {{ number_format($product->price, 2) }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function liveDashboard() {
    return {
        autoRefresh: true,
        loading: false,
        lastUpdated: new Date().toLocaleTimeString(),
        refreshInterval: 60000, // 1 minute
        refreshTimer: null,
        
        init() {
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
                this.refreshAll();
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
        
        async refreshAll() {
            this.loading = true;
            
            try {
                // Trigger refresh on all widgets
                window.dispatchEvent(new CustomEvent('dashboard-refresh'));
                
                // Update timestamp
                this.lastUpdated = new Date().toLocaleTimeString();
            } catch (error) {
                console.error('Failed to refresh dashboard:', error);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection 