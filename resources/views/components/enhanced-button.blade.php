@props([
    'variant' => 'primary',
    'size' => 'md',
    'loading' => false,
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'ripple' => true,
    'href' => null,
    'type' => 'button'
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 relative overflow-hidden';
    
    $variantClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 hover:shadow-lg hover:-translate-y-0.5',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500 hover:shadow-lg hover:-translate-y-0.5',
        'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 hover:shadow-lg hover:-translate-y-0.5',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 hover:shadow-lg hover:-translate-y-0.5',
        'warning' => 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500 hover:shadow-lg hover:-translate-y-0.5',
        'info' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 hover:shadow-lg hover:-translate-y-0.5',
        'outline-primary' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white focus:ring-blue-500 hover:shadow-md hover:scale-105',
        'outline-secondary' => 'border-2 border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white focus:ring-gray-500 hover:shadow-md hover:scale-105',
        'ghost' => 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:ring-gray-500 hover:shadow-sm',
        'link' => 'text-blue-600 underline-offset-4 hover:underline focus:ring-blue-500 hover:text-blue-700',
        'gradient' => 'bg-gradient-to-r from-purple-500 to-pink-500 text-white hover:from-purple-600 hover:to-pink-600 focus:ring-purple-500 hover:shadow-lg hover:-translate-y-0.5 hover:scale-105'
    ];
    
    $sizeClasses = [
        'xs' => 'px-2.5 py-1.5 text-xs rounded',
        'sm' => 'px-3 py-2 text-sm rounded-md',
        'md' => 'px-4 py-2 text-sm rounded-md',
        'lg' => 'px-4 py-2 text-base rounded-lg',
        'xl' => 'px-6 py-3 text-base rounded-lg'
    ];
    
    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
    
    if ($disabled || $loading) {
        $classes .= ' opacity-50 cursor-not-allowed pointer-events-none';
    }
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}
       @if($ripple) 
           onclick="createRipple(event)" 
       @endif>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif($icon && $iconPosition === 'left')
            <i class="{{ $icon }} mr-2 transition-transform duration-200 group-hover:scale-110"></i>
        @endif
        
        <span class="relative z-10">{{ $slot }}</span>
        
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }} ml-2 transition-transform duration-200 group-hover:scale-110"></i>
        @endif
    </a>
@else
    <button 
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes . ' group']) }}
        @if($ripple) 
            onclick="createRipple(event)" 
        @endif
        @if($disabled || $loading) disabled @endif>
        
        @if($loading)
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="relative z-10">{{ $loading === true ? 'Loading...' : $loading }}</span>
            </div>
        @else
            <div class="flex items-center">
                @if($icon && $iconPosition === 'left')
                    <i class="{{ $icon }} mr-2 transition-transform duration-200 group-hover:scale-110"></i>
                @endif
                
                <span class="relative z-10">{{ $slot }}</span>
                
                @if($icon && $iconPosition === 'right')
                    <i class="{{ $icon }} ml-2 transition-transform duration-200 group-hover:scale-110"></i>
                @endif
            </div>
        @endif
    </button>
@endif

<script>
    function createRipple(event) {
        const button = event.currentTarget;
        const rect = button.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;
        
        const ripple = document.createElement('span');
        ripple.classList.add('absolute', 'rounded-full', 'bg-white', 'opacity-30', 'pointer-events-none', 'animate-ping');
        ripple.style.left = x - 10 + 'px';
        ripple.style.top = y - 10 + 'px';
        ripple.style.width = '20px';
        ripple.style.height = '20px';
        ripple.style.transform = 'scale(0)';
        ripple.style.animation = 'ripple 0.6s linear';
        
        button.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }
</script>

<style>
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
</style>
