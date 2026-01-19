@props([
    'id' => 'modal-' . uniqid(),
    'title' => 'Modal Title',
    'size' => 'md', // sm, md, lg, xl, full
    'showFooter' => true,
    'closeButton' => true,
    'backdrop' => true,
])

@php
    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        'full' => 'max-w-full mx-4',
    ];
    $modalSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div 
    x-data="{ 
        open: false,
        toggle() {
            this.open = !this.open;
            if (this.open) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        },
        close() {
            this.open = false;
            document.body.style.overflow = '';
        }
    }"
    @keydown.escape.window="close()"
    x-init="
        window.addEventListener('open-modal-{{ $id }}', () => { open = true; document.body.style.overflow = 'hidden'; });
        window.addEventListener('close-modal-{{ $id }}', () => close());
    "
    x-cloak
>
    <!-- Trigger Slot -->
    <div @click="toggle()">
        {{ $trigger ?? '' }}
    </div>

    <!-- Modal Overlay -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Backdrop -->
        @if($backdrop)
            <div 
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                @click="close()"
            ></div>
        @endif

        <!-- Modal Container -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <!-- Modal Content -->
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.stop
                class="relative w-full {{ $modalSize }} bg-white rounded-lg shadow-xl overflow-hidden"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $title }}
                    </h3>
                    @if($closeButton)
                        <button 
                            type="button"
                            @click="close()"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-150 focus:outline-none"
                        >
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    @endif
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                    {{ $slot }}
                </div>

                <!-- Modal Footer -->
                @if($showFooter)
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $footer ?? '' }}
                        
                        @if(!isset($footer))
                            <button 
                                type="button"
                                @click="close()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150"
                            >
                                Tutup
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
