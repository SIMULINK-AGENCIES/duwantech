{{-- Dashboard Card Component --}}
@props(['icon', 'color' => 'blue', 'label', 'value' => null, 'action' => false, 'href' => null])
@php
    $iconColors = [
        'blue' => 'text-blue-600 bg-blue-100',
        'green' => 'text-green-600 bg-green-100',
        'yellow' => 'text-yellow-600 bg-yellow-100',
        'purple' => 'text-purple-600 bg-purple-100',
    ];
@endphp
@if($action)
<a href="{{ $href }}" class="flex flex-col items-center justify-center bg-white rounded-2xl shadow hover:shadow-md p-6 transition group cursor-pointer h-full">
    <div class="p-3 rounded-full {{ $iconColors[$color] ?? 'text-blue-600 bg-blue-100' }} mb-3">
        @include('components.dashboard.icons.' . $icon)
    </div>
    <div class="font-semibold text-gray-800 text-lg mb-1 group-hover:text-{{ $color }}-600">{{ $label }}</div>
</a>
@else
<div class="flex flex-col items-center justify-center bg-white rounded-2xl shadow p-6 h-full">
    <div class="p-3 rounded-full {{ $iconColors[$color] ?? 'text-blue-600 bg-blue-100' }} mb-3">
        @include('components.dashboard.icons.' . $icon)
    </div>
    <div class="font-semibold text-gray-800 text-lg mb-1">{{ $label }}</div>
    <div class="text-2xl font-bold text-{{ $color }}-600">{{ $value }}</div>
</div>
@endif 