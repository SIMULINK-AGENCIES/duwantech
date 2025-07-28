<x-admin.layouts.master title="Notification Center">
    <x-slot name="breadcrumbs">
        @php
            $breadcrumbs = [
                ['title' => 'Notifications', 'url' => route('admin.notifications.index')]
            ];
        @endphp
    </x-slot>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notification Center</h1>
            <p class="text-gray-600 mt-1">Manage and monitor all admin notifications</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Test Notification Button (Development) -->
            @if(app()->environment('local'))
            <button id="create-test-notification"
                    class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Test Notification
            </button>
            @endif
            
            <!-- Preferences Button -->
            <button id="notification-preferences"
                    class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Preferences
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Notifications</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Unread</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['unread']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['today']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Urgent</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['urgent']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <!-- Filters -->
                <div class="flex flex-wrap items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <label for="type-filter" class="text-sm font-medium text-gray-700">Type:</label>
                        <select id="type-filter" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="success">Success</option>
                            <option value="error">Error</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <label for="priority-filter" class="text-sm font-medium text-gray-700">Priority:</label>
                        <select id="priority-filter" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <label for="status-filter" class="text-sm font-medium text-gray-700">Status:</label>
                        <select id="status-filter" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All</option>
                            <option value="unread">Unread</option>
                            <option value="read">Read</option>
                        </select>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-3">
                    <button id="bulk-mark-read" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            disabled>
                        Mark Selected Read
                    </button>
                    
                    <button id="bulk-delete" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            disabled>
                        Delete Selected
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Notifications</h3>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    <label for="select-all" class="text-sm text-gray-700">Select All</label>
                </div>
            </div>
        </div>
        
        @if($notifications->count() > 0)
            <ul id="notifications-list" class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                <li class="notification-item {{ !$notification->is_read ? 'bg-blue-50' : '' }}" data-notification-id="{{ $notification->id }}">
                    <div class="px-4 py-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <!-- Checkbox -->
                            <input type="checkbox" 
                                   class="notification-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" 
                                   value="{{ $notification->id }}">
                            
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $notification->getColorClass() }}">
                                    {!! $notification->getIcon() !!}
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 {{ !$notification->is_read ? 'font-semibold' : '' }}">
                                        {{ $notification->title }}
                                        @if($notification->priority === 'high')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                                Urgent
                                            </span>
                                        @endif
                                    </p>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                        @if(!$notification->is_read)
                                            <span class="inline-flex w-2 h-2 bg-blue-500 rounded-full"></span>
                                        @endif
                                    </div>
                                </div>
                                
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                
                                @if($notification->user)
                                    <p class="text-xs text-gray-500 mt-2">Related to: {{ $notification->user->name }}</p>
                                @endif
                                
                                <!-- Actions -->
                                <div class="flex items-center space-x-3 mt-3">
                                    @if(!$notification->is_read)
                                        <button class="mark-read-btn text-xs text-blue-600 hover:text-blue-800 font-medium" 
                                                data-notification-id="{{ $notification->id }}">
                                            Mark as read
                                        </button>
                                    @endif
                                    
                                    <button class="delete-btn text-xs text-red-600 hover:text-red-800 font-medium" 
                                            data-notification-id="{{ $notification->id }}">
                                        Delete
                                    </button>
                                    
                                    @if($notification->data && isset($notification->data['order_id']))
                                        <a href="{{ route('admin.orders.show', $notification->data['order_id']) }}" 
                                           class="text-xs text-green-600 hover:text-green-800 font-medium">
                                            View Order
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            
            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                <p class="text-gray-500 mb-4">You're all caught up! No notifications to display.</p>
            </div>
        @endif
    </div>
</div>

