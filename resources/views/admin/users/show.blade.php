<x-admin.layouts.master title="User Details">
    <x-slot name="breadcrumbs">
        @php
            $breadcrumbs = [
                ['title' => 'Users', 'url' => route('admin.users.index')],
                ['title' => $user->name, 'url' => route('admin.users.show', $user)]
            ];
        @endphp
    </x-slot>

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            Back to Users
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Name:</span>
                    <span class="text-sm text-gray-900">{{ $user->name }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Email:</span>
                    <span class="text-sm text-gray-900">{{ $user->email }}</span>
                </div>
                
                @if($user->phone)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                        <span class="text-sm text-gray-900">{{ $user->phone }}</span>
                    </div>
                @endif
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Member Since:</span>
                    <span class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Email Verified:</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->email_verified_at ? 'Yes' : 'No' }}
                    </span>
                </div>
                
                @if($user->email_verified_at)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Verified At:</span>
                        <span class="text-sm text-gray-900">{{ $user->email_verified_at->format('M d, Y H:i') }}</span>
                    </div>
                @endif
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Roles:</span>
                    <div class="flex space-x-2">
                        @if($user->hasRole('admin'))
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                Admin
                            </span>
                        @endif
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            User
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Statistics -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Statistics</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-gray-900">{{ $user->orders->count() }}</p>
                    <p class="text-sm text-gray-600">Total Orders</p>
                </div>
                
                <div class="text-center">
                    <p class="text-2xl font-semibold text-gray-900">
                        KES {{ number_format($user->orders->sum('amount')) }}
                    </p>
                    <p class="text-sm text-gray-600">Total Spent</p>
                </div>
                
                <div class="text-center">
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $user->orders->where('status', 'completed')->count() }}
                    </p>
                    <p class="text-sm text-gray-600">Completed Orders</p>
                </div>
                
                <div class="text-center">
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $user->orders->where('status', 'pending')->count() }}
                    </p>
                    <p class="text-sm text-gray-600">Pending Orders</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    @if($user->orders->count() > 0)
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Orders</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($user->orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->product->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    KES {{ number_format($order->amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-6">
                <p class="mt-1 text-sm text-gray-500">This user hasn't placed any orders yet.</p>
            </div>
        </div>
    @endif
</div>
</x-admin.layouts.master>     <p class="mt-1 text-sm text-gray-500">This user hasn't placed any orders yet.</p>
            </div>
        </div>
    @endif
</div>
@endsection 