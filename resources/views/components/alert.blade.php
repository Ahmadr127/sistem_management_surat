@props([
    'type' => 'info', // success, error, warning, info
    'title' => '',
    'dismissible' => true,
    'icon' => null,
])

@php
    $typeConfig = [
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'text' => 'text-green-800',
            'icon' => 'ri-checkbox-circle-line',
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-800',
            'icon' => 'ri-error-warning-line',
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-200',
            'text' => 'text-yellow-800',
            'icon' => 'ri-alert-line',
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-800',
            'icon' => 'ri-information-line',
        ],
    ];

    $config = $typeConfig[$type] ?? $typeConfig['info'];
    $displayIcon = $icon ?? $config['icon'];
@endphp

<div 
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
    {{ $attributes->merge(['class' => "p-4 rounded-lg border {$config['bg']} {$config['border']}"]) }}
>
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="{{ $displayIcon }} text-xl {{ $config['text'] }}"></i>
        </div>
        <div class="ml-3 flex-1">
            @if($title)
                <h3 class="text-sm font-medium {{ $config['text'] }}">{{ $title }}</h3>
            @endif
            <div class="text-sm {{ $config['text'] }} {{ $title ? 'mt-1' : '' }}">
                {{ $slot }}
            </div>
        </div>
        @if($dismissible)
            <div class="ml-auto pl-3">
                <button 
                    type="button"
                    @click="show = false"
                    class="-mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex {{ $config['text'] }} hover:bg-opacity-20 focus:outline-none"
                >
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>
        @endif
    </div>
</div>
