{{-- Grid Component --}}
@props([
    'cols' => 1, // 1-4
    'gap' => 'default', // 'none', 'sm', 'default', 'lg'
    'class' => ''
])

@php
    $colClasses = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 sm:grid-cols-2',
        3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
    ];
    $gapClasses = [
        'none' => 'gap-0',
        'sm' => 'gap-2 sm:gap-3',
        'default' => 'gap-4 sm:gap-6',
        'lg' => 'gap-6 sm:gap-8',
    ];
@endphp

<div class="grid {{ $colClasses[$cols] }} {{ $gapClasses[$gap] }} {{ $class }}">
    {{ $slot }}
</div>
