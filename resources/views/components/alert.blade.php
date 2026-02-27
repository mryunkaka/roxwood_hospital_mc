{{-- Alert Component --}}
@props([
    'type' => 'info', // 'success', 'danger', 'warning', 'info'
    'dismissible' => false,
    'icon' => true,
    'class' => '',
    'autoHide' => false,
    'autoHideDelay' => 10000, // milliseconds (default 10 seconds)
    'title' => null
])

@php
    // Icons for each alert type
    $icons = [
        'success' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'danger' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'warning' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
        'info' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    ];

    $alertClass = 'alert-' . $type;

    // Close button styles
    $closeButtonClass = 'flex-shrink-0 p-1.5 rounded-md transition-all duration-200 hover:bg-black/10 active:bg-black/15 ' .
                        'theme-dark:hover:bg-white/10 theme-dark:active:bg-white/15';
@endphp

<div x-data="{ show: true }"
     x-init="{{ $autoHide ? 'setTimeout(() => show = false, ' . $autoHideDelay . ')' : '' }}"
     x-show="show"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="alert-component rounded-xl border p-4 shadow-sm {{ $alertClass }} {{ $class }}"
     x-cloak
     role="alert"
     aria-live="polite">

    <div class="flex items-start gap-3">
        @if($icon)
            <div class="flex-shrink-0 mt-0.5">
                <div class="p-0.5 rounded-full ring-1 ring-current opacity-20 alert-icon">
                    {!! $icons[$type] !!}
                </div>
            </div>
        @endif

        <div class="flex-1 min-w-0">
            @if($title)
                <div class="text-sm font-semibold leading-5 alert-content mb-1">
                    {{ $title }}
                </div>
            @endif
            <div class="text-sm leading-5 {{ $title ? 'opacity-90' : '' }}">
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

@once
<style>
/* Alert Success */
.alert-success {
    background-color: #f0fdf4;
    border-color: #bbf7d0;
}
.alert-success .alert-content {
    color: #15803d;
}
.theme-dark .alert-success {
    background-color: rgba(34, 197, 94, 0.15);
    border-color: rgba(34, 197, 94, 0.3);
}
.theme-dark .alert-success .alert-content {
    color: #86efac;
}
.theme-dark .alert-success .alert-icon {
    color: #86efac;
}
.theme-stylis .alert-success {
    background-color: rgba(220, 252, 231, 0.9);
    border-color: rgba(129, 230, 217, 0.5);
}
.theme-stylis .alert-success .alert-content {
    color: #047857;
}
.theme-stylis.theme-dark .alert-success {
    background-color: rgba(34, 197, 94, 0.15);
    border-color: rgba(52, 211, 153, 0.3);
}
.theme-stylis.theme-dark .alert-success .alert-content {
    color: #6ee7b7;
}
.theme-stylis.theme-dark .alert-success .alert-icon {
    color: #6ee7b7;
}

/* Alert Danger */
.alert-danger {
    background-color: #fef2f2;
    border-color: #fecaca;
}
.alert-danger .alert-content {
    color: #b91c1c;
}
.theme-dark .alert-danger {
    background-color: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.3);
}
.theme-dark .alert-danger .alert-content {
    color: #fca5a5;
}
.theme-dark .alert-danger .alert-icon {
    color: #fca5a5;
}
.theme-stylis .alert-danger {
    background-color: rgba(254, 226, 226, 0.9);
    border-color: rgba(251, 113, 133, 0.4);
}
.theme-stylis .alert-danger .alert-content {
    color: #b91c1c;
}
.theme-stylis.theme-dark .alert-danger {
    background-color: rgba(239, 68, 68, 0.15);
    border-color: rgba(248, 113, 113, 0.3);
}
.theme-stylis.theme-dark .alert-danger .alert-content {
    color: #fda4af;
}
.theme-stylis.theme-dark .alert-danger .alert-icon {
    color: #fda4af;
}

/* Alert Warning */
.alert-warning {
    background-color: #fffbeb;
    border-color: #fde68a;
}
.alert-warning .alert-content {
    color: #b45309;
}
.theme-dark .alert-warning {
    background-color: rgba(245, 158, 11, 0.15);
    border-color: rgba(245, 158, 11, 0.3);
}
.theme-dark .alert-warning .alert-content {
    color: #fcd34d;
}
.theme-dark .alert-warning .alert-icon {
    color: #fcd34d;
}
.theme-stylis .alert-warning {
    background-color: rgba(254, 243, 199, 0.9);
    border-color: rgba(251, 191, 36, 0.4);
}
.theme-stylis .alert-warning .alert-content {
    color: #b45309;
}
.theme-stylis.theme-dark .alert-warning {
    background-color: rgba(245, 158, 11, 0.15);
    border-color: rgba(251, 191, 36, 0.3);
}
.theme-stylis.theme-dark .alert-warning .alert-content {
    color: #fbbf24;
}
.theme-stylis.theme-dark .alert-warning .alert-icon {
    color: #fbbf24;
}

/* Alert Info */
.alert-info {
    background-color: #f0f9ff;
    border-color: #bae6fd;
}
.alert-info .alert-content {
    color: #0369a1;
}
.theme-dark .alert-info {
    background-color: rgba(14, 165, 233, 0.15);
    border-color: rgba(14, 165, 233, 0.3);
}
.theme-dark .alert-info .alert-content {
    color: #7dd3fc;
}
.theme-dark .alert-info .alert-icon {
    color: #7dd3fc;
}
.theme-stylis .alert-info {
    background-color: rgba(224, 242, 254, 0.9);
    border-color: rgba(125, 211, 252, 0.4);
}
.theme-stylis .alert-info .alert-content {
    color: #0284c7;
}
.theme-stylis.theme-dark .alert-info {
    background-color: rgba(14, 165, 233, 0.15);
    border-color: rgba(56, 189, 248, 0.3);
}
.theme-stylis.theme-dark .alert-info .alert-content {
    color: #7dd3fc;
}
.theme-stylis.theme-dark .alert-info .alert-icon {
    color: #7dd3fc;
}
</style>
@endonce
