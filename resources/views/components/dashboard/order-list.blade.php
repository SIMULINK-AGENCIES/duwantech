{{-- Dashboard Order List Component --}}
@props(['orders'])
<div class="bg-white rounded-2xl shadow mb-8">
    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
        <a href="{{ route('dashboard.orders') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            View All
        </a>
    </div>
    @if($orders->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($orders as $order)
                <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $order->product->name }}</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                            <span>Order #{{ $order->order_number }}</span>
                            <span>Amount: <span class="font-medium text-gray-700">KES {{ number_format($order->amount) }}</span></span>
                            <span>{{ $order->created_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('dashboard.orders') }}" class="text-blue-600 hover:underline text-xs font-medium mt-2 md:mt-0">Details</a>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No orders yet</h3>
            <p class="mt-1 text-sm text-gray-500">Start shopping to see your orders here.</p>
            <div class="mt-6">
                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Start Shopping
                </a>
            </div>
        </div>
    @endif
</div> 