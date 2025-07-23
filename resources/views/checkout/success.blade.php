@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
                <p class="text-gray-600">Your order has been created and payment initiated.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Order Details -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h2>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Number:</span>
                            <span class="font-medium">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Date:</span>
                            <span class="font-medium">{{ $order->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($order->status === 'paid') bg-green-100 text-green-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="font-medium">{{ strtoupper($order->payment_method) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone Number:</span>
                            <span class="font-medium">{{ $order->phone_number }}</span>
                        </div>
                        @if($order->notes)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Notes:</span>
                                <span class="font-medium">{{ $order->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Product Details -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Product Details</h2>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $order->product->thumbnail_url }}" 
                                 alt="{{ $order->product->name }}" 
                                 class="w-20 h-20 object-cover rounded">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $order->product->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $order->product->description }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xl font-bold text-green-600">KSh {{ number_format($order->amount) }}</span>
                                    @if(isset($order->product->attributes['condition']))
                                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $order->product->attributes['condition'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Status</h2>
                <div id="payment-status" class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">Payment Status</p>
                            <p class="text-sm text-gray-600" id="status-message">
                                @if($order->isPaid())
                                    Payment completed successfully
                                @else
                                    Waiting for payment confirmation...
                                @endif
                            </p>
                        </div>
                        <div id="status-indicator" class="flex items-center">
                            @if($order->isPaid())
                                <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-green-600 font-medium">Paid</span>
                            @else
                                <div class="w-4 h-4 bg-yellow-500 rounded-full mr-2 animate-pulse"></div>
                                <span class="text-yellow-600 font-medium">Pending</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($order->payment && $order->payment->mpesa_receipt_number)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">M-PESA Receipt:</span> {{ $order->payment->mpesa_receipt_number }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <a href="{{ route('home') }}" 
                   class="flex-1 bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-700 transition-colors text-center">
                    Continue Shopping
                </a>
                @if(!$order->isPaid())
                    <button onclick="checkPaymentStatus()" 
                            class="flex-1 bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                        Check Payment Status
                    </button>
                @endif
            </div>

            <!-- M-PESA Instructions -->
            @if(!$order->isPaid())
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">What's Next?</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Check your phone for the M-PESA prompt</li>
                        <li>• Enter your M-PESA PIN when prompted</li>
                        <li>• Confirm the payment of KSh {{ number_format($order->amount) }}</li>
                        <li>• You'll receive a confirmation SMS from M-PESA</li>
                        <li>• Click "Check Payment Status" to refresh the status</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function checkPaymentStatus() {
    fetch('{{ route("checkout.status", $order->order_number) }}')
        .then(response => response.json())
        .then(data => {
            const statusIndicator = document.getElementById('status-indicator');
            const statusMessage = document.getElementById('status-message');
            
            if (data.is_paid) {
                statusIndicator.innerHTML = `
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-green-600 font-medium">Paid</span>
                `;
                statusMessage.textContent = 'Payment completed successfully';
                location.reload(); // Reload to show receipt number
            } else {
                statusMessage.textContent = 'Payment still pending. Please check your phone and try again.';
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
        });
}

// Auto-check payment status every 10 seconds if not paid
@if(!$order->isPaid())
setInterval(checkPaymentStatus, 10000);
@endif
</script>
@endsection 