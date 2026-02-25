{{-- Button Component --}}
@props([
    'variant' => 'primary', // 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'ghost', 'link'
    'size' => 'default', // 'sm', 'default', 'lg'
    'type' => 'button', // 'button', 'submit', 'reset'
    'disabled' => false,
    'fullWidth' => false,
    'icon' => null,
    'iconPosition' => 'left', // 'left', 'right'
    'class' => ''
])

@php
    $variantClasses = [
        'primary' => 'bg-primary text-white hover:bg-primary-dark focus:ring-primary/50 active:bg-primary-dark',
        'secondary' => 'bg-surface text-text-primary border border-border hover:bg-surface-hover hover:border-border-medium',
        'success' => 'bg-success text-white hover:bg-success-600 focus:ring-success/50',
        'danger' => 'bg-danger text-white hover:bg-danger-600 focus:ring-danger/50',
        'warning' => 'bg-warning text-white hover:bg-warning-600 focus:ring-warning/50',
        'info' => 'bg-info text-white hover:bg-info-600 focus:ring-info/50',
        'ghost' => 'bg-transparent text-text-primary hover:bg-surface-hover',
        'link' => 'bg-transparent text-primary hover:text-primary-dark hover:underline p-0',
    ];
    $sizeClasses = [
        'sm' => 'px-4 py-2 text-sm',
        'default' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    $iconSizes = [
        'sm' => 'w-4 h-4',
        'default' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
    ];

    // Rounded class - more rounded untuk modern look
    $roundedClass = 'rounded-xl';
    if ($variant === 'link') {
        $roundedClass = '';
    }
@endphp

<button {{ $attributes->merge([
    'type' => $type,
    'disabled' => $disabled,
    'class' => 'inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-50 disabled:pointer-events-none ' .
                 $variantClasses[$variant] . ' ' .
                 $sizeClasses[$size] . ' ' .
                 $roundedClass . ' ' .
                 ($disabled ? '' : 'active:scale-[0.98]') . ' ' .
                 ($fullWidth ? 'w-full' : '') . ' ' .
                 $class
]) }}>
    @if($icon && $iconPosition === 'left')
        <span class="{{ $iconSizes[$size] }} shrink-0">{!! $icon !!}</span>
    @endif

    {{ $slot }}

    @if($icon && $iconPosition === 'right')
        <span class="{{ $iconSizes[$size] }} shrink-0">{!! $icon !!}</span>
    @endif
</button>
