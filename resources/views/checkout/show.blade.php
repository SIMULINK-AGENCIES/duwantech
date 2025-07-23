@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-green-600">Home</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('categories.show', $product->category->slug) }}" class="hover:text-green-600">{{ $product->category->name }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li><span class="text-gray-900">{{ $product->name }}</span></li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Complete Your Purchase</h1>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Product Details -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Product Details</h2>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $product->thumbnail_url }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-20 h-20 object-cover rounded">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $product->description }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xl font-bold text-green-600">KSh {{ number_format($product->price) }}</span>
                                    @if(isset($product->attributes['condition']))
                                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $product->attributes['condition'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>
                    
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('checkout.process', $product->slug) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                M-PESA Phone Number
                            </label>
                            <input type="tel" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ old('phone_number', auth()->user()->phone ?? '') }}"
                                   placeholder="e.g., 0712345678"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('phone_number') border-red-500 @enderror"
                                   required>
                            @error('phone_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-600 mt-1">Enter the phone number registered with M-PESA</p>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Notes (Optional)
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Any special instructions or notes..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Order Summary</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Product Price:</span>
                                    <span class="font-medium">KSh {{ number_format($product->price) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Delivery:</span>
                                    <span class="font-medium text-green-600">Free</span>
                                </div>
                                <hr class="my-2">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span class="text-green-600">KSh {{ number_format($product->price) }}</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            Pay with M-PESA
                        </button>
                    </form>

                    <!-- M-PESA Instructions -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-900 mb-2">How to Pay with M-PESA</h3>
                        <ol class="text-sm text-blue-800 space-y-1">
                            <li>1. Click "Pay with M-PESA" button</li>
                            <li>2. You'll receive an M-PESA prompt on your phone</li>
                            <li>3. Enter your M-PESA PIN when prompted</li>
                            <li>4. Confirm the payment</li>
                            <li>5. You'll receive a confirmation message</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 