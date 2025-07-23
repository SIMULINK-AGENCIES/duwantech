{{-- Product Info --}}
@props(['product'])
<div class="flex flex-col gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-2">{{ $product->name }}</h1>
        <div class="flex items-center gap-2 mb-2">
            <span class="text-green-600 text-2xl font-bold">KES {{ number_format($product->price, 2) }}</span>
            @if($product->is_featured)
                <span class="ml-2 px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded">Featured</span>
            @endif
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <span>Category: <span class="font-semibold text-gray-700">{{ $product->category->name ?? '-' }}</span></span>
            <span class="mx-2">|</span>
            <span><svg class="inline w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg> {{ $product->views ?? 0 }} views</span>
        </div>
    </div>
    <div class="text-gray-700 mb-4">
        {{ $product->description }}
    </div>
    <form class="flex items-center gap-3 mb-4">
        <label for="quantity" class="text-sm text-gray-600">Qty:</label>
        <input id="quantity" name="quantity" type="number" min="1" value="1" class="w-16 px-2 py-2 border rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-2 rounded-lg transition">Add to Cart</button>
    </form>
    <div class="flex items-center gap-4 mt-2">
        <span class="text-gray-500 text-sm">Share:</span>
        <a href="#" class="hover:text-blue-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.522-4.477-10-10-10S2 6.478 2 12c0 5 3.657 9.127 8.438 9.877v-6.987h-2.54v-2.89h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.242 0-1.632.771-1.632 1.562v1.875h2.773l-.443 2.89h-2.33v6.987C18.343 21.127 22 17 22 12z"/></svg></a>
        <a href="#" class="hover:text-pink-500"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2h8.5A5.75 5.75 0 0122 7.75v8.5A5.75 5.75 0 0116.25 22h-8.5A5.75 5.75 0 012 16.25v-8.5A5.75 5.75 0 017.75 2zm0 1.5A4.25 4.25 0 003.5 7.75v8.5A4.25 4.25 0 007.75 20.5h8.5a4.25 4.25 0 004.25-4.25v-8.5A4.25 4.25 0 0016.25 3.5h-8.5zm8.75 2.25a.75.75 0 110 1.5.75.75 0 010-1.5zM12 7.25A4.75 4.75 0 1112 16.75a4.75 4.75 0 010-9.5zm0 1.5a3.25 3.25 0 100 6.5 3.25 3.25 0 000-6.5z"/></svg></a>
        <a href="#" class="hover:text-black"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.75 2v12.25a2.25 2.25 0 11-2.25-2.25h.25V9.5a5.25 5.25 0 102.25 4.25V2h-2.25z"/></svg></a>
        <a href="#" class="hover:text-gray-800"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.53 2H21l-7.19 8.21L22 22h-6.56l-5.25-6.98L3.5 22H0l7.64-8.73L2 2h6.56l4.88 6.49L17.53 2zm-2.13 16.98h2.18l-6.16-8.2-2.18-2.91H7.03l6.16 8.2 2.21 2.91z"/></svg></a>
    </div>
    <div x-data="{ tab: 'description' }" class="mt-8">
        <div class="flex gap-4 border-b mb-4">
            <button @click="tab = 'description'" :class="tab === 'description' ? 'border-b-2 border-green-600 text-green-700' : 'text-gray-500'" class="pb-2 px-2 font-semibold transition">Description</button>
            <button @click="tab = 'reviews'" :class="tab === 'reviews' ? 'border-b-2 border-green-600 text-green-700' : 'text-gray-500'" class="pb-2 px-2 font-semibold transition">Reviews</button>
            <button @click="tab = 'shipping'" :class="tab === 'shipping' ? 'border-b-2 border-green-600 text-green-700' : 'text-gray-500'" class="pb-2 px-2 font-semibold transition">Shipping Info</button>
        </div>
        <div>
            <div x-show="tab === 'description'" class="text-gray-700">
                {{ $product->description }}
            </div>
            <div x-show="tab === 'reviews'" class="text-gray-700">
                <p class="italic text-gray-400">No reviews yet.</p>
            </div>
            <div x-show="tab === 'shipping'" class="text-gray-700">
                <p>Standard shipping: 1-3 business days. Free delivery across Kenya.</p>
            </div>
        </div>
    </div>
</div> 