@props([
    'title',
    'value' => 0,
    'subtitle' => '',
    'icon' => 'ri-file-line',
    'color' => 'blue'
])

@php
    $colors = [
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'border' => 'border-blue-200'],
        'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'border' => 'border-green-200'],
        'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'border' => 'border-yellow-200'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'border' => 'border-purple-200'],
        'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'border' => 'border-red-200'],
        'gray' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-200'],
    ];
    
    $selectedColor = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md hover:border-{{ $color }}-100 transition-all duration-200 group">
    <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 {{ $selectedColor['bg'] }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
            <i class="{{ $icon }} text-xl {{ $selectedColor['text'] }}"></i>
        </div>
        <span class="text-xs font-medium {{ $selectedColor['text'] }} {{ $selectedColor['bg'] }} px-2 py-1 rounded-full">
            {{ $title }}
        </span>
    </div>
    <h3 class="text-3xl font-bold text-gray-800 mb-1">{{ number_format($value) }}</h3>
    <p class="text-sm text-gray-500">{{ $subtitle }}</p>
</div>
