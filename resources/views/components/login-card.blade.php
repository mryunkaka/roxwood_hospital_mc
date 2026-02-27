{{-- Login Card Component --}}
@props([
    'padding' => 'lg',
    'class' => ''
])

@php
    $paddingClasses = [
        'none' => '',
        'sm' => 'p-3 sm:p-4',
        'default' => 'p-4 sm:p-5 md:p-6',
        'lg' => 'p-4 sm:p-6 md:p-8',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'w-full max-w-md mx-auto ' . $class]) }}>
    {{-- Card --}}
    <div class="rounded-2xl bg-surface border border-border shadow-lg {{ $paddingClasses[$padding] }}">
        {{ $slot }}
    </div>
</div>
