{{-- Category Card Component --}}
@props(['category'])
<a href="#" class="flex flex-col items-center bg-white rounded-xl shadow hover:shadow-md p-4 transition group">
    <div class="w-14 h-14 flex items-center justify-center bg-green-50 rounded-full mb-2">
        {{-- Placeholder icon, replace with $category->icon or image if available --}}
        <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </div>
    <span class="font-semibold text-gray-700 group-hover:text-green-600">{{ $category->name }}</span>
    <span class="text-xs text-gray-400 mt-1">{{ $category->products_count ?? 0 }} products</span>
</a> 