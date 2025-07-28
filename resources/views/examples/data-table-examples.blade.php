{{-- Data Table Widget Examples --}}
@extends('admin.layouts.app')

@section('title', 'Data Table Widget Examples')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Data Table Widget Examples</h1>
        <p class="text-gray-600">Comprehensive examples of data table widgets with sorting, filtering, and pagination features</p>
    </div>

    {{-- Basic Data Table --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Basic Data Table - User Management</h2>
        <x-admin.widgets.data-table
            title="User Management"
            :data="[
                ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin', 'status' => 'Active', 'created_at' => '2024-01-15', 'last_login' => '2024-07-28 10:30:00'],
                ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'Manager', 'status' => 'Active', 'created_at' => '2024-02-20', 'last_login' => '2024-07-27 15:45:00'],
                ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'role' => 'User', 'status' => 'Inactive', 'created_at' => '2024-03-10', 'last_login' => '2024-07-25 09:15:00'],
                ['id' => 4, 'name' => 'Alice Brown', 'email' => 'alice@example.com', 'role' => 'Editor', 'status' => 'Active', 'created_at' => '2024-04-05', 'last_login' => '2024-07-28 14:20:00'],
                ['id' => 5, 'name' => 'Charlie Wilson', 'email' => 'charlie@example.com', 'role' => 'User', 'status' => 'Pending', 'created_at' => '2024-05-12', 'last_login' => null]
            ]"
            :columns="[
                ['key' => 'id', 'label' => 'ID', 'width' => '60px', 'align' => 'center'],
                ['key' => 'name', 'label' => 'Name', 'sortable' => true],
                ['key' => 'email', 'label' => 'Email', 'sortable' => true],
                ['key' => 'role', 'label' => 'Role', 'type' => 'badge', 'badgeClass' => [
                    'Admin' => 'bg-red-100 text-red-800',
                    'Manager' => 'bg-blue-100 text-blue-800',
                    'Editor' => 'bg-green-100 text-green-800',
                    'User' => 'bg-gray-100 text-gray-800'
                ]],
                ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'badgeClass' => [
                    'Active' => 'bg-green-100 text-green-800',
                    'Inactive' => 'bg-red-100 text-red-800',
                    'Pending' => 'bg-yellow-100 text-yellow-800'
                ]],
                ['key' => 'created_at', 'label' => 'Created', 'type' => 'date', 'sortable' => true],
                ['key' => 'last_login', 'label' => 'Last Login', 'type' => 'datetime']
            ]"
            :rowActions="[
                ['key' => 'edit', 'label' => 'Edit', 'icon' => 'edit', 'class' => 'text-blue-600 hover:text-blue-800'],
                ['key' => 'delete', 'label' => 'Delete', 'icon' => 'trash', 'class' => 'text-red-600 hover:text-red-800']
            ]"
            :sortable="true"
            :filterable="true"
            :searchable="true"
            :paginated="true"
            :perPage="3"
            :exportable="true"
        />
    </div>

    {{-- Advanced Data Table with Bulk Actions --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Advanced Data Table - Product Inventory</h2>
        <x-admin.widgets.data-table
            title="Product Inventory"
            :data="[
                ['id' => 1, 'sku' => 'LAPTOP-001', 'name' => 'Gaming Laptop Pro', 'category' => 'Electronics', 'price' => 1299.99, 'stock' => 25, 'status' => 'In Stock', 'rating' => 4.5, 'sales' => 145],
                ['id' => 2, 'sku' => 'PHONE-002', 'name' => 'Smartphone X', 'category' => 'Electronics', 'price' => 899.99, 'stock' => 0, 'status' => 'Out of Stock', 'rating' => 4.2, 'sales' => 89],
                ['id' => 3, 'sku' => 'BOOK-003', 'name' => 'Programming Guide', 'category' => 'Books', 'price' => 49.99, 'stock' => 120, 'status' => 'In Stock', 'rating' => 4.8, 'sales' => 234],
                ['id' => 4, 'sku' => 'SHIRT-004', 'name' => 'Cotton T-Shirt', 'category' => 'Clothing', 'price' => 24.99, 'stock' => 5, 'status' => 'Low Stock', 'rating' => 4.1, 'sales' => 567],
                ['id' => 5, 'sku' => 'WATCH-005', 'name' => 'Smart Watch', 'category' => 'Electronics', 'price' => 299.99, 'stock' => 45, 'status' => 'In Stock', 'rating' => 4.3, 'sales' => 78],
                ['id' => 6, 'sku' => 'CHAIR-006', 'name' => 'Office Chair', 'category' => 'Furniture', 'price' => 199.99, 'stock' => 12, 'status' => 'In Stock', 'rating' => 4.0, 'sales' => 43],
                ['id' => 7, 'sku' => 'MOUSE-007', 'name' => 'Wireless Mouse', 'category' => 'Electronics', 'price' => 39.99, 'stock' => 85, 'status' => 'In Stock', 'rating' => 4.4, 'sales' => 312]
            ]"
            :columns="[
                ['key' => 'sku', 'label' => 'SKU', 'width' => '100px'],
                ['key' => 'name', 'label' => 'Product Name', 'sortable' => true],
                ['key' => 'category', 'label' => 'Category', 'type' => 'select', 'filterable' => true, 'options' => [
                    'Electronics' => 'Electronics',
                    'Books' => 'Books',
                    'Clothing' => 'Clothing',
                    'Furniture' => 'Furniture'
                ]],
                ['key' => 'price', 'label' => 'Price', 'type' => 'currency', 'sortable' => true, 'align' => 'right'],
                ['key' => 'stock', 'label' => 'Stock', 'type' => 'number', 'sortable' => true, 'align' => 'center'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'badgeClass' => [
                    'In Stock' => 'bg-green-100 text-green-800',
                    'Out of Stock' => 'bg-red-100 text-red-800',
                    'Low Stock' => 'bg-yellow-100 text-yellow-800'
                ]],
                ['key' => 'rating', 'label' => 'Rating', 'type' => 'decimal', 'sortable' => true, 'align' => 'center'],
                ['key' => 'sales', 'label' => 'Sales', 'type' => 'number', 'sortable' => true, 'align' => 'right']
            ]"
            :rowActions="[
                ['key' => 'view', 'label' => 'View', 'icon' => 'eye', 'class' => 'text-gray-600 hover:text-gray-800'],
                ['key' => 'edit', 'label' => 'Edit', 'icon' => 'edit', 'class' => 'text-blue-600 hover:text-blue-800'],
                ['key' => 'duplicate', 'label' => 'Duplicate', 'icon' => 'copy', 'class' => 'text-green-600 hover:text-green-800'],
                ['key' => 'delete', 'label' => 'Delete', 'icon' => 'trash', 'class' => 'text-red-600 hover:text-red-800']
            ]"
            :bulkActions="[
                ['key' => 'activate', 'label' => 'Activate', 'icon' => 'check', 'class' => 'bg-green-600 text-white hover:bg-green-700'],
                ['key' => 'deactivate', 'label' => 'Deactivate', 'icon' => 'times', 'class' => 'bg-yellow-600 text-white hover:bg-yellow-700'],
                ['key' => 'delete', 'label' => 'Delete', 'icon' => 'trash', 'class' => 'bg-red-600 text-white hover:bg-red-700']
            ]"
            :selectable="true"
            :showBulkActions="true"
            :showColumnFilters="true"
            :perPage="5"
            :exportable="true"
            realTimeEndpoint="/api/tables/products"
            :refreshInterval="60000"
        />
    </div>

    {{-- Compact Data Table --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Compact Data Table - Order History</h2>
        <x-admin.widgets.data-table
            title="Recent Orders"
            :data="[
                ['id' => 'ORD-001', 'customer' => 'John Doe', 'product' => 'Gaming Laptop', 'amount' => 1299.99, 'status' => 'Completed', 'date' => '2024-07-28'],
                ['id' => 'ORD-002', 'customer' => 'Jane Smith', 'product' => 'Smartphone', 'amount' => 899.99, 'status' => 'Processing', 'date' => '2024-07-27'],
                ['id' => 'ORD-003', 'customer' => 'Bob Johnson', 'product' => 'Book Set', 'amount' => 149.99, 'status' => 'Shipped', 'date' => '2024-07-26'],
                ['id' => 'ORD-004', 'customer' => 'Alice Brown', 'product' => 'Office Chair', 'amount' => 199.99, 'status' => 'Cancelled', 'date' => '2024-07-25']
            ]"
            :columns="[
                ['key' => 'id', 'label' => 'Order ID', 'width' => '100px'],
                ['key' => 'customer', 'label' => 'Customer'],
                ['key' => 'product', 'label' => 'Product'],
                ['key' => 'amount', 'label' => 'Amount', 'type' => 'currency', 'align' => 'right'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'badgeClass' => [
                    'Completed' => 'bg-green-100 text-green-800',
                    'Processing' => 'bg-blue-100 text-blue-800',
                    'Shipped' => 'bg-purple-100 text-purple-800',
                    'Cancelled' => 'bg-red-100 text-red-800'
                ]],
                ['key' => 'date', 'label' => 'Date', 'type' => 'date']
            ]"
            :compact="true"
            :striped="true"
            :hover="true"
            :perPage="10"
            :showPerPageSelector="false"
        />
    </div>

    {{-- Real-time Data Table --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Real-time Data Table - Live Analytics</h2>
        <x-admin.widgets.data-table
            title="Live Website Analytics"
            :data="[
                ['page' => '/dashboard', 'views' => 1245, 'unique_visitors' => 892, 'bounce_rate' => 23.5, 'avg_time' => '02:15', 'conversions' => 45],
                ['page' => '/products', 'views' => 2890, 'unique_visitors' => 1654, 'bounce_rate' => 18.2, 'avg_time' => '03:42', 'conversions' => 127],
                ['page' => '/checkout', 'views' => 567, 'unique_visitors' => 432, 'bounce_rate' => 12.8, 'avg_time' => '01:55', 'conversions' => 89],
                ['page' => '/contact', 'views' => 334, 'unique_visitors' => 298, 'bounce_rate' => 45.3, 'avg_time' => '01:23', 'conversions' => 12]
            ]"
            :columns="[
                ['key' => 'page', 'label' => 'Page', 'sortable' => true],
                ['key' => 'views', 'label' => 'Page Views', 'type' => 'number', 'sortable' => true, 'align' => 'right'],
                ['key' => 'unique_visitors', 'label' => 'Unique Visitors', 'type' => 'number', 'sortable' => true, 'align' => 'right'],
                ['key' => 'bounce_rate', 'label' => 'Bounce Rate', 'type' => 'percentage', 'sortable' => true, 'align' => 'right'],
                ['key' => 'avg_time', 'label' => 'Avg. Time', 'align' => 'center'],
                ['key' => 'conversions', 'label' => 'Conversions', 'type' => 'number', 'sortable' => true, 'align' => 'right']
            ]"
            realTimeEndpoint="/api/tables/analytics"
            :refreshInterval="15000"
            :exportable="true"
            :perPage="10"
        />
    </div>

    {{-- Dark Theme Data Table --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dark Theme Data Table</h2>
        <div class="bg-gray-900 rounded-lg p-6">
            <x-admin.widgets.data-table
                title="Server Monitoring"
                theme="dark"
                :data="[
                    ['server' => 'web-01', 'status' => 'Online', 'cpu' => 45.2, 'memory' => 78.5, 'disk' => 23.1, 'uptime' => '15 days'],
                    ['server' => 'web-02', 'status' => 'Online', 'cpu' => 52.8, 'memory' => 65.3, 'disk' => 41.7, 'uptime' => '22 days'],
                    ['server' => 'db-01', 'status' => 'Warning', 'cpu' => 89.3, 'memory' => 91.2, 'disk' => 67.4, 'uptime' => '8 days'],
                    ['server' => 'cache-01', 'status' => 'Offline', 'cpu' => 0.0, 'memory' => 0.0, 'disk' => 0.0, 'uptime' => '0 days']
                ]"
                :columns="[
                    ['key' => 'server', 'label' => 'Server', 'sortable' => true],
                    ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'badgeClass' => [
                        'Online' => 'bg-green-100 text-green-800',
                        'Warning' => 'bg-yellow-100 text-yellow-800',
                        'Offline' => 'bg-red-100 text-red-800'
                    ]],
                    ['key' => 'cpu', 'label' => 'CPU %', 'type' => 'percentage', 'sortable' => true, 'align' => 'right'],
                    ['key' => 'memory', 'label' => 'Memory %', 'type' => 'percentage', 'sortable' => true, 'align' => 'right'],
                    ['key' => 'disk', 'label' => 'Disk %', 'type' => 'percentage', 'sortable' => true, 'align' => 'right'],
                    ['key' => 'uptime', 'label' => 'Uptime', 'align' => 'center']
                ]"
                :rowActions="[
                    ['key' => 'restart', 'label' => 'Restart', 'icon' => 'redo', 'class' => 'text-yellow-400 hover:text-yellow-300'],
                    ['key' => 'logs', 'label' => 'Logs', 'icon' => 'file-alt', 'class' => 'text-blue-400 hover:text-blue-300']
                ]"
                :perPage="5"
                realTimeEndpoint="/api/tables/servers"
                :refreshInterval="10000"
            />
        </div>
    </div>

    {{-- Custom Formatted Data Table --}}
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Custom Formatted Data Table - Financial Report</h2>
        <x-admin.widgets.data-table
            title="Monthly Financial Report"
            :data="[
                ['month' => 'January', 'revenue' => 125000, 'expenses' => 87500, 'profit' => 37500, 'growth' => 12.5, 'customers' => 1250],
                ['month' => 'February', 'revenue' => 142000, 'expenses' => 89200, 'profit' => 52800, 'growth' => 15.8, 'customers' => 1420],
                ['month' => 'March', 'revenue' => 158000, 'expenses' => 92400, 'profit' => 65600, 'growth' => 18.2, 'customers' => 1580],
                ['month' => 'April', 'revenue' => 134000, 'expenses' => 95100, 'profit' => 38900, 'growth' => -8.5, 'customers' => 1340],
                ['month' => 'May', 'revenue' => 167000, 'expenses' => 96800, 'profit' => 70200, 'growth' => 22.1, 'customers' => 1670],
                ['month' => 'June', 'revenue' => 189000, 'expenses' => 98500, 'profit' => 90500, 'growth' => 26.4, 'customers' => 1890]
            ]"
            :columns="[
                ['key' => 'month', 'label' => 'Month', 'sortable' => true],
                ['key' => 'revenue', 'label' => 'Revenue', 'type' => 'currency', 'sortable' => true, 'align' => 'right'],
                ['key' => 'expenses', 'label' => 'Expenses', 'type' => 'currency', 'sortable' => true, 'align' => 'right'],
                ['key' => 'profit', 'label' => 'Profit', 'type' => 'currency', 'sortable' => true, 'align' => 'right'],
                ['key' => 'growth', 'label' => 'Growth', 'type' => 'percentage', 'sortable' => true, 'align' => 'right'],
                ['key' => 'customers', 'label' => 'Customers', 'type' => 'number', 'sortable' => true, 'align' => 'right']
            ]"
            :exportable="true"
            :perPage="12"
            :showPerPageSelector="false"
            :bordered="true"
        />
    </div>

    {{-- API Integration Documentation --}}
    <div class="bg-blue-50 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-blue-900 mb-4">API Endpoints for Real-time Data</h2>
        <div class="space-y-4 text-sm">
            <div>
                <h3 class="font-semibold text-blue-800">Products Endpoint</h3>
                <code class="bg-blue-100 px-2 py-1 rounded">GET /api/tables/products</code>
                <p class="text-blue-700 mt-1">Returns product inventory data with pagination support</p>
            </div>
            <div>
                <h3 class="font-semibold text-blue-800">Analytics Endpoint</h3>
                <code class="bg-blue-100 px-2 py-1 rounded">GET /api/tables/analytics</code>
                <p class="text-blue-700 mt-1">Returns real-time website analytics data</p>
            </div>
            <div>
                <h3 class="font-semibold text-blue-800">Server Monitoring Endpoint</h3>
                <code class="bg-blue-100 px-2 py-1 rounded">GET /api/tables/servers</code>
                <p class="text-blue-700 mt-1">Returns server status and performance metrics</p>
            </div>
        </div>
    </div>

    {{-- JavaScript Event Handling Examples --}}
    <div class="bg-gray-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">JavaScript Event Handling</h2>
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Row Actions</h3>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm"><code>// Listen for row actions
document.addEventListener('rowAction', function(e) {
    const { action, id, table } = e.detail;
    
    switch(action) {
        case 'edit':
            window.location.href = `/admin/users/${id}/edit`;
            break;
        case 'delete':
            if(confirm('Are you sure?')) {
                // Delete logic here
            }
            break;
    }
});</code></pre>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Bulk Actions</h3>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm"><code>// Listen for bulk actions
document.addEventListener('bulkAction', function(e) {
    const { action, ids, table } = e.detail;
    
    switch(action) {
        case 'delete':
            if(confirm(`Delete ${ids.length} items?`)) {
                // Bulk delete logic here
            }
            break;
        case 'activate':
            // Bulk activate logic here
            break;
    }
});</code></pre>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Basic Usage</h3>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto text-sm"><code>&lt;x-admin.widgets.data-table
    title="Users"
    :data="$users"
    :columns="[
        ['key' => 'name', 'label' => 'Name', 'sortable' => true],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'created_at', 'label' => 'Created', 'type' => 'date']
    ]"
    :sortable="true"
    :filterable="true"
    :paginated="true"
