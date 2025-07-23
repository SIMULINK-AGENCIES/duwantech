{{-- Hero Section --}}
<section class="relative bg-gradient-to-r from-green-500 to-orange-400 rounded-b-3xl overflow-hidden">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between px-4 sm:px-8 py-12 md:py-20 gap-8">
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-4">
                Kenya's Premier Tech Marketplace
            </h1>
            <p class="text-white text-lg md:text-2xl mb-8">
                Discover amazing deals on smartphones, laptops, and electronics. Verified sellers, authentic products, M-PESA payments.
            </p>
            <form action="{{ route('search') }}" method="GET" class="max-w-lg mx-auto md:mx-0">
                <div class="relative">
                    <input type="text" name="q" class="w-full pl-12 pr-4 py-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-400 focus:outline-none text-lg shadow" placeholder="What are you looking for?" value="{{ request('q') }}">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    </span>
                </div>
            </form>
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <a href="#featured" class="inline-flex items-center justify-center bg-white text-orange-600 font-bold px-8 py-3 rounded-xl shadow hover:bg-orange-50 transition text-lg">
                    Shop Now <span class="ml-2">→</span>
                </a>
            </div>
        </div>
        <div class="flex-1 flex justify-center md:justify-end">
            <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=400&q=80" alt="Hero" class="w-80 h-80 object-contain rounded-2xl shadow-lg hidden md:block">
        </div>
    </div>
</section> 