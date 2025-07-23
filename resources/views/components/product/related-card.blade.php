{{-- Related Product Card --}}
@props(['related'])
<div class="bg-white rounded-2xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">
    <a href="{{ route('products.show', $related) }}">
        <img src="{{ $related->image ?? 'https://via.placeholder.com/400x300/4F46E5/FFFFFF?text=' . urlencode($related->name) }}" alt="{{ $related->name }}" class="w-full h-44 object-cover">
    </a>
    <div class="p-4 flex-1 flex flex-col">
        <a href="{{ route('products.show', $related) }}" class="font-bold text-lg mb-1 hover:text-green-600">{{ $related->name }}</a>
        <div class="text-green-600 text-xl font-bold mb-2">KSh {{ number_format($related->price) }}</div>
        <a href="{{ route('products.show', $related) }}" class="mt-auto block w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 rounded-lg font-semibold transition">View Details</a>
    </div>
</div> 