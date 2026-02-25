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

    // Theme-aware modal styling
    $modalClasses = 'w-full ' . $sizeClasses[$size] . ' rounded-2xl bg-surface shadow-2xl border border-border ' .
                    'theme-dark:shadow-black/50 ' .
                    'theme-stylis:rounded-3xl theme-stylis:border-white/30 theme-stylis:shadow-xl ' .
                    'theme-stylis:backdrop-blur-xl theme-stylis:bg-white/90 ' .
                    'theme-stylis.theme-dark:bg-surface/90 theme-stylis.theme-dark:border-teal-700/30';

    $headerClasses = 'flex items-center justify-between px-6 py-4 border-b border-border ' .
                     'theme-stylis:border-teal-200/50 ' .
                     'theme-stylis.theme-dark:border-teal-700/30';

    $footerClasses = 'flex items-center justify-end gap-3 px-6 py-4 border-t border-border ' .
                     'theme-stylis:border-teal-200/50 ' .
                     'theme-stylis.theme-dark:border-teal-700/30';

    $closeButtonClasses = 'p-2 rounded-xl hover:bg-surface-hover transition-all duration-200 ' .
                          'hover:scale-110 active:scale-95 ' .
                          'theme-stylis:hover:bg-black/5 ' .
                          'theme-dark:hover:bg-white/10';
@endphp

<div x-data="{
    open: false,
    close() {
        this.open = false;
    }
}" class="{{ $class }}" x-cloak>
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
         style="display: none;"
         class="fixed inset-0 z-50 {{ $backdrop ? 'bg-black/50 backdrop-blur-sm theme-stylis:bg-teal-900/20' : '' }}">

        {{-- Modal Container --}}
        <div class="flex items-center justify-center min-h-screen p-4 {{ $centered ? '' : 'items-start pt-20' }}"
             @click.self="close()">

            {{-- Modal Content --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="{{ $modalClasses }}"
                 @keydown.escape.window="close()">

                {{-- Header --}}
                @if($title)
                    <div class="{{ $headerClasses }}">
                        <h3 class="text-lg font-semibold text-text-primary">{{ $title }}</h3>
                        <button @click="close()" class="{{ $closeButtonClasses }}" aria-label="Close modal">
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
                    <div class="{{ $footerClasses }}">
                        {{-- Add close functionality to buttons with data-dismiss="modal" attribute --}}
                        {!! preg_replace('/(<button[^>]*)(data-dismiss="modal")/', '$1@click="close()" $2', $footer) !!}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
