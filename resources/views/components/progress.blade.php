{{-- Progress Bar Component --}}
{{-- Progress bar dengan berbagai variant dan size --}}

@props([
    'value' => 0, // 0-100
    'max' => 100,
    'variant' => 'primary', // 'primary' | 'success' | 'danger' | 'warning' | 'info' | 'secondary'
    'size' => 'md', // 'sm' | 'md' | 'lg'
    'showLabel' => false,
    'label' => null,
    'showPercentage' => false,
    'striped' => false,
    'animated' => false,
    'class' => ''
])

@php
    // Height berdasarkan size
    $heights = [
        'sm' => 'h-1',
        'md' => 'h-2',
        'lg' => 'h-3'
    ];

    // Text colors
    $textColors = [
        'primary' => 'text-primary-600 dark:text-primary-400',
        'success' => 'text-success-600 dark:text-success-400',
        'danger' => 'text-danger-600 dark:text-danger-400',
        'warning' => 'text-warning-600 dark:text-warning-400',
        'info' => 'text-info-600 dark:text-info-400',
        'secondary' => 'text-gray-600 dark:text-gray-400'
    ];

    // Background colors
    $bgColors = [
        'primary' => 'bg-primary-500',
        'success' => 'bg-success-500',
        'danger' => 'bg-danger-500',
        'warning' => 'bg-warning-500',
        'info' => 'bg-info-500',
        'secondary' => 'bg-gray-400'
    ];

    $height = $heights[$size] ?? 'h-2';
    $textColor = $textColors[$variant] ?? $textColors['primary'];
    $bgColor = $bgColors[$variant] ?? $bgColors['primary'];

    // Calculate percentage
    $percentage = $max > 0 ? min(100, ($value / $max) * 100) : 0;
@endphp

<div class="progress-wrapper {{ $class }}">
    @if ($showLabel || $label || $showPercentage)
        <div class="flex items-center justify-between mb-1">
            @if ($label)
                <span class="text-sm font-medium text-text-primary">{{ $label }}</span>
            @endif
            @if ($showPercentage)
                <span class="text-sm {{ $textColor }}">{{ number_format($percentage, 0) }}%</span>
            @endif
        </div>
    @endif

    <div class="progress-bar-container bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden {{ $height }}">
        <div
            class="progress-bar-fill {{ $bgColor }} {{ $height }} transition-all duration-300 ease-out
                   @if($striped) progress-bar-striped @endif
                   @if($animated) progress-bar-animated @endif"
            style="width: {{ $percentage }}%"
            role="progressbar"
            :value="$percentage"
            aria-valuemin="0"
            aria-valuemax="100"
        ></div>
    </div>
</div>

@once
@push('styles')
<style>
/* Progress bar striped animation */
.progress-bar-striped {
    background-image: linear-gradient(
        45deg,
        rgba(255, 255, 255, 0.15) 25%,
        transparent 25%,
        transparent 50%,
        rgba(255, 255, 255, 0.15) 50%,
        rgba(255, 255, 255, 0.15) 75%,
        transparent 75%,
        transparent
    );
    background-size: 1rem 1rem;
}

/* Progress bar animated */
.progress-bar-animated {
    animation: progress-bar-stripe 1s linear infinite;
}

@keyframes progress-bar-stripe {
    0% {
        background-position: 1rem 0;
    }
    100% {
        background-position: 0 0;
    }
}

/* Stylis theme variants */
.theme-stylis .progress-bar-container {
    @apply bg-teal-100/50 dark:bg-teal-900/50;
}

.theme-stylis .progress-bar-fill.bg-primary-500 {
    @apply bg-teal-500;
}

.theme-stylis .progress-bar-fill.bg-success-500 {
    @apply bg-emerald-500;
}

.theme-stylis .progress-bar-fill.bg-danger-500 {
    @apply bg-rose-500;
}

.theme-stylis .progress-bar-fill.bg-warning-500 {
    @apply bg-amber-500;
}

.theme-stylis .progress-bar-fill.bg-info-500 {
    @apply bg-sky-500;
}

.theme-stylis .progress-bar-fill.bg-gray-400 {
    @apply bg-gray-400;
}
</style>
@endpush
@endonce
