<x-admin.layouts.master title="Dashboard">
    <x-slot name="breadcrumbs">
        @php
            $breadcrumbs = [
                ['title' => 'Dashboard', 'url' => route('admin.dashboard')]
            ];
        @endphp
    </x-slot>

    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Welcome back! Here's what's happening with your store today.
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button type="button" 
                    class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12"></path>
                </svg>
                Export Data
            </button>
            <button type="button" 
                    class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New
            </button>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenue Card -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">$12,345</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                +12.5%
                            </span>
                            from last month
                        </p>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">1,234</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                +8.2%
                            </span>
                            from last month
                        </p>
                    </div>
                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11h8"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Customers</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">5,678</p>
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 13.586V6a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                -2.1%
                            </span>
                            from last month
                        </p>
                    </div>
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Rate Card -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Conversion Rate</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">3.45%</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                +0.3%
                            </span>
                            from last month
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue Overview</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Monthly revenue for the past 12 months</p>
            </div>
            <div class="card-body">
                <div class="h-64 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                    <p class="text-gray-500 dark:text-gray-400">Chart placeholder - Revenue trend</p>
                </div>
            </div>
        </div>

        <!-- Sales Funnel -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sales Funnel</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Conversion stages breakdown</p>
            </div>
            <div class="card-body">
                <div class="h-64 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                    <p class="text-gray-500 dark:text-gray-400">Chart placeholder - Sales funnel</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h3>
                    <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600 dark:text-blue-400">#{{ str_pad($i, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Order #100{{ $i }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Customer {{ $i }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format(rand(50, 500), 2) }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $i % 3 == 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : ($i % 2 == 0 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400') }}">
                                    {{ $i % 3 == 0 ? 'Completed' : ($i % 2 == 0 ? 'Processing' : 'Pending') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Products</h3>
                    <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <img src="https://images.unsplash.com/photo-{{ 1500000000 + $i }}?w=40&h=40&fit=crop&crop=faces" 
                                     alt="Product {{ $i }}" 
                                     class="w-10 h-10 rounded-lg object-cover">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Product {{ $i }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ rand(10, 100) }} sales</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format(rand(20, 200), 2) }}</p>
                                <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-1">
                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ rand(30, 90) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Dashboard-specific JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any dashboard-specific functionality
            console.log('Dashboard loaded successfully');
            
            // Example: Initialize charts when dashboard loads
            // initializeCharts();
            
            // Example: Setup real-time updates
            // setupRealTimeUpdates();
        });
    </script>
    @endpush
</x-admin.layouts.master>