<!-- Notification Preferences Modal -->
<div id="preferences-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Notification Preferences</h3>
                
                <form id="preferences-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notification Types</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="types[]" value="email" class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">Email notifications</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="types[]" value="push" class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">Push notifications</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="types[]" value="sound" class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">Sound notifications</span>
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                        <select name="frequency" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="real_time">Real-time</option>
                            <option value="hourly">Hourly digest</option>
                            <option value="daily">Daily digest</option>
                        </select>
                    </div>
                </form>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button id="save-preferences" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Save
                </button>
                <button id="cancel-preferences" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notification Center JavaScript functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const notificationCheckboxes = document.querySelectorAll('.notification-checkbox');
    const bulkMarkReadBtn = document.getElementById('bulk-mark-read');
    const bulkDeleteBtn = document.getElementById('bulk-delete');
    const createTestBtn = document.getElementById('create-test-notification');
    const preferencesBtn = document.getElementById('notification-preferences');
    const preferencesModal = document.getElementById('preferences-modal');
    
    // Select all functionality
    selectAllCheckbox?.addEventListener('change', function() {
        notificationCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });
    
    // Individual checkbox functionality
    notificationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
    
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.notification-checkbox:checked');
        const hasSelection = checkedBoxes.length > 0;
        
        if (bulkMarkReadBtn) bulkMarkReadBtn.disabled = !hasSelection;
        if (bulkDeleteBtn) bulkDeleteBtn.disabled = !hasSelection;
        
        // Update select all checkbox state
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checkedBoxes.length === notificationCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < notificationCheckboxes.length;
        }
    }
    
    // Individual mark as read
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const notificationId = this.dataset.notificationId;
            await markAsRead(notificationId);
        });
    });
    
    // Individual delete
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (confirm('Are you sure you want to delete this notification?')) {
                const notificationId = this.dataset.notificationId;
                await deleteNotification(notificationId);
            }
        });
    });
    
    // Bulk mark as read
    bulkMarkReadBtn?.addEventListener('click', async function() {
        const selected = getSelectedNotifications();
        if (selected.length > 0) {
            for (const id of selected) {
                await markAsRead(id);
            }
        }
    });
    
    // Bulk delete
    bulkDeleteBtn?.addEventListener('click', async function() {
        const selected = getSelectedNotifications();
        if (selected.length > 0 && confirm(`Are you sure you want to delete ${selected.length} notification(s)?`)) {
            await bulkDelete(selected);
        }
    });
    
    // Test notification (development)
    createTestBtn?.addEventListener('click', async function() {
        try {
            const response = await fetch('/admin/notifications/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                showAlert('Test notification created successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            }
        } catch (error) {
            showAlert('Failed to create test notification', 'error');
        }
    });
    
    // Preferences modal
    preferencesBtn?.addEventListener('click', function() {
        preferencesModal?.classList.remove('hidden');
    });
    
    document.getElementById('cancel-preferences')?.addEventListener('click', function() {
        preferencesModal?.classList.add('hidden');
    });
    
    // Helper functions
    function getSelectedNotifications() {
        return Array.from(document.querySelectorAll('.notification-checkbox:checked')).map(cb => cb.value);
    }
    
    async function markAsRead(notificationId) {
        try {
            const response = await fetch(`/admin/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (item) {
                    item.classList.remove('bg-blue-50');
                    const markBtn = item.querySelector('.mark-read-btn');
                    if (markBtn) markBtn.remove();
                }
                showAlert('Notification marked as read', 'success');
            }
        } catch (error) {
            showAlert('Failed to mark notification as read', 'error');
        }
    }
    
    async function deleteNotification(notificationId) {
        try {
            const response = await fetch(`/admin/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (item) item.remove();
                showAlert('Notification deleted successfully', 'success');
            }
        } catch (error) {
            showAlert('Failed to delete notification', 'error');
        }
    }
    
    async function bulkDelete(notificationIds) {
        try {
            const response = await fetch('/admin/notifications/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ notification_ids: notificationIds })
            });
            
            if (response.ok) {
                notificationIds.forEach(id => {
                    const item = document.querySelector(`[data-notification-id="${id}"]`);
                    if (item) item.remove();
                });
                showAlert(`${notificationIds.length} notifications deleted successfully`, 'success');
                updateBulkActions();
            }
        } catch (error) {
            showAlert('Failed to delete notifications', 'error');
        }
    }
    
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-md ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        alertDiv.textContent = message;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
});
</script>
</div>
</x-admin.layouts.master>
