{{-- Badge Component --}}
@props([
    'variant' => 'default', // 'default', 'primary', 'success', 'danger', 'warning', 'info'
    'size' => 'default', // 'sm', 'default', 'lg'
    'dot' => false,
    'class' => ''
])

@php
    // Base classes
    $baseClasses = 'inline-flex items-center gap-1.5 rounded-full font-medium transition-all duration-200';

    // Size classes
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'default' => 'px-2.5 py-1 text-xs',
        'lg' => 'px-3 py-1.5 text-sm',
    ];

    // Variant classes - using data attributes for theme switching
    $variantDataAttr = 'data-badge-variant="' . $variant . '"';
@endphp

@php
// Theme-aware badge styling dengan CSS-based approach
// Kita gunakan base class + data attribute untuk theme switching
$variantBaseClasses = [
    'default' => 'badge-default',
    'primary' => 'badge-primary',
    'success' => 'badge-success',
    'danger' => 'badge-danger',
    'warning' => 'badge-warning',
    'info' => 'badge-info',
];
$variantClass = $variantBaseClasses[$variant] ?? 'badge-default';
@endphp

<span {{ $variantDataAttr }} class="{{ $baseClasses }} {{ $variantClass }} {{ $sizeClasses[$size] }} {{ $class }}">
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
    @endif

    {{ $slot }}
</span>
