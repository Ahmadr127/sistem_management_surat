@props([
    'title' => '',
    'icon' => null,
    'headerClass' => 'bg-gray-50',
    'bodyClass' => '',
    'noPadding' => false,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-200 overflow-hidden']) }}>
    @if($title || $icon)
        <div class="p-6 border-b border-gray-200 {{ $headerClass }}">
            <h3 class="text-sm font-semibold text-gray-800">
                @if($icon)
                    <i class="{{ $icon }} mr-2 text-gray-600"></i>
                @endif
                {{ $title }}
            </h3>
            {{ $header ?? '' }}
        </div>
    @endif

    <div class="{{ $noPadding ? '' : 'p-6' }} {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>
