@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Sidebar: Categories -->
            <aside class="w-full lg:w-1/4">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Categories</h2>
                    <div class="space-y-4">
                        @foreach($categories ?? [] as $cat)
                            <div class="border-b border-gray-100 pb-4 last:border-b-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                            </svg>
                                        </div>
                                        <a href="{{ route('categories.show', $cat->slug) }}" class="text-gray-700 hover:text-green-600 font-medium">
                                            {{ $cat->name }}
                                        </a>
                                    </div>
                                    <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full">
                                        {{ $cat->products_count ?? 0 }}
                                    </span>
                                </div>
                                
                                @if($cat->subcategories->count())
                                    <div class="ml-11 mt-2 space-y-2">
                                        @foreach($cat->subcategories as $subcategory)
                                            <div class="flex items-center justify-between">
                                                <a href="{{ route('categories.show', $subcategory->slug) }}" class="text-sm text-gray-600 hover:text-green-600">
                                                    {{ $subcategory->name }}
                                                </a>
                                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                                    {{ $subcategory->products_count ?? 0 }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1">
                <!-- Breadcrumbs & Filters -->
                <div class="mb-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <a href="{{ route('home') }}" class="hover:text-green-600">Home</a>
                        <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $category->name }}</span>
                    </div>
                    
                    <!-- Active Filters -->
                    @if($activeFilters->count() > 0 || $min_price || $max_price)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($min_price || $max_price)
                                <span class="bg-orange-500 text-white text-sm px-3 py-1 rounded-full flex items-center">
                                    KSh {{ $min_price ?? 0 }} - {{ $max_price ?? '∞' }}
                                    <button onclick="removePriceFilter()" class="ml-2 text-white hover:text-gray-200">×</button>
                                </span>
                            @endif
                            @foreach($activeFilters as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $v)
                                        <span class="bg-orange-500 text-white text-sm px-3 py-1 rounded-full flex items-center">
                                            {{ ucfirst($key) }}: {{ $v }}
                                            <button onclick="removeFilter('{{ $key }}', '{{ $v }}')" class="ml-2 text-white hover:text-gray-200">×</button>
                                        </span>
                                    @endforeach
                                @else
                                    <span class="bg-orange-500 text-white text-sm px-3 py-1 rounded-full flex items-center">
                                        {{ ucfirst($key) }}: {{ $value }}
                                        <button onclick="removeFilter('{{ $key }}', '{{ $value }}')" class="ml-2 text-white hover:text-gray-200">×</button>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Trending Products Banner -->
                <div class="bg-gradient-to-r from-green-600 to-orange-500 rounded-lg p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ $category->name }}</h2>
                                <p class="text-white text-opacity-90">Most popular items this week - limited time offers!</p>
                            </div>
                        </div>
                        <span class="bg-green-500 text-white text-sm px-3 py-1 rounded-full">Hot Deals</span>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg p-6 text-center shadow">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-3xl font-bold text-green-600">4.7</span>
                        </div>
                        <p class="text-gray-600">Average Rating</p>
                    </div>

                    <div class="bg-white rounded-lg p-6 text-center shadow">
                        <span class="text-3xl font-bold text-orange-600">1,250+</span>
                        <p class="text-gray-600">Happy Customers</p>
                    </div>

                    <div class="bg-white rounded-lg p-6 text-center shadow">
                        <span class="text-3xl font-bold text-orange-600">50+</span>
                        <p class="text-gray-600">Products Sold Today</p>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <div class="relative">
                                <img src="{{ $product->thumbnail_url ?? 'https://via.placeholder.com/300x200?text=Product' }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-48 object-cover">
                                
                                <!-- Featured Tag -->
                                <div class="absolute top-2 left-2">
                                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Featured</span>
                                </div>
                                
                                <!-- Discount Tag -->
                                @if(isset($product->attributes['discount']) && $product->attributes['discount'] > 0)
                                    <div class="absolute top-2 right-2">
                                        <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full">-{{ $product->attributes['discount'] }}%</span>
                                    </div>
                                @endif
                                
                                <!-- Wishlist Icon -->
                                <button class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-2xl font-bold text-green-600">KSh {{ number_format($product->price) }}</span>
                                    @if(isset($product->attributes['condition']))
                                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $product->attributes['condition'] }}</span>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('products.show', $product->slug) }}" 
                                       class="flex-1 bg-green-600 text-white text-center py-2 rounded-lg hover:bg-green-700 transition-colors">
                                        View Details
                                    </a>
                                    @auth
                                        <a href="tel:+254700123456" 
                                           class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                                            Call
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($products->count() == 0)
                    <div class="text-center py-12">
                        <div class="text-gray-500 text-lg">No products found in this category.</div>
                        <a href="{{ route('home') }}" class="text-green-600 hover:text-green-700 mt-2 inline-block">Browse all categories</a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

<script>
function removeFilter(key, value) {
    const url = new URL(window.location);
    const params = url.searchParams;
    
    if (params.has(key)) {
        const values = params.getAll(key);
        const newValues = values.filter(v => v !== value);
        params.delete(key);
        newValues.forEach(v => params.append(key, v));
    }
    
    window.location.href = url.toString();
}

function removePriceFilter() {
    const url = new URL(window.location);
    url.searchParams.delete('min_price');
    url.searchParams.delete('max_price');
    window.location.href = url.toString();
}
</script>
@endsection 