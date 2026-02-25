{{-- Card Component --}}
@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'default', // 'none', 'sm', 'default', 'lg'
    'border' => true,
    'shadow' => 'default', // 'none', 'sm', 'default', 'lg', 'xl'
    'hoverable' => false,
    'class' => ''
])

@php
    $paddingClasses = [
        'none' => '',
        'sm' => 'p-4',
        'default' => 'p-5 sm:p-6',
        'lg' => 'p-6 sm:p-8',
    ];
    $shadowClasses = [
        'none' => '',
        'sm' => 'shadow-sm',
        'default' => 'shadow',
        'lg' => 'shadow-lg',
        'xl' => 'shadow-xl',
    ];
@endphp

<div class="rounded-2xl bg-surface {{ $border ? 'border border-border' : '' }} {{ $paddingClasses[$padding] }} {{ $shadowClasses[$shadow] }} {{ $hoverable ? 'card-elevated cursor-pointer' : '' }} transition-all duration-300 {{ $class }}">
    @if($title || $subtitle)
        <div class="mb-4">
            @if($title)
                <h3 class="text-lg font-semibold text-text-primary">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="mt-1 text-sm text-text-secondary">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
