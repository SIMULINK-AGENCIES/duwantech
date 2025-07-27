@props([
    'label' => null,
    'error' => null,
    'required' => false,
    'type' => 'text',
    'icon' => null,
    'iconPosition' => 'left',
    'placeholder' => null,
    'floating' => false,
    'animate' => true
])

@php
    $id = $attributes->get('id', 'input_' . uniqid());
    $inputClasses = 'block w-full transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    
    if ($error) {
        $inputClasses .= ' border-red-300 focus:ring-red-500';
    }
    
    if ($icon) {
        if ($iconPosition === 'left') {
            $inputClasses .= ' pl-10';
        } else {
            $inputClasses .= ' pr-10';
        }
    }
    
    if ($animate) {
        $inputClasses .= ' hover:border-blue-400 hover:shadow-md';
    }
    
    if ($floating) {
        $inputClasses .= ' px-4 py-3 placeholder-transparent';
    } else {
        $inputClasses .= ' px-3 py-2';
    }
@endphp

<div class="space-y-1 {{ $animate ? 'animate-on-scroll' : '' }}">
    @if($label && !$floating)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 transition-colors duration-200">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-1">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 {{ $iconPosition === 'left' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center pointer-events-none">
                <i class="{{ $icon }} text-gray-400 transition-colors duration-200"></i>
            </div>
        @endif
        
        <input 
            type="{{ $type }}"
            id="{{ $id }}"
            {{ $attributes->merge(['class' => $inputClasses . ($floating ? ' peer' : '')]) }}
            placeholder="{{ $floating ? ' ' : $placeholder }}"
            @if($animate)
                onfocus="this.parentElement.parentElement.classList.add('focused')"
                onblur="this.parentElement.parentElement.classList.remove('focused')"
            @endif
        />
        
        @if($label && $floating)
            <label for="{{ $id }}" class="absolute left-4 -top-2.5 bg-white px-2 text-sm text-gray-600 transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-blue-600 peer-focus:scale-95">
                {{ $label }}
                @if($required)
                    <span class="text-red-500 ml-1">*</span>
                @endif
            </label>
        @endif
    </div>
    
    @if($error)
        <div class="flex items-center space-x-1 text-red-600 text-sm animate-shake">
            <i class="fas fa-exclamation-circle text-xs"></i>
            <span>{{ $error }}</span>
        </div>
    @endif
</div>

<style>
    .focused {
        transform: scale(1.01);
    }
    
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    /* Enhanced focus styles */
    input:focus + label {
        color: #3b82f6;
    }
    
    /* Smooth transitions for all form elements */
    input, label {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
