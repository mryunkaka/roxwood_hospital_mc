{{-- Brand Logo Component - Roxwood Hospital --}}
@props([
    'size' => 'md',
    'showText' => true,
    'textClass' => '',
    'class' => ''
])

@php
    $sizeClasses = [
        'sm' => ['container' => 'w-10 h-10', 'icon' => 'w-5 h-5', 'text' => 'text-xl'],
        'md' => ['container' => 'w-14 h-14', 'icon' => 'w-8 h-8', 'text' => 'text-3xl'],
        'lg' => ['container' => 'w-16 h-16', 'icon' => 'w-10 h-10', 'text' => 'text-4xl'],
    ];
    $s = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="inline-flex items-center justify-center gap-3 {{ $class }}">
    <div class="{{ $s['container'] }} rounded-xl flex items-center justify-center shadow-lg bg-gradient-to-br"
         style="background: linear-gradient(to bottom right, var(--color-logo-bg-from), var(--color-logo-bg-to));">
        <svg class="{{ $s['icon'] }}" style="color: var(--color-logo-icon);"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
    </div>
    @if($showText)
        <span class="font-bold {{ $s['text'] }} text-text-primary {{ $textClass }}">Roxwood</span>
    @endif
</div>
