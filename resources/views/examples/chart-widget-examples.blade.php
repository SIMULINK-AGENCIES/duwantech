{{-- Chart Widget Examples --}}
@extends('admin.layouts.app')

@section('title', 'Chart Widget Examples')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Chart Widget Examples</h1>
        <p class="text-gray-600">Comprehensive examples of chart widgets with different types and configurations</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Line Chart Example --}}
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Line Chart - Sales Trend</h2>
            <x-admin.widgets.chart-widget
                title="Monthly Sales Trend"
                type="line"
                :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                :datasets="[
                    [
                        'label' => 'Sales 2024',
                        'data' => [12000, 19000, 15000, 25000, 22000, 30000],
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'Sales 2023',
                        'data' => [10000, 16000, 13000, 20000, 18000, 25000],
                        'tension' => 0.4
                    ]
                ]"
                height="350px"
                :showGrid="true"
                :showLegend="true"
                :zoomable="true"
                :exportable="true"
                realTimeEndpoint="/api/charts/sales-trend"
                :refreshInterval="60000"
            />
        </div>

        {{-- Bar Chart Example --}}
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Bar Chart - Revenue by Category</h2>
            <x-admin.widgets.chart-widget
                title="Revenue by Product Category"
                type="bar"
                :labels="['Electronics', 'Clothing', 'Books', 'Home & Garden', 'Sports']"
                :datasets="[
                    [
                        'label' => 'Q4 2024',
                        'data' => [45000, 32000, 18000, 25000, 12000]
                    ]
                ]"
                height="350px"
                :drillDown="true"
                drillDownEndpoint="/api/charts/category-details"
                :colors="['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']"
            />
        </div>

        {{-- Pie Chart Example --}}
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Pie Chart - Market Share</h2>
            <x-admin.widgets.chart-widget
                title="Market Share Distribution"
                type="pie"
                :labels="['Desktop', 'Mobile', 'Tablet', 'Smart TV']"
                :datasets="[
                    [
                        'label' => 'Market Share',
                        'data' => [45, 35, 15, 5]
                    ]
                ]"
                height="350px"
                :showLegend="true"
                :drillDown="true"
                drillDownEndpoint="/api/charts/device-details"
            />
        </div>

        {{-- Donut Chart Example --}}
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Donut Chart - Order Status</h2>
            <x-admin.widgets.chart-widget
                title="Order Status Distribution"
                type="donut"
                :labels="['Completed', 'Processing', 'Pending', 'Cancelled']"
                :datasets="[
                    [
                        'label' => 'Orders',
                        'data' => [850, 120, 45, 25]
                    ]
                ]"
                height="350px"
                :colors="['#10B981', '#F59E0B', '#6B7280', '#EF4444']"
                realTimeEndpoint="/api/charts/order-status"
                :refreshInterval="30000"
            />
        </div>

        {{-- Area Chart Example --}}
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Area Chart - Website Traffic</h2>
            <x-admin.widgets.chart-widget
                title="Website Traffic Overview"
                type="area"
                :labels="['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00']"
                :datasets="[
                    [
                        'label' => 'Page Views',
                        'data' => [2400, 1200, 3500, 4200, 3800, 2900, 2100],
                        'fill' => true
                    ],
                    [
                        'label' => 'Unique Visitors',
                        'data' => [1800, 900, 2800, 3200, 2900, 2200, 1600],
                        'fill' => true
                    ]
                ]"
                height="350px"
                :zoomable="true"
                :exportable="true"
                realTimeEndpoint="/api/charts/traffic"
                :refreshInterval="120000"
            />
        </div>

        {{-- Multi-dataset Bar Chart --}}
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Multi-Dataset Bar Chart - Performance Comparison</h2>
            <x-admin.widgets.chart-widget
                title="Quarterly Performance Comparison"
                type="bar"
                :labels="['Q1', 'Q2', 'Q3', 'Q4']"
                :datasets="[
                    [
                        'label' => 'Revenue ($k)',
                        'data' => [120, 150, 180, 220]
                    ],
                    [
                        'label' => 'Profit ($k)',
                        'data' => [45, 55, 70, 85]
                    ],
                    [
                        'label' => 'Expenses ($k)',
                        'data' => [75, 95, 110, 135]
                    ]
                ]"
                height="350px"
                :showGrid="true"
                :showLegend="true"
                :drillDown="true"
                drillDownEndpoint="/api/charts/quarterly-details"
            />
        </div>
    </div>

    {{-- Advanced Chart with Custom Options --}}
    <div class="mt-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Advanced Chart with Custom Options</h2>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <x-admin.widgets.chart-widget
                title="Advanced Analytics Dashboard"
                type="line"
                :labels="['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8']"
                :datasets="[
                    [
                        'label' => 'Conversions',
                        'data' => [120, 135, 145, 160, 155, 175, 180, 195],
                        'tension' => 0.4,
                        'pointRadius' => 6,
                        'pointHoverRadius' => 8
                    ],
                    [
                        'label' => 'Leads',
                        'data' => [800, 850, 920, 980, 950, 1020, 1080, 1150],
                        'tension' => 0.4,
                        'pointRadius' => 6,
                        'pointHoverRadius' => 8,
                        'yAxisID' => 'y1'
                    ]
                ]"
                height="400px"
                :showGrid="true"
                :showLegend="true"
                :zoomable="true"
                :exportable="true"
                realTimeEndpoint="/api/charts/advanced-analytics"
                :refreshInterval="45000"
                :drillDown="true"
                drillDownEndpoint="/api/charts/advanced-details"
                :customOptions="[
                    'scales' => [
                        'y' => [
                            'type' => 'linear',
                            'display' => true,
                            'position' => 'left',
                            'title' => [
                                'display' => true,
                                'text' => 'Conversions'
                            ]
                        ],
                        'y1' => [
                            'type' => 'linear',
                            'display' => true,
                            'position' => 'right',
                            'title' => [
                                'display' => true,
                                'text' => 'Leads'
                            ],
                            'grid' => [
                                'drawOnChartArea' => false
                            ]
                        ]
                    ]
                ]"
            />
        </div>
    </div>

    {{-- Dark Theme Example --}}
    <div class="mt-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dark Theme Chart</h2>
        <div class="bg-gray-900 rounded-lg shadow-lg p-6">
            <x-admin.widgets.chart-widget
                title="Dark Theme Analytics"
                type="bar"
                theme="dark"
                :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
                :datasets="[
                    [
                        'label' => 'Daily Active Users',
                        'data' => [1200, 1500, 1800, 1600, 2100, 2400, 1900]
                    ]
                ]"
                height="300px"
                :showGrid="true"
                :showLegend="true"
                :exportable="true"
            />
        </div>
    </div>

    {{-- API Endpoints Documentation --}}
    <div class="mt-12 bg-blue-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-blue-900 mb-4">API Endpoints for Real-time Data</h2>
        <div class="space-y-4 text-sm">
            <div>
                <h3 class="font-semibold text-blue-800">Sales Trend Endpoint</h3>
                <code class="bg-blue-100 px-2 py-1 rounded">GET /api/charts/sales-trend</code>
                <p class="text-blue-700 mt-1">Returns monthly sales data with labels and datasets</p>
            </div>
            <div>
                <h3 class="font-semibold text-blue-800">Category Details Drill-down</h3>
                <code class="bg-blue-100 px-2 py-1 rounded">GET /api/charts/category-details?label={label}&value={value}</code>
                <p class="text-blue-700 mt-1">Returns detailed breakdown for specific category</p>
            </div>
            <div>
                <h3 class="font-semibold text-blue-800">Order Status Real-time</h3>
                <code class="bg-blue-100 px-2 py-1 rounded">GET /api/charts/order-status</code>
                <p class="text-blue-700 mt-1">Returns current order status distribution</p>
            </div>
        </div>
    </div>

    {{-- Usage Examples Code --}}
    <div class="mt-12 bg-gray-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Usage Examples</h2>
        <div class="space-y-6">
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Simple Line Chart</h3>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm"><code>&lt;x-admin.widgets.chart-widget
    title="Monthly Sales"
    type="line"
    :labels="['Jan', 'Feb', 'Mar', 'Apr']"
    :datasets="[
        [
            'label' => 'Sales',
            'data' => [100, 200, 150, 300]
        ]
    ]"
    height="300px"
/&gt;</code></pre>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Interactive Bar Chart with Drill-down</h3>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm"><code>&lt;x-admin.widgets.chart-widget
    title="Category Revenue"
    type="bar"
    :labels="['Electronics', 'Clothing', 'Books']"
    :datasets="[
        [
            'label' => 'Revenue',
            'data' => [45000, 32000, 18000]
        ]
    ]"
    :drillDown="true"
    drillDownEndpoint="/api/charts/category-details"
    height="350px"
/&gt;</code></pre>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Real-time Pie Chart</h3>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm"><code>&lt;x-admin.widgets.chart-widget
    title="Market Share"
    type="pie"
    :labels="['Desktop', 'Mobile', 'Tablet']"
    :datasets="[
        [
            'label' => 'Share',
            'data' => [45, 35, 20]
        ]
    ]"
    realTimeEndpoint="/api/charts/market-share"
    :refreshInterval="30000"
    height="350px"
/&gt;</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection
