{{-- Related Products List --}}
@props(['relatedProducts'])
<div class="mt-16">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Related Products</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach ($relatedProducts as $related)
            <x-product.related-card :related="$related" />
        @endforeach
    </div>
</div> 