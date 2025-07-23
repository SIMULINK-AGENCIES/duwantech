{{-- @extends('layouts.app')

@section('title', "Kenya's Premier Tech Marketplace")

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-green-500 to-orange-400 rounded-b-3xl overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between px-4 sm:px-8 py-12 md:py-20 gap-8">
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-4">Kenya's Premier Tech Marketplace</h1>
                <p class="text-white text-lg md:text-2xl mb-8">Discover amazing deals on smartphones, laptops, and electronics. Verified sellers, authentic products, M-PESA payments.</p>
                <form action="{{ route('search') }}" method="GET" class="max-w-lg mx-auto md:mx-0">
                    <div class="relative">
                        <input type="text" name="q" class="w-full pl-12 pr-4 py-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-400 focus:outline-none text-lg shadow" placeholder="What are you looking for?" value="{{ request('q') }}">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        </span>
                    </div>
                </form>
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="#trending" class="inline-flex items-center justify-center bg-white text-orange-600 font-bold px-8 py-3 rounded-xl shadow hover:bg-orange-50 transition text-lg">Shop Now <span class="ml-2">→</span></a>
                </div>
            </div>
            <div class="flex-1 flex justify-center md:justify-end">
                <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=400&q=80" alt="Hero" class="w-80 h-80 object-contain rounded-2xl shadow-lg hidden md:block">
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 flex flex-col lg:flex-row gap-8 mt-8">
        <!-- Sidebar Categories -->
        <aside class="w-full lg:w-72 flex-shrink-0 order-2 lg:order-1 mb-8 lg:mb-0">
            <div class="bg-white rounded-2xl shadow p-4 sm:p-6 sticky top-8">
                <h2 class="text-lg font-bold mb-4 sm:mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Categories
                </h2>
                <div class="space-y-4">
                    @foreach($categories as $category)
                        <div>
                            <div class="flex items-center justify-between cursor-pointer group">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded bg-green-100 text-green-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                                    </span>
                                    <span class="font-medium text-base group-hover:text-green-600 transition">{{ $category->name }}</span>
                                </div>
                                <span class="bg-orange-100 text-orange-600 text-xs font-bold px-2 py-1 rounded-full">{{ $category->products_count ?? 0 }}</span>
                            </div>
                            @if($category->children->count())
                                <div class="ml-8 mt-2 space-y-1">
                                    @foreach($category->children as $sub)
                                        <div class="flex items-center justify-between text-sm text-gray-600">
                                            <span>{{ $sub->name }}</span>
                                            <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $sub->products_count ?? 0 }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <!-- Social Share Buttons -->
                            <div class="flex gap-2 mt-2 ml-8">
                                <a href="#" title="Share on Facebook" class="hover:text-blue-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.522-4.477-10-10-10S2 6.478 2 12c0 5 3.657 9.127 8.438 9.877v-6.987h-2.54v-2.89h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.242 0-1.632.771-1.632 1.562v1.875h2.773l-.443 2.89h-2.33v6.987C18.343 21.127 22 17 22 12z"/></svg></a>
                                <a href="#" title="Share on Instagram" class="hover:text-pink-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2h8.5A5.75 5.75 0 0122 7.75v8.5A5.75 5.75 0 0116.25 22h-8.5A5.75 5.75 0 012 16.25v-8.5A5.75 5.75 0 017.75 2zm0 1.5A4.25 4.25 0 003.5 7.75v8.5A4.25 4.25 0 007.75 20.5h8.5a4.25 4.25 0 004.25-4.25v-8.5A4.25 4.25 0 0016.25 3.5h-8.5zm8.75 2.25a.75.75 0 110 1.5.75.75 0 010-1.5zM12 7.25A4.75 4.75 0 1112 16.75a4.75 4.75 0 010-9.5zm0 1.5a3.25 3.25 0 100 6.5 3.25 3.25 0 000-6.5z"/></svg></a>
                                <a href="#" title="Share on TikTok" class="hover:text-black"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.75 2v12.25a2.25 2.25 0 11-2.25-2.25h.25V9.5a5.25 5.25 0 102.25 4.25V2h-2.25z"/></svg></a>
                                <a href="#" title="Share on X" class="hover:text-gray-800"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.53 2H21l-7.19 8.21L22 22h-6.56l-5.25-6.98L3.5 22H0l7.64-8.73L2 2h6.56l4.88 6.49L17.53 2zm-2.13 16.98h2.18l-6.16-8.2-2.18-2.91H7.03l6.16 8.2 2.21 2.91z"/></svg></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-w-0 order-1 lg:order-2">
            <!-- Trending Banner -->
            <div class="rounded-2xl bg-gradient-to-r from-green-500 to-orange-400 p-4 sm:p-6 mb-8 flex items-center gap-4">
                <span class="text-white text-lg sm:text-2xl font-bold flex items-center gap-2">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    Trending Products
                </span>
                <span class="sm:ml-4 bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full">Hot Deals</span>
                <span class="sm:ml-auto text-white text-xs sm:text-base text-center sm:text-left">Most popular items this week – limited time offers!</span>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow p-4 sm:p-6 flex flex-col items-center">
                    <span class="text-green-500 text-2xl sm:text-3xl mb-2"><svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.784.57-1.838-.197-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.045 9.394c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"/></svg></span>
                    <div class="text-xl sm:text-2xl font-bold text-green-700 mb-1">4.7</div>
                    <div class="text-gray-500 text-xs sm:text-base">Average Rating</div>
                </div>
                <div class="bg-white rounded-2xl shadow p-4 sm:p-6 flex flex-col items-center">
                    <span class="text-orange-500 text-2xl sm:text-3xl mb-2"><svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M13 7H7v6h6V7z"/><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v1a1 1 0 102 0V5zm-1 2a1 1 0 00-1 1v1a1 1 0 002 0V8a1 1 0 00-1-1zm-1 3a1 1 0 011-1h1a1 1 0 110 2h-1a1 1 0 01-1-1z" clip-rule="evenodd"/></svg></span>
                    <div class="text-xl sm:text-2xl font-bold text-orange-600 mb-1">1,250+</div>
                    <div class="text-gray-500 text-xs sm:text-base">Happy Customers</div>
                </div>
                <div class="bg-white rounded-2xl shadow p-4 sm:p-6 flex flex-col items-center">
                    <span class="text-yellow-500 text-2xl sm:text-3xl mb-2"><svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M3 3h14v14H3V3zm2 2v10h10V5H5zm2 2h6v6H7V7z"/></svg></span>
                    <div class="text-xl sm:text-2xl font-bold text-yellow-600 mb-1">50+</div>
                    <div class="text-gray-500 text-xs sm:text-base">Products Sold Today</div>
                </div>
            </div>

            <!-- Trending Products Grid -->
            <div id="trending">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($recentProducts as $product)
                        <div class="bg-white rounded-2xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">
                            <div class="relative">
                                <img src="{{ $product->image ?? 'https://via.placeholder.com/400x300/4F46E5/FFFFFF?text=' . urlencode($product->name) }}" alt="{{ $product->name }}" class="w-full h-44 sm:h-56 object-cover">
                                @if($product->is_featured || $loop->index < 2)
                                    <span class="absolute top-3 left-3 bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full">Featured</span>
                                @endif
                                <span class="absolute top-3 right-3 bg-white/80 rounded-full p-2 cursor-pointer shadow">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </span>
                                <span class="absolute bottom-3 left-3 bg-orange-100 text-orange-600 text-xs font-bold px-2 py-1 rounded-full">-{{ $product->attributes['discount'] ?? rand(10,20) }}%</span>
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="font-bold text-base sm:text-lg mb-1">{{ $product->name }}</h3>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-green-600 text-lg sm:text-xl font-bold">KSh {{ number_format($product->price) }}</span>
                                    @if(($product->attributes['condition'] ?? 'New') === 'New')
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">New</span>
                                    @endif
                                </div>
                                @auth
                                <div class="flex gap-2 mb-2">
                                    <a href="tel:{{ $product->seller_phone ?? '+254700000000' }}" class="flex-1 bg-blue-100 text-blue-700 font-semibold py-2 rounded-lg text-xs sm:text-sm text-center hover:bg-blue-200 transition">Call Shop</a>
                                    <a href="#" class="flex-1 bg-green-100 text-green-700 font-semibold py-2 rounded-lg text-xs sm:text-sm text-center hover:bg-green-200 transition">Buy via M-PESA</a>
                                </div>
                                @endauth
                                <a href="{{ route('products.show', $product) }}" class="mt-auto block w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 rounded-lg font-semibold transition text-sm sm:text-base">View Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>
</div>
@endsection  --}}

@extends('layouts.app')

@section('title', "Kenya's Premier Tech Marketplace")

@section('content')
    {{-- ================= HERO SECTION ================= --}}
    <x-home.hero />

    {{-- ================= CATEGORY GRID ================= --}}
    <x-home.category-grid :categories="$categories" />

    {{-- ================= FEATURED PRODUCTS SLIDER ================= --}}
    <x-home.featured-slider :featuredProducts="$featuredProducts" />

    {{-- ================= RECENT ARRIVALS GRID ================= --}}
    <x-home.recent-grid :recentProducts="$recentProducts" />

    {{-- ================= NEWSLETTER SECTION ================= --}}
    <x-home.newsletter />

    {{-- ================= FOOTER ================= --}}
    <x-home.footer />
@endsection 