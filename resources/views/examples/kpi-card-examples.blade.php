{{-- KPI Card Widget Usage Examples --}}

{{-- Basic Revenue KPI Card --}}
<x-admin.widgets.kpi-card
    title="Total Revenue"
    value="125000"
    previous-value="110000"
    icon="dollar-sign"
    color="green"
    format="currency"
    :clickable="true"
    href="/admin/revenue"
    real-time-endpoint="/api/admin/kpi/revenue"
/>

{{-- Orders KPI Card --}}
<x-admin.widgets.kpi-card
    title="Total Orders"
    value="1247"
    previous-value="1098"
    icon="shopping-cart"
    color="blue"
    format="number"
    :clickable="true"
    href="/admin/orders"
    real-time-endpoint="/api/admin/kpi/orders"
/>

{{-- Users KPI Card --}}
<x-admin.widgets.kpi-card
    title="Active Users"
    value="8543"
    previous-value="7892"
    icon="users"
    color="purple"
    format="number"
    :clickable="true"
    href="/admin/users"
    real-time-endpoint="/api/admin/kpi/users"
/>

{{-- Conversion Rate KPI Card --}}
<x-admin.widgets.kpi-card
    title="Conversion Rate"
    value="3.42"
    previous-value="3.18"
    icon="chart-line"
    color="indigo"
    format="percentage"
    suffix="%"
    :clickable="true"
    href="/admin/analytics/conversions"
    real-time-endpoint="/api/admin/kpi/conversion-rate"
/>

{{-- Grid Layout Example --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Revenue Card --}}
    <x-admin.widgets.kpi-card
        title="Revenue"
        value="125000"
        previous-value="110000"
        icon="dollar-sign"
        color="green"
        format="currency"
        :clickable="true"
        href="/admin/revenue"
        real-time-endpoint="/api/admin/kpi/revenue"
        refresh-interval="15000"
    />
    
    {{-- Orders Card --}}
    <x-admin.widgets.kpi-card
        title="Orders"
        value="1247"
        previous-value="1098"
        icon="shopping-cart"
        color="blue"
        format="number"
        :clickable="true"
        href="/admin/orders"
        real-time-endpoint="/api/admin/kpi/orders"
    />
    
    {{-- Users Card --}}
    <x-admin.widgets.kpi-card
        title="Users"
        value="8543"
        previous-value="7892"
        icon="users"
        color="purple"
        format="number"
        :clickable="true"
        href="/admin/users"
        real-time-endpoint="/api/admin/kpi/users"
    />
    
    {{-- Conversion Card --}}
    <x-admin.widgets.kpi-card
        title="Conversion"
        value="3.42"
        previous-value="3.18"
        icon="percentage"
        color="yellow"
        format="percentage"
        suffix="%"
        :clickable="true"
        href="/admin/analytics"
        real-time-endpoint="/api/admin/kpi/conversion"
    />
</div>

{{-- Loading State Example --}}
<x-admin.widgets.kpi-card
    title="Loading Data"
    value="0"
    icon="spinner"
    color="gray"
    :loading="true"
/>

{{-- Large Size Example --}}
<x-admin.widgets.kpi-card
    title="Total Sales"
    value="2500000"
    previous-value="2200000"
    icon="chart-bar"
    color="green"
    format="currency"
    size="lg"
    :animated="true"
    real-time-endpoint="/api/admin/kpi/sales"
/>

{{-- Custom Styling Example --}}
<x-admin.widgets.kpi-card
    title="Custom KPI"
    value="99.5"
    previous-value="98.2"
    icon="star"
    color="yellow"
    format="decimal"
    suffix="%"
    custom-class="border-2 border-yellow-300 shadow-lg"
    :show-trend="true"
/>

{{-- Non-clickable Card --}}
<x-admin.widgets.kpi-card
    title="System Status"
    value="100"
    icon="server"
    color="green"
    format="percentage"
    suffix="%"
    :clickable="false"
    :show-trend="false"
/>

{{-- Small Size Cards in Row --}}
<div class="flex space-x-4">
    <x-admin.widgets.kpi-card
        title="Today"
        value="1250"
        icon="calendar-day"
        color="blue"
        format="currency"
        size="sm"
    />
    
    <x-admin.widgets.kpi-card
        title="This Week"
        value="8750"
        icon="calendar-week"
        color="green"
        format="currency"
        size="sm"
    />
    
    <x-admin.widgets.kpi-card
        title="This Month"
        value="35000"
        icon="calendar"
        color="purple"
        format="currency"
        size="sm"
    />
</div>
