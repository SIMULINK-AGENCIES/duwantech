{{-- Recent Arrivals Grid --}}
<section class="max-w-7xl mx-auto px-4 sm:px-8 py-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Recent Arrivals</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($recentProducts as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section> 