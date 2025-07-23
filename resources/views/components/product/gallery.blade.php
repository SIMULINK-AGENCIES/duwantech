{{-- Product Gallery --}}
@props(['product'])
<div class="w-full bg-white rounded-2xl shadow p-4 flex items-center justify-center mb-4">
    <img src="{{ $product->image ?? 'https://via.placeholder.com/500x500/4F46E5/FFFFFF?text=' . urlencode($product->name) }}"
         alt="{{ $product->name }}"
         class="w-full max-w-xs h-80 object-contain rounded-xl transition-transform duration-200 hover:scale-105"
    >
</div> 