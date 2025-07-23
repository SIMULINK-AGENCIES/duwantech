{{-- Featured Products Slider with Interactivity --}}
<section id="featured" class="max-w-7xl mx-auto px-4 sm:px-8 py-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Featured Products</h2>
    <div x-data="{ scroll: 0, max: 0 }" x-init="max = $refs.slider.scrollWidth - $refs.slider.clientWidth" class="relative">
        <button @click="scroll = Math.max(scroll - 300, 0); $refs.slider.scrollTo({ left: scroll, behavior: 'smooth' })" :disabled="scroll === 0" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white shadow rounded-full p-2 disabled:opacity-30 disabled:cursor-not-allowed hidden sm:block">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div x-ref="slider" class="flex gap-6 overflow-x-auto scroll-smooth no-scrollbar pb-2">
            @foreach ($featuredProducts as $product)
                <x-product-card :product="$product" featured="true" />
            @endforeach
        </div>
        <button @click="scroll = Math.min(scroll + 300, max); $refs.slider.scrollTo({ left: scroll, behavior: 'smooth' })" :disabled="scroll >= max" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white shadow rounded-full p-2 disabled:opacity-30 disabled:cursor-not-allowed hidden sm:block">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</section>

{{-- Hide scrollbars utility --}}
<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style> 