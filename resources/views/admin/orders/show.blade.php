<x-admin.layouts.master title="Order Details">
    <x-slot name="breadcrumbs">
        @php
            $breadcrumbs = [
                ['title' => 'Orders', 'url' => route('admin.orders.index')],
                ['title' => 'Order #' . $order->order_number, 'url' => route('admin.orders.show', $order)]
            ];
        @endphp
    </x-slot>

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            Back to Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Details -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Order Number:</span>
                    <span class="text-sm text-gray-900">{{ $order->order_number }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Date:</span>
                    <span class="text-sm text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Status:</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                           ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Amount:</span>
                    <span class="text-sm font-semibold text-gray-900">KES {{ number_format($order->amount) }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Payment Method:</span>
                    <span class="text-sm text-gray-900">{{ ucfirst($order->payment_method) }}</span>
                </div>
                
                @if($order->phone_number)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                        <span class="text-sm text-gray-900">{{ $order->phone_number }}</span>
                    </div>
                @endif
                
                @if($order->notes)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Notes:</span>
                        <span class="text-sm text-gray-900">{{ $order->notes }}</span>
                    </div>
                @endif
            </div>

            <!-- Update Status -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Update Order Status</h4>
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="flex space-x-2">
                        <select name="status" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Customer & Product Details -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Name:</span>
                        <p class="text-sm text-gray-900">{{ $order->user->name }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Email:</span>
                        <p class="text-sm text-gray-900">{{ $order->user->email }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                        <p class="text-sm text-gray-900">{{ $order->user->phone ?? 'Not provided' }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Member Since:</span>
                        <p class="text-sm text-gray-900">{{ $order->user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Product Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Information</h3>
                
                <div class="flex items-center space-x-4">
                    <img src="{{ $order->product->image }}" alt="{{ $order->product->name }}" 
                         class="w-16 h-16 rounded-lg object-cover">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">{{ $order->product->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $order->product->category->name }}</p>
                        <p class="text-sm font-semibold text-gray-900">KES {{ number_format($order->product->price) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            @if($order->payment)
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Payment ID:</span>
                            <span class="text-sm text-gray-900">{{ $order->payment->payment_id }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Status:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $order->payment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($order->payment->status) }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Amount:</span>
                            <span class="text-sm font-semibold text-gray-900">KES {{ number_format($order->payment->amount) }}</span>
                        </div>
                        
                        @if($order->payment->transaction_id)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Transaction ID:</span>
                                <span class="text-sm text-gray-900">{{ $order->payment->transaction_id }}</span>
                            </div>
                        @endif
                        
                        @if($order->payment->paid_at)
                            <div class="flex justify-between">
                </div>
            @endif
        </div>
    </div>
</div>
</x-admin.layouts.master>     </div>
            @endif
        </div>
    </div>
</div>
@endsection 