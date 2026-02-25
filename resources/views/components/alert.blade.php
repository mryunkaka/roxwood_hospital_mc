{{-- Alert Component --}}
@props([
    'type' => 'info', // 'success', 'danger', 'warning', 'info'
    'dismissible' => false,
    'icon' => true,
    'class' => ''
])

@php
    // Theme-aware alert styling for light, dark, and stylis themes
    $typeClasses = [
        'success' => 'bg-success-50 border-success-200 text-success-700 ' .
                     'theme-dark:bg-success-900/25 theme-dark:border-success-700/50 theme-dark:text-success-300 ' .
                     'theme-stylis:bg-emerald-50/90 theme-stylis:border-emerald-300/60 theme-stylis:text-emerald-800 ' .
                     'theme-stylis.theme-dark:bg-emerald-900/20 theme-stylis.theme-dark:border-emerald-600/40 theme-stylis.theme-dark:text-emerald-300',

        'danger' => 'bg-danger-50 border-danger-200 text-danger-700 ' .
                   'theme-dark:bg-danger-900/25 theme-dark:border-danger-700/50 theme-dark:text-danger-300 ' .
                   'theme-stylis:bg-rose-50/90 theme-stylis:border-rose-300/60 theme-stylis:text-rose-800 ' .
                   'theme-stylis.theme-dark:bg-rose-900/20 theme-stylis.theme-dark:border-rose-600/40 theme-stylis.theme-dark:text-rose-300',

        'warning' => 'bg-warning-50 border-warning-200 text-warning-700 ' .
                   'theme-dark:bg-warning-900/25 theme-dark:border-warning-700/50 theme-dark:text-warning-300 ' .
                   'theme-stylis:bg-amber-50/90 theme-stylis:border-amber-300/60 theme-stylis:text-amber-800 ' .
                   'theme-stylis.theme-dark:bg-amber-900/20 theme-stylis.theme-dark:border-amber-600/40 theme-stylis.theme-dark:text-amber-300',

        'info' => 'bg-info-50 border-info-200 text-info-700 ' .
                 'theme-dark:bg-info-900/25 theme-dark:border-info-700/50 theme-dark:text-info-300 ' .
                 'theme-stylis:bg-sky-50/90 theme-stylis:border-sky-300/60 theme-stylis:text-sky-800 ' .
                 'theme-stylis.theme-dark:bg-sky-900/20 theme-stylis.theme-dark:border-sky-600/40 theme-stylis.theme-dark:text-sky-300',
    ];

    // Icons for each alert type
    $icons = [
        'success' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'danger' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'warning' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
        'info' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    ];

    // Close button hover styles per theme
    $closeButtonClass = 'flex-shrink-0 p-1.5 rounded-md transition-all duration-200 ' .
                        'hover:bg-black/10 active:bg-black/15 ' .
                        'theme-dark:hover:bg-white/10 theme-dark:active:bg-white/15 ' .
                        'theme-stylis:hover:bg-black/10 theme-stylis:active:bg-black/15 ' .
                        'theme-stylis.theme-dark:hover:bg-white/10 theme-stylis.theme-dark:active:bg-white/15';
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="alert-component rounded-xl border p-4 shadow-sm {{ $typeClasses[$type] }} {{ $class }}"
     x-cloak
     role="alert"
     aria-live="polite">

    <div class="flex items-start gap-3">
        @if($icon)
            <div class="flex-shrink-0 mt-0.5">
                <div class="p-0.5 rounded-full ring-1 ring-current opacity-20">
                    {!! $icons[$type] !!}
                </div>
            </div>
        @endif

        <div class="flex-1 min-w-0">
            <div class="text-sm font-medium leading-5">
                {{ $slot }}
            </div>
        </div>

        @if($dismissible)
            <button @click="show = false"
                    class="{{ $closeButtonClass }}"
                    aria-label="Close alert">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>
</div>
