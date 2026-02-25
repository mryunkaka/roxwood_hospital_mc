{{-- Container Component --}}
@props([
    'size' => 'default', // 'sm', 'default', 'lg', 'xl', 'full'
    'class' => ''
])

@php
    $sizes = [
        'sm' => 'max-w-2xl',
        'default' => 'max-w-7xl',
        'lg' => 'max-w-7xl lg:max-w-screen-xl',
        'xl' => 'max-w-7xl xl:max-w-screen-2xl',
        'full' => 'max-w-full',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['default'];
@endphp

<div class="{{ $sizeClass }} mx-auto px-4 sm:px-6 lg:px-8 {{ $class }}">
    {{ $slot }}
</div>