/&gt;</code></pre>
            </div>
        </div>
    </div>
</div>

<script>
// Example event handlers for the data tables
document.addEventListener('DOMContentLoaded', function() {
    // Handle row actions
    document.addEventListener('rowAction', function(e) {
        const { action, id, table } = e.detail;
        
        console.log(`Row action: ${action} on ID: ${id}`);
        
        switch(action) {
            case 'view':
                alert(`Viewing item ${id}`);
                break;
            case 'edit':
                alert(`Editing item ${id}`);
                break;
            case 'delete':
                if(confirm(`Are you sure you want to delete item ${id}?`)) {
                    alert(`Deleting item ${id}`);
                }
                break;
            case 'duplicate':
                alert(`Duplicating item ${id}`);
                break;
        }
    });
    
    // Handle bulk actions
    document.addEventListener('bulkAction', function(e) {
        const { action, ids, table } = e.detail;
        
        console.log(`Bulk action: ${action} on IDs:`, ids);
        
        switch(action) {
            case 'activate':
                alert(`Activating ${ids.length} items`);
                break;
            case 'deactivate':
                alert(`Deactivating ${ids.length} items`);
                break;
            case 'delete':
                if(confirm(`Are you sure you want to delete ${ids.length} items?`)) {
                    alert(`Deleting ${ids.length} items`);
                }
                break;
        }
    });
});
</script>
@endsection
