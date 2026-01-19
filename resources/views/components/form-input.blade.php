@props([
    'name',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'icon' => null,
    'hint' => null,
    'rows' => 3,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="text-sm font-semibold text-gray-800">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($type === 'textarea')
            <textarea
                name="{{ $name }}"
                id="{{ $name }}"
                rows="{{ $rows }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                placeholder="{{ $placeholder }}"
                {{ $attributes->merge(['class' => 'w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200']) }}
            >{{ old($name, $value) }}</textarea>
        @elseif($type === 'date')
            <input
                type="date"
                name="{{ $name }}"
                id="{{ $name }}"
                value="{{ old($name, $value) }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $attributes->merge(['class' => 'w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200']) }}
            >
            @if($icon)
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <i class="{{ $icon }}"></i>
                </div>
            @endif
        @else
            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $attributes->merge(['class' => 'w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200']) }}
            >
            @if($icon)
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <i class="{{ $icon }}"></i>
                </div>
            @endif
        @endif
    </div>

    @if($hint)
        <p class="text-xs text-gray-500 leading-relaxed">
            <i class="ri-information-line mr-1"></i>
            {{ $hint }}
        </p>
    @endif

    @error($name)
        <p class="text-red-500 text-xs">
            <i class="ri-error-warning-line mr-1"></i>
            {{ $message }}
        </p>
    @enderror
</div>
