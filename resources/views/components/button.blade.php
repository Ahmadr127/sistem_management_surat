@props([
    'variant' => 'primary', // primary, secondary, success, danger, warning, info
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'type' => 'button',
    'loading' => false,
])

@php
    $variantClasses = [
        'primary' => 'text-white bg-green-600 hover:bg-green-700 focus:ring-green-500',
        'secondary' => 'text-gray-700 bg-white border-2 border-gray-200 hover:bg-gray-50 hover:border-gray-300',
        'success' => 'text-white bg-green-600 hover:bg-green-700 focus:ring-green-500',
        'danger' => 'text-white bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'warning' => 'text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
        'info' => 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-6 py-3 text-sm',
        'lg' => 'px-8 py-4 text-base',
    ];

    $classes = $variantClasses[$variant] ?? $variantClasses['primary'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "inline-flex items-center {$sizeClass} font-medium border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 {$classes}"]) }}
    {{ $loading ? 'disabled' : '' }}
>
    @if($loading)
        <i class="ri-loader-4-line animate-spin mr-2"></i>
    @elseif($icon && $iconPosition === 'left')
        <i class="{{ $icon }} mr-2"></i>
    @endif

    {{ $slot }}

    @if($icon && $iconPosition === 'right' && !$loading)
        <i class="{{ $icon }} ml-2"></i>
    @endif
</button>
