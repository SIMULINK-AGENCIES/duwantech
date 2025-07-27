<!-- Quick Actions Widget Component -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            <p class="text-sm text-gray-500">Frequently used actions</p>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fas fa-bolt text-yellow-500"></i>
            <span class="text-xs text-gray-500">Quick Access</span>
        </div>
    </div>

    <!-- Action Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
        <!-- Add New Product -->
        <a href="{{ route('admin.products.create') }}" 
           class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition-all duration-200">
            <div class="w-10 h-10 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200">
                <i class="fas fa-plus text-blue-600"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700 text-center">Add Product</span>
        </a>

        <!-- View Orders -->
        <a href="{{ route('admin.orders.index') }}" 
           class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-green-400 hover:bg-green-50 transition-all duration-200">
            <div class="w-10 h-10 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200">
                <i class="fas fa-shopping-cart text-green-600"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-green-700 text-center">View Orders</span>
        </a>

        <!-- Manage Users -->
        <a href="{{ route('admin.users.index') }}" 
           class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-purple-400 hover:bg-purple-50 transition-all duration-200">
            <div class="w-10 h-10 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200">
                <i class="fas fa-users text-purple-600"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-purple-700 text-center">Manage Users</span>
        </a>

        <!-- View Reports -->
        <a href="{{ route('admin.reports.index') }}" 
           class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-yellow-400 hover:bg-yellow-50 transition-all duration-200">
            <div class="w-10 h-10 bg-yellow-100 group-hover:bg-yellow-200 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200">
                <i class="fas fa-chart-bar text-yellow-600"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-yellow-700 text-center">View Reports</span>
        </a>

        <!-- Settings -->
        <a href="{{ route('admin.settings.index') }}" 
           class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition-all duration-200">
            <div class="w-10 h-10 bg-gray-100 group-hover:bg-gray-200 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200">
                <i class="fas fa-cog text-gray-600"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-700 text-center">Settings</span>
        </a>

        <!-- Inventory -->
        <a href="{{ route('admin.products.index') }}" 
           class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200">
            <div class="w-10 h-10 bg-indigo-100 group-hover:bg-indigo-200 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200">
                <i class="fas fa-boxes text-indigo-600"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-700 text-center">Products</span>
        </a>

        <!-- Analytics -->
        <a href="{{ route('admin.reports.index') }}" 
           class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-teal-400 hover:bg-teal-50 transition-all duration-200">
            <div class="w-10 h-10 bg-teal-100 group-hover:bg-teal-200 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200">
                <i class="fas fa-chart-line text-teal-600"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-teal-700 text-center">Analytics</span>
        </a>

        <!-- Export Data -->
        <a href="{{ route('admin.reports.export.revenue') }}" 
           class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-red-400 hover:bg-red-50 transition-all duration-200">
            <div class="w-10 h-10 bg-red-100 group-hover:bg-red-200 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200">
                <i class="fas fa-download text-red-600"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700 text-center">Export Data</span>
        </a>
    </div>

    <!-- Recent Actions -->
    <div class="mt-6 pt-6 border-t border-gray-200">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Recent Actions</h4>
        <div class="space-y-2">
            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-md">
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-plus text-xs text-blue-600"></i>
                    </div>
                    <span class="text-sm text-gray-700">Added new product</span>
                </div>
                <span class="text-xs text-gray-500">2 min ago</span>
            </div>
            
            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-md">
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-xs text-green-600"></i>
                    </div>
                    <span class="text-sm text-gray-700">Updated order status</span>
                </div>
                <span class="text-xs text-gray-500">5 min ago</span>
            </div>
            
            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-md">
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-xs text-purple-600"></i>
                    </div>
                    <span class="text-sm text-gray-700">Reviewed user account</span>
                </div>
                <span class="text-xs text-gray-500">10 min ago</span>
            </div>
        </div>
    </div>
</div>

<script>
function exportData() {
    // Show export modal or start export process
    if (confirm('Export dashboard data to CSV?')) {
        window.location.href = '/admin/export/dashboard-data';
    }
}
</script>
