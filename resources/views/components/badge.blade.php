{{-- Badge Component --}}
@props([
    'variant' => 'default', // 'default', 'primary', 'success', 'danger', 'warning', 'info'
    'size' => 'default', // 'sm', 'default', 'lg'
    'dot' => false,
    'class' => ''
])

@php
    $variantClasses = [
        'default' => 'bg-surface-alt text-text-secondary border border-border',
        'primary' => 'bg-primary-50 text-primary-600',
        'success' => 'bg-success-50 text-success-600',
        'danger' => 'bg-danger-50 text-danger-600',
        'warning' => 'bg-warning-50 text-warning-600',
        'info' => 'bg-info-50 text-info-600',
    ];
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'default' => 'px-2.5 py-1 text-xs',
        'lg' => 'px-3 py-1.5 text-sm',
    ];
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full font-medium {{ $variantClasses[$variant] }} {{ $sizeClasses[$size] }} {{ $class }}">
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
    @endif

    {{ $slot }}
</span>
