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

@once
<style>
/* Badge Default */
.badge-default {
    background-color: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
}
.theme-dark .badge-default {
    background-color: #1e293b;
    color: #cbd5e1;
    border-color: #334155;
}
.theme-stylis .badge-default {
    background-color: rgba(255, 255, 255, 0.7);
    color: #475569;
    border-color: rgba(153, 213, 201, 0.3);
}
.theme-stylis.theme-dark .badge-default {
    background-color: rgba(30, 41, 59, 0.8);
    color: #99f6e4;
    border-color: rgba(77, 182, 172, 0.3);
}

/* Badge Primary */
.badge-primary {
    background-color: #dbeafe;
    color: #1d4ed8;
}
.theme-dark .badge-primary {
    background-color: rgba(59, 130, 246, 0.2);
    color: #93c5fd;
}
.theme-stylis .badge-primary {
    background-color: rgba(219, 234, 254, 0.8);
    color: #0369a1;
}
.theme-stylis.theme-dark .badge-primary {
    background-color: rgba(45, 212, 191, 0.2);
    color: #5eead4;
}

/* Badge Success */
.badge-success {
    background-color: #dcfce7;
    color: #15803d;
}
.theme-dark .badge-success {
    background-color: rgba(34, 197, 94, 0.2);
    color: #86efac;
}
.theme-stylis .badge-success {
    background-color: rgba(220, 252, 231, 0.8);
    color: #047857;
}
.theme-stylis.theme-dark .badge-success {
    background-color: rgba(52, 211, 153, 0.2);
    color: #6ee7b7;
}

/* Badge Danger */
.badge-danger {
    background-color: #fee2e2;
    color: #b91c1c;
}
.theme-dark .badge-danger {
    background-color: rgba(239, 68, 68, 0.2);
    color: #fca5a5;
}
.theme-stylis .badge-danger {
    background-color: rgba(254, 226, 226, 0.8);
    color: #b91c1c;
}
.theme-stylis.theme-dark .badge-danger {
    background-color: rgba(248, 113, 113, 0.2);
    color: #fda4af;
}

/* Badge Warning */
.badge-warning {
    background-color: #fef3c7;
    color: #b45309;
}
.theme-dark .badge-warning {
    background-color: rgba(245, 158, 11, 0.2);
    color: #fcd34d;
}
.theme-stylis .badge-warning {
    background-color: rgba(254, 243, 199, 0.8);
    color: #b45309;
}
.theme-stylis.theme-dark .badge-warning {
    background-color: rgba(251, 191, 36, 0.2);
    color: #fbbf24;
}

/* Badge Info */
.badge-info {
    background-color: #e0f2fe;
    color: #0369a1;
}
.theme-dark .badge-info {
    background-color: rgba(14, 165, 233, 0.2);
    color: #7dd3fc;
}
.theme-stylis .badge-info {
    background-color: rgba(224, 242, 254, 0.8);
    color: #0284c7;
}
.theme-stylis.theme-dark .badge-info {
    background-color: rgba(56, 189, 248, 0.2);
    color: #7dd3fc;
}
</style>
@endonce
