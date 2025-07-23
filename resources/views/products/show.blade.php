{{-- @extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Mobile Header -->
    <div class="bg-white shadow-sm border-b sticky top-0 z-40">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center">
                <a href="{{ url()->previous() }}" class="mr-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-lg font-semibold text-gray-900 truncate">{{ $product->name }}</h1>
            </div>
            <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-600 hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                </button>
                <button class="p-2 text-gray-600 hover:text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <!-- Product Images -->
        <div class="bg-white">
            <div class="relative">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                     class="w-full h-64 object-cover">
                @if($product->is_featured)
                    <div class="absolute top-4 left-4">
                        <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full font-medium">
                            Featured
                        </span>
                    </div>
                @endif
            </div>
            
            @if($product->gallery && count($product->gallery) > 0)
                <div class="p-4">
                    <div class="flex space-x-2 overflow-x-auto">
                        @foreach($product->gallery as $image)
                            <img src="{{ $image }}" alt="{{ $product->name }}" 
                                 class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Information -->
        <div class="bg-white p-4 space-y-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $product->name }}</h2>
                <p class="text-sm text-gray-600">{{ $product->category->name }}</p>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-gray-900">KES {{ number_format($product->price) }}</p>
                    @if($product->attributes && isset($product->attributes['negotiable']) && $product->attributes['negotiable'])
                        <p class="text-sm text-gray-600">Price negotiable</p>
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    @if($product->attributes && isset($product->attributes['condition']))
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                            {{ $product->attributes['condition'] === 'New' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $product->attributes['condition'] }}
                        </span>
                    @endif
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                        {{ $product->views ?? 0 }} views
                    </span>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                <p class="text-gray-700">{{ $product->description }}</p>
            </div>

            @if($product->attributes)
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Specifications</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($product->attributes as $key => $value)
                            @if($key !== 'negotiable' && $key !== 'condition')
                                <div>
                                    <p class="text-sm font-medium text-gray-600 capitalize">{{ str_replace('_', ' ', $key) }}</p>
                                    <p class="text-sm text-gray-900">{{ $value }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Contact Information -->
        <div class="bg-white p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Contact Information</h3>
            <div class="space-y-2">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-gray-700">
                        @if($product->attributes && isset($product->attributes['location']))
                            {{ $product->attributes['location'] }}
                        @else
                            Nairobi, Kenya
                        @endif
                    </span>
                </div>
                
                @auth
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-gray-700">+254 700 000 000</span>
                    </div>
                @else
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span class="text-gray-700">Login to view contact details</span>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="bg-white p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Related Products</h3>
                <div class="space-y-3">
                    @foreach($relatedProducts as $relatedProduct)
                        <a href="{{ route('products.show', $relatedProduct) }}" 
                           class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <img src="{{ $relatedProduct->image }}" alt="{{ $relatedProduct->name }}" 
                                 class="w-16 h-16 rounded-lg object-cover">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $relatedProduct->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $relatedProduct->category->name }}</p>
                                <p class="text-sm font-semibold text-gray-900">KES {{ number_format($relatedProduct->price) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Mobile Bottom Action Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 p-4">
        <div class="flex space-x-3">
            @auth
                <a href="tel:+254700000000" 
                   class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg font-medium text-center">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Call Shop
                    </div>
                </a>
                <a href="{{ route('checkout.index', $product) }}" 
                   class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium text-center">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Buy via M-PESA
                    </div>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg font-medium text-center">
                    Login to Contact
                </a>
                <a href="{{ route('register') }}" 
                   class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium text-center">
                    Sign Up
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection  --}}
@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        {{-- ================= PRODUCT DETAILS SECTION ================= --}}
        <div class="flex flex-col lg:flex-row gap-10">
            <div class="lg:w-1/2 flex flex-col items-center">
                <x-product.gallery :product="$product" />
            </div>
            <div class="lg:w-1/2">
                <x-product.info :product="$product" />
            </div>
        </div>
        {{-- ================= RELATED PRODUCTS SECTION ================= --}}
        <x-product.related-list :relatedProducts="$relatedProducts" />
    </div>
</div>
@endsection 