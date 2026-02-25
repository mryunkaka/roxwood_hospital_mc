{{-- Modal Component --}}
@props([
    'id' => 'modal-' . uniqid(),
    'title' => null,
    'size' => 'default', // 'sm', 'default', 'lg', 'xl', 'full'
    'centered' => true,
    'backdrop' => true,
    'class' => ''
])

@php
    $sizeClasses = [
        'sm' => 'max-w-md',
        'default' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        'full' => 'max-w-full mx-4',
    ];
@endphp

<div x-data="{ open: false }" class="{{ $class }}">
    {{-- Trigger --}}
    <button @click="open = true" {{ $attributes }}>
        {{ $trigger ?? 'Open Modal' }}
    </button>

    {{-- Modal Overlay --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 {{ $backdrop ? 'bg-black/50 backdrop-blur-sm' : '' }}"
         x-cloak>
        {{-- Modal Content --}}
        <div class="flex items-center justify-center min-h-screen p-4 {{ $centered ? '' : 'items-start pt-20' }}"
             @click.self="if('{{ $backdrop }}' === '1') open = false">

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="w-full {{ $sizeClasses[$size] }} rounded-xl bg-surface shadow-2xl">

                {{-- Header --}}
                @if($title)
                    <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                        <h3 class="text-lg font-semibold text-text-primary">{{ $title }}</h3>
                        <button @click="open = false" class="p-1 rounded-lg hover:bg-surface-hover transition-colors">
                            <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                {{-- Body --}}
                <div class="px-6 py-4 {{ $title ? '' : 'pt-6' }}">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                @if(isset($footer) && $footer)
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-border">
                        {{ $footer }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
