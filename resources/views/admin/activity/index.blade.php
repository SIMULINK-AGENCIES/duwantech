<x-admin.layouts.master title="Activity Feed">
    <x-slot name="breadcrumbs">
        @php
            $breadcrumbs = [
                ['title' => 'Activity Feed', 'url' => route('admin.activity.index')]
            ];
        @endphp
    </x-slot>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Activity Feed</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Monitor real-time user activity and system events
            </p>
        </div>
        
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <!-- Export Button -->
            <button onclick="window.print()" 
                    class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Export
            </button>
            
            <!-- Back to Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z">
                    </path>
                </svg>
                Dashboard
            </a>
        </div>
    </div>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Activity Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Activities -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Today's Activities</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['today']['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- User Logins -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">User Logins</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['today']['logins'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- New Orders -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">New Orders</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['today']['orders'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Payments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Payments</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['today']['payments'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Activity Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Activity Feed (Main Content) -->
        <div class="lg:col-span-3">
            @include('admin.components.activity-feed', [
                'config' => [
                    'maxActivities' => 50,
                    'autoRefresh' => true,
                    'showFilters' => true,
                    'compact' => false
                ]
            ])
        </div>
        
        <!-- Activity Insights Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Top Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Actions (7 days)</h3>
                <div class="space-y-3">
                    @if(isset($stats['top_actions']) && count($stats['top_actions']) > 0)
                        @foreach($stats['top_actions'] as $action)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $action->action)) }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $action->count }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm">No activity data available</p>
                    @endif
                </div>
            </div>
            
            <!-- Weekly Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Weekly Summary</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Activities</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['this_week']['total'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Daily Average</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['this_week']['avg_per_day'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Activity Legend -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Types</h3>
                <div class="space-y-2">
                    @foreach($activityTypes as $key => $label)
                        @if($key !== 'all')
                            <div class="flex items-center space-x-2 text-sm">
                                <div class="w-3 h-3 rounded-full
                                    @switch($key)
                                        @case('login') bg-green-400 @break
                                        @case('logout') bg-gray-400 @break
                                        @case('registration') bg-blue-400 @break
                                        @case('order_created') bg-indigo-400 @break
                                        @case('payment_completed') bg-purple-400 @break
                                        @default bg-gray-300
                                    @endswitch">
                                </div>
                                <span class="text-gray-600">{{ $label }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-admin.layouts.master>
