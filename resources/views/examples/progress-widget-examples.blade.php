{{-- Progress Widget Examples --}}
@extends('admin.layouts.app')

@section('title', 'Progress Widget Examples')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Progress Widget Examples</h1>
        <p class="text-gray-600">Comprehensive examples of progress widgets with goal tracking, progress bars, and achievement indicators</p>
    </div>

    {{-- Basic Progress Bars --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Basic Progress Bars</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Sales Goal -->
            <x-admin.widgets.progress-widget
                title="Monthly Sales Goal"
                type="bar"
                :value="75000"
                :max="100000"
                :target="85000"
                format="currency"
                color="green"
                icon="chart-line"
                description="Track monthly sales performance"
                :animated="true"
                :showTarget="true"
                :trend="12.5"
                trendPeriod="vs last month"
            />
            
            <!-- Project Completion -->
            <x-admin.widgets.progress-widget
                title="Project Completion"
                type="bar"
                :value="68"
                :max="100"
                format="percentage"
                color="blue"
                icon="tasks"
                description="Overall project progress"
                :striped="true"
                :animated="true"
                :milestones="[
                    ['value' => 25, 'label' => 'Planning', 'color' => 'bg-blue-500'],
                    ['value' => 50, 'label' => 'Development', 'color' => 'bg-yellow-500'],
                    ['value' => 75, 'label' => 'Testing', 'color' => 'bg-orange-500'],
                    ['value' => 100, 'label' => 'Deployment', 'color' => 'bg-green-500']
                ]"
            />
            
            <!-- Customer Satisfaction -->
            <x-admin.widgets.progress-widget
                title="Customer Satisfaction"
                type="bar"
                :value="4.2"
                :max="5"
                format="number"
                unit="★"
                color="yellow"
                icon="star"
                description="Average customer rating"
                :gradient="true"
                :trend="-2.1"
                trendPeriod="this week"
            />
        </div>
    </div>

    {{-- Circular Progress Indicators --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Circular Progress Indicators</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- CPU Usage -->
            <x-admin.widgets.progress-widget
                title="CPU Usage"
                type="circle"
                :value="45"
                :max="100"
                format="percentage"
                color="blue"
                icon="microchip"
                size="md"
                :animated="true"
                realTimeEndpoint="/api/widgets/cpu-usage"
                :refreshInterval="5000"
            />
            
            <!-- Memory Usage -->
            <x-admin.widgets.progress-widget
                title="Memory Usage"
                type="circle"
                :value="78"
                :max="100"
                format="percentage"
                color="green"
                icon="memory"
                :animated="true"
                realTimeEndpoint="/api/widgets/memory-usage"
                :refreshInterval="5000"
            />
            
            <!-- Disk Space -->
            <x-admin.widgets.progress-widget
                title="Disk Space"
                type="circle"
                :value="156"
                :max="500"
                format="number"
                unit="GB"
                color="purple"
                icon="hdd"
                :animated="true"
            />
            
            <!-- Battery Level -->
            <x-admin.widgets.progress-widget
                title="Battery Level"
                type="semicircle"
                :value="87"
                :max="100"
                format="percentage"
                color="green"
                icon="battery-three-quarters"
                :animated="true"
            />
        </div>
    </div>

    {{-- Goal Tracking with Milestones --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Goal Tracking with Milestones</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Revenue Goal -->
            <x-admin.widgets.progress-widget
                title="Annual Revenue Goal"
                type="bar"
                :value="850000"
                :max="1200000"
                :target="1000000"
                format="currency"
                color="green"
                icon="dollar-sign"
                description="Track annual revenue progress"
                size="lg"
                :animated="true"
                :gradient="true"
                :milestones="[
                    ['value' => 300000, 'label' => 'Q1 Target', 'color' => 'bg-blue-500'],
                    ['value' => 600000, 'label' => 'Q2 Target', 'color' => 'bg-yellow-500'],
                    ['value' => 900000, 'label' => 'Q3 Target', 'color' => 'bg-orange-500'],
                    ['value' => 1200000, 'label' => 'Annual Goal', 'color' => 'bg-green-500']
                ]"
                :trend="8.3"
                trendPeriod="vs last quarter"
            />
            
            <!-- User Growth -->
            <x-admin.widgets.progress-widget
                title="User Growth Target"
                type="circle"
                :value="12500"
                :max="20000"
                :target="15000"
                format="number"
                unit="users"
                color="blue"
                icon="users"
                description="Monthly active users"
                size="lg"
                :animated="true"
                :milestones="[
                    ['value' => 5000, 'label' => 'Early Adopters', 'color' => 'bg-gray-500'],
                    ['value' => 10000, 'label' => 'Growth Phase', 'color' => 'bg-blue-500'],
                    ['value' => 15000, 'label' => 'Target Reached', 'color' => 'bg-green-500'],
                    ['value' => 20000, 'label' => 'Stretch Goal', 'color' => 'bg-purple-500']
                ]"
                realTimeEndpoint="/api/widgets/user-growth"
                :refreshInterval="30000"
            />
        </div>
    </div>

    {{-- Achievement Systems --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Achievement Systems</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Sales Achievements -->
            <x-admin.widgets.progress-widget
                title="Sales Performance"
                type="bar"
                :value="245000"
                :max="500000"
                format="currency"
                color="green"
                icon="trophy"
                description="Unlock achievements as you hit sales milestones"
                size="lg"
                :animated="true"
                :striped="true"
                :achievements="[
                    ['label' => 'First Sale', 'icon' => 'star', 'unlocked' => true],
                    ['label' => 'Rising Star', 'icon' => 'arrow-up', 'requirement' => 100000, 'unlocked' => true],
                    ['label' => 'Sales Champion', 'icon' => 'medal', 'requirement' => 250000, 'unlocked' => false],
                    ['label' => 'Legend', 'icon' => 'crown', 'requirement' => 500000, 'unlocked' => false]
                ]"
                :milestones="[
                    ['value' => 100000, 'label' => 'Bronze', 'color' => 'bg-yellow-600'],
                    ['value' => 250000, 'label' => 'Silver', 'color' => 'bg-gray-400'],
                    ['value' => 500000, 'label' => 'Gold', 'color' => 'bg-yellow-500']
                ]"
            />
            
            <!-- Learning Progress -->
            <x-admin.widgets.progress-widget
                title="Learning Progress"
                type="stepped"
                :value="7"
                :max="10"
                format="number"
                unit="lessons"
                color="purple"
                icon="graduation-cap"
                description="Complete lessons to unlock achievements"
                size="lg"
                :animated="true"
                :achievements="[
                    ['label' => 'Getting Started', 'icon' => 'play', 'unlocked' => true],
                    ['label' => 'Quick Learner', 'icon' => 'bolt', 'requirement' => 5, 'unlocked' => true],
                    ['label' => 'Almost There', 'icon' => 'clock', 'requirement' => 8, 'unlocked' => false],
                    ['label' => 'Course Master', 'icon' => 'graduation-cap', 'requirement' => 10, 'unlocked' => false]
                ]"
            />
        </div>
    </div>

    {{-- Real-time Progress Indicators --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Real-time Progress Indicators</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Server Load -->
            <x-admin.widgets.progress-widget
                title="Server Load"
                type="line"
                :value="42"
                :max="100"
                format="percentage"
                color="red"
                icon="server"
                :animated="true"
                realTimeEndpoint="/api/widgets/server-load"
                :refreshInterval="3000"
            />
            
            <!-- Active Users -->
            <x-admin.widgets.progress-widget
                title="Active Users"
                type="circle"
                :value="1247"
                :max="2000"
                format="number"
                color="blue"
                icon="user-friends"
                :animated="true"
                realTimeEndpoint="/api/widgets/active-users"
                :refreshInterval="10000"
            />
            
            <!-- Queue Processing -->
            <x-admin.widgets.progress-widget
                title="Queue Processing"
                type="bar"
                :value="156"
                :max="200"
                format="number"
                unit="jobs"
                color="yellow"
                icon="list"
                :animated="true"
                :striped="true"
                realTimeEndpoint="/api/widgets/queue-status"
                :refreshInterval="5000"
            />
            
            <!-- Download Progress -->
            <x-admin.widgets.progress-widget
                title="Download Progress"
                type="line"
                :value="67"
                :max="100"
                format="percentage"
                color="green"
                icon="download"
                :animated="true"
                realTimeEndpoint="/api/widgets/download-progress"
                :refreshInterval="1000"
            />
        </div>
    </div>

    {{-- Custom Styled Progress Widgets --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Custom Styled Progress Widgets</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Health Score -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6">
                <x-admin.widgets.progress-widget
                    title="Health Score"
                    type="circle"
                    :value="85"
                    :max="100"
                    format="percentage"
                    color="green"
                    icon="heartbeat"
                    description="System health indicator"
                    :animated="true"
                    :gradient="true"
                    customClass="bg-transparent shadow-none border-0"
                />
            </div>
            
            <!-- Performance Index -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6">
                <x-admin.widgets.progress-widget
                    title="Performance Index"
                    type="semicircle"
                    :value="92"
                    :max="100"
                    format="percentage"
                    color="blue"
                    icon="tachometer-alt"
                    description="Overall performance score"
                    :animated="true"
                    customClass="bg-transparent shadow-none border-0"
                />
            </div>
            
            <!-- Efficiency Rating -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6">
                <x-admin.widgets.progress-widget
                    title="Efficiency Rating"
                    type="bar"
                    :value="4.7"
                    :max="5"
                    format="number"
                    unit="★"
                    color="purple"
                    icon="star"
                    description="Team efficiency score"
                    :animated="true"
                    :gradient="true"
                    customClass="bg-transparent shadow-none border-0"
                />
            </div>
        </div>
    </div>

    {{-- Dark Theme Progress Widgets --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dark Theme Progress Widgets</h2>
        <div class="bg-gray-900 rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Network Usage -->
                <x-admin.widgets.progress-widget
                    title="Network Usage"
                    type="bar"
                    theme="dark"
                    :value="234"
                    :max="1000"
                    format="number"
                    unit="Mbps"
                    color="blue"
                    icon="wifi"
                    :animated="true"
                    :striped="true"
                />
                
                <!-- Storage Used -->
                <x-admin.widgets.progress-widget
                    title="Storage Used"
                    type="circle"
                    theme="dark"
                    :value="67"
                    :max="100"
                    format="percentage"
                    color="red"
                    icon="database"
                    :animated="true"
                />
                
                <!-- Backup Progress -->
                <x-admin.widgets.progress-widget
                    title="Backup Progress"
                    type="stepped"
                    theme="dark"
                    :value="8"
                    :max="12"
                    format="number"
                    unit="files"
                    color="green"
                    icon="cloud-upload-alt"
                    :animated="true"
                />
            </div>
        </div>
    </div>

    {{-- API Integration Documentation --}}
    <div class="bg-blue-50 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-blue-900 mb-4">API Endpoints for Real-time Data</h2>
        <div class="space-y-4 text-sm">
            <div>
                <h3 class="font-semibold text-blue-800">System Metrics</h3>
                <code class="bg-blue-100 px-2 py-1 rounded">GET /api/widgets/cpu-usage</code>
                <p class="text-blue-700 mt-1">Returns current CPU usage percentage</p>
            </div>
            <div>
                <h3 class="font-semibold text-blue-800">User Analytics</h3>
                <code class="bg-blue-100 px-2 py-1 rounded">GET /api/widgets/active-users</code>
                <p class="text-blue-700 mt-1">Returns current active user count</p>
            </div>
            <div>
                <h3 class="font-semibold text-blue-800">Sales Progress</h3>
                <code class="bg-blue-100 px-2 py-1 rounded">GET /api/widgets/sales-progress</code>
                <p class="text-blue-700 mt-1">Returns sales data with targets and milestones</p>
            </div>
        </div>
    </div>

    {{-- Usage Examples Code --}}
    <div class="bg-gray-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Usage Examples</h2>
        <div class="space-y-6">
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Basic Progress Bar</h3>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm"><code>&lt;x-admin.widgets.progress-widget
    title="Sales Goal"
    type="bar"
    :value="75000"
    :max="100000"
    format="currency"
    color="green"
    icon="chart-line"
    :animated="true"
/&gt;</code></pre>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Circular Progress with Real-time Updates</h3>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm"><code>&lt;x-admin.widgets.progress-widget
    title="CPU Usage"
    type="circle"
    :value="45"
    :max="100"
    format="percentage"
    color="blue"
    icon="microchip"
    realTimeEndpoint="/api/widgets/cpu-usage"
    :refreshInterval="5000"
/&gt;</code></pre>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Progress with Milestones and Achievements</h3>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm"><code>&lt;x-admin.widgets.progress-widget
    title="Learning Progress"
    type="bar"
    :value="7"
    :max="10"
    format="number"
    unit="lessons"
    color="purple"
    :milestones="[
        ['value' => 5, 'label' => 'Halfway', 'color' => 'bg-yellow-500'],
        ['value' => 10, 'label' => 'Complete', 'color' => 'bg-green-500']
    ]"
    :achievements="[
        ['label' => 'Quick Learner', 'icon' => 'bolt', 'unlocked' => true],
        ['label' => 'Course Master', 'icon' => 'graduation-cap', 'unlocked' => false]
    ]"
/&gt;</code></pre>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Stepped Progress Indicator</h3>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm"><code>&lt;x-admin.widgets.progress-widget
    title="Project Stages"
    type="stepped"
    :value="6"
    :max="10"
    format="number"
    unit="stages"
    color="indigo"
    :animated="true"
/&gt;</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection
