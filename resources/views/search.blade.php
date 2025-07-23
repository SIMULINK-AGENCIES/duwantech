@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-4">Search Results for "{{ $query }}"</h1>
    @if($products->count())
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                    <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="h-32 w-full object-cover rounded mb-2">
                    <div class="font-medium">{{ $product->name }}</div>
                    <div class="text-blue-700 font-bold">Ksh {{ number_format($product->price) }}</div>
                    <div class="text-xs text-gray-500 mb-2">Category: {{ $product->category->name ?? '-' }}</div>
                    <a href="{{ route('products.show', $product->slug) }}" class="text-xs text-white bg-blue-600 px-2 py-1 rounded hover:bg-blue-700 mt-auto">View</a>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @else
        <div class="text-gray-600">No products found matching your search.</div>
    @endif
</div>
@endsection 