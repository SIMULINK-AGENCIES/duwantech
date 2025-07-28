<x-admin.layouts.master title="Orders">
    <x-slot name="breadcrumbs">
        @php
            $breadcrumbs = [
                ['title' => 'Orders', 'url' => route('admin.orders.index')]
            ];
        @endphp
    </x-slot>

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
        <a href="{{ route('admin.orders.transactions') }}" class="btn btn-success">
            View Transactions
        </a>
    </div>

    @if($orders->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @foreach($orders as $order)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $order->product->name }}</h3>
                                        <p class="text-sm text-gray-600">Order #{{ $order->order_number }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->user->name }} • {{ $order->user->email }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-900">KES {{ number_format($order->amount) }}</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                            @if($order->payment)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $order->payment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst($order->payment->status) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No orders</h3>
            <p class="mt-1 text-sm text-gray-500">Orders will appear here when customers make purchases.</p>
        </div>
    @endif
</div>
</x-admin.layouts.master> <p class="mt-1 text-sm text-gray-500">Orders will appear here when customers make purchases.</p>
        </div>
    @endif
</div>
@endsection 