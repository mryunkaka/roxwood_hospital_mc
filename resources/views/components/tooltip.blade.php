{{-- Tooltip Component --}}
{{-- Tooltip dengan berbagai position dan variant --}}

@props([
    'content' => '', // Tooltip content (text or HTML)
    'position' => 'top', // 'top' | 'bottom' | 'left' | 'right'
    'variant' => 'dark', // 'dark' | 'light'
    'trigger' => 'hover', // 'hover' | 'click' | 'focus'
    'disabled' => false,
    'class' => ''
])

@php
    // Position classes
    $positionClasses = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
    ];

    // Variant colors
    $variantColors = [
        'dark' => 'bg-gray-900 dark:bg-gray-700 text-white',
        'light' => 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-600'
    ];

    $positionClass = $positionClasses[$position] ?? $positionClasses['top'];
    $variantColor = $variantColors[$variant] ?? $variantColors['dark'];

    // Arrow position classes
    $arrowClasses = [
        'top' => 'bottom-[-6px] left-1/2 -translate-x-1/2 border-l-transparent border-r-transparent border-b-transparent border-t-current',
        'bottom' => 'top-[-6px] left-1/2 -translate-x-1/2 border-l-transparent border-r-transparent border-t-transparent border-b-current',
        'left' => 'right-[-6px] top-1/2 -translate-y-1/2 border-t-transparent border-b-transparent border-r-transparent border-l-current',
        'right' => 'left-[-6px] top-1/2 -translate-y-1/2 border-t-transparent border-b-transparent border-l-transparent border-r-current',
    ];

    $arrowColor = $variant === 'dark'
        ? 'text-gray-900 dark:text-gray-700'
        : 'text-white dark:text-gray-800';

    $arrowClass = $arrowClasses[$position] ?? $arrowClasses['top'];
@endphp

<span
    x-data="{ tooltipOpen: false }"
    class="tooltip-wrapper inline-flex relative {{ $class }}"
    @mouseenter="{{ $trigger === 'hover' ? 'tooltipOpen = true' : '' }}"
    @mouseleave="{{ $trigger === 'hover' ? 'tooltipOpen = false' : '' }}"
    @click="{{ $trigger === 'click' ? 'tooltipOpen = !tooltipOpen' : ($trigger === 'hover' ? 'tooltipOpen = !tooltipOpen' : '') }}"
    @focus="{{ $trigger === 'focus' ? 'tooltipOpen = true' : '' }}"
    @blur="{{ $trigger === 'focus' ? 'tooltipOpen = false' : '' }}"
    @click.outside="{{ $trigger === 'click' ? 'tooltipOpen = false' : '' }}"
>
    {{-- Trigger element (slot) --}}
    {{ $slot }}

    {{-- Tooltip --}}
    <span
        class="tooltip absolute {{ $positionClass }} {{ $variantColor }} px-3 py-2 text-sm rounded-lg shadow-lg pointer-events-none
               z-50 whitespace-nowrap
               x-show="tooltipOpen && !{{ $disabled ? 'true' : 'false' }}"
               x-transition:enter="transition ease-out duration-150"
               x-transition:enter-start="opacity-0 scale-95"
               x-transition:enter-end="opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-100"
               x-transition:leave-start="opacity-100 scale-100"
               x-transition:leave-end="opacity-0 scale-95"
               x-cloak
        role="tooltip"
        @if($disabled)
            aria-hidden="true"
        @else
            x-bind:aria-hidden="!tooltipOpen"
        @endif
    >
        {!! $content !!}

        {{-- Arrow --}}
        <span class="absolute w-0 h-0 border-4 {{ $arrowClass }} {{ $arrowColor }}"></span>
    </span>
</span>

@once
@push('styles')
<style>
/* Tooltip styling */
.tooltip-wrapper {
    cursor: help;
}
</style>
@endpush
@endonce
