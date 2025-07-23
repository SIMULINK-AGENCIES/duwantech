{{-- Product Card Component --}}
@props(['product', 'featured' => false])
<div class="min-w-[250px] max-w-xs bg-white rounded-2xl shadow hover:shadow-lg transition flex-shrink-0 flex flex-col">
    <img src="{{ $product->image ?? 'https://via.placeholder.com/400x300/4F46E5/FFFFFF?text=' . urlencode($product->name) }}" alt="{{ $product->name }}" class="w-full h-44 object-cover rounded-t-2xl">
    <div class="p-4 flex-1 flex flex-col">
        @if($featured)
            <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded mb-2">Featured</span>
        @endif
        <h3 class="font-bold text-lg mb-1">{{ $product->name }}</h3>
        <div class="text-sm text-gray-500 mb-2">{{ $product->category->name ?? '' }}</div>
        <div class="text-green-600 text-xl font-bold mb-2">KSh {{ number_format($product->price) }}</div>
        <a href="{{ route('products.show', $product) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 rounded-lg font-semibold transition">View Details</a>
    </div>
</div> 